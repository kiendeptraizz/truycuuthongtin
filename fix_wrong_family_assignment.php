<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CustomerService;
use App\Models\FamilyAccount;
use Illuminate\Support\Facades\DB;

echo "=== XÓA family_account_id SAI (dịch vụ không khớp với family) ===\n\n";

// Lấy tất cả CustomerService có family_account_id
$servicesWithFamily = CustomerService::whereNotNull('family_account_id')
    ->with('servicePackage', 'familyAccount.servicePackage')
    ->get();

echo "Tìm thấy {$servicesWithFamily->count()} dịch vụ có family_account_id\n";
echo "Đang kiểm tra...\n\n";

$removed = 0;
$correct = 0;

foreach ($servicesWithFamily as $service) {
    $familyAccount = $service->familyAccount;

    if (!$familyAccount) {
        echo "⚠ Service #{$service->id}: Family account không tồn tại\n";
        continue;
    }

    $servicePackageName = $service->servicePackage->name;
    $familyPackageName = $familyAccount->servicePackage->name;

    // Kiểm tra xem service package có khớp với family package không
    // Hoặc ít nhất cùng loại (cùng chứa "CHATGPT", "Gemini", etc)
    $isMatch = false;

    // Logic: Nếu service package chứa từ khóa giống family package thì OK
    if (stripos($servicePackageName, 'CHATGPT') !== false && stripos($familyPackageName, 'CHATGPT') !== false) {
        $isMatch = true;
    } elseif (stripos($servicePackageName, 'Gemini') !== false && stripos($familyPackageName, 'Gemini') !== false) {
        $isMatch = true;
    } elseif ($servicePackageName === $familyPackageName) {
        $isMatch = true;
    }

    if (!$isMatch) {
        // Dịch vụ không khớp với family - XÓA family_account_id
        $customerName = $service->customer ? $service->customer->name : "N/A";

        echo "✗ REMOVE Service #{$service->id}: {$customerName}\n";
        echo "   Service: {$servicePackageName}\n";
        echo "   Family: {$familyAccount->family_name} ({$familyPackageName})\n";
        echo "   → XÓA family_account_id\n\n";

        $service->family_account_id = null;
        $service->save();

        $removed++;
    } else {
        $correct++;
    }
}

echo "\n=== KẾT QUẢ ===\n";
echo "Đã xóa family_account_id SAI: {$removed}\n";
echo "Dịch vụ đúng (giữ nguyên): {$correct}\n";

echo "\n✓ Hoàn tất!\n";
