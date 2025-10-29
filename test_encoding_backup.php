<?php

/**
 * Test Backup & Restore Encoding
 * Kiểm tra xem backup và restore có giữ nguyên encoding UTF8MB4 cho tiếng Việt không
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Customer;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing Backup & Restore Encoding...\n\n";

// Test 1: Kiểm tra charset hiện tại của database
echo "📊 Current Database Charset:\n";
$charset = DB::select("SELECT @@character_set_database, @@collation_database")[0];
echo "Character Set: " . $charset->{'@@character_set_database'} . "\n";
echo "Collation: " . $charset->{'@@collation_database'} . "\n\n";

// Test 2: Kiểm tra charset của bảng customers
echo "📋 Customers Table Charset:\n";
$tableInfo = DB::select("SHOW TABLE STATUS LIKE 'customers'")[0];
echo "Collation: " . $tableInfo->Collation . "\n\n";

// Test 3: Tạo test customer với tiếng Việt
echo "👤 Creating test customer with Vietnamese name...\n";
$testCustomer = new Customer();
$testCustomer->name = "Nguyễn Thị Hương Giang";
$testCustomer->phone = "0987654321";
$testCustomer->email = "test.encoding@example.com";
$testCustomer->address = "123 Đường Lê Văn Lương, Quận 7, TP.HCM";
$testCustomer->save();

echo "✅ Test customer created with ID: " . $testCustomer->id . "\n";
echo "Original name: " . $testCustomer->name . "\n\n";

// Test 4: Đọc lại từ database
echo "🔍 Reading back from database...\n";
$readBack = Customer::find($testCustomer->id);
echo "Read back name: " . $readBack->name . "\n";
echo "Encoding preserved: " . ($readBack->name === "Nguyễn Thị Hương Giang" ? "✅ YES" : "❌ NO") . "\n\n";

// Test 5: Kiểm tra raw bytes
echo "🔢 Raw bytes comparison:\n";
echo "Original bytes: " . bin2hex("Nguyễn Thị Hương Giang") . "\n";
echo "Database bytes: " . bin2hex($readBack->name) . "\n";
echo "Bytes match: " . (bin2hex("Nguyễn Thị Hương Giang") === bin2hex($readBack->name) ? "✅ YES" : "❌ NO") . "\n\n";

// Test 6: Kiểm tra một số customer khác có sẵn
echo "👥 Checking existing customers encoding...\n";
$customers = Customer::limit(5)->get();
foreach ($customers as $customer) {
    $hasVietnamese = preg_match('/[àáạảãăắằẳẵặâấầẩẫậđèéẹẻẽêếềểễệìíịỉĩòóọỏõôốồổỗộơớờởỡợùúụủũưứừửữựỳýỵỷỹ]/u', $customer->name);
    echo "ID {$customer->id}: {$customer->name} " . ($hasVietnamese ? "(có tiếng Việt)" : "(không có tiếng Việt)") . "\n";
}

// Cleanup - xóa test customer
echo "\n🧹 Cleaning up test data...\n";
Customer::destroy($testCustomer->id);
echo "✅ Test customer deleted.\n\n";

echo "🎯 Test completed! Check results above.\n";
