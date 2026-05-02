<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\PendingOrderController;
use App\Models\PendingOrder;
use App\Services\TelegramBotService;
use App\Services\VietQrService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Long-polling Telegram bot listener.
 *
 * Cách dùng:
 *   php artisan telegram:listen
 *
 * Bot xử lý:
 *   - Tin nhắn dạng số (vd "100000", "100k") → tạo pending order, gửi lại QR
 *   - /start, /help — hướng dẫn
 *   - /list — 5 đơn pending hôm nay
 *   - /cancel DH-XXX-XXX — huỷ đơn
 *
 * Bảo mật: chỉ user có ID trong TELEGRAM_ADMIN_IDS mới được dùng.
 */
class TelegramListenCommand extends Command
{
    protected $signature = 'telegram:listen';
    protected $description = 'Long polling Telegram bot — nhận tin nhắn để tạo pending orders';

    private TelegramBotService $bot;
    private VietQrService $qr;

    public function handle(): int
    {
        $this->bot = app(TelegramBotService::class);
        $this->qr = app(VietQrService::class);

        if (!$this->bot->isConfigured()) {
            $this->error('❌ TELEGRAM_BOT_TOKEN chưa được cấu hình trong .env.');
            $this->line('   Lấy token từ @BotFather, đặt: TELEGRAM_BOT_TOKEN=<token>');
            return 1;
        }

        // Đảm bảo không có webhook đang gắn (xung đột với getUpdates)
        $this->bot->deleteWebhook();
        $this->info('🤖 Bot đang lắng nghe... (Ctrl+C để dừng)');

        $offset = (int) Cache::get('telegram_bot_offset', 0);

        while (true) {
            try {
                $resp = $this->bot->getUpdates($offset, 25);
                if (!($resp['ok'] ?? false)) {
                    $this->warn('getUpdates thất bại. Thử lại sau 5s...');
                    sleep(5);
                    continue;
                }
                foreach (($resp['result'] ?? []) as $update) {
                    $offset = max($offset, $update['update_id'] + 1);
                    Cache::put('telegram_bot_offset', $offset, now()->addDays(7));
                    $this->processUpdate($update);
                }
            } catch (\Throwable $e) {
                Log::error('Telegram bot loop exception', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                $this->warn('Exception: ' . $e->getMessage() . '. Tiếp tục sau 5s...');
                sleep(5);
            }
        }
    }

    private function processUpdate(array $update): void
    {
        $message = $update['message'] ?? null;
        if (!$message) return;

        $chatId = $message['chat']['id'];
        $userId = $message['from']['id'] ?? '';
        $text = trim((string) ($message['text'] ?? ''));

        // Whitelist
        if (!$this->bot->isAdmin($userId)) {
            $this->bot->sendMessage(
                $chatId,
                "❌ Bạn không có quyền dùng bot này.\n\nUser ID: <code>{$userId}</code>\n\nNếu bạn là admin, thêm ID này vào <code>TELEGRAM_ADMIN_IDS</code> trong .env."
            );
            $this->line("Chặn user lạ: $userId ($text)");
            return;
        }

        if ($text === '' ) return;

        $this->line("[$userId] $text");

        // Lệnh
        if (str_starts_with($text, '/')) {
            $this->handleCommand($chatId, $userId, $text);
            return;
        }

        // Mặc định: parse số tiền
        $this->handleCreateOrder($chatId, $userId, $text);
    }

    private function handleCommand(int|string $chatId, string $userId, string $text): void
    {
        $parts = preg_split('/\s+/', $text, 2);
        $cmd = strtolower($parts[0]);
        $arg = $parts[1] ?? '';

        switch ($cmd) {
            case '/start':
            case '/help':
                $this->bot->sendMessage($chatId, $this->helpMessage());
                break;

            case '/list':
                $this->sendListPending($chatId);
                break;

            case '/cancel':
                if (!$arg) {
                    $this->bot->sendMessage($chatId, "Cú pháp: <code>/cancel DH-260501-001</code>");
                    break;
                }
                $this->cancelOrder($chatId, $arg);
                break;

            default:
                $this->bot->sendMessage($chatId, "❓ Lệnh không nhận diện được. Gõ /help để xem hướng dẫn.");
        }
    }

    private function handleCreateOrder(int|string $chatId, string $userId, string $text): void
    {
        // Thử parse format đầy đủ: "100k 1t email@x.com tên_dv [full]\ngd_email@y.com"
        $parsed = $this->parseFullCommand($text);

        if ($parsed) {
            $amount = $parsed['amount'];
            // Lưu vào note để admin có thể tham chiếu khi fill cuối ngày
            $noteParts = ["DV:{$parsed['service_name']}", "TK:{$parsed['email']}"];
            if ($parsed['family_email']) $noteParts[] = "GD:{$parsed['family_email']}";
            $noteParts[] = "Hạn:{$parsed['duration_label']}";
            if ($parsed['has_full']) $noteParts[] = "BH:full";
            $note = implode(' | ', $noteParts);
        } else {
            // Fallback format cũ: "100k" hoặc "100k chatgpt cho A"
            $amount = 0;
            $note = null;
            if (preg_match('/^([\d.,]+\s*(?:k|nghìn|nghin|tr|triệu|trieu|m)?)\s*(.*)$/iu', $text, $m)) {
                $amount = parseShortAmount($m[1]);
                $note = trim($m[2] ?? '') ?: null;
            }
        }

        if ($amount <= 0) {
            $this->bot->sendMessage($chatId, "❌ Không nhận diện được số tiền.\n\n<b>Format đầy đủ:</b>\n<code>100k 1m email@gmail.com claude</code>\n<code>gd_familyemail@gmail.com</code> <i>(dòng 2, tuỳ chọn)</i>\n\n<b>Đơn vị thời hạn:</b>\n• <code>Xd</code> = X ngày (vd <code>25d</code>)\n• <code>Xm</code> = X tháng (vd <code>1m</code>)\n• <code>Xy</code> = X năm (vd <code>1y</code>)\n\nThêm chữ <code>full</code> bất kỳ đâu để đánh dấu bảo hành full.\n\n<b>Format ngắn:</b>\n• <code>100k</code>\n• <code>100k chatgpt cho Thu Hà</code>");
            return;
        }

        try {
            $order = PendingOrderController::createOrder([
                'amount' => $amount,
                'note' => $note,
                'created_via' => 'telegram',
                'telegram_chat_id' => (string) $chatId,
            ]);
        } catch (\Throwable $e) {
            $this->bot->sendMessage($chatId, "❌ Lỗi tạo đơn: " . $e->getMessage());
            return;
        }

        $caption = $this->buildCaption($order, $parsed, $parsed ? null : $note);

        $this->bot->sendPhoto($chatId, $order->qrCodeUrl(), $caption);
    }

    /**
     * Parse format đầy đủ:
     *   Dòng 1: <amount> <duration> <email_tk> <tên_dịch_vụ...> [full]
     *   Dòng 2 (tuỳ chọn): gd_<email_family>
     *
     * Trả về null nếu không đủ thông tin → caller fallback format cũ.
     */
    private function parseFullCommand(string $text): ?array
    {
        $lines = preg_split('/\R+/', trim($text)) ?: [];
        $line1 = $lines[0] ?? '';
        $line2 = $lines[1] ?? '';

        $tokens = preg_split('/\s+/', trim($line1)) ?: [];
        if (count($tokens) < 4) return null; // chưa đủ info — fallback

        // Token 1: amount
        $amount = parseShortAmount($tokens[0]);
        if ($amount <= 0) return null;

        // Token 2: duration
        $duration = $this->parseDuration($tokens[1]);
        if ($duration === null) return null;

        // Token 3: email tài khoản
        if (!filter_var($tokens[2], FILTER_VALIDATE_EMAIL)) return null;
        $accountEmail = $tokens[2];

        // Token 4+: tên dịch vụ + có thể chứa "full"
        $hasFull = false;
        $serviceTokens = [];
        foreach (array_slice($tokens, 3) as $t) {
            if (strcasecmp($t, 'full') === 0) {
                $hasFull = true;
            } else {
                $serviceTokens[] = $t;
            }
        }
        if (empty($serviceTokens)) return null; // phải có tên dịch vụ
        $serviceName = implode(' ', $serviceTokens);

        // Dòng 2: family email (gd_xxx@yyy.zz)
        $familyEmail = null;
        if (preg_match('/^gd_(.+)$/i', trim($line2), $m)) {
            $candidate = trim($m[1]);
            if (filter_var($candidate, FILTER_VALIDATE_EMAIL)) {
                $familyEmail = $candidate;
            }
        }

        return [
            'amount' => $amount,
            'duration_days' => $duration['days'],
            'duration_label' => $duration['label'],
            'duration_unit' => $duration['unit'], // 'day' | 'month' | 'year'
            'duration_value' => $duration['value'],
            'email' => $accountEmail,
            'service_name' => $serviceName,
            'has_full' => $hasFull,
            'family_email' => $familyEmail,
        ];
    }

    /**
     * Parse "1m" / "25d" / "1y" → ['days' => N, 'label' => '...', ...]
     *   m = month (tháng), d = day (ngày), y = year (năm)
     */
    private function parseDuration(string $token): ?array
    {
        // Năm: Xy
        if (preg_match('/^(\d+)y$/i', $token, $m)) {
            $v = (int) $m[1];
            return ['days' => $v * 365, 'label' => "{$v} năm", 'unit' => 'year', 'value' => $v];
        }
        // Tháng: Xm
        if (preg_match('/^(\d+)m$/i', $token, $m)) {
            $v = (int) $m[1];
            return ['days' => $v * 30, 'label' => "{$v} tháng", 'unit' => 'month', 'value' => $v];
        }
        // Ngày: Xd
        if (preg_match('/^(\d+)d$/i', $token, $m)) {
            $v = (int) $m[1];
            return ['days' => $v, 'label' => "{$v} ngày", 'unit' => 'day', 'value' => $v];
        }
        return null;
    }

    /**
     * Build caption — gọn nếu format cũ, đầy đủ nếu format mới.
     */
    private function buildCaption(\App\Models\PendingOrder $order, ?array $parsed, ?string $note): string
    {
        $tail = "\n\n<b><i>📌 Thông tin đơn hàng đã được tích hợp vào QR, quý khách vui lòng quét mã chuyển khoản và chụp lại bill giúp em, em cám ơn ạ</i></b>";

        if (!$parsed) {
            // Format cũ — chỉ mã đơn + ghi chú nếu có (bỏ 💰 và 🕐 vì QR đã hiển thị tiền)
            return "✅ <b>{$order->order_code}</b>"
                . ($note ? "\n📒 {$note}" : '')
                . $tail;
        }

        // Format mới — chi tiết đơn hàng
        // Tính ngày hết hạn theo lịch (chuẩn hơn cộng days)
        $today = now();
        $expiresAt = match ($parsed['duration_unit']) {
            'year'  => $today->copy()->addYears($parsed['duration_value']),
            'month' => $today->copy()->addMonths($parsed['duration_value']),
            default => $today->copy()->addDays($parsed['duration_value']),
        };

        $lines = [
            "✅ <b>{$order->order_code}</b>",
            '',
            "📌 Tên dịch vụ: <b>{$parsed['service_name']}</b>",
            "📌 Giá dịch vụ: <b>" . formatShortAmount($parsed['amount']) . "</b>",
            "📌 Email tài khoản: <code>{$parsed['email']}</code>",
        ];

        if ($parsed['family_email']) {
            $lines[] = "📌 Mã nhóm - gia đình: <code>{$parsed['family_email']}</code>";
        }

        $lines[] = sprintf(
            "📌 Thời hạn tài khoản: từ %s đến %s (%s)",
            $today->format('d/m/Y'),
            $expiresAt->format('d/m/Y'),
            $parsed['duration_label']
        );

        // Bảo hành: full nếu user gõ "full", không thì để trống cho user tự điền
        $lines[] = "📌 Bảo hành: " . ($parsed['has_full'] ? '<b>full thời hạn</b>' : '');

        return implode("\n", $lines) . $tail;
    }

    private function sendListPending(int|string $chatId): void
    {
        $orders = PendingOrder::where('status', 'pending')
            ->whereDate('created_at', today())
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        if ($orders->isEmpty()) {
            $this->bot->sendMessage($chatId, "📭 Hôm nay chưa có đơn pending nào.");
            return;
        }

        $lines = ["📋 <b>Đơn pending hôm nay (" . $orders->count() . "):</b>\n"];
        $total = 0;
        foreach ($orders as $o) {
            $lines[] = sprintf(
                "• <b>%s</b> · %s · %s%s",
                $o->order_code,
                formatShortAmount($o->amount),
                $o->created_at->format('H:i'),
                $o->note ? " · {$o->note}" : ''
            );
            $total += $o->amount;
        }
        $lines[] = "\n💵 Tổng: <b>" . formatShortAmount($total) . "</b> (" . number_format($total, 0, ',', '.') . "đ)";
        $this->bot->sendMessage($chatId, implode("\n", $lines));
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

    private function helpMessage(): string
    {
        return "🤖 <b>Bot tạo pending order</b>\n\n"
            . "<b>Tạo đơn nhanh</b> — gõ số tiền:\n"
            . "• <code>100k</code> — 100,000đ\n"
            . "• <code>200k</code> — 200,000đ\n"
            . "• <code>1.5tr</code> — 1,500,000đ\n"
            . "• <code>100k chatgpt cho Thu Hà</code> — kèm ghi chú\n\n"
            . "<b>Lệnh:</b>\n"
            . "/list — 10 đơn pending hôm nay\n"
            . "/cancel DH-XXX-XXX — huỷ đơn\n"
            . "/help — hướng dẫn này\n\n"
            . "Sau khi tạo, bạn forward QR cho khách qua Zalo. Cuối ngày vào web để fill thông tin chi tiết.";
    }
}
