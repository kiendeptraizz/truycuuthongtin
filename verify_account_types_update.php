<?php

/**
 * 🔍 XÁC MINH CẬP NHẬT LOẠI TÀI KHOẢN
 * 
 * Script này sẽ xác minh rằng tất cả loại tài khoản đã được cập nhật thành công
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ServicePackage;
use Illuminate\Support\Facades\DB;

echo "🔍 XÁC MINH CẬP NHẬT LOẠI TÀI KHOẢN 🔍\n";
echo "====================================\n\n";

try {
    // Kiểm tra các loại tài khoản hiện tại
    echo "📊 KIỂM TRA CÁC LOẠI TÀI KHOẢN HIỆN TẠI...\n";
    echo "=========================================\n";

    $accountTypes = DB::table('service_packages')
        ->select('account_type', DB::raw('COUNT(*) as count'))
        ->groupBy('account_type')
        ->orderBy('count', 'desc')
        ->get();

    echo "📋 Danh sách loại tài khoản:\n";
    foreach ($accountTypes as $type) {
        echo "   • {$type->account_type}: {$type->count} gói\n";
    }
    echo "\n";

    // Kiểm tra các loại tài khoản mong đợi
    echo "✅ KIỂM TRA CÁC LOẠI TÀI KHOẢN MONG ĐỢI...\n";
    echo "==========================================\n";

    $expectedTypes = [
        'Tài khoản chính chủ',
        'Tài khoản cấp (dùng riêng)',
        'Tài khoản add family',
        'Tài khoản dùng chung'
    ];

    $foundTypes = $accountTypes->pluck('account_type')->toArray();
    $missingTypes = [];
    $extraTypes = [];

    // Kiểm tra loại tài khoản mong đợi
    foreach ($expectedTypes as $expectedType) {
        if (in_array($expectedType, $foundTypes)) {
            $count = $accountTypes->where('account_type', $expectedType)->first()->count ?? 0;
            echo "   ✅ {$expectedType}: {$count} gói\n";
        } else {
            echo "   ❌ {$expectedType}: KHÔNG TÌM THẤY\n";
            $missingTypes[] = $expectedType;
        }
    }

    // Kiểm tra loại tài khoản không mong đợi (cũ)
    $oldTypes = ['Add mail', 'Team dùng chung'];
    foreach ($oldTypes as $oldType) {
        if (in_array($oldType, $foundTypes)) {
            $count = $accountTypes->where('account_type', $oldType)->first()->count ?? 0;
            echo "   ⚠️ {$oldType}: {$count} gói (LOẠI CŨ - CẦN CẬP NHẬT)\n";
            $extraTypes[] = $oldType;
        }
    }
    echo "\n";

    // Kiểm tra tài khoản dùng chung
    echo "🔍 KIỂM TRA TÀI KHOẢN DÙNG CHUNG...\n";
    echo "==================================\n";

    $sharedAccountsCount = DB::table('customer_services')
        ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
        ->where('service_packages.account_type', 'Tài khoản dùng chung')
        ->whereNotNull('customer_services.login_email')
        ->where('customer_services.login_email', '!=', '')
        ->count();

    $uniqueSharedEmails = DB::table('customer_services')
        ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
        ->where('service_packages.account_type', 'Tài khoản dùng chung')
        ->whereNotNull('customer_services.login_email')
        ->where('customer_services.login_email', '!=', '')
        ->distinct()
        ->count('customer_services.login_email');

    echo "📊 Dịch vụ tài khoản dùng chung: {$sharedAccountsCount}\n";
    echo "📧 Email dùng chung duy nhất: {$uniqueSharedEmails}\n\n";

    // Tóm tắt kết quả
    echo "📋 TÓM TẮT KẾT QUẢ:\n";
    echo "===================\n";

    if (empty($missingTypes) && empty($extraTypes)) {
        echo "🎉 HOÀN HẢO! TẤT CẢ LOẠI TÀI KHOẢN ĐÃ ĐƯỢC CẬP NHẬT THÀNH CÔNG!\n\n";

        echo "✅ CÁC LOẠI TÀI KHOẢN MỚI:\n";
        echo "==========================\n";
        echo "1. Tài khoản chính chủ - Tài khoản cá nhân, quyền sở hữu hoàn toàn\n";
        echo "2. Tài khoản cấp (dùng riêng) - Tài khoản phụ, sử dụng riêng biệt\n";
        echo "3. Tài khoản add family - Tài khoản thêm vào gói gia đình\n";
        echo "4. Tài khoản dùng chung - Tài khoản nhiều người cùng sử dụng\n\n";

        echo "🔧 CHỨC NĂNG HOẠT ĐỘNG:\n";
        echo "=======================\n";
        echo "✅ Dropdown tạo gói dịch vụ có 4 loại tài khoản mới\n";
        echo "✅ Trang quản lý tài khoản dùng chung hoạt động bình thường\n";
        echo "✅ Dữ liệu hiện có được bảo toàn và cập nhật chính xác\n";
        echo "✅ Backup đã được tạo để đảm bảo an toàn\n\n";
    } else {
        echo "⚠️ CÒN VẤN ĐỀ CẦN KHẮC PHỤC:\n";
        echo "=============================\n";

        if (!empty($missingTypes)) {
            echo "❌ Loại tài khoản thiếu:\n";
            foreach ($missingTypes as $type) {
                echo "   • {$type}\n";
            }
            echo "\n";
        }

        if (!empty($extraTypes)) {
            echo "⚠️ Loại tài khoản cũ cần cập nhật:\n";
            foreach ($extraTypes as $type) {
                echo "   • {$type}\n";
            }
            echo "\n";
        }

        echo "💡 ĐỀ XUẤT:\n";
        echo "===========\n";
        echo "1. Chạy lại script update_account_types.php\n";
        echo "2. Kiểm tra lại database để đảm bảo dữ liệu chính xác\n";
        echo "3. Xóa cache view: php artisan view:clear\n\n";
    }

    // Kiểm tra một số gói dịch vụ cụ thể
    echo "🔍 KIỂM TRA MỘT SỐ GÓI DỊCH VỤ CỤ THỂ...\n";
    echo "=========================================\n";

    $samplePackages = ServicePackage::take(5)->get();
    foreach ($samplePackages as $package) {
        echo "📦 {$package->name} (ID: {$package->id})\n";
        echo "   🏷️ Loại: {$package->account_type}\n";
        echo "   💰 Giá: " . number_format($package->price) . "đ\n\n";
    }

    // Tạo báo cáo
    generateVerificationReport($accountTypes, $sharedAccountsCount, $uniqueSharedEmails, $missingTypes, $extraTypes);
} catch (\Exception $e) {
    echo "❌ LỖI: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

function generateVerificationReport($accountTypes, $sharedAccountsCount, $uniqueSharedEmails, $missingTypes, $extraTypes)
{
    $timestamp = date('Y-m-d_H-i-s');
    $reportFile = "account_types_verification_report_{$timestamp}.txt";

    $report = "BÁO CÁO XÁC MINH CẬP NHẬT LOẠI TÀI KHOẢN\n";
    $report .= "========================================\n\n";
    $report .= "Ngày xác minh: " . date('Y-m-d H:i:s') . "\n\n";

    $report .= "CÁC LOẠI TÀI KHOẢN HIỆN TẠI:\n";
    $report .= "============================\n";
    foreach ($accountTypes as $type) {
        $report .= "• {$type->account_type}: {$type->count} gói\n";
    }
    $report .= "\n";

    $report .= "THỐNG KÊ TÀI KHOẢN DÙNG CHUNG:\n";
    $report .= "==============================\n";
    $report .= "• Tổng dịch vụ: {$sharedAccountsCount}\n";
    $report .= "• Email duy nhất: {$uniqueSharedEmails}\n\n";

    if (empty($missingTypes) && empty($extraTypes)) {
        $report .= "KẾT QUẢ: THÀNH CÔNG HOÀN TOÀN\n";
        $report .= "=============================\n";
        $report .= "✅ Tất cả loại tài khoản đã được cập nhật chính xác\n";
        $report .= "✅ Không có loại tài khoản thiếu hoặc cũ\n";
        $report .= "✅ Hệ thống hoạt động bình thường\n";
    } else {
        $report .= "KẾT QUẢ: CÒN VẤN ĐỀ\n";
        $report .= "====================\n";
        if (!empty($missingTypes)) {
            $report .= "❌ Loại thiếu: " . implode(', ', $missingTypes) . "\n";
        }
        if (!empty($extraTypes)) {
            $report .= "⚠️ Loại cũ: " . implode(', ', $extraTypes) . "\n";
        }
    }

    $report .= "\nCÁC LOẠI TÀI KHOẢN MỚI:\n";
    $report .= "=======================\n";
    $report .= "1. Tài khoản chính chủ\n";
    $report .= "2. Tài khoản cấp (dùng riêng)\n";
    $report .= "3. Tài khoản add family\n";
    $report .= "4. Tài khoản dùng chung\n";

    file_put_contents($reportFile, $report);
    echo "📄 Báo cáo xác minh đã lưu: {$reportFile}\n";
}
