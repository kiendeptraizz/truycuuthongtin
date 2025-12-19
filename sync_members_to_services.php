<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\FamilyMember;
use App\Models\CustomerService;
use App\Models\ServicePackage;

echo "=== ĐỒNG BỘ DỊCH VỤ CHATGPT PLANT CHO FAMILYMEMBERS TRONG FAMILY #30 ===\n\n";

// Lấy gói CHATGPT PLANT 1TH
$chatgptPackage = ServicePackage::where('name', 'CHATGPT PLANT 1TH')->first();

if (!$chatgptPackage) {
    echo "Không tìm thấy gói CHATGPT PLANT 1TH\n";
    exit;
}

// Lấy tất cả FamilyMembers trong family #30
$familyMembers = FamilyMember::where('family_account_id', 30)
    ->where('status', 'active')
    ->with('customer')
    ->get();

$updated = 0;
$skipped = 0;

foreach ($familyMembers as $member) {
    $customerName = $member->customer ? $member->customer->name : "Đã xóa";

    // Tìm dịch vụ CHATGPT PLANT 1TH CÁ NHÂN của customer này
    $services = CustomerService::where('customer_id', $member->customer_id)
        ->where('service_package_id', $chatgptPackage->id)
        ->where('status', 'active')
        ->whereNull('family_account_id')
        ->get();

    if ($services->count() > 0) {
        echo "\n{$customerName}: Tìm thấy {$services->count()} dịch vụ CHATGPT PLANT cá nhân\n";

        foreach ($services as $service) {
            echo "  ✓ Gán Service #{$service->id} vào Family #30\n";
            $service->family_account_id = 30;
            $service->save();
            $updated++;
        }
    } else {
        // Kiểm tra xem đã có dịch vụ trong family chưa
        $existingServices = CustomerService::where('customer_id', $member->customer_id)
            ->where('service_package_id', $chatgptPackage->id)
            ->where('family_account_id', 30)
            ->where('status', 'active')
            ->count();

        if ($existingServices > 0) {
            echo "{$customerName}: Đã có {$existingServices} dịch vụ trong family ✓\n";
            $skipped++;
        } else {
            echo "{$customerName}: Không có dịch vụ CHATGPT PLANT cá nhân nào\n";
            $skipped++;
        }
    }
}

echo "\n=== KẾT QUẢ ===\n";
echo "Đã gán vào family: {$updated} dịch vụ\n";
echo "Bỏ qua: {$skipped} thành viên\n";

echo "\n✓ Hoàn tất!\n";
