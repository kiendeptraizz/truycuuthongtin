<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerService;
use App\Models\ServicePackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SharedAccountController extends Controller
{
    /**
     * Hiển thị danh sách tài khoản dùng chung
     */
    public function index(Request $request)
    {
        // Sử dụng query builder để tránh lỗi GROUP BY
        $query = DB::table('customer_services')
            ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
            ->select([
                'customer_services.login_email',
                DB::raw('COUNT(*) as total_services'),
                DB::raw('COUNT(DISTINCT customer_services.customer_id) as unique_customers'),
                DB::raw('MAX(customer_services.expires_at) as latest_expiry'),
                DB::raw('MIN(customer_services.expires_at) as earliest_expiry'),
                DB::raw('COUNT(CASE WHEN customer_services.status = "active" THEN 1 END) as active_count'),
                DB::raw('COUNT(CASE WHEN customer_services.expires_at < NOW() THEN 1 END) as expired_count'),
                DB::raw('COUNT(CASE WHEN customer_services.expires_at BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY) THEN 1 END) as expiring_soon_count'),
                // Thông tin tài khoản dùng chung
                DB::raw('MAX(customer_services.login_password) as shared_password'),
                DB::raw('MAX(customer_services.two_factor_code) as two_factor_code'),
                DB::raw('MAX(customer_services.password_expires_at) as password_expires_at'),
                DB::raw('MAX(customer_services.shared_account_notes) as shared_notes'),
                DB::raw('COUNT(CASE WHEN customer_services.is_password_shared = 1 THEN 1 END) as shared_count')
            ])
            ->where('service_packages.account_type', 'Team dùng chung')
            ->whereNotNull('customer_services.login_email')
            ->where('customer_services.login_email', '!=', '')
            ->groupBy('customer_services.login_email')
            ->havingRaw('COUNT(*) > 0'); // Có ít nhất 1 dịch vụ

        // Lọc theo tài khoản có vấn đề
        if ($request->filter_type === 'problematic') {
            $query->havingRaw('COUNT(DISTINCT customer_services.customer_id) > 1')
                ->orHavingRaw('COUNT(CASE WHEN customer_services.expires_at < NOW() THEN 1 END) > 0')
                ->orHavingRaw('COUNT(CASE WHEN customer_services.expires_at BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY) THEN 1 END) > 0');
        }

        // Lọc theo số lượng khách hàng
        if ($request->min_customers) {
            $query->havingRaw('COUNT(DISTINCT customer_services.customer_id) >= ?', [$request->min_customers]);
        }

        // Sắp xếp
        $sortBy = $request->get('sort_by', 'total_services');
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedSorts = ['total_services', 'unique_customers', 'latest_expiry', 'earliest_expiry', 'expired_count', 'expiring_soon_count'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $sharedAccounts = $query->paginate(20);

        // Thống kê tổng quan
        $stats = [
            'total_shared_accounts' => $sharedAccounts->total(),
            'total_users_in_shared' => DB::table('customer_services')
                ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
                ->where('service_packages.account_type', 'Team dùng chung')
                ->whereNotNull('customer_services.login_email')
                ->where('customer_services.login_email', '!=', '')
                ->count(),
            'problematic_accounts' => DB::table('customer_services')
                ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
                ->where('service_packages.account_type', 'Team dùng chung')
                ->whereNotNull('customer_services.login_email')
                ->where('customer_services.login_email', '!=', '')
                ->select('customer_services.login_email')
                ->groupBy('customer_services.login_email')
                ->havingRaw('COUNT(DISTINCT customer_services.customer_id) > 1')
                ->get()
                ->count(),
            'expiring_shared_services' => DB::table('customer_services')
                ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
                ->where('service_packages.account_type', 'Team dùng chung')
                ->whereNotNull('customer_services.login_email')
                ->where('customer_services.login_email', '!=', '')
                ->where('customer_services.expires_at', '<=', now()->addDays(7))
                ->count(),
        ];

        // Lấy danh sách gói dịch vụ để filter
        $servicePackages = ServicePackage::orderBy('name')->get();

        return view('admin.shared-accounts.index', compact('sharedAccounts', 'stats', 'servicePackages'));
    }

    /**
     * Hiển thị chi tiết tài khoản dùng chung
     */
    public function show($email, Request $request)
    {
        $services = CustomerService::with(['customer', 'servicePackage', 'assignedBy'])
            ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
            ->where('service_packages.account_type', 'Team dùng chung')
            ->where('login_email', $email)
            ->select('customer_services.*') // Chỉ lấy các cột từ customer_services
            ->orderBy('expires_at', 'asc')
            ->get();

        if ($services->isEmpty()) {
            return redirect()->route('admin.shared-accounts.index')
                ->with('error', 'Không tìm thấy dịch vụ nào với email này.');
        }

        // Thống kê cho email này
        $stats = [
            'total_services' => $services->count(),
            'unique_customers' => $services->pluck('customer_id')->unique()->count(),
            'active_services' => $services->where('status', 'active')->count(),
            'expired_services' => $services->filter(function ($service) {
                return $service->expires_at && $service->expires_at->isPast();
            })->count(),
            'expiring_soon' => $services->filter(function ($service) {
                return $service->expires_at &&
                    $service->expires_at->isFuture() &&
                    $service->expires_at->diffInDays(now()) <= 7;
            })->count(),
        ];

        return view('admin.shared-accounts.show', compact('services', 'email', 'stats'));
    }

    /**
     * Báo cáo chi tiết về tài khoản dùng chung
     */
    public function report(Request $request)
    {
        // Top 10 tài khoản dùng chung nhiều nhất
        $topSharedAccounts = DB::table('customer_services')
            ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
            ->where('service_packages.account_type', 'Team dùng chung')
            ->whereNotNull('customer_services.login_email')
            ->where('customer_services.login_email', '!=', '')
            ->select([
                'customer_services.login_email',
                DB::raw('COUNT(*) as total_services'),
                DB::raw('COUNT(DISTINCT customer_services.customer_id) as unique_customers'),
                DB::raw('COUNT(CASE WHEN customer_services.status = "active" THEN 1 END) as active_count'),
                DB::raw('COUNT(CASE WHEN customer_services.expires_at < NOW() THEN 1 END) as expired_count')
            ])
            ->groupBy('customer_services.login_email')
            ->havingRaw('COUNT(*) > 0')
            ->orderBy('total_services', 'desc')
            ->limit(10)
            ->get();

        // Tài khoản có vấn đề (nhiều khách hàng dùng chung)
        $problematicAccounts = DB::table('customer_services')
            ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
            ->join('customers', 'customer_services.customer_id', '=', 'customers.id')
            ->where('service_packages.account_type', 'Team dùng chung')
            ->whereNotNull('customer_services.login_email')
            ->where('customer_services.login_email', '!=', '')
            ->select([
                'customer_services.login_email',
                DB::raw('COUNT(*) as total_services'),
                DB::raw('COUNT(DISTINCT customer_services.customer_id) as unique_customers'),
                DB::raw('GROUP_CONCAT(DISTINCT customers.name SEPARATOR ", ") as customer_names')
            ])
            ->groupBy('customer_services.login_email')
            ->havingRaw('COUNT(DISTINCT customer_services.customer_id) > 1')
            ->orderBy('unique_customers', 'desc')
            ->limit(20)
            ->get();

        // Thống kê tổng quan
        $overallStats = [
            'total_services_with_email' => DB::table('customer_services')
                ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
                ->where('service_packages.account_type', 'Team dùng chung')
                ->whereNotNull('customer_services.login_email')
                ->where('customer_services.login_email', '!=', '')
                ->count(),
            'unique_emails' => DB::table('customer_services')
                ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
                ->where('service_packages.account_type', 'Team dùng chung')
                ->whereNotNull('customer_services.login_email')
                ->where('customer_services.login_email', '!=', '')
                ->distinct()
                ->count('customer_services.login_email'),
            'shared_emails' => DB::table('customer_services')
                ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
                ->where('service_packages.account_type', 'Team dùng chung')
                ->whereNotNull('customer_services.login_email')
                ->where('customer_services.login_email', '!=', '')
                ->select('customer_services.login_email')
                ->groupBy('customer_services.login_email')
                ->havingRaw('COUNT(*) > 0')
                ->get()
                ->count(),
            'multi_customer_emails' => DB::table('customer_services')
                ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
                ->where('service_packages.account_type', 'Team dùng chung')
                ->whereNotNull('customer_services.login_email')
                ->where('customer_services.login_email', '!=', '')
                ->select([
                    'customer_services.login_email',
                    DB::raw('COUNT(DISTINCT customer_services.customer_id) as unique_customers')
                ])
                ->groupBy('customer_services.login_email')
                ->havingRaw('COUNT(DISTINCT customer_services.customer_id) > 1')
                ->get()
                ->count(),
        ];

        return view('admin.shared-accounts.report', compact(
            'topSharedAccounts',
            'problematicAccounts',
            'overallStats'
        ));
    }

    /**
     * Hiển thị form chỉnh sửa thông tin tài khoản dùng chung
     */
    public function edit($email)
    {
        $sharedAccountInfo = CustomerService::with(['customer', 'servicePackage'])
            ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
            ->where('service_packages.account_type', 'Team dùng chung')
            ->where('customer_services.login_email', $email)
            ->select('customer_services.*')
            ->first();

        if (!$sharedAccountInfo) {
            return redirect()->route('admin.shared-accounts.index')
                ->with('error', 'Không tìm thấy tài khoản dùng chung này.');
        }

        // Lấy tất cả dịch vụ cùng email để hiển thị danh sách khách hàng
        $allServices = CustomerService::with(['customer', 'servicePackage'])
            ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
            ->where('service_packages.account_type', 'Team dùng chung')
            ->where('customer_services.login_email', $email)
            ->select('customer_services.*')
            ->get();

        return view('admin.shared-accounts.edit', compact('sharedAccountInfo', 'allServices', 'email'));
    }

    /**
     * Cập nhật thông tin tài khoản dùng chung
     */
    public function update(Request $request, $email)
    {
        $request->validate([
            'login_password' => 'nullable|string|max:255',
            'two_factor_code' => 'nullable|string|max:100',
            'recovery_codes' => 'nullable|string',
            'shared_account_notes' => 'nullable|string',
            'customer_instructions' => 'nullable|string',
            'password_expires_at' => 'nullable|date',
            'is_password_shared' => 'boolean',
        ]);

        // Cập nhật TẤT CẢ các dịch vụ cùng email (để đồng bộ thông tin)
        $services = CustomerService::join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
            ->where('service_packages.account_type', 'Team dùng chung')
            ->where('customer_services.login_email', $email)
            ->select('customer_services.*')
            ->get();

        if ($services->isEmpty()) {
            return redirect()->route('admin.shared-accounts.index')
                ->with('error', 'Không tìm thấy tài khoản dùng chung này.');
        }

        $updateData = [];

        // Chỉ cập nhật các trường có dữ liệu được gửi lên
        if ($request->filled('login_password')) {
            $updateData['login_password'] = $request->login_password;
        }

        if ($request->filled('two_factor_code')) {
            $updateData['two_factor_code'] = $request->two_factor_code;
            $updateData['two_factor_updated_at'] = now();
        }

        if ($request->filled('recovery_codes')) {
            // Chuyển đổi chuỗi thành array
            $codes = array_filter(array_map('trim', explode("\n", $request->recovery_codes)));
            $updateData['recovery_codes'] = $codes;
        }

        if ($request->filled('shared_account_notes')) {
            $updateData['shared_account_notes'] = $request->shared_account_notes;
        }

        if ($request->filled('customer_instructions')) {
            $updateData['customer_instructions'] = $request->customer_instructions;
        }

        if ($request->filled('password_expires_at')) {
            $updateData['password_expires_at'] = $request->password_expires_at;
        }

        $updateData['is_password_shared'] = $request->boolean('is_password_shared');

        // Cập nhật danh sách khách hàng đã chia sẻ
        $sharedWith = $services->pluck('customer_id')->unique()->values()->toArray();
        $updateData['shared_with_customers'] = $sharedWith;

        // Cập nhật tất cả services cùng email
        foreach ($services as $service) {
            $service->update($updateData);
        }

        return redirect()->route('admin.shared-accounts.show', $email)
            ->with('success', 'Đã cập nhật thông tin tài khoản dùng chung thành công!');
    }
}
