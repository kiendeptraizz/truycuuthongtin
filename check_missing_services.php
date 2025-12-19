<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$familyId = 41;

echo "=== KIỂM TRA DỊCH VỤ THIẾU CHO FAMILY #$familyId ===\n\n";

$family = \App\Models\FamilyAccount::find($familyId);
echo "Family: {$family->family_name}\n";
echo "Package: {$family->servicePackage->package_name} (ID: {$family->service_package_id})\n\n";

$members = \App\Models\FamilyMember::where('family_account_id', $familyId)
    ->where('status', 'active')
    ->get();

echo "=== MEMBERS TRONG FAMILY ===\n";
foreach ($members as $member) {
    echo "\n👤 {$member->member_name} (Customer #{$member->customer_id})\n";
    echo "   Email: {$member->member_email}\n";
    echo "   Start: {$member->start_date}\n";
    echo "   End: {$member->end_date}\n";

    // Tìm dịch vụ của customer này
    $services = \App\Models\CustomerService::where('customer_id', $member->customer_id)
        ->where('service_package_id', $family->service_package_id)
        ->get();

    if ($services->count() == 0) {
        echo "   ❌ KHÔNG CÓ CUSTOMER SERVICE!\n";
        echo "   💡 Cần tạo CustomerService với:\n";
        echo "      - customer_id: {$member->customer_id}\n";
        echo "      - service_package_id: {$family->service_package_id}\n";
        echo "      - family_account_id: {$familyId}\n";
        echo "      - login_email: {$member->member_email}\n";
    } else {
        foreach ($services as $s) {
            echo "   ✅ Service #{$s->id}:\n";
            echo "      - Status: {$s->status}\n";
            echo "      - Email: {$s->login_email}\n";
            echo "      - family_account_id: " . ($s->family_account_id ?? 'NULL') . "\n";

            if (!$s->family_account_id) {
                echo "      ⚠️ THIẾU family_account_id!\n";
                echo "      💡 Cần UPDATE family_account_id = {$familyId}\n";
            }
        }
    }
}

echo "\n\n=== ĐÁNH GIÁ ===\n";
$totalMembers = $members->count();
$servicesWithFamily = \App\Models\CustomerService::where('family_account_id', $familyId)->count();

echo "Tổng members active: {$totalMembers}\n";
echo "Tổng services có family_account_id: {$servicesWithFamily}\n";
echo "Thiếu: " . ($totalMembers - $servicesWithFamily) . " services\n";
