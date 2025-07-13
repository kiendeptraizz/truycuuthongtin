<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\ServicePackage;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class LeadController extends Controller
{
    public function index(Request $request): View
    {
        $query = Lead::with(['servicePackage', 'assignedUser', 'customer'])
            ->orderBy('created_at', 'desc');

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Lọc theo độ ưu tiên
        if ($request->filled('priority')) {
            $query->byPriority($request->priority);
        }

        // Lọc theo người phụ trách
        if ($request->filled('assigned_to')) {
            $query->assignedTo($request->assigned_to);
        }

        // Lọc theo nguồn
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        // Lọc cần theo dõi hôm nay
        if ($request->filled('need_follow_up') && $request->need_follow_up) {
            $query->needFollowUpToday();
        }

        // Lọc quá hạn
        if ($request->filled('overdue') && $request->overdue) {
            $query->overdueFollowUp();
        }

        $leads = $query->paginate(20);

        // Statistics
        $stats = [
            'total' => Lead::count(),
            'new' => Lead::where('status', 'new')->count(),
            'follow_up_today' => Lead::needFollowUpToday()->count(),
            'overdue' => Lead::overdueFollowUp()->count(),
            'converted_this_month' => Lead::where('status', 'won')
                ->whereMonth('converted_at', now()->month)
                ->count(),
        ];

        $servicePackages = ServicePackage::all();
        $users = User::all();

        return view('admin.leads.index', compact('leads', 'stats', 'servicePackages', 'users'));
    }

    public function create(): View
    {
        $servicePackages = ServicePackage::all();
        $users = User::all();

        return view('admin.leads.create', compact('servicePackages', 'users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'source' => 'required|string|max:100',
            'status' => 'required|in:new,contacted,interested,quoted,negotiating,won,lost,follow_up',
            'priority' => 'required|in:low,medium,high,urgent',
            'notes' => 'nullable|string',
            'requirements' => 'nullable|string',
            'estimated_value' => 'nullable|numeric|min:0',
            'service_package_id' => 'nullable|exists:service_packages,id',
            'assigned_to' => 'nullable|exists:users,id',
            'next_follow_up_at' => 'nullable|date',
        ]);

        $lead = Lead::create($validated);

        // Thêm hoạt động tạo lead
        $lead->addActivity('note', 'Lead được tạo mới');

        return redirect()->route('admin.leads.index')
            ->with('success', 'Tạo lead mới thành công!');
    }

    public function show(Lead $lead): View
    {
        $lead->load(['servicePackage', 'assignedUser', 'customer', 'activities.user']);

        return view('admin.leads.show', compact('lead'));
    }

    public function edit(Lead $lead): View
    {
        $servicePackages = ServicePackage::all();
        $users = User::all();

        return view('admin.leads.edit', compact('lead', 'servicePackages', 'users'));
    }

    public function update(Request $request, Lead $lead): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'source' => 'required|string|max:100',
            'status' => 'required|in:new,contacted,interested,quoted,negotiating,won,lost,follow_up',
            'priority' => 'required|in:low,medium,high,urgent',
            'notes' => 'nullable|string',
            'requirements' => 'nullable|string',
            'estimated_value' => 'nullable|numeric|min:0',
            'service_package_id' => 'nullable|exists:service_packages,id',
            'assigned_to' => 'nullable|exists:users,id',
            'next_follow_up_at' => 'nullable|date',
        ]);

        $oldStatus = $lead->status;
        $lead->update($validated);

        // Thêm hoạt động nếu có thay đổi trạng thái
        if ($oldStatus !== $validated['status']) {
            $lead->addActivity('note', "Trạng thái thay đổi từ '{$lead->getStatusName()}' thành '{$lead->getStatusName()}'");
        }

        return redirect()->route('admin.leads.show', $lead)
            ->with('success', 'Cập nhật lead thành công!');
    }

    public function destroy(Lead $lead): RedirectResponse
    {
        $lead->delete();

        return redirect()->route('admin.leads.index')
            ->with('success', 'Xóa lead thành công!');
    }

    public function addActivity(Request $request, Lead $lead): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:call,email,meeting,note,quote,follow_up',
            'notes' => 'required|string',
            'next_follow_up_at' => 'nullable|date',
        ]);

        $activity = $lead->addActivity($validated['type'], $validated['notes']);

        // Cập nhật lần liên hệ cuối
        $lead->updateLastContact();

        // Cập nhật lịch theo dõi tiếp theo nếu có
        if (!empty($validated['next_follow_up_at'])) {
            $lead->update(['next_follow_up_at' => $validated['next_follow_up_at']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Thêm hoạt động thành công!',
            'activity' => $activity->load('user')
        ]);
    }

    public function convert(Request $request, Lead $lead): RedirectResponse
    {
        if ($lead->status === 'won') {
            return redirect()->back()->with('error', 'Lead đã được chuyển đổi rồi!');
        }

        $validated = $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_address' => 'nullable|string',
        ]);

        // Tạo dữ liệu customer
        $customerData = array_filter([
            'name' => $validated['customer_name'] ?? $lead->name,
            'email' => $validated['customer_email'] ?? $lead->email,
            'phone' => $validated['customer_phone'] ?? $lead->phone,
            'address' => $validated['customer_address'] ?? null,
        ]);

        $customer = $lead->convertToCustomer($customerData);

        // Thêm hoạt động chuyển đổi
        $lead->addActivity('converted', "Lead được chuyển đổi thành khách hàng: {$customer->name}");

        return redirect()->route('admin.customers.show', $customer)
            ->with('success', 'Chuyển đổi lead thành khách hàng thành công!');
    }

    public function markAsLost(Request $request, Lead $lead): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $lead->update(['status' => 'lost']);

        $reason = $validated['reason'] ?? 'Không có lý do cụ thể';
        $lead->addActivity('lost', "Lead bị mất. Lý do: {$reason}");

        return redirect()->back()
            ->with('success', 'Đánh dấu lead là mất thành công!');
    }

    public function bulkAction(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'action' => 'required|in:assign,status,priority,delete',
            'lead_ids' => 'required|array',
            'lead_ids.*' => 'exists:leads,id',
            'value' => 'nullable|string',
        ]);

        $leads = Lead::whereIn('id', $validated['lead_ids']);

        switch ($validated['action']) {
            case 'assign':
                $leads->update(['assigned_to' => $validated['value']]);
                break;
            case 'status':
                $leads->update(['status' => $validated['value']]);
                break;
            case 'priority':
                $leads->update(['priority' => $validated['value']]);
                break;
            case 'delete':
                $leads->delete();
                break;
        }

        return response()->json([
            'success' => true,
            'message' => 'Thực hiện hành động hàng loạt thành công!'
        ]);
    }
}