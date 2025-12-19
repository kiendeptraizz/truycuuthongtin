<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$emails = [
    'videolophoc1@gmail.com',
    'phattran.gen@gmail.com',
    'lenhung1550@gmail.com',
    'leanhthang0903@gmail.com',
    'avbuilding18@gmail.com',
];

echo "=== KIỂM TRA 5 EMAIL TRONG GIA ĐÌNH ===\n\n";

foreach ($emails as $email) {
    echo "📧 {$email}\n";
    echo str_repeat('-', 70) . "\n";

    // Tìm CustomerService với login_email này
    $services = \App\Models\CustomerService::where('login_email', $email)->get();

    if ($services->count() == 0) {
        echo "   ❌ KHÔNG TÌM THẤY DỊCH VỤ NÀO\n\n";
        continue;
    }

    foreach ($services as $service) {
        echo "   Service #{$service->id}:\n";
        echo "   - Customer: {$service->customer->name} (#{$service->customer_id})\n";
        echo "   - Package: {$service->servicePackage->package_name}\n";
        echo "   - Status: {$service->status}\n";
        echo "   - family_account_id: " . ($service->family_account_id ?? 'NULL') . "\n";

        if ($service->family_account_id) {
            $family = \App\Models\FamilyAccount::find($service->family_account_id);
            echo "   - Family: {$family->family_name}\n";
            echo "   - Family Code: {$family->family_code}\n";
        } else {
            echo "   ⚠️ CHƯA GẮN VÀO FAMILY NÀO\n";
        }

        echo "\n";
    }
}

echo "\n=== TÓM TẮT ===\n";
echo "Kiểm tra xem các email này có thuộc cùng 1 Family không:\n\n";

$familyGroups = [];

foreach ($emails as $email) {
    $services = \App\Models\CustomerService::where('login_email', $email)
        ->whereNotNull('family_account_id')
        ->get();

    foreach ($services as $service) {
        $familyId = $service->family_account_id;
        if (!isset($familyGroups[$familyId])) {
            $family = \App\Models\FamilyAccount::find($familyId);
            $familyGroups[$familyId] = [
                'name' => $family->family_name,
                'code' => $family->family_code,
                'emails' => []
            ];
        }
        if (!in_array($email, $familyGroups[$familyId]['emails'])) {
            $familyGroups[$familyId]['emails'][] = $email;
        }
    }
}

foreach ($familyGroups as $familyId => $info) {
    echo "🏠 Family #{$familyId}: {$info['name']} ({$info['code']})\n";
    echo "   Số email: " . count($info['emails']) . "\n";
    foreach ($info['emails'] as $email) {
        echo "   ✓ {$email}\n";
    }
    echo "\n";
}

// Kiểm tra email không có family
$orphanEmails = [];
foreach ($emails as $email) {
    $hasFamily = \App\Models\CustomerService::where('login_email', $email)
        ->whereNotNull('family_account_id')
        ->exists();

    if (!$hasFamily) {
        $orphanEmails[] = $email;
    }
}

if (count($orphanEmails) > 0) {
    echo "⚠️ EMAIL CHƯA GẮN VÀO FAMILY:\n";
    foreach ($orphanEmails as $email) {
        echo "   - {$email}\n";
    }
}
