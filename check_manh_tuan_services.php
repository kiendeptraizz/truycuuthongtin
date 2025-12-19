<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Customer;
use App\Models\CustomerService;

echo "=== Kiểm tra tất cả dịch vụ của khách hàng Mạnh Tuấn ===\n\n";

// Tìm khách hàng Mạnh Tuấn
$customer = Customer::where('name', 'like', '%Mạnh Tuấn%')
    ->orWhere('name', 'like', '%MạNh TuấN%')
    ->first();

if (!$customer) {
    echo "Không tìm thấy khách hàng Mạnh Tuấn\n";
    exit;
}

echo "Khách hàng: {$customer->name} (ID: {$customer->id})\n";
echo "Email: {$customer->email}\n";
echo "Phone: {$customer->phone}\n\n";

// Lấy tất cả dịch vụ của khách hàng này
$allServices = CustomerService::where('customer_id', $customer->id)
    ->with('servicePackage', 'familyAccount')
    ->orderBy('created_at', 'desc')
    ->get();

echo "=== TẤT CẢ DỊCH VỤ CỦA KHÁCH HÀNG NÀY ===\n";
echo "Tổng số: {$allServices->count()}\n\n";

$servicesInFamily = 0;
$servicesInFamily30 = collect();

foreach ($allServices as $service) {
    $package = $service->servicePackage ? $service->servicePackage->name : "N/A";
    $familyInfo = "";

    if ($service->family_account_id) {
        $servicesInFamily++;
        $familyName = $service->familyAccount ? $service->familyAccount->family_name : "Đã xóa";
        $familyCode = $service->familyAccount ? $service->familyAccount->family_code : "N/A";
        $familyInfo = " | Family: {$familyName} ({$familyCode}) [ID: {$service->family_account_id}]";

        if ($service->family_account_id == 30) {
            $servicesInFamily30->push($service);
        }
    }

    $statusIcon = $service->status == 'active' ? '✓' : '✗';

    echo "  [{$statusIcon}] Service #{$service->id}: {$package} - {$service->status}";
    echo $familyInfo;
    echo " | Expires: " . ($service->expires_at ? $service->expires_at->format('d/m/Y') : 'N/A');
    echo "\n";
}

echo "\n=== TỔNG KẾT ===\n";
echo "Tổng số dịch vụ: {$allServices->count()}\n";
echo "Dịch vụ active: " . $allServices->where('status', 'active')->count() . "\n";
echo "Dịch vụ trong Family (bất kỳ): {$servicesInFamily}\n";
echo "Dịch vụ trong Family #30 (Chat GPT Plant): {$servicesInFamily30->count()}\n";
echo "  - Active: " . $servicesInFamily30->where('status', 'active')->count() . "\n";
echo "  - Expired: " . $servicesInFamily30->where('status', 'expired')->count() . "\n";

if ($servicesInFamily30->count() > 0) {
    echo "\nChi tiết dịch vụ trong Family #30:\n";
    foreach ($servicesInFamily30 as $service) {
        echo "  - Service #{$service->id}: {$service->servicePackage->name} - {$service->status} - Expires: {$service->expires_at->format('d/m/Y')}\n";
    }
}
