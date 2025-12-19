<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\FamilyAccount;
use App\Models\CustomerService;

// User's expected list
$expectedList = <<<EOT
Fam 1,Chủ Fam,kiendtph49182@gmail.com
Fam 1,Thành viên,dinhtruongloc.1996@gmail.com
Fam 1,Thành viên,thailien.231197@gmail.com
Fam 1,Thành viên,thegioidecor12345@gmail.com
Fam 1,Thành viên,alexnguyen.mta.8889@gmail.com
Fam 1,Thành viên,tube238@gmail.com
Fam 2,Chủ Fam,lowkeyzz2008@gmail.com
Fam 2,Thành viên,tnphuonga2nd2018@gmail.com
Fam 2,Thành viên,htrung27791@gmail.com
Fam 2,Thành viên,nguyenthilien25091995@gmail.com
Fam 2,Thành viên,huethutrieuphu92@gmail.com
Fam 2,Thành viên,hoanglongpro1211@gmail.com
Fam 3,Chủ Fam,geminipro.1661@gmail.com
Fam 3,Thành viên,chungvietphuong2311@gmail.com
Fam 3,Thành viên,quypcna@gmail.com
Fam 3,Thành viên,beotoetls95@gmail.com
Fam 3,Thành viên,belleembrase@gmail.com
Fam 3,Thành viên,leengocthanhtruc@gmail.com
Fam 6,Chủ Fam,khoathanhphuc@gmail.com
Fam 6,Thành viên,tutbtv@gmail.com
Fam 6,Thành viên,vhnauy@gmail.com
Fam 6,Thành viên,sighhh1509@gmail.com
Fam 6,Thành viên,hungpchy.pixie@gmail.com
Fam 6,Thành viên,thanhphamxuan2042005@gmail.com
Fam 7,Chủ Fam,kienchatgpt945@gmail.com
Fam 7,Thành viên,m0903366155@gmail.com
Fam 7,Thành viên,huycao0356@gmail.com
Fam 7,Thành viên,nhakhoacourse@gmail.com
Fam 7,Thành viên,nhumanhkhanh@gmail.com
Fam 7,Thành viên,bonthui3005@gmail.com
Fam 8,Chủ Fam,vietanhc190@gmail.com
Fam 8,Thành viên,phamhieudong1705@gmail.com
Fam 8,Thành viên,nguyenngocsonkdol@gmail.com
Fam 8,Thành viên,minhhatlu@gmail.com
Fam 8,Thành viên,trungtuank54@gmail.com
Fam 8,Thành viên,hoangthanhbinh472001@gmail.com
Fam 9,Chủ Fam,anhvandz.2bn@gmail.com
Fam 9,Thành viên,videolophoc1@gmail.com
Fam 9,Thành viên,phattran.gen@gmail.com
Fam 9,Thành viên,lenhung1550@gmail.com
Fam 9,Thành viên,leanhthang0903@gmail.com
Fam 9,Thành viên,avbuilding18@gmail.com
Fam 10,Chủ Fam,kien83667@gmail.com
Fam 10,Thành viên,phansinhtung@gmail.com
Fam 10,Thành viên,letuananhhn@gmail.com
Fam 10,Thành viên,duchoa1349@gmail.com
Fam 10,Thành viên,baonamcenter555bn@gmail.com
Fam 10,Thành viên,bichhuyen1602@gmail.com
Fam 11,Chủ Fam,hainguyenthi2110@gmail.com
Fam 11,Thành viên,ngocnhung20011@gmail.com
Fam 11,Thành viên,kenjita1992@gmail.com
Fam 11,Thành viên,ngocnguyen115@gmail.com
Fam 11,Thành viên,ducminhle281201@gmail.com
Fam 12,Chủ Fam,dtkien18@gmail.com
Fam 12,Thành viên,leduongdc47@gmail.com
Fam 12,Thành viên,tienganhchobekids@gmail.com
Fam 12,Thành viên,vungan291999@gmail.com
Fam 12,Thành viên,jennyphan.popodoo@gmail.com
Fam 14,Chủ Fam,thytiensfs@gmail.com
Fam 14,Thành viên,vivuive2494@gmail.com
Fam 14,Thành viên,cuccangio@gmail.com
Fam 14,Thành viên,hoivocr@gmail.com
Fam 14,Thành viên,holleybuimn10@gmail.com
Fam 14,Thành viên,trinhhongnhung94@gmail.com
Fam 15,Chủ Fam,nhatnguyenskibidu@gmail.com
Fam 15,Thành viên,hactiensinh127@gmail.com
Fam 15,Thành viên,ngocthuong195@gmail.com
Fam 15,Thành viên,trangnguyenthuy1979@gmail.com
Fam 15,Thành viên,lethingoclan6780@gmail.com
Fam 15,Thành viên,tnqt1082@gmail.com
Fam 16,Chủ Fam,nhai76755@gmail.com
Fam 16,Thành viên,luubaolinh.mnhoamai@gmail.com
Fam 16,Thành viên,nguyenthixuanbach1976@gmail.com
Fam 16,Thành viên,nguyenthingoctram0909993186@gmail.com
Fam 16,Thành viên,lethanhhien7674@gmail.com
Fam 16,Thành viên,thongth@gmail.com
Fam 17,Chủ Fam,ngtducsfs@gmail.com
Fam 17,Thành viên,y.tranthinhu1504@gmail.com
Fam 17,Thành viên,huyendangmn10@gmail.com
Fam 17,Thành viên,nguyenthingoctuyenmn10@gmail.com
Fam 17,Thành viên,thuynguyencout@gmail.com
Fam 17,Thành viên,phungho121183@gmail.com
Fam 18,Chủ Fam,nhnhat229@gmail.com
Fam 18,Thành viên,laletuongan@gmail.com
Fam 18,Thành viên,kymngan2598@gmail.com
Fam 18,Thành viên,nguyenthihatien1808@gmail.com
Fam 18,Thành viên,vothithat1982@gmail.com
Fam 19,Chủ Fam,nhatphamchuai@gmail.com
Fam 19,Thành viên,kimanh24g@gmail.com
Fam 19,Thành viên,minhxuan1803@gmail.com
Fam 19,Thành viên,thanhthao210879@gmail.com
Fam 19,Thành viên,hoannm.globalleaders@gmail.com
Fam 19,Thành viên,mocsucmy2013@gmail.com
Fam 20,Chủ Fam,phamconvy@gmail.com
Fam 20,Thành viên,dinhnguyenthaovy010823@gmail.com
Fam 20,Thành viên,luongthingocchau@gmail.com
Fam 20,Thành viên,minhtuanpro134@gmail.com
Fam 20,Thành viên,minhkhoibui19@gmail.com
Fam 21,Chủ Fam,phamconvy1@gmail.com
Fam 21,Thành viên,hanhuynh10523@gmail.com
Fam 21,Thành viên,trucquynh2265@gmail.com
Fam 21,Thành viên,duongtrungduong93@gmail.com
Fam 21,Thành viên,lienhueduong2014@gmail.com
Fam 21,Thành viên,huongduyminh76@gmail.com
Fam 22,Chủ Fam,phanconvy5@gmail.com
Fam 22,Thành viên,mnga2311@gmail.com
Fam 22,Thành viên,nguyenminhthu14022002@gmail.com
Fam 22,Thành viên,kimngoc.kentenglish@gmail.com
Fam 22,Thành viên,tqngan86@gmail.com
Fam 22,Thành viên,minhhuyen4292@gmail.com
Fam 23,Chủ Fam,phamconvy2@gmail.com
Fam 23,Thành viên,hienhathi28081980@gmail.com
Fam 23,Thành viên,tranthachkimvan@gmail.com
Fam 23,Thành viên,micccccc87@gmail.com
Fam 23,Thành viên,luongthaotran@gmail.com
EOT;

