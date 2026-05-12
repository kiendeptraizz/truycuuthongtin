<?php

namespace App\Console\Commands\Concerns;

use App\Models\PendingOrder;
use Illuminate\Support\Facades\Log;

/**
 * Pending order actions từ bot Telegram (button trong /list + lệnh thủ công).
 *
 * Tách trait từ god-class TelegramListenCommand (P1.3 audit).
 *
 * - sendListPending: render /list (10 đơn pending mới nhất)
 * - handleViewQrCallback: button 📷 Xem QR
 * - handleMarkPaidCallback: button 💳 Đã trả (race-safe via PaymentService)
 * - handleCancelOrderCallback: button ❌ Huỷ (callback)
 * - cancelOrder: lệnh /cancel DH-XXX-XXX
 */
trait HandlesPendingOrderActions
{
    private function handleCancelOrderCallback(int|string $chatId, string $userId, int $orderId): void
    {
        $order = PendingOrder::find($orderId);
        if (!$order) {
            $this->bot->sendMessage($chatId, "❌ Đơn không tồn tại (đã bị xoá?).");
            return;
        }
        if ($order->status !== 'pending') {
            $this->bot->sendMessage($chatId, "⚠️ Đơn <code>{$order->order_code}</code> không thể huỷ (trạng thái: {$order->status}).");
            return;
        }
        if ($order->paid_at) {
            $this->bot->sendMessage($chatId, "⚠️ Đơn <code>{$order->order_code}</code> đã thanh toán — không nên huỷ.");
            return;
        }

        $order->update(['status' => 'cancelled']);
        Log::info('Bot Telegram: cancelled pending order via /list button', [
            'order_id' => $order->id,
            'order_code' => $order->order_code,
            'by_user' => $userId,
        ]);

        $this->bot->sendMessage($chatId, "✅ Đã huỷ đơn <code>{$order->order_code}</code>.");
    }

    /**
     * User bấm "📷 Xem QR" — gửi lại ảnh QR thanh toán (admin forward cho khách).
     */
    private function handleViewQrCallback(int|string $chatId, int $orderId): void
    {
        $order = PendingOrder::find($orderId);
        if (!$order) {
            $this->bot->sendMessage($chatId, "❌ Đơn không tồn tại.");
            return;
        }
        if ($order->paid_at) {
            $this->bot->sendMessage(
                $chatId,
                "ℹ️ Đơn <code>{$order->order_code}</code> đã được thanh toán — không cần QR nữa."
            );
            return;
        }
        if ($order->status === 'cancelled') {
            $this->bot->sendMessage($chatId, "❌ Đơn <code>{$order->order_code}</code> đã huỷ.");
            return;
        }

        $caption = sprintf(
            "📷 <b>%s</b>\n💵 %s",
            $order->order_code,
            formatShortAmount((int) $order->amount)
        );
        if ($order->note) {
            $caption .= "\n📝 " . e($order->note);
        }

        $this->sendPhotoSafe($chatId, $order->qrCodeUrl(), $caption);
    }

    /**
     * User bấm nút "💳 Đã trả" trong list /list — manual mark paid (khi Pay2S
     * chưa nhận, hoặc khách CK bằng cách khác).
     */
    private function handleMarkPaidCallback(int|string $chatId, string $userId, int $orderId): void
    {
        $order = PendingOrder::find($orderId);
        if (!$order) {
            $this->bot->sendMessage($chatId, "❌ Đơn không tồn tại.");
            return;
        }
        if ($order->status === 'cancelled') {
            $this->bot->sendMessage($chatId, "⚠️ Đơn <code>{$order->order_code}</code> đã huỷ — không thể mark paid.");
            return;
        }

        // Race fix (P0.3): KHÔNG pre-check paid_at ngoài transaction. Double-click
        // có thể qua check cùng lúc (cả 2 thấy paid_at=null) → 2 transaction race.
        // Rely on PaymentService::markOrderPaid trả status='already_paid' khi
        // lock thấy paid_at != null trong transaction (atomic).
        $payment = app(\App\Services\PaymentService::class);
        $bankTxId = "manual-bot-{$userId}-" . now()->timestamp;
        $rawPayload = json_encode([
            'source' => 'manual_telegram',
            'admin_telegram_id' => $userId,
            'marked_at' => now()->toIso8601String(),
        ], JSON_UNESCAPED_UNICODE);

        $result = $payment->markOrderPaid(
            $order,
            (int) $order->amount,
            $bankTxId,
            $rawPayload,
            'manual'
        );

        if (!$result['ok']) {
            $this->bot->sendMessage(
                $chatId,
                "❌ Lỗi đánh dấu thanh toán: " . ($result['error'] ?? $result['status'])
            );
            return;
        }

        // already_paid → click double, không gửi noti success thứ 2
        if (($result['status'] ?? '') === 'already_paid') {
            $this->bot->sendMessage(
                $chatId,
                "⚠️ Đơn <code>{$order->order_code}</code> đã được đánh dấu thanh toán trước đó."
            );
            return;
        }

        if (!empty($result['cs_id'])) {
            // CS đã activate → reply success
            $this->bot->sendMessage(
                $chatId,
                "💰 Đã đánh dấu đơn <code>{$order->order_code}</code> ("
                    . formatShortAmount((int) $order->amount) . ") là <b>đã thanh toán</b>.\n"
                    . "✅ Đã tạo/activate dịch vụ <b>#{$result['cs_id']}</b> cho khách."
            );
        } else {
            // Chưa đủ data → reply kèm link fill rõ ràng
            $fillUrl = rtrim(config('app.url'), '/') . '/admin/pending-orders/' . $order->id . '/fill';
            $this->bot->sendMessage(
                $chatId,
                "💰 Đã đánh dấu đơn <code>{$order->order_code}</code> ("
                    . formatShortAmount((int) $order->amount) . ") là <b>đã thanh toán</b>.\n\n"
                    . "⚠️ <b>Chưa đủ data structured</b> — cần fill thủ công qua web.\n\n"
                    . "🔗 Mở form fill ngay:\n{$fillUrl}",
                ['disable_web_page_preview' => true]
            );
        }
    }

