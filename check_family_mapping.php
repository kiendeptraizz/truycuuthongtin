<?php

/**
 * Kiểm tra mapping email trong danh sách với database
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Danh sách email từ người dùng
$userFamilyList = [
    'Fam 1' => ['owner' => 'kiendtph49182@gmail.com', 'members' => ['dinhtruongloc.1996@gmail.com', 'thailien.231197@gmail.com', 'thegioidecor12345@gmail.com', 'alexnguyen.mta.8889@gmail.com', 'tube238@gmail.com']],
    'Fam 2' => ['owner' => 'lowkeyzz2008@gmail.com', 'members' => ['tnphuonga2nd2018@gmail.com', 'htrung27791@gmail.com', 'nguyenthilien25091995@gmail.com', 'huethutrieuphu92@gmail.com', 'hoanglongpro1211@gmail.com']],
    'Fam 3' => ['owner' => 'geminipro.1661@gmail.com', 'members' => ['chungvietphuong2311@gmail.com', 'quypcna@gmail.com', 'beotoetls95@gmail.com', 'belleembrase@gmail.com', 'leengocthanhtruc@gmail.com']],
    'Fam 6' => ['owner' => 'khoathanhphuc@gmail.com', 'members' => ['tutbtv@gmail.com', 'vhnauy@gmail.com', 'sighhh1509@gmail.com', 'hungpchy.pixie@gmail.com', 'thanhphamxuan2042005@gmail.com']],
    'Fam 7' => ['owner' => 'kienchatgpt945@gmail.com', 'members' => ['m0903366155@gmail.com', 'huycao0356@gmail.com', 'nhakhoacourse@gmail.com', 'nhumanhkhanh@gmail.com', 'bonthui3005@gmail.com']],
    'Fam 8' => ['owner' => 'vietanhc190@gmail.com', 'members' => ['phamhieudong1705@gmail.com', 'nguyenngocsonkdol@gmail.com', 'minhhatlu@gmail.com', 'trungtuank54@gmail.com', 'hoangthanhbinh472001@gmail.com']],
    'Fam 9' => ['owner' => 'anhvandz.2bn@gmail.com', 'members' => ['videolophoc1@gmail.com', 'phattran.gen@gmail.com', 'lenhung1550@gmail.com', 'leanhthang0903@gmail.com', 'avbuilding18@gmail.com']],
    'Fam 10' => ['owner' => 'kien83667@gmail.com', 'members' => ['phansinhtung@gmail.com', 'letuananhhn@gmail.com', 'duchoa1349@gmail.com', 'baonamcenter555bn@gmail.com', 'bichhuyen1602@gmail.com']],
    'Fam 11' => ['owner' => 'hainguyenthi2110@gmail.com', 'members' => ['ngocnhung20011@gmail.com', 'kenjita1992@gmail.com', 'ngocnguyen115@gmail.com', 'ducminhle281201@gmail.com']],
    'Fam 12' => ['owner' => 'dtkien18@gmail.com', 'members' => ['leduongdc47@gmail.com', 'tienganhchobekids@gmail.com', 'vungan291999@gmail.com', 'jennyphan.popodoo@gmail.com']],
    'Fam 14' => ['owner' => 'thytiensfs@gmail.com', 'members' => ['vivuive2494@gmail.com', 'cuccangio@gmail.com', 'hoivocr@gmail.com', 'holleybuimn10@gmail.com', 'trinhhongnhung94@gmail.com']],
    'Fam 15' => ['owner' => 'nhatnguyenskibidu@gmail.com', 'members' => ['hactiensinh127@gmail.com', 'ngocthuong195@gmail.com', 'trangnguyenthuy1979@gmail.com', 'lethingoclan6780@gmail.com', 'tnqt1082@gmail.com']],
    'Fam 16' => ['owner' => 'nhai76755@gmail.com', 'members' => ['luubaolinh.mnhoamai@gmail.com', 'nguyenthixuanbach1976@gmail.com', 'nguyenthingoctram0909993186@gmail.com', 'lethanhhien7674@gmail.com', 'thongth@gmail.com']],
    'Fam 17' => ['owner' => 'ngtducsfs@gmail.com', 'members' => ['y.tranthinhu1504@gmail.com', 'huyendangmn10@gmail.com', 'nguyenthingoctuyenmn10@gmail.com', 'thuynguyencout@gmail.com', 'phungho121183@gmail.com']],
    'Fam 18' => ['owner' => 'nhnhat229@gmail.com', 'members' => ['laletuongan@gmail.com', 'kymngan2598@gmail.com', 'nguyenthihatien1808@gmail.com', 'vothithat1982@gmail.com']],
    'Fam 19' => ['owner' => 'nhatphamchuai@gmail.com', 'members' => ['kimanh24g@gmail.com', 'minhxuan1803@gmail.com', 'thanhthao210879@gmail.com', 'hoannm.globalleaders@gmail.com', 'mocsucmy2013@gmail.com']],
    'Fam 20' => ['owner' => 'phamconvy@gmail.com', 'members' => ['dinhnguyenthaovy010823@gmail.com', 'luongthingocchau@gmail.com', 'minhtuanpro134@gmail.com', 'minhkhoibui19@gmail.com']],
    'Fam 21' => ['owner' => 'phamconvy1@gmail.com', 'members' => ['hanhuynh10523@gmail.com', 'trucquynh2265@gmail.com', 'duongtrungduong93@gmail.com', 'lienhueduong2014@gmail.com', 'huongduyminh76@gmail.com']],
    'Fam 22' => ['owner' => 'phanconvy5@gmail.com', 'members' => ['mnga2311@gmail.com', 'nguyenminhthu14022002@gmail.com', 'kimngoc.kentenglish@gmail.com', 'tqngan86@gmail.com', 'minhhuyen4292@gmail.com']],
    'Fam 23' => ['owner' => 'phamconvy2@gmail.com', 'members' => ['hienhathi28081980@gmail.com', 'tranthachkimvan@gmail.com', 'micccccc87@gmail.com', 'luongthaotran@gmail.com']],
];

// Tạo mapping email -> fam name từ danh sách người dùng
$emailToUserFam = [];
foreach ($userFamilyList as $famName => $data) {
    $emailToUserFam[strtolower($data['owner'])] = ['fam' => $famName, 'role' => 'Chủ Fam'];
    foreach ($data['members'] as $member) {
        $emailToUserFam[strtolower($member)] = ['fam' => $famName, 'role' => 'Thành viên'];
    }
}

// Lấy tất cả email từ database (customer_services với Gemini + 2TB)
$dbServices = DB::table('customer_services')
    ->select(
        'customer_services.login_email',
        'customer_services.family_account_id',
        'family_accounts.family_name as db_family_name',
        'family_accounts.owner_email as db_owner_email',
        'customers.name as customer_name'
    )
    ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
    ->join('customers', 'customer_services.customer_id', '=', 'customers.id')
    ->leftJoin('family_accounts', 'customer_services.family_account_id', '=', 'family_accounts.id')
    ->where('service_packages.name', 'LIKE', '%Gemini%')
    ->where('service_packages.name', 'LIKE', '%2TB%')
    ->get();

// Tạo mapping email -> DB family info
$emailToDbFam = [];
foreach ($dbServices as $service) {
    $email = strtolower($service->login_email);
    $emailToDbFam[$email] = [
        'family_id' => $service->family_account_id,
        'family_name' => $service->db_family_name,
        'owner_email' => $service->db_owner_email,
        'customer_name' => $service->customer_name,
    ];
}

// Lấy tất cả family trong database
$allFamilies = DB::table('family_accounts')
    ->select('id', 'family_name', 'owner_email')
    ->get()
    ->keyBy('id');

echo "==========================================================\n";
echo "   KIỂM TRA MAPPING EMAIL GIỮA DANH SÁCH VÀ DATABASE\n";
echo "   Ngày: " . date('d/m/Y H:i:s') . "\n";
echo "==========================================================\n\n";

$matches = [];
$mismatches = [];
$notInDb = [];
$onlyInDb = [];

// Kiểm tra từng email trong danh sách người dùng
foreach ($emailToUserFam as $email => $userInfo) {
    if (isset($emailToDbFam[$email])) {
        $dbInfo = $emailToDbFam[$email];

        // So sánh family
        $dbFamId = $dbInfo['family_id'];
        $dbFamName = $dbInfo['family_name'] ?? 'Không có';

        $matches[] = [
            'email' => $email,
            'user_fam' => $userInfo['fam'],
            'user_role' => $userInfo['role'],
            'db_fam_id' => $dbFamId,
            'db_fam_name' => $dbFamName,
            'customer_name' => $dbInfo['customer_name'],
        ];
    } else {
        $notInDb[] = [
            'email' => $email,
            'user_fam' => $userInfo['fam'],
            'user_role' => $userInfo['role'],
        ];
    }
}

// Tìm email chỉ có trong DB nhưng không có trong danh sách
foreach ($emailToDbFam as $email => $dbInfo) {
    if (!isset($emailToUserFam[$email])) {
        $onlyInDb[] = [
            'email' => $email,
            'db_fam_id' => $dbInfo['family_id'],
            'db_fam_name' => $dbInfo['family_name'],
            'customer_name' => $dbInfo['customer_name'],
        ];
    }
}

// Báo cáo email có trong cả danh sách và DB
echo "==========================================================\n";
echo "✅ EMAIL CÓ TRONG CẢ DANH SÁCH VÀ DATABASE (" . count($matches) . " email)\n";
echo "==========================================================\n\n";

echo "| Email | Fam (Danh sách) | Vai trò | Fam DB | Tên Fam DB | Khách hàng |\n";
echo "|-------|-----------------|---------|--------|------------|------------|\n";

foreach ($matches as $m) {
    $dbFamDisplay = $m['db_fam_id'] ? "#{$m['db_fam_id']}" : "Không có";
    echo "| {$m['email']} | {$m['user_fam']} | {$m['user_role']} | {$dbFamDisplay} | {$m['db_fam_name']} | {$m['customer_name']} |\n";
}

// Báo cáo email KHÔNG có trong DB
echo "\n==========================================================\n";
echo "❌ EMAIL TRONG DANH SÁCH NHƯNG KHÔNG CÓ TRONG DATABASE (" . count($notInDb) . " email)\n";
echo "==========================================================\n\n";

if (count($notInDb) > 0) {
    echo "| Email | Fam (Danh sách) | Vai trò |\n";
    echo "|-------|-----------------|----------|\n";
    foreach ($notInDb as $n) {
        echo "| {$n['email']} | {$n['user_fam']} | {$n['user_role']} |\n";
    }
} else {
    echo "Không có email nào.\n";
}

// Báo cáo email chỉ có trong DB
echo "\n==========================================================\n";
echo "⚠️ EMAIL TRONG DATABASE NHƯNG KHÔNG CÓ TRONG DANH SÁCH (" . count($onlyInDb) . " email)\n";
echo "==========================================================\n\n";

if (count($onlyInDb) > 0) {
    echo "| Email | Fam DB | Tên Fam DB | Khách hàng |\n";
    echo "|-------|--------|------------|------------|\n";
    foreach ($onlyInDb as $o) {
        $dbFamDisplay = $o['db_fam_id'] ? "#{$o['db_fam_id']}" : "Không có";
        echo "| {$o['email']} | {$dbFamDisplay} | {$o['db_fam_name']} | {$o['customer_name']} |\n";
    }
} else {
    echo "Không có email nào.\n";
}

// Tạo bảng mapping Family
echo "\n==========================================================\n";
echo "📋 BẢNG MAPPING FAMILY (Danh sách -> Database)\n";
echo "==========================================================\n\n";

$famMapping = [];
foreach ($matches as $m) {
    $userFam = $m['user_fam'];
    $dbFamId = $m['db_fam_id'] ?? 'N/A';
    $dbFamName = $m['db_fam_name'] ?? 'N/A';

    if (!isset($famMapping[$userFam])) {
        $famMapping[$userFam] = [];
    }

    $key = $dbFamId . '|' . $dbFamName;
    if (!isset($famMapping[$userFam][$key])) {
        $famMapping[$userFam][$key] = [
            'db_fam_id' => $dbFamId,
            'db_fam_name' => $dbFamName,
            'count' => 0,
            'emails' => [],
        ];
    }
    $famMapping[$userFam][$key]['count']++;
    $famMapping[$userFam][$key]['emails'][] = $m['email'];
}

foreach ($famMapping as $userFam => $dbFams) {
    echo "📁 {$userFam}:\n";
    foreach ($dbFams as $info) {
        $dbFamDisplay = $info['db_fam_id'] ? "Fam #{$info['db_fam_id']}" : "Không có Fam";
        echo "   -> Trong DB: {$dbFamDisplay} ({$info['db_fam_name']}) - {$info['count']} thành viên\n";
        foreach ($info['emails'] as $email) {
            echo "      • {$email}\n";
        }
    }
    echo "\n";
}

echo "==========================================================\n";
echo "✅ Hoàn thành kiểm tra!\n";
