<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\FamilyMember;
use App\Models\CustomerService;

echo "=== KIỂM TRA TOÀN BỘ CHI TIẾT DỊCH VỤ CHATGPT CỦA 4 THÀNH VIÊN ===\n\n";

$customerNames = ['Quynh Nguyen', 'Hxthang', 'Trần Văn Mạnh', 'Cao Quý'];

foreach ($customerNames as $name) {
    $member = FamilyMember::where('family_account_id', 30)
        ->where('status', 'active')
        ->whereHas('customer', function ($q) use ($name) {
            $q->where('name', 'like', "%{$name}%");
        })
        ->with('customer')
        ->first();

    if (!$member) {
        echo "✗ Không tìm thấy FamilyMember: {$name}\n\n";
        continue;
    }

    $customer = $member->customer;
    echo "=== {$customer->name} (Customer ID: {$customer->id}) ===\n";

    // Lấy TẤT CẢ dịch vụ có chứa "chatgpt" hoặc "gpt" trong tên
    $allServices = CustomerService::where('customer_id', $customer->id)
        ->whereHas('servicePackage', function ($q) {
            $q->where('name', 'like', '%chatgpt%')
                ->orWhere('name', 'like', '%gpt%')
                ->orWhere('name', 'like', '%GPT%');
        })
        ->with('servicePackage', 'familyAccount')
        ->get();

    echo "Tổng số dịch vụ liên quan GPT: {$allServices->count()}\n\n";

    foreach ($allServices as $service) {
        $packageName = $service->servicePackage->name;
        $status = $service->status;
        $familyInfo = "CÁ NHÂN";

        if ($service->family_account_id) {
            $familyName = $service->familyAccount ? $service->familyAccount->family_name : "Family #" . $service->family_account_id;
            $familyInfo = "FAMILY: {$familyName} (ID: {$service->family_account_id})";
        }

        $statusIcon = $status == 'active' ? '✓' : '✗';
        echo "  [{$statusIcon}] Service #{$service->id}\n";
        echo "      Package: {$packageName}\n";
        echo "      Status: {$status}\n";
        echo "      Location: {$familyInfo}\n";
        echo "      Expires: " . ($service->expires_at ? $service->expires_at->format('d/m/Y') : 'N/A') . "\n";
        echo "\n";
    }

    echo "\n";
}

echo "\n=== QUYẾT ĐỊNH ===\n";
echo "1. Nếu có dịch vụ 'chat gpt plant 6th' ACTIVE và CÁ NHÂN → Gán vào Family #30\n";
echo "2. Nếu dịch vụ đã thuộc Family KHÁC → Xem xét có nên chuyển không\n";
echo "3. Nếu không có dịch vụ nào → Xóa khỏi FamilyMember\n";
