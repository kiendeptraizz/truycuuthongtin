<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\FamilyAccount;
use App\Models\CustomerService;
use App\Models\Customer;
use App\Models\ServicePackage;
use Illuminate\Support\Facades\DB;

// Get Gemini Pro service package
$geminiPackage = ServicePackage::where('name', 'like', '%Gemini Pro%2TB%')->first();
if (!$geminiPackage) {
    echo "❌ Không tìm thấy gói Gemini Pro + 2TB!\n";
    // List available packages
    $packages = ServicePackage::where('name', 'like', '%gemini%')->get();
    foreach ($packages as $p) {
        echo "  - ID:{$p->id} | {$p->name}\n";
    }
    exit(1);
}
echo "✅ Gói dịch vụ: {$geminiPackage->name} (ID: {$geminiPackage->id})\n\n";

// Dates
$activatedAt = \Carbon\Carbon::parse('2025-12-10');
$expiresAt = \Carbon\Carbon::parse('2026-01-10');

// Family mapping
$famMapping = [
    1 => 35, 2 => 33, 3 => 36, 6 => 38, 7 => 39,
    8 => 40, 9 => 41, 10 => 42, 11 => 43, 12 => 45,
    14 => 48, 15 => 49, 16 => 50, 17 => 51, 18 => 52,
    19 => 53, 20 => 54, 21 => 55, 22 => 56, 23 => 57,
];

$fixes = [
    // Fam 2: Fix typo in email
    2 => [
        'fix_email' => ['hoanglongpro121@gmail.com' => 'hoanglongpro1211@gmail.com'],
    ],
    // Fam 3: Add 3 missing, move beotoetls95 from Fam 8
    3 => [
        'add' => ['beotoetls95@gmail.com', 'belleembrase@gmail.com', 'leengocthanhtruc@gmail.com'],
    ],
    // Fam 8: Remove beotoetls95 (moving to Fam 3), add phamhieudong1705
    8 => [
        'remove' => ['beotoetls95@gmail.com'],
        'add' => ['phamhieudong1705@gmail.com'],
    ],
    // Fam 10: Add 2 missing
    10 => [
        'add' => ['letuananhhn@gmail.com', 'baonamcenter555bn@gmail.com'],
    ],
    // Fam 12: Remove strange email, add jennyphan
    12 => [
        'remove' => ['uxboslbmgrnaaaltinerte@nss.tinhochay.org'],
        'add' => ['jennyphan.popodoo@gmail.com'],
    ],
    // Fam 14-23: Add all missing members
    14 => [
        'add' => ['vivuive2494@gmail.com', 'cuccangio@gmail.com', 'hoivocr@gmail.com', 'holleybuimn10@gmail.com', 'trinhhongnhung94@gmail.com'],
    ],
    15 => [
        'add' => ['ngocthuong195@gmail.com', 'trangnguyenthuy1979@gmail.com', 'lethingoclan6780@gmail.com', 'tnqt1082@gmail.com'],
    ],
    16 => [
        'add' => ['luubaolinh.mnhoamai@gmail.com', 'nguyenthixuanbach1976@gmail.com', 'nguyenthingoctram0909993186@gmail.com', 'lethanhhien7674@gmail.com'],
    ],
    17 => [
        'add' => ['y.tranthinhu1504@gmail.com', 'huyendangmn10@gmail.com', 'nguyenthingoctuyenmn10@gmail.com', 'thuynguyencout@gmail.com', 'phungho121183@gmail.com'],
    ],
    18 => [
        'add' => ['laletuongan@gmail.com', 'kymngan2598@gmail.com', 'nguyenthihatien1808@gmail.com', 'vothithat1982@gmail.com'],
    ],
    19 => [
        'add' => ['kimanh24g@gmail.com', 'minhxuan1803@gmail.com', 'thanhthao210879@gmail.com', 'hoannm.globalleaders@gmail.com', 'mocsucmy2013@gmail.com'],
    ],
    20 => [
        'add' => ['dinhnguyenthaovy010823@gmail.com', 'luongthingocchau@gmail.com', 'minhtuanpro134@gmail.com', 'minhkhoibui19@gmail.com'],
    ],
    21 => [
        'add' => ['hanhuynh10523@gmail.com', 'trucquynh2265@gmail.com', 'duongtrungduong93@gmail.com', 'lienhueduong2014@gmail.com', 'huongduyminh76@gmail.com'],
    ],
    22 => [
        'add' => ['mnga2311@gmail.com', 'nguyenminhthu14022002@gmail.com', 'kimngoc.kentenglish@gmail.com', 'tqngan86@gmail.com', 'minhhuyen4292@gmail.com'],
    ],
    23 => [
        'add' => ['hienhathi28081980@gmail.com', 'tranthachkimvan@gmail.com', 'micccccc87@gmail.com', 'luongthaotran@gmail.com'],
    ],
];

