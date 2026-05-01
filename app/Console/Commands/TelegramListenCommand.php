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
        // Tách số tiền + ghi chú: "100k" hoặc "100k chatgpt cho A"
        $amount = 0;
        $note = null;

        if (preg_match('/^([\d.,]+\s*(?:k|nghìn|nghin|tr|triệu|trieu|m)?)\s*(.*)$/iu', $text, $m)) {
            $amount = parseShortAmount($m[1]);
            $note = trim($m[2] ?? '') ?: null;
        }

        if ($amount <= 0) {
            $this->bot->sendMessage($chatId, "❌ Không nhận diện được số tiền.\n\nVí dụ:\n• <code>100k</code>\n• <code>200k</code>\n• <code>1.5tr</code>\n• <code>100k chatgpt cho Thu Hà</code>");
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

        $caption = sprintf(
            "✅ <b>%s</b>\n💰 <b>%s</b> (%sđ)\n🕐 %s%s\n\n🏦 <b>%s</b>\n💳 <code>%s</code>\n👤 <b>%s</b>\n📝 ND CK: <code>%s</code>",
            $order->order_code,
            formatShortAmount($order->amount),
            number_format($order->amount, 0, ',', '.'),
            $order->created_at->format('H:i d/m/Y'),
            $note ? "\n📒 {$note}" : '',
            $this->qr->bankShortName(),
            $this->qr->accountNumber(),
            $this->qr->accountName(),
            $order->order_code
        );

        $this->bot->sendPhoto($chatId, $order->qrCodeUrl(), $caption);
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
