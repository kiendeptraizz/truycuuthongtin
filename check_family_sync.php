<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\FamilyAccount;
use App\Models\FamilyMember;
use App\Models\CustomerService;

// Check Family Account ID 30 (Chat GPT Plant)
$familyAccountId = 30;

echo "=== Kiểm tra Family Account #$familyAccountId ===\n\n";

$family = FamilyAccount::find($familyAccountId);

if (!$family) {
    echo "Không tìm thấy Family Account #$familyAccountId\n";
    exit;
}

echo "Family: {$family->family_name} ({$family->family_code})\n";
echo "Max Members: {$family->max_members}\n";
echo "Current Members (từ DB): {$family->current_members}\n\n";

// Check FamilyMembers
$familyMembers = FamilyMember::where('family_account_id', $familyAccountId)->get();
echo "=== FAMILY MEMBERS (bảng family_members) ===\n";
echo "Tổng số: {$familyMembers->count()}\n";
echo "Active: " . $familyMembers->where('status', 'active')->count() . "\n";
echo "Suspended: " . $familyMembers->where('status', 'suspended')->count() . "\n";
echo "Removed: " . $familyMembers->where('status', 'removed')->count() . "\n\n";

echo "Chi tiết:\n";
foreach ($familyMembers as $member) {
    $customer = $member->customer;
    echo "  - ID {$member->id}: Customer #{$member->customer_id} - ";
    echo $customer ? $customer->name : "Đã xóa";
    echo " - Status: {$member->status}\n";
}

echo "\n=== CUSTOMER SERVICES (bảng customer_services) ===\n";
$customerServices = CustomerService::where('family_account_id', $familyAccountId)->get();
echo "Tổng số: {$customerServices->count()}\n";
echo "Active: " . $customerServices->where('status', 'active')->count() . "\n";
echo "Expired: " . $customerServices->where('status', 'expired')->count() . "\n";
echo "Cancelled: " . $customerServices->where('status', 'cancelled')->count() . "\n\n";

echo "Chi tiết:\n";
foreach ($customerServices as $service) {
    $customer = $service->customer;
    echo "  - ID {$service->id}: Customer #{$service->customer_id} - ";
    echo $customer ? $customer->name : "Đã xóa";
    echo " - Package: {$service->servicePackage->name}";
    echo " - Status: {$service->status}\n";
}

echo "\n=== PHÂN TÍCH ===\n";
echo "Số FamilyMembers: {$familyMembers->count()}\n";
echo "Số CustomerServices: {$customerServices->count()}\n";

if ($familyMembers->count() != $customerServices->count()) {
    echo "\n⚠️ CẢNH BÁO: Không đồng bộ!\n";
    echo "Có " . ($familyMembers->count() - $customerServices->count()) . " FamilyMembers không có CustomerService tương ứng!\n";

    // Tìm FamilyMembers không có CustomerService
    $memberCustomerIds = $familyMembers->pluck('customer_id');
    $serviceCustomerIds = $customerServices->pluck('customer_id');
    $missingInServices = $memberCustomerIds->diff($serviceCustomerIds);

    if ($missingInServices->count() > 0) {
        echo "\nCustomer IDs có FamilyMember nhưng không có CustomerService:\n";
        foreach ($missingInServices as $customerId) {
            $member = $familyMembers->where('customer_id', $customerId)->first();
            $customer = $member->customer;
            echo "  - Customer #$customerId: " . ($customer ? $customer->name : "Đã xóa") . "\n";
        }
    }
} else {
    echo "\n✓ Đồng bộ tốt!\n";
}
