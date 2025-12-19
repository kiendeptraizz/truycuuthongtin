<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$familyId = 41;

echo "=== FIX FAMILY_ACCOUNT_ID CHO FAMILY #$familyId ===\n\n";

$updates = [
    998 => ['customer_id' => 353, 'email' => 'avbuilding18@gmail.com'],
    991 => ['customer_id' => 517, 'email' => 'lenhung1550@gmail.com'],
];

foreach ($updates as $serviceId => $info) {
    $service = \App\Models\CustomerService::find($serviceId);

    if ($service) {
        echo "Service #{$serviceId} ({$info['email']}):\n";
        echo "  - Current family_account_id: " . ($service->family_account_id ?? 'NULL') . "\n";

        $service->family_account_id = $familyId;
        $service->save();

        echo "  ✅ Updated family_account_id = {$familyId}\n\n";
    } else {
        echo "❌ Service #{$serviceId} không tồn tại\n\n";
    }
}

echo "=== KIỂM TRA LẠI ===\n";
$family = \App\Models\FamilyAccount::find($familyId);
$slotCount = \App\Models\CustomerService::where('family_account_id', $familyId)->count();

echo "Family: {$family->family_name}\n";
echo "Slots hiện tại: {$slotCount}/5\n";
echo "Còn lại: " . (5 - $slotCount) . " slots\n";

if ($slotCount > 0) {
    echo "\n✅ ĐÃ FIX XONG! Slots đã được tính đúng.\n";
} else {
    echo "\n❌ Vẫn còn vấn đề.\n";
}
