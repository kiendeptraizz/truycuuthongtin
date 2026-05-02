<?php

namespace App\Console\Commands;

use App\Services\TelegramBotService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Gửi thông báo Telegram về các đơn hết hạn cho admin.
 *
 * Chạy thủ công: php artisan bot:notify-expirations
 * Schedule: dailyAt('09:00') trong routes/console.php.
 *
 * Lấy admin IDs từ env TELEGRAM_ADMIN_IDS (comma separated). Loop gửi cho từng ID.
 * Reuse buildExpirationsMessage() trong TelegramListenCommand để giữ format
 * thống nhất với lệnh thủ công "⏰ Hết hạn".
 */
class NotifyExpirationsCommand extends Command
{
    protected $signature = 'bot:notify-expirations';
    protected $description = 'Gửi noti Telegram đơn hết hạn hôm nay/quá hạn/sắp hết hạn cho admin';

    public function handle(): int
    {
        $bot = app(TelegramBotService::class);
        if (!$bot->isConfigured()) {
            $this->error('TELEGRAM_BOT_TOKEN chưa cấu hình. Skip.');
            return 1;
        }

        $adminIdsRaw = (string) env('TELEGRAM_ADMIN_IDS', '');
        $adminIds = array_filter(array_map('trim', explode(',', $adminIdsRaw)));

        if (empty($adminIds)) {
            $this->warn('TELEGRAM_ADMIN_IDS rỗng. Không có ai để noti.');
            return 0;
        }

        // Build message dùng chung với handler "⏰ Hết hạn" trong bot
        $listener = app(TelegramListenCommand::class);
        $msg = $listener->buildExpirationsMessage();

        $msg = "🔔 <b>Nhắc đơn hết hạn (9h sáng)</b>\n\n" . $msg
            . "\n\n<i>Tự động gửi mỗi sáng. Vào /admin/customer-services để xử lý.</i>";

        $sent = 0;
        foreach ($adminIds as $chatId) {
            try {
                $resp = $bot->sendMessage($chatId, $msg);
                if ($resp['ok'] ?? false) {
                    $sent++;
                    $this->info("✓ Sent to {$chatId}");
                } else {
                    $this->warn("✗ Failed for {$chatId}: " . ($resp['description'] ?? '?'));
                }
            } catch (\Throwable $e) {
                Log::error('NotifyExpirations: send failed', ['chat_id' => $chatId, 'error' => $e->getMessage()]);
                $this->error("✗ Exception for {$chatId}: " . $e->getMessage());
            }
        }

        $this->info("Hoàn tất. Đã gửi cho {$sent}/" . count($adminIds) . " admin.");
        return 0;
    }
}
