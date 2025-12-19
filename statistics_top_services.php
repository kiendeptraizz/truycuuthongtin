<?php

/**
 * Thống kê dịch vụ tài khoản được khách hàng mua nhiều nhất
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\CustomerService;
use App\Models\ServicePackage;
use Illuminate\Support\Facades\DB;

echo "==========================================================\n";
echo "   THỐNG KÊ DỊCH VỤ TÀI KHOẢN ĐƯỢC MUA NHIỀU NHẤT\n";
echo "   Ngày: " . date('d/m/Y H:i:s') . "\n";
echo "==========================================================\n\n";

// Thống kê tổng quan
$totalServices = CustomerService::count();
$totalPackages = ServicePackage::count();
$activeServices = CustomerService::where('status', 'active')->count();

echo "📊 TỔNG QUAN:\n";
echo "   - Tổng số dịch vụ đã bán: {$totalServices}\n";
echo "   - Tổng số gói dịch vụ: {$totalPackages}\n";
echo "   - Số dịch vụ đang hoạt động: {$activeServices}\n\n";

// Thống kê top dịch vụ được mua nhiều nhất (tất cả thời gian)
echo "==========================================================\n";
echo "🏆 TOP 20 DỊCH VỤ ĐƯỢC MUA NHIỀU NHẤT (TẤT CẢ THỜI GIAN)\n";
echo "==========================================================\n\n";

$topServices = DB::table('customer_services')
    ->select(
        'service_packages.id',
        'service_packages.name as package_name',
        'service_packages.account_type',
        'service_packages.price',
        DB::raw('COUNT(customer_services.id) as total_purchases'),
        DB::raw('SUM(customer_services.price) as total_revenue'),
        DB::raw('COUNT(CASE WHEN customer_services.status = "active" THEN 1 END) as active_count'),
        DB::raw('COUNT(CASE WHEN customer_services.status = "expired" THEN 1 END) as expired_count')
    )
    ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
    ->groupBy('service_packages.id', 'service_packages.name', 'service_packages.account_type', 'service_packages.price')
    ->orderByDesc('total_purchases')
    ->limit(20)
    ->get();

$rank = 1;
foreach ($topServices as $service) {
    $revenue = number_format($service->total_revenue ?? 0, 0, ',', '.');
    $price = number_format($service->price ?? 0, 0, ',', '.');

    echo "#{$rank}. {$service->package_name}\n";
    echo "    📦 Loại: {$service->account_type}\n";
    echo "    💰 Giá: {$price} VNĐ\n";
    echo "    🛒 Số lần mua: {$service->total_purchases}\n";
    echo "    ✅ Đang hoạt động: {$service->active_count} | ⏰ Hết hạn: {$service->expired_count}\n";
    echo "    💵 Tổng doanh thu: {$revenue} VNĐ\n";
    echo "    ──────────────────────────────────────\n";
    $rank++;
}

// Thống kê theo loại tài khoản
echo "\n==========================================================\n";
echo "📈 THỐNG KÊ THEO LOẠI TÀI KHOẢN\n";
echo "==========================================================\n\n";

$byAccountType = DB::table('customer_services')
    ->select(
        'service_packages.account_type',
        DB::raw('COUNT(customer_services.id) as total_purchases'),
        DB::raw('SUM(customer_services.price) as total_revenue')
    )
    ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
    ->groupBy('service_packages.account_type')
    ->orderByDesc('total_purchases')
    ->get();

foreach ($byAccountType as $type) {
    $revenue = number_format($type->total_revenue ?? 0, 0, ',', '.');
    $accountType = $type->account_type ?? 'Không xác định';
    echo "📁 {$accountType}\n";
    echo "   - Số lần mua: {$type->total_purchases}\n";
    echo "   - Tổng doanh thu: {$revenue} VNĐ\n\n";
}

// Thống kê theo tháng (6 tháng gần nhất)
echo "==========================================================\n";
echo "📅 TOP DỊCH VỤ THEO THÁNG (6 THÁNG GẦN NHẤT)\n";
echo "==========================================================\n\n";

$sixMonthsAgo = now()->subMonths(6);

$monthlyTop = DB::table('customer_services')
    ->select(
        DB::raw('DATE_FORMAT(customer_services.created_at, "%Y-%m") as month'),
        'service_packages.name as package_name',
        DB::raw('COUNT(customer_services.id) as purchases')
    )
    ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
    ->where('customer_services.created_at', '>=', $sixMonthsAgo)
    ->groupBy(DB::raw('DATE_FORMAT(customer_services.created_at, "%Y-%m")'), 'service_packages.name')
    ->orderBy('month', 'desc')
    ->orderByDesc('purchases')
    ->get();

// Nhóm theo tháng và lấy top 3 mỗi tháng
$grouped = $monthlyTop->groupBy('month');

foreach ($grouped as $month => $services) {
    $topThree = $services->take(3);
    echo "📆 Tháng {$month}:\n";
    $i = 1;
    foreach ($topThree as $s) {
        echo "   {$i}. {$s->package_name} - {$s->purchases} lần mua\n";
        $i++;
    }
    echo "\n";
}

// Thống kê khách hàng mua nhiều nhất
echo "==========================================================\n";
echo "👤 TOP 10 KHÁCH HÀNG MUA NHIỀU NHẤT\n";
echo "==========================================================\n\n";

$topCustomers = DB::table('customer_services')
    ->select(
        'customers.id',
        'customers.name as customer_name',
        'customers.phone',
        DB::raw('COUNT(customer_services.id) as total_services'),
        DB::raw('SUM(customer_services.price) as total_spent')
    )
    ->join('customers', 'customer_services.customer_id', '=', 'customers.id')
    ->groupBy('customers.id', 'customers.name', 'customers.phone')
    ->orderByDesc('total_services')
    ->limit(10)
    ->get();

$rank = 1;
foreach ($topCustomers as $customer) {
    $spent = number_format($customer->total_spent ?? 0, 0, ',', '.');
    echo "#{$rank}. {$customer->customer_name}\n";
    echo "    📱 SĐT: {$customer->phone}\n";
    echo "    🛒 Số dịch vụ đã mua: {$customer->total_services}\n";
    echo "    💰 Tổng chi tiêu: {$spent} VNĐ\n";
    echo "    ──────────────────────────────────────\n";
    $rank++;
}

// Tổng doanh thu
$totalRevenue = CustomerService::sum('price');
$totalRevenueFormatted = number_format($totalRevenue ?? 0, 0, ',', '.');

echo "\n==========================================================\n";
echo "💰 TỔNG DOANH THU: {$totalRevenueFormatted} VNĐ\n";
echo "==========================================================\n";

echo "\n✅ Hoàn thành thống kê!\n";
