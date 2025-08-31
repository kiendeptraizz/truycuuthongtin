<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\FamilyMember;
use App\Models\CustomerService;

echo "Bắt đầu đồng bộ Family Members với Customer Services...\n";

$members = FamilyMember::with('familyAccount')->get();
echo "Tổng số Family Members: " . $members->count() . "\n";

$created = 0;
$skipped = 0;

foreach ($members as $member) {
    if (!$member->familyAccount || !$member->customer_id || !$member->member_email) {
        $skipped++;
        echo "Bỏ qua member ID {$member->id} - thiếu thông tin\n";
        continue;
    }

    // Kiểm tra xem đã có Customer Service chưa
    $existingService = CustomerService::where('customer_id', $member->customer_id)
        ->where('login_email', $member->member_email)
        ->first();

    if ($existingService) {
        $skipped++;
        echo "Bỏ qua member ID {$member->id} - đã có Customer Service\n";
        continue;
    }

    // Xác định status
    $status = 'active';
    if ($member->status === 'removed' || $member->status === 'suspended') {
        $status = 'cancelled';
    } elseif ($member->end_date && $member->end_date->isPast()) {
        $status = 'expired';
    }

    // Tạo Customer Service mới
    CustomerService::create([
        'customer_id' => $member->customer_id,
        'service_package_id' => $member->familyAccount->service_package_id,
        'login_email' => $member->member_email,
        'login_password' => null,
        'activated_at' => $member->start_date ?: $member->created_at,
        'expires_at' => $member->end_date,
        'status' => $status,
        'assigned_by' => 1, // Admin ID
        'internal_notes' => "Dịch vụ được tạo tự động từ Family Account: {$member->familyAccount->family_name} (ID: {$member->familyAccount->id}). Thành viên Family Member ID: {$member->id}",
        'created_at' => $member->created_at,
        'updated_at' => now(),
    ]);

    $created++;
    echo "Tạo Customer Service cho member ID {$member->id} - {$member->member_email} - Status: {$status}\n";
}

echo "\n=== KẾT QUẢ ===\n";
echo "Đã tạo: {$created} Customer Service mới\n";
echo "Bỏ qua: {$skipped} records\n";
echo "Hoàn thành!\n";
