<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$email = 'anhvandz.2bn@gmail.com';

echo "=== KIỂM TRA FAMILY ACCOUNT: $email ===\n\n";

$family = \App\Models\FamilyAccount::where('owner_email', $email)->first();

if (!$family) {
    echo "❌ Không tìm thấy Family Account\n";
    exit;
}

echo "✅ Family Account tìm thấy:\n";
echo "   ID: {$family->id}\n";
echo "   Tên: {$family->family_name}\n";
echo "   Mã: {$family->family_code}\n";
echo "   Service Package ID: {$family->service_package_id}\n";
echo "   Package: {$family->servicePackage->package_name}\n";
echo "   Max members (cũ): {$family->max_members}\n";
echo "   Current members (cũ): {$family->current_members}\n";
echo "   Status: {$family->status}\n";
echo "   Created: {$family->created_at}\n";
echo "\n";

echo "=== KIỂM TRA CUSTOMER SERVICES (SLOTS) ===\n";
$services = \App\Models\CustomerService::where('family_account_id', $family->id)->get();
echo "Tổng số CustomerService (slots): {$services->count()}\n\n";

if ($services->count() == 0) {
    echo "⚠️ KHÔNG CÓ SLOT NÀO!\n";
    echo "   Lý do có thể:\n";
    echo "   1. Family Account được tạo nhưng chưa gán dịch vụ cho khách hàng nào\n";
    echo "   2. Các dịch vụ được tạo nhưng không có family_account_id\n";
    echo "   3. Dịch vụ đã bị xóa\n\n";
} else {
    foreach ($services as $index => $s) {
        echo "Slot " . ($index + 1) . ":\n";
        echo "   Service ID: {$s->id}\n";
        echo "   Customer: {$s->customer->name} (#{$s->customer_id})\n";
        echo "   Email: {$s->login_email}\n";
        echo "   Package: {$s->servicePackage->package_name}\n";
        echo "   Status: {$s->status}\n";
        echo "   Activated: {$s->activated_at}\n";
        echo "   Expires: {$s->expires_at}\n";
        echo "\n";
    }
}

echo "\n=== KIỂM TRA FAMILY MEMBERS (Bảng cũ) ===\n";
$members = \App\Models\FamilyMember::where('family_account_id', $family->id)->get();
echo "Tổng số FamilyMember: {$members->count()}\n\n";

if ($members->count() > 0) {
    foreach ($members as $m) {
        echo "- {$m->member_name} (Customer #{$m->customer_id})\n";
        echo "  Status: {$m->status}\n";
        echo "  Email: {$m->member_email}\n";
        echo "\n";
    }
}

echo "\n=== KIỂM TRA DỊCH VỤ GEMINI CỦA GIA ĐÌNH ===\n";
// Kiểm tra xem có service nào của package Gemini không
$packageId = $family->service_package_id;
$allGeminiServices = \App\Models\CustomerService::where('service_package_id', $packageId)
    ->whereHas('customer', function ($q) use ($family) {
        // Tìm các customer liên quan đến family này
    })
    ->get();

echo "Package ID đang dùng: {$packageId}\n";
echo "Tên package: {$family->servicePackage->package_name}\n";

// Tìm owner của family account
$owner = \App\Models\Customer::find($family->customer_id);
if ($owner) {
    echo "\nChủ sở hữu Family: {$owner->name} (#{$owner->id})\n";

    // Kiểm tra dịch vụ của chủ
    $ownerServices = \App\Models\CustomerService::where('customer_id', $owner->id)
        ->where('service_package_id', $packageId)
        ->get();

    echo "Dịch vụ của chủ: {$ownerServices->count()}\n";
    foreach ($ownerServices as $s) {
        echo "  - Service #{$s->id}: family_account_id = " . ($s->family_account_id ?? 'NULL') . "\n";
    }
}

echo "\n=== KẾT LUẬN ===\n";
if ($services->count() == 0) {
    echo "❌ Family Account này có 0 slots\n";
    echo "💡 Giải pháp: Cần gán dịch vụ cho khách hàng và set family_account_id = {$family->id}\n";
} else {
    echo "✅ Family Account có {$services->count()} slots đang sử dụng\n";
}
