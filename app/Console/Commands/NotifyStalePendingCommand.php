<?php

namespace App\Console\Commands;

use App\Models\PendingOrder;
use App\Services\TelegramBotService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Alert đơn pending stale: tạo > 30 phút mà chưa CK.
 *
 * Chạy thủ công: php artisan bot:notify-stale-pending
 * Schedule: mỗi 30 phút trong routes/console.php.
 *
 * Logic:
 *   - Query PO chưa paid, chưa cancelled, created > 30m + < 24h (cap để không
 *     spam đơn cũ quá lâu).
 *   - Skip đơn đã từng nhắc (cache flag stale_reminded_<order_id> 24h TTL).
 *   - Gửi 1 noti gộp cho admin liệt kê các đơn stale + hint mark paid manual
 *     hoặc huỷ.
 */
class NotifyStalePendingCommand extends Command
{
    protected $signature = 'bot:notify-stale-pending';
    protected $description = 'Alert đơn pending stale >30 phút chưa CK — gửi Telegram cho admin';

    public function handle(): int
    {
        $bot = app(TelegramBotService::class);
        if (!$bot->isConfigured()) {
            $this->error('TELEGRAM_BOT_TOKEN chưa cấu hình. Skip.');
            return 1;
        }

        $adminIds = array_filter(array_map('trim', explode(',', (string) env('TELEGRAM_ADMIN_IDS', ''))));
        if (empty($adminIds)) {
            $this->warn('TELEGRAM_ADMIN_IDS rỗng. Skip.');
            return 0;
        }

        // Đơn pending stale: tạo > 30m + < 24h, chưa paid, chưa cancelled, chưa nhắc
        $orders = PendingOrder::query()
            ->where('status', 'pending')
            ->whereNull('paid_at')
            ->where('created_at', '<=', now()->subMinutes(30))
            ->where('created_at', '>=', now()->subHours(24))
            ->orderBy('created_at')
            ->get();

        // Lọc bỏ đơn đã từng nhắc (cache flag 24h)
        $orders = $orders->filter(function ($o) {
            return !Cache::has("stale_reminded_{$o->id}");
        });

        if ($orders->isEmpty()) {
            $this->info('Không có đơn stale cần nhắc.');
            return 0;
        }

        // Build message
        $lines = ["⚠️ <b>Đơn pending stale > 30 phút</b> (" . $orders->count() . " đơn):\n"];
        foreach ($orders as $o) {
            $minutesAgo = (int) $o->created_at->diffInMinutes(now());
            $lines[] = sprintf(
                "• <code>%s</code> · %s · ⏰ %d phút trước%s",
                $o->order_code,
                formatShortAmount((int) $o->amount),
                $minutesAgo,
                $o->note ? ' · ' . e(\Illuminate\Support\Str::limit($o->note, 30)) : ''
            );
        }
        $lines[] = "\n💡 <i>Khách có thể đã quên CK. Liên hệ nhắc nhẹ, hoặc bấm 💳 Đã trả nếu khách CK qua kênh khác.</i>";
        $lines[] = "<i>Bot tự động nhắc mỗi 30 phút. Mỗi đơn chỉ nhắc 1 lần/ngày.</i>";

        $msg = implode("\n", $lines);

        $sent = 0;
        foreach ($adminIds as $chatId) {
            try {
                $resp = $bot->sendMessage($chatId, $msg);
                if ($resp['ok'] ?? false) {
                    $sent++;
                    $this->info("✓ Sent to {$chatId}");
                }
            } catch (\Throwable $e) {
                Log::error('NotifyStalePending: send failed', ['chat_id' => $chatId, 'error' => $e->getMessage()]);
            }
        }

        // Mark đã nhắc (24h TTL — không nhắc lại đơn đó trong 24h)
        foreach ($orders as $o) {
            Cache::put("stale_reminded_{$o->id}", true, now()->addHours(24));
        }

        $this->info("Đã nhắc " . $orders->count() . " đơn cho {$sent}/" . count($adminIds) . " admin.");
        return 0;
    }
}
