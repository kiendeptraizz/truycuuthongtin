<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== KIỂM TRA VÀ SỬA LẠI 2 FAMILY ===\n\n";

$family40Emails = [
    'trungtuank54@gmail.com',
    'nguyenngocsonkdol@gmail.com',
    'minhhatlu@gmail.com',
    'hoangthanhbinh472001@gmail.com',
];

$family41Emails = [
    'videolophoc1@gmail.com',
    'phattran.gen@gmail.com',
    'lenhung1550@gmail.com',
    'leanhthang0903@gmail.com',
];

echo "=== FAMILY #40: gg one 8 (vietanhc190@gmail.com) ===\n";
echo "Cần có 4 emails:\n";
foreach ($family40Emails as $email) {
    echo "  - {$email}\n";
}

echo "\nKiểm tra hiện tại:\n";
foreach ($family40Emails as $email) {
    $service = \App\Models\CustomerService::where('login_email', $email)
        ->whereNotNull('service_package_id')
        ->where('service_package_id', 7) // Gemini Pro + 2TB
        ->first();

    if (!$service) {
        echo "  ❌ {$email} - KHÔNG TÌM THẤY DỊCH VỤ\n";
    } else {
        $currentFamily = $service->family_account_id;
        echo "  " . ($currentFamily == 40 ? "✅" : "⚠️") . " {$email} - Service #{$service->id}, Family: " . ($currentFamily ?? 'NULL') . "\n";

        if ($currentFamily != 40) {
            echo "     → CẦN UPDATE family_account_id = 40\n";
        }
    }
}

echo "\n=== FAMILY #41: GG ONE 9 (anhvandz.2bn@gmail.com) ===\n";
echo "Cần có 4 emails:\n";
foreach ($family41Emails as $email) {
    echo "  - {$email}\n";
}

echo "\nKiểm tra hiện tại:\n";
foreach ($family41Emails as $email) {
    $service = \App\Models\CustomerService::where('login_email', $email)
        ->whereNotNull('service_package_id')
        ->where('service_package_id', 7) // Gemini Pro + 2TB
        ->first();

    if (!$service) {
        echo "  ❌ {$email} - KHÔNG TÌM THẤY DỊCH VỤ\n";
    } else {
        $currentFamily = $service->family_account_id;
        echo "  " . ($currentFamily == 41 ? "✅" : "⚠️") . " {$email} - Service #{$service->id}, Family: " . ($currentFamily ?? 'NULL') . "\n";

        if ($currentFamily != 41) {
            echo "     → CẦN UPDATE family_account_id = 41\n";
        }
    }
}

echo "\n=== BẮT ĐẦU FIX ===\n\n";

$updates = [];

// Chuẩn bị updates cho Family #40
foreach ($family40Emails as $email) {
    $service = \App\Models\CustomerService::where('login_email', $email)
        ->where('service_package_id', 7)
        ->first();

    if ($service && $service->family_account_id != 40) {
        $updates[] = [
            'service_id' => $service->id,
            'email' => $email,
            'old_family' => $service->family_account_id,
            'new_family' => 40,
        ];
    }
}

// Chuẩn bị updates cho Family #41
foreach ($family41Emails as $email) {
    $service = \App\Models\CustomerService::where('login_email', $email)
        ->where('service_package_id', 7)
        ->first();

    if ($service && $service->family_account_id != 41) {
        $updates[] = [
            'service_id' => $service->id,
            'email' => $email,
            'old_family' => $service->family_account_id,
            'new_family' => 41,
        ];
    }
}

if (count($updates) == 0) {
    echo "✅ TẤT CẢ ĐÃ ĐÚNG, KHÔNG CẦN FIX!\n";
} else {
    echo "Sẽ update " . count($updates) . " services:\n\n";

    foreach ($updates as $update) {
        echo "Service #{$update['service_id']} ({$update['email']}):\n";
        echo "  Old family: " . ($update['old_family'] ?? 'NULL') . " → New family: {$update['new_family']}\n";

        $service = \App\Models\CustomerService::find($update['service_id']);
        $service->family_account_id = $update['new_family'];
        $service->save();

        echo "  ✅ ĐÃ UPDATE\n\n";
    }
}

echo "\n=== KIỂM TRA SAU KHI FIX ===\n\n";

$family40Count = \App\Models\CustomerService::where('family_account_id', 40)->count();
$family41Count = \App\Models\CustomerService::where('family_account_id', 41)->count();

echo "Family #40: {$family40Count}/5 slots\n";
echo "Family #41: {$family41Count}/5 slots\n";

echo "\n✅ HOÀN TẤT!\n";
