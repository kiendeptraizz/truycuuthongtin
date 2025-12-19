<?php

/**
 * Script thống kê khách hàng không hoạt động
 * - Có mã khách hàng nhưng không có dịch vụ nào
 * - Dịch vụ đã hết hạn từ lâu
 * - Có dịch vụ hết hạn nhưng lâu chưa gia hạn
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Customer;
use App\Models\CustomerService;
use Carbon\Carbon;

// Cấu hình thời gian (có thể điều chỉnh)
$EXPIRED_LONG_AGO_DAYS = 60;      // Số ngày hết hạn được coi là "từ lâu"
$NOT_RENEWED_DAYS = 30;            // Số ngày chưa gia hạn sau khi hết hạn

$today = Carbon::now();

echo "╔═══════════════════════════════════════════════════════════════════════════════╗\n";
echo "║         THỐNG KÊ KHÁCH HÀNG KHÔNG HOẠT ĐỘNG / DỊCH VỤ HẾT HẠN                ║\n";
echo "║                     Ngày: " . $today->format('d/m/Y H:i:s') . "                           ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════════════╝\n\n";

// ============================================================================
// 1. KHÁCH HÀNG CÓ MÃ NHƯNG KHÔNG CÓ DỊCH VỤ NÀO
// ============================================================================
echo "═══════════════════════════════════════════════════════════════════════════════\n";
echo "📌 1. KHÁCH HÀNG CÓ MÃ NHƯNG KHÔNG CÓ BẤT KỲ DỊCH VỤ NÀO\n";
echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

$customersWithoutServices = Customer::whereNotNull('customer_code')
    ->whereDoesntHave('customerServices')
    ->orderBy('created_at', 'desc')
    ->get();

if ($customersWithoutServices->count() > 0) {
    echo "Tìm thấy: {$customersWithoutServices->count()} khách hàng\n\n";
    echo str_pad("STT", 5) . str_pad("Mã KH", 12) . str_pad("Tên khách hàng", 35) . str_pad("SĐT", 15) . str_pad("Email", 30) . "Ngày tạo\n";
    echo str_repeat("-", 120) . "\n";

    $stt = 1;
    foreach ($customersWithoutServices as $customer) {
        $createdAt = $customer->created_at ? $customer->created_at->format('d/m/Y') : 'N/A';
        echo str_pad($stt++, 5)
            . str_pad($customer->customer_code ?? 'N/A', 12)
            . str_pad(mb_substr($customer->name ?? 'N/A', 0, 33), 35)
            . str_pad($customer->phone ?? 'N/A', 15)
            . str_pad(mb_substr($customer->email ?? 'N/A', 0, 28), 30)
            . $createdAt . "\n";
    }
} else {
    echo "✅ Không có khách hàng nào không có dịch vụ\n";
}

// ============================================================================
// 2. KHÁCH HÀNG CHỈ CÓ DỊCH VỤ ĐÃ HẾT HẠN (KHÔNG CÓ DỊCH VỤ ACTIVE NÀO)
// ============================================================================
echo "\n\n═══════════════════════════════════════════════════════════════════════════════\n";
echo "📌 2. KHÁCH HÀNG CHỈ CÓ DỊCH VỤ ĐÃ HẾT HẠN (KHÔNG CÓ DỊCH VỤ ACTIVE)\n";
echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

// Lấy khách hàng có dịch vụ expired nhưng không có dịch vụ active
$customersOnlyExpired = Customer::whereNotNull('customer_code')
    ->whereHas('customerServices', function ($query) {
        $query->where('status', 'expired');
    })
    ->whereDoesntHave('customerServices', function ($query) {
        $query->where('status', 'active');
    })
    ->with(['customerServices' => function ($query) {
        $query->where('status', 'expired')
            ->orderBy('expires_at', 'desc');
    }, 'customerServices.servicePackage'])
    ->orderBy('name')
    ->get();

if ($customersOnlyExpired->count() > 0) {
    echo "Tìm thấy: {$customersOnlyExpired->count()} khách hàng\n\n";

    $stt = 1;
    foreach ($customersOnlyExpired as $customer) {
        $latestExpired = $customer->customerServices->first();
        $expiredAt = $latestExpired && $latestExpired->expires_at
            ? $latestExpired->expires_at->format('d/m/Y')
            : 'N/A';
        $daysExpired = $latestExpired && $latestExpired->expires_at
            ? (int)$today->diffInDays($latestExpired->expires_at, false)
            : 'N/A';
        $serviceName = $latestExpired && $latestExpired->servicePackage
            ? $latestExpired->servicePackage->name
            : 'N/A';

        echo "├─ {$stt}. [{$customer->customer_code}] {$customer->name}\n";
        echo "│     SĐT: " . ($customer->phone ?? 'N/A') . " | Email: " . ($customer->email ?? 'N/A') . "\n";
        echo "│     Dịch vụ cuối: " . mb_substr($serviceName, 0, 40) . "\n";
        $daysLabel = $daysExpired >= 0 ? "còn {$daysExpired} ngày" : "đã " . abs($daysExpired) . " ngày trước";
        echo "│     Hết hạn: {$expiredAt} ({$daysLabel})\n";
        echo "│     Số DV expired: " . $customer->customerServices->count() . "\n";
        echo "│\n";
        $stt++;
    }
} else {
    echo "✅ Không có khách hàng nào chỉ có dịch vụ expired\n";
}

// ============================================================================
// 3. DỊCH VỤ HẾT HẠN TỪ LÂU (Hơn 60 ngày)
// ============================================================================
echo "\n\n═══════════════════════════════════════════════════════════════════════════════\n";
echo "📌 3. DỊCH VỤ HẾT HẠN TỪ LÂU (HƠN {$EXPIRED_LONG_AGO_DAYS} NGÀY)\n";
echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

$expiredLongAgoDate = $today->copy()->subDays($EXPIRED_LONG_AGO_DAYS);

$servicesExpiredLongAgo = CustomerService::where('status', 'expired')
    ->whereNotNull('expires_at')
    ->where('expires_at', '<', $expiredLongAgoDate)
    ->with(['customer', 'servicePackage'])
    ->orderBy('expires_at', 'asc')
    ->get();

if ($servicesExpiredLongAgo->count() > 0) {
    // Nhóm theo khoảng thời gian hết hạn
    $grouped = [
        '60-90 ngày' => $servicesExpiredLongAgo->filter(function ($s) use ($today) {
            $days = $today->diffInDays($s->expires_at);
            return $days >= 60 && $days < 90;
        }),
        '90-180 ngày' => $servicesExpiredLongAgo->filter(function ($s) use ($today) {
            $days = $today->diffInDays($s->expires_at);
            return $days >= 90 && $days < 180;
        }),
        '180-365 ngày' => $servicesExpiredLongAgo->filter(function ($s) use ($today) {
            $days = $today->diffInDays($s->expires_at);
            return $days >= 180 && $days < 365;
        }),
        'Trên 1 năm' => $servicesExpiredLongAgo->filter(function ($s) use ($today) {
            $days = $today->diffInDays($s->expires_at);
            return $days >= 365;
        }),
    ];

    echo "Tổng số dịch vụ hết hạn từ lâu: {$servicesExpiredLongAgo->count()}\n\n";

    echo "📊 PHÂN BỐ THEO THỜI GIAN:\n";
    foreach ($grouped as $label => $services) {
        $percent = round(($services->count() / $servicesExpiredLongAgo->count()) * 100, 1);
        echo "   • {$label}: {$services->count()} dịch vụ ({$percent}%)\n";
    }

    echo "\n📋 CHI TIẾT (TOP 30 HẾT HẠN LÂU NHẤT):\n";
    echo str_repeat("-", 120) . "\n";
    echo str_pad("STT", 5) . str_pad("Mã KH", 12) . str_pad("Tên KH", 25) . str_pad("Dịch vụ", 35) . str_pad("Hết hạn", 12) . "Số ngày\n";
    echo str_repeat("-", 120) . "\n";

    $stt = 1;
    foreach ($servicesExpiredLongAgo->take(30) as $service) {
        $customerCode = $service->customer->customer_code ?? 'N/A';
        $customerName = mb_substr($service->customer->name ?? 'N/A', 0, 23);
        $serviceName = mb_substr($service->servicePackage->name ?? 'N/A', 0, 33);
        $expiredAt = $service->expires_at->format('d/m/Y');
        $daysExpired = abs((int)$today->diffInDays($service->expires_at, false));

        echo str_pad($stt++, 5)
            . str_pad($customerCode, 12)
            . str_pad($customerName, 25)
            . str_pad($serviceName, 35)
            . str_pad($expiredAt, 12)
            . $daysExpired . " ngày\n";
    }
} else {
    echo "✅ Không có dịch vụ nào hết hạn quá {$EXPIRED_LONG_AGO_DAYS} ngày\n";
}

// ============================================================================
// 4. KHÁCH HÀNG CÓ DỊCH VỤ HẾT HẠN NHƯNG CHƯA GIA HẠN (30 ngày+)
// ============================================================================
echo "\n\n═══════════════════════════════════════════════════════════════════════════════\n";
echo "📌 4. KHÁCH HÀNG CÓ DỊCH VỤ HẾT HẠN CHƯA GIA HẠN (TRÊN {$NOT_RENEWED_DAYS} NGÀY)\n";
echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

$notRenewedDate = $today->copy()->subDays($NOT_RENEWED_DAYS);

// Lấy các dịch vụ hết hạn trong 30 ngày qua nhưng chưa có dịch vụ mới cùng loại
$recentlyExpiredServices = CustomerService::where('status', 'expired')
    ->whereNotNull('expires_at')
    ->where('expires_at', '>=', $expiredLongAgoDate)
    ->where('expires_at', '<', $notRenewedDate)
    ->with(['customer', 'servicePackage'])
    ->orderBy('expires_at', 'asc')
    ->get();

if ($recentlyExpiredServices->count() > 0) {
    // Kiểm tra xem khách hàng có gia hạn (có dịch vụ mới active không)
    $needsRenewal = [];

    foreach ($recentlyExpiredServices as $service) {
        if (!$service->customer) continue;

        // Kiểm tra xem có dịch vụ active cùng gói không
        $hasRenewed = CustomerService::where('customer_id', $service->customer_id)
            ->where('service_package_id', $service->service_package_id)
            ->where('status', 'active')
            ->exists();

        if (!$hasRenewed) {
            $needsRenewal[] = $service;
        }
    }

    echo "Tìm thấy: " . count($needsRenewal) . " dịch vụ cần xem xét gia hạn\n\n";

    if (count($needsRenewal) > 0) {
        echo str_pad("STT", 5) . str_pad("Mã KH", 12) . str_pad("Tên KH", 25) . str_pad("SĐT", 15) . str_pad("Dịch vụ", 30) . str_pad("Hết hạn", 12) . "Số ngày\n";
        echo str_repeat("-", 120) . "\n";

        $stt = 1;
        foreach (array_slice($needsRenewal, 0, 50) as $service) {
            $customerCode = $service->customer->customer_code ?? 'N/A';
            $customerName = mb_substr($service->customer->name ?? 'N/A', 0, 23);
            $phone = $service->customer->phone ?? 'N/A';
            $serviceName = mb_substr($service->servicePackage->name ?? 'N/A', 0, 28);
            $expiredAt = $service->expires_at->format('d/m/Y');
            $daysExpired = abs((int)$today->diffInDays($service->expires_at, false));

            echo str_pad($stt++, 5)
                . str_pad($customerCode, 12)
                . str_pad($customerName, 25)
                . str_pad($phone, 15)
                . str_pad($serviceName, 30)
                . str_pad($expiredAt, 12)
                . $daysExpired . " ngày\n";
        }

        if (count($needsRenewal) > 50) {
            echo "\n... và " . (count($needsRenewal) - 50) . " dịch vụ khác\n";
        }
    }
} else {
    echo "✅ Không có dịch vụ nào trong khoảng thời gian này\n";
}

// ============================================================================
// 5. TỔNG KẾT
// ============================================================================
echo "\n\n╔═══════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                              📊 TỔNG KẾT                                       ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════════════╝\n\n";

$totalCustomers = Customer::whereNotNull('customer_code')->count();
$totalActiveServices = CustomerService::where('status', 'active')->count();
$totalExpiredServices = CustomerService::where('status', 'expired')->count();

echo "📈 THỐNG KÊ TỔNG QUAN:\n";
echo "   • Tổng số khách hàng có mã: {$totalCustomers}\n";
echo "   • Tổng dịch vụ đang active: {$totalActiveServices}\n";
echo "   • Tổng dịch vụ đã expired: {$totalExpiredServices}\n";
echo "\n";

echo "⚠️ CẦN CHÚ Ý:\n";
echo "   • KH không có dịch vụ nào: {$customersWithoutServices->count()}\n";
echo "   • KH chỉ có dịch vụ expired: {$customersOnlyExpired->count()}\n";
echo "   • Dịch vụ hết hạn trên {$EXPIRED_LONG_AGO_DAYS} ngày: {$servicesExpiredLongAgo->count()}\n";
echo "   • DV cần xem xét gia hạn: " . count($needsRenewal ?? []) . "\n";
echo "\n";

echo "💡 KHUYẾN NGHỊ:\n";
if ($customersWithoutServices->count() > 0) {
    echo "   → Xem xét liên hệ {$customersWithoutServices->count()} KH chưa sử dụng dịch vụ\n";
}
if ($customersOnlyExpired->count() > 0) {
    echo "   → Liên hệ {$customersOnlyExpired->count()} KH có DV expired để gia hạn\n";
}
if (isset($needsRenewal) && count($needsRenewal) > 0) {
    echo "   → Gửi nhắc nhở gia hạn cho " . count($needsRenewal) . " dịch vụ\n";
}

echo "\n=== KẾT THÚC BÁO CÁO ===\n";
