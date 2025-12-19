<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\FamilyMember;
use App\Models\CustomerService;

echo "=== KIỂM TRA THÀNH VIÊN KHÔNG CÓ TRONG DANH SÁCH SLOT ===\n\n";

// Lấy tất cả FamilyMembers active trong Family #30
$familyMembers = FamilyMember::where('family_account_id', 30)
    ->where('status', 'active')
    ->with('customer')
    ->get();

echo "Tổng số FamilyMembers active: {$familyMembers->count()}\n\n";

// Lấy tất cả CustomerServices active trong Family #30
$activeServices = CustomerService::where('family_account_id', 30)
    ->where('status', 'active')
    ->pluck('customer_id')
    ->unique();

echo "Số khách hàng có dịch vụ active trong family: {$activeServices->count()}\n\n";

echo "=== THÀNH VIÊN KHÔNG CÓ DỊCH VỤ TRONG SLOT ===\n\n";

$missingCount = 0;

foreach ($familyMembers as $member) {
    $customerName = $member->customer ? $member->customer->name : "Đã xóa";
    $customerId = $member->customer_id;

    // Kiểm tra xem customer này có dịch vụ active trong family không
    if (!$activeServices->contains($customerId)) {
        $missingCount++;
        echo "{$missingCount}. {$customerName} (Customer ID: {$customerId})\n";
        echo "   FamilyMember ID: {$member->id}\n";

        // Kiểm tra xem họ có dịch vụ nào liên quan đến GPT/ChatGPT không
        $gptServices = CustomerService::where('customer_id', $customerId)
            ->where('status', 'active')
            ->whereHas('servicePackage', function ($q) {
                $q->where('name', 'like', '%gpt%')
                    ->orWhere('name', 'like', '%chatgpt%')
                    ->orWhere('name', 'like', '%GPT%');
            })
            ->with('servicePackage', 'familyAccount')
            ->get();

        if ($gptServices->count() > 0) {
            echo "   CÓ {$gptServices->count()} dịch vụ GPT:\n";
            foreach ($gptServices as $service) {
                $familyInfo = $service->family_account_id
                    ? "Family #" . $service->family_account_id
                    : "Cá nhân";
                echo "     - Service #{$service->id}: {$service->servicePackage->name} ({$familyInfo})\n";
            }
        } else {
            echo "   KHÔNG có dịch vụ GPT nào\n";
        }
        echo "\n";
    }
}

if ($missingCount == 0) {
    echo "✓ TẤT CẢ thành viên đều có dịch vụ trong danh sách slot!\n";
} else {
    echo "\n=== TỔNG KẾT ===\n";
    echo "Số thành viên KHÔNG có dịch vụ trong slot: {$missingCount}/{$familyMembers->count()}\n";
}
