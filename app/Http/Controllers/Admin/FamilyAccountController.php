<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FamilyAccount;
use App\Models\ServicePackage;
use App\Models\FamilyMember;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class FamilyAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get service packages for family type với thống kê
        $servicePackages = ServicePackage::where('account_type', 'Tài khoản add family')
            ->orWhere('account_type', 'like', '%family%')
            ->active()
            ->withCount(['familyAccounts', 'familyAccounts as active_families_count' => function ($q) {
                $q->where('status', 'active');
            }])
            ->orderBy('name')
            ->get();

        // Nếu có filter theo package, lấy family accounts của package đó
        $familyAccounts = collect();
        $selectedPackage = null;
        $emailSearchResults = collect();
        $emailSearchQuery = null;

        // Global email search
        if ($request->filled('email_search')) {
            $emailSearchQuery = trim($request->email_search);
            $emailSearchResults = $this->searchByEmail($emailSearchQuery);
        }

        if ($request->filled('service_package_id')) {
            $selectedPackage = ServicePackage::find($request->service_package_id);

            $query = FamilyAccount::with(['servicePackage', 'members', 'customerServices'])
                ->where('service_package_id', $request->service_package_id);

            // Search filters
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('family_name', 'like', "%{$search}%")
                        ->orWhere('family_code', 'like', "%{$search}%")
                        ->orWhere('owner_email', 'like', "%{$search}%")
                        ->orWhere('owner_name', 'like', "%{$search}%");
                });
            }

            // Status filter
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $familyAccounts = $query->latest()->paginate(20)->withQueryString();

            // Cập nhật current_members
            foreach ($familyAccounts as $account) {
                $account->current_members = $account->customerServices->where('status', 'active')->count();
            }
        }

        // Statistics
        $stats = [
            'total' => FamilyAccount::count(),
            'active' => FamilyAccount::where('status', 'active')->count(),
            'expired' => FamilyAccount::where('status', 'expired')->count(),
            'suspended' => FamilyAccount::where('status', 'suspended')->count(),
        ];

        return view('admin.family-accounts.index', compact(
            'familyAccounts', 
            'servicePackages', 
            'stats', 
            'selectedPackage',
            'emailSearchResults',
            'emailSearchQuery'
        ));
    }

    /**
     * Search by email across all family-related data
     */
    private function searchByEmail(string $email)
    {
        $results = collect();

        // 1. Search in CustomerService (login_email) with family account
        $customerServices = \App\Models\CustomerService::with(['customer', 'familyAccount', 'servicePackage'])
            ->whereNotNull('family_account_id')
            ->where(function ($q) use ($email) {
                $q->where('login_email', 'like', "%{$email}%")
                  ->orWhereHas('customer', function ($cq) use ($email) {
                      $cq->where('email', 'like', "%{$email}%")
                        ->orWhere('name', 'like', "%{$email}%");
                  });
            })
            ->get();

        foreach ($customerServices as $service) {
            $results->push([
                'type' => 'customer_service',
                'email' => $service->login_email ?: ($service->customer->email ?? 'N/A'),
                'customer_name' => $service->customer->name ?? 'N/A',
                'customer_code' => $service->customer->customer_code ?? 'N/A',
                'customer_id' => $service->customer_id,
                'family_id' => $service->family_account_id,
                'family_name' => $service->familyAccount->family_name ?? 'N/A',
                'family_code' => $service->familyAccount->family_code ?? 'N/A',
                'service_package' => $service->servicePackage->name ?? 'N/A',
                'status' => $service->status,
                'expires_at' => $service->expires_at,
                'source' => 'Dịch vụ khách hàng',
            ]);
        }

        // 2. Search in FamilyAccount (owner_email)
        $familyAccounts = FamilyAccount::with('servicePackage')
            ->where('owner_email', 'like', "%{$email}%")
            ->get();

        foreach ($familyAccounts as $family) {
            // Check if not already in results
            $exists = $results->where('family_id', $family->id)->where('type', 'family_owner')->first();
            if (!$exists) {
                $results->push([
                    'type' => 'family_owner',
                    'email' => $family->owner_email,
                    'customer_name' => $family->owner_name ?? 'Chủ Family',
                    'customer_code' => '-',
                    'customer_id' => null,
                    'family_id' => $family->id,
                    'family_name' => $family->family_name,
                    'family_code' => $family->family_code,
                    'service_package' => $family->servicePackage->name ?? 'N/A',
                    'status' => $family->status,
                    'expires_at' => $family->expires_at,
                    'source' => 'Chủ sở hữu Family',
                ]);
            }
        }

        // 3. Search in FamilyMember (member_email)
        $familyMembers = FamilyMember::with(['familyAccount.servicePackage', 'customer'])
            ->where('member_email', 'like', "%{$email}%")
            ->get();

        foreach ($familyMembers as $member) {
            $results->push([
                'type' => 'family_member',
                'email' => $member->member_email,
                'customer_name' => $member->customer->name ?? $member->member_name ?? 'N/A',
                'customer_code' => $member->customer->customer_code ?? 'N/A',
                'customer_id' => $member->customer_id,
                'family_id' => $member->family_account_id,
                'family_name' => $member->familyAccount->family_name ?? 'N/A',
                'family_code' => $member->familyAccount->family_code ?? 'N/A',
                'service_package' => $member->familyAccount->servicePackage->name ?? 'N/A',
                'status' => $member->status,
                'expires_at' => $member->end_date ?? $member->expires_at,
                'source' => 'Thành viên Family',
            ]);
        }

        return $results->unique(function ($item) {
            return $item['email'] . '-' . $item['family_id'] . '-' . $item['type'];
        })->values();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get family-type service packages
        $servicePackages = ServicePackage::with('category')
            ->where(function ($query) {
                $query->where('account_type', 'Tài khoản add family')
                    ->orWhere('account_type', 'like', '%family%');
            })
            ->active()
            ->get();

        // Define account type priority
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'family_name' => 'required|string|max:255',
            'service_package_id' => 'required|exists:service_packages,id',
            'owner_email' => 'required|email|max:255',
            'owner_name' => 'nullable|string|max:255',
            'max_members' => 'required|integer|min:1',
            'expires_at' => 'required|date|after:today',
            'family_notes' => 'nullable|string',
            'internal_notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $familyAccount = FamilyAccount::create([
                'family_name' => $request->family_name,
                'service_package_id' => $request->service_package_id,
                'owner_email' => $request->owner_email,
                'owner_name' => $request->owner_name,
                'max_members' => $request->max_members,
                'current_members' => 0,
                'activated_at' => now(),
                'expires_at' => Carbon::parse($request->expires_at),
                'status' => 'active',
                'family_notes' => $request->family_notes,
                'internal_notes' => $request->internal_notes,
                'created_by' => 1,
                'managed_by' => 1,
            ]);

            DB::commit();

            return redirect()->route('admin.family-accounts.show', $familyAccount)
                ->with('success', 'Family Account đã được tạo thành công!');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi tạo Family Account: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(FamilyAccount $familyAccount)
    {
        $familyAccount->load([
            'servicePackage',
            'members.customer',
            'customerServices.customer',
            'customerServices.servicePackage'
        ]);

        // Group members by status (giữ lại để hiển thị thông tin)
        $activeMembers = $familyAccount->members->where('status', 'active');
        $inactiveMembers = $familyAccount->members->whereIn('status', ['suspended', 'removed']);

        // Get customer services info - ĐÂY MỚI LÀ SỐ SLOT THỰC SỰ
        $activeServices = $familyAccount->customerServices->where('status', 'active');
        $totalServices = $familyAccount->customerServices->count();

        // Cập nhật current_members dựa trên số CustomerService
        $familyAccount->current_members = $activeServices->count();

        return view('admin.family-accounts.show', compact(
            'familyAccount',
            'activeMembers',
            'inactiveMembers',
            'activeServices',
            'totalServices'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FamilyAccount $familyAccount)
    {
        // Get family-type service packages
        $servicePackages = ServicePackage::with('category')
            ->where(function ($query) {
                $query->where('account_type', 'Tài khoản add family')
                    ->orWhere('account_type', 'like', '%family%');
            })
            ->active()
            ->get();

        // Define account type priority
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, FamilyAccount $familyAccount)
    {
        $request->validate([
            'family_name' => 'required|string|max:255',
            'service_package_id' => 'required|exists:service_packages,id',
            'owner_email' => 'required|email|max:255',
            'owner_name' => 'nullable|string|max:255',
            'max_members' => 'required|integer|min:1',
            'expires_at' => 'required|date',
            'status' => 'required|in:active,expired,suspended,cancelled',
            'family_notes' => 'nullable|string',
            'internal_notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $familyAccount->update([
                'family_name' => $request->family_name,
                'service_package_id' => $request->service_package_id,
                'owner_email' => $request->owner_email,
                'owner_name' => $request->owner_name,
                'max_members' => $request->max_members,
                'expires_at' => Carbon::parse($request->expires_at),
                'status' => $request->status,
                'family_notes' => $request->family_notes,
                'internal_notes' => $request->internal_notes,
                'managed_by' => 1,
            ]);

            DB::commit();

            return redirect()->route('admin.family-accounts.show', $familyAccount)
                ->with('success', 'Family Account đã được cập nhật thành công!');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật Family Account: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FamilyAccount $familyAccount)
    {
        DB::beginTransaction();
        try {
            // Optional: Check if family has active members
            // Uncomment the lines below if you want to prevent deletion of families with active members
            // if ($familyAccount->members()->where('status', 'active')->exists()) {
            //     return redirect()->route('admin.family-accounts.index')
            //         ->with('error', 'Không thể xóa Family Account còn có thành viên đang hoạt động!');
            // }

            // Delete family account (cascade will automatically delete members)
            $familyAccount->delete();

            DB::commit();

            return redirect()->route('admin.family-accounts.index')
                ->with('success', 'Family Account đã được xóa thành công!');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->route('admin.family-accounts.index')
                ->with('error', 'Có lỗi xảy ra khi xóa Family Account: ' . $e->getMessage());
        }
    }

    /**
     * Show form to add member to family account
     */
    public function addMemberForm(FamilyAccount $familyAccount)
    {
        // Check if family is full
        if ($familyAccount->current_members >= $familyAccount->max_members) {
            return redirect()->route('admin.family-accounts.show', $familyAccount)
                ->with('error', 'Family Account đã đầy. Không thể thêm thành viên mới!');
        }

        // Get available customers (not already in any active family)
        $customers = Customer::whereDoesntHave('familyMemberships', function ($query) {
            $query->where('status', 'active');
        })->orderBy('name')->get();

        // Load service package information
        $familyAccount->load('servicePackage');

        return view('admin.family-accounts.add-member', compact('familyAccount', 'customers'));
    }

    /**
     * Add member to family account
     */
    public function addMember(Request $request, FamilyAccount $familyAccount)
    {
        // Validate based on member type (existing or new)
        $rules = [
            'member_type' => 'required|in:existing,new',
            'member_email' => 'required|email|max:255',
            'start_date' => 'required|date|after_or_equal:today',
            'member_notes' => 'nullable|string',
        ];

        if ($request->member_type === 'existing') {
            $rules['customer_id'] = 'required|exists:customers,id';
        } else {
            $rules['customer_name'] = 'required|string|max:255';
            $rules['customer_phone'] = 'nullable|string|max:20';
        }

        // Calculate end_date based on service package if not provided
        if (!$request->end_date && $familyAccount->servicePackage) {
            $duration = $familyAccount->servicePackage->duration_months ?? 1;
            $calculatedEndDate = Carbon::parse($request->start_date)->addMonths($duration);
        } else {
            $rules['end_date'] = 'required|date|after:start_date';
            $calculatedEndDate = Carbon::parse($request->end_date);
        }

        $request->validate($rules);

        // Check if family is full
        if ($familyAccount->current_members >= $familyAccount->max_members) {
            return redirect()->back()
                ->with('error', 'Family Account đã đầy. Không thể thêm thành viên mới!');
        }

        DB::beginTransaction();
        try {
            $customerId = null;

            if ($request->member_type === 'existing') {
                $customerId = $request->customer_id;

                // Check if customer is already in an active family
                $existingMembership = FamilyMember::where('customer_id', $customerId)
                    ->where('status', 'active')
                    ->exists();

                if ($existingMembership) {
                    return redirect()->back()
                        ->with('error', 'Khách hàng này đã là thành viên của một Family Account khác!');
                }
            } else {
                // Create new customer
                $customerCode = $this->generateCustomerCode();

                $customer = Customer::create([
                    'name' => $request->customer_name,
                    'email' => $request->member_email,
                    'phone' => $request->customer_phone,
                    'customer_code' => $customerCode,
                    'status' => 'active',
                    'created_at' => now(),
                ]);

                $customerId = $customer->id;
            }

            // Add member
            $familyMember = FamilyMember::create([
                'family_account_id' => $familyAccount->id,
                'customer_id' => $customerId,
                'member_name' => $request->member_type === 'new' ? $request->customer_name : null,
                'member_email' => $request->member_email,
                'member_role' => 'member',
                'status' => 'active',
                'member_notes' => $request->member_notes,
                'start_date' => Carbon::parse($request->start_date),
                'end_date' => $calculatedEndDate,
                'first_usage_at' => now(),
                'added_by' => null, // Set to null for now
            ]);

            // Create corresponding Customer Service
            \App\Models\CustomerService::create([
                'customer_id' => $customerId,
                'service_package_id' => $familyAccount->service_package_id,
                'login_email' => $request->member_email,
                'login_password' => null, // Family member doesn't have individual password
                'activated_at' => Carbon::parse($request->start_date),
                'expires_at' => $calculatedEndDate,
                'status' => 'active',
                'assigned_by' => 1,
                'internal_notes' => 'Dịch vụ được tạo tự động từ Family Account: ' . $familyAccount->family_name . ' (ID: ' . $familyAccount->id . '). Thành viên Family Member ID: ' . $familyMember->id,
            ]);

            // Update current members count
            $familyAccount->increment('current_members');

            DB::commit();

            $message = $request->member_type === 'new'
                ? 'Khách hàng mới và thành viên đã được tạo thành công!'
                : 'Thành viên đã được thêm vào Family Account thành công!';

            return redirect()->route('admin.family-accounts.show', $familyAccount)
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi thêm thành viên: ' . $e->getMessage());
        }
    }

    /**
     * Generate unique customer code
     */
    private function generateCustomerCode()
    {
        do {
            $code = 'FAM' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (Customer::where('customer_code', $code)->exists());

        return $code;
    }

    /**
     * Remove member from family account
     */
    public function removeMember(FamilyAccount $familyAccount, FamilyMember $member)
    {
        if ($member->family_account_id !== $familyAccount->id) {
            return redirect()->route('admin.family-accounts.show', $familyAccount)
                ->with('error', 'Thành viên không thuộc Family Account này!');
        }

        DB::beginTransaction();
        try {
            // Update member status
            $member->update([
                'status' => 'removed',
                'removed_at' => now(),
                'removed_by' => 1,
            ]);

            // Update corresponding Customer Service status
            \App\Models\CustomerService::where('customer_id', $member->customer_id)
                ->where('service_package_id', $familyAccount->service_package_id)
                ->where('login_email', $member->member_email)
                ->update([
                    'status' => 'cancelled',
                    'internal_notes' => DB::raw("CONCAT(COALESCE(internal_notes, ''), '\n[" . now()->format('d/m/Y H:i') . "] Dịch vụ bị hủy do thành viên bị xóa khỏi Family Account.')"),
                ]);

            // Update current members count
            $familyAccount->decrement('current_members');

            DB::commit();

            return redirect()->route('admin.family-accounts.show', $familyAccount)
                ->with('success', 'Thành viên đã được xóa khỏi Family Account thành công!');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->route('admin.family-accounts.show', $familyAccount)
                ->with('error', 'Có lỗi xảy ra khi xóa thành viên: ' . $e->getMessage());
        }
    }

    /**
     * Show form to edit member
     */
    public function editMemberForm(FamilyAccount $familyAccount, FamilyMember $member)
    {
        if ($member->family_account_id !== $familyAccount->id) {
            return redirect()->route('admin.family-accounts.show', $familyAccount)
                ->with('error', 'Thành viên không thuộc Family Account này!');
        }

        // Load service package information for duration calculation
        $familyAccount->load('servicePackage');

        return view('admin.family-accounts.edit-member', compact('familyAccount', 'member'));
    }

    /**
     * Update member information
     */
    public function updateMember(Request $request, FamilyAccount $familyAccount, FamilyMember $member)
    {
        $request->validate([
            'member_email' => 'required|email|max:255',
            'status' => 'required|in:active,suspended,removed',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'member_notes' => 'nullable|string',
        ]);

        if ($member->family_account_id !== $familyAccount->id) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Thành viên không thuộc Family Account này!'], 400);
            }
            return redirect()->route('admin.family-accounts.show', $familyAccount)
                ->with('error', 'Thành viên không thuộc Family Account này!');
        }

        DB::beginTransaction();
        try {
            $oldStatus = $member->status;

            // Auto calculate end_date if not provided
            $startDate = Carbon::parse($request->start_date);
            $endDate = $request->end_date ?
                Carbon::parse($request->end_date) :
                $this->calculateEndDate($familyAccount, $startDate);

            $member->update([
                'member_email' => $request->member_email,
                'status' => $request->status,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'member_notes' => $request->member_notes,
            ]);

            // Update current members count if status changed
            if ($oldStatus !== $request->status) {
                if ($oldStatus === 'active' && $request->status !== 'active') {
                    $familyAccount->decrement('current_members');
                } elseif ($oldStatus !== 'active' && $request->status === 'active') {
                    $familyAccount->increment('current_members');
                }
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Thông tin thành viên đã được cập nhật!',
                    'data' => [
                        'start_date' => $startDate->format('d/m/Y'),
                        'end_date' => $endDate->format('d/m/Y'),
                        'calculated_end_date' => !$request->end_date
                    ]
                ]);
            }

            return redirect()->route('admin.family-accounts.show', $familyAccount)
                ->with('success', 'Thông tin thành viên đã được cập nhật thành công!');
        } catch (\Exception $e) {
            DB::rollback();

            if ($request->wantsJson()) {
                return response()->json(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Calculate end date based on service package duration
     */
    private function calculateEndDate(FamilyAccount $familyAccount, Carbon $startDate)
    {
        $servicePackage = $familyAccount->servicePackage;
        $durationDays = $servicePackage->duration_days ?? 30; // Default 30 days if null

        return $startDate->copy()->addDays($durationDays);
    }

    /**
     * Generate report for family accounts
     */
    public function report()
    {
        $stats = [
            'total_families' => FamilyAccount::count(),
            'active_families' => FamilyAccount::where('status', 'active')->count(),
            'total_members' => FamilyMember::where('status', 'active')->count(),
            'expired_families' => FamilyAccount::where('status', 'expired')->count(),
            'expiring_soon' => FamilyAccount::where('status', 'active')
                ->where('expires_at', '<=', now()->addDays(7))
                ->count(),
        ];

        // Family accounts by service package
        $packageStats = FamilyAccount::join('service_packages', 'family_accounts.service_package_id', '=', 'service_packages.id')
            ->select('service_packages.name', DB::raw('count(*) as count'))
            ->groupBy('service_packages.name')
            ->orderBy('count', 'desc')
            ->get();

        // Recent activity
        $recentFamilies = FamilyAccount::with('servicePackage')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.family-accounts.report', compact('stats', 'packageStats', 'recentFamilies'));
    }
}
