<?php

namespace App\Console\Commands\Concerns;

/**
 * Helpers format/build messages cho TelegramListenCommand.
 *
 * Tách trait từ god-class TelegramListenCommand (P1.3 audit) — gom các hàm
 * thuần "build text/markup" không có IO (không gọi bot, không query DB).
 *
 * mainMenuMarkup() ref `self::BTN_*` constants — định nghĩa ở class chính.
 * Khi trait merge vào class, `self::` resolve về class đó.
 */
trait BuildsTelegramMessages
{
    /**
     * Rút gọn account_type cho hiển thị inline button (Telegram giới hạn label ngắn).
     */
    private function shortAccountType(string $type): string
    {
        $map = [
            'Tài khoản chính chủ' => '👤 chính chủ',
            'Tài khoản dùng chung' => '🔑 dùng chung',
            'Tài khoản add family' => '👨‍👩‍👧 add family',
            'Tài khoản cấp (dùng riêng)' => '🎁 cấp riêng',
        ];
        return $map[$type] ?? $type;
    }

    /**
     * Build reply_markup với inline keyboard "↩ Bước trước" + "❌ Huỷ đơn".
     * @param bool $includeBack Có nút back hay không (step 1 không có).
     */
    private function navMarkup(bool $includeBack = true): array
    {
        $row = [];
        if ($includeBack) {
            $row[] = ['text' => '↩ Bước trước', 'callback_data' => 'back'];
        }
        $row[] = ['text' => '❌ Huỷ đơn', 'callback_data' => 'cancel'];
        return ['reply_markup' => json_encode(['inline_keyboard' => [$row]])];
    }

    /**
     * Persistent reply keyboard hiện cố định ở chân màn hình. Dùng để show menu
     * chính sau /start, /help, hoặc khi finalize đơn xong.
     *
     * self::BTN_* ref constants trong class chính (TelegramListenCommand).
     */
    private function mainMenuMarkup(): array
    {
        $keyboard = [
            [['text' => self::BTN_NEW_ORDER], ['text' => self::BTN_MULTI_ORDER]],
            [['text' => self::BTN_PENDING], ['text' => self::BTN_STATS]],
            [['text' => self::BTN_EXPIRING], ['text' => self::BTN_QUICK_ORDER]],
            [['text' => self::BTN_HELP]],
        ];
        return ['reply_markup' => json_encode([
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'is_persistent' => true,
        ])];
    }

    private function normalizeVietnameseName(string $name): string
    {
        $name = mb_strtolower($name, 'UTF-8');
        $words = explode(' ', $name);
        $formatted = array_map(
            fn($w) => mb_convert_case($w, MB_CASE_TITLE, 'UTF-8'),
            $words
        );
        return implode(' ', $formatted);
    }

    /**
     * Parse "1m" / "25d" / "1y" → ['days' => N, 'label' => '...', ...]
     *   d = day (ngày), m = month (tháng), y = year (năm)
     */
    private function parseDuration(string $token): ?array
    {
        if (preg_match('/^(\d+)y$/i', $token, $m)) {
            $v = (int) $m[1];
            return ['days' => $v * 365, 'label' => "{$v} năm", 'unit' => 'year', 'value' => $v];
        }
        if (preg_match('/^(\d+)m$/i', $token, $m)) {
            $v = (int) $m[1];
            return ['days' => $v * 30, 'label' => "{$v} tháng", 'unit' => 'month', 'value' => $v];
        }
        if (preg_match('/^(\d+)d$/i', $token, $m)) {
            $v = (int) $m[1];
            return ['days' => $v, 'label' => "{$v} ngày", 'unit' => 'day', 'value' => $v];
        }
        return null;
    }

    /**
     * Note compact lưu vào pending_orders.note để admin tham chiếu khi fill cuối ngày.
     */
    private function buildNote(array $data): string
    {
        $parts = [];
        if (!empty($data['customer_code']) && !empty($data['customer_name'])) {
            $parts[] = "KH:{$data['customer_code']} {$data['customer_name']}";
        }
        if (!empty($data['service_name'])) $parts[] = "DV:{$data['service_name']}";
        if (!empty($data['email'])) $parts[] = "TK:{$data['email']}";
        if (!empty($data['family_email'])) $parts[] = "GD:{$data['family_email']}";
        if (!empty($data['duration_label'])) $parts[] = "Hạn:{$data['duration_label']}";
        if (!empty($data['warranty_label'])) $parts[] = "BH:{$data['warranty_label']}";
        if (!empty($data['profit_amount'])) $parts[] = "LN:" . formatShortAmount((int) $data['profit_amount']);
        return implode(' | ', $parts);
    }

