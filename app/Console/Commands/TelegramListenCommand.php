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

        if ($text === '') return;
        $this->line("[$userId] $text");

        // Huỷ conversation đang dở (gõ /huy / huỷ / /cancel không có arg)
        $lc = strtolower($text);
        if (in_array($lc, ['/huy', '/huỷ', 'huy', 'huỷ'], true) || $lc === '/cancel') {
            if ($this->getState($chatId)) {
                $this->clearState($chatId);
                $this->bot->sendMessage($chatId, "❌ Đã huỷ. Gõ số tiền (vd <code>100k</code>) để bắt đầu đơn mới.");
                return;
            }
            // Không có conversation → cho /cancel rơi xuống lệnh cũ (cancel order code)
        }

        // Đang trong conversation? → tiếp tục
        $state = $this->getState($chatId);
        if ($state) {
            $this->handleConversationStep($chatId, $userId, $text, $state);
            return;
        }

        // Lệnh / (không có conversation)
        if (str_starts_with($text, '/')) {
            $this->handleCommand($chatId, $userId, $text);
            return;
        }

        // Bắt đầu conversation từ số tiền
        $this->startConversation($chatId, $userId, $text);
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

    /**
     * Bắt đầu conversation mới — user gõ số tiền → bot hỏi tên KH.
     */
    private function startConversation(int|string $chatId, string $userId, string $text): void
    {
        $amount = parseShortAmount($text);
        if ($amount <= 0) {
            $this->bot->sendMessage(
                $chatId,
                "❌ Vui lòng gõ số tiền để bắt đầu đơn mới.\n\n"
                    . "Ví dụ: <code>100k</code>, <code>200k</code>, <code>1.5tr</code>\n\n"
                    . "Gõ /help để xem hướng dẫn."
            );
            return;
        }

        $this->setState($chatId, [
            'step' => 'customer_name',
            'data' => ['amount' => $amount],
        ]);

        $this->bot->sendMessage(
            $chatId,
            "💰 Đơn <b>" . formatShortAmount($amount) . "</b>\n\n"
                . "👤 <b>Bước 1/6:</b> Tên khách hàng?\n"
                . "<i>(KH cũ sẽ tự tìm theo tên, KH mới sẽ tự tạo + sinh mã. Gõ /huy để huỷ)</i>"
        );
    }

    /**
     * Xử lý từng bước trong conversation.
     */
    private function handleConversationStep(int|string $chatId, string $userId, string $text, array $state): void
    {
        $step = $state['step'] ?? null;
        $data = $state['data'] ?? [];

        switch ($step) {
            case 'customer_name':
                $name = trim($text);
                if (mb_strlen($name) < 2) {
                    $this->bot->sendMessage($chatId, "❌ Tên quá ngắn. Gõ lại tên khách hàng:");
                    return;
                }
                try {
                    $customer = $this->findOrCreateCustomer($name);
                } catch (\Throwable $e) {
                    Log::error('Telegram: findOrCreateCustomer failed', ['name' => $name, 'error' => $e->getMessage()]);
                    $this->bot->sendMessage($chatId, "❌ Lỗi tạo/tìm khách hàng: " . $e->getMessage());
                    return;
                }
                $isNew = $customer->wasRecentlyCreated;
                $data['customer_id'] = $customer->id;
                $data['customer_code'] = $customer->customer_code;
                $data['customer_name'] = $customer->name;

                $headLine = $isNew
                    ? "✅ Đã tạo KH mới: <code>{$customer->customer_code}</code> — <b>{$customer->name}</b>"
                    : "✅ Tìm thấy KH cũ: <code>{$customer->customer_code}</code> — <b>{$customer->name}</b>";

                $this->setState($chatId, ['step' => 'duration', 'data' => $data]);
                $this->bot->sendMessage(
                    $chatId,
                    $headLine . "\n\n⏰ <b>Bước 2/6:</b> Thời hạn tài khoản?\n"
                        . "<i>Vd: <code>1m</code> (1 tháng), <code>25d</code> (25 ngày), <code>1y</code> (1 năm)</i>"
                );
                return;

            case 'duration':
                $dur = $this->parseDuration(trim($text));
                if (!$dur) {
                    $this->bot->sendMessage(
                        $chatId,
                        "❌ Sai format thời hạn.\n"
                            . "Gõ vd: <code>1m</code> (tháng), <code>25d</code> (ngày), <code>1y</code> (năm)."
                    );
                    return;
                }
                $data['duration_days'] = $dur['days'];
                $data['duration_label'] = $dur['label'];
                $data['duration_unit'] = $dur['unit'];
                $data['duration_value'] = $dur['value'];

                $this->setState($chatId, ['step' => 'email', 'data' => $data]);
                $this->bot->sendMessage(
                    $chatId,
                    "📧 <b>Bước 3/6:</b> Email tài khoản?\n<i>Vd: <code>huatungthang@gmail.com</code></i>"
                );
                return;

            case 'email':
                $email = trim($text);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $this->bot->sendMessage($chatId, "❌ Email không hợp lệ. Gõ lại:");
                    return;
                }
                $data['email'] = $email;
                $this->setState($chatId, ['step' => 'service_name', 'data' => $data]);
                $this->bot->sendMessage(
                    $chatId,
                    "📦 <b>Bước 4/6:</b> Tên dịch vụ?\n<i>Vd: <code>claude</code>, <code>chatgpt plus</code>, <code>gemini pro</code></i>"
                );
                return;

            case 'service_name':
                $sname = trim($text);
                if (mb_strlen($sname) < 1) {
                    $this->bot->sendMessage($chatId, "❌ Gõ lại tên dịch vụ:");
                    return;
                }
                $data['service_name'] = $sname;
                $this->setState($chatId, ['step' => 'family_email', 'data' => $data]);
                $this->bot->sendMessage(
                    $chatId,
                    "👥 <b>Bước 5/6:</b> Mã nhóm - gia đình (email)?\n<i>Gõ <code>skip</code> nếu không có</i>"
                );
                return;

            case 'family_email':
                $input = trim($text);
                $lcInput = strtolower($input);
                if (in_array($lcInput, ['skip', 'không', 'khong', 'no', '-', 'k', 'bo', 'bỏ'], true)) {
                    $data['family_email'] = null;
                } elseif (filter_var($input, FILTER_VALIDATE_EMAIL)) {
                    $data['family_email'] = $input;
                } else {
                    $this->bot->sendMessage($chatId, "❌ Email không hợp lệ. Gõ lại hoặc <code>skip</code>:");
                    return;
                }
                $this->setState($chatId, ['step' => 'warranty', 'data' => $data]);
                $this->bot->sendMessage(
                    $chatId,
                    "🛡 <b>Bước 6/6:</b> Bảo hành full thời hạn?\n<i>Gõ <code>full</code> nếu có, hoặc <code>skip</code> để trống</i>"
                );
                return;

            case 'warranty':
                $lcInput = strtolower(trim($text));
                $data['has_full'] = in_array($lcInput, ['full', 'có', 'co', 'yes', 'y', 'ok'], true);
                $this->finalizeOrder($chatId, $userId, $data);
                $this->clearState($chatId);
                return;

            default:
                // State lạ — clear và yêu cầu start lại
                $this->clearState($chatId);
                $this->bot->sendMessage($chatId, "⚠️ Phiên đã hết. Gõ số tiền để bắt đầu đơn mới.");
        }
    }

    /**
     * Tạo PendingOrder + gửi caption + QR ảnh sau khi user trả lời đủ 6 bước.
     */
    private function finalizeOrder(int|string $chatId, string $userId, array $data): void
    {
        try {
            $order = PendingOrderController::createOrder([
                'amount' => $data['amount'],
                'note' => $this->buildNote($data),
                'customer_id' => $data['customer_id'] ?? null,
                'created_via' => 'telegram',
                'telegram_chat_id' => (string) $chatId,
            ]);
        } catch (\Throwable $e) {
            Log::error('Telegram: finalizeOrder failed', ['error' => $e->getMessage(), 'data' => $data]);
            $this->bot->sendMessage($chatId, "❌ Lỗi tạo đơn: " . $e->getMessage());
            return;
        }

        $caption = $this->buildCaption($order, $data);
        $this->bot->sendPhoto($chatId, $order->qrCodeUrl(), $caption);
    }

    /**
     * Tìm KH theo tên (exact match sau khi normalize tiếng Việt) hoặc tạo mới.
     * Customer model auto-format name + auto-gen customer_code (KUN/CTV) khi creating.
     */
    private function findOrCreateCustomer(string $name): \App\Models\Customer
    {
        $name = trim($name);
        // Normalize giống mutator của Customer model: lowercase → title case từng từ
        $normalized = $this->normalizeVietnameseName($name);

        $customer = \App\Models\Customer::where('name', $normalized)
            ->orderBy('id') // ưu tiên KH cũ nhất nếu trùng tên
            ->first();

        if ($customer) {
            return $customer;
        }
        return \App\Models\Customer::create(['name' => $name]);
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
        if (!empty($data['has_full'])) $parts[] = "BH:full";
        return implode(' | ', $parts);
    }

    /**
     * Build caption Telegram với chi tiết đơn hàng.
     */
    private function buildCaption(\App\Models\PendingOrder $order, array $data): string
    {
        $tail = "\n\n<b><i>📌 Thông tin đơn hàng đã được tích hợp vào QR, quý khách vui lòng quét mã chuyển khoản và chụp lại bill giúp em, em cám ơn ạ</i></b>";

        $today = now();
        $expiresAt = match ($data['duration_unit'] ?? 'day') {
            'year'  => $today->copy()->addYears((int) ($data['duration_value'] ?? 0)),
            'month' => $today->copy()->addMonths((int) ($data['duration_value'] ?? 0)),
            default => $today->copy()->addDays((int) ($data['duration_value'] ?? 0)),
        };

        $lines = [
            "✅ <b>{$order->order_code}</b>",
            '',
            "👤 Khách hàng: <code>{$data['customer_code']}</code> — <b>{$data['customer_name']}</b>",
            "📌 Tên dịch vụ: <b>{$data['service_name']}</b>",
            "📌 Giá dịch vụ: <b>" . formatShortAmount($data['amount']) . "</b>",
            "📌 Email tài khoản: <code>{$data['email']}</code>",
        ];

        if (!empty($data['family_email'])) {
            $lines[] = "📌 Mã nhóm - gia đình: <code>{$data['family_email']}</code>";
        }

        $lines[] = sprintf(
            "📌 Thời hạn tài khoản: từ %s đến %s (%s)",
            $today->format('d/m/Y'),
            $expiresAt->format('d/m/Y'),
            $data['duration_label'] ?? ''
        );

        $lines[] = "📌 Bảo hành: " . (!empty($data['has_full']) ? '<b>full thời hạn</b>' : '');

        return implode("\n", $lines) . $tail;
    }

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
        return "🤖 <b>Bot tạo đơn pending</b>\n\n"
            . "<b>Cách tạo đơn:</b> Gõ số tiền để bắt đầu, bot sẽ hỏi 6 bước:\n"
            . "1️⃣ Tên khách hàng (mới sẽ tự tạo + sinh mã KUN, cũ sẽ tự tìm)\n"
            . "2️⃣ Thời hạn — <code>1m</code>=tháng, <code>25d</code>=ngày, <code>1y</code>=năm\n"
            . "3️⃣ Email tài khoản\n"
            . "4️⃣ Tên dịch vụ\n"
            . "5️⃣ Mã nhóm-gia đình (gõ <code>skip</code> nếu không có)\n"
            . "6️⃣ Bảo hành (gõ <code>full</code> hoặc <code>skip</code>)\n\n"
            . "<b>Ví dụ số tiền hợp lệ:</b>\n"
            . "<code>100k</code>, <code>200k</code>, <code>1.5tr</code>, <code>500000</code>\n\n"
            . "<b>Lệnh:</b>\n"
            . "/list — đơn pending hôm nay\n"
            . "/cancel DH-XXX-XXX — huỷ 1 đơn cụ thể\n"
            . "/huy (hoặc <code>huy</code>) — huỷ conversation đang gõ\n"
            . "/help — hướng dẫn này\n\n"
            . "Sau khi xong, bot trả về QR + chi tiết đơn để forward cho khách.";
    }
}