// Map Fam numbers to database IDs
$famMapping = [
    1 => 35,   // fam_01_kiendtph49182@gmail.com
    2 => 33,   // fam_02_lowkeyzz2008@gmail.com
    3 => 36,   // fam_03_geminipro.1661@gmail.com
    6 => 38,   // fam_06_khoathanhphuc@gmail.com
    7 => 39,   // fam_07_kienchatgpt945@gmail.com
    8 => 40,   // fam_08_vietanhc190@gmail.com
    9 => 41,   // fam_09_anhvandz.2bn@gmail.com
    10 => 42,  // fam_10_kien83667@gmail.com
    11 => 43,  // fam_11_hainguyenthi2110@gmail.com
    12 => 45,  // fam_12_dtkien18@gmail.com
    14 => 48,  // fam_14_thytiensfs@gmail.com
    15 => 49,  // fam_15_nhatnguyenskibidu@gmail.com
    16 => 50,  // fam_16_nhai76755@gmail.com
    17 => 51,  // fam_17_ngtducsfs@gmail.com
    18 => 52,  // fam_18_nhnhat229@gmail.com
    19 => 53,  // fam_19_nhatphamchuai@gmail.com
    20 => 54,  // fam_20_phamconvy@gmail.com
    21 => 55,  // fam_21_phamconvy1@gmail.com
    22 => 56,  // fam_22_phanconvy5@gmail.com
    23 => 57,  // fam_23_phamconvy2@gmail.com
];

