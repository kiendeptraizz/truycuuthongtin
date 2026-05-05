<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PendingOrder;
use App\Services\PaymentService;
use App\Services\TelegramBotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
     * Logic activate/create CS đã tách sang PaymentService — chia sẻ với
     * admin manual mark paid.
     */
    public function __invoke(Request $request, TelegramBotService $bot, PaymentService $payment): JsonResponse
    {
        // 1) Verify token
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

        $transactions = $payload['transactions'] ?? [$payload];
        if (!is_array($transactions)) {
            $transactions = [$payload];
        }

        $results = [];
        foreach ($transactions as $tx) {
            $results[] = $this->processTransaction($tx, $bot, $payment);
        }

        return response()->json(['ok' => true, 'processed' => $results]);
    }

    private function processTransaction(array $tx, TelegramBotService $bot, PaymentService $payment): array
    {
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

        // Verify HMAC checksum (defense-in-depth — kẻ xấu có token vẫn không
        // forge được giao dịch nếu không có secret). Default OFF nếu chưa
        // cấu hình PAY2S_HMAC_SECRET (giữ backward compat).
        if (!$this->verifyChecksum($tx)) {
            Log::warning('Pay2S webhook: invalid checksum', [
                'tx_id' => $bankTxId,
                'content' => $content,
                'amount' => $amount,
            ]);
            return ['ok' => false, 'error' => 'invalid_checksum'];
        }

        // Idempotent layer 1 — bankTxId duplicate
        // - Đơn lẻ: bank_transaction_id = $bankTxId exact
        // - Lô đơn: bank_transaction_id = "$bankTxId-0", "$bankTxId-1", ... (suffix index)
        // → check cả 2 pattern để chặn Pay2S retry trước khi vào DB transaction.
        if ($bankTxId !== '') {
            $duplicate = PendingOrder::where('bank_transaction_id', $bankTxId)
                ->orWhere('bank_transaction_id', 'LIKE', $bankTxId . '-%')
                ->exists();
            if ($duplicate) {
                return ['ok' => true, 'skipped' => 'duplicate', 'tx' => $bankTxId];
            }
        }
        if ($bankTxId === '') {
            Log::warning('Pay2S webhook: empty bankTxId, fallback to paid_at lock check', [
                'content' => $content,
                'amount' => $amount,
            ]);
        }

        // PRIORITY 1: Match GROUP code trước (lô nhiều đơn): "GR-260502-001", "GR260502001"
        // Nếu match → mark TẤT CẢ PO trong lô paid, gửi 1 noti gộp.
        if (preg_match('/GR[-\s]?(\d{6})[-\s]?(\d{3})/i', $content, $g)) {
            $groupCode = sprintf('GR-%s-%s', $g[1], $g[2]);
            $groupResult = $payment->markGroupPaid(
                $groupCode,
                $amount,
                $bankTxId ?: null,
                json_encode($tx, JSON_UNESCAPED_UNICODE),
                'pay2s'
            );

            // Chỉ gửi noti khi có đơn thực sự chuyển pending → paid LẦN NÀY.
            // Tránh spam noti khi Pay2S retry webhook (tất cả đơn đã paid trước đó).
            if ($groupResult['ok'] && ($groupResult['newly_paid_count'] ?? 0) > 0) {
                $this->sendGroupPaidNotification($bot, $groupCode, $amount, $groupResult);
            }

            return [
                'ok' => $groupResult['ok'],
                'matched' => true,
                'group_code' => $groupCode,
                'count' => $groupResult['count'] ?? 0,
                'status' => $groupResult['status'],
                'orders' => $groupResult['orders'] ?? [],
            ];
        }

        // PRIORITY 2: Match order_code linh hoạt: "DH-260502-001", "DH260502001", "DH 260502 001"
        if (!preg_match('/DH[-\s]?(\d{6})[-\s]?(\d{3})/i', $content, $m)) {
            Log::info('Pay2S webhook: no order_code or group_code in content', ['content' => $content]);
            // Cảnh báo admin — khách CK rồi nhưng content thiếu mã đơn → cần mark thủ công
            $this->sendUnmatchedNotification($bot, $bankTxId, $amount, $content, 'no_code', null);
            return ['ok' => true, 'matched' => false, 'content' => $content];
        }
        $orderCode = sprintf('DH-%s-%s', $m[1], $m[2]);

        $order = PendingOrder::where('order_code', $orderCode)->first();
        if (!$order) {
            Log::warning('Pay2S webhook: order_code not found', ['order_code' => $orderCode]);
            // Cảnh báo admin — khách gõ mã sai (typo) → cần mark thủ công đúng đơn
            $this->sendUnmatchedNotification($bot, $bankTxId, $amount, $content, 'order_not_found', $orderCode);
            return ['ok' => true, 'matched' => false, 'order_code' => $orderCode];
        }

        // Delegate atomic flow sang PaymentService
        $result = $payment->markOrderPaid(
            $order,
            $amount,
            $bankTxId ?: null,
            json_encode($tx, JSON_UNESCAPED_UNICODE),
            'pay2s'
        );

        if (!$result['ok'] || in_array($result['status'], ['already_paid', 'not_found'], true)) {
            return [
                'ok' => $result['ok'],
                'matched' => true,
                'order_code' => $orderCode,
                'status' => $result['status'],
            ];
        }

        // Telegram noti — ngoài transaction
        $this->sendPaidNotification($bot, $order->refresh(), $amount);

        return [
            'ok' => true,
            'matched' => true,
            'order_code' => $orderCode,
            'paid_amount' => $amount,
            'customer_service_id' => $result['cs_id'] ?? null,
        ];
    }

    /**
     * Telegram noti khi cả LÔ đơn được mark paid. 1 message gộp thay vì N
     * message rời rạc. Link tracking trỏ đến /tra-cuu?code=GR-XXX để khách
     * thấy đủ N service vừa mua trong cùng trang.
     */
    private function sendGroupPaidNotification(TelegramBotService $bot, string $groupCode, int $amount, array $groupResult): void
    {
        try {
            $orders = PendingOrder::where('group_code', $groupCode)
                ->with('customer', 'servicePackage')
                ->orderBy('order_code')
                ->get();

            if ($orders->isEmpty()) return;

            $customer = $orders->first()->customer;
            $totalExpected = (int) $groupResult['total_expected'];
            $delta = (int) $groupResult['delta'];

            $deltaNote = $delta === 0
                ? ''
                : ($delta > 0
                    ? "\n⚠️ <i>Khách trả dư " . formatShortAmount($delta) . " (so với tổng lô " . formatShortAmount($totalExpected) . ")</i>"
                    : "\n⚠️ <i>Khách trả thiếu " . formatShortAmount(abs($delta)) . " (so với tổng lô " . formatShortAmount($totalExpected) . ")</i>");

            $customerLine = $customer
                ? "<code>{$customer->customer_code}</code> — <b>" . e($customer->name) . "</b>"
                : "<i>(chưa gắn KH)</i>";

            $orderLines = $orders->map(function ($o) {
                $pkg = $o->servicePackage->name ?? 'N/A';
                return "  • <code>{$o->order_code}</code> — " . e($pkg) . " (" . formatShortAmount((int) $o->amount) . ")";
            })->implode("\n");

            $lookupUrl = rtrim(config('app.url'), '/') . '/tra-cuu?code=' . urlencode($groupCode);

            $msg = "💰 <b>ĐÃ NHẬN TIỀN — Cám ơn quý khách đã mua hàng!</b>\n\n"
                . "👤 Mã khách hàng: {$customerLine}\n"
                . "🛒 Mã lô: <code>{$groupCode}</code> ({$groupResult['count']} đơn)\n"
                . $orderLines . "\n\n"
                . "💵 Tổng tiền nhận: <b>" . formatShortAmount($amount) . "</b>"
                . $deltaNote
                . "\n\n"
                . "🔗 Link theo dõi đơn hàng:\n{$lookupUrl}";

            $adminIds = array_filter(array_map('trim', explode(',', (string) env('TELEGRAM_ADMIN_IDS', ''))));
            foreach ($adminIds as $chatId) {
                $bot->sendMessage($chatId, $msg, ['disable_web_page_preview' => true]);
            }
        } catch (\Throwable $e) {
            Log::error('Pay2S webhook: Group Telegram noti failed', [
                'group_code' => $groupCode,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Verify HMAC checksum của transaction từ Pay2S.
     *
     * Cấu hình env (default OFF — backward compat):
     *   PAY2S_HMAC_SECRET=<secret từ Pay2S dashboard>
     *   PAY2S_HMAC_FIELDS=id,transferAmount,content   (mặc định)
     *   PAY2S_HMAC_ALGO=md5                            (md5/sha1/sha256)
     *   PAY2S_HMAC_SEPARATOR=|
     *
     * User cần xem tài liệu Pay2S để biết exact format HMAC rồi điền 4
     * env này. Nếu PAY2S_HMAC_SECRET trống → skip verify (return true).
     *
     * @return bool true nếu valid hoặc HMAC chưa bật; false nếu fail.
     */
    private function verifyChecksum(array $tx): bool
    {
        $secret = (string) env('PAY2S_HMAC_SECRET', '');
        if ($secret === '') {
            // HMAC chưa cấu hình — giữ behavior cũ (chỉ verify token Bearer)
            return true;
        }

        $expected = (string) ($tx['checksum'] ?? '');
        if ($expected === '') {
            // HMAC bật mà payload không có checksum → reject
            return false;
        }

        $fields = explode(',', (string) env('PAY2S_HMAC_FIELDS', 'id,transferAmount,content'));
        $separator = (string) env('PAY2S_HMAC_SEPARATOR', '|');
        $algo = (string) env('PAY2S_HMAC_ALGO', 'md5');

        $parts = [];
        foreach ($fields as $f) {
            $f = trim($f);
            $parts[] = (string) ($tx[$f] ?? '');
        }
        $payload = implode($separator, $parts);

        try {
            $computed = hash_hmac($algo, $payload, $secret);
        } catch (\Throwable $e) {
            Log::error('Pay2S HMAC: compute failed', [
                'algo' => $algo,
                'error' => $e->getMessage(),
            ]);
            return false;
        }

        return hash_equals(strtolower($expected), strtolower($computed));
    }

    private function sendPaidNotification(TelegramBotService $bot, PendingOrder $order, int $amount): void
    {
        try {
            $order->loadMissing('customer');
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
                . "🔗 Link theo dõi đơn hàng:\n{$lookupUrl}";

            foreach ($adminIds as $chatId) {
                $bot->sendMessage($chatId, $msg, ['disable_web_page_preview' => true]);
            }
        } catch (\Throwable $e) {
            Log::error('Pay2S webhook: Telegram noti failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Cảnh báo admin khi Pay2S nhận giao dịch nhưng KHÔNG match được đơn hàng:
     *   - reason='no_code': content CK không có mã đơn (DH-XXX/GR-XXX). Khách quên ghi.
     *   - reason='order_not_found': content có mã DH-XXX nhưng không tồn tại trong DB
     *     (khách gõ sai số / mã đơn cũ đã xoá).
     *
     * Idempotent qua Cache key 'pay2s_unmatched_<bankTxId>' TTL 1h — Pay2S retry
     * 2-3 lần với cùng tx, chỉ gửi noti 1 lần.
     */
    private function sendUnmatchedNotification(
        TelegramBotService $bot,
        string $bankTxId,
        int $amount,
        string $content,
        string $reason,
        ?string $attemptedOrderCode
    ): void {
        try {
            // Idempotent — chặn duplicate noti khi Pay2S retry
            if ($bankTxId !== '') {
                $cacheKey = "pay2s_unmatched_{$bankTxId}";
                if (Cache::has($cacheKey)) {
                    return;
                }
                Cache::put($cacheKey, true, now()->addHour());
            }

            $reasonLine = match ($reason) {
                'no_code' => "❌ <b>Lý do:</b> Khách CK <b>không ghi mã đơn</b> trong nội dung.",
                'order_not_found' => "❌ <b>Lý do:</b> Mã <code>{$attemptedOrderCode}</code> KHÔNG tồn tại trong hệ thống (khách gõ sai?).",
                default => "❌ <b>Lý do:</b> Không match đơn nào.",
            };

            $pendingUrl = rtrim(config('app.url'), '/') . '/admin/pending-orders';

            $msg = "⚠️ <b>CK KHÔNG MATCH ĐƠN — Cần mark thủ công!</b>\n\n"
                . "💵 <b>Số tiền:</b> " . formatShortAmount($amount) . " (" . number_format($amount, 0, ',', '.') . "đ)\n"
                . "📝 <b>Nội dung CK:</b> <code>" . e(mb_substr($content, 0, 200)) . "</code>\n"
                . "🆔 <b>Bank TX:</b> <code>" . e($bankTxId) . "</code>\n\n"
                . $reasonLine . "\n\n"
                . "🔗 <a href=\"{$pendingUrl}\">Vào /admin/pending-orders để mark thủ công</a>\n"
                . "<i>Hoặc trong bot: 📋 Đơn pending → bấm 💳 Đã trả trên đơn tương ứng.</i>";

            $adminIds = array_filter(array_map('trim', explode(',', (string) env('TELEGRAM_ADMIN_IDS', ''))));
            foreach ($adminIds as $chatId) {
                $bot->sendMessage($chatId, $msg, ['disable_web_page_preview' => true]);
            }
        } catch (\Throwable $e) {
            Log::error('Pay2S webhook: sendUnmatchedNotification failed', [
                'bank_tx' => $bankTxId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
