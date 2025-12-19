<?php

/**
 * Script kiểm tra tính năng tự động cập nhật status dịch vụ hết hạn
 * 
 * Chạy script: php test_expired_services_update.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CustomerService;
use Carbon\Carbon;

echo "\n";
echo "╔════════════════════════════════════════════════════════════════════╗\n";
echo "║       KIỂM TRA TỰ ĐỘNG CẬP NHẬT STATUS DỊCH VỤ HẾT HẠN          ║\n";
echo "╚════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

// 1. Thống kê tổng quan
echo "📊 THỐNG KÊ TỔNG QUAN:\n";
echo str_repeat("-", 70) . "\n";

$totalServices = CustomerService::count();
$activeServices = CustomerService::where('status', 'active')->count();
$expiredServices = CustomerService::where('status', 'expired')->count();
$cancelledServices = CustomerService::where('status', 'cancelled')->count();

echo sprintf("   Tổng số dịch vụ: %d\n", $totalServices);
echo sprintf("   ├─ Active:       %d (%.1f%%)\n", $activeServices, ($activeServices / $totalServices) * 100);
echo sprintf("   ├─ Expired:      %d (%.1f%%)\n", $expiredServices, ($expiredServices / $totalServices) * 100);
echo sprintf("   └─ Cancelled:    %d (%.1f%%)\n", $cancelledServices, ($cancelledServices / $totalServices) * 100);
echo "\n";

// 2. Kiểm tra dịch vụ cần cập nhật
echo "🔍 KIỂM TRA DỊCH VỤ CẦN CẬP NHẬT:\n";
echo str_repeat("-", 70) . "\n";

$yesterday = Carbon::now()->subDay()->endOfDay();
$needUpdate = CustomerService::where('status', 'active')
    ->where('expires_at', '<=', $yesterday)
    ->count();

if ($needUpdate > 0) {
    echo sprintf("   ⚠️  Có %d dịch vụ status='active' nhưng đã hết hạn!\n", $needUpdate);
    echo "   💡 Chạy command: php artisan services:update-expired\n";
} else {
    echo "   ✓ Tất cả dịch vụ active đều còn hạn sử dụng.\n";
}
echo "\n";

// 3. Kiểm tra scope expired()
echo "🎯 KIỂM TRA SCOPE EXPIRED():\n";
echo str_repeat("-", 70) . "\n";

$scopeExpiredCount = CustomerService::expired()->count();
echo sprintf("   Số dịch vụ qua scope expired(): %d\n", $scopeExpiredCount);
echo sprintf("   Số dịch vụ có status='expired':  %d\n", $expiredServices);

if ($scopeExpiredCount === $expiredServices) {
    echo "   ✓ Scope expired() hoạt động đúng!\n";
} else {
    echo "   ⚠️  Scope expired() có vấn đề!\n";
}
echo "\n";

// 4. Kiểm tra scope expiredByDate()
echo "📅 KIỂM TRA SCOPE EXPIRED BY DATE():\n";
echo str_repeat("-", 70) . "\n";

$expiredByDateCount = CustomerService::expiredByDate()->count();
$manualCount = CustomerService::where('expires_at', '<=', $yesterday)->count();

echo sprintf("   Scope expiredByDate():           %d\n", $expiredByDateCount);
echo sprintf("   Manual count (expires_at <= ...): %d\n", $manualCount);

if ($expiredByDateCount === $manualCount) {
    echo "   ✓ Scope expiredByDate() hoạt động đúng!\n";
} else {
    echo "   ⚠️  Scope expiredByDate() có vấn đề!\n";
}
echo "\n";

// 5. Phân tích chi tiết dịch vụ hết hạn theo thời gian
echo "📈 PHÂN TÍCH DỊCH VỤ HẾT HẠN THEO THỜI GIAN:\n";
echo str_repeat("-", 70) . "\n";

$expiredByDateWithStatus = CustomerService::expiredByDate()
    ->selectRaw('status, COUNT(*) as count')
    ->groupBy('status')
    ->get();

echo sprintf("   Tổng dịch vụ có expires_at đã qua: %d\n", $expiredByDateCount);
echo "   Phân loại theo status:\n";

foreach ($expiredByDateWithStatus as $item) {
    $percentage = ($item->count / $expiredByDateCount) * 100;
    echo sprintf("   ├─ %s: %d (%.1f%%)\n", ucfirst($item->status), $item->count, $percentage);
}
echo "\n";

// 6. Kiểm tra scheduled task
echo "⏰ KIỂM TRA SCHEDULED TASK:\n";
echo str_repeat("-", 70) . "\n";

$consolePath = base_path('routes/console.php');
$consoleContent = file_get_contents($consolePath);

if (strpos($consoleContent, 'services:update-expired') !== false) {
    echo "   ✓ Command đã được đăng ký trong schedule.\n";
    echo "   📌 Sẽ chạy tự động hàng ngày vào lúc 00:05 AM\n";
    echo "\n";
    echo "   Để test schedule:\n";
    echo "   └─ php artisan schedule:work\n";
} else {
    echo "   ⚠️  Command chưa được đăng ký trong schedule!\n";
}
echo "\n";

// 7. Gợi ý hành động
echo "💡 GỢI Ý HÀNH ĐỘNG:\n";
echo str_repeat("-", 70) . "\n";

if ($needUpdate > 0) {
    echo "   1️⃣  Chạy ngay: php artisan services:update-expired\n";
    echo "   2️⃣  Kiểm tra lại sau khi chạy\n";
} else {
    echo "   ✅ Hệ thống đang hoạt động tốt!\n";
}

echo "   3️⃣  Để chạy tự động, đảm bảo cron job đã được setup:\n";
echo "       * * * * * cd " . base_path() . " && php artisan schedule:run\n";
echo "\n";

// 8. Kết luận
echo "╔════════════════════════════════════════════════════════════════════╗\n";

if ($needUpdate === 0 && $scopeExpiredCount === $expiredServices) {
    echo "║                    ✅ HỆ THỐNG HOẠT ĐỘNG CHUẨN                     ║\n";
} else {
    echo "║                ⚠️  CẦN CHẠY COMMAND CẬP NHẬT                      ║\n";
}

echo "╚════════════════════════════════════════════════════════════════════╝\n";
echo "\n";
