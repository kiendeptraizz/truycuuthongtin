<?php

/**
 * Danh sách email Gemini Pro + 2TB với thông tin Family và mã khách hàng
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "==========================================================\n";
echo "   DANH SÁCH EMAIL GEMINI PRO + 2TB (CHI TIẾT)\n";
echo "   Ngày: " . date('d/m/Y H:i:s') . "\n";
echo "==========================================================\n\n";

$services = DB::table('customer_services')
    ->select(
        'customer_services.id as service_id',
        'customer_services.login_email',
        'customer_services.status',
        'customer_services.expires_at',
        'customer_services.family_account_id',
        'customers.id as customer_id',
        'customers.name as customer_name',
        'customers.phone',
        'service_packages.name as package_name',
        'family_accounts.family_name',
        'family_accounts.owner_email as family_email',
        'family_accounts.family_code'
    )
    ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
    ->join('customers', 'customer_services.customer_id', '=', 'customers.id')
    ->leftJoin('family_accounts', 'customer_services.family_account_id', '=', 'family_accounts.id')
    ->where('service_packages.name', 'LIKE', '%Gemini%')
    ->where('service_packages.name', 'LIKE', '%2TB%')
    ->orderBy('family_accounts.id')
    ->orderBy('customer_services.expires_at', 'desc')
    ->get();

echo "Tổng số: " . count($services) . " tài khoản\n\n";

echo "| STT | Mã KH | Tên khách hàng | Email đăng nhập | Family | Hết hạn | Trạng thái |\n";
echo "|-----|-------|----------------|-----------------|--------|---------|------------|\n";

foreach ($services as $index => $s) {
    $num = $index + 1;
    $status = $s->status == 'active' ? 'Active' : 'Expired';
    $expiresAt = $s->expires_at ? date('d/m/Y', strtotime($s->expires_at)) : 'N/A';
    $familyId = $s->family_account_id ?? '-';

    echo "| {$num} | #{$s->customer_id} | {$s->customer_name} | {$s->login_email} | Fam #{$familyId} | {$expiresAt} | {$status} |\n";
}

echo "\n==========================================================\n";
echo "CHI TIẾT THEO FAMILY:\n";
echo "==========================================================\n\n";

$grouped = $services->groupBy('family_account_id');

foreach ($grouped as $famId => $members) {
    $famName = $members->first()->family_name ?? 'Không có Family';
    $famEmail = $members->first()->family_email ?? 'N/A';
    echo "📁 FAMILY #{$famId}: {$famName}\n";
    echo "   Email Family: {$famEmail}\n";
    echo "   Số thành viên: " . count($members) . "\n";
    echo "   ----------------------------------------\n";

    foreach ($members as $m) {
        $statusIcon = $m->status == 'active' ? '✅' : '❌';
        $expiresAt = $m->expires_at ? date('d/m/Y', strtotime($m->expires_at)) : 'N/A';
        echo "   {$statusIcon} #{$m->customer_id} - {$m->customer_name}\n";
        echo "      Email: {$m->login_email}\n";
        echo "      Hết hạn: {$expiresAt}\n";
    }
    echo "\n";
}

echo "==========================================================\n";
echo "✅ Hoàn thành!\n";
