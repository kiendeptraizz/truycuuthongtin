<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== KIỂM TRA FAMILY #40 - ai đang chiếm 5 slots ===\n\n";

$family = \App\Models\FamilyAccount::find(40);
echo "Family: {$family->family_name}\n";
echo "Code: {$family->family_code}\n";
echo "Owner Email: {$family->owner_email}\n\n";

$correctEmails = [
    'trungtuank54@gmail.com',
    'nguyenngocsonkdol@gmail.com',
    'minhhatlu@gmail.com',
    'hoangthanhbinh472001@gmail.com',
];

echo "=== 5 SLOTS HIỆN TẠI ===\n";

$services = \App\Models\CustomerService::where('family_account_id', 40)
    ->with('customer', 'servicePackage')
    ->get();

echo "Tổng số: {$services->count()}/5 slots\n\n";

$index = 1;
foreach ($services as $service) {
    $email = $service->login_email;
    $isCorrect = in_array($email, $correctEmails);
    
    echo "Slot {$index}: " . ($isCorrect ? "✅" : "❌ THỪA") . "\n";
    echo "  Service ID: {$service->id}\n";
    echo "  Customer: {$service->customer->name} (#{$service->customer_id})\n";
    echo "  Email: {$email}\n";
    echo "  Package: {$service->servicePackage->package_name}\n";
    echo "  Status: {$service->status}\n";
    
    if (!$isCorrect) {
        echo "  ⚠️ EMAIL NÀY KHÔNG NÊN Ở FAMILY #40!\n";
    }
    
    echo "\n";
    $index++;
}

echo "\n=== PHÂN TÍCH ===\n";
$wrongServices = $services->filter(function($s) use ($correctEmails) {
    return !in_array($s->login_email, $correctEmails);
});

if ($wrongServices->count() > 0) {
    echo "Có {$wrongServices->count()} email THỪA trong Family #40:\n";
    foreach ($wrongServices as $s) {
        echo "  - {$s->login_email} (Service #{$s->id})\n";
    }
    echo "\n💡 NÊN XÓA hoặc chuyển sang Family khác\n";
} else {
    echo "✅ Tất cả đều đúng!\n";
}

echo "\n=== EMAIL CẦN CÓ NHƯNG CHƯA CÓ ===\n";
foreach ($correctEmails as $email) {
    $exists = $services->where('login_email', $email)->count() > 0;
    if (!$exists) {
        echo "  ❌ {$email} - CHƯA CÓ SERVICE TRONG FAMILY #40\n";
        
        // Tìm xem email này có service nào không
        $otherService = \App\Models\CustomerService::where('login_email', $email)
            ->where('service_package_id', 7)
            ->first();
        
        if ($otherService) {
            echo "     → Tìm thấy Service #{$otherService->id}, family_account_id: " . ($otherService->family_account_id ?? 'NULL') . "\n";
        } else {
            echo "     → KHÔNG TÌM THẤY DỊCH VỤ NÀO\n";
        }
    }
}
