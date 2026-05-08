<?php

namespace App\Console\Commands\Concerns;

use Illuminate\Support\Facades\Log;

/**
 * Refund flow trên bot Telegram.
 *
 * Tách trait từ god-class TelegramListenCommand (P1.3 audit). Flow:
 *   1. Click 💰 "Tính tiền hoàn" trong /dh → handleRefundPreviewCallback
 *      → preview + 2 button confirm/cancel.
 *   2. Click ✅ → handleRefundConfirmCallback → DB::transaction + lockForUpdate,
 *      recompute amount, update status='cancelled' + refund_reason auto.
 *   3. Click ❌ → handleRefundCancelCallback → no-op.
 *
 * Audit log tự ghi qua CustomerServiceObserver khi update CS.
 */
trait HandlesRefundFlow
{
    /**
     * User bấm "💰 Tính tiền hoàn" — gửi preview + 2 button confirm/cancel.
     *
     * Trước đây gắn link web vào "Mở web để xác nhận" nên admin phải mở browser
     * + login + click confirm + gõ lý do. Giờ confirm trực tiếp trên bot, lý do
     * auto-fill "Hoàn tiền qua bot Telegram" — admin chỉ cần 1 chạm.
     *
     * Vẫn giữ link web (làm secondary) cho trường hợp admin muốn override
     * amount hoặc gõ lý do tự do.
     */
    private function handleRefundPreviewCallback(int|string $chatId, int $csId): void
    {
        $cs = \App\Models\CustomerService::with(['customer', 'servicePackage'])->find($csId);
        if (!$cs) {
            $this->bot->sendMessage($chatId, "❌ Dịch vụ không tồn tại.");
            return;
        }

        $calc = app(\App\Services\RefundCalculator::class)->compute($cs);

        if (!$calc['ok']) {
            $reasonMap = [
                'already_refunded' => 'Đơn đã được hoàn tiền trước đó',
                'already_cancelled' => 'Đơn đã huỷ — không thể hoàn',
                'no_order_amount' => 'Đơn không có số tiền dịch vụ → không tính được',
                'no_expires_at' => 'Đơn thiếu ngày hết hạn → không tính được',
            ];
            $reason = $reasonMap[$calc['reason'] ?? ''] ?? ($calc['reason'] ?? 'Không xác định');
            $this->bot->sendMessage($chatId, "⚠️ Không thể tính tiền hoàn: {$reason}.");
            return;
        }

        $refundAmount = (int) ($calc['refund_amount'] ?? 0);

        $lines = [
            "💰 <b>TÍNH TIỀN HOÀN</b> — đơn <code>{$cs->order_code}</code>",
            "",
            "💵 Số tiền đơn: <b>" . formatShortAmount((int) ($calc['order_amount'] ?? 0)) . "</b>",
        ];

        if ($calc['mode'] === 'full') {
            $lines[] = "🟢 Đơn chưa kích hoạt → hoàn <b>FULL</b>.";
        } elseif ($calc['mode'] === 'expired') {
            $lines[] = "🔴 Đơn đã hết hạn → hoàn <b>0đ</b>.";
        } else {
            // partial
            $lines[] = "📅 Tổng thời hạn: {$calc['total_days']} ngày";
            $lines[] = "✅ Đã dùng: {$calc['days_used']} ngày";
            $lines[] = "⏳ Còn lại: <b>{$calc['days_remaining']} ngày</b> ({$calc['percent_remaining']}%)";
        }

        $lines[] = "";
        $lines[] = "💸 <b>Hoàn đề xuất: " . formatShortAmount($refundAmount) . "</b>";
        $lines[] = "";
        $lines[] = "<i>Bấm <b>✅ Xác nhận</b> để ghi nhận hoàn ngay (status đơn → cancelled). Lý do sẽ tự ghi \"Hoàn tiền qua bot Telegram\". Nhớ chuyển khoản cho khách thủ công.</i>";

        $appUrl = rtrim(config('app.url'), '/');

        $this->bot->sendMessage(
            $chatId,
            implode("\n", $lines),
            [
                'disable_web_page_preview' => true,
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [
                            ['text' => '✅ Xác nhận hoàn ' . formatShortAmount($refundAmount), 'callback_data' => "cs_refund_ok_{$cs->id}"],
                        ],
                        [
                            ['text' => '❌ Huỷ', 'callback_data' => "cs_refund_no_{$cs->id}"],
                            ['text' => '🌐 Mở web (tuỳ chỉnh)', 'url' => "{$appUrl}/admin/customer-services/{$cs->id}/refund"],
                        ],
                    ],
                ]),
            ]
        );
    }

    /**
     * User bấm "✅ Xác nhận hoàn" — thực hiện refund ngay trên bot, không cần lý do.
     *
     * Recompute amount tại thời điểm confirm (tránh dùng amount cũ nếu admin để
     * preview lâu — days_used có thể đã tăng). Atomic via DB::transaction +
     * lockForUpdate (chống double-click race khi 2 admin cùng bấm).
     */
    private function handleRefundConfirmCallback(int|string $chatId, int $csId): void
    {
        $cs = \App\Models\CustomerService::find($csId);
        if (!$cs) {
            $this->bot->sendMessage($chatId, "❌ Dịch vụ không tồn tại.");
            return;
        }

        try {
            $result = \Illuminate\Support\Facades\DB::transaction(function () use ($csId) {
                $cs = \App\Models\CustomerService::lockForUpdate()->find($csId);
                if (!$cs) {
                    return ['status' => 'not_found'];
                }
                if ($cs->refunded_at !== null) {
                    return ['status' => 'already_refunded', 'cs' => $cs];
                }
                if ($cs->status === 'cancelled') {
                    return ['status' => 'already_cancelled', 'cs' => $cs];
                }

                // Recompute amount để tránh stale (preview sent lâu trước)
                $calc = app(\App\Services\RefundCalculator::class)->compute($cs);
                if (!($calc['ok'] ?? false)) {
                    return ['status' => 'calc_fail', 'reason' => $calc['reason'] ?? 'unknown'];
                }

                $refundAmount = (int) ($calc['refund_amount'] ?? 0);

                $cs->update([
                    'refund_amount' => $refundAmount,
                    'refunded_at' => now(),
                    'refund_reason' => 'Hoàn tiền qua bot Telegram',
                    'status' => 'cancelled',
                ]);

                return ['status' => 'ok', 'cs' => $cs->fresh(), 'amount' => $refundAmount];
            });
        } catch (\Throwable $e) {
            Log::error('Refund confirm via bot failed', [
                'cs_id' => $csId,
                'error' => $e->getMessage(),
            ]);
            $this->bot->sendMessage($chatId, "❌ Lỗi khi hoàn tiền: " . $e->getMessage());
            return;
        }

        $code = $cs->order_code ?: "#{$cs->id}";
        switch ($result['status']) {
            case 'not_found':
                $this->bot->sendMessage($chatId, "❌ Dịch vụ không tồn tại.");
                return;
            case 'already_refunded':
                $when = $result['cs']->refunded_at ? formatDate($result['cs']->refunded_at, 'H:i d/m/Y') : '';
                $this->bot->sendMessage($chatId, "⚠️ Đơn <code>{$code}</code> đã hoàn từ trước ({$when}).");
                return;
            case 'already_cancelled':
                $this->bot->sendMessage($chatId, "⚠️ Đơn <code>{$code}</code> đã huỷ — không thể hoàn nữa.");
                return;
            case 'calc_fail':
                $this->bot->sendMessage($chatId, "⚠️ Không tính được tiền hoàn (lý do: {$result['reason']}).");
                return;
            case 'ok':
                Log::info('Refund confirmed via bot', [
                    'cs_id' => $csId,
                    'order_code' => $code,
                    'amount' => $result['amount'],
                    'chat_id' => $chatId,
                ]);
                $this->bot->sendMessage(
                    $chatId,
                    "✅ Đã hoàn <b>" . formatShortAmount($result['amount']) . "</b> cho đơn <code>{$code}</code>.\n"
                        . "📌 Status: <b>cancelled</b>\n"
                        . "💸 Vui lòng <b>chuyển khoản thủ công</b> cho khách."
                );
                return;
        }
    }

    /**
     * User bấm "❌ Huỷ" trong preview refund — chỉ acknowledge, không làm gì.
     */
    private function handleRefundCancelCallback(int|string $chatId, int $csId): void
    {
        $this->bot->sendMessage($chatId, "👌 Đã huỷ thao tác hoàn tiền (đơn không thay đổi).");
    }
}
