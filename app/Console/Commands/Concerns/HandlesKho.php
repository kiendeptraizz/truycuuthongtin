<?php

namespace App\Console\Commands\Concerns;

use App\Models\ResourceAccount;
use App\Models\ResourceCategory;
use App\Services\GoogleSheetsWebhookService;
use Illuminate\Support\Facades\Log;

/**
 * Trait quản lý "Kho TK" (Resource Account) qua bot Telegram.
 *
 * Flow:
 *   1. User bấm "📦 Kho TK" → sendKhoMenu (inline: ➕ Thêm / 🔍 Tìm)
 *   2. Bấm "➕ Thêm TK" → kho_select_category step (inline keyboard list categories)
 *   3. Click 1 category → kho_email step
 *   4. Gõ email → kho_password step
 *   5. Gõ password → kho_note step
 *   6. Gõ note hoặc bấm /skip → save ResourceAccount + push Google Sheet (nếu cấu hình)
 *
 * Lệnh:
 *   /kho list [keyword]  — show top 10 TK chưa bán, optional filter keyword
 */
trait HandlesKho
{
    /** Bấm "📦 Kho TK" — show menu 2 options. */
    private function sendKhoMenu(int|string $chatId): void
    {
        $availableCount = ResourceAccount::where('is_available', true)->count();
        $totalCount = ResourceAccount::count();

        $extras = ['reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text' => '➕ Thêm TK mới', 'callback_data' => 'kho_add']],
                [['text' => '🔍 Xem kho (10 TK gần nhất)', 'callback_data' => 'kho_list']],
            ],
        ])];
        $this->bot->sendMessage(
            $chatId,
            "📦 <b>Kho tài khoản</b>\n\n"
                . "Tổng số TK: <b>{$totalCount}</b> (còn <b>{$availableCount}</b> chưa bán).\n\n"
                . "Chọn hành động:",
            $extras
        );
    }

    /** Click "➕ Thêm TK" — prompt chọn category. */
    private function handleKhoAddCallback(int|string $chatId): void
    {
        $categories = ResourceCategory::where('is_active', true)
            ->orderBy('sort_order')->orderBy('name')->get();

        if ($categories->isEmpty()) {
            $this->bot->sendMessage(
                $chatId,
                "❌ Chưa có category resource nào. Vào /admin/resources tạo category trước."
            );
            return;
        }

        // Inline keyboard 2 cols per row
        $rows = [];
        $row = [];
        foreach ($categories as $i => $cat) {
            $label = ($cat->icon ? $cat->icon . ' ' : '') . $cat->name;
            $row[] = ['text' => $label, 'callback_data' => "kho_cat_{$cat->id}"];
            if (count($row) >= 2) {
                $rows[] = $row;
                $row = [];
            }
        }
        if (!empty($row)) $rows[] = $row;
        $rows[] = [['text' => '❌ Huỷ', 'callback_data' => 'kho_cancel']];

        $this->bot->sendMessage(
            $chatId,
            "📦 <b>Thêm TK kho — Bước 1/4: Category</b>\n\n"
                . "Chọn danh mục TK bạn vừa mua:",
            ['reply_markup' => json_encode(['inline_keyboard' => $rows])]
        );
    }

    /** Click 1 category → start kho_email step. */
    private function handleKhoCategoryCallback(int|string $chatId, int $categoryId): void
    {
        $cat = ResourceCategory::find($categoryId);
        if (!$cat) {
            $this->bot->sendMessage($chatId, "❌ Category không tồn tại.");
            return;
        }

        $data = [
            'category_id' => $cat->id,
            'category_name' => $cat->name,
        ];
        $this->setState($chatId, ['step' => 'kho_email', 'data' => $data]);

        $this->sendAndTrack(
            $chatId,
            "✅ Đã chọn: <b>" . e($cat->name) . "</b>\n\n"
                . "📧 <b>Bước 2/4: Email / Username</b>\n"
                . "<i>Gõ email hoặc username TK (vd: <code>user@gmail.com</code>)</i>"
        );
    }

    /** Cancel kho flow callback. */
    private function handleKhoCancelCallback(int|string $chatId): void
    {
        $this->clearStateAndPurge($chatId);
        $this->bot->sendMessage($chatId, "❌ Đã huỷ thêm TK kho.");
    }

    /** Show 10 TK gần nhất (chưa bán). */
    private function handleKhoListCallback(int|string $chatId, ?string $keyword = null): void
    {
        $query = ResourceAccount::with('category')
            ->where('is_available', true)
            ->orderByDesc('created_at')
            ->limit(10);

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('email', 'LIKE', "%{$keyword}%")
                    ->orWhere('username', 'LIKE', "%{$keyword}%")
                    ->orWhere('notes', 'LIKE', "%{$keyword}%");
            });
        }

        $accounts = $query->get();
        $total = ResourceAccount::where('is_available', true)->count();

        if ($accounts->isEmpty()) {
            $msg = $keyword
                ? "🔍 Không tìm thấy TK khớp <code>" . e($keyword) . "</code> trong kho."
                : "📦 Kho rỗng (chưa có TK nào chưa bán).";
            $this->bot->sendMessage($chatId, $msg);
            return;
        }

        $header = $keyword
            ? "🔍 <b>Kết quả tìm \"" . e($keyword) . "\"</b>: " . $accounts->count() . " TK\n\n"
            : "📦 <b>10 TK gần nhất trong kho</b> (tổng <b>{$total}</b> còn):\n\n";

        $lines = [$header];
        foreach ($accounts as $acc) {
            $catName = $acc->category->name ?? '?';
            $lines[] = "• #" . $acc->id . " — <b>" . e($catName) . "</b>\n"
                . "  📧 <code>" . e($acc->email ?? $acc->username ?? '?') . "</code>\n"
                . ($acc->notes ? "  📝 <i>" . e(mb_substr($acc->notes, 0, 80)) . "</i>\n" : '')
                . "  🕐 " . $acc->created_at->format('d/m/Y');
        }

        $this->bot->sendMessage($chatId, implode("\n\n", $lines));
    }

    /** Finalize: save ResourceAccount + push Sheet. */
    private function finalizeKhoAdd(int|string $chatId, array $data): void
    {
        try {
            $account = ResourceAccount::create([
                'resource_category_id' => $data['category_id'],
                'email' => $data['email'] ?? null,
                'username' => null,
                'password' => $data['password'] ?? null,
                'notes' => $data['note'] ?? null,
                'is_available' => true,
                'status' => 'active',
            ]);
        } catch (\Throwable $e) {
            Log::error('Bot kho: save ResourceAccount failed', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            $this->bot->sendMessage($chatId, "❌ Lỗi lưu kho: " . $e->getMessage());
            $this->clearStateAndPurge($chatId);
            return;
        }

        $trackedMsgs = $data['_track_msgs'] ?? [];
        $this->clearState($chatId);

        $sheetStatus = '';
        try {
            $sheetService = app(GoogleSheetsWebhookService::class);
            if ($sheetService->isEnabled()) {
                $pushed = $sheetService->pushResourceAccount($account);
                $sheetStatus = $pushed
                    ? "\n📊 <i>Đã sync ra Google Sheet.</i>"
                    : "\n⚠️ <i>Sync Sheet thất bại (xem log).</i>";
            }
        } catch (\Throwable $e) {
            Log::warning('Bot kho: GoogleSheetsWebhookService failed', ['error' => $e->getMessage()]);
            $sheetStatus = "\n⚠️ <i>Sync Sheet lỗi: " . e($e->getMessage()) . "</i>";
        }

        $this->bot->sendMessage(
            $chatId,
            "✅ <b>Đã lưu vào kho TK #{$account->id}</b>\n\n"
                . "📦 Category: <b>" . e($data['category_name']) . "</b>\n"
                . "📧 Email: <code>" . e($account->email ?? '?') . "</code>\n"
                . ($account->notes ? "📝 Note: <i>" . e($account->notes) . "</i>\n" : '')
                . $sheetStatus,
            $this->mainMenuMarkup()
        );

        // Async cleanup các prompt cũ
        if (!empty($trackedMsgs)) {
            $this->purgeTrackedMessages($chatId, ['_track_msgs' => $trackedMsgs]);
        }
    }
}
