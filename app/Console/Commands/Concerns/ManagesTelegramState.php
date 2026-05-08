<?php

namespace App\Console\Commands\Concerns;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * State machine + message tracking cho TelegramListenCommand.
 *
 * State per-chat (Cache TTL 30 phút) lưu step hiện tại + data accumulated
 * qua các step. _track_msgs trong state['data'] chứa danh sách message_id
 * (cả bot + user) sẽ bị xoá khi finalize/cancel để chat sạch.
 *
 * Tách trait từ god-class TelegramListenCommand (P1.3 audit).
 */
trait ManagesTelegramState
{
    // ========================================================================
    // STATE STORAGE — dùng Cache (Laravel) để lưu state per chat
    // ========================================================================

    private function getState(int|string $chatId): ?array
    {
        return Cache::get("tg_state_{$chatId}");
    }

    private function setState(int|string $chatId, array $state): void
    {
        Cache::put("tg_state_{$chatId}", $state, now()->addMinutes(30));
    }

    private function clearState(int|string $chatId): void
    {
        Cache::forget("tg_state_{$chatId}");
    }

    /**
     * Xoá tất cả tracked messages của state hiện tại + clear state.
     * Dùng khi user huỷ flow (/huy / cancel callback / step lạ) hoặc finalize.
     */
    private function clearStateAndPurge(int|string $chatId): void
    {
        $state = $this->getState($chatId);
        if ($state) {
            $this->purgeTrackedMessages($chatId, $state['data'] ?? []);
        }
        $this->clearState($chatId);
    }

    // ========================================================================
    // MESSAGE TRACKING — track message_id để xoá sau khi finalize đơn
    // (giúp chat sạch sau mỗi lần tạo đơn xong)
    // ========================================================================

    /**
     * Gửi message và lưu message_id vào state (nếu đang trong conversation)
     * để xoá sau khi finalize. Dùng thay $this->bot->sendMessage cho các
     * prompt/status trong flow tạo đơn.
     */
    private function sendAndTrack(int|string $chatId, string $text, array $extras = []): array
    {
        $resp = $this->bot->sendMessage($chatId, $text, $extras);
        $msgId = $resp['result']['message_id'] ?? null;
        if ($msgId !== null) {
            $this->trackMessageId($chatId, (int) $msgId);
        }
        return $resp;
    }

    /**
     * Push message_id vào state['data']['_track_msgs']. Skip nếu không có
     * state (message ngoài conversation — không cần xoá).
     */
    private function trackMessageId(int|string $chatId, int $msgId): void
    {
        $state = $this->getState($chatId);
        if (!$state) return;
        $data = $state['data'] ?? [];
        $data['_track_msgs'] = $data['_track_msgs'] ?? [];
        $data['_track_msgs'][] = $msgId;
        $this->setState($chatId, [
            'step' => $state['step'] ?? null,
            'data' => $data,
        ]);
    }

    /**
     * Xoá tất cả message đã track (prompt bot + reply user) sau khi finalize.
     * Telegram cho phép bot delete message trong 48 giờ (kể cả của user trong
     * private chat). Lỗi delete (msg quá cũ/đã xoá) → ignore silently.
     */
    private function purgeTrackedMessages(int|string $chatId, array $data): void
    {
        $ids = $data['_track_msgs'] ?? [];
        if (empty($ids)) return;
        foreach ($ids as $id) {
            try {
                $this->bot->call('deleteMessage', [
                    'chat_id' => $chatId,
                    'message_id' => (int) $id,
                ]);
            } catch (\Throwable $e) {
                // ignore — msg cũ > 48h hoặc đã xoá
            }
        }
    }

    /**
     * Gửi ảnh qua Telegram. Nếu Telegram fail fetch URL ảnh (vd VietQR API tạm
     * down trả HTML/JSON error → Telegram báo "wrong type of the web page
     * content" 400) → fallback sendMessage với caption + link URL clickable
     * để user vẫn nhận được info đơn + có thể tap link mở QR thủ công.
     *
     * Tránh bug: 1 lần sendPhoto fail làm crash whole flow finalizeOrder →
     * không clearState → user stuck ở bước cuối + đơn vẫn được tạo trong DB
     * nhưng không có cách biết.
     */
    private function sendPhotoSafe(int|string $chatId, string $url, string $caption = '', array $extras = []): void
    {
        try {
            $this->bot->sendPhoto($chatId, $url, $caption, $extras);
        } catch (\Throwable $e) {
            Log::warning('Telegram: sendPhoto failed → fallback text+link', [
                'chat_id' => $chatId,
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            $linkLine = "\n\n📷 <a href=\"" . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . "\">📥 Mở ảnh QR (Telegram không tải được — tap link)</a>";
            try {
                $this->bot->sendMessage($chatId, $caption . $linkLine, $extras);
            } catch (\Throwable $e2) {
                Log::error('Telegram: fallback sendMessage cũng fail', [
                    'chat_id' => $chatId,
                    'error' => $e2->getMessage(),
                ]);
                // Last resort plain message
                $this->bot->sendMessage($chatId, "✅ Đã tạo đơn nhưng Telegram không gửi được QR. Vào /admin/pending-orders để xem chi tiết.");
            }
        }
    }
}
