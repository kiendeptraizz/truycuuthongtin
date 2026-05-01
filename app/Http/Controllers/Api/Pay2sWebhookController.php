<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PendingOrder;
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

        // Idempotent — đã xử lý GD này rồi thì bỏ qua
        if ($bankTxId !== '' && PendingOrder::where('bank_transaction_id', $bankTxId)->exists()) {
            return ['ok' => true, 'skipped' => 'duplicate', 'tx' => $bankTxId];
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

        if ($order->paid_at !== null) {
            return ['ok' => true, 'skipped' => 'already_paid', 'order_code' => $orderCode];
        }

        $order->update([
            'paid_at' => now(),
            'paid_amount' => $amount,
            'bank_transaction_id' => $bankTxId ?: null,
            'bank_raw_payload' => json_encode($tx, JSON_UNESCAPED_UNICODE),
        ]);

        // Telegram noti
        try {
            $adminIds = array_filter(array_map('trim', explode(',', (string) env('TELEGRAM_ADMIN_IDS', ''))));
            $delta = $amount - (int) $order->amount;
            $deltaNote = $delta === 0
                ? ''
                : ($delta > 0 ? "\n⚠️ Khách trả dư " . formatShortAmount($delta) : "\n⚠️ Khách trả thiếu " . formatShortAmount(abs($delta)));

            $msg = sprintf(
                "💰 <b>ĐÃ NHẬN TIỀN</b>\n\n"
                    . "📋 Đơn: <code>%s</code>\n"
                    . "💵 Số tiền: <b>%s</b> (%sđ)%s\n"
                    . "📝 Ghi chú: %s\n"
                    . "🕐 %s\n\n"
                    . "👉 Vào web để fill thông tin đơn này.",
                $order->order_code,
                formatShortAmount($amount),
                number_format($amount, 0, ',', '.'),
                $deltaNote,
                $order->note ?: '—',
                now()->format('H:i:s d/m/Y')
            );

            foreach ($adminIds as $chatId) {
                $bot->sendMessage($chatId, $msg);
            }
        } catch (\Throwable $e) {
            Log::error('Pay2S webhook: Telegram noti failed', ['error' => $e->getMessage()]);
        }

        return [
            'ok' => true,
            'matched' => true,
            'order_code' => $orderCode,
            'paid_amount' => $amount,
        ];
    }
}
