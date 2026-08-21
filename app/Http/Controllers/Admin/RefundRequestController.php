<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerService;
use App\Models\RefundRequest;
use App\Services\RefundCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
     * Xem chi tiết: khớp đúng đơn theo mã + tính tiền hoàn theo ngày còn lại,
     * lấy mốc là NGÀY TÀI KHOẢN LỖI (admin nhập) chứ không phải hôm nay.
     */
    public function show(Request $request, RefundRequest $refundRequest, RefundCalculator $calc)
    {
        $service = $this->findMatchedService($refundRequest);

        // Ngày lỗi: ưu tiên query param (khi admin đổi để xem trước), rồi giá trị đã lưu, rồi hôm nay.
        $raw = $request->query('error_date') ?: optional($refundRequest->error_date)->format('Y-m-d');
        try {
            $errorDate = $raw ? Carbon::parse($raw)->startOfDay() : now()->startOfDay();
        } catch (\Throwable $e) {
            $errorDate = now()->startOfDay();
        }

        $calcResult = $service ? $calc->compute($service, $errorDate) : null;

        return view('admin.refund-requests.show', [
            'refundRequest' => $refundRequest,
            'service' => $service,
            'calc' => $calcResult,
            'errorDate' => $errorDate,
        ]);
    }

    /**
     * Cập nhật trạng thái + ghi chú. Nếu form gửi kèm error_date (trang chi tiết)
     * thì lưu ngày lỗi và tính lại số tiền hoàn (computed_refund) theo mốc đó.
     */
    public function update(Request $request, RefundRequest $refundRequest, RefundCalculator $calc)
    {
        $validated = $request->validate([
            'status'     => 'required|in:pending,done,rejected',
            'admin_note' => 'nullable|string|max:1000',
            'error_date' => 'nullable|date',
        ]);

        $data = [
            'status'     => $validated['status'],
            'admin_note' => $validated['admin_note'] ?? null,
        ];

        // Chỉ đụng tới error_date/computed_refund khi form CÓ gửi field (trang chi tiết).
        // Modal ở trang danh sách không gửi → giữ nguyên giá trị cũ.
        if ($request->has('error_date')) {
            $errorDate = $validated['error_date'] ? Carbon::parse($validated['error_date'])->startOfDay() : null;
            $data['error_date'] = $errorDate;
            $data['computed_refund'] = null;

            $service = $this->findMatchedService($refundRequest);
            if ($service && $errorDate) {
                $result = $calc->compute($service, $errorDate);
                if (($result['ok'] ?? false) === true) {
                    $data['computed_refund'] = $result['refund_amount'] ?? null;
                }
            }
        }

        $refundRequest->update($data);

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

        return redirect()->route('admin.refund-requests.index')
            ->with('success', 'Đã xóa yêu cầu hoàn tiền.');
    }

    /**
     * Khớp CustomerService theo mã đơn khách nhập (chấp nhận có/không dấu gạch).
     */
    private function findMatchedService(RefundRequest $refundRequest): ?CustomerService
    {
        $code = trim((string) $refundRequest->order_code);
        if ($code === '') {
            return null;
        }
        $upper = strtoupper($code);
        $stripped = str_replace(['-', ' '], '', $upper);

        return CustomerService::where('order_code', $upper)
            ->orWhereRaw('UPPER(REPLACE(order_code, "-", "")) = ?', [$stripped])
            ->with(['customer', 'servicePackage'])
            ->first();
    }
}
