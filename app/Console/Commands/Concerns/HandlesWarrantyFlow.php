<?php

namespace App\Console\Commands\Concerns;

/**
 * Warranty 3-step flow trên bot Telegram.
 *
 * Tách trait từ god-class TelegramListenCommand (P1.3 audit).
 *
 * Flow state machine:
 *   warranty_email → warranty_password → warranty_extend → warranty_note → finalize
 *
 * Steps email/password được handle trong handleConversationStep (giữ ở class
 * chính vì gắn liền state machine chung). Trait này chứa:
 *   - handleWarrantyStartCallback: entry point từ "🛡 Bảo hành" button
 *   - handleWarrantySkipEmail / SkipExtend: callback cho "⏭ Bỏ qua"
 *   - promptWarrantyExtend / Note: prompt cho 2 step cuối
 *   - finalizeWarranty: submit qua WarrantyService + reply summary
 *
 * Tất cả prompt đều sendAndTrack để purgeTrackedMessages khi finalize.
 */
trait HandlesWarrantyFlow
{
    /**
     * User bấm "🛡 Bảo hành" — start state machine: hỏi email TK mới (hoặc skip),
     * gia hạn ngày (hoặc skip), ghi chú.
     */
    private function handleWarrantyStartCallback(int|string $chatId, string $userId, int $csId): void
    {
        $cs = \App\Models\CustomerService::find($csId);
        if (!$cs) {
            $this->bot->sendMessage($chatId, "❌ Dịch vụ không tồn tại.");
            return;
        }
        if ($cs->status === 'cancelled') {
            $this->bot->sendMessage($chatId, "⚠️ Đơn đã huỷ — không thể bảo hành.");
            return;
        }

        // Set state warranty step 1: email mới
        $this->setState($chatId, [
            'step' => 'warranty_email',
            'data' => [
                'cs_id' => $csId,
                'order_code' => $cs->order_code,
                'telegram_user_id' => $userId,
            ],
        ]);

        // sendAndTrack để cleanup prompt khi finalize warranty (warranty_note step
        // đã gọi purgeTrackedMessages — chat sạch sau mỗi phiên bảo hành).
        $this->sendAndTrack(
            $chatId,
            "🛡 <b>BẢO HÀNH</b> — đơn <code>{$cs->order_code}</code>\n\n"
                . "Email TK hiện tại: <code>" . e($cs->login_email ?? '—') . "</code>\n\n"
                . "<b>Bước 1/3:</b> Nhập email TK <b>mới</b> (nếu đổi TK).",
            ['reply_markup' => json_encode([
                'inline_keyboard' => [[
                    ['text' => '⏭ Bỏ qua (không đổi TK)', 'callback_data' => 'wr_skip_email'],
                ]],
            ])]
        );
    }

    private function handleWarrantySkipEmail(int|string $chatId): void
    {
        $state = $this->getState($chatId);
        if (!$state || ($state['step'] ?? '') !== 'warranty_email') return;

        $data = $state['data'] ?? [];
        $data['replacement_email'] = null;
        $data['replacement_password'] = null;

        $this->setState($chatId, ['step' => 'warranty_extend', 'data' => $data]);
        $this->promptWarrantyExtend($chatId);
    }

    private function handleWarrantySkipExtend(int|string $chatId): void
    {
        $state = $this->getState($chatId);
        if (!$state || ($state['step'] ?? '') !== 'warranty_extend') return;

        $data = $state['data'] ?? [];
        $data['extended_days'] = null;

        $this->setState($chatId, ['step' => 'warranty_note', 'data' => $data]);
        $this->promptWarrantyNote($chatId);
    }

    private function promptWarrantyExtend(int|string $chatId): void
    {
        // sendAndTrack — prompt sẽ bị xoá khi warranty_note finalize (chat sạch).
        $this->sendAndTrack(
            $chatId,
            "<b>Bước 2/3:</b> Số ngày <b>gia hạn thêm</b> cho đơn (cộng vào ngày hết hạn hiện tại).\n"
                . "<i>VD: <code>7</code> = thêm 7 ngày, <code>30</code> = thêm 30 ngày.</i>",
            ['reply_markup' => json_encode([
                'inline_keyboard' => [[
                    ['text' => '⏭ Bỏ qua (không gia hạn)', 'callback_data' => 'wr_skip_extend'],
                ]],
            ])]
        );
    }

    private function promptWarrantyNote(int|string $chatId): void
    {
        // sendAndTrack — prompt sẽ bị xoá khi warranty_note finalize (chat sạch).
        $this->sendAndTrack(
            $chatId,
            "<b>Bước 3/3:</b> Nhập <b>ghi chú bảo hành</b> (lý do, mô tả lỗi, …)\n"
                . "<i>VD: \"TK lỗi đăng nhập, đã đổi TK mới\" / \"Khách báo lỗi tạm, đã hỗ trợ qua Zalo\".</i>"
        );
    }

    /**
     * Submit warranty từ bot — gọi WarrantyService rồi reply summary.
     */
    private function finalizeWarranty(int|string $chatId, array $data, string $note): void
    {
        $csId = $data['cs_id'] ?? null;
        if (!$csId) {
            $this->bot->sendMessage($chatId, "❌ State lỗi (thiếu cs_id). Hãy thử lại từ /dh.");
            return;
        }

        $cs = \App\Models\CustomerService::find($csId);
        if (!$cs) {
            $this->bot->sendMessage($chatId, "❌ Dịch vụ không tồn tại.");
            return;
        }

        $service = app(\App\Services\WarrantyService::class);
        $result = $service->apply($cs, [
            'replacement_email' => $data['replacement_email'] ?? null,
            'replacement_password' => $data['replacement_password'] ?? null,
            'extended_days' => $data['extended_days'] ?? null,
            'note' => $note,
            'actor_type' => 'bot',
            'actor_id' => null,
            'actor_label' => 'telegram:' . ($data['telegram_user_id'] ?? '?'),
        ]);

        if (!$result['ok']) {
            $this->bot->sendMessage(
                $chatId,
                "❌ Lỗi ghi nhận bảo hành: " . ($result['error'] ?? '?')
            );
            return;
        }

        $lines = ["✅ <b>Đã ghi nhận bảo hành</b> cho đơn <code>{$cs->order_code}</code>"];
        if (!empty($data['replacement_email'])) {
            $lines[] = "📧 TK mới: <code>" . e($data['replacement_email']) . "</code>";
        }
        if (!empty($data['extended_days'])) {
            $cs->refresh();
            $lines[] = "⏰ Gia hạn thêm <b>{$data['extended_days']} ngày</b>";
            if ($cs->expires_at) {
                $lines[] = "🔴 Ngày hết hạn mới: <b>" . $cs->expires_at->format('d/m/Y') . "</b>";
            }
        }
        $lines[] = "📝 " . e($note);

        $appUrl = rtrim(config('app.url'), '/');
        $lines[] = "";
        $lines[] = "🔗 <a href=\"{$appUrl}/admin/customer-services/{$cs->id}/warranty\">Xem lịch sử bảo hành</a>";

        $this->bot->sendMessage(
            $chatId,
            implode("\n", $lines),
            ['disable_web_page_preview' => true]
        );
    }
}
