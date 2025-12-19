<?php

/**
 * Script tạo tất cả Family theo danh sách và cập nhật email
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "==========================================================\n";
echo "   TẠO VÀ CẬP NHẬT FAMILY THEO DANH SÁCH\n";
echo "   Ngày: " . date('d/m/Y H:i:s') . "\n";
echo "==========================================================\n\n";

// BƯỚC 1: Cập nhật email theo yêu cầu
echo "📋 BƯỚC 1: Cập nhật email theo yêu cầu...\n";

$emailUpdates = [
    // hoangnamhg1212@gmail.com đổi thành tsukyo999@gmail.com
    ['old' => 'hoangnamhg1212@gmail.com', 'new' => 'tsukyo999@gmail.com'],
    // phamthiphuonganh9999@gmail.com thay bằng Truymenhqn1@gmail.com
    ['old' => 'phamthiphuonganh9999@gmail.com', 'new' => 'Truymenhqn1@gmail.com'],
    // thekhiem333@gmail.com thay bằng hactiensinh127@gmail.com
    ['old' => 'thekhiem333@gmail.com', 'new' => 'hactiensinh127@gmail.com'],
    // quachanhmaker@gmail.com của Thongth Petrolimex thay bằng thongth@gmail.com
    ['old' => 'quachanhmaker@gmail.com', 'new' => 'thongth@gmail.com', 'customer_name' => 'Thongth Petrolimex'],
];

foreach ($emailUpdates as $update) {
    $query = DB::table('customer_services')
        ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
        ->where('service_packages.name', 'LIKE', '%Gemini%')
        ->where('service_packages.name', 'LIKE', '%2TB%')
        ->whereRaw('LOWER(customer_services.login_email) = ?', [strtolower($update['old'])]);

    // Nếu có customer_name, thêm điều kiện
    if (isset($update['customer_name'])) {
        $query->join('customers', 'customer_services.customer_id', '=', 'customers.id')
            ->where('customers.name', 'LIKE', '%' . $update['customer_name'] . '%');
    }

    $serviceIds = $query->pluck('customer_services.id');

    if ($serviceIds->isNotEmpty()) {
        DB::table('customer_services')
            ->whereIn('id', $serviceIds)
            ->update(['login_email' => $update['new']]);
        echo "   ✅ {$update['old']} -> {$update['new']} (" . count($serviceIds) . " bản ghi)\n";
    } else {
        echo "   ⚠️ Không tìm thấy: {$update['old']}\n";
    }
}

// BƯỚC 2: Danh sách Family cần tạo/cập nhật
echo "\n📋 BƯỚC 2: Tạo tất cả Family theo danh sách...\n";

$familyList = [
    'fam_01_kiendtph49182@gmail.com' => 'kiendtph49182@gmail.com',
    'fam_02_lowkeyzz2008@gmail.com' => 'lowkeyzz2008@gmail.com',
    'fam_03_geminipro.1661@gmail.com' => 'geminipro.1661@gmail.com',
    'fam_04_quachanhmaker@gmail.com' => 'quachanhmaker@gmail.com',  // Fam #32 - cần đổi tên
    'fam_05_buimyst952485@gmail.com' => 'buimyst952485@gmail.com',  // Fam #34 - cần đổi tên
    'fam_06_khoathanhphuc@gmail.com' => 'khoathanhphuc@gmail.com',
    'fam_07_kienchatgpt945@gmail.com' => 'kienchatgpt945@gmail.com',
    'fam_08_vietanhc190@gmail.com' => 'vietanhc190@gmail.com',
    'fam_09_anhvandz.2bn@gmail.com' => 'anhvandz.2bn@gmail.com',
    'fam_10_kien83667@gmail.com' => 'kien83667@gmail.com',
    'fam_11_hainguyenthi2110@gmail.com' => 'hainguyenthi2110@gmail.com',
    'fam_12_dtkien18@gmail.com' => 'dtkien18@gmail.com',
    'fam_14_thytiensfs@gmail.com' => 'thytiensfs@gmail.com',
    'fam_15_nhatnguyenskibidu@gmail.com' => 'nhatnguyenskibidu@gmail.com',
    'fam_16_nhai76755@gmail.com' => 'nhai76755@gmail.com',
    'fam_17_ngtducsfs@gmail.com' => 'ngtducsfs@gmail.com',
    'fam_18_nhnhat229@gmail.com' => 'nhnhat229@gmail.com',
    'fam_19_nhatphamchuai@gmail.com' => 'nhatphamchuai@gmail.com',
    'fam_20_phamconvy@gmail.com' => 'phamconvy@gmail.com',
    'fam_21_phamconvy1@gmail.com' => 'phamconvy1@gmail.com',
    'fam_22_phanconvy5@gmail.com' => 'phanconvy5@gmail.com',
    'fam_23_phamconvy2@gmail.com' => 'phamconvy2@gmail.com',
];

// Mapping owner_email hiện tại trong DB với family name mới
$ownerToNewFamName = [
    'kiendtph49182@gmail.com' => 'fam_01_kiendtph49182@gmail.com',
    'lowkeyzz2008@gmail.com' => 'fam_02_lowkeyzz2008@gmail.com',
    'geminipro.1661@gmail.com' => 'fam_03_geminipro.1661@gmail.com',
    'quachanhmaker@gmail.com' => 'fam_04_quachanhmaker@gmail.com',
    'buimyst952485@gmail.com' => 'fam_05_buimyst952485@gmail.com',
    'khoathanhphuc@gmail.com' => 'fam_06_khoathanhphuc@gmail.com',
    'kienchatgpt945@gmail.com' => 'fam_07_kienchatgpt945@gmail.com',
    'vietanhc190@gmail.com' => 'fam_08_vietanhc190@gmail.com',
    'anhvandz.2bn@gmail.com' => 'fam_09_anhvandz.2bn@gmail.com',
    'kien83667@gmail.com' => 'fam_10_kien83667@gmail.com',
    'hainguyenthi2110@gmail.com' => 'fam_11_hainguyenthi2110@gmail.com',
    'dtkien18@gmail.com' => 'fam_12_dtkien18@gmail.com',
];

// Đổi tên Family #32 và #34
$famUpdates = [
    32 => 'fam_04_quachanhmaker@gmail.com',
    34 => 'fam_05_buimyst952485@gmail.com',
];

foreach ($famUpdates as $famId => $newName) {
    $fam = DB::table('family_accounts')->where('id', $famId)->first();
    if ($fam && $fam->family_name !== $newName) {
        DB::table('family_accounts')
            ->where('id', $famId)
            ->update(['family_name' => $newName]);
        echo "   ✅ Family #{$famId}: '{$fam->family_name}' -> '{$newName}'\n";
    }
}

// Lấy gói Gemini Pro + 2TB
$geminiPackage = DB::table('service_packages')
    ->where('name', 'LIKE', '%Gemini%')
    ->where('name', 'LIKE', '%2TB%')
    ->first();

$packageId = $geminiPackage ? $geminiPackage->id : null;
echo "   Gói Gemini: " . ($geminiPackage ? "#{$geminiPackage->id} - {$geminiPackage->name}" : "Không tìm thấy") . "\n";

// Tạo các Family mới chưa có trong DB
$newFamilies = [
    'fam_14_thytiensfs@gmail.com' => 'thytiensfs@gmail.com',
    'fam_15_nhatnguyenskibidu@gmail.com' => 'nhatnguyenskibidu@gmail.com',
    'fam_16_nhai76755@gmail.com' => 'nhai76755@gmail.com',
    'fam_17_ngtducsfs@gmail.com' => 'ngtducsfs@gmail.com',
    'fam_18_nhnhat229@gmail.com' => 'nhnhat229@gmail.com',
    'fam_19_nhatphamchuai@gmail.com' => 'nhatphamchuai@gmail.com',
    'fam_20_phamconvy@gmail.com' => 'phamconvy@gmail.com',
    'fam_21_phamconvy1@gmail.com' => 'phamconvy1@gmail.com',
    'fam_22_phanconvy5@gmail.com' => 'phanconvy5@gmail.com',
    'fam_23_phamconvy2@gmail.com' => 'phamconvy2@gmail.com',
];

echo "\n📋 BƯỚC 3: Tạo các Family mới...\n";

foreach ($newFamilies as $famName => $ownerEmail) {
    // Kiểm tra xem đã có family với owner này chưa
    $existingFam = DB::table('family_accounts')
        ->whereRaw('LOWER(owner_email) = ?', [strtolower($ownerEmail)])
        ->first();

    if (!$existingFam) {
        // Tạo family_code từ tên family
        $famCode = strtoupper(str_replace(['@gmail.com', '.', '_'], ['', '', '-'], $famName));
        $newId = DB::table('family_accounts')->insertGetId([
            'family_name' => $famName,
            'family_code' => $famCode,
            'owner_email' => $ownerEmail,
            'owner_name' => $ownerEmail,
            'service_package_id' => $packageId,
            'max_members' => 6,
            'current_members' => 0,
            'status' => 'active',
            'activated_at' => now(),
            'expires_at' => now()->addYear(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "   ✅ Tạo mới: {$famName} (ID: #{$newId})\n";
    } else {
        echo "   ⏭️ Đã tồn tại: {$ownerEmail} (Family #{$existingFam->id})\n";
    }
}

// BƯỚC 4: Cập nhật thành viên vào đúng Family
echo "\n📋 BƯỚC 4: Gán thành viên vào đúng Family...\n";

// Mapping email thành viên -> owner email của Family
$memberToFamily = [
    // Fam 1 - kiendtph49182@gmail.com
    'dinhtruongloc.1996@gmail.com' => 'kiendtph49182@gmail.com',
    'thailien.231197@gmail.com' => 'kiendtph49182@gmail.com',
    'alexnguyen.mta.8889@gmail.com' => 'kiendtph49182@gmail.com',
    'tube238@gmail.com' => 'kiendtph49182@gmail.com',

    // Fam 2 - lowkeyzz2008@gmail.com
    'tnphuonga2nd2018@gmail.com' => 'lowkeyzz2008@gmail.com',
    'htrung27791@gmail.com' => 'lowkeyzz2008@gmail.com',
    'nguyenthilien25091995@gmail.com' => 'lowkeyzz2008@gmail.com',
    'huethutrieuphu92@gmail.com' => 'lowkeyzz2008@gmail.com',
    'hoanglongpro121@gmail.com' => 'lowkeyzz2008@gmail.com',  // hoanglongpro1211 -> Fam 2

    // Fam 3 - geminipro.1661@gmail.com
    'chungvietphuong2311@gmail.com' => 'geminipro.1661@gmail.com',
    'quypcna@gmail.com' => 'geminipro.1661@gmail.com',

    // Fam 4 - quachanhmaker@gmail.com
    'duongdtvn2@gmail.com' => 'quachanhmaker@gmail.com',
    'tqngan86@gmail.com' => 'quachanhmaker@gmail.com',
    'quachanhmaker@gmail.com' => 'quachanhmaker@gmail.com',

    // Fam 5 - buimyst952485@gmail.com
    'tsukyo999@gmail.com' => 'buimyst952485@gmail.com',  // đã đổi từ hoangnamhg1212
    'truymenhqn1@gmail.com' => 'buimyst952485@gmail.com',  // đã đổi từ phamthiphuonganh9999
    'miniriviu@gmail.com' => 'buimyst952485@gmail.com',
    'kimngoc.kentenglish@gmail.com' => 'buimyst952485@gmail.com',
    'minhtuanpro134@gmail.com' => 'buimyst952485@gmail.com',

    // Fam 6 - khoathanhphuc@gmail.com
    'tutbtv@gmail.com' => 'khoathanhphuc@gmail.com',
    'vhnauy@gmail.com' => 'khoathanhphuc@gmail.com',
    'sighhh1509@gmail.com' => 'khoathanhphuc@gmail.com',
    'hungpchy.pixie@gmail.com' => 'khoathanhphuc@gmail.com',
    'thanhphamxuan2042005@gmail.com' => 'khoathanhphuc@gmail.com',

    // Fam 7 - kienchatgpt945@gmail.com
    'm0903366155@gmail.com' => 'kienchatgpt945@gmail.com',
    'huycao0356@gmail.com' => 'kienchatgpt945@gmail.com',
    'nhakhoacourse@gmail.com' => 'kienchatgpt945@gmail.com',
    'nhumanhkhanh@gmail.com' => 'kienchatgpt945@gmail.com',
    'thegioidecor12345@gmail.com' => 'kienchatgpt945@gmail.com',

    // Fam 8 - vietanhc190@gmail.com
    'nguyenngocsonkdol@gmail.com' => 'vietanhc190@gmail.com',
    'minhhatlu@gmail.com' => 'vietanhc190@gmail.com',
    'trungtuank54@gmail.com' => 'vietanhc190@gmail.com',
    'hoangthanhbinh472001@gmail.com' => 'vietanhc190@gmail.com',
    'beotoetls95@gmail.com' => 'vietanhc190@gmail.com',

    // Fam 9 - anhvandz.2bn@gmail.com
    'videolophoc1@gmail.com' => 'anhvandz.2bn@gmail.com',
    'phattran.gen@gmail.com' => 'anhvandz.2bn@gmail.com',
    'lenhung1550@gmail.com' => 'anhvandz.2bn@gmail.com',
    'leanhthang0903@gmail.com' => 'anhvandz.2bn@gmail.com',
    'avbuilding18@gmail.com' => 'anhvandz.2bn@gmail.com',

    // Fam 10 - kien83667@gmail.com
    'phansinhtung@gmail.com' => 'kien83667@gmail.com',
    'duchoa1349@gmail.com' => 'kien83667@gmail.com',
    'bichhuyen1602@gmail.com' => 'kien83667@gmail.com',

    // Fam 11 - hainguyenthi2110@gmail.com
    'ngocnhung20011@gmail.com' => 'hainguyenthi2110@gmail.com',
    'kenjita1992@gmail.com' => 'hainguyenthi2110@gmail.com',
    'ngocnguyen115@gmail.com' => 'hainguyenthi2110@gmail.com',
    'ducminhle281201@gmail.com' => 'hainguyenthi2110@gmail.com',

    // Fam 12 - dtkien18@gmail.com
    'leduongdc47@gmail.com' => 'dtkien18@gmail.com',
    'tienganhchobekids@gmail.com' => 'dtkien18@gmail.com',
    'vungan291999@gmail.com' => 'dtkien18@gmail.com',

    // Fam 15 - nhatnguyenskibidu@gmail.com
    'hactiensinh127@gmail.com' => 'nhatnguyenskibidu@gmail.com',  // đã đổi từ thekhiem333

    // Fam 16 - nhai76755@gmail.com
    'thongth@gmail.com' => 'nhai76755@gmail.com',  // đã đổi từ quachanhmaker của Thongth
];

// Lấy tất cả family
$allFamilies = DB::table('family_accounts')
    ->select('id', 'owner_email')
    ->get()
    ->keyBy(function ($item) {
        return strtolower($item->owner_email);
    });

$updatedCount = 0;
foreach ($memberToFamily as $memberEmail => $ownerEmail) {
    $ownerEmailLower = strtolower($ownerEmail);

    if (!isset($allFamilies[$ownerEmailLower])) {
        echo "   ⚠️ Family không tồn tại cho: {$ownerEmail}\n";
        continue;
    }

    $correctFamId = $allFamilies[$ownerEmailLower]->id;

    // Tìm service với email này
    $services = DB::table('customer_services')
        ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
        ->where('service_packages.name', 'LIKE', '%Gemini%')
        ->where('service_packages.name', 'LIKE', '%2TB%')
        ->whereRaw('LOWER(customer_services.login_email) = ?', [strtolower($memberEmail)])
        ->select('customer_services.id', 'customer_services.family_account_id')
        ->get();

    foreach ($services as $service) {
        if ($service->family_account_id != $correctFamId) {
            DB::table('customer_services')
                ->where('id', $service->id)
                ->update(['family_account_id' => $correctFamId]);
            echo "   ✅ {$memberEmail}: Fam #{$service->family_account_id} -> Fam #{$correctFamId}\n";
            $updatedCount++;
        }
    }
}

echo "\n   Đã cập nhật {$updatedCount} email vào đúng Family\n";

// BƯỚC 5: Xóa Family #47 (ytb 1 thang) nếu không còn thành viên
echo "\n📋 BƯỚC 5: Kiểm tra Family không còn sử dụng...\n";
$fam47Members = DB::table('customer_services')
    ->where('family_account_id', 47)
    ->count();

if ($fam47Members == 0) {
    // DB::table('family_accounts')->where('id', 47)->delete();
    echo "   ⚠️ Family #47 không còn thành viên (có thể xóa)\n";
} else {
    echo "   Family #47 còn {$fam47Members} thành viên\n";
}

echo "\n==========================================================\n";
echo "✅ HOÀN THÀNH!\n";
echo "==========================================================\n";