    private function sendListPending(int|string $chatId): void
    {
        // Show TẤT CẢ đơn pending (không filter ngày — đồng bộ với web
        // /admin/pending-orders mặc định không filter date). Trước đây chỉ
        // show today → user confused vì web 3 đơn còn bot 1 đơn.
        $pendingQuery = PendingOrder::where('status', 'pending');
        $totalPending = (clone $pendingQuery)->count();

        $orders = $pendingQuery
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        if ($orders->isEmpty()) {
            $this->bot->sendMessage($chatId, "📭 Không có đơn pending nào.");
            return;
        }

        // Header tổng hợp + tổng tiền
        $total = 0;
        foreach ($orders as $o) $total += $o->amount;
        $header = "📋 <b>Đơn pending ({$totalPending}):</b>";
        if ($totalPending > 10) {
            $header .= "\n<i>Hiện 10 đơn mới nhất — còn " . ($totalPending - 10) . " đơn cũ. Vào /admin/pending-orders để xem hết.</i>";
        }
        $header .= "\n\n💵 Tổng " . $orders->count() . " đơn: <b>" . formatShortAmount($total) . "</b> ("
                . number_format($total, 0, ',', '.') . "đ)";
        $this->bot->sendMessage($chatId, $header);

        // Mỗi đơn 1 message kèm inline button — tránh nhồi vào 1 message text dài
        // không có cách click huỷ từng đơn riêng
        foreach ($orders as $o) {
            // Đơn từ ngày khác → show ngày luôn (vì giờ list không filter today)
            $timeLabel = $o->created_at->isToday()
                ? $o->created_at->format('H:i')
                : $o->created_at->format('H:i d/m');
            $line = sprintf(
                "• <b>%s</b>\n💵 %s · 🕐 %s%s",
                $o->order_code,
                formatShortAmount($o->amount),
                $timeLabel,
                $o->note ? "\n📝 " . e($o->note) : ''
            );
            // Layer paid status hiển thị nếu có (đơn đã CK nhưng chưa fill)
            if ($o->paid_at) {
                $line .= "\n✅ <i>Đã thanh toán {$o->paid_at->format('H:i d/m')}</i>";
            }

            $buttons = [];
            if (!$o->paid_at) {
                $buttons[] = ['text' => '📷 Xem QR', 'callback_data' => "po_qr_{$o->id}"];
                $buttons[] = ['text' => '💳 Đã trả', 'callback_data' => "po_paid_{$o->id}"];
            }
            $buttons[] = ['text' => '❌ Huỷ', 'callback_data' => "po_huy_{$o->id}"];

            $this->bot->sendMessage(
                $chatId,
                $line,
                ['reply_markup' => json_encode(['inline_keyboard' => [$buttons]])]
            );
        }
    }

    private function cancelOrder(int|string $chatId, string $orderCode): void
    {
        $order = PendingOrder::where('order_code', $orderCode)->first();
        if (!$order) {
            $this->bot->sendMessage($chatId, "❌ Không tìm thấy đơn <code>{$orderCode}</code>");
            return;
        }
        if ($order->status !== 'pending') {
            $this->bot->sendMessage($chatId, "⚠️ Đơn <code>{$orderCode}</code> không thể huỷ (status: {$order->status})");
            return;
        }
        $order->update(['status' => 'cancelled']);
        $this->bot->sendMessage($chatId, "✅ Đã huỷ đơn <code>{$orderCode}</code>");
    }
}
