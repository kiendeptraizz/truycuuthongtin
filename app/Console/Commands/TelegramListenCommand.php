<?php

namespace App\Console\Commands;

use App\Console\Commands\Concerns\BuildsTelegramMessages;
use App\Console\Commands\Concerns\HandlesPendingOrderActions;
use App\Console\Commands\Concerns\HandlesRefundFlow;
use App\Console\Commands\Concerns\HandlesStats;
use App\Console\Commands\Concerns\HandlesWarrantyFlow;
use App\Console\Commands\Concerns\ManagesTelegramState;
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
 *   - Bấm nút "📝 Tạo đơn" → bot hỏi 7 bước (amount → tên KH → ...) → tạo PendingOrder + QR
 *   - Bấm nút "🛒 Đơn nhiều DV" → flow lô đơn nhiều dịch vụ cùng lúc (mã lô GR-XXX)
 *   - Bấm nút "⚡ Tạo đơn nhanh" → chỉ hỏi số tiền + tên/mã KH → tạo PendingOrder pending (chờ fill)
 *   - /start, /help — hướng dẫn
 *   - /list — 10 đơn pending hôm nay
 *   - /dh DH-XXX — xem chi tiết 1 đơn + menu hành động (refund / bảo hành)
 *   - /cancel DH-XXX-XXX — huỷ đơn
 *
 * LƯU Ý: chỉ tạo đơn khi user BẤM NÚT "📝 Tạo đơn" — gõ số tiền root KHÔNG còn
 * tự start flow nữa (tránh /dh, text vô tình bị treat như input).
 *
 * Bảo mật: chỉ user có ID trong TELEGRAM_ADMIN_IDS mới được dùng.
 */
class TelegramListenCommand extends Command
{
    use ManagesTelegramState;
    use BuildsTelegramMessages;
    use HandlesStats;
    use HandlesRefundFlow;
    use HandlesWarrantyFlow;
    use HandlesPendingOrderActions;

    protected $signature = 'telegram:listen';
    protected $description = 'Long polling Telegram bot — nhận tin nhắn để tạo pending orders';

    private TelegramBotService $bot;
    private VietQrService $qr;

    // Label các nút trên persistent reply keyboard. Khi user bấm, Telegram gửi
    // lại đúng text này — bot match để route đến handler tương ứng.
    private const BTN_NEW_ORDER = '📝 Tạo đơn';
    private const BTN_MULTI_ORDER = '🛒 Đơn nhiều DV';
    private const BTN_PENDING = '📋 Đơn pending';
    private const BTN_STATS = '📊 Thống kê';
    private const BTN_EXPIRING = '⏰ Hết hạn';
    private const BTN_QUICK_ORDER = '⚡ Tạo đơn nhanh';
    private const BTN_HELP = '❓ Hướng dẫn';

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
                    $errCode = $resp['error_code'] ?? null;
                    $desc = $resp['description'] ?? '?';
                    // 409 Conflict — webhook gắn trở lại bởi instance khác hoặc test
                    // → re-arm: deleteWebhook + tiếp tục poll, không cần restart bot.
                    if ($errCode === 409) {
                        Log::warning('Telegram getUpdates 409 — re-arm via deleteWebhook', ['desc' => $desc]);
                        $this->warn('409 Conflict — gọi deleteWebhook để re-arm...');
                        try {
                            $this->bot->deleteWebhook();
                        } catch (\Throwable $e) {
                            $this->warn('deleteWebhook fail: ' . $e->getMessage());
                        }
                        sleep(2);
                        continue;
                    }
                    $this->warn("getUpdates thất bại (code={$errCode}): {$desc}. Thử lại sau 5s...");
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
        // Inline button click → callback_query
        if (isset($update['callback_query'])) {
            $this->handleCallbackQuery($update['callback_query']);
            return;
        }

        $message = $update['message'] ?? null;
        if (!$message) return;

        $chatId = $message['chat']['id'];
        $userId = $message['from']['id'] ?? '';
        $text = trim((string) ($message['text'] ?? ''));
        // Caption đi kèm photo/video/document — coi như tin nhắn text bình thường
        if ($text === '') {
            $text = trim((string) ($message['caption'] ?? ''));
        }

        // Whitelist
        if (!$this->bot->isAdmin($userId)) {
            $this->bot->sendMessage(
                $chatId,
                "❌ Bạn không có quyền dùng bot này.\n\nUser ID: <code>{$userId}</code>\n\nNếu bạn là admin, thêm ID này vào <code>TELEGRAM_ADMIN_IDS</code> trong .env."
            );
            $this->line("Chặn user lạ: $userId ($text)");
            return;
        }

        // Non-text update không có caption (sticker/voice/photo trống/...) — feedback friendly
        if ($text === '') {
            $attachmentType = $this->detectAttachmentType($message);
            if ($attachmentType !== null) {
                $state = $this->getState($chatId);
                if ($state) {
                    $stepLabel = $this->stepLabel($state['step'] ?? '');
                    $this->bot->sendMessage(
                        $chatId,
                        "📎 Bot nhận được <b>{$attachmentType}</b> nhưng chỉ xử lý tin nhắn text.\n\n"
                            . "Bạn đang ở bước: <b>{$stepLabel}</b>. Vui lòng gõ text "
                            . "(hoặc gõ <code>/huy</code> để huỷ phiên)."
                    );
                } else {
                    $this->bot->sendMessage(
                        $chatId,
                        "📎 Bot nhận được <b>{$attachmentType}</b> nhưng chỉ hiểu tin nhắn text.\n"
                            . "Bấm 📝 <b>Tạo đơn</b> hoặc gõ số tiền (vd <code>100k</code>) để bắt đầu."
                    );
                }
                $this->line("[$userId] (non-text: {$attachmentType})");
            }
            return;
        }
        $this->line("[$userId] $text");

        // Huỷ conversation đang dở (gõ /huy / huỷ / /cancel không có arg)
        $lc = strtolower($text);
        if (in_array($lc, ['/huy', '/huỷ', 'huy', 'huỷ'], true) || $lc === '/cancel') {
            if ($this->getState($chatId)) {
                $this->clearStateAndPurge($chatId);
                $this->bot->sendMessage($chatId, "❌ Đã huỷ. Gõ số tiền (vd <code>100k</code>) để bắt đầu đơn mới.");
                return;
            }
            // Không có conversation → cho /cancel rơi xuống lệnh cũ (cancel order code)
        }

        // Quay lại bước trước
        if (in_array($lc, ['/lai', '/lại', '/back', 'lai', 'lại', 'back', 'quay lại', 'quaylai'], true)) {
            $state = $this->getState($chatId);
            if ($state) {
                $this->goBackStep($chatId, $state);
            } else {
                $this->bot->sendMessage($chatId, "ℹ️ Không có phiên nào đang chạy. Gõ số tiền để bắt đầu.");
            }
            return;
        }

        $state = $this->getState($chatId);

        // User trong conversation gõ COMMAND (không phải /skip /full /huy /lai)
        // → auto-cancel state + chạy command. Tránh tình huống user gõ /dh DH-XXX
        // bị treat như tên KH, hay /list bị treat như input bước email...
        if ($state && str_starts_with($text, '/')) {
            $cmdLc = strtolower(preg_split('/\s+/', $text)[0]);
            $allowedInConversation = ['/skip', '/full', '/huy', '/huỷ', '/cancel', '/lai', '/lại', '/back'];
            if (!in_array($cmdLc, $allowedInConversation, true)) {
                $stepLabel = $this->stepLabel($state['step'] ?? '');
                $this->clearStateAndPurge($chatId);
                $this->bot->sendMessage(
                    $chatId,
                    "⚠️ Đã huỷ phiên đang dở (bước <i>{$stepLabel}</i>) vì bạn dùng lệnh khác.\n"
                        . "Để tạo đơn lại, bấm 📝 <b>Tạo đơn</b>."
                );
                $state = null; // đã clear, fall through xuống handleCommand
            }
        }

        // Đang trong conversation? → tiếp tục
        if ($state) {
            // Track user message_id để xoá sau khi finalize (chat sạch sau mỗi đơn)
            $userMsgId = $message['message_id'] ?? null;
            if ($userMsgId) {
                $this->trackMessageId($chatId, (int) $userMsgId);
                // Reload state để $state['data']['_track_msgs'] mới được nhận
                $state = $this->getState($chatId) ?? $state;
            }
            $this->handleConversationStep($chatId, $userId, $text, $state);
            return;
        }

        // User bấm nút menu chính (text trùng label) — route handler tương ứng
        if ($this->handleMenuButton($chatId, $userId, $text)) {
            return;
        }

        // Lệnh / (không có conversation)
        if (str_starts_with($text, '/')) {
            $this->handleCommand($chatId, $userId, $text);
            return;
        }

        // Auto-detect mã đơn DH-yymmdd-XXX (có/không dấu gạch) → show chi tiết +
        // menu hành động (refund/warranty). User không phải gõ /dh prefix.
        if (preg_match('/^DH[-\s]?\d{6}[-\s]?\d{3}$/i', $text)) {
            $this->sendOrderDetails($chatId, $text);
            return;
        }

