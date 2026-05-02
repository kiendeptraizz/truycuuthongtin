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

        $data = ['amount' => $amount];
        $this->setState($chatId, [
            'step' => 'customer_name',
            'data' => $data,
        ]);

        $this->promptCustomerName($chatId, $data);
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
                $input = trim($text);
                if (mb_strlen($input) < 2) {
                    $this->bot->sendMessage($chatId, "❌ Quá ngắn. Gõ lại tên hoặc mã khách hàng:");
                    return;
                }

                // Detect mã KH (KUN/CTV + digits) — tìm chính xác theo customer_code
                $matchedByCode = false;
                if (preg_match('/^(KUN|CTV)\d+$/i', $input)) {
                    $code = strtoupper($input);
                    $customer = \App\Models\Customer::where('customer_code', $code)->first();
                    if (!$customer) {
                        $this->bot->sendMessage(
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
                        $this->bot->sendMessage($chatId, "❌ Lỗi tạo/tìm khách hàng: " . $e->getMessage());
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

                $this->setState($chatId, ['step' => 'duration', 'data' => $data]);
                $this->bot->sendMessage($chatId, $headLine);
                $this->promptDuration($chatId);
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
                $this->promptEmail($chatId);
                return;

            case 'email':
                $email = trim($text);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $this->bot->sendMessage($chatId, "❌ Email không hợp lệ. Gõ lại:");
                    return;
                }
                $data['email'] = $email;
                $this->setState($chatId, ['step' => 'service_package', 'data' => $data]);
                $this->sendCategoryPicker($chatId);
                return;

            case 'service_package':
                $kw = trim($text);
                if (mb_strlen($kw) < 1) {
                    $this->bot->sendMessage($chatId, "❌ Gõ keyword tìm gói dịch vụ:");
                    return;
                }

                $packages = \App\Models\ServicePackage::active()
                    ->where('name', 'LIKE', '%' . $kw . '%')
                    ->with('category')
                    ->orderBy('name')
                    ->limit(20)
                    ->get();

                if ($packages->isEmpty()) {
                    $this->bot->sendMessage(
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

                $this->bot->sendMessage(
                    $chatId,
                    "🔍 Tìm thấy <b>" . $packages->count() . "</b> gói khớp <code>{$kw}</code>. Click để chọn:",
                    ['reply_markup' => json_encode(['inline_keyboard' => $buttons])]
                );
                // Giữ state ở 'service_package' để user gõ keyword lại nếu muốn
                return;

            case 'family_email':
                $input = trim($text);
                $lcInput = strtolower($input);
                if (in_array($lcInput, ['skip', 'không', 'khong', 'no', '-', 'bo', 'bỏ'], true)) {
                    $data['family_email'] = null;
                } elseif ($input === '') {
                    $this->bot->sendMessage($chatId, "❌ Trống. Gõ mã/email/số hoặc <code>skip</code>:");
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

                if (in_array($lcInput, ['skip', 'không', 'khong', 'no', '-', 'bo', 'bỏ', '0'], true)) {
                    // Không bảo hành
                    $data['warranty_days'] = null;
                    $data['warranty_label'] = null;
                    $data['has_full'] = false;
                } elseif (in_array($lcInput, ['full', 'có', 'co', 'yes', 'y', 'ok'], true)) {
                    // Full thời hạn = warranty_days = duration_days
                    $data['warranty_days'] = (int) ($data['duration_days'] ?? 0);
                    $data['warranty_label'] = 'full thời hạn';
                    $data['has_full'] = true;
                } else {
                    // Parse Xd / Xm / Xy
                    $w = $this->parseDuration($input);
                    if (!$w) {
                        $this->bot->sendMessage(
                            $chatId,
                            "❌ Sai format bảo hành.\n"
                                . "Gõ vd: <code>30d</code> (30 ngày), <code>1m</code> (1 tháng), <code>1y</code> (1 năm), <code>full</code> (full thời hạn) hoặc <code>skip</code>:",
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

                if (in_array($lcInput, ['skip', 'không', 'khong', 'no', '-', 'bo', 'bỏ'], true)) {
                    $data['profit_amount'] = null;
                } else {
                    $profit = parseShortAmount($input);
                    if ($profit < 0) {
                        $this->bot->sendMessage(
                            $chatId,
                            "❌ Sai format. Gõ vd: <code>50k</code>, <code>200k</code>, <code>1.5tr</code>, hoặc <code>skip</code>:",
                            $this->navMarkup()
                        );
                        return;
                    }
                    $data['profit_amount'] = $profit;
                }

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
     * Tạo PendingOrder + CustomerService (status='pending') + gửi caption + QR ảnh
     * sau khi user trả lời đủ 7 bước.
     *
     * Hybrid flow: tạo CS pending NGAY khi finalize → admin/khách thấy đơn trên web
     * (không bị coi là đã giao dịch vụ vì status='pending'). Khi Pay2S báo paid,
     * webhook sẽ đổi status='active' + set activated_at/expires_at.
     */
    private function finalizeOrder(int|string $chatId, string $userId, array $data): void
    {
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
            return;
        }

        // Hybrid: tạo CustomerService pending NGAY (chưa active)
        $this->tryCreatePendingCustomerService($order, $data);

        $caption = $this->buildCaption($order, $data);
        $this->bot->sendPhoto($chatId, $order->qrCodeUrl(), $caption);
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
                $this->clearState($chatId);
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

        // Callback khác chưa hỗ trợ
        Log::info('Telegram callback_query unknown', ['data' => $cbData]);
    }

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

        $this->bot->sendMessage(
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

        $this->bot->sendMessage(
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

        $this->bot->sendMessage(
            $chatId,
            "✅ Đã chọn: <b>{$pkg->name}</b>\n"
                . "<i>Loại: {$pkg->account_type} · Danh mục: " . ($pkg->category?->name ?? '—') . "</i>"
        );
        $this->promptFamilyEmail($chatId);
    }

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
            $this->clearState($chatId);
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

    private function promptCustomerName(int|string $chatId, array $data): void
    {
        $amount = (int) ($data['amount'] ?? 0);
        $this->bot->sendMessage(
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
        $this->bot->sendMessage(
            $chatId,
            "⏰ <b>Bước 2/7:</b> Thời hạn tài khoản?\n"
                . "<i>Vd: <code>1m</code> (1 tháng), <code>25d</code> (25 ngày), <code>1y</code> (1 năm)</i>",
            $this->navMarkup()
        );
    }

    private function promptEmail(int|string $chatId): void
    {
        $this->bot->sendMessage(
            $chatId,
            "📧 <b>Bước 3/7:</b> Email tài khoản?\n"
                . "<i>Vd: <code>huatungthang@gmail.com</code></i>",
            $this->navMarkup()
        );
    }

    private function promptFamilyEmail(int|string $chatId): void
    {
        $this->bot->sendMessage(
            $chatId,
            "👥 <b>Bước 5/7:</b> Mã nhóm - gia đình?\n"
                . "<i>Có thể là email / số / mã / text bất kỳ.</i>\n"
                . "<i>Vd: <code>2</code>, <code>gd_abc@gmail.com</code>, <code>gia đình A</code></i>\n"
                . "<i>Gõ <code>skip</code> nếu không có</i>",
            $this->navMarkup()
        );
    }

    private function promptWarranty(int|string $chatId): void
    {
        $this->bot->sendMessage(
            $chatId,
            "🛡 <b>Bước 6/7:</b> Bảo hành?\n"
                . "<i>Vd: <code>30d</code> (30 ngày), <code>1m</code> (1 tháng), <code>1y</code> (1 năm)</i>\n"
                . "<i>Gõ <code>full</code> = full thời hạn, <code>skip</code> = không bảo hành</i>",
            $this->navMarkup()
        );
    }

    private function promptProfit(int|string $chatId): void
    {
        $this->bot->sendMessage(
            $chatId,
            "💵 <b>Bước 7/7:</b> Lợi nhuận của đơn?\n"
                . "<i>Vd: <code>50k</code>, <code>200k</code>, <code>1.5tr</code></i>\n"
                . "<i>Gõ <code>skip</code> nếu chưa biết (có thể nhập sau qua web)</i>",
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
            . "<b>Cách tạo đơn:</b> Gõ số tiền để bắt đầu, bot sẽ hỏi 7 bước:\n"
            . "1️⃣ Tên/mã khách hàng (mới: tự tạo + sinh KUN; cũ: gõ tên hoặc mã KUN/CTV)\n"
            . "2️⃣ Thời hạn — <code>1m</code>=tháng, <code>25d</code>=ngày, <code>1y</code>=năm\n"
            . "3️⃣ Email tài khoản\n"
            . "4️⃣ Gói dịch vụ (chọn từ danh mục hoặc gõ keyword search)\n"
            . "5️⃣ Mã nhóm-gia đình (email/số/text bất kỳ, hoặc <code>skip</code>)\n"
            . "6️⃣ Bảo hành — <code>30d</code>/<code>1m</code>/<code>1y</code>, <code>full</code>=full thời hạn, <code>skip</code>=không\n"
            . "7️⃣ Lợi nhuận — <code>50k</code>/<code>200k</code>/<code>1.5tr</code> hoặc <code>skip</code>\n\n"
            . "<b>Số tiền hợp lệ:</b>\n"
            . "<code>100k</code>, <code>200k</code>, <code>1.5tr</code>, <code>500000</code>\n\n"
            . "<b>Lệnh:</b>\n"
            . "/list — đơn pending hôm nay\n"
            . "/cancel DH-XXX-XXX — huỷ 1 đơn\n"
            . "/huy — huỷ conversation đang gõ\n"
            . "/lai — quay về bước trước\n"
            . "/help — hướng dẫn này\n\n"
            . "Sau khi xong, bot trả về QR + chi tiết đơn. Khi khách CK, hệ thống tự tạo dịch vụ trên web.";
    }
}
