<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FamilyAccount;
use App\Models\FamilyMember;
use App\Models\Customer;
use App\Models\ServicePackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FamilyAccountController extends Controller
{
    /**
     * Display a listing of family accounts
     */
    public function index(Request $request)
    {
        $query = FamilyAccount::with(['servicePackage', 'activeMembers', 'createdBy', 'managedBy']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('service_package_id')) {
            $query->where('service_package_id', $request->service_package_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('family_name', 'like', "%{$search}%")
                    ->orWhere('family_code', 'like', "%{$search}%")
                    ->orWhere('owner_email', 'like', "%{$search}%")
                    ->orWhere('owner_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('expiring_soon')) {
            $query->expiringSoon($request->expiring_soon ?: 7);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $familyAccounts = $query->paginate(15)->withQueryString();

        // Get service packages for filter
        $servicePackages = ServicePackage::where('account_type', 'Tài khoản add family')
            ->active()
            ->orderBy('name')
            ->get();

        // Statistics
        $stats = [
            'total_families' => FamilyAccount::count(),
            'active_families' => FamilyAccount::where('status', 'active')->count(),
            'expiring_soon' => FamilyAccount::expiringSoon()->count(),
            'total_members' => FamilyMember::where('status', 'active')->count(),
            'avg_members_per_family' => FamilyAccount::active()->avg('current_members'),
            'total_revenue' => FamilyAccount::sum('total_paid'),
        ];

        return view('admin.family-accounts.index', compact(
            'familyAccounts',
            'servicePackages',
            'stats'
        ));
    }

    /**
     * Show the form for creating a new family account
     */
    public function create()
    {
        $servicePackages = ServicePackage::with('category')
            ->where('account_type', 'Tài khoản add family')
            ->active()
            ->get();

        // Define account type priority for the selector
        $accountTypePriority = [
            'Tài khoản add family' => 1,
            'Tài khoản dùng chung' => 2,
            'Tài khoản chính chủ' => 3,
            'Tài khoản cấp (dùng riêng)' => 4,
        ];

        // Sort packages by account type priority, then by name
        $servicePackages = $servicePackages->sortBy(function ($package) use ($accountTypePriority) {
            $priority = $accountTypePriority[$package->account_type] ?? 999;
            return [$priority, $package->name];
        });

        return view('admin.family-accounts.create', compact('servicePackages', 'accountTypePriority'));
    }

    /**
     * Store a newly created family account
     */
    public function store(Request $request)
    {
        $request->validate([
            'family_name' => 'required|string|max:255',
            'service_package_id' => 'required|exists:service_packages,id',
            'owner_email' => 'required|email|max:255',
            'owner_name' => 'nullable|string|max:255',
            'max_members' => 'required|integer|min:1|max:20',
            'activated_at' => 'required|date',
            'expires_at' => 'required|date|after:activated_at',
            'family_notes' => 'nullable|string',
            'internal_notes' => 'nullable|string',
        ]);

        // Verify service package is family type
        $servicePackage = ServicePackage::findOrFail($request->service_package_id);
        if ($servicePackage->account_type !== 'Tài khoản add family') {
            return back()->withErrors(['service_package_id' => 'Gói dịch vụ phải là loại "Tài khoản add family"']);
        }

        $familyAccount = FamilyAccount::create([
            'family_name' => $request->family_name,
            'service_package_id' => $request->service_package_id,
            'owner_email' => $request->owner_email,
            'owner_name' => $request->owner_name,
            'max_members' => $request->max_members,
            'activated_at' => $request->activated_at,
            'expires_at' => $request->expires_at,
            'family_notes' => $request->family_notes,
            'internal_notes' => $request->internal_notes,
            'created_by' => auth('admin')->id(),
            'managed_by' => auth('admin')->id(),
        ]);

        return redirect()->route('admin.family-accounts.show', $familyAccount)
            ->with('success', 'Family Account đã được tạo thành công!');
    }

    /**
     * Display the specified family account
     */
    public function show(FamilyAccount $familyAccount)
    {
        $familyAccount->load([
            'servicePackage.category',
            'members.customer',
            'members.addedBy',
            'members.removedBy',
            'createdBy',
            'managedBy'
        ]);

        // Get members with different statuses
        $activeMembers = $familyAccount->members()->active()->with('customer')->get();
        $inactiveMembers = $familyAccount->members()->inactive()->with('customer')->get();
        $removedMembers = $familyAccount->members()->removed()->with('customer')->get();

        // Statistics for this family
        $memberStats = [
            'total_members' => $familyAccount->members()->count(),
            'active_members' => $activeMembers->count(),
            'inactive_members' => $inactiveMembers->count(),
            'removed_members' => $removedMembers->count(),
            'available_slots' => $familyAccount->available_slots,
            'usage_percentage' => $familyAccount->usage_percentage,
        ];

        return view('admin.family-accounts.show', compact(
            'familyAccount',
            'activeMembers',
            'inactiveMembers',
            'removedMembers',
            'memberStats'
        ));
    }

    /**
     * Show the form for editing the specified family account
     */
    public function edit(FamilyAccount $familyAccount)
    {
        $servicePackages = ServicePackage::with('category')
            ->where('account_type', 'Tài khoản add family')
            ->active()
            ->get();

        // Define account type priority for the selector
        $accountTypePriority = [
            'Tài khoản add family' => 1,
            'Tài khoản dùng chung' => 2,
            'Tài khoản chính chủ' => 3,
            'Tài khoản cấp (dùng riêng)' => 4,
        ];

        // Sort packages by account type priority, then by name
        $servicePackages = $servicePackages->sortBy(function ($package) use ($accountTypePriority) {
            $priority = $accountTypePriority[$package->account_type] ?? 999;
            return [$priority, $package->name];
        });

        return view('admin.family-accounts.edit', compact('familyAccount', 'servicePackages', 'accountTypePriority'));
    }

    /**
     * Update the specified family account
     */
    public function update(Request $request, FamilyAccount $familyAccount)
    {
        $request->validate([
            'family_name' => 'required|string|max:255',
            'service_package_id' => 'required|exists:service_packages,id',
            'owner_email' => 'required|email|max:255',
            'owner_name' => 'nullable|string|max:255',
            'max_members' => 'required|integer|min:1|max:20',
            'activated_at' => 'required|date',
            'expires_at' => 'required|date|after:activated_at',
            'status' => ['required', Rule::in(['active', 'expired', 'suspended', 'cancelled'])],
            'family_notes' => 'nullable|string',
            'internal_notes' => 'nullable|string',
        ]);

        // Verify service package is family type
        $servicePackage = ServicePackage::findOrFail($request->service_package_id);
        if ($servicePackage->account_type !== 'Tài khoản add family') {
            return back()->withErrors(['service_package_id' => 'Gói dịch vụ phải là loại "Tài khoản add family"']);
        }

        // Check if reducing max_members would exceed current members
        if ($request->max_members < $familyAccount->current_members) {
            return back()->withErrors(['max_members' => 'Không thể giảm số thành viên tối đa xuống dưới số thành viên hiện tại (' . $familyAccount->current_members . ')']);
        }

        $familyAccount->update([
            'family_name' => $request->family_name,
            'service_package_id' => $request->service_package_id,
            'owner_email' => $request->owner_email,
            'owner_name' => $request->owner_name,
            'max_members' => $request->max_members,
            'activated_at' => $request->activated_at,
            'expires_at' => $request->expires_at,
            'status' => $request->status,
            'family_notes' => $request->family_notes,
            'internal_notes' => $request->internal_notes,
            'managed_by' => auth('admin')->id(),
        ]);

        return redirect()->route('admin.family-accounts.show', $familyAccount)
            ->with('success', 'Family Account đã được cập nhật thành công!');
    }

    /**
     * Remove the specified family account
     */
    public function destroy(FamilyAccount $familyAccount)
    {
        // Check if family has active members
        if ($familyAccount->activeMembers()->count() > 0) {
            return back()->with('error', 'Không thể xóa Family Account có thành viên đang hoạt động. Vui lòng xóa tất cả thành viên trước.');
        }

        $familyName = $familyAccount->family_name;
        $familyAccount->delete();

        return redirect()->route('admin.family-accounts.index')
            ->with('success', "Family Account '{$familyName}' đã được xóa thành công!");
    }

    /**
     * Show form to add member to family account
     */
    public function addMemberForm(FamilyAccount $familyAccount)
    {
        if (!$familyAccount->canAddMember()) {
            return back()->with('error', 'Không thể thêm thành viên: Family đã đầy hoặc không hoạt động.');
        }

        // Get customers not already in this family
        $existingCustomerIds = $familyAccount->members()->pluck('customer_id')->toArray();
        $availableCustomers = Customer::whereNotIn('id', $existingCustomerIds)
            ->orderBy('name')
            ->get();

        return view('admin.family-accounts.add-member', compact('familyAccount', 'availableCustomers'));
    }

    /**
     * Add member to family account
     */
    public function addMember(Request $request, FamilyAccount $familyAccount)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'member_email' => 'nullable|email|max:255',
            'member_role' => 'required|in:member,admin',
            'member_notes' => 'nullable|string',
        ]);

        if (!$familyAccount->canAddMember()) {
            return back()->with('error', 'Không thể thêm thành viên: Family đã đầy hoặc không hoạt động.');
        }

        $customer = Customer::findOrFail($request->customer_id);

        // Check if customer is already in this family
        if ($familyAccount->members()->where('customer_id', $customer->id)->exists()) {
            return back()->with('error', 'Khách hàng này đã là thành viên của family.');
        }

        // Check if customer is in another active family
        $existingMembership = FamilyMember::where('customer_id', $customer->id)
            ->where('status', 'active')
            ->whereHas('familyAccount', function ($q) {
                $q->where('status', 'active');
            })
            ->first();

        if ($existingMembership) {
            return back()->with('error', 'Khách hàng này đã là thành viên của family khác: ' . $existingMembership->familyAccount->family_name);
        }

        try {
            $familyAccount->addMember($customer, [
                'member_email' => $request->member_email ?: $customer->email,
                'member_role' => $request->member_role,
                'member_notes' => $request->member_notes,
            ]);

            return redirect()->route('admin.family-accounts.show', $familyAccount)
                ->with('success', "Đã thêm {$customer->name} vào family thành công!");
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi khi thêm thành viên: ' . $e->getMessage());
        }
    }

    /**
     * Remove member from family account
     */
    public function removeMember(Request $request, FamilyAccount $familyAccount, FamilyMember $member)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        if ($member->family_account_id !== $familyAccount->id) {
            return back()->with('error', 'Thành viên không thuộc family này.');
        }

        if ($member->status === 'removed') {
            return back()->with('error', 'Thành viên đã được xóa trước đó.');
        }

        try {
            $memberName = $member->member_name;
            $familyAccount->removeMember($member, $request->reason);

            return back()->with('success', "Đã xóa {$memberName} khỏi family thành công!");
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi khi xóa thành viên: ' . $e->getMessage());
        }
    }

    /**
     * Update member status or role
     */
    public function updateMember(Request $request, FamilyAccount $familyAccount, FamilyMember $member)
    {
        $request->validate([
            'status' => 'required|in:active,inactive',
            'member_role' => 'required|in:member,admin',
            'member_notes' => 'nullable|string',
        ]);

        if ($member->family_account_id !== $familyAccount->id) {
            return back()->with('error', 'Thành viên không thuộc family này.');
        }

        $member->update([
            'status' => $request->status,
            'member_role' => $request->member_role,
            'member_notes' => $request->member_notes,
        ]);

        // Update family member count
        $familyAccount->updateMemberCount();

        return back()->with('success', 'Đã cập nhật thông tin thành viên thành công!');
    }

    /**
     * Family accounts report
     */
    public function report(Request $request)
    {
        // Top family accounts by member count
        $topFamilies = FamilyAccount::with('servicePackage')
            ->where('status', 'active')
            ->orderBy('current_members', 'desc')
            ->limit(10)
            ->get();

        // Family accounts expiring soon
        $expiringSoon = FamilyAccount::with('servicePackage')
            ->expiringSoon(30)
            ->orderBy('expires_at', 'asc')
            ->get();

        // Overall statistics
        $overallStats = [
            'total_families' => FamilyAccount::count(),
            'active_families' => FamilyAccount::where('status', 'active')->count(),
            'total_members' => FamilyMember::where('status', 'active')->count(),
            'avg_members_per_family' => round(FamilyAccount::active()->avg('current_members'), 2),
            'total_revenue' => FamilyAccount::sum('total_paid'),
            'monthly_revenue' => FamilyAccount::where('status', 'active')->sum('monthly_cost'),
            'families_near_full' => FamilyAccount::active()
                ->whereRaw('current_members >= max_members * 0.8')
                ->count(),
            'underutilized_families' => FamilyAccount::active()
                ->whereRaw('current_members <= max_members * 0.3')
                ->count(),
        ];

        // Service package distribution
        $packageStats = DB::table('family_accounts')
            ->join('service_packages', 'family_accounts.service_package_id', '=', 'service_packages.id')
            ->select('service_packages.name', DB::raw('COUNT(*) as count'))
            ->where('family_accounts.status', 'active')
            ->groupBy('service_packages.id', 'service_packages.name')
            ->orderBy('count', 'desc')
            ->get();

        return view('admin.family-accounts.report', compact(
            'topFamilies',
            'expiringSoon',
            'overallStats',
            'packageStats'
        ));
    }
}
