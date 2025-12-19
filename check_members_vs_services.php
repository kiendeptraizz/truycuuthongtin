<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\FamilyMember;
use App\Models\CustomerService;

echo "=== KIỂM TRA THÀNH VIÊN KHÔNG CÓ DỊCH VỤ TRONG FAMILY #30 ===\n\n";

// Lấy tất cả FamilyMembers trong family #30
$familyMembers = FamilyMember::where('family_account_id', 30)
    ->where('status', 'active')
    ->with('customer')
    ->get();

echo "Tổng số FamilyMembers: {$familyMembers->count()}\n\n";

$hasService = 0;
$noService = 0;

foreach ($familyMembers as $member) {
    $customerName = $member->customer ? $member->customer->name : "Đã xóa";

    // Kiểm tra xem customer này có dịch vụ nào với family_account_id = 30 không
    $services = CustomerService::where('customer_id', $member->customer_id)
        ->where('family_account_id', 30)
        ->where('status', 'active')
        ->with('servicePackage')
        ->get();

    if ($services->count() > 0) {
        echo "✓ {$customerName}: CÓ {$services->count()} dịch vụ trong family\n";
        foreach ($services as $service) {
            echo "   - {$service->servicePackage->name}\n";
        }
        $hasService++;
    } else {
        echo "✗ {$customerName}: KHÔNG có dịch vụ nào trong family\n";

        // Kiểm tra xem họ có dịch vụ nào không
        $allServices = CustomerService::where('customer_id', $member->customer_id)
            ->where('status', 'active')
            ->with('servicePackage')
            ->get();

        if ($allServices->count() > 0) {
            echo "   Nhưng có {$allServices->count()} dịch vụ KHÁC:\n";
            foreach ($allServices as $service) {
                $familyInfo = $service->family_account_id ? " (Family #{$service->family_account_id})" : " (Cá nhân)";
                echo "   - {$service->servicePackage->name}{$familyInfo}\n";
            }
        }

        $noService++;
    }
    echo "\n";
}

echo "=== TỔNG KẾT ===\n";
echo "FamilyMembers CÓ dịch vụ trong family: {$hasService}\n";
echo "FamilyMembers KHÔNG có dịch vụ trong family: {$noService}\n";
