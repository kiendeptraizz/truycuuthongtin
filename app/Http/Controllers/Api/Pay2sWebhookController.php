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
     * Pay2S webhook handler — nhận giao dịch tiền vào, match với pending order theo
     * order_code trong nội dung CK, mark paid và gửi noti Telegram.
     *
     * Pay2S thường gửi 1 trong các format:
     *   { "transaction_id":"...", "amount": 100000, "content": "...", "transfer_type": "in", ... }
     *   { "id":"...", "amount": ..., "description":"...", ... }
     * Endpoint accept linh hoạt, trích thông tin theo nhiều tên field.
     */
    public function __invoke(Request $request, TelegramBotService $bot): JsonResponse
    {
        $payload = $request->all();
        $rawJson = json_encode($payload, JSON_UNESCAPED_UNICODE);

        // 1) Verify token (Pay2S gắn token vào header Authorization hoặc body)
        $expected = (string) env('PAY2S_WEBHOOK_TOKEN', '');
        if ($expected !== '') {
            $auth = $request->header('Authorization', '');
            $token = preg_replace('/^(Bearer|Apikey|Token)\s+/i', '', $auth);
            if (!hash_equals($expected, $token) && !hash_equals($expected, (string) $request->input('token'))) {
                Log::warning('Pay2S webhook: invalid token', ['headers' => $request->headers->all()]);
                return response()->json(['ok' => false, 'error' => 'invalid_token'], 401);
            }
        }

        Log::info('Pay2S webhook received', ['payload' => $payload]);

        // 2) Trích các field linh hoạt
        $amount = (int) ($payload['amount'] ?? $payload['transferAmount'] ?? $payload['transfer_amount'] ?? 0);
        $content = (string) ($payload['content'] ?? $payload['description'] ?? $payload['transferContent'] ?? $payload['transfer_content'] ?? '');
        $bankTxId = (string) ($payload['transaction_id'] ?? $payload['transactionId'] ?? $payload['id'] ?? $payload['referenceNumber'] ?? $payload['reference_number'] ?? '');
        $direction = strtolower((string) ($payload['transfer_type'] ?? $payload['transferType'] ?? $payload['type'] ?? 'in'));

        if ($amount <= 0 || $content === '') {
            return response()->json(['ok' => false, 'error' => 'missing_fields', 'received' => array_keys($payload)], 422);
        }

        // Chỉ xử lý tiền vào
        if (!in_array($direction, ['in', 'credit', 'tien_vao', 'tienvao'], true)) {
            return response()->json(['ok' => true, 'skipped' => 'not_credit']);
        }

        // 3) Idempotent — đã xử lý GD này rồi thì bỏ qua
        if ($bankTxId !== '' && PendingOrder::where('bank_transaction_id', $bankTxId)->exists()) {
            return response()->json(['ok' => true, 'skipped' => 'duplicate']);
        }

        // 4) Tìm pending order theo order_code trong nội dung CK (regex DH-XXXXXX-XXX)
        if (!preg_match('/DH-\d{6}-\d{3}/', $content, $m)) {
            Log::info('Pay2S webhook: no order_code matched in content', ['content' => $content]);
            return response()->json(['ok' => true, 'matched' => false]);
        }
        $orderCode = $m[0];

        $order = PendingOrder::where('order_code', $orderCode)->first();
        if (!$order) {
            Log::warning('Pay2S webhook: order_code not found', ['order_code' => $orderCode]);
            return response()->json(['ok' => true, 'matched' => false, 'order_code' => $orderCode]);
        }

        if ($order->status !== 'pending' && $order->paid_at !== null) {
            return response()->json(['ok' => true, 'skipped' => 'already_paid']);
        }

        // 5) Mark paid
        $order->update([
            'paid_at' => now(),
            'paid_amount' => $amount,
            'bank_transaction_id' => $bankTxId ?: null,
            'bank_raw_payload' => $rawJson,
        ]);

        // 6) Telegram noti — gửi cho admin
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

        return response()->json([
            'ok' => true,
            'matched' => true,
            'order_code' => $orderCode,
            'paid_amount' => $amount,
        ]);
    }
}
