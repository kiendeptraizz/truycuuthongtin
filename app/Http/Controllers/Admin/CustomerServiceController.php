<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\ServicePackage;
use App\Models\CustomerService;
use App\Models\Profit;
use Illuminate\Http\Request;

class CustomerServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CustomerService::with(['customer', 'servicePackage.category']);

        // Filter by service package
        if ($request->filled('service_package_id')) {
            $query->where('service_package_id', $request->service_package_id);
        }

        // Filter by service category
        if ($request->filled('category_id')) {
            $query->whereHas('servicePackage', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        // Filter by date range (activated_at)
        if ($request->filled('date_from')) {
            $query->whereDate('activated_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('activated_at', '<=', $request->date_to);
        }

        // Filter by expiry date range
        if ($request->filled('expiry_from')) {
            $query->whereDate('expires_at', '>=', $request->expiry_from);
        }

        if ($request->filled('expiry_to')) {
            $query->whereDate('expires_at', '<=', $request->expiry_to);
        }

        // Filter by status
        if ($request->filled('filter')) {
            switch ($request->filter) {
                case 'expiring':
                    $query->expiringSoon();
                    break;
                case 'expired':
                    $query->expired();
                    break;
                case 'active':
                    $query->active();
                    break;
                case 'expiring-not-reminded':
                    $query->expiringSoonNotReminded();
                    break;
                case 'reminded':
                    $query->reminded();
                    break;
                case 'activated-today':
                    $query->whereDate('activated_at', today());
                    break;
                case 'activated-yesterday':
                    $query->whereDate('activated_at', today()->subDay());
                    break;
                case 'activated-this-week':
                    $query->whereBetween('activated_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ]);
                    break;
                case 'activated-this-month':
                    $query->whereMonth('activated_at', now()->month)
                        ->whereYear('activated_at', now()->year);
                    break;
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('customer_code', 'like', "%{$search}%");
            })->orWhereHas('servicePackage', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Sắp xếp: dịch vụ sắp hết hạn lên trước, sau đó mới tới created_at
        $customerServices = $query->orderByRaw('
            CASE 
                WHEN expires_at IS NOT NULL AND expires_at <= DATE_ADD(NOW(), INTERVAL 5 DAY) AND expires_at > NOW() 
                THEN 0 
                ELSE 1 
            END
        ')
            ->orderBy('expires_at', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Get data for filter dropdowns
        $servicePackages = ServicePackage::with('category')->orderBy('name')->get();
        $categories = \App\Models\ServiceCategory::orderBy('name')->get();

        // Thống kê nhanh cho dịch vụ kích hoạt hôm nay
        $todayStats = null;
        if ($request->filter === 'activated-today') {
            $todayActivated = CustomerService::with(['customer', 'servicePackage'])
                ->whereDate('activated_at', today())
                ->get();

            $todayStats = [
                'total_services' => $todayActivated->count(),
                'unique_customers' => $todayActivated->pluck('customer_id')->unique()->count(),
                'revenue_estimate' => $todayActivated->sum(function ($service) {
                    return $service->servicePackage->price ?? 0;
                }),
                'top_packages' => $todayActivated->groupBy('servicePackage.name')
                    ->map(function ($group) {
                        return $group->count();
                    })->sortDesc()->take(3)
            ];
        }

        return view('admin.customer-services.index', compact(
            'customerServices',
            'servicePackages',
            'categories',
            'todayStats'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::orderBy('name')->get();

        // Group service packages by account_type with priority order
        $servicePackages = ServicePackage::with('category')->active()->get();

        // Define account type priority and styling
        $accountTypePriority = [
            'Tài khoản dùng chung' => 1,
            'Tài khoản chính chủ' => 2,
            'Tài khoản add family' => 3,
            'Tài khoản cấp (dùng riêng)' => 4,
        ];

        // Sort packages by account type priority, then by name
        $servicePackages = $servicePackages->sortBy(function ($package) use ($accountTypePriority) {
            $priority = $accountTypePriority[$package->account_type] ?? 999;
            return [$priority, $package->name];
        });

        return view('admin.customer-services.create', compact('customers', 'servicePackages', 'accountTypePriority'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'service_package_id' => 'required|exists:service_packages,id',
            'login_email' => 'required|email|max:255',
            'login_password' => 'nullable|string|max:255',
            'activated_at' => 'required|date',
            'expires_at' => 'required|date|after:activated_at',
            'status' => 'required|in:active,expired,cancelled',
            'internal_notes' => 'nullable|string',
            'profit_amount' => 'nullable|numeric|min:0',
            'profit_notes' => 'nullable|string|max:1000',
        ]);

        // Tạo dịch vụ khách hàng
        $customerService = CustomerService::create([
            'customer_id' => $request->customer_id,
            'service_package_id' => $request->service_package_id,
            'assigned_by' => auth('admin')->id(),
            'login_email' => $request->login_email,
            'login_password' => $request->login_password,
            'activated_at' => $request->activated_at,
            'expires_at' => $request->expires_at,
            'status' => $request->status,
            'internal_notes' => $request->internal_notes,
        ]);

        // Nếu có nhập lợi nhuận, tạo record profit
        if ($request->filled('profit_amount')) {
            Profit::create([
                'customer_service_id' => $customerService->id,
                'profit_amount' => $request->profit_amount,
                'notes' => $request->profit_notes,
                'created_by' => auth('admin')->id(),
            ]);
        }

        return redirect(route('admin.customer-services.index') . '#service-' . $customerService->id)
            ->with('success', 'Dịch vụ đã được gán thành công!' .
                ($request->filled('profit_amount') ? ' Lợi nhuận đã được ghi nhận.' : ''));
    }

    /**
     * Display the specified resource.
     */
    public function show(CustomerService $customerService)
    {
        $customerService->load(['customer', 'servicePackage.category', 'supplier', 'supplierService']);

        return view('admin.customer-services.show', compact('customerService'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CustomerService $customerService)
    {
        $customerService->load(['supplier', 'supplierService', 'profit']);
        $customers = Customer::orderBy('name')->get();

        // Group service packages by account_type with priority order
        $servicePackages = ServicePackage::with('category')->active()->get();

        // Define account type priority and styling
        $accountTypePriority = [
            'Tài khoản dùng chung' => 1,
            'Tài khoản chính chủ' => 2,
            'Tài khoản add family' => 3,
            'Tài khoản cấp (dùng riêng)' => 4,
        ];

        // Sort packages by account type priority, then by name
        $servicePackages = $servicePackages->sortBy(function ($package) use ($accountTypePriority) {
            $priority = $accountTypePriority[$package->account_type] ?? 999;
            return [$priority, $package->name];
        });

        $suppliers = \App\Models\Supplier::with('products')->orderBy('supplier_name')->get();

        return view('admin.customer-services.edit', compact('customerService', 'customers', 'servicePackages', 'suppliers', 'accountTypePriority'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CustomerService $customerService)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'service_package_id' => 'required|exists:service_packages,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'supplier_service_id' => 'nullable|exists:supplier_products,id',
            'login_email' => 'required|email|max:255',
            'login_password' => 'nullable|string|max:255',
            'activated_at' => 'required|date',
            'expires_at' => 'required|date|after:activated_at',
            'status' => 'required|in:active,expired,cancelled',
            'internal_notes' => 'nullable|string',
            'profit_amount' => 'nullable|numeric|min:0',
            'profit_notes' => 'nullable|string|max:1000',
        ]);

        $customerService->update($request->except(['profit_amount', 'profit_notes']));

        // Xử lý lợi nhuận
        if ($request->filled('profit_amount')) {
            // Kiểm tra xem đã có lợi nhuận chưa
            if ($customerService->profit) {
                // Cập nhật lợi nhuận hiện có
                $customerService->profit->update([
                    'profit_amount' => $request->profit_amount,
                    'notes' => $request->profit_notes,
                ]);
                $profitMessage = ' Lợi nhuận đã được cập nhật.';
            } else {
                // Tạo mới lợi nhuận
                Profit::create([
                    'customer_service_id' => $customerService->id,
                    'profit_amount' => $request->profit_amount,
                    'notes' => $request->profit_notes,
                    'created_by' => auth('admin')->id(),
                ]);
                $profitMessage = ' Lợi nhuận đã được ghi nhận.';
            }
        } else {
            // Nếu không nhập profit_amount nhưng có lợi nhuận hiện tại, có thể xóa hoặc giữ nguyên
            // Ở đây tôi sẽ giữ nguyên lợi nhuận hiện tại
            $profitMessage = '';
        }

        // Kiểm tra source parameter để xác định redirect về đâu
        $source = $request->query('source');
        $customerId = $request->query('customer_id');

        if ($source === 'customer' && $customerId) {
            // Nếu đến từ trang chi tiết khách hàng, redirect về trang đó
            return redirect()->route('admin.customers.show', $customerId)
                ->with('success', 'Thông tin dịch vụ đã được cập nhật!' . $profitMessage);
        } elseif ($source === 'shared-account') {
            // Nếu đến từ trang chi tiết tài khoản dùng chung, redirect về trang đó
            return redirect()->route('admin.shared-accounts.show', urlencode($customerService->login_email))
                ->with('success', 'Thông tin dịch vụ đã được cập nhật!' . $profitMessage);
        }

        // Mặc định redirect về trang danh sách dịch vụ với anchor
        return redirect(route('admin.customer-services.index') . '#service-' . $customerService->id)
            ->with('success', 'Thông tin dịch vụ đã được cập nhật!' . $profitMessage);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomerService $customerService)
    {
        $customerId = $customerService->customer_id;
        $loginEmail = $customerService->login_email;
        $customerService->delete();

        // Kiểm tra referer để xác định redirect về đâu
        $referer = request()->headers->get('referer');

        if ($referer && strpos($referer, '/admin/customers/') !== false) {
            // Nếu đến từ trang chi tiết khách hàng, redirect về trang đó
            return redirect()->route('admin.customers.show', $customerId)
                ->with('success', 'Dịch vụ đã được xóa!');
        } elseif ($referer && strpos($referer, '/admin/shared-accounts/') !== false) {
            // Nếu đến từ trang chi tiết tài khoản dùng chung, redirect về trang đó
            return redirect()->route('admin.shared-accounts.show', urlencode($loginEmail))
                ->with('success', 'Dịch vụ đã được xóa!');
        }

        // Mặc định redirect về trang danh sách dịch vụ
        return redirect()->route('admin.customer-services.index')
            ->with('success', 'Dịch vụ đã được xóa!');
    }

    /**
     * Show form to assign service to customer
     */
    public function assignForm(Customer $customer)
    {
        // Group service packages by account_type with priority order
        $servicePackages = ServicePackage::with('category')->active()->get();

        // Define account type priority and styling
        $accountTypePriority = [
            'Tài khoản dùng chung' => 1,
            'Tài khoản chính chủ' => 2,
            'Tài khoản add family' => 3,
            'Tài khoản cấp (dùng riêng)' => 4,
        ];

        // Sort packages by account type priority, then by name
        $servicePackages = $servicePackages->sortBy(function ($package) use ($accountTypePriority) {
            $priority = $accountTypePriority[$package->account_type] ?? 999;
            return [$priority, $package->name];
        });

        $suppliers = \App\Models\Supplier::with('products')->orderBy('supplier_name')->get();

        // Check if customer has active family membership
        $hasFamilyMembership = $customer->activeFamilyMembership()->exists();

        // Get available family accounts for "add team" services
        $availableFamilyAccounts = \App\Models\FamilyAccount::with(['members'])
            ->where('status', 'active')
            ->get()
            ->map(function ($family) {
                $family->family_members_count = $family->members()->where('status', 'active')->count();
                return $family;
            })
            ->filter(function ($family) {
                // Only show families that have available slots
                return $family->family_members_count < $family->max_members;
            });

        return view('admin.customer-services.assign', compact('customer', 'servicePackages', 'suppliers', 'accountTypePriority', 'hasFamilyMembership', 'availableFamilyAccounts'));
    }

    /**
     * Assign service to customer
     */
    public function assignService(Request $request, Customer $customer)
    {
        $request->validate([
            'service_package_id' => 'required|exists:service_packages,id',
            'family_account_id' => 'nullable|exists:family_accounts,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'supplier_service_id' => 'nullable|exists:supplier_products,id',
            'login_email' => 'required|email|max:255',
            'login_password' => 'nullable|string|max:255',
            'activated_at' => 'required|date',
            'expires_at' => 'required|date|after:activated_at',
            'internal_notes' => 'nullable|string',
            'profit_amount' => 'nullable|numeric|min:0',
            'profit_notes' => 'nullable|string|max:1000',
        ]);

        // Check if service package is "add family" type
        $servicePackage = ServicePackage::findOrFail($request->service_package_id);
        if (strpos($servicePackage->account_type, 'add family') !== false) {
            // Require family account selection for add family services
            if (!$request->filled('family_account_id')) {
                return back()->withErrors([
                    'family_account_id' => 'Vui lòng chọn Family Account để thêm khách hàng vào.'
                ])->withInput();
            }

            // Check if family account has available slots
            $familyAccount = \App\Models\FamilyAccount::findOrFail($request->family_account_id);
            $currentMembersCount = $familyAccount->members()->where('status', 'active')->count();

            if ($currentMembersCount >= $familyAccount->max_members) {
                return back()->withErrors([
                    'family_account_id' => 'Family Account này đã đầy. Vui lòng chọn Family Account khác.'
                ])->withInput();
            }
        }

        // Tạo dịch vụ khách hàng
        $customerService = CustomerService::create([
            'customer_id' => $customer->id,
            'service_package_id' => $request->service_package_id,
            'assigned_by' => auth('admin')->id(),
            'supplier_id' => $request->supplier_id,
            'supplier_service_id' => $request->supplier_service_id,
            'login_email' => $request->login_email,
            'login_password' => $request->login_password,
            'activated_at' => $request->activated_at,
            'expires_at' => $request->expires_at,
            'status' => 'active',
            'internal_notes' => $request->internal_notes,
        ]);

        // Nếu có nhập lợi nhuận, tạo record profit
        if ($request->filled('profit_amount')) {
            \App\Models\Profit::create([
                'customer_service_id' => $customerService->id,
                'profit_amount' => $request->profit_amount,
                'notes' => $request->profit_notes,
                'created_by' => auth('admin')->id(),
            ]);
        }

        // Nếu là dịch vụ "add family", thêm khách hàng vào family account
        if (strpos($servicePackage->account_type, 'add family') !== false && $request->filled('family_account_id')) {
            \App\Models\FamilyMember::updateOrCreate(
                [
                    'family_account_id' => $request->family_account_id,
                    'customer_id' => $customer->id,
                ],
                [
                    'member_name' => $customer->name,
                    'member_email' => $request->login_email,
                    'start_date' => $request->activated_at, // Thêm ngày bắt đầu
                    'end_date' => $request->expires_at,     // Thêm ngày kết thúc
                    'status' => 'active', // Kích hoạt lại nếu thành viên đã bị xóa
                    'removed_at' => null, // Xóa dấu vết bị xóa
                    'added_by' => auth('admin')->id(),
                ]
            );
        }

        return redirect()->route('admin.customers.show', $customer)
            ->with('success', 'Dịch vụ đã được gán cho khách hàng thành công!' .
                ($request->filled('profit_amount') ? ' Lợi nhuận đã được ghi nhận.' : '') .
                (strpos($servicePackage->account_type, 'add family') !== false ? ' Khách hàng đã được thêm vào Family Account.' : ''));
    }

    /**
     * Đánh dấu đã nhắc nhở khách hàng
     */
    public function markReminded(Request $request, CustomerService $customerService)
    {
        $request->validate([
            'notes' => 'nullable|string|max:255'
        ]);

        $notes = $request->notes ?: 'Đánh dấu từ giao diện web';
        $customerService->markAsReminded($notes);

        return response()->json([
            'success' => true,
            'message' => 'Đã đánh dấu nhắc nhở thành công!',
            'reminder_count' => $customerService->reminder_count,
            'reminder_sent_at' => $customerService->reminder_sent_at->format('d/m H:i'),
            'needs_reminder_again' => $customerService->needsReminderAgain()
        ]);
    }

    /**
     * Reset trạng thái nhắc nhở
     */
    public function resetReminder(CustomerService $customerService)
    {
        $customerService->resetReminderStatus();

        return response()->json([
            'success' => true,
            'message' => 'Đã reset trạng thái nhắc nhở!'
        ]);
    }

    /**
     * Lấy báo cáo nhắc nhở
     */
    public function reminderReport(Request $request)
    {
        $days = $request->get('days', 5);

        $expiringSoon = CustomerService::expiringSoon($days)->count();
        $reminded = CustomerService::expiringSoon($days)->where('reminder_sent', true)->count();
        $notReminded = CustomerService::expiringSoon($days)->where('reminder_sent', false)->count();

        $services = CustomerService::with(['customer', 'servicePackage'])
            ->expiringSoon($days)
            ->orderBy('expires_at', 'asc') // Gần hết hạn nhất lên trước
            ->orderBy('reminder_sent', 'asc') // Chưa nhắc lên trước
            ->get();

        return view('admin.customer-services.reminder-report', compact(
            'services',
            'expiringSoon',
            'reminded',
            'notReminded',
            'days'
        ));
    }

    /**
     * Báo cáo thống kê hàng ngày
     */
    public function dailyReport(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));
        $selectedDate = \Carbon\Carbon::parse($date);

        // Thống kê dịch vụ kích hoạt trong ngày
        $activatedToday = CustomerService::with(['customer', 'servicePackage'])
            ->whereDate('activated_at', $selectedDate)
            ->get();

        // Thống kê dịch vụ hết hạn trong ngày
        $expiredToday = CustomerService::with(['customer', 'servicePackage'])
            ->whereDate('expires_at', $selectedDate)
            ->get();

        // Thống kê dịch vụ sắp hết hạn trong 5 ngày từ ngày đã chọn
        $expiringSoon = CustomerService::with(['customer', 'servicePackage'])
            ->whereBetween('expires_at', [
                $selectedDate->copy(),
                $selectedDate->copy()->addDays(5)
            ])
            ->get();

        // Thống kê tổng hợp
        $stats = [
            'activated' => [
                'total_services' => $activatedToday->count(),
                'unique_customers' => $activatedToday->pluck('customer_id')->unique()->count(),
                'revenue_estimate' => $activatedToday->sum(function ($service) {
                    return $service->servicePackage->price ?? 0;
                }),
                'by_package' => $activatedToday->groupBy('servicePackage.name')
                    ->map(function ($group) {
                        return [
                            'count' => $group->count(),
                            'revenue' => $group->sum(function ($service) {
                                return $service->servicePackage->price ?? 0;
                            })
                        ];
                    })->sortByDesc('count')
            ],
            'expired' => [
                'total_services' => $expiredToday->count(),
                'unique_customers' => $expiredToday->pluck('customer_id')->unique()->count(),
                'by_package' => $expiredToday->groupBy('servicePackage.name')
                    ->map(function ($group) {
                        return $group->count();
                    })->sortByDesc(function ($count) {
                        return $count;
                    })
            ],
            'expiring_soon' => [
                'total_services' => $expiringSoon->count(),
                'reminded' => $expiringSoon->where('reminder_sent', true)->count(),
                'not_reminded' => $expiringSoon->where('reminder_sent', false)->count(),
            ]
        ];

        return view('admin.customer-services.daily-report', compact(
            'selectedDate',
            'activatedToday',
            'expiredToday',
            'expiringSoon',
            'stats'
        ));
    }
}
