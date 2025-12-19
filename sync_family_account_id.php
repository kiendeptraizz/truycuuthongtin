<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CustomerService;
use App\Models\FamilyMember;
use Illuminate\Support\Facades\DB;

echo "=== ĐỒNG BỘ family_account_id TỪ FamilyMember SANG CustomerService ===\n\n";

// Tìm tất cả CustomerService không có family_account_id
$servicesWithoutFamily = CustomerService::whereNull('family_account_id')
    ->whereHas('servicePackage', function ($q) {
        $q->where('account_type', 'like', '%family%');
    })
    ->with('customer', 'servicePackage')
    ->get();

echo "Tìm thấy {$servicesWithoutFamily->count()} dịch vụ không có family_account_id\n";
echo "Đang kiểm tra và đồng bộ...\n\n";

$updated = 0;
$notFound = 0;
$errors = 0;

foreach ($servicesWithoutFamily as $service) {
    try {
        // Tìm FamilyMember tương ứng với customer này
        $familyMember = FamilyMember::where('customer_id', $service->customer_id)
            ->where('status', 'active')
            ->first();

        if ($familyMember) {
            // Cập nhật family_account_id vào CustomerService
            $service->family_account_id = $familyMember->family_account_id;
            $service->save();

            $customerName = $service->customer ? $service->customer->name : "N/A";
            $packageName = $service->servicePackage->name;

            echo "✓ Updated Service #{$service->id}: {$customerName} - {$packageName} -> Family #{$familyMember->family_account_id}\n";
            $updated++;
        } else {
            $customerName = $service->customer ? $service->customer->name : "N/A";
            echo "⚠ Service #{$service->id}: {$customerName} - Không tìm thấy FamilyMember tương ứng\n";
            $notFound++;
        }
    } catch (\Exception $e) {
        echo "✗ Error Service #{$service->id}: {$e->getMessage()}\n";
        $errors++;
    }
}

echo "\n=== KẾT QUẢ ===\n";
echo "Đã cập nhật: {$updated}\n";
echo "Không tìm thấy FamilyMember: {$notFound}\n";
echo "Lỗi: {$errors}\n";

// Kiểm tra lại sau khi sync
echo "\n=== KIỂM TRA LẠI ===\n";
$chatgptPlantPackage = \App\Models\ServicePackage::where('name', 'like', '%CHATGPT PLANT 1TH%')->first();
if ($chatgptPlantPackage) {
    $activeServices = CustomerService::where('service_package_id', $chatgptPlantPackage->id)
        ->where('status', 'active')
        ->get();

    $withFamily = $activeServices->whereNotNull('family_account_id')->count();
    $withoutFamily = $activeServices->whereNull('family_account_id')->count();

    echo "CHATGPT PLANT 1TH active:\n";
    echo "- Có family_account_id: {$withFamily}\n";
    echo "- Không có family_account_id: {$withoutFamily}\n";
}

echo "\n✓ Hoàn tất!\n";
