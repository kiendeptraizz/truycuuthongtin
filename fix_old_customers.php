<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Customer;

echo "=== SỬA TÊN KHÁCH HÀNG BỊ LỖI ENCODING ===\n\n";

// Tìm khách hàng có tên bị lỗi (chứa ký tự ?)
$customersWithErrors = Customer::where('name', 'LIKE', '%?%')->get();

echo "📊 Tìm thấy {$customersWithErrors->count()} khách hàng bị lỗi encoding\n\n";

if ($customersWithErrors->count() === 0) {
    echo "✅ Không có khách hàng nào bị lỗi!\n";
    exit;
}

echo "📋 Danh sách khách hàng bị lỗi:\n";
foreach ($customersWithErrors as $customer) {
    echo "  {$customer->customer_code}: {$customer->name}\n";
}

echo "\n⚠️  LƯU Ý: Script này sẽ cần bạn cung cấp tên đúng cho từng khách hàng.\n";
echo "Bạn có muốn tiếp tục? (y/n): ";

$handle = fopen("php://stdin", "r");
$line = fgets($handle);
$choice = trim($line);
fclose($handle);

if (strtolower($choice) !== 'y') {
    echo "❌ Hủy bỏ.\n";
    exit;
}

echo "\n🔧 Bắt đầu sửa...\n\n";

$fixed = 0;
$skipped = 0;

foreach ($customersWithErrors as $customer) {
    echo "Khách hàng: {$customer->customer_code}\n";
    echo "Tên hiện tại: {$customer->name}\n";
    echo "Nhập tên đúng (Enter để bỏ qua): ";

    $handle = fopen("php://stdin", "r");
    $newName = trim(fgets($handle));
    fclose($handle);

    if (empty($newName)) {
        echo "⏭️  Bỏ qua\n\n";
        $skipped++;
        continue;
    }

    // Cập nhật tên mới
    $customer->name = $newName;
    $customer->save();

    echo "✅ Đã sửa: {$newName}\n\n";
    $fixed++;
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "KẾT QUẢ:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ Đã sửa: {$fixed} khách hàng\n";
echo "⏭️  Bỏ qua: {$skipped} khách hàng\n";
echo "📊 Tổng cộng: " . ($fixed + $skipped) . " khách hàng\n\n";

if ($fixed > 0) {
    echo "🎉 Hoàn thành! Tên khách hàng đã được sửa đúng encoding.\n";
    echo "💡 Refresh lại trang admin để xem kết quả.\n";
}
