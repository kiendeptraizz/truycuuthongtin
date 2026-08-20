<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RefundRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RefundRequestController extends Controller
{
    /**
     * Danh sách yêu cầu hoàn tiền khách gửi từ trang /refund.
     */
    public function index(Request $request)
    {
        $status = $request->get('status');
        $q = trim((string) $request->get('q', ''));

        $refundRequests = RefundRequest::query()
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('customer_name', 'like', "%{$q}%")
                        ->orWhere('order_code', 'like', "%{$q}%")
                        ->orWhere('code', 'like', "%{$q}%")
                        ->orWhere('bank_account', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $pendingCount = RefundRequest::where('status', 'pending')->count();

        return view('admin.refund-requests.index', compact('refundRequests', 'status', 'q', 'pendingCount'));
    }

    /**
     * Cập nhật trạng thái + ghi chú xử lý.
     */
    public function update(Request $request, RefundRequest $refundRequest)
    {
        $validated = $request->validate([
            'status'     => 'required|in:pending,done,rejected',
            'admin_note' => 'nullable|string|max:1000',
        ]);

        $refundRequest->update($validated);

        return back()->with('success', 'Đã cập nhật yêu cầu hoàn tiền ' . $refundRequest->code . '.');
    }

    /**
     * Xóa yêu cầu (đồng thời xóa ảnh QR khỏi storage).
     */
    public function destroy(RefundRequest $refundRequest)
    {
        if ($refundRequest->qr_image_path) {
            Storage::disk('public')->delete($refundRequest->qr_image_path);
        }

        $refundRequest->delete();

        return back()->with('success', 'Đã xóa yêu cầu hoàn tiền.');
    }
}
