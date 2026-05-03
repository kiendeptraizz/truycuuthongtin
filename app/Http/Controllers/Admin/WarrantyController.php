<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerService;
use App\Services\WarrantyService;
use Illuminate\Http\Request;

/**
 * Bảo hành cho 1 CustomerService — list lịch sử + form thêm bảo hành mới.
 *
 * Routes:
 *   GET  /admin/customer-services/{cs}/warranty — trang list + form
 *   POST /admin/customer-services/{cs}/warranty — submit bảo hành mới
 */
class WarrantyController extends Controller
{
    public function index(CustomerService $customerService)
    {
        $customerService->load(['customer', 'servicePackage', 'warranties.actor']);

        return view('admin.customer-services.warranty', [
            'cs' => $customerService,
            'warranties' => $customerService->warranties, // đã orderByDesc trong relation
        ]);
    }

    public function store(Request $request, CustomerService $customerService, WarrantyService $service)
    {
        $validated = $request->validate([
            'replacement_email' => 'nullable|email|max:255',
            'replacement_password' => 'nullable|string|max:255',
            'extended_days' => 'nullable|integer|min:0|max:3650',
            'note' => 'required|string|min:3|max:2000',
        ]);

        $user = $request->user();
        $result = $service->apply($customerService, [
            'replacement_email' => $validated['replacement_email'] ?? null,
            'replacement_password' => $validated['replacement_password'] ?? null,
            'extended_days' => $validated['extended_days'] ?? null,
            'note' => $validated['note'],
            'actor_type' => 'user',
            'actor_id' => $user?->id,
            'actor_label' => $user?->email ?? $user?->name,
        ]);

        if (!$result['ok']) {
            return back()->withErrors(['error' => $result['error'] ?? 'Lỗi bảo hành.'])->withInput();
        }

        $msg = '✅ Đã ghi nhận bảo hành.';
        if (!empty($validated['replacement_email'])) {
            $msg .= " TK đã đổi thành <code>{$validated['replacement_email']}</code>.";
        }
        if (!empty($validated['extended_days'])) {
            $msg .= " Đã gia hạn thêm {$validated['extended_days']} ngày.";
        }

        return redirect()->route('admin.customer-services.warranty', $customerService)
            ->with('success', $msg);
    }
}
