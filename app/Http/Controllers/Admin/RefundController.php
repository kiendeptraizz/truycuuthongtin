<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerService;
use App\Services\RefundCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Refund tính + xác nhận cho CustomerService.
 *
 * Flow:
 *   GET  /admin/customer-services/{cs}/refund — preview tiền hoàn (computed)
 *   POST /admin/customer-services/{cs}/refund — admin xác nhận hoàn → huỷ đơn
 *
 * Admin trả tiền THỦ CÔNG ngoài hệ thống. Hệ thống chỉ ghi nhận:
 *   - refund_amount (số tiền đã trả lại)
 *   - refunded_at (thời điểm)
 *   - refund_reason (lý do)
 *   - status = 'cancelled'
 */
class RefundController extends Controller
{
    /**
     * GET — preview refund + form xác nhận.
     */
    public function preview(CustomerService $customerService, RefundCalculator $calc)
    {
        $calculation = $calc->compute($customerService);

        return view('admin.customer-services.refund', [
            'cs' => $customerService->load(['customer', 'servicePackage']),
            'calc' => $calculation,
        ]);
    }

    /**
     * POST — confirm + execute refund:
     *   - Lưu refund_amount, refunded_at, refund_reason
     *   - Set status='cancelled'
     *   - Audit log tự động ghi qua CustomerServiceObserver
     */
    public function confirm(Request $request, CustomerService $customerService, RefundCalculator $calc)
    {
        if ($customerService->refunded_at !== null) {
            return back()->withErrors(['error' => 'Đơn này đã được hoàn tiền trước đó.']);
        }
        if ($customerService->status === 'cancelled') {
            return back()->withErrors(['error' => 'Đơn đã huỷ — không thể hoàn lại.']);
        }

        $validated = $request->validate([
            'refund_amount' => 'required|integer|min:0',
            'refund_reason' => 'required|string|min:3|max:1000',
        ]);

        // Tính lại để biết cap upper bound (hoàn không quá order_amount)
        $calculation = $calc->compute($customerService);
        $maxAllowed = (int) ($calculation['order_amount'] ?? $customerService->order_amount ?? 0);

        if ($validated['refund_amount'] > $maxAllowed && $maxAllowed > 0) {
            return back()->withErrors([
                'refund_amount' => "Số tiền hoàn không được vượt quá số tiền đơn (" . number_format($maxAllowed, 0, ',', '.') . "đ).",
            ])->withInput();
        }

        DB::transaction(function () use ($customerService, $validated) {
            $customerService->update([
                'refund_amount' => $validated['refund_amount'],
                'refunded_at' => now(),
                'refund_reason' => $validated['refund_reason'],
                'status' => 'cancelled',
            ]);
        });

        Log::info('Refund confirmed', [
            'cs_id' => $customerService->id,
            'order_code' => $customerService->order_code,
            'admin_id' => auth()->id(),
            'amount' => $validated['refund_amount'],
        ]);

        return redirect()->route('admin.customer-services.show', $customerService)
            ->with('success', sprintf(
                '✅ Đã ghi nhận hoàn %sđ cho đơn %s. Đơn đã huỷ. Vui lòng chuyển khoản cho khách thủ công.',
                number_format($validated['refund_amount'], 0, ',', '.'),
                $customerService->order_code ?: '#' . $customerService->id
            ));
    }
}
