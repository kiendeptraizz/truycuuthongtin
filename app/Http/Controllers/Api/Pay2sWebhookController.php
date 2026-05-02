<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PendingOrder;
use App\Services\TelegramBotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Pay2sWebhookController extends Controller
{
    /**
     * Pay2S webhook handler.
     *
     * Format payload thực tế từ Pay2S:
     *   {
     *     "transactions": [
     *       { "id":..., "gateway":"ACB", "accountNumber":"24621481",
     *         "content":"DH260502001 GD ...", "transferType":"IN",
     *         "transferAmount":5000, "checksum":"...", ... }
     *     ]
     *   }
     *
     * Nội dung CK qua bank thường bị strip dấu gạch ngang/khoảng trắng,
     * nên match cả "DH-260502-001" và "DH260502001".
     */
    public function __invoke(Request $request, TelegramBotService $bot): JsonResponse
    {
        // 1) Verify token (Pay2S gắn vào header Authorization "Apikey xxx" hoặc body)
        $expected = (string) env('PAY2S_WEBHOOK_TOKEN', '');
        if ($expected !== '') {
            $auth = $request->header('Authorization', '');
            $token = preg_replace('/^(Bearer|Apikey|Token)\s+/i', '', $auth);
            if (!hash_equals($expected, $token) && !hash_equals($expected, (string) $request->input('token'))) {
                Log::warning('Pay2S webhook: invalid token', ['headers' => $request->headers->all()]);
                return response()->json(['ok' => false, 'error' => 'invalid_token'], 401);
            }
        }

        $payload = $request->all();
        Log::info('Pay2S webhook received', ['payload' => $payload]);

        // 2) Pay2S wrap trong transactions[] — xử lý từng GD
        $transactions = $payload['transactions'] ?? [$payload];
        if (!is_array($transactions)) {
            $transactions = [$payload];
        }

        $results = [];
        foreach ($transactions as $tx) {
            $results[] = $this->processTransaction($tx, $bot);
        }

        return response()->json(['ok' => true, 'processed' => $results]);
    }

    private function processTransaction(array $tx, TelegramBotService $bot): array
    {
        // Trích field theo nhiều tên (Pay2S vs format khác)
        $amount = (int) ($tx['transferAmount'] ?? $tx['amount'] ?? $tx['transfer_amount'] ?? 0);
        $content = (string) ($tx['content'] ?? $tx['description'] ?? $tx['transferContent'] ?? $tx['transfer_content'] ?? '');
        $bankTxId = (string) ($tx['id'] ?? $tx['transaction_id'] ?? $tx['transactionId'] ?? $tx['referenceNumber'] ?? '');
        $direction = strtoupper((string) ($tx['transferType'] ?? $tx['transfer_type'] ?? $tx['type'] ?? 'IN'));

        if ($amount <= 0 || $content === '') {
            return ['ok' => false, 'error' => 'missing_fields'];
        }

        if (!in_array($direction, ['IN', 'CREDIT', 'TIEN_VAO', 'TIENVAO'], true)) {
            return ['ok' => true, 'skipped' => 'not_credit'];
        }

        // Idempotent layer 1 — bankTxId duplicate
        if ($bankTxId !== '' && PendingOrder::where('bank_transaction_id', $bankTxId)->exists()) {
            return ['ok' => true, 'skipped' => 'duplicate', 'tx' => $bankTxId];
        }
        if ($bankTxId === '') {
            // Pay2S thường có id; thiếu là edge case (test/sandbox). Vẫn tiếp tục
            // vì idempotent layer 2 (paid_at !== null trong lock) sẽ chặn double-activate.
            Log::warning('Pay2S webhook: empty bankTxId, fallback to paid_at lock check', [
                'content' => $content,
                'amount' => $amount,
            ]);
        }

        // Match order_code linh hoạt: "DH-260502-001", "DH260502001", "DH 260502 001"
        if (!preg_match('/DH[-\s]?(\d{6})[-\s]?(\d{3})/i', $content, $m)) {
            Log::info('Pay2S webhook: no order_code in content', ['content' => $content]);
            return ['ok' => true, 'matched' => false, 'content' => $content];
        }
        $orderCode = sprintf('DH-%s-%s', $m[1], $m[2]);

        // ATOMIC: lock order + update paid + create/activate CS + create Profit + mark completed
        // Nếu bất kỳ bước nào throw → rollback toàn bộ (tránh state nửa-active).
        try {
            $result = DB::transaction(function () use ($orderCode, $amount, $bankTxId, $tx) {
                $order = PendingOrder::where('order_code', $orderCode)->lockForUpdate()->first();
                if (!$order) {
                    Log::warning('Pay2S webhook: order_code not found', ['order_code' => $orderCode]);
                    return ['ok' => true, 'matched' => false, 'order_code' => $orderCode];
                }
                if ($order->paid_at !== null) {
                    return ['ok' => true, 'skipped' => 'already_paid', 'order_code' => $orderCode];
                }

                $order->update([
                    'paid_at' => now(),
                    'paid_amount' => $amount,
                    'bank_transaction_id' => $bankTxId ?: null,
                    'bank_raw_payload' => json_encode($tx, JSON_UNESCAPED_UNICODE),
                ]);

                $order->refresh();
                $createdCsId = $this->tryAutoCreateCustomerService($order);

                return [
                    'ok' => true,
                    'matched' => true,
                    'order_id' => $order->id,
                    'order_code' => $orderCode,
                    'paid_amount' => $amount,
                    'customer_service_id' => $createdCsId,
                ];
            });
        } catch (\Throwable $e) {
            Log::error('Pay2S webhook: transaction failed — rolled back', [
                'order_code' => $orderCode,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return ['ok' => false, 'error' => 'transaction_failed', 'order_code' => $orderCode];
        }

        // Skipped/not matched → return ngay, không gửi noti
        if (!($result['matched'] ?? false)) {
            return $result;
        }

        // Telegram noti — đặt NGOÀI transaction (HTTP call không block DB lock)
        try {
            $order = PendingOrder::with('customer')->find($result['order_id']);
            if (!$order) {
                return $result;
            }

            $adminIds = array_filter(array_map('trim', explode(',', (string) env('TELEGRAM_ADMIN_IDS', ''))));
            $delta = $amount - (int) $order->amount;
            $deltaNote = $delta === 0
                ? ''
                : ($delta > 0
                    ? "\n\n⚠️ <i>Khách trả dư " . formatShortAmount($delta) . "</i>"
                    : "\n\n⚠️ <i>Khách trả thiếu " . formatShortAmount(abs($delta)) . "</i>");

            $customerLine = $order->customer
                ? "<code>{$order->customer->customer_code}</code> — <b>" . e($order->customer->name) . "</b>"
                : "<i>(chưa gắn KH)</i>";

            $lookupUrl = rtrim(config('app.url'), '/') . '/tra-cuu?code=' . urlencode((string) $order->order_code);

            $msg = "💰 <b>ĐÃ NHẬN TIỀN — Cám ơn quý khách đã mua hàng!</b>\n\n"
                . "👤 Mã khách hàng: {$customerLine}\n"
                . "📋 Mã đơn hàng: <code>{$order->order_code}</code>\n"
                . "💵 Số tiền: <b>" . formatShortAmount($amount) . "</b>"
                . $deltaNote
                . "\n\n"
                . "🔗 <a href=\"{$lookupUrl}\">Theo dõi và xem chi tiết đơn hàng tại đây</a>";

            foreach ($adminIds as $chatId) {
                $bot->sendMessage($chatId, $msg, ['disable_web_page_preview' => true]);
            }
        } catch (\Throwable $e) {
            Log::error('Pay2S webhook: Telegram noti failed', ['error' => $e->getMessage()]);
        }

        unset($result['order_id']);
        return $result;
    }

    /**
     * Hybrid flow:
     *   - Đơn từ bot Telegram đã có CustomerService với status='pending' → ACTIVATE
     *     (đổi sang 'active' + set activated_at = now, expires_at = now + duration_days).
     *   - Đơn web/đơn cũ chưa có CustomerService → CREATE mới luôn với status='active'
     *     (nếu đủ data structured).
     *
     * Trả về customer_service_id của CS đã activate/tạo, null nếu thiếu data.
     */
    private function tryAutoCreateCustomerService(PendingOrder $order): ?int
    {
        // CASE 1: Đã có CustomerService (do bot tạo pending) → activate
        if ($order->customer_service_id) {
            return $this->activatePendingCustomerService($order);
        }

        // CASE 2: Chưa có CS — tạo mới với status='active' nếu đủ data
        $missing = array_filter([
            'customer_id' => !$order->customer_id,
            'service_package_id' => !$order->service_package_id,
            'account_email' => empty($order->account_email),
            'duration_days' => !$order->duration_days,
        ]);

        if (!empty($missing)) {
            Log::info('Pay2S webhook: skip auto-create CustomerService (thiếu data structured — đơn cần fill thủ công qua web)', [
                'order_id' => $order->id,
                'order_code' => $order->order_code,
                'missing' => array_keys($missing),
            ]);
            return null;
        }

        return $this->createActiveCustomerService($order);
    }

    /**
     * Activate CS pending → đổi status='active', set activated_at + expires_at.
     * Tạo Profit nếu bot có nhập profit_amount.
     *
     * Chạy trong outer DB::transaction (processTransaction). Không try-catch ở đây
     * để exception bubble lên rollback toàn bộ — tránh state nửa-active.
     */
    private function activatePendingCustomerService(PendingOrder $order): ?int
    {
        $cs = \App\Models\CustomerService::find($order->customer_service_id);
        if (!$cs) {
            Log::warning('Pay2S webhook: CS link tới đơn không tồn tại — fallback create mới', [
                'order_id' => $order->id,
                'cs_id_orphan' => $order->customer_service_id,
            ]);
            $order->update(['customer_service_id' => null]);
            return $this->createActiveCustomerService($order);
        }

        // Idempotent — webhook bắn lại lần 2 sẽ không double-activate
        if ($cs->status === 'active' && $cs->activated_at) {
            return $cs->id;
        }

        $now = now();
        $expiresAt = $now->copy()->addDays((int) ($order->duration_days ?? $cs->duration_days ?? 0));

        $cs->update([
            'status' => 'active',
            'activated_at' => $now,
            'expires_at' => $expiresAt,
            'internal_notes' => trim(($cs->internal_notes ?? '') . "\n\n💰 Thanh toán xác nhận qua Pay2S ({$now->format('d/m/Y H:i')})"),
        ]);

        // Tạo Profit nếu chưa có
        if (!empty($order->profit_amount) && $order->profit_amount > 0 && !$cs->profit) {
            \App\Models\Profit::create([
                'customer_service_id' => $cs->id,
                'profit_amount' => $order->profit_amount,
                'notes' => "Tự tạo từ đơn {$order->order_code} qua Pay2S webhook",
            ]);
        }

        $order->update(['status' => 'completed']);

        Log::info('Pay2S webhook: activated pending CustomerService', [
            'order_id' => $order->id,
            'order_code' => $order->order_code,
            'customer_service_id' => $cs->id,
        ]);

        return $cs->id;
    }

    /**
     * Tạo CS active mới (cho đơn web nhanh hoặc đơn cũ không có CS pending).
     * Chạy trong outer DB::transaction — không try-catch ở đây.
     */
    private function createActiveCustomerService(PendingOrder $order): ?int
    {
        $now = now();
        $expiresAt = $now->copy()->addDays((int) $order->duration_days);

        $internalNotes = "📋 Tự tạo từ đơn {$order->order_code} qua Pay2S webhook ({$now->format('d/m/Y H:i')})";
        if (!empty($order->family_code)) {
            $internalNotes .= "\nMã nhóm-gia đình: {$order->family_code}";
        }

        $cs = \App\Models\CustomerService::create([
            'pending_order_id' => $order->id,
            'customer_id' => $order->customer_id,
            'service_package_id' => $order->service_package_id,
            'login_email' => $order->account_email,
            'activated_at' => $now,
            'expires_at' => $expiresAt,
            'status' => 'active',
            'duration_days' => $order->duration_days,
            'warranty_days' => $order->warranty_days,
            'order_amount' => $order->amount,
            'family_code' => $order->family_code,
            'internal_notes' => $internalNotes,
        ]);

        if (!empty($order->profit_amount) && $order->profit_amount > 0) {
            \App\Models\Profit::create([
                'customer_service_id' => $cs->id,
                'profit_amount' => $order->profit_amount,
                'notes' => "Tự tạo từ đơn {$order->order_code} qua Pay2S webhook",
            ]);
        }

        $order->update([
            'customer_service_id' => $cs->id,
            'status' => 'completed',
        ]);

        Log::info('Pay2S webhook: created active CustomerService', [
            'order_id' => $order->id,
            'order_code' => $order->order_code,
            'customer_service_id' => $cs->id,
        ]);

        return $cs->id;
    }
}
