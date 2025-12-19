<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CustomerService;
use App\Models\ServicePackage;

echo "=== KIỂM TRA TẤT CẢ DỊCH VỤ CHATGPT PLANT ===\n\n";

// Tìm tất cả các gói có chứa "chatgpt plant" hoặc "chat gpt plant"
$plantPackages = ServicePackage::where(function ($q) {
    $q->where('name', 'like', '%chatgpt plant%')
        ->orWhere('name', 'like', '%chat gpt plant%')
        ->orWhere('name', 'like', '%CHATGPT PLANT%');
})->get();

echo "Tìm thấy " . $plantPackages->count() . " gói dịch vụ PLANT:\n";
foreach ($plantPackages as $package) {
    echo "  - {$package->name} (ID: {$package->id})\n";
}
echo "\n";

// Lấy tất cả dịch vụ active của các gói này
$allPlantServices = CustomerService::whereIn('service_package_id', $plantPackages->pluck('id'))
    ->where('status', 'active')
    ->with('customer', 'servicePackage', 'familyAccount')
    ->orderBy('family_account_id')
    ->orderBy('customer_id')
    ->get();

echo "Tổng số dịch vụ PLANT active: {$allPlantServices->count()}\n\n";

// Phân loại theo family_account_id
$inFamily30 = $allPlantServices->where('family_account_id', 30);
$inOtherFamily = $allPlantServices->whereNotNull('family_account_id')->where('family_account_id', '!=', 30);
$standalone = $allPlantServices->whereNull('family_account_id');

echo "=== PHÂN LOẠI ===\n";
echo "Trong Family #30: {$inFamily30->count()}\n";
echo "Trong Family KHÁC: {$inOtherFamily->count()}\n";
echo "Cá nhân (không có family): {$standalone->count()}\n\n";

if ($inOtherFamily->count() > 0) {
    echo "=== DỊCH VỤ PLANT TRONG FAMILY KHÁC ===\n\n";
    $groupedByFamily = $inOtherFamily->groupBy('family_account_id');
    foreach ($groupedByFamily as $familyId => $services) {
        $familyName = $services->first()->familyAccount ? $services->first()->familyAccount->family_name : "N/A";
        echo "Family #{$familyId} ({$familyName}): {$services->count()} dịch vụ\n";
        foreach ($services as $service) {
            $customerName = $service->customer ? $service->customer->name : "N/A";
            echo "  - Service #{$service->id}: {$customerName} - {$service->servicePackage->name}\n";
        }
        echo "\n";
    }
}

if ($standalone->count() > 0) {
    echo "=== DỊCH VỤ PLANT CÁ NHÂN (KHÔNG THUỘC FAMILY NÀO) ===\n\n";
    $groupedByPackage = $standalone->groupBy('servicePackage.name');
    foreach ($groupedByPackage as $packageName => $services) {
        echo "{$packageName}: {$services->count()} dịch vụ\n";
        foreach ($services as $service) {
            $customerName = $service->customer ? $service->customer->name : "N/A";
            echo "  - Service #{$service->id}: {$customerName}\n";
        }
        echo "\n";
    }
}

echo "\n=== KHUYẾN NGHỊ ===\n";
if ($inOtherFamily->count() > 0) {
    echo "⚠ Có {$inOtherFamily->count()} dịch vụ PLANT trong Family KHÁC (không phải #30)\n";
    echo "  → Cần xem xét có nên chuyển về Family #30 không\n\n";
}
if ($standalone->count() > 0) {
    echo "⚠ Có {$standalone->count()} dịch vụ PLANT cá nhân\n";
    echo "  → NÊN gán vào Family #30\n";
}
