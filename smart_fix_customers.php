<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Customer;

echo "=== SỬA TÊN KHÁCH HÀNG THÔNG MINH ===\n\n";

// Mapping các ký tự bị lỗi thường gặp
$fixMappings = [
    'Nguy???n' => 'Nguyễn',
    'V??n' => 'Văn',
    'Th??nh' => 'Thành',
    'Ph??t' => 'Phát',
    'Qu???C' => 'Quốc',
    'Anh' => 'Anh',
    'Ho??Ng' => 'Hoàng',
    'Th???' => 'Thị',
    'L??' => 'Lê',
    'H???|' => 'Hải',
    'Minh' => 'Minh',
    '???N' => 'Đức',
    '???N' => 'Đức',
    '?????t' => 'Đạt',
    '?????t' => 'Đạt',
];

// Tìm khách hàng có tên bị lỗi
$customersWithErrors = Customer::where('name', 'LIKE', '%?%')->get();

echo "📊 Tìm thấy {$customersWithErrors->count()} khách hàng bị lỗi\n\n";

if ($customersWithErrors->count() === 0) {
    echo "✅ Không có khách hàng nào bị lỗi!\n";
    exit;
}

$fixed = 0;
$skipped = 0;

foreach ($customersWithErrors as $customer) {
    $originalName = $customer->name;
    $fixedName = $originalName;

    // Áp dụng các mapping
    foreach ($fixMappings as $wrong => $correct) {
        $fixedName = str_replace($wrong, $correct, $fixedName);
    }

    // Nếu vẫn còn ký tự ? thì bỏ qua
    if (strpos($fixedName, '?') !== false) {
        echo "⏭️  Bỏ qua {$customer->customer_code}: {$originalName} (không thể tự động sửa)\n";
        $skipped++;
        continue;
    }

    // Cập nhật tên đã sửa
    $customer->name = $fixedName;
    $customer->save();

    echo "✅ Sửa {$customer->customer_code}: {$originalName} → {$fixedName}\n";
    $fixed++;
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "KẾT QUẢ:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ Đã sửa tự động: {$fixed} khách hàng\n";
echo "⏭️  Cần sửa thủ công: {$skipped} khách hàng\n\n";

if ($fixed > 0) {
    echo "🎉 Hoàn thành! Một số tên đã được sửa tự động.\n";
    echo "💡 Refresh lại trang admin để xem kết quả.\n\n";
}

if ($skipped > 0) {
    echo "⚠️  Còn {$skipped} khách hàng cần sửa thủ công.\n";
    echo "   → Dùng script fix_old_customers.php để sửa từng cái.\n";
}