// Helper function to generate customer name from email
function generateNameFromEmail($email) {
    $localPart = explode('@', $email)[0];
    // Remove numbers and special chars, capitalize
    $name = preg_replace('/[0-9._-]+/', ' ', $localPart);
    $name = ucwords(trim($name));
    if (empty($name)) {
        $name = ucfirst($localPart);
    }
    return $name;
}

// Helper function to find or create customer
function findOrCreateCustomer($email, $name = null) {
    // Search by email first
    $customer = Customer::where('email', strtolower($email))->first();
    if ($customer) {
        return $customer;
    }
    
    // Search by name matching email pattern
    $customer = Customer::where('name', 'like', '%' . explode('@', $email)[0] . '%')->first();
    if ($customer) {
        return $customer;
    }
    
    // Create new customer
    $customerName = $name ?: generateNameFromEmail($email);
    $customer = Customer::create([
        'name' => $customerName,
        'email' => strtolower($email),
    ]);
    
    echo "     📝 Tạo khách hàng mới: {$customer->name} ({$customer->customer_code})\n";
    return $customer;
}

// Helper function to find or create customer service
function findOrCreateService($customer, $email, $familyId, $packageId, $activatedAt, $expiresAt) {
    // Check if service already exists
    $existing = CustomerService::where('customer_id', $customer->id)
        ->where('family_account_id', $familyId)
        ->where('service_package_id', $packageId)
        ->where('status', 'active')
        ->first();
    
    if ($existing) {
        echo "     ⚠️  Dịch vụ đã tồn tại cho {$email}\n";
        return $existing;
    }
    
    // Check if there's a service with this login_email but different family
    $existingByEmail = CustomerService::where('login_email', 'like', $email)
        ->where('service_package_id', $packageId)
        ->where('status', 'active')
        ->first();
    
    if ($existingByEmail) {
        // Update family_account_id
        $existingByEmail->family_account_id = $familyId;
        $existingByEmail->save();
        echo "     🔄 Chuyển dịch vụ {$email} sang Family ID {$familyId}\n";
        return $existingByEmail;
    }
    
    // Create new service
    $service = CustomerService::create([
        'customer_id' => $customer->id,
        'service_package_id' => $packageId,
        'family_account_id' => $familyId,
        'login_email' => strtolower($email),
        'activated_at' => $activatedAt,
        'expires_at' => $expiresAt,
        'status' => 'active',
    ]);
    
    echo "     ✅ Tạo dịch vụ mới: {$email}\n";
    return $service;
}

echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                    SỬA LỖI FAMILY ACCOUNTS                                  ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n\n";

DB::beginTransaction();

try {
    $totalAdded = 0;
    $totalRemoved = 0;
    $totalFixed = 0;
    
    foreach ($fixes as $famNum => $actions) {
        $dbId = $famMapping[$famNum];
        $family = FamilyAccount::find($dbId);
        
        if (!$family) {
            echo "❌ Fam {$famNum}: Không tìm thấy Family ID {$dbId}\n";
            continue;
        }
        
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "📂 Fam {$famNum} ({$family->family_name}) - ID: {$dbId}\n";
        
        // Fix email typos
        if (isset($actions['fix_email'])) {
            foreach ($actions['fix_email'] as $oldEmail => $newEmail) {
                $service = CustomerService::where('login_email', 'like', $oldEmail)
                    ->where('family_account_id', $dbId)
                    ->first();
                
                if ($service) {
                    $service->login_email = $newEmail;
                    $service->save();
                    echo "  🔧 Sửa email: {$oldEmail} → {$newEmail}\n";
                    $totalFixed++;
                } else {
                    echo "  ⚠️  Không tìm thấy {$oldEmail} để sửa\n";
                }
            }
        }
        
        // Remove unwanted services
        if (isset($actions['remove'])) {
            foreach ($actions['remove'] as $email) {
                $service = CustomerService::where('login_email', 'like', $email)
                    ->where('family_account_id', $dbId)
                    ->first();
                
                if ($service) {
                    $service->family_account_id = null;
                    $service->save();
                    echo "  🗑️  Gỡ bỏ: {$email} khỏi Family\n";
                    $totalRemoved++;
                }
            }
        }
        
        // Add new members
        if (isset($actions['add'])) {
            foreach ($actions['add'] as $email) {
                echo "  ➕ Thêm: {$email}\n";
                
                $customer = findOrCreateCustomer($email);
                findOrCreateService(
                    $customer, 
                    $email, 
                    $dbId, 
                    $geminiPackage->id, 
                    $activatedAt, 
                    $expiresAt
                );
                $totalAdded++;
            }
        }
    }
    
    DB::commit();
    
    echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "✅ HOÀN THÀNH!\n";
    echo "   - Email đã sửa: {$totalFixed}\n";
    echo "   - Email đã gỡ bỏ: {$totalRemoved}\n";
    echo "   - Email đã thêm: {$totalAdded}\n";
    
} catch (\Exception $e) {
    DB::rollback();
    echo "\n❌ LỖI: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

