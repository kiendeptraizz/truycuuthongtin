<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\FamilyAccount;

// Check Family Account ID 30 (Chat GPT Plant)
$familyAccountId = 30;

echo "=== Kiểm tra Family Account #$familyAccountId sau khi cập nhật ===\n\n";

$family = FamilyAccount::with(['customerServices'])->find($familyAccountId);

if (!$family) {
    echo "Không tìm thấy Family Account #$familyAccountId\n";
    exit;
}

echo "Family: {$family->family_name} ({$family->family_code})\n";
echo "Max Members (Slots): {$family->max_members}\n\n";

// Đếm theo CustomerService (LOGIC MỚI - Mỗi dịch vụ = 1 slot)
$activeServices = $family->customerServices->where('status', 'active');
echo "=== SỐ SLOT ĐANG SỬ DỤNG (theo CustomerService) ===\n";
echo "Active Services: {$activeServices->count()}\n";
echo "Total Services: {$family->customerServices->count()}\n\n";

echo "Chi tiết các dịch vụ (mỗi dịch vụ = 1 slot):\n";
foreach ($family->customerServices as $service) {
    $customer = $service->customer;
    echo "  - Service #{$service->id}: ";
    echo $customer ? $customer->name : "Đã xóa";
    echo " - Package: {$service->servicePackage->name}";
    echo " - Status: {$service->status}\n";
}

echo "\n=== KẾT LUẬN ===\n";
echo "✓ Slots đang dùng: {$activeServices->count()}/{$family->max_members}\n";
echo "✓ Còn trống: " . ($family->max_members - $activeServices->count()) . " slots\n";

if ($activeServices->count() >= $family->max_members) {
    echo "⚠️ Family đã hết slot!\n";
} else {
    echo "✓ Có thể thêm " . ($family->max_members - $activeServices->count()) . " dịch vụ nữa\n";
}
