<?php

/**
 * Thông tin chi tiết các email chưa xử lý
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$emails = [
    'sondo3125@gmail.com',
    'kiendtph491822@gmail.com',
    'hiepnguyen2797@gmail.com',
    'duongdtvn2@gmail.com',
    'quachanhmaker@gmail.com',
    'hoanglongpro121@gmail.com',
    'hoangnamhg1212@gmail.com',
    'phamthiphuonganh9999@gmail.com',
    'miniriviu@gmail.com',
    'thekhiem333@gmail.com',
];

echo "==========================================================\n";
echo "   THÔNG TIN CHI TIẾT CÁC EMAIL CHƯA XỬ LÝ\n";
echo "   Ngày: " . date('d/m/Y H:i:s') . "\n";
echo "==========================================================\n\n";

foreach ($emails as $email) {
    $services = DB::table('customer_services')
        ->select(
            'customer_services.id as service_id',
            'customer_services.login_email',
            'customer_services.family_account_id',
            'customer_services.status',
            'customer_services.expires_at',
            'customers.id as customer_id',
            'customers.name as customer_name',
            'customers.phone',
            'service_packages.name as package_name',
            'family_accounts.family_name',
            'family_accounts.owner_email'
        )
        ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
        ->join('customers', 'customer_services.customer_id', '=', 'customers.id')
        ->leftJoin('family_accounts', 'customer_services.family_account_id', '=', 'family_accounts.id')
        ->whereRaw('LOWER(customer_services.login_email) = ?', [strtolower($email)])
        ->where('service_packages.name', 'LIKE', '%Gemini%')
        ->where('service_packages.name', 'LIKE', '%2TB%')
        ->get();

    echo "📧 Email: {$email}\n";
    echo "   ----------------------------------------\n";

    if ($services->isEmpty()) {
        echo "   ❌ Không tìm thấy trong database\n\n";
    } else {
        foreach ($services as $s) {
            $famId = $s->family_account_id ?? 'Không có';
            $famName = $s->family_name ?? 'Không có Family';
            $ownerEmail = $s->owner_email ?? 'N/A';
            $expiresAt = $s->expires_at ? date('d/m/Y', strtotime($s->expires_at)) : 'N/A';

            echo "   Mã KH: #{$s->customer_id}\n";
            echo "   Tên KH: {$s->customer_name}\n";
            echo "   SĐT: {$s->phone}\n";
            echo "   Gói dịch vụ: {$s->package_name}\n";
            echo "   Family ID: #{$famId}\n";
            echo "   Tên Family: {$famName}\n";
            echo "   Chủ Family: {$ownerEmail}\n";
            echo "   Trạng thái: {$s->status}\n";
            echo "   Hết hạn: {$expiresAt}\n";
        }
        echo "\n";
    }
}

echo "==========================================================\n";
echo "BẢNG TỔNG HỢP:\n";
echo "==========================================================\n\n";

echo "| Email | Mã KH | Tên KH | Family ID | Tên Family | Chủ Family |\n";
echo "|-------|-------|--------|-----------|------------|------------|\n";

foreach ($emails as $email) {
    $services = DB::table('customer_services')
        ->select(
            'customers.id as customer_id',
            'customers.name as customer_name',
            'customer_services.family_account_id',
            'family_accounts.family_name',
            'family_accounts.owner_email'
        )
        ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
        ->join('customers', 'customer_services.customer_id', '=', 'customers.id')
        ->leftJoin('family_accounts', 'customer_services.family_account_id', '=', 'family_accounts.id')
        ->whereRaw('LOWER(customer_services.login_email) = ?', [strtolower($email)])
        ->where('service_packages.name', 'LIKE', '%Gemini%')
        ->where('service_packages.name', 'LIKE', '%2TB%')
        ->first();

    if ($services) {
        $famId = $services->family_account_id ?? '-';
        $famName = $services->family_name ?? 'Không có';
        $ownerEmail = $services->owner_email ?? 'N/A';
        echo "| {$email} | #{$services->customer_id} | {$services->customer_name} | #{$famId} | {$famName} | {$ownerEmail} |\n";
    } else {
        echo "| {$email} | - | - | - | Không tìm thấy | - |\n";
    }
}

echo "\n✅ Hoàn thành!\n";
