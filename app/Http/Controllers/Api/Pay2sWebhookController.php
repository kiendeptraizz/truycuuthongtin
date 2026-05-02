<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PendingOrder;
use App\Services\PaymentService;
use App\Services\TelegramBotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

        // Idempotent layer 1 — bankTxId duplicate
        if ($bankTxId !== '' && PendingOrder::where('bank_transaction_id', $bankTxId)->exists()) {
            return ['ok' => true, 'skipped' => 'duplicate', 'tx' => $bankTxId];
        }
        if ($bankTxId === '') {
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

        $order = PendingOrder::where('order_code', $orderCode)->first();
        if (!$order) {
            Log::warning('Pay2S webhook: order_code not found', ['order_code' => $orderCode]);
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
                . "🔗 <a href=\"{$lookupUrl}\">Theo dõi và xem chi tiết đơn hàng tại đây</a>";

            foreach ($adminIds as $chatId) {
                $bot->sendMessage($chatId, $msg, ['disable_web_page_preview' => true]);
            }
        } catch (\Throwable $e) {
            Log::error('Pay2S webhook: Telegram noti failed', ['error' => $e->getMessage()]);
        }
    }
}
