<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CustomerService;
use App\Models\ServicePackage;
use App\Models\FamilyAccount;

echo "=== KIỂM TRA TẤT CẢ DỊCH VỤ CHATGPT PLANT 1TH ===\n\n";

// Tìm gói dịch vụ CHATGPT PLANT 1TH
$package = ServicePackage::where('name', 'like', '%CHATGPT PLANT 1TH%')->first();

if (!$package) {
    echo "Không tìm thấy gói CHATGPT PLANT 1TH\n";
    exit;
}

echo "Gói dịch vụ: {$package->name} (ID: {$package->id})\n";
echo "Loại tài khoản: {$package->account_type}\n\n";

// Lấy TẤT CẢ dịch vụ của gói này
$allServices = CustomerService::where('service_package_id', $package->id)
    ->where('status', 'active')
    ->with('customer', 'familyAccount')
    ->orderBy('customer_id')
    ->get();

echo "=== TẤT CẢ DỊCH VỤ ACTIVE CỦA GÓI NÀY ===\n";
echo "Tổng số: {$allServices->count()}\n\n";

$servicesInFamily = $allServices->filter(function ($service) {
    return $service->family_account_id !== null;
});

$servicesNotInFamily = $allServices->filter(function ($service) {
    return $service->family_account_id === null;
});

echo "Dịch vụ TRONG family: {$servicesInFamily->count()}\n";
echo "Dịch vụ KHÔNG trong family (cá nhân): {$servicesNotInFamily->count()}\n\n";

echo "=== CHI TIẾT DỊCH VỤ TRONG FAMILY ===\n";
$familyGroups = $servicesInFamily->groupBy('family_account_id');

foreach ($familyGroups as $familyId => $services) {
    $family = FamilyAccount::find($familyId);
    $familyName = $family ? $family->family_name : "Đã xóa";
    $familyCode = $family ? $family->family_code : "N/A";

    echo "\n🏠 Family: {$familyName} ({$familyCode}) [ID: {$familyId}]\n";
    echo "   Số dịch vụ active: {$services->count()}\n";

    if ($family) {
        echo "   Max slots: {$family->max_members}\n";
        echo "   Current slots (từ DB): {$family->current_members}\n";
    }

    echo "   Danh sách:\n";
    foreach ($services as $service) {
        $customerName = $service->customer ? $service->customer->name : "Đã xóa";
        echo "   - Service #{$service->id}: {$customerName} | Expires: {$service->expires_at->format('d/m/Y')}\n";
    }
}

echo "\n\n=== CHI TIẾT DỊCH VỤ CÁ NHÂN (KHÔNG TRONG FAMILY) ===\n";
echo "Tổng số: {$servicesNotInFamily->count()}\n";

// Nhóm theo khách hàng
$customerGroups = $servicesNotInFamily->groupBy('customer_id');
echo "Số khách hàng: {$customerGroups->count()}\n\n";

echo "Top 10 khách hàng có nhiều dịch vụ cá nhân nhất:\n";
$topCustomers = $customerGroups->sortByDesc(function ($services) {
    return $services->count();
})->take(10);

foreach ($topCustomers as $customerId => $services) {
    $customerName = $services->first()->customer ? $services->first()->customer->name : "Đã xóa";
    echo "- {$customerName}: {$services->count()} dịch vụ\n";
}

echo "\n=== TỔNG KẾT ===\n";
echo "Tổng dịch vụ CHATGPT PLANT 1TH active: {$allServices->count()}\n";
echo "Trong family: {$servicesInFamily->count()} dịch vụ (" . round($servicesInFamily->count() / $allServices->count() * 100, 1) . "%)\n";
echo "Cá nhân: {$servicesNotInFamily->count()} dịch vụ (" . round($servicesNotInFamily->count() / $allServices->count() * 100, 1) . "%)\n";

// Tính tổng slots đang dùng trong tất cả family
$totalSlotsUsed = $servicesInFamily->count();
echo "\n📊 TỔNG SLOTS ĐANG DÙNG TRONG TẤT CẢ FAMILY: {$totalSlotsUsed}\n";
