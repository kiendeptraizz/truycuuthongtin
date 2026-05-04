<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Wrapper Telegram Bot API.
 *
 * Token + admin IDs lấy từ .env:
 *   TELEGRAM_BOT_TOKEN=...
 *   TELEGRAM_ADMIN_IDS=123,456
 */
class TelegramBotService
{
    private string $token;
    private array $adminIds;

    public function __construct()
    {
        $this->token = (string) env('TELEGRAM_BOT_TOKEN', '');
        $admins = (string) env('TELEGRAM_ADMIN_IDS', '');
        $this->adminIds = array_filter(array_map('trim', explode(',', $admins)));
    }

    public function isConfigured(): bool
    {
        return $this->token !== '';
    }

    public function isAdmin(string|int $userId): bool
    {
        return in_array((string) $userId, $this->adminIds, true);
    }

    public function call(string $method, array $params = []): array
    {
        if (!$this->isConfigured()) {
            throw new \RuntimeException('Telegram bot token chưa cấu hình.');
        }
        $url = "https://api.telegram.org/bot{$this->token}/{$method}";

        // Timeout config tối ưu cho UX:
        //   - getUpdates (long polling): cần timeout dài (~30s) để đợi update
        //   - sendMessage / sendPhoto / etc: cần timeout NGẮN để fail-fast khi
        //     external slow (VietQR API chậm làm Telegram fetch URL slow → trả 400).
        // Trước đây dùng timeout(60)+retry(2,500) cho TẤT CẢ → 1 sendPhoto fail
        // có thể block 60+ giây → Pay2S webhook timeout → Pay2S retry → spiral.
        $isLongPoll = $method === 'getUpdates';
        $timeout = $isLongPoll ? 35 : 10;
        $connectTimeout = 3;

        $client = Http::timeout($timeout)->connectTimeout($connectTimeout);
        // Retry chỉ cho long-poll (transient network issue khi đợi update lâu).
        // sendMessage/sendPhoto KHÔNG retry — chấp nhận fail nhanh để upstream
        // (sendPhotoSafe / webhook handler) có thể fallback gracefully.
        if ($isLongPoll) {
            $client = $client->retry(2, 500);
        }

        $resp = $client->post($url, $params);

        if (!$resp->successful()) {
            Log::warning("Telegram API failed: {$method}", ['response' => $resp->body()]);
            return ['ok' => false, 'description' => $resp->body()];
        }
        return $resp->json();
    }

    public function sendMessage(string|int $chatId, string $text, array $extras = []): array
    {
        return $this->call('sendMessage', array_merge([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ], $extras));
    }

    public function sendPhoto(string|int $chatId, string $photoUrl, string $caption = '', array $extras = []): array
    {
        return $this->call('sendPhoto', array_merge([
            'chat_id' => $chatId,
            'photo' => $photoUrl,
            'caption' => $caption,
            'parse_mode' => 'HTML',
        ], $extras));
    }

    public function getUpdates(int $offset = 0, int $timeout = 25): array
    {
        return $this->call('getUpdates', [
            'offset' => $offset,
            'timeout' => $timeout,
        ]);
    }

    public function deleteWebhook(): array
    {
        return $this->call('deleteWebhook', ['drop_pending_updates' => true]);
    }
}
