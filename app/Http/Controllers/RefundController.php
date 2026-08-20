<?php

namespace App\Http\Controllers;

use App\Models\RefundRequest;
use App\Services\TelegramBotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RefundController extends Controller
{
    /**
     * Trang công khai: form khách hàng tự gửi yêu cầu hoàn tiền.
     */
    public function create()
    {
        return view('refund.create');
    }

    /**
     * Nhận yêu cầu hoàn tiền từ khách: lưu DB + upload ảnh QR (public disk).
     */
    public function store(Request $request, TelegramBotService $telegram)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'order_code'    => 'required|string|max:64',
            'bank_account'  => 'required|string|max:64',
            'qr_image'      => 'required|image|mimes:jpeg,jpg,png,webp|max:5120',
        ], [
            'customer_name.required' => 'Vui lòng nhập tên khách hàng.',
            'order_code.required'    => 'Vui lòng nhập mã đơn hàng cần hoàn tiền.',
            'bank_account.required'  => 'Vui lòng nhập số tài khoản nhận hoàn tiền.',
            'qr_image.required'      => 'Vui lòng gửi ảnh mã QR nhận tiền.',
            'qr_image.image'         => 'File QR phải là hình ảnh.',
            'qr_image.mimes'         => 'Ảnh QR chỉ chấp nhận JPG, PNG hoặc WEBP.',
            'qr_image.max'           => 'Ảnh QR tối đa 5MB.',
        ]);

        $path = $request->file('qr_image')->store('refund_qr', 'public');

        $refund = RefundRequest::create([
            'customer_name' => $validated['customer_name'],
            'order_code'    => strtoupper(trim($validated['order_code'])),
            'bank_account'  => trim($validated['bank_account']),
            'qr_image_path' => $path,
            'ip_address'    => $request->ip(),
        ]);

        $this->notifyAdmins($telegram, $refund);

        return redirect()
            ->route('refund.create')
            ->with('refund_success', $refund->code);
    }

    /**
     * Báo admin qua Telegram khi có yêu cầu hoàn tiền mới (kèm ảnh QR).
     * Best-effort: lỗi Telegram không được chặn phản hồi cho khách.
     */
    private function notifyAdmins(TelegramBotService $telegram, RefundRequest $refund): void
    {
        try {
            if (!$telegram->isConfigured()) {
                return;
            }

            $caption = "🔔 <b>YÊU CẦU HOÀN TIỀN MỚI</b>\n\n"
                . "🧾 Mã theo dõi: <b>{$refund->code}</b>\n"
                . "👤 Khách: <b>" . e($refund->customer_name) . "</b>\n"
                . "📦 Mã đơn: <code>" . e($refund->order_code) . "</code>\n"
                . "🏦 Số TK: <b>" . e($refund->bank_account) . "</b>\n"
                . "🕒 " . $refund->created_at->format('H:i d/m/Y') . "\n\n"
                . "➡️ Xử lý: " . rtrim(config('app.url'), '/') . "/admin/refund-requests";

            $qrUrl = $refund->qr_url;

            foreach ($telegram->adminIds() as $chatId) {
                if ($qrUrl) {
                    $telegram->sendPhoto($chatId, $qrUrl, $caption);
                } else {
                    $telegram->sendMessage($chatId, $caption, ['disable_web_page_preview' => true]);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Refund: gửi thông báo Telegram thất bại', [
                'refund_code' => $refund->code,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