// Parse expected list
$expected = [];
$lines = explode("\n", trim($expectedList));
foreach ($lines as $line) {
    $parts = str_getcsv($line);
    if (count($parts) >= 3) {
        preg_match('/Fam (\d+)/', $parts[0], $matches);
        $famNum = (int)$matches[1];
        $role = trim($parts[1]);
        $email = strtolower(trim($parts[2]));
        
        if (!isset($expected[$famNum])) {
            $expected[$famNum] = ['owner' => null, 'members' => []];
        }
        
        if ($role === 'Chủ Fam') {
            $expected[$famNum]['owner'] = $email;
        } else {
            $expected[$famNum]['members'][] = $email;
        }
    }
}

// Get actual data from database
$differences = [];
$totalExpected = 0;
$totalActual = 0;
$totalMatch = 0;

echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                    SO SÁNH DỮ LIỆU FAMILY ACCOUNTS                          ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n\n";

foreach ($expected as $famNum => $data) {
    $dbId = $famMapping[$famNum] ?? null;
    
    if (!$dbId) {
        echo "⚠️  Fam {$famNum}: Không tìm thấy trong mapping!\n";
        continue;
    }
    
    $family = FamilyAccount::with('customerServices.customer')->find($dbId);
    
    if (!$family) {
        echo "❌ Fam {$famNum} (DB ID: {$dbId}): Không tồn tại trong database!\n";
        continue;
    }
    
    // Get actual emails from database
    $actualOwner = strtolower($family->owner_email);
    $actualMembers = $family->customerServices
        ->where('status', 'active')
        ->map(function($s) {
            return strtolower($s->login_email ?: ($s->customer->email ?? ''));
        })
        ->filter()
        ->unique()
        ->values()
        ->toArray();
    
    $expectedOwner = $data['owner'];
    $expectedMembers = $data['members'];
    
    $hasIssue = false;
    $famIssues = [];
    
    // Check owner
    if ($expectedOwner !== $actualOwner) {
        $hasIssue = true;
        $famIssues[] = "  📧 Chủ Fam khác nhau:";
        $famIssues[] = "     - Mong đợi: {$expectedOwner}";
        $famIssues[] = "     - Thực tế:  {$actualOwner}";
    }
    
    // Find missing members (in expected but not in actual)
    $missing = array_diff($expectedMembers, $actualMembers);
    if (!empty($missing)) {
        $hasIssue = true;
        $famIssues[] = "  ❌ THIẾU trong DB (có trong danh sách nhưng không có trong DB):";
        foreach ($missing as $email) {
            $famIssues[] = "     - {$email}";
        }
    }
    
    // Find extra members (in actual but not in expected)
    $extra = array_diff($actualMembers, $expectedMembers);
    // Also remove owner from extra
    $extra = array_filter($extra, fn($e) => $e !== $expectedOwner && $e !== $actualOwner);
    if (!empty($extra)) {
        $hasIssue = true;
        $famIssues[] = "  ➕ THỪA trong DB (có trong DB nhưng không có trong danh sách):";
        foreach ($extra as $email) {
            $famIssues[] = "     - {$email}";
        }
    }
    
    // Count stats
    $totalExpected += count($expectedMembers) + 1; // +1 for owner
    $totalActual += count($actualMembers) + 1;
    
    if ($hasIssue) {
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "🔴 Fam {$famNum} ({$family->family_name}) - ID: {$dbId}\n";
        echo "   Mong đợi: " . (count($expectedMembers) + 1) . " người | Thực tế: " . (count($actualMembers) + 1) . " người\n";
        foreach ($famIssues as $issue) {
            echo $issue . "\n";
        }
        $differences[$famNum] = [
            'missing' => $missing,
            'extra' => array_values($extra),
            'owner_mismatch' => $expectedOwner !== $actualOwner
        ];
    } else {
        $totalMatch++;
        echo "✅ Fam {$famNum}: Khớp hoàn toàn (" . (count($expectedMembers) + 1) . " thành viên)\n";
    }
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 TỔNG KẾT:\n";
echo "   - Tổng số Family kiểm tra: " . count($expected) . "\n";
echo "   - Family khớp hoàn toàn: " . $totalMatch . "\n";
echo "   - Family có sai lệch: " . (count($expected) - $totalMatch) . "\n";

if (!empty($differences)) {
    $totalMissing = 0;
    $totalExtra = 0;
    foreach ($differences as $d) {
        $totalMissing += count($d['missing']);
        $totalExtra += count($d['extra']);
    }
    echo "   - Tổng số email THIẾU: {$totalMissing}\n";
    echo "   - Tổng số email THỪA: {$totalExtra}\n";
}

echo "\n";

