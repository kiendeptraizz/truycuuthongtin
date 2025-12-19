<?php

/**
 * Script sửa Family theo danh sách người dùng
 * 1. Đổi tên family theo format: fam_01_email@gmail.com
 * 2. Sửa email bị gán sai family về đúng family
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Danh sách Family từ người dùng với format mới
$userFamilyList = [
    'Fam 1' => [
        'new_name' => 'fam_01_kiendtph49182@gmail.com',
        'owner' => 'kiendtph49182@gmail.com',
        'members' => ['dinhtruongloc.1996@gmail.com', 'thailien.231197@gmail.com', 'thegioidecor12345@gmail.com', 'alexnguyen.mta.8889@gmail.com', 'tube238@gmail.com']
    ],
    'Fam 2' => [
        'new_name' => 'fam_02_lowkeyzz2008@gmail.com',
        'owner' => 'lowkeyzz2008@gmail.com',
        'members' => ['tnphuonga2nd2018@gmail.com', 'htrung27791@gmail.com', 'nguyenthilien25091995@gmail.com', 'huethutrieuphu92@gmail.com', 'hoanglongpro1211@gmail.com']
    ],
    'Fam 3' => [
        'new_name' => 'fam_03_geminipro.1661@gmail.com',
        'owner' => 'geminipro.1661@gmail.com',
        'members' => ['chungvietphuong2311@gmail.com', 'quypcna@gmail.com', 'beotoetls95@gmail.com', 'belleembrase@gmail.com', 'leengocthanhtruc@gmail.com']
    ],
    'Fam 6' => [
        'new_name' => 'fam_06_khoathanhphuc@gmail.com',
        'owner' => 'khoathanhphuc@gmail.com',
        'members' => ['tutbtv@gmail.com', 'vhnauy@gmail.com', 'sighhh1509@gmail.com', 'hungpchy.pixie@gmail.com', 'thanhphamxuan2042005@gmail.com']
    ],
    'Fam 7' => [
        'new_name' => 'fam_07_kienchatgpt945@gmail.com',
        'owner' => 'kienchatgpt945@gmail.com',
        'members' => ['m0903366155@gmail.com', 'huycao0356@gmail.com', 'nhakhoacourse@gmail.com', 'nhumanhkhanh@gmail.com', 'bonthui3005@gmail.com', 'thegioidecor12345@gmail.com']
    ],
    'Fam 8' => [
        'new_name' => 'fam_08_vietanhc190@gmail.com',
        'owner' => 'vietanhc190@gmail.com',
        'members' => ['phamhieudong1705@gmail.com', 'nguyenngocsonkdol@gmail.com', 'minhhatlu@gmail.com', 'trungtuank54@gmail.com', 'hoangthanhbinh472001@gmail.com', 'beotoetls95@gmail.com']
    ],
    'Fam 9' => [
        'new_name' => 'fam_09_anhvandz.2bn@gmail.com',
        'owner' => 'anhvandz.2bn@gmail.com',
        'members' => ['videolophoc1@gmail.com', 'phattran.gen@gmail.com', 'lenhung1550@gmail.com', 'leanhthang0903@gmail.com', 'avbuilding18@gmail.com']
    ],
    'Fam 10' => [
        'new_name' => 'fam_10_kien83667@gmail.com',
        'owner' => 'kien83667@gmail.com',
        'members' => ['phansinhtung@gmail.com', 'letuananhhn@gmail.com', 'duchoa1349@gmail.com', 'baonamcenter555bn@gmail.com', 'bichhuyen1602@gmail.com']
    ],
    'Fam 11' => [
        'new_name' => 'fam_11_hainguyenthi2110@gmail.com',
        'owner' => 'hainguyenthi2110@gmail.com',
        'members' => ['ngocnhung20011@gmail.com', 'kenjita1992@gmail.com', 'ngocnguyen115@gmail.com', 'ducminhle281201@gmail.com']
    ],
    'Fam 12' => [
        'new_name' => 'fam_12_dtkien18@gmail.com',
        'owner' => 'dtkien18@gmail.com',
        'members' => ['leduongdc47@gmail.com', 'tienganhchobekids@gmail.com', 'vungan291999@gmail.com', 'jennyphan.popodoo@gmail.com']
    ],
    'Fam 14' => [
        'new_name' => 'fam_14_thytiensfs@gmail.com',
        'owner' => 'thytiensfs@gmail.com',
        'members' => ['vivuive2494@gmail.com', 'cuccangio@gmail.com', 'hoivocr@gmail.com', 'holleybuimn10@gmail.com', 'trinhhongnhung94@gmail.com']
    ],
    'Fam 15' => [
        'new_name' => 'fam_15_nhatnguyenskibidu@gmail.com',
        'owner' => 'nhatnguyenskibidu@gmail.com',
        'members' => ['hactiensinh127@gmail.com', 'ngocthuong195@gmail.com', 'trangnguyenthuy1979@gmail.com', 'lethingoclan6780@gmail.com', 'tnqt1082@gmail.com']
    ],
    'Fam 16' => [
        'new_name' => 'fam_16_nhai76755@gmail.com',
        'owner' => 'nhai76755@gmail.com',
        'members' => ['luubaolinh.mnhoamai@gmail.com', 'nguyenthixuanbach1976@gmail.com', 'nguyenthingoctram0909993186@gmail.com', 'lethanhhien7674@gmail.com', 'thongth@gmail.com']
    ],
    'Fam 17' => [
        'new_name' => 'fam_17_ngtducsfs@gmail.com',
        'owner' => 'ngtducsfs@gmail.com',
        'members' => ['y.tranthinhu1504@gmail.com', 'huyendangmn10@gmail.com', 'nguyenthingoctuyenmn10@gmail.com', 'thuynguyencout@gmail.com', 'phungho121183@gmail.com']
    ],
    'Fam 18' => [
        'new_name' => 'fam_18_nhnhat229@gmail.com',
        'owner' => 'nhnhat229@gmail.com',
        'members' => ['laletuongan@gmail.com', 'kymngan2598@gmail.com', 'nguyenthihatien1808@gmail.com', 'vothithat1982@gmail.com']
    ],
    'Fam 19' => [
        'new_name' => 'fam_19_nhatphamchuai@gmail.com',
        'owner' => 'nhatphamchuai@gmail.com',
        'members' => ['kimanh24g@gmail.com', 'minhxuan1803@gmail.com', 'thanhthao210879@gmail.com', 'hoannm.globalleaders@gmail.com', 'mocsucmy2013@gmail.com']
    ],
    'Fam 20' => [
        'new_name' => 'fam_20_phamconvy@gmail.com',
        'owner' => 'phamconvy@gmail.com',
        'members' => ['dinhnguyenthaovy010823@gmail.com', 'luongthingocchau@gmail.com', 'minhtuanpro134@gmail.com', 'minhkhoibui19@gmail.com']
    ],
    'Fam 21' => [
        'new_name' => 'fam_21_phamconvy1@gmail.com',
        'owner' => 'phamconvy1@gmail.com',
        'members' => ['hanhuynh10523@gmail.com', 'trucquynh2265@gmail.com', 'duongtrungduong93@gmail.com', 'lienhueduong2014@gmail.com', 'huongduyminh76@gmail.com']
    ],
    'Fam 22' => [
        'new_name' => 'fam_22_phanconvy5@gmail.com',
        'owner' => 'phanconvy5@gmail.com',
        'members' => ['mnga2311@gmail.com', 'nguyenminhthu14022002@gmail.com', 'kimngoc.kentenglish@gmail.com', 'tqngan86@gmail.com', 'minhhuyen4292@gmail.com']
    ],
    'Fam 23' => [
        'new_name' => 'fam_23_phamconvy2@gmail.com',
        'owner' => 'phamconvy2@gmail.com',
        'members' => ['hienhathi28081980@gmail.com', 'tranthachkimvan@gmail.com', 'micccccc87@gmail.com', 'luongthaotran@gmail.com']
    ],
];

// Mapping từ owner email (trong DB) sang Fam name trong danh sách
$dbOwnerToUserFam = [
    'kiendtph49182@gmail.com' => 'Fam 1',      // DB Fam #35 -> Fam 1
    'lowkeyzz2008@gmail.com' => 'Fam 2',       // DB Fam #33 -> Fam 2
    'geminipro.1661@gmail.com' => 'Fam 3',     // DB Fam #36 -> Fam 3
    'khoathanhphuc@gmail.com' => 'Fam 6',      // DB Fam #38 -> Fam 6
    'kienchatgpt945@gmail.com' => 'Fam 7',     // DB Fam #39 -> Fam 7
    'vietanhc190@gmail.com' => 'Fam 8',        // DB Fam #40 -> Fam 8
    'anhvandz.2bn@gmail.com' => 'Fam 9',       // DB Fam #41 -> Fam 9
    'kien83667@gmail.com' => 'Fam 10',         // DB Fam #42 -> Fam 10
    'hainguyenthi2110@gmail.com' => 'Fam 11',  // DB Fam #43 -> Fam 11
    'dtkien18@gmail.com' => 'Fam 12',          // DB Fam #45 -> Fam 12
];

// Tạo mapping email -> đúng Family Name (từ danh sách người dùng)
$emailToCorrectFam = [];
foreach ($userFamilyList as $famName => $data) {
    $allEmails = array_merge([$data['owner']], $data['members']);
    foreach ($allEmails as $email) {
        $emailToCorrectFam[strtolower($email)] = $famName;
    }
}

echo "==========================================================\n";
echo "   SCRIPT SỬA FAMILY THEO DANH SÁCH\n";
echo "   Ngày: " . date('d/m/Y H:i:s') . "\n";
echo "==========================================================\n\n";

// BƯỚC 1: Lấy danh sách Family trong DB
echo "📋 BƯỚC 1: Lấy danh sách Family trong Database...\n";
$dbFamilies = DB::table('family_accounts')
    ->select('id', 'family_name', 'owner_email')
    ->get();

echo "   Tìm thấy " . count($dbFamilies) . " family trong DB\n\n";

// BƯỚC 2: Mapping DB Family ID -> User Fam Name
echo "📋 BƯỚC 2: Tạo mapping Family DB -> Danh sách...\n";
$dbFamIdToUserFam = [];
foreach ($dbFamilies as $fam) {
    $ownerEmail = strtolower($fam->owner_email);
    if (isset($dbOwnerToUserFam[$ownerEmail])) {
        $userFam = $dbOwnerToUserFam[$ownerEmail];
        $dbFamIdToUserFam[$fam->id] = [
            'user_fam' => $userFam,
            'new_name' => $userFamilyList[$userFam]['new_name'],
            'old_name' => $fam->family_name,
        ];
        echo "   DB Fam #{$fam->id} ({$fam->family_name}) -> {$userFam} ({$userFamilyList[$userFam]['new_name']})\n";
    }
}

// BƯỚC 3: Đổi tên Family trong DB
echo "\n📋 BƯỚC 3: Đổi tên Family theo format mới...\n";
$renamedCount = 0;
foreach ($dbFamIdToUserFam as $famId => $mapping) {
    $newName = $mapping['new_name'];
    $oldName = $mapping['old_name'];

    if ($oldName !== $newName) {
        DB::table('family_accounts')
            ->where('id', $famId)
            ->update(['family_name' => $newName]);
        echo "   ✅ Fam #{$famId}: '{$oldName}' -> '{$newName}'\n";
        $renamedCount++;
    } else {
        echo "   ⏭️ Fam #{$famId}: Đã đúng tên\n";
    }
}
echo "   Đã đổi tên {$renamedCount} family\n";

// BƯỚC 4: Lấy customer_services với Gemini + 2TB và sửa family_account_id
echo "\n📋 BƯỚC 4: Sửa email bị gán sai Family...\n";

$dbServices = DB::table('customer_services')
    ->select(
        'customer_services.id',
        'customer_services.login_email',
        'customer_services.family_account_id',
        'family_accounts.family_name as current_fam_name',
        'family_accounts.owner_email as current_owner_email'
    )
    ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
    ->leftJoin('family_accounts', 'customer_services.family_account_id', '=', 'family_accounts.id')
    ->where('service_packages.name', 'LIKE', '%Gemini%')
    ->where('service_packages.name', 'LIKE', '%2TB%')
    ->get();

$fixedCount = 0;
$notFixedEmails = [];

foreach ($dbServices as $service) {
    $email = strtolower($service->login_email);

    // Kiểm tra email có trong danh sách không
    if (isset($emailToCorrectFam[$email])) {
        $correctUserFam = $emailToCorrectFam[$email];
        $correctNewName = $userFamilyList[$correctUserFam]['new_name'];

        // Tìm Family ID trong DB tương ứng với correct Fam
        $correctFamId = null;
        foreach ($dbFamIdToUserFam as $famId => $mapping) {
            if ($mapping['user_fam'] === $correctUserFam) {
                $correctFamId = $famId;
                break;
            }
        }

        // Nếu family_account_id hiện tại khác với correct, thì sửa
        if ($correctFamId && $service->family_account_id != $correctFamId) {
            $oldFamId = $service->family_account_id ?? 'NULL';
            DB::table('customer_services')
                ->where('id', $service->id)
                ->update(['family_account_id' => $correctFamId]);
            echo "   ✅ Service #{$service->id} ({$email}): Fam #{$oldFamId} -> Fam #{$correctFamId} ({$correctUserFam})\n";
            $fixedCount++;
        }
    } else {
        $notFixedEmails[] = $email;
    }
}

echo "\n   Đã sửa {$fixedCount} email về đúng Family\n";

// Báo cáo email không có trong danh sách
if (count($notFixedEmails) > 0) {
    echo "\n⚠️ Các email trong DB nhưng KHÔNG có trong danh sách (chưa xử lý):\n";
    foreach (array_unique($notFixedEmails) as $email) {
        echo "   - {$email}\n";
    }
}

echo "\n==========================================================\n";
echo "✅ HOÀN THÀNH!\n";
echo "   - Đã đổi tên: {$renamedCount} family\n";
echo "   - Đã sửa family: {$fixedCount} email\n";
echo "==========================================================\n";