        // Text root khác — KHÔNG auto-start tạo đơn nữa (trước đây gõ số tiền sẽ
        // tự bắt đầu flow → user vô tình gõ "260503001" liền bị tạo KH rác).
        // Giờ chỉ nudge user dùng nút menu cho rõ ý đồ.
        $this->bot->sendMessage(
            $chatId,
            "🤔 Bot không hiểu yêu cầu này.\n\n"
                . "💡 Bấm <b>📝 Tạo đơn</b> để bắt đầu, gõ thẳng <b>mã đơn</b> (vd <code>DH-260502-025</code>) để xem chi tiết, hoặc bấm <b>❓ Hướng dẫn</b>.",
            $this->mainMenuMarkup()
        );
    }

    /**
     * Route text từ persistent menu button → handler. Return true nếu match.
     */
    private function handleMenuButton(int|string $chatId, string $userId, string $text): bool
    {
        switch ($text) {
            case self::BTN_NEW_ORDER:
                $this->promptAmount($chatId);
                return true;
            case self::BTN_PENDING:
                $this->sendListPending($chatId);
                return true;
            case self::BTN_STATS:
                $this->sendStatsToday($chatId);
                return true;
            case self::BTN_EXPIRING:
                $this->sendExpirations($chatId);
                return true;
            case self::BTN_QUICK_ORDER:
                $this->promptQuickOrder($chatId);
                return true;
            case self::BTN_MULTI_ORDER:
                $this->promptMultiCount($chatId);
                return true;
            case self::BTN_HELP:
                $this->bot->sendMessage($chatId, $this->helpMessage(), $this->mainMenuMarkup());
                return true;
        }
        return false;
    }

    private function handleCommand(int|string $chatId, string $userId, string $text): void
    {
        $parts = preg_split('/\s+/', $text, 2);
        $cmd = strtolower($parts[0]);
        $arg = $parts[1] ?? '';

        switch ($cmd) {
            case '/start':
                // Deep-link: /start DH-XXX-XXX hoặc /start KUN12345 (Telegram chỉ
                // cho phép [a-zA-Z0-9_-] tối đa 64 ký tự trong start param). Format
                // link: https://t.me/<bot_username>?start=DH-260501-001 — admin
                // share link cho khách/nội bộ để mở trực tiếp đơn.
                if ($arg !== '') {
                    // Telegram chỉ cho phép [a-zA-Z0-9_-] trong start param. Nên
                    // share link đôi lúc encode "DH-260501-001" thành "DH_260501_001"
                    // (có client thay - thành _) → normalize cả 2 dạng.
                    $up = strtoupper(trim($arg));
                    // Mã đơn DH-yymmdd-XXX (có/không gạch hoặc gạch dưới)
                    if (preg_match('/^DH[-_]?(\d{6})[-_]?(\d{3})$/i', $up, $m)) {
                        $this->sendOrderDetails($chatId, "DH-{$m[1]}-{$m[2]}");
                        break;
                    }
                    // Mã KH KUN12345
                    if (preg_match('/^KUN\d{4,}$/i', $up)) {
                        $this->sendCustomerSearchResults($chatId, $up);
                        break;
                    }
                    // Param lạ → fall through gửi welcome + cảnh báo
                    $this->bot->sendMessage(
                        $chatId,
                        "⚠️ Tham số <code>" . e($arg) . "</code> không hợp lệ (cần DH-XXX-XXX hoặc KUN12345). Hiển thị menu chính:"
                    );
                }
                $this->bot->sendMessage(
                    $chatId,
                    "👋 <b>Chào admin!</b>\n\n"
                        . "Bot đã sẵn sàng. Bấm nút bên dưới để chọn chức năng:\n\n"
                        . "📝 <b>Tạo đơn</b> — bot sẽ hỏi 7 bước (tên KH, gói, ...)\n"
                        . "🛒 <b>Đơn nhiều DV</b> — Khách mua nhiều DV cùng lúc, CK 1 lần (mã lô GR-XXX)\n"
                        . "📋 <b>Đơn pending</b> — list 10 đơn chưa thanh toán mới nhất\n"
                        . "📊 <b>Thống kê</b> — profit + số đơn hôm nay/tháng\n"
                        . "⏰ <b>Hết hạn</b> — đơn hết hạn hôm nay/tuần này\n"
                        . "⚡ <b>Tạo đơn nhanh</b> — chỉ hỏi số tiền + KH (gói/email/... fill sau qua web)\n"
                        . "❓ <b>Hướng dẫn</b> — chi tiết các tính năng",
                    $this->mainMenuMarkup()
                );
                break;

            case '/help':
                $this->bot->sendMessage($chatId, $this->helpMessage(), $this->mainMenuMarkup());
                break;

            case '/menu':
                $this->bot->sendMessage($chatId, "📱 Menu chính:", $this->mainMenuMarkup());
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

            case '/skip':
            case '/full':
                $this->bot->sendMessage(
                    $chatId,
                    "ℹ️ Lệnh <code>{$cmd}</code> chỉ dùng được khi đang trong các bước tạo đơn (5/6/7).\n"
                        . "Bấm 📝 Tạo đơn để bắt đầu.",
                    $this->mainMenuMarkup()
                );
                break;

            case '/dh':
                if (!$arg) {
                    $this->bot->sendMessage($chatId, "Cú pháp: <code>/dh DH-260501-001</code>");
                    break;
                }
                $this->sendOrderDetails($chatId, $arg);
                break;

            case '/kh':
                if (!$arg) {
                    $this->bot->sendMessage($chatId, "Cú pháp: <code>/kh tên/mã/email/SĐT</code>\nVd: <code>/kh nguyen van a</code>, <code>/kh KUN12345</code>");
                    break;
                }
                $this->sendCustomerSearchResults($chatId, $arg);
                break;

            case '/dt':
                // /dt N — doanh thu + profit + top DV trong N ngày qua
                // Default N=7. Cap [1, 90] để tránh query nặng.
                $days = $arg !== '' ? (int) preg_replace('/\D/', '', $arg) : 7;
                if ($days < 1) $days = 7;
                if ($days > 90) $days = 90;
                $this->sendStatsRange($chatId, $days);
                break;

            default:
                $this->bot->sendMessage($chatId, "❓ Lệnh không nhận diện được. Gõ /help để xem hướng dẫn.");
        }
    }

    /**
     * Trả chi tiết 1 đơn theo mã (cho lệnh /dh DH-XXX-XXX).
     * Tolerant với format có/không dấu gạch.
     */
    private function sendOrderDetails(int|string $chatId, string $rawCode): void
    {
        $code = strtoupper(trim($rawCode));
        // Match cả "DH260501001" lẫn "DH-260501-001"
        if (preg_match('/^DH[-\s]?(\d{6})[-\s]?(\d{3})$/i', $code, $m)) {
            $code = "DH-{$m[1]}-{$m[2]}";
        }

        $order = PendingOrder::with(['customer', 'servicePackage.category'])
            ->where('order_code', $code)
            ->first();

        // Fallback: tìm CS theo order_code (đơn web nhanh không qua PO)
        // withTrashed() để CS đã bị soft-delete vẫn lookup được — show view có
        // badge "đã trashed" thay vì user nhận thông báo "không tìm thấy".
        if (!$order) {
            $cs = \App\Models\CustomerService::withTrashed()
                ->with(['customer', 'servicePackage.category'])
                ->where('order_code', $code)
                ->first();
            if ($cs) {
                $this->sendCustomerServiceDetails($chatId, $cs);
                return;
            }
            $this->bot->sendMessage($chatId, "❌ Không tìm thấy đơn <code>{$code}</code>");
            return;
        }

        // ƯU TIÊN: nếu PO đã có CS link (đơn đã activate thành dịch vụ) → show CS view
        // để có menu refund + warranty. View PO chỉ phù hợp cho đơn pending.
        // withTrashed() — CS soft-deleted vẫn render CS view có badge cảnh báo.
        if ($order->customer_service_id) {
            $cs = \App\Models\CustomerService::withTrashed()
                ->with(['customer', 'servicePackage.category'])
                ->find($order->customer_service_id);
            if ($cs) {
                $this->sendCustomerServiceDetails($chatId, $cs);
                return;
            }
            // CS hard-deleted (orphan) → fall through render PO view
        }

        $statusEmoji = match ($order->status) {
            'pending' => '⏳',
            'completed' => '✅',
            'cancelled' => '❌',
            default => '❓',
        };
        $statusLabel = match ($order->status) {
            'pending' => 'Chờ thanh toán',
            'completed' => 'Đã hoàn tất',
            'cancelled' => 'Đã huỷ',
            default => $order->status,
        };

        $lines = [
            "📋 <code>{$order->order_code}</code>",
            "{$statusEmoji} Trạng thái: <b>{$statusLabel}</b>",
            "💵 Số tiền: <b>" . formatShortAmount((int) $order->amount) . "</b>",
            "🕐 Tạo lúc: " . $order->created_at->format('H:i d/m/Y'),
        ];

        if ($order->customer) {
            $lines[] = "👤 Khách: <code>{$order->customer->customer_code}</code> — <b>" . e($order->customer->name) . "</b>";
        }
        if ($order->servicePackage) {
            $lines[] = "📦 Gói: <b>" . e($order->servicePackage->name) . "</b>";
        }
        if ($order->account_email) {
            $lines[] = "📧 Email TK: <code>" . e($order->account_email) . "</code>";
        }
        if ($order->paid_at) {
            $lines[] = "✅ Đã thanh toán: " . $order->paid_at->format('H:i d/m/Y') . " (" . formatShortAmount((int) $order->paid_amount) . ")";
        }
        if ($order->customer_service_id) {
            $lines[] = "🔗 Dịch vụ KH: #{$order->customer_service_id}";
        }
        if ($order->note) {
            $lines[] = "📝 " . e($order->note);
        }

        // Inline buttons: tuỳ status mà show button khác nhau
        $buttons = [];
        if ($order->status === 'pending') {
            $buttons[] = ['text' => '📷 Xem QR', 'callback_data' => "po_qr_{$order->id}"];
            if (!$order->paid_at) {
                $buttons[] = ['text' => '💳 Đã trả', 'callback_data' => "po_paid_{$order->id}"];
            }
            $buttons[] = ['text' => '❌ Huỷ', 'callback_data' => "po_huy_{$order->id}"];
        }

        $extras = [];
        if (!empty($buttons)) {
            $extras['reply_markup'] = json_encode(['inline_keyboard' => [$buttons]]);
        }

        $this->bot->sendMessage($chatId, implode("\n", $lines), $extras);
    }

    /**
     * Search KH theo query (mã KUN/CTV / tên / email / SĐT) — reuse logic của
     * CustomerController::searchApi.
     */
    private function sendCustomerSearchResults(int|string $chatId, string $query): void
    {
        $q = trim($query);
        if (mb_strlen($q) < 2) {
            $this->bot->sendMessage($chatId, "❌ Query quá ngắn (≥ 2 ký tự).");
            return;
        }

        $customers = \App\Models\Customer::query()
            ->where(function ($w) use ($q) {
                $w->where('customer_code', 'LIKE', "%{$q}%")
                    ->orWhere('name', 'LIKE', "%{$q}%")
                    ->orWhere('email', 'LIKE', "%{$q}%")
                    ->orWhere('phone', 'LIKE', "%{$q}%");
            })
            ->orderByRaw('CASE WHEN UPPER(customer_code) = UPPER(?) THEN 0 ELSE 1 END', [$q])
            ->orderByDesc('id')
            ->limit(10)
            ->get(['id', 'customer_code', 'name', 'email', 'phone']);

        if ($customers->isEmpty()) {
            $this->bot->sendMessage(
                $chatId,
                "🔍 Không tìm thấy KH nào khớp <code>" . e($q) . "</code>.\n"
                    . "Thử query khác: tên, mã KUN/CTV, email hoặc SĐT."
            );
            return;
        }

        $lines = ["🔍 <b>Tìm thấy " . $customers->count() . " KH</b> khớp <code>" . e($q) . "</code>:"];
        $buttons = [];
        foreach ($customers as $c) {
            $line = "• <code>{$c->customer_code}</code> — <b>" . e($c->name) . "</b>";
            if ($c->phone) {
                $line .= " · 📱" . e($c->phone);
            }
            if ($c->email) {
                $line .= " · 📧" . e($c->email);
            }
            $lines[] = $line;
            // Mỗi KH 1 button → click xem chi tiết + đơn gần nhất
            $buttons[] = [[
                'text' => "👤 {$c->customer_code} — " . mb_substr($c->name, 0, 30),
                'callback_data' => "cust_{$c->id}",
            ]];
        }
        if ($customers->count() === 10) {
            $lines[] = "\n<i>Hiển thị 10 KH gần nhất. Search cụ thể hơn nếu cần.</i>";
        }

        $this->bot->sendMessage(
            $chatId,
            implode("\n", $lines),
            ['reply_markup' => json_encode(['inline_keyboard' => $buttons])]
        );
    }

    /**
     * Click 1 KH từ kết quả /kh → hiện chi tiết KH + N đơn gần nhất.
     */
    private function handleCustomerDetailsCallback(int|string $chatId, int $customerId): void
    {
        $customer = \App\Models\Customer::with(['customerServices' => function ($q) {
            $q->orderByDesc('created_at')->limit(5)->with('servicePackage');
        }])->find($customerId);

        if (!$customer) {
            $this->bot->sendMessage($chatId, "❌ KH không tồn tại.");
            return;
        }

        $lines = [
            "👤 <b>" . e($customer->name) . "</b>",
            "🆔 Mã: <code>{$customer->customer_code}</code>",
        ];
        if ($customer->phone) $lines[] = "📱 SĐT: <code>" . e($customer->phone) . "</code>";
        if ($customer->email) $lines[] = "📧 Email: <code>" . e($customer->email) . "</code>";

        $services = $customer->customerServices;
        if ($services->isEmpty()) {
            $lines[] = "\n📭 KH chưa có dịch vụ nào.";
        } else {
            $lines[] = "\n📋 <b>" . $services->count() . " đơn gần nhất:</b>";
            foreach ($services as $cs) {
                $statusIcon = match (true) {
                    $cs->status === 'cancelled' && $cs->refunded_at => '↩️',
                    $cs->status === 'cancelled' => '❌',
                    $cs->status === 'active' => '✅',
                    $cs->status === 'expired' => '⏰',
                    $cs->status === 'pending' => '⏳',
                    default => '❓',
                };
                $orderCode = $cs->order_code ? "<code>{$cs->order_code}</code>" : "#{$cs->id}";
                $pkgName = $cs->servicePackage?->name ?? '?';
                $expiry = $cs->expires_at ? $cs->expires_at->format('d/m/Y') : 'không hạn';
                $lines[] = "{$statusIcon} {$orderCode} — " . e($pkgName) . " (HH: {$expiry})";
            }
            $lines[] = "\n<i>Gõ <code>/dh DH-...</code> để xem chi tiết 1 đơn.</i>";
        }

        $this->bot->sendMessage($chatId, implode("\n", $lines));
    }

    private function sendCustomerServiceDetails(int|string $chatId, \App\Models\CustomerService $cs): void
    {
        $statusEmoji = match ($cs->status) {
            'active' => '✅',
            'expired' => '⏰',
            'cancelled' => $cs->refunded_at ? '↩️' : '❌',
            'pending' => '⏳',
            default => '❓',
        };
        $statusLabel = match (true) {
            $cs->status === 'cancelled' && $cs->refunded_at !== null => 'Đã hoàn tiền',
            $cs->status === 'cancelled' => 'Đã huỷ',
            $cs->status === 'active' => 'Đang hoạt động',
            $cs->status === 'expired' => 'Đã hết hạn',
            $cs->status === 'pending' => 'Chờ thanh toán',
            default => $cs->status,
        };

        $lines = [
            "📋 <code>{$cs->order_code}</code> (dịch vụ KH #{$cs->id})",
            "{$statusEmoji} Trạng thái: <b>{$statusLabel}</b>",
        ];
        if ($cs->customer) {
            $lines[] = "👤 Khách: <code>{$cs->customer->customer_code}</code> — <b>" . e($cs->customer->name) . "</b>";
        }
        if ($cs->servicePackage) {
            $lines[] = "📦 Gói: <b>" . e($cs->servicePackage->name) . "</b>";
        }
        if ($cs->login_email) {
            $lines[] = "📧 Email TK: <code>" . e($cs->login_email) . "</code>";
        }
        if ($cs->order_amount) {
            $lines[] = "💵 Số tiền: <b>" . formatShortAmount((int) $cs->order_amount) . "</b>";
        }
        // Profit (nếu admin đã nhập trong Pay2S webhook hoặc bot bước 7)
        $profit = $cs->relationLoaded('profit') ? $cs->profit : $cs->profit()->first();
        if ($profit && $profit->profit_amount) {
            $lines[] = "💎 Lợi nhuận: <b>" . formatShortAmount((int) $profit->profit_amount) . "</b>";
        }
        if ($cs->activated_at) {
            $lines[] = "🟢 Kích hoạt: " . $cs->activated_at->format('d/m/Y');
        }
        if ($cs->expires_at) {
            $lines[] = "🔴 Hết hạn: " . $cs->expires_at->format('d/m/Y');
        }
        if ($cs->warranty_days) {
            $lines[] = "🛡 Bảo hành: {$cs->warranty_days} ngày";
        }
        if ($cs->refunded_at) {
            $lines[] = "↩️ Đã hoàn: <b>" . formatShortAmount((int) $cs->refund_amount) . "</b> ({$cs->refunded_at->format('d/m/Y')})";
        }

        // Cảnh báo nếu CS đã chuyển thùng rác (soft-delete)
        $isTrashed = method_exists($cs, 'trashed') ? $cs->trashed() : ($cs->deleted_at !== null);
        if ($isTrashed) {
            $lines[] = "🗑 <b>Đã chuyển vào thùng rác</b> ({$cs->deleted_at->format('d/m/Y H:i')}) — khôi phục tại <code>/admin/customer-services/trash</code>";
        }

        // Action menu inline: chỉ hiện cho CS chưa cancelled VÀ chưa trashed
        $extras = [];
        if ($cs->status !== 'cancelled' && !$isTrashed) {
            $rows = [];
            // Hàng 1: Tính tiền hoàn (nếu có order_amount > 0)
            if ((int) $cs->order_amount > 0) {
                $rows[] = [
                    ['text' => '💰 Tính tiền hoàn', 'callback_data' => "cs_refund_{$cs->id}"],
                ];
            }
            // Hàng 2: Bảo hành
            $rows[] = [
                ['text' => '🛡 Bảo hành đơn hàng', 'callback_data' => "cs_warranty_{$cs->id}"],
            ];
            if (!empty($rows)) {
                $extras['reply_markup'] = json_encode(['inline_keyboard' => $rows]);
            }
        }

        $this->bot->sendMessage($chatId, implode("\n", $lines), $extras);
    }

    /**
     * Xử lý từng bước trong conversation.
     */
    private function handleConversationStep(int|string $chatId, string $userId, string $text, array $state): void
    {
        $step = $state['step'] ?? null;
        $data = $state['data'] ?? [];

        switch ($step) {
            case 'awaiting_amount':
                $amount = parseShortAmount($text);
                if ($amount <= 0) {
                    $this->sendAndTrack(
                        $chatId,
                        "❌ Số tiền không hợp lệ.\n"
                            . "Gõ vd: <code>100k</code>, <code>200k</code>, <code>1.5tr</code>, <code>500000</code>",
                        $this->navMarkup(false)
                    );
                    return;
                }
                // Giữ _multi nếu đang multi-mode, chỉ reset các field per-order
                $multi = $data['_multi'] ?? null;
                $newData = ['amount' => $amount];
                if ($multi) {
                    $newData['_multi'] = $multi;
                }

                // Đơn 2+: skip step customer_name, dùng customer chung từ đơn 1
                if ($multi && ($multi['index'] ?? 0) > 0 && !empty($multi['shared_customer_id'])) {
                    $newData['customer_id'] = $multi['shared_customer_id'];
                    $newData['customer_code'] = $multi['shared_customer_code'];
                    $newData['customer_name'] = $multi['shared_customer_name'];
                    $this->setState($chatId, ['step' => 'duration', 'data' => $newData]);
                    $this->promptDuration($chatId);
                    return;
                }

                $this->setState($chatId, ['step' => 'customer_name', 'data' => $newData]);
                $this->promptCustomerName($chatId, $newData);
                return;

            case 'quick_order_amount':
                $amount = parseShortAmount($text);
                if ($amount <= 0) {
                    $this->sendAndTrack(
                        $chatId,
                        "❌ Số tiền không hợp lệ.\n"
                            . "Gõ vd: <code>100k</code>, <code>200k</code>, <code>1.5tr</code>, <code>500000</code>",
                        $this->navMarkup(false)
                    );
                    return;
                }
                $data['amount'] = $amount;
                $this->setState($chatId, ['step' => 'quick_order_customer', 'data' => $data]);
                $this->sendAndTrack(
                    $chatId,
                    "⚡ <b>Tạo đơn nhanh</b> — Bước 2/2: Tên hoặc mã khách hàng?\n"
                        . "• Gõ <i>tên</i> (vd: <code>Nguyễn Văn A</code>) — KH mới sẽ tự tạo mã KUN\n"
                        . "• Hoặc gõ <i>mã KH</i> (vd: <code>KUN98473</code>) — chọn KH cũ trong DB",
                    $this->navMarkup(true)
                );
                return;

            case 'quick_order_customer':
                $input = trim($text);
                if (mb_strlen($input) < 2) {
                    $this->sendAndTrack($chatId, "❌ Quá ngắn. Gõ lại tên hoặc mã khách hàng:");
                    return;
                }

                $matchedByCode = false;
                if (preg_match('/^(KUN|CTV)\d+$/i', $input)) {
                    $code = strtoupper($input);
                    $customer = \App\Models\Customer::where('customer_code', $code)->first();
                    if (!$customer) {
                        $this->sendAndTrack(
                            $chatId,
                            "❌ Không tìm thấy KH với mã <code>{$code}</code>.\n\n"
                                . "Gõ <b>tên đầy đủ</b> để tạo KH mới hoặc tìm theo tên, hoặc /huy để huỷ."
                        );
                        return;
                    }
                    $matchedByCode = true;
                } else {
                    try {
                        $customer = $this->findOrCreateCustomer($input);
                    } catch (\Throwable $e) {
                        Log::error('Telegram quick order: findOrCreateCustomer failed', [
                            'name' => $input,
                            'error' => $e->getMessage(),
                        ]);
                        $this->sendAndTrack($chatId, "❌ Lỗi tạo/tìm khách hàng: " . $e->getMessage());
                        return;
                    }
                }

                $data['customer_id'] = $customer->id;
                $data['customer_code'] = $customer->customer_code;
                $data['customer_name'] = $customer->name;

                if ($matchedByCode) {
                    $headLine = "✅ Tìm thấy KH theo mã: <code>{$customer->customer_code}</code> — <b>{$customer->name}</b>";
                } elseif ($customer->wasRecentlyCreated) {
                    $headLine = "✅ Đã tạo KH mới: <code>{$customer->customer_code}</code> — <b>{$customer->name}</b>";
                } else {
                    $headLine = "✅ Tìm thấy KH cũ: <code>{$customer->customer_code}</code> — <b>{$customer->name}</b>";
                }

                $this->sendAndTrack($chatId, $headLine);
                $this->finalizeQuickOrder($chatId, $userId, $data);
                return;

            case 'warranty_email':
                $email = trim($text);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $this->sendAndTrack(
                        $chatId,
                        "❌ Email không hợp lệ. Gõ lại email TK mới hoặc bấm <b>⏭ Bỏ qua</b>.",
                        ['reply_markup' => json_encode([
                            'inline_keyboard' => [[
                                ['text' => '⏭ Bỏ qua (không đổi TK)', 'callback_data' => 'wr_skip_email'],
                            ]],
                        ])]
                    );
                    return;
                }
                $data = $state['data'] ?? [];
                $data['replacement_email'] = $email;
                $this->setState($chatId, ['step' => 'warranty_password', 'data' => $data]);
                $this->sendAndTrack(
                    $chatId,
                    "🔑 Nhập <b>mật khẩu TK mới</b> (gõ <code>-</code> nếu chưa có hoặc giữ pass cũ):"
                );
                return;

            case 'warranty_password':
                $pwd = trim($text);
                $data = $state['data'] ?? [];
                $data['replacement_password'] = ($pwd === '-' || $pwd === '') ? null : $pwd;
                $this->setState($chatId, ['step' => 'warranty_extend', 'data' => $data]);
                $this->promptWarrantyExtend($chatId);
                return;

            case 'warranty_extend':
                $days = (int) preg_replace('/\D/', '', $text);
                if ($days < 0 || $days > 3650) {
                    $this->sendAndTrack(
                        $chatId,
                        "❌ Số ngày phải từ 0 đến 3650. Gõ lại hoặc bấm <b>⏭ Bỏ qua</b>.",
                        ['reply_markup' => json_encode([
                            'inline_keyboard' => [[
                                ['text' => '⏭ Bỏ qua (không gia hạn)', 'callback_data' => 'wr_skip_extend'],
                            ]],
                        ])]
                    );
                    return;
                }
                $data = $state['data'] ?? [];
                $data['extended_days'] = $days > 0 ? $days : null;
                $this->setState($chatId, ['step' => 'warranty_note', 'data' => $data]);
                $this->promptWarrantyNote($chatId);
                return;

            case 'warranty_note':
                $note = trim($text);
                if (mb_strlen($note) < 3) {
                    $this->sendAndTrack($chatId, "❌ Ghi chú quá ngắn (≥ 3 ký tự). Gõ lại:");
                    return;
                }
                $data = $state['data'] ?? [];
                // Purge prompt/reply messages của warranty flow trước khi finalize gửi summary
                $this->purgeTrackedMessages($chatId, $data);
                $this->clearState($chatId);
                $this->finalizeWarranty($chatId, $data, $note);
                return;

            case 'awaiting_multi_count':
                $count = (int) preg_replace('/\D/', '', $text);
                if ($count < 2 || $count > 5) {
                    $this->sendAndTrack(
                        $chatId,
                        "❌ Số đơn phải từ 2 đến 5. Gõ lại số (vd <code>2</code> hoặc <code>3</code>):",
                        $this->navMarkup(false)
                    );
                    return;
                }
                // Khởi tạo state multi-mode (lưu trong data._multi để propagate qua mọi step).
                $newData = [
                    '_multi' => [
                        'count' => $count,
                        'index' => 0,
                        'drafts' => [], // chứa data của các đơn đã hoàn thành
                        'shared_customer_id' => null,
                        'shared_customer_code' => null,
                        'shared_customer_name' => null,
                    ],
                ];
                $this->setState($chatId, ['step' => 'awaiting_amount', 'data' => $newData]);
                $this->sendAndTrack(
                    $chatId,
                    "🛒 <b>Bắt đầu lô {$count} đơn</b>\n\n"
                        . "📦 <b>Đơn 1/{$count}:</b> Gõ số tiền\n"
                        . "<i>Vd: <code>100k</code>, <code>200k</code>, <code>1.5tr</code></i>",
                    $this->navMarkup(false)
                );
                return;

            case 'customer_name':
                $input = trim($text);
                if (mb_strlen($input) < 2) {
                    $this->sendAndTrack($chatId, "❌ Quá ngắn. Gõ lại tên hoặc mã khách hàng:");
                    return;
                }

                // Detect mã KH (KUN/CTV + digits) — tìm chính xác theo customer_code
                $matchedByCode = false;
                if (preg_match('/^(KUN|CTV)\d+$/i', $input)) {
                    $code = strtoupper($input);
                    $customer = \App\Models\Customer::where('customer_code', $code)->first();
                    if (!$customer) {
                        $this->sendAndTrack(
                            $chatId,
                            "❌ Không tìm thấy KH với mã <code>{$code}</code>.\n\n"
                                . "Gõ <b>tên đầy đủ</b> để tạo KH mới hoặc tìm theo tên, hoặc /huy để huỷ."
                        );
                        return;
                    }
                    $matchedByCode = true;
                } else {
                    // Treat as tên KH — find by normalized name hoặc create
                    try {
                        $customer = $this->findOrCreateCustomer($input);
                    } catch (\Throwable $e) {
                        Log::error('Telegram: findOrCreateCustomer failed', ['name' => $input, 'error' => $e->getMessage()]);
                        $this->sendAndTrack($chatId, "❌ Lỗi tạo/tìm khách hàng: " . $e->getMessage());
                        return;
                    }
                }

                $data['customer_id'] = $customer->id;
                $data['customer_code'] = $customer->customer_code;
                $data['customer_name'] = $customer->name;

                // Multi-mode: lưu customer làm shared cho cả lô (các đơn 2+ sẽ skip step này)
                if (!empty($data['_multi'])) {
                    $data['_multi']['shared_customer_id'] = $customer->id;
                    $data['_multi']['shared_customer_code'] = $customer->customer_code;
                    $data['_multi']['shared_customer_name'] = $customer->name;
                }

                if ($matchedByCode) {
                    $headLine = "✅ Tìm thấy KH theo mã: <code>{$customer->customer_code}</code> — <b>{$customer->name}</b>";
                } elseif ($customer->wasRecentlyCreated) {
                    $headLine = "✅ Đã tạo KH mới: <code>{$customer->customer_code}</code> — <b>{$customer->name}</b>";
                } else {
                    $headLine = "✅ Tìm thấy KH cũ: <code>{$customer->customer_code}</code> — <b>{$customer->name}</b>";
                }

                $this->setState($chatId, ['step' => 'duration', 'data' => $data]);
                $this->sendAndTrack($chatId, $headLine);
                $this->promptDuration($chatId);
                return;

            case 'duration':
                $dur = $this->parseDuration(trim($text));
                if (!$dur) {
                    $this->sendAndTrack(
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
                $this->promptEmail($chatId);
                return;

            case 'email':
                $emailInput = trim($text);
                $lcEmail = strtolower($emailInput);
                // Skip → email = null, chuyển bước tiếp theo
                if (in_array($lcEmail, ['/skip', 'skip', 'không', 'khong', 'no', '-', 'bo', 'bỏ'], true)) {
                    $data['email'] = null;
                    $this->setState($chatId, ['step' => 'service_package', 'data' => $data]);
                    $this->sendAndTrack($chatId, "⏭ Đã bỏ qua email — có thể bổ sung sau qua web.");
                    $this->sendCategoryPicker($chatId);
                    return;
                }
                if (!filter_var($emailInput, FILTER_VALIDATE_EMAIL)) {
                    $this->sendAndTrack($chatId, "❌ Email không hợp lệ. Gõ lại hoặc /skip để bỏ qua:");
                    return;
                }
                $data['email'] = $emailInput;
                $this->setState($chatId, ['step' => 'service_package', 'data' => $data]);
                $this->sendCategoryPicker($chatId);
                return;

            case 'service_package':
                $kw = trim($text);
                if (mb_strlen($kw) < 1) {
                    $this->sendAndTrack($chatId, "❌ Gõ keyword tìm gói dịch vụ:");
                    return;
                }

                $packages = \App\Models\ServicePackage::active()
                    ->where('name', 'LIKE', '%' . $kw . '%')
                    ->with('category')
                    ->orderBy('name')
                    ->limit(20)
                    ->get();

                if ($packages->isEmpty()) {
                    $this->sendAndTrack(
                        $chatId,
                        "❌ Không tìm thấy gói nào khớp <b>'{$kw}'</b>.\n"
                            . "<i>Gõ keyword khác (ngắn hơn, vd <code>claude</code> thay vì <code>claude pro</code>)</i>"
                    );
                    return;
                }

                if ($packages->count() === 1) {
                    $this->selectServicePackage($chatId, $packages->first(), $data);
                    return;
                }

                // Nhiều kết quả → inline keyboard, mỗi button 1 gói
                $buttons = [];
                foreach ($packages as $pkg) {
                    $cat = $pkg->category?->name ?? '?';
                    $type = $this->shortAccountType((string) $pkg->account_type);
                    $label = "{$pkg->name} · {$type} · {$cat}";
                    // Telegram giới hạn callback_data ≤ 64 byte → dùng pkg_<id>
                    $buttons[] = [['text' => $label, 'callback_data' => "pkg_{$pkg->id}"]];
                }
                // Nút điều hướng
                $buttons[] = [
                    ['text' => '📂 Danh mục', 'callback_data' => 'cats'],
                    ['text' => '↩ Bước trước', 'callback_data' => 'back'],
                    ['text' => '❌ Huỷ', 'callback_data' => 'cancel'],
                ];

                $this->sendAndTrack(
                    $chatId,
                    "🔍 Tìm thấy <b>" . $packages->count() . "</b> gói khớp <code>{$kw}</code>. Click để chọn:",
                    ['reply_markup' => json_encode(['inline_keyboard' => $buttons])]
                );
                // Giữ state ở 'service_package' để user gõ keyword lại nếu muốn
                return;

            case 'family_email':
                $input = trim($text);
                $lcInput = strtolower($input);
                if (in_array($lcInput, ['/skip', 'skip', 'không', 'khong', 'no', '-', 'bo', 'bỏ'], true)) {
                    $data['family_email'] = null;
                } elseif ($input === '') {
                    $this->sendAndTrack($chatId, "❌ Trống. Gõ mã/email/số hoặc /skip:");
                    return;
                } else {
                    // Cho phép bất cứ định dạng gì: email, số, text, code...
                    $data['family_email'] = $input;
                }
                $this->setState($chatId, ['step' => 'warranty', 'data' => $data]);
                $this->promptWarranty($chatId);
                return;

            case 'warranty':
                $input = trim($text);
                $lcInput = strtolower($input);

                if (in_array($lcInput, ['/skip', 'skip', 'không', 'khong', 'no', '-', 'bo', 'bỏ', '0'], true)) {
                    // Không bảo hành
                    $data['warranty_days'] = null;
                    $data['warranty_label'] = null;
                    $data['has_full'] = false;
                } elseif (in_array($lcInput, ['/full', 'full', 'có', 'co', 'yes', 'y', 'ok'], true)) {
                    // Full thời hạn = warranty_days = duration_days
                    $data['warranty_days'] = (int) ($data['duration_days'] ?? 0);
                    $data['warranty_label'] = 'full thời hạn';
                    $data['has_full'] = true;
                } else {
                    // Parse Xd / Xm / Xy
                    $w = $this->parseDuration($input);
                    if (!$w) {
                        $this->sendAndTrack(
                            $chatId,
                            "❌ Sai format bảo hành.\n"
                                . "Gõ vd: <code>30d</code> (30 ngày), <code>1m</code> (1 tháng), <code>1y</code> (1 năm), /full (full thời hạn) hoặc /skip:",
                            $this->navMarkup()
                        );
                        return;
                    }
                    $data['warranty_days'] = $w['days'];
                    $data['warranty_label'] = $w['label'];
                    $data['has_full'] = false;
                }

                $this->setState($chatId, ['step' => 'profit', 'data' => $data]);
                $this->promptProfit($chatId);
                return;

            case 'profit':
                $input = trim($text);
                $lcInput = strtolower($input);

                if (in_array($lcInput, ['/skip', 'skip', 'không', 'khong', 'no', '-', 'bo', 'bỏ'], true)) {
                    $data['profit_amount'] = null;
                } else {
                    $profit = parseShortAmount($input);
                    if ($profit < 0) {
                        $this->sendAndTrack(
                            $chatId,
                            "❌ Sai format. Gõ vd: <code>50k</code>, <code>200k</code>, <code>1.5tr</code>, hoặc /skip:",
                            $this->navMarkup()
                        );
                        return;
                    }
                    $data['profit_amount'] = $profit;
                }

                // finalizeOrder xử lý multi-mode internally + tự clearState khi xong
                $this->finalizeOrder($chatId, $userId, $data);
                return;

            default:
                // State lạ — clear và yêu cầu start lại
                $this->clearStateAndPurge($chatId);
                $this->bot->sendMessage($chatId, "⚠️ Phiên đã hết. Gõ số tiền để bắt đầu đơn mới.");
        }
    }

    /**
     * Tạo PendingOrder + CustomerService (status='pending') + gửi caption + QR ảnh
     * sau khi user trả lời đủ 7 bước.
     *
     * Hybrid flow: tạo CS pending NGAY khi finalize → admin/khách thấy đơn trên web
     * (không bị coi là đã giao dịch vụ vì status='pending'). Khi Pay2S báo paid,
     * webhook sẽ đổi status='active' + set activated_at/expires_at.
     *
     * **Multi-mode**: nếu data['_multi'] tồn tại → đây là 1 trong N đơn của lô.
     * - Nếu chưa phải đơn cuối: lưu draft, prompt cho đơn tiếp theo.
     * - Nếu là đơn cuối: tạo TẤT CẢ N PendingOrder cùng group_code, gửi 1 QR tổng.
     */
    private function finalizeOrder(int|string $chatId, string $userId, array $data): void
    {
        // ===== Multi-mode handling =====
        $multi = $data['_multi'] ?? null;
        if ($multi) {
            // Lưu draft đơn vừa hoàn thành (không kèm _multi metadata)
            $draftData = $data;
            unset($draftData['_multi']);
            $multi['drafts'][] = $draftData;
            $multi['index']++;

            // Còn đơn để tạo → reset state về 'awaiting_amount' với customer giữ nguyên
            if ($multi['index'] < $multi['count']) {
                $next = $multi['index'] + 1;
                // Giữ _track_msgs để xoá hết khi finalize lô (cuối cùng)
                $newData = [
                    '_multi' => $multi,
                    '_track_msgs' => $data['_track_msgs'] ?? [],
                ];
                $this->setState($chatId, ['step' => 'awaiting_amount', 'data' => $newData]);
                $this->sendAndTrack(
                    $chatId,
                    "✅ Đã lưu đơn {$multi['index']}/{$multi['count']}.\n\n"
                        . "📦 <b>Đơn {$next}/{$multi['count']}:</b> Gõ số tiền\n"
                        . "<i>Vd: <code>100k</code>, <code>200k</code>, <code>1.5tr</code></i>",
                    $this->navMarkup(false)
                );
                return;
            }

            // Đơn cuối → tạo lô. Purge tracked messages TRƯỚC khi gửi caption + QR.
            $trackedIds = $data['_track_msgs'] ?? [];
            $this->purgeTrackedMessages($chatId, ['_track_msgs' => $trackedIds]);
            $this->clearState($chatId);
            $this->finalizeMultiOrder($chatId, $userId, $multi['drafts']);
            return;
        }

        // ===== Single-order mode (flow cũ) =====
        try {
            $order = PendingOrderController::createOrder([
                'amount' => $data['amount'],
                'note' => $this->buildNote($data),
                'customer_id' => $data['customer_id'] ?? null,
                'service_package_id' => $data['service_package_id'] ?? null,
                'account_email' => $data['email'] ?? null,
                'family_code' => $data['family_email'] ?? null,
                'duration_days' => $data['duration_days'] ?? null,
                'warranty_days' => $data['warranty_days'] ?? null,
                'profit_amount' => $data['profit_amount'] ?? null,
                'created_via' => 'telegram',
                'telegram_chat_id' => (string) $chatId,
            ]);
        } catch (\Throwable $e) {
            Log::error('Telegram: finalizeOrder failed', ['error' => $e->getMessage(), 'data' => $data]);
            $this->bot->sendMessage($chatId, "❌ Lỗi tạo đơn: " . $e->getMessage());
            $this->clearStateAndPurge($chatId);
            return;
        }

        // Hybrid: tạo CustomerService pending NGAY (chưa active)
        $this->tryCreatePendingCustomerService($order, $data);

        // Purge prompts/replies TRƯỚC khi clearState + gửi caption.
        // ClearState để các message tiếp theo (caption + QR) không bị track.
        $this->purgeTrackedMessages($chatId, $data);
        $this->clearState($chatId);

        $caption = $this->buildCaption($order, $data);
        $this->sendPhotoSafe($chatId, $order->qrCodeUrl(), $caption);
    }

    /**
     * Tạo lô đơn (multi-order): N PendingOrder + N CustomerService pending share cùng
     * group_code. Gửi 1 QR tổng (amount = sum, addInfo = group_code) thay vì N QR rời.
     *
     * @param  int|string $chatId
     * @param  string $userId
     * @param  array<int, array> $drafts  Mỗi draft là full $data của 1 đơn (đủ 7 bước).
     */
    private function finalizeMultiOrder(int|string $chatId, string $userId, array $drafts): void
    {
        if (empty($drafts)) {
            $this->bot->sendMessage($chatId, "❌ Lô rỗng — không có đơn nào để tạo.");
            return;
        }

        try {
            $result = \Illuminate\Support\Facades\DB::transaction(function () use ($chatId, $drafts) {
                $groupCode = PendingOrder::generateGroupCode();
                $orders = [];
                foreach ($drafts as $draft) {
                    $order = PendingOrderController::createOrder([
                        'amount' => $draft['amount'],
                        'note' => $this->buildNote($draft),
                        'group_code' => $groupCode, // chia sẻ cho cả lô
                        'customer_id' => $draft['customer_id'] ?? null,
                        'service_package_id' => $draft['service_package_id'] ?? null,
                        'account_email' => $draft['email'] ?? null,
                        'family_code' => $draft['family_email'] ?? null,
                        'duration_days' => $draft['duration_days'] ?? null,
                        'warranty_days' => $draft['warranty_days'] ?? null,
                        'profit_amount' => $draft['profit_amount'] ?? null,
                        'created_via' => 'telegram',
                        'telegram_chat_id' => (string) $chatId,
                    ]);

                    // Tạo CS pending tương ứng
                    $this->tryCreatePendingCustomerService($order, $draft);

                    $orders[] = ['order' => $order, 'draft' => $draft];
                }
                return ['groupCode' => $groupCode, 'orders' => $orders];
            });
        } catch (\Throwable $e) {
            Log::error('Telegram: finalizeMultiOrder failed', [
                'error' => $e->getMessage(),
                'drafts_count' => count($drafts),
            ]);
            $this->bot->sendMessage($chatId, "❌ Lỗi tạo lô đơn: " . $e->getMessage());
            return;
        }

        // Build caption + QR tổng
        $totalAmount = (int) array_sum(array_column($drafts, 'amount'));
        $groupCode = $result['groupCode'];
        $qrUrl = $this->qr->buildQrUrl($totalAmount, $groupCode);

        // Header: lô + KH + tổng tiền
        $header = [
            "🛒 <b>LÔ ĐƠN — " . count($drafts) . " dịch vụ</b>",
            "🏷 Mã lô: <code>{$groupCode}</code>",
        ];
        if (!empty($drafts[0]['customer_code']) && !empty($drafts[0]['customer_name'])) {
            $header[] = "👤 Khách hàng: <code>{$drafts[0]['customer_code']}</code> — <b>" . e($drafts[0]['customer_name']) . "</b>";
        }
        $header[] = "💵 Tổng tiền: <b>" . formatShortAmount($totalAmount) . "</b>";

        // Block từng đơn — format giống đơn lẻ (✅ mã đơn + 📌 dịch vụ/giá/email/...)
        $blocks = collect($result['orders'])->map(function ($item) {
            $order = $item['order'];
            $draft = $item['draft'];
            $lines = ["✅ <code>{$order->order_code}</code>"];
            $lines = array_merge($lines, $this->buildOrderDetailsLines($draft));
            return implode("\n", $lines);
        })->implode("\n\n──────\n\n");

        $tail = "<b><i>📌 Thông tin đơn hàng đã được tích hợp vào QR, quý khách vui lòng quét mã chuyển khoản và chụp lại bill giúp em, em cám ơn ạ</i></b>";

        $caption = implode("\n", $header)
            . "\n\n──────\n\n"
            . $blocks
            . "\n\n──────\n\n"
            . $tail;

        $this->sendPhotoSafe($chatId, $qrUrl, $caption);
        $this->bot->sendMessage(
            $chatId,
            "✅ Đã tạo lô <code>{$groupCode}</code> gồm " . count($drafts) . " đơn. Đợi khách CK 1 lần là bot tự active cả lô.",
            $this->mainMenuMarkup()
        );
    }

    /**
     * Tạo CustomerService với status='pending' nếu đủ data structured.
     * Pay2S webhook sau này sẽ activate (đổi sang 'active' + set activated_at/expires_at).
     */
    private function tryCreatePendingCustomerService(\App\Models\PendingOrder $order, array $data): void
    {
        // Cần đủ: customer + service_package + email + duration_days
        if (
            empty($data['customer_id']) ||
            empty($data['service_package_id']) ||
            empty($data['email']) ||
            empty($data['duration_days'])
        ) {
            return;
        }

        try {
            $cs = \App\Models\CustomerService::create([
                'pending_order_id' => $order->id,
                'customer_id' => $data['customer_id'],
                'service_package_id' => $data['service_package_id'],
                'login_email' => $data['email'],
                'activated_at' => null, // chưa activate, chờ Pay2S paid
                'expires_at' => null,
                'status' => 'pending',
                'duration_days' => $data['duration_days'],
                'warranty_days' => $data['warranty_days'] ?? null,
                'order_amount' => $data['amount'] ?? null,
                'family_code' => $data['family_email'] ?? null,
                'price' => 0,
                'cost_price' => 0,
                'internal_notes' => "📋 Tạo từ bot Telegram đơn {$order->order_code} ("
                    . now()->format('d/m/Y H:i') . ") — đang chờ Pay2S báo thanh toán.",
            ]);

            $order->update(['customer_service_id' => $cs->id]);

            Log::info('Telegram bot: created pending CustomerService', [
                'order_code' => $order->order_code,
                'customer_service_id' => $cs->id,
                'customer_id' => $data['customer_id'],
            ]);
        } catch (\Throwable $e) {
            // Không throw — bot vẫn gửi QR cho user, admin có thể fill thủ công sau
            Log::error('Telegram bot: tryCreatePendingCustomerService failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Xử lý click button inline (chọn ServicePackage).
     */
    private function handleCallbackQuery(array $cb): void
    {
        $callbackId = (string) ($cb['id'] ?? '');
        $cbData = (string) ($cb['data'] ?? '');
        $chatId = $cb['message']['chat']['id'] ?? null;
        $userId = (string) ($cb['from']['id'] ?? '');

        // Trả ack ngay để Telegram bỏ trạng thái loading nút bấm
        if ($callbackId !== '') {
            try {
                $this->bot->call('answerCallbackQuery', ['callback_query_id' => $callbackId]);
            } catch (\Throwable $e) {
                // ignore
            }
        }

        if (!$chatId || !$this->bot->isAdmin($userId)) return;

        // Mọi callback liên quan tới chọn gói đều yêu cầu state đang ở step service_package
        $state = $this->getState($chatId);
        $atServicePackageStep = $state && ($state['step'] ?? '') === 'service_package';

        // Click "↩ Bước trước" — quay lại step trước
        if ($cbData === 'back') {
            if (!$state) {
                $this->bot->sendMessage($chatId, "⚠️ Phiên đã hết. Gõ số tiền để bắt đầu.");
                return;
            }
            $this->goBackStep($chatId, $state);
            return;
        }

        // Click "❌ Huỷ đơn" — clear state, kết thúc phiên
        if ($cbData === 'cancel') {
            if ($state) {
                $this->clearStateAndPurge($chatId);
            }
            $this->bot->sendMessage($chatId, "❌ Đã huỷ đơn. Gõ số tiền (vd <code>100k</code>) để bắt đầu đơn mới.");
            return;
        }

        // Click "↩ Quay lại danh mục"
        if ($cbData === 'cats') {
            if (!$atServicePackageStep) {
                $this->bot->sendMessage($chatId, "⚠️ Phiên đã hết. Gõ số tiền để bắt đầu đơn mới.");
                return;
            }
            $this->sendCategoryPicker($chatId);
            return;
        }

        // Click button category hoặc paginate trong category — format cat_<id>_p<page>
        if (preg_match('/^cat_(\d+)_p(\d+)$/', $cbData, $m)) {
            if (!$atServicePackageStep) {
                $this->bot->sendMessage($chatId, "⚠️ Phiên đã hết. Gõ số tiền để bắt đầu đơn mới.");
                return;
            }
            $this->sendPackagesInCategory($chatId, (int) $m[1], (int) $m[2]);
            return;
        }

        // Click chọn package
        if (preg_match('/^pkg_(\d+)$/', $cbData, $m)) {
            if (!$atServicePackageStep) {
                $this->bot->sendMessage($chatId, "⚠️ Phiên đã hết hoặc không còn ở bước chọn gói.");
                return;
            }
            $pkg = \App\Models\ServicePackage::with('category')->find((int) $m[1]);
            if (!$pkg) {
                $this->bot->sendMessage($chatId, "❌ Gói này không còn tồn tại. Quay lại chọn lại:");
                $this->sendCategoryPicker($chatId);
                return;
            }
            $this->selectServicePackage($chatId, $pkg, $state['data'] ?? []);
            return;
        }

        // Click trang giữa (📄 1/3) — không làm gì
        if ($cbData === 'noop') return;

        // Click "❌ Huỷ đơn" trong /list — huỷ đơn theo id
        if (preg_match('/^po_huy_(\d+)$/', $cbData, $m)) {
            $this->handleCancelOrderCallback($chatId, $userId, (int) $m[1]);
            return;
        }

        // Click "💳 Đã trả" trong /list — manual mark paid theo id
        if (preg_match('/^po_paid_(\d+)$/', $cbData, $m)) {
            $this->handleMarkPaidCallback($chatId, $userId, (int) $m[1]);
            return;
        }

        // Click "📷 Xem QR" trong /list — gửi lại ảnh QR
        if (preg_match('/^po_qr_(\d+)$/', $cbData, $m)) {
            $this->handleViewQrCallback($chatId, (int) $m[1]);
            return;
        }

        // Click "💰 Tính tiền hoàn" — gửi preview refund của CS
        if (preg_match('/^cs_refund_(\d+)$/', $cbData, $m)) {
            $this->handleRefundPreviewCallback($chatId, (int) $m[1]);
            return;
        }

        // Click "✅ Xác nhận hoàn" trong preview refund — thực hiện refund ngay
        if (preg_match('/^cs_refund_ok_(\d+)$/', $cbData, $m)) {
            $this->handleRefundConfirmCallback($chatId, (int) $m[1]);
            return;
        }

        // Click "❌ Huỷ" trong preview refund — chỉ acknowledge
        if (preg_match('/^cs_refund_no_(\d+)$/', $cbData, $m)) {
            $this->handleRefundCancelCallback($chatId, (int) $m[1]);
            return;
        }

        // Click "🛡 Bảo hành đơn hàng" — start warranty conversation
        if (preg_match('/^cs_warranty_(\d+)$/', $cbData, $m)) {
            $this->handleWarrantyStartCallback($chatId, $userId, (int) $m[1]);
            return;
        }

        // Click "Bỏ qua TK mới" trong warranty flow
        if ($cbData === 'wr_skip_email') {
            $this->handleWarrantySkipEmail($chatId);
            return;
        }
        if ($cbData === 'wr_skip_extend') {
            $this->handleWarrantySkipExtend($chatId);
            return;
        }

        // Click 1 KH từ kết quả /kh → xem chi tiết + N đơn gần nhất
        if (preg_match('/^cust_(\d+)$/', $cbData, $m)) {
            $this->handleCustomerDetailsCallback($chatId, (int) $m[1]);
            return;
        }

        // Click "⏭ Bỏ qua email" trong flow tạo đơn (Bước 3/7)
        if ($cbData === 'step_skip_email') {
            $state = $this->getState($chatId);
            if ($state && ($state['step'] ?? null) === 'email') {
                $data = $state['data'] ?? [];
                $data['email'] = null;
                $this->setState($chatId, ['step' => 'service_package', 'data' => $data]);
                $this->sendAndTrack($chatId, "⏭ Đã bỏ qua email — có thể bổ sung sau qua web.");
                $this->sendCategoryPicker($chatId);
            }
            return;
        }

        // Callback khác chưa hỗ trợ
        Log::info('Telegram callback_query unknown', ['data' => $cbData]);
    }

    /**
     * User bấm nút "❌ Huỷ đơn" trong list /list.
     */
    /**
     * Gửi inline keyboard với list categories có active packages.
     */
    private function sendCategoryPicker(int|string $chatId): void
    {
        $cats = \App\Models\ServiceCategory::query()
            ->withCount(['servicePackages as active_count' => fn($q) => $q->where('is_active', true)])
            ->having('active_count', '>', 0)
            ->orderBy('name')
            ->get();

        if ($cats->isEmpty()) {
            $this->bot->sendMessage(
                $chatId,
                "❌ Chưa có danh mục nào. Hãy gõ <b>keyword</b> để search trực tiếp:"
            );
            return;
        }

        $buttons = [];
        foreach ($cats as $cat) {
            $buttons[] = [[
                'text' => "📂 {$cat->name} ({$cat->active_count} gói)",
                'callback_data' => "cat_{$cat->id}_p1",
            ]];
        }
        // Nút điều hướng cuối: back + cancel
        $buttons[] = [
            ['text' => '↩ Bước trước', 'callback_data' => 'back'],
            ['text' => '❌ Huỷ đơn', 'callback_data' => 'cancel'],
        ];

        $this->sendAndTrack(
            $chatId,
            "📦 <b>Bước 4/7:</b> Chọn gói dịch vụ\n\n"
                . "<b>2 cách:</b>\n"
                . "• Click 📂 danh mục bên dưới\n"
                . "• Hoặc gõ <b>keyword</b> để search nhanh (vd <code>claude</code>, <code>chatgpt</code>)",
            ['reply_markup' => json_encode(['inline_keyboard' => $buttons])]
        );
    }

    /**
     * Gửi inline keyboard list packages trong 1 category, paginate 8/page.
     */
    private function sendPackagesInCategory(int|string $chatId, int $categoryId, int $page = 1): void
    {
        $perPage = 8;

        $cat = \App\Models\ServiceCategory::find($categoryId);
        if (!$cat) {
            $this->bot->sendMessage($chatId, "❌ Danh mục không tồn tại.");
            return;
        }

        $query = \App\Models\ServicePackage::active()->where('category_id', $categoryId);
        $total = (clone $query)->count();
        if ($total === 0) {
            $this->bot->sendMessage($chatId, "📭 Danh mục <b>{$cat->name}</b> chưa có gói nào active.");
            return;
        }

        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $totalPages));

        $packages = $query
            ->orderBy('name')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        $buttons = [];
        foreach ($packages as $pkg) {
            $type = $this->shortAccountType((string) $pkg->account_type);
            $buttons[] = [[
                'text' => "{$pkg->name} · {$type}",
                'callback_data' => "pkg_{$pkg->id}",
            ]];
        }

        // Hàng paginate (chỉ nếu có nhiều page)
        if ($totalPages > 1) {
            $navRow = [];
            if ($page > 1) {
                $navRow[] = ['text' => '« Trước', 'callback_data' => "cat_{$categoryId}_p" . ($page - 1)];
            }
            $navRow[] = ['text' => "📄 {$page}/{$totalPages}", 'callback_data' => 'noop'];
            if ($page < $totalPages) {
                $navRow[] = ['text' => 'Sau »', 'callback_data' => "cat_{$categoryId}_p" . ($page + 1)];
            }
            $buttons[] = $navRow;
        }

        // Hàng cuối: danh mục / bước trước / huỷ
        $buttons[] = [
            ['text' => '↩ Danh mục', 'callback_data' => 'cats'],
            ['text' => '⏮ Bước trước', 'callback_data' => 'back'],
            ['text' => '❌ Huỷ', 'callback_data' => 'cancel'],
        ];

        $this->sendAndTrack(
            $chatId,
            "📦 <b>{$cat->name}</b> — {$total} gói (trang {$page}/{$totalPages})\n"
                . "<i>Click 1 gói để chọn, hoặc gõ keyword để search:</i>",
            ['reply_markup' => json_encode(['inline_keyboard' => $buttons])]
        );
    }

    /**
     * Lưu ServicePackage đã chọn vào state, sang bước 5 (mã gia đình).
     */
    private function selectServicePackage(int|string $chatId, \App\Models\ServicePackage $pkg, array $data): void
    {
        $data['service_package_id'] = $pkg->id;
        $data['service_name'] = $pkg->name;
        $data['account_type'] = $pkg->account_type;
        $data['category_name'] = $pkg->category?->name;

        $this->setState($chatId, ['step' => 'family_email', 'data' => $data]);

        $this->sendAndTrack(
            $chatId,
            "✅ Đã chọn: <b>{$pkg->name}</b>\n"
                . "<i>Loại: {$pkg->account_type} · Danh mục: " . ($pkg->category?->name ?? '—') . "</i>"
        );
        $this->promptFamilyEmail($chatId);
    }

    /**
     * Thứ tự các bước conversation (dùng cho /lai để quay lại).
     */
    private const STEP_ORDER = ['customer_name', 'duration', 'email', 'service_package', 'family_email', 'warranty', 'profit'];

    /**
     * Quay lại bước trước. Clear field của step hiện tại + step trước trong $data,
     * gửi lại prompt cho step trước.
     */
    private function goBackStep(int|string $chatId, array $state): void
    {
        $current = $state['step'] ?? null;
        $data = $state['data'] ?? [];

        $idx = array_search($current, self::STEP_ORDER, true);
        if ($idx === false || $idx === 0) {
            // Bước đầu hoặc state lạ — clear hết
            $this->clearStateAndPurge($chatId);
            $this->bot->sendMessage($chatId, "↩ Đã huỷ phiên. Gõ số tiền (vd <code>100k</code>) để bắt đầu đơn mới.");
            return;
        }

        // Clear field tích luỹ ở step hiện tại + step trước (để prompt hiện sạch sẽ)
        $clearMap = [
            'customer_name' => ['customer_id', 'customer_code', 'customer_name'],
            'duration' => ['duration_days', 'duration_label', 'duration_unit', 'duration_value'],
            'email' => ['email'],
            'service_package' => ['service_package_id', 'service_name', 'account_type', 'category_name'],
            'family_email' => ['family_email'],
            'warranty' => ['warranty_days', 'warranty_label', 'has_full'],
            'profit' => ['profit_amount'],
        ];
        foreach (($clearMap[$current] ?? []) as $f) unset($data[$f]);

        $prevStep = self::STEP_ORDER[$idx - 1];
        foreach (($clearMap[$prevStep] ?? []) as $f) unset($data[$f]);

        $this->setState($chatId, ['step' => $prevStep, 'data' => $data]);
        $this->renderStepPrompt($chatId, $prevStep, $data);
    }

    /**
     * Gửi prompt cho 1 step (dùng khi back hoặc start). Inline với prompt khi advance trong handleConversationStep.
     */
    private function renderStepPrompt(int|string $chatId, string $step, array $data): void
    {
        match ($step) {
            'customer_name' => $this->promptCustomerName($chatId, $data),
            'duration' => $this->promptDuration($chatId),
            'email' => $this->promptEmail($chatId),
            'service_package' => $this->sendCategoryPicker($chatId),
            'family_email' => $this->promptFamilyEmail($chatId),
            'warranty' => $this->promptWarranty($chatId),
            'profit' => $this->promptProfit($chatId),
            default => null,
        };
    }

    /**
     * Bước đầu khi user bấm "📝 Tạo đơn" — hỏi số tiền, lưu state awaiting_amount
     * để `handleConversationStep` parse số tiền rồi chuyển sang step customer_name.
     */
    private function promptAmount(int|string $chatId): void
    {
        $this->setState($chatId, ['step' => 'awaiting_amount', 'data' => []]);
        $this->sendAndTrack(
            $chatId,
            "💰 <b>Bước 0/7:</b> Số tiền đơn hàng?\n"
                . "<i>Vd: <code>100k</code>, <code>200k</code>, <code>1.5tr</code>, <code>500000</code></i>",
            $this->navMarkup(false) // không có back vì là bước đầu
        );
    }

    /**
     * "⚡ Tạo đơn nhanh" — flow 2 bước (số tiền + tên/mã KH) → tạo PendingOrder
     * pending push lên web "đơn chờ fill". Admin fill chi tiết (gói/email/duration/
     * warranty/profit) sau qua /admin/pending-orders. Ngắn hơn flow đầy đủ 7 bước
     * nhưng vẫn track được đơn + Pay2S match được mã đơn.
     */
    private function promptQuickOrder(int|string $chatId): void
    {
        $this->setState($chatId, ['step' => 'quick_order_amount', 'data' => []]);
        $this->sendAndTrack(
            $chatId,
            "⚡ <b>Tạo đơn nhanh</b> — Bước 1/2: Số tiền đơn hàng?\n"
                . "<i>Vd: <code>100k</code>, <code>200k</code>, <code>1.5tr</code>, <code>500000</code></i>\n\n"
                . "<i>Đơn sẽ được tạo + push lên web chờ fill chi tiết (gói/email/...) sau.</i>\n\n"
                . "Gõ /huy để huỷ.",
            $this->navMarkup(false)
        );
    }

    /**
     * "🛒 Đơn nhiều DV" — bắt đầu flow tạo lô đơn (2-5 đơn cùng 1 KH cùng 1 lần CK).
     * State machine sẽ track multi_mode + multi_index + customer_id shared.
     */
    private function promptMultiCount(int|string $chatId): void
    {
        $this->setState($chatId, [
            'step' => 'awaiting_multi_count',
            'data' => [],
        ]);
        $this->sendAndTrack(
            $chatId,
            "🛒 <b>Đơn nhiều dịch vụ</b> — Khách mua nhiều DV cùng lúc, CK 1 lần.\n\n"
                . "Số đơn cần tạo? (2 đến 5 đơn)\n"
                . "<i>Vd: gõ <code>2</code> hoặc <code>3</code></i>\n\n"
                . "Sau đó bot sẽ hỏi tên KH (1 lần) + thông tin từng đơn (gói, email, ...). "
                . "Cuối cùng bot sinh 1 QR tổng + mã lô <code>GR-XXX</code>.\n\n"
                . "Gõ /huy để huỷ.",
            $this->navMarkup(false)
        );
    }

    /**
     * Finalize đơn nhanh — tạo PendingOrder với amount + customer_id (chưa có
     * gói/email/duration/warranty/profit — admin fill sau qua web).
     * Status = 'pending', sẽ xuất hiện trong /admin/pending-orders + bot /list.
     */
    private function finalizeQuickOrder(int|string $chatId, string $userId, array $data): void
    {
        $note = sprintf(
            "Đơn nhanh từ bot — chờ fill chi tiết. KH: %s (%s)",
            $data['customer_name'] ?? '?',
            $data['customer_code'] ?? '?'
        );

        try {
            $order = PendingOrderController::createOrder([
                'amount' => $data['amount'],
                'note' => $note,
                'customer_id' => $data['customer_id'] ?? null,
                // KHÔNG có service_package_id, account_email, family_code,
                // duration_days, warranty_days, profit_amount — admin fill sau.
                'created_via' => 'telegram',
                'telegram_chat_id' => (string) $chatId,
            ]);
        } catch (\Throwable $e) {
            Log::error('Telegram: finalizeQuickOrder failed', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            $this->sendAndTrack($chatId, "❌ Lỗi tạo đơn nhanh: " . $e->getMessage());
            $this->clearStateAndPurge($chatId);
            return;
        }

        // Purge messages bước 1/2 + 2/2 + headline trước khi gửi caption + QR
        $this->purgeTrackedMessages($chatId, $data);
        $this->clearState($chatId);

        $caption = "✅ <code>{$order->order_code}</code> <i>(đơn nhanh)</i>\n\n"
            . "👤 Khách hàng: <code>{$data['customer_code']}</code> — <b>" . e($data['customer_name']) . "</b>\n"
            . "💵 Giá đơn: <b>" . formatShortAmount((int) $data['amount']) . "</b>\n\n"
            . "<i>⏳ Đơn đang chờ fill chi tiết (gói / email / thời hạn / bảo hành / lợi nhuận). "
            . "Vào <code>/admin/pending-orders</code> để fill.</i>\n\n"
            . "<b><i>📌 Thông tin đơn hàng đã được tích hợp vào QR, quý khách vui lòng quét mã chuyển khoản và chụp lại bill giúp em, em cám ơn ạ</i></b>";

        $this->sendPhotoSafe($chatId, $order->qrCodeUrl(), $caption);
    }

    private function promptCustomerName(int|string $chatId, array $data): void
    {
        $amount = (int) ($data['amount'] ?? 0);
        $this->sendAndTrack(
            $chatId,
            "💰 Đơn <b>" . formatShortAmount($amount) . "</b>\n\n"
                . "👤 <b>Bước 1/7:</b> Tên hoặc mã khách hàng?\n"
                . "<i>• Gõ <b>tên</b> (vd: <code>Nguyễn Văn A</code>)</i>\n"
                . "<i>• Hoặc <b>mã KH</b> (vd: <code>KUN98473</code>)</i>",
            $this->navMarkup(false) // Bước 1 không có back
        );
    }

    private function promptDuration(int|string $chatId): void
    {
        $this->sendAndTrack(
            $chatId,
            "⏰ <b>Bước 2/7:</b> Thời hạn tài khoản?\n"
                . "<i>Vd: <code>1m</code> (1 tháng), <code>25d</code> (25 ngày), <code>1y</code> (1 năm)</i>",
            $this->navMarkup()
        );
    }

    private function promptEmail(int|string $chatId): void
    {
        // Inline keyboard có nút "Bỏ qua" — TH email không có (vd dịch vụ
        // không cần TK riêng / sẽ fill sau). Nút Bước trước/Huỷ giữ pattern.
        $extras = ['reply_markup' => json_encode([
            'inline_keyboard' => [
                [
                    ['text' => '⏭ Bỏ qua email', 'callback_data' => 'step_skip_email'],
                ],
                [
                    ['text' => '↩ Bước trước', 'callback_data' => 'back'],
                    ['text' => '❌ Huỷ đơn', 'callback_data' => 'cancel'],
                ],
            ],
        ])];
        $this->sendAndTrack(
            $chatId,
            "📧 <b>Bước 3/7:</b> Email tài khoản?\n"
                . "<i>Vd: <code>huatungthang@gmail.com</code></i>\n\n"
                . "Gõ <code>/skip</code> hoặc bấm <b>⏭ Bỏ qua</b> nếu chưa có email.",
            $extras
        );
    }

    private function promptFamilyEmail(int|string $chatId): void
    {
        $this->sendAndTrack(
            $chatId,
            "👥 <b>Bước 5/7:</b> Mã nhóm - gia đình?\n"
                . "<i>Có thể là email / số / mã / text bất kỳ.</i>\n"
                . "<i>Vd: <code>2</code>, <code>gd_abc@gmail.com</code>, <code>gia đình A</code></i>\n\n"
                . "Bấm /skip nếu không có",
            $this->navMarkup()
        );
    }

    private function promptWarranty(int|string $chatId): void
    {
        $this->sendAndTrack(
            $chatId,
            "🛡 <b>Bước 6/7:</b> Bảo hành?\n"
                . "<i>Vd: <code>30d</code> (30 ngày), <code>1m</code> (1 tháng), <code>1y</code> (1 năm)</i>\n\n"
                . "Bấm /full = full thời hạn\n"
                . "Bấm /skip = không bảo hành",
            $this->navMarkup()
        );
    }

    private function promptProfit(int|string $chatId): void
    {
        $this->sendAndTrack(
            $chatId,
            "💵 <b>Bước 7/7:</b> Lợi nhuận của đơn?\n"
                . "<i>Vd: <code>50k</code>, <code>200k</code>, <code>1.5tr</code></i>\n\n"
                . "Bấm /skip nếu chưa biết (có thể nhập sau qua web)",
            $this->navMarkup()
        );
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

        // Race fix: 2 user gõ cùng tên cùng lúc, cả 2 đều where().first() trả null,
        // cả 2 đều create → 2 KH trùng tên. Lock theo tên-normalized (md5 vì lock
        // key có giới hạn ký tự) để serialize concurrent calls. Block tối đa 8s
        // rồi throw — caller (handleConversationStep) đã có try-catch.
        $lockKey = 'customer_find_or_create:' . md5($normalized);
        return Cache::lock($lockKey, 10)->block(8, function () use ($normalized, $name) {
            $customer = \App\Models\Customer::where('name', $normalized)
                ->orderBy('id') // ưu tiên KH cũ nhất nếu trùng tên
                ->first();
            if ($customer) {
                return $customer;
            }
            // createSafe có retry UNIQUE customer_code (race khác — KUN code trùng).
            return \App\Models\Customer::createSafe(['name' => $name]);
        });
    }

}
