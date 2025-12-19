<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CustomerService;
use App\Models\FamilyAccount;

echo "=== KIỂM TRA VÀ XÓA TOÀN BỘ DỊCH VỤ SAI TRONG FAMILY #30 ===\n\n";

$family = FamilyAccount::find(30);
if (!$family) {
    echo "Không tìm thấy Family #30\n";
    exit;
}

echo "Family: {$family->family_name}\n";
echo "Gói dịch vụ family: {$family->servicePackage->name}\n\n";

// Lấy TẤT CẢ dịch vụ trong family này
$services = CustomerService::where('family_account_id', 30)
    ->with('customer', 'servicePackage')
    ->get();

echo "Tổng số dịch vụ trong family: {$services->count()}\n\n";

$removed = 0;
$kept = 0;

foreach ($services as $service) {
    $customerName = $service->customer ? $service->customer->name : "N/A";
    $packageName = $service->servicePackage->name;

    // Chỉ GIỮ LẠI dịch vụ "CHATGPT PLANT 1TH"
    if ($packageName === 'CHATGPT PLANT 1TH') {
        echo "✓ GIỮ Service #{$service->id}: {$customerName} - {$packageName}\n";
        $kept++;
    } else {
        echo "✗ XÓA Service #{$service->id}: {$customerName} - {$packageName}\n";

        $service->family_account_id = null;
        $service->save();

        $removed++;
    }
}

echo "\n=== KẾT QUẢ ===\n";
echo "Đã xóa: {$removed}\n";
echo "Giữ lại: {$kept}\n";

echo "\n✓ Hoàn tất!\n";
