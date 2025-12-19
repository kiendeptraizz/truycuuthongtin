<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\ServicePackage;
use App\Models\CustomerService;
use App\Models\Profit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomerServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CustomerService::with(['customer', 'servicePackage.category', 'familyAccount']);

        // Filter by service package (by ID or by name search)
        if ($request->filled('service_package_id')) {
            $query->where('service_package_id', $request->service_package_id);
        } elseif ($request->filled('service_package_search')) {
            // Text-based search for service package name
            $packageSearch = trim($request->service_package_search);
            $query->whereHas('servicePackage', function ($q) use ($packageSearch) {
                $q->where('name', 'like', "%{$packageSearch}%");
            });
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
                    // Lọc theo ngày hết hạn thực tế (đã qua ngày hết hạn hoặc hết hạn hôm nay)
                    // Bất kể status là gì
                    // Dịch vụ hết hạn ngày hôm nay được coi là "đã hết hạn" từ 00:00:00
                    $query->where('expires_at', '<=', now()->startOfDay());
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
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                // Tìm kiếm trong thông tin khách hàng
                $q->whereHas('customer', function ($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%")
                        ->orWhere('customer_code', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                })
                    // Tìm kiếm trong email đăng nhập dịch vụ (không phân biệt hoa thường)
                    ->orWhereRaw('LOWER(login_email) LIKE ?', ['%' . strtolower($search) . '%'])
                    // Tìm kiếm trong tên gói dịch vụ
                    ->orWhereHas('servicePackage', function ($subQ) use ($search) {
                        $subQ->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Sắp xếp dựa trên filter
        if ($request->filled('filter') && $request->filter === 'expired') {
            // Khi lọc "Đã hết hạn": sắp xếp theo ngày hết hạn giảm dần (mới nhất lên đầu)
            $customerServices = $query->orderBy('expires_at', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            // Sắp xếp mặc định: dịch vụ sắp hết hạn lên trước, sau đó mới tới created_at
            // Dịch vụ sắp hết hạn = hết hạn từ ngày mai đến 5 ngày tới
            $customerServices = $query->orderByRaw('
                CASE
                    WHEN expires_at IS NOT NULL
                    AND expires_at > CURDATE()
                    AND expires_at <= DATE_ADD(CURDATE(), INTERVAL 5 DAY)
                    THEN 0
                    ELSE 1
                END
            ')
                ->orderBy('expires_at', 'asc')
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        }

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
        // Log request data for debugging
        Log::info('CustomerService Store Request', [
            'profit_amount_original' => $request->profit_amount,
            'all_data' => $request->except(['_token', 'login_password'])
        ]);

        // Parse currency values BEFORE validation
        if ($request->filled('profit_amount')) {
            $parsedProfit = parseCurrency($request->profit_amount);
            Log::info('Parsing profit_amount', [
                'original' => $request->profit_amount,
                'parsed' => $parsedProfit
            ]);
            $request->merge([
                'profit_amount' => $parsedProfit
            ]);
        }

        try {
            $validatedData = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'service_package_id' => 'required|exists:service_packages,id',
                'login_email' => 'required|email|max:255',
                'login_password' => 'nullable|string|max:255',
                'activated_at' => 'required|date',
                'expires_at' => 'required|date|after:activated_at',
                'status' => 'required|in:active,expired,cancelled',
                'duration_days' => 'required|integer|min:1',
                'cost_price' => 'required|string',
                'price' => 'required|string',
                'internal_notes' => 'nullable|string',
                'profit_amount' => 'nullable|numeric|min:0',
                'profit_notes' => 'nullable|string|max:1000',
            ]);
            Log::info('Validation passed');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', [
                'errors' => $e->errors()
            ]);
            throw $e;
        }

        // Parse currency inputs
        $costPrice = parseCurrency($request->cost_price);
        $price = parseCurrency($request->price);

        // Tạo dịch vụ khách hàng
        $customerService = CustomerService::create([
            'customer_id' => $request->customer_id,
            'service_package_id' => $request->service_package_id,
            'assigned_by' => 1,
            'login_email' => $request->login_email,
            'login_password' => $request->login_password,
            'activated_at' => $request->activated_at,
            'expires_at' => $request->expires_at,
            'status' => $request->status,
            'duration_days' => $request->duration_days,
            'cost_price' => $costPrice,
            'price' => $price,
            'internal_notes' => $request->internal_notes,
            'family_account_id' => $request->family_account_id ?? null, // THÊM family_account_id
        ]);

        // Nếu có nhập lợi nhuận, tạo record profit
        if ($request->filled('profit_amount')) {
            // Profit amount đã được parse ở trên
            Profit::create([
                'customer_service_id' => $customerService->id,
                'profit_amount' => $request->profit_amount,
                'notes' => $request->profit_notes,
                'created_by' => 1,
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
        $customerService->load(['customer', 'servicePackage.category', 'familyAccount']);

        return view('admin.customer-services.show', compact('customerService'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CustomerService $customerService)
    {
        $customerService->load(['profit']);
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

        $suppliers = collect(); // Empty collection since we removed suppliers

        // Check if customer has active family membership
        $hasFamilyMembership = $customerService->customer->activeFamilyMembership()->exists();

        // Get available family accounts for "add team" services
        $availableFamilyAccounts = \App\Models\FamilyAccount::with(['customerServices', 'servicePackage'])
            ->where('status', 'active')
            ->get()
            ->map(function ($family) {
                $family->used_slots = $family->customerServices()->where('status', 'active')->count();
                $family->available_slots = $family->max_members - $family->used_slots;
                return $family;
            })
            ->filter(function ($family) {
                // Only show families that have available slots
                return $family->used_slots < $family->max_members;
            });

        // Get current family account if exists
        $currentFamilyMembership = $customerService->customer->activeFamilyMembership()->first();

        // Get shared credentials for shared account services
        $sharedCredentials = \App\Models\SharedAccountCredential::with(['servicePackage'])
            ->where('status', 'active')
            ->get()
            ->map(function ($cred) {
                $cred->available_slots = $cred->max_users - $cred->current_users;
                return $cred;
            })
            ->filter(function ($cred) {
                return $cred->available_slots > 0;
            });

        return view('admin.customer-services.edit', compact('customerService', 'customers', 'servicePackages', 'suppliers', 'accountTypePriority', 'hasFamilyMembership', 'availableFamilyAccounts', 'currentFamilyMembership', 'sharedCredentials'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CustomerService $customerService)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'service_package_id' => 'required|exists:service_packages,id',
            'family_account_id' => 'nullable|exists:family_accounts,id',
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
            // Parse profit_amount để xóa dấu chấm phân cách hàng nghìn
            $profitAmount = parseCurrency($request->profit_amount);

            // Kiểm tra xem đã có lợi nhuận chưa
            if ($customerService->profit) {
                // Cập nhật lợi nhuận hiện có
                $customerService->profit->update([
                    'profit_amount' => $profitAmount,
                    'notes' => $request->profit_notes,
                ]);
                $profitMessage = ' Lợi nhuận đã được cập nhật.';
            } else {
                // Tạo mới lợi nhuận
                Profit::create([
                    'customer_service_id' => $customerService->id,
                    'profit_amount' => $profitAmount,
                    'notes' => $request->profit_notes,
                    'created_by' => 1,
                ]);
                $profitMessage = ' Lợi nhuận đã được ghi nhận.';
            }
        } else {
            // Nếu không nhập profit_amount nhưng có lợi nhuận hiện tại, có thể xóa hoặc giữ nguyên
            // Ở đây tôi sẽ giữ nguyên lợi nhuận hiện tại
            $profitMessage = '';
        }

        // Xử lý family account cho dịch vụ "add family"
        $familyMessage = '';
        $servicePackage = $customerService->servicePackage;
        if (strpos($servicePackage->account_type, 'add family') !== false && $request->filled('family_account_id')) {
            \App\Models\FamilyMember::updateOrCreate(
                [
                    'family_account_id' => $request->family_account_id,
                    'customer_id' => $customerService->customer_id,
                ],
                [
                    'member_name' => $customerService->customer->name,
                    'member_email' => $request->login_email,
                    'start_date' => $request->activated_at,
                    'end_date' => $request->expires_at,
                    'status' => 'active',
                    'removed_at' => null,
                    'added_by' => 1,
                ]
            );
            $familyMessage = ' Khách hàng đã được cập nhật trong Family Account.';
        }

        // Kiểm tra source parameter để xác định redirect về đâu
        $source = $request->input('redirect_source', $request->query('source'));
        $customerId = $request->input('redirect_customer_id', $request->query('customer_id'));

        if ($source === 'customer' && $customerId) {
            // Nếu đến từ trang chi tiết khách hàng, redirect về trang đó
            return redirect()->route('admin.customers.show', $customerId)
                ->with('success', 'Thông tin dịch vụ đã được cập nhật!' . $profitMessage . $familyMessage);
        } elseif ($source === 'shared-account') {
            // Nếu đến từ trang chi tiết tài khoản dùng chung, redirect về trang đó
            return redirect()->route('admin.shared-accounts.show', urlencode($customerService->login_email))
                ->with('success', 'Thông tin dịch vụ đã được cập nhật!' . $profitMessage . $familyMessage);
        }

        // Mặc định redirect về trang danh sách dịch vụ với anchor
        return redirect(route('admin.customer-services.index') . '#service-' . $customerService->id)
            ->with('success', 'Thông tin dịch vụ đã được cập nhật!' . $profitMessage . $familyMessage);
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

        $suppliers = collect(); // Empty collection since we removed suppliers

        // Check if customer has active family membership
        $hasFamilyMembership = $customer->activeFamilyMembership()->exists();

        // Get available family accounts for "add team" services
        $availableFamilyAccounts = \App\Models\FamilyAccount::with(['customerServices', 'servicePackage'])
            ->where('status', 'active')
            ->get()
            ->map(function ($family) {
                $family->used_slots = $family->customerServices()->where('status', 'active')->count();
                $family->available_slots = $family->max_members - $family->used_slots;
                return $family;
            })
            ->filter(function ($family) {
                // Only show families that have available slots
                return $family->used_slots < $family->max_members;
            });

        // Get available shared credentials for "shared account" services
        $sharedCredentials = \App\Models\SharedAccountCredential::with(['servicePackage'])
            ->where('is_active', true)
            ->where('status', 'active')
            ->get()
            ->map(function ($cred) {
                $cred->current_users = $cred->customerServices()->where('status', 'active')->count();
                $cred->available_slots = $cred->max_users - $cred->current_users;
                return $cred;
            })
            ->filter(function ($cred) {
                // Only show credentials that have available slots
                return $cred->current_users < $cred->max_users;
            });

        return view('admin.customer-services.assign', compact('customer', 'servicePackages', 'suppliers', 'accountTypePriority', 'hasFamilyMembership', 'availableFamilyAccounts', 'sharedCredentials'));
    }

    /**
     * Assign service to customer
     */
    public function assignService(Request $request, Customer $customer)
    {
        // Log request data for debugging
        Log::info('AssignService Request', [
            'customer_id' => $customer->id,
            'profit_amount_original' => $request->profit_amount,
            'all_data' => $request->except(['_token', 'login_password'])
        ]);

        // Parse currency values BEFORE validation
        if ($request->filled('profit_amount')) {
            $parsedProfit = parseCurrency($request->profit_amount);
            Log::info('Parsing profit_amount in assignService', [
                'original' => $request->profit_amount,
                'parsed' => $parsedProfit
            ]);
            $request->merge([
                'profit_amount' => $parsedProfit
            ]);
        }

        try {
            $request->validate([
                'service_package_id' => 'required|exists:service_packages,id',
                'family_account_id' => 'nullable|exists:family_accounts,id',
                'shared_credential_id' => 'nullable|exists:shared_account_credentials,id',
                'login_email' => 'required|email|max:255',
                'login_password' => 'nullable|string|max:255',
                'activated_at' => 'required|date',
                'expires_at' => 'required|date|after:activated_at',
                'duration_days' => 'required|integer|min:1',
                'cost_price' => 'nullable|string',
                'price' => 'nullable|string',
                'internal_notes' => 'nullable|string',
                'profit_amount' => 'nullable|numeric|min:0',
                'profit_notes' => 'nullable|string|max:1000',
            ]);
            Log::info('Validation passed in assignService');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed in assignService', [
                'errors' => $e->errors()
            ]);
            throw $e;
        }

        // Parse currency inputs (default to 0 if not provided)
        $costPrice = $request->filled('cost_price') ? parseCurrency($request->cost_price) : 0;
        $price = $request->filled('price') ? parseCurrency($request->price) : 0;

        // Check if service package is "add family" type
        $servicePackage = ServicePackage::findOrFail($request->service_package_id);
        if (strpos($servicePackage->account_type, 'add family') !== false) {
            // Require family account selection for add family services
            if (!$request->filled('family_account_id')) {
                return back()->withErrors([
                    'family_account_id' => 'Vui lòng chọn Family Account để thêm khách hàng vào.'
                ])->withInput();
            }

            // Check if family account has available slots (mỗi CustomerService = 1 slot)
            $familyAccount = \App\Models\FamilyAccount::findOrFail($request->family_account_id);
            $currentServicesCount = $familyAccount->customerServices()->where('status', 'active')->count();

            if ($currentServicesCount >= $familyAccount->max_members) {
                $availableSlots = $familyAccount->max_members - $currentServicesCount;
                return back()->withErrors([
                    'family_account_id' => "Family Account này đã đầy ({$currentServicesCount}/{$familyAccount->max_members} slots). Vui lòng chọn Family Account khác."
                ])->withInput();
            }
        }

        // Check if service package is "shared account" type
        $sharedCredentialId = null;
        if ($servicePackage->account_type === 'Tài khoản dùng chung' && $request->filled('shared_credential_id')) {
            $sharedCredential = \App\Models\SharedAccountCredential::findOrFail($request->shared_credential_id);
            $currentUsersCount = $sharedCredential->customerServices()->where('status', 'active')->count();

            if ($currentUsersCount >= $sharedCredential->max_users) {
                return back()->withErrors([
                    'shared_credential_id' => "Tài khoản này đã đầy ({$currentUsersCount}/{$sharedCredential->max_users} slots). Vui lòng chọn tài khoản khác."
                ])->withInput();
            }
            $sharedCredentialId = $request->shared_credential_id;
        }

        // Tạo dịch vụ khách hàng
        $customerService = CustomerService::create([
            'customer_id' => $customer->id,
            'service_package_id' => $request->service_package_id,
            'assigned_by' => 1,
            'login_email' => $request->login_email,
            'login_password' => $request->login_password,
            'activated_at' => $request->activated_at,
            'expires_at' => $request->expires_at,
            'status' => 'active',
            'duration_days' => $request->duration_days,
            'cost_price' => $costPrice,
            'price' => $price,
            'internal_notes' => $request->internal_notes,
            'family_account_id' => $request->family_account_id ?? null,
            'shared_credential_id' => $sharedCredentialId,
        ]);

        // Nếu có nhập lợi nhuận, tạo record profit
        if ($request->filled('profit_amount')) {
            // Profit amount đã được parse ở trên trước validation
            Log::info('Creating profit record', [
                'profit_amount' => $request->profit_amount
            ]);

            \App\Models\Profit::create([
                'customer_service_id' => $customerService->id,
                'profit_amount' => $request->profit_amount,
                'notes' => $request->profit_notes,
                'created_by' => 1,
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
                    'added_by' => 1,
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

    /**
     * Hiển thị trang thống kê chi tiết dịch vụ khách hàng
     */
    public function statistics()
    {
        // Thống kê tổng quan
        $totalServices = CustomerService::count();
        $activeServices = CustomerService::where('status', 'active')->count();
        $inactiveServices = CustomerService::where('status', 'inactive')->count();
        $cancelledServices = CustomerService::where('status', 'cancelled')->count();
        $expiredByStatus = CustomerService::where('status', 'expired')->count();

        // Thống kê theo ngày hết hạn
        $now = now();
        $servicesWithExpiry = CustomerService::whereNotNull('expires_at');

        $expiredByDate = CustomerService::expiredByDate()->count();
        $validByDate = $servicesWithExpiry->clone()->where('expires_at', '>=', $now)->count();

        // Phân loại dịch vụ hết hạn theo thời gian
        $expiredCategories = [
            'today' => CustomerService::whereDate('expires_at', $now->toDateString())->count(),
            'yesterday' => CustomerService::whereDate('expires_at', $now->copy()->subDay()->toDateString())->count(),
            'last_week' => CustomerService::whereBetween('expires_at', [
                $now->copy()->subWeek()->startOfDay(),
                $now->copy()->subDay()->endOfDay()
            ])->count(),
            'last_month' => CustomerService::whereBetween('expires_at', [
                $now->copy()->subMonth()->startOfDay(),
                $now->copy()->subWeek()->endOfDay()
            ])->count(),
            'over_month' => CustomerService::where('expires_at', '<', $now->copy()->subMonth())->count()
        ];

        // Dịch vụ hết hạn cần xóa (hết hạn > 30 ngày, bất kể status)
        $expiredServicesToDelete = CustomerService::with(['customer', 'servicePackage'])
            ->where('expires_at', '<', $now->copy()->subDays(30))
            ->whereIn('status', ['expired', 'cancelled']) // Chỉ xóa expired và cancelled
            ->orderBy('expires_at')
            ->limit(100)
            ->get();

        // Thống kê theo gói dịch vụ
        $servicesByPackage = CustomerService::with('servicePackage')
            ->selectRaw('service_package_id, COUNT(*) as count')
            ->groupBy('service_package_id')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Dịch vụ sắp hết hạn (7 ngày tới)
        $expiringSoon = CustomerService::with(['customer', 'servicePackage'])
            ->where('expires_at', '>=', $now)
            ->where('expires_at', '<=', $now->copy()->addDays(7))
            ->orderBy('expires_at')
            ->get();

        // Danh sách dịch vụ đã hết hạn (50 dịch vụ gần nhất)
        $expiredServices = CustomerService::with(['customer', 'servicePackage'])
            ->where('expires_at', '<', $now)
            ->orderBy('expires_at', 'desc')
            ->limit(50)
            ->get();

        return view('admin.customer-services.statistics', compact(
            'totalServices',
            'activeServices',
            'inactiveServices',
            'cancelledServices',
            'expiredByStatus',
            'expiredByDate',
            'validByDate',
            'expiredCategories',
            'expiredServicesToDelete',
            'servicesByPackage',
            'expiringSoon',
            'expiredServices'
        ));
    }

    /**
     * Xóa các dịch vụ đã hết hạn lâu
     */
    public function deleteExpiredServices(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:30|max:365',
            'confirm' => 'required|accepted'
        ]);

        $days = $request->input('days', 30);
        $cutoffDate = now()->subDays($days);

        // Chỉ xóa các dịch vụ hết hạn > X ngày và có status = 'expired' hoặc 'cancelled'
        $deletedCount = CustomerService::where('expires_at', '<', $cutoffDate)
            ->whereIn('status', ['expired', 'cancelled'])
            ->delete();

        return redirect()->route('admin.customer-services.statistics')
            ->with('success', "Đã xóa thành công {$deletedCount} dịch vụ hết hạn trên {$days} ngày.");
    }
}