    /**
     * Build caption Telegram với chi tiết đơn hàng.
     */
    private function buildCaption(\App\Models\PendingOrder $order, array $data): string
    {
        $tail = "\n\n<b><i>📌 Thông tin đơn hàng đã được tích hợp vào QR, quý khách vui lòng quét mã chuyển khoản và chụp lại bill giúp em, em cám ơn ạ</i></b>";

        $lines = [
            "✅ <code>{$order->order_code}</code>",
            '',
            "👤 Khách hàng: <code>{$data['customer_code']}</code> — <b>{$data['customer_name']}</b>",
        ];
        $lines = array_merge($lines, $this->buildOrderDetailsLines($data));

        return implode("\n", $lines) . $tail;
    }

    /**
     * Trả về các dòng mô tả 1 đơn (KHÔNG có header order_code, KHÔNG có customer line,
     * KHÔNG có tail). Reuse cho cả buildCaption (đơn lẻ) lẫn finalizeMultiOrder (lô).
     *
     * Lines: 📌 Tên dịch vụ / 📌 Giá / 📌 Email / 📌 Mã nhóm (nếu có) / 📌 Thời hạn / 📌 Bảo hành (nếu có)
     */
    private function buildOrderDetailsLines(array $data): array
    {
        $today = now();
        $expiresAt = match ($data['duration_unit'] ?? 'day') {
            'year'  => $today->copy()->addYears((int) ($data['duration_value'] ?? 0)),
            'month' => $today->copy()->addMonths((int) ($data['duration_value'] ?? 0)),
            default => $today->copy()->addDays((int) ($data['duration_value'] ?? 0)),
        };

        $lines = [
            "📌 Tên dịch vụ: <b>{$data['service_name']}</b>",
            "📌 Giá dịch vụ: <b>" . formatShortAmount($data['amount']) . "</b>",
        ];
        // Email optional — chỉ render dòng nếu có (admin có thể skip ở bước 3/7)
        if (!empty($data['email'])) {
            $lines[] = "📌 Email tài khoản: <code>{$data['email']}</code>";
        } else {
            $lines[] = "📌 Email tài khoản: <i>(chưa có — sẽ fill sau qua web)</i>";
        }

        if (!empty($data['family_email'])) {
            // family_email giờ free-form (email/số/text) — escape HTML để tránh vỡ markup
            $safe = htmlspecialchars((string) $data['family_email'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $lines[] = "📌 Mã nhóm - gia đình: <code>{$safe}</code>";
        }

        $lines[] = sprintf(
            "📌 Thời hạn tài khoản: từ %s đến %s (%s)",
            $today->format('d/m/Y'),
            $expiresAt->format('d/m/Y'),
            $data['duration_label'] ?? ''
        );

        if (!empty($data['warranty_label'])) {
            $lines[] = "📌 Bảo hành: <b>{$data['warranty_label']}</b>";
        }

        return $lines;
    }

    private function helpMessage(): string
    {
        return "🤖 <b>Bot quản lý đơn — Hướng dẫn</b>\n\n"
            . "Bấm các nút bên dưới (cuối màn hình) để chọn nhanh:\n\n"
            . "📝 <b>Tạo đơn</b> — bot hỏi 7 bước:\n"
            . "  0️⃣ Số tiền — <code>100k</code>/<code>200k</code>/<code>1.5tr</code>\n"
            . "  1️⃣ Tên/mã KH — tên mới sẽ tự tạo KUN; gõ <code>KUN98473</code> để chọn KH cũ\n"
            . "  2️⃣ Thời hạn — <code>1m</code>=tháng, <code>25d</code>=ngày, <code>1y</code>=năm\n"
            . "  3️⃣ Email tài khoản\n"
            . "  4️⃣ Gói dịch vụ — chọn từ danh mục hoặc gõ keyword\n"
            . "  5️⃣ Mã nhóm/gia đình — bất kỳ (hoặc /skip)\n"
            . "  6️⃣ Bảo hành — <code>30d</code>/<code>1m</code>/<code>1y</code>/ /full / /skip\n"
            . "  7️⃣ Lợi nhuận — <code>50k</code>/<code>200k</code>/ /skip\n\n"
            . "📋 <b>Đơn pending</b> — list 10 đơn pending mới nhất (toàn bộ, không filter ngày) + tổng tiền.\n\n"
            . "📊 <b>Thống kê</b> — profit hôm nay + tháng, doanh thu, đơn paid/pending/cancelled, KH mới.\n\n"
            . "⏰ <b>Hết hạn</b> — đơn hết hạn HÔM NAY + đã quá hạn + sắp hết hạn (3 ngày tới).\n"
            . "<i>Bot tự nhắc lúc 9h sáng mỗi ngày.</i>\n\n"
            . "⚡ <b>Tạo đơn nhanh</b> — Flow ngắn 2 bước (số tiền + tên/mã KH) → tạo PendingOrder pending. Bot gửi QR ngay với mã đơn DH-XXX để khách CK. Admin fill chi tiết (gói/email/duration/...) sau qua <code>/admin/pending-orders</code>. Tiện cho lúc bận hoặc khách cần QR ngay.\n\n"
            . "🛒 <b>Đơn nhiều DV</b> — Khách mua nhiều DV cùng lúc + CK 1 lần. Bot hỏi tên KH 1 lần + thông tin từng đơn → sinh mã lô <code>GR-XXX</code> + 1 QR tổng. Pay2S match GR → mark cả lô paid + activate tất cả services tự động.\n\n"
            . "<b>Lệnh thủ công:</b>\n"
            . "/menu — hiện menu\n"
            . "/list — list 10 đơn pending mới nhất (toàn bộ)\n"
            . "/dh DH-XXX-XXX — xem chi tiết 1 đơn (hoặc gõ thẳng mã đơn)\n"
            . "/kh tên/mã/email/SĐT — search KH (vd <code>/kh nguyen</code>, <code>/kh KUN12345</code>)\n"
            . "/dt N — doanh thu + top DV trong N ngày qua (vd <code>/dt 7</code>, <code>/dt 30</code>)\n"
            . "/cancel DH-XXX-XXX — huỷ 1 đơn\n"
            . "/huy — huỷ conversation đang gõ\n"
            . "/lai — quay về bước trước\n\n"
            . "<i>📝 Để tạo đơn mới, bấm nút <b>📝 Tạo đơn</b>.</i>\n"
            . "<i>🔍 Gõ thẳng <code>DH-XXX-XXX</code> để xem chi tiết + menu hành động (refund / bảo hành).</i>";
    }

    /**
     * Phát hiện loại attachment trong message Telegram (photo/sticker/voice/...).
     * Return tên tiếng Việt để hiển thị, hoặc null nếu không phải attachment đã biết.
     */
    private function detectAttachmentType(array $message): ?string
    {
        $map = [
            'photo' => 'ảnh',
            'sticker' => 'sticker',
            'voice' => 'tin nhắn thoại',
            'audio' => 'audio',
            'video' => 'video',
            'video_note' => 'video tròn',
            'animation' => 'GIF/animation',
            'document' => 'tệp đính kèm',
            'contact' => 'danh thiếp',
            'location' => 'vị trí',
            'venue' => 'địa điểm',
            'poll' => 'poll',
            'dice' => 'dice/emoji động',
        ];
        foreach ($map as $key => $label) {
            if (isset($message[$key])) {
                return $label;
            }
        }
        return null;
    }

    /**
     * Map step machine → label tiếng Việt cho user.
     */
    private function stepLabel(string $step): string
    {
        return match ($step) {
            'awaiting_amount' => 'số tiền',
            'quick_order_amount' => 'số tiền (đơn nhanh)',
            'quick_order_customer' => 'tên/mã khách hàng (đơn nhanh)',
            'awaiting_multi_count' => 'số đơn (lô đa dịch vụ)',
            'customer_name' => 'tên hoặc mã khách hàng',
            'duration' => 'thời hạn',
            'email' => 'email tài khoản',
            'service_package' => 'chọn gói dịch vụ',
            'family_email' => 'mã nhóm/gia đình',
            'warranty' => 'bảo hành',
            'profit' => 'lợi nhuận',
            default => $step ?: 'không xác định',
        };
    }
}
