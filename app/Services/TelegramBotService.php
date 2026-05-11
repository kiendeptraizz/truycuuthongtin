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

    /**
     * Trả list admin chat IDs (đã parse từ TELEGRAM_ADMIN_IDS env).
     * DRY helper — thay 4 chỗ lặp `array_filter(array_map('trim', explode(',', env(...))))`.
     */
    public function adminIds(): array
    {
        return $this->adminIds;
    }

    public function call(string $method, array $params = []): array
    {
        if (!$this->isConfigured()) {
            throw new \RuntimeException('Telegram bot token chưa cấu hình.');
        }
        $url = "https://api.telegram.org/bot{$this->token}/{$method}";

        // Timeout config tối ưu cho UX:
        //   - getUpdates (long polling): 35s — cần đợi update lâu
        //   - sendPhoto: 30s — Telegram server cần fetch URL ảnh (VietQR/CDN)
        //     + upload CDN → đôi khi 10-20s là bình thường. Trước đây 10s gây
        //     fallback text+link liên tục dù VietQR + Telegram đều OK.
        //   - sendMessage / deleteMessage / etc: 10s — text-only, fast.
        // Lý do tách: trước đây timeout(60)+retry(2,500) cho TẤT CẢ → 1 fail
        // có thể block 60+ giây → Pay2S webhook timeout → spiral. Giờ tách
        // đúng theo nature của mỗi method.
        $isLongPoll = $method === 'getUpdates';
        $isMediaUpload = in_array($method, ['sendPhoto', 'sendDocument', 'sendVideo', 'sendAudio'], true);
        $timeout = match (true) {
            $isLongPoll => 35,
            $isMediaUpload => 30,
            default => 10,
        };
        // connectTimeout 10s thay vì 3s — VPS có thể có DNS resolve spike >3s
        // (gặp lỗi "Resolving timed out after 3000 milliseconds" cho
        // api.telegram.org). 10s đủ rộng cho spike mà vẫn fail-fast khi
        // service thực sự down (vs 60s gây block lâu).
        $connectTimeout = 10;

        $client = Http::timeout($timeout)->connectTimeout($connectTimeout);
        // Retry strategy theo method:
        // - Long-poll: 2 retry × 500ms (network transient)
        // - sendMessage/etc: 1 retry × 1s (DNS spike / connection reset)
        // - sendPhoto: KHÔNG retry (Telegram fetch URL có thể slow nhưng
        //   sendPhotoSafe đã có fallback text+link)
        if ($isLongPoll) {
            $client = $client->retry(2, 500);
        } elseif (!$isMediaUpload) {
            $client = $client->retry(1, 1000);
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
