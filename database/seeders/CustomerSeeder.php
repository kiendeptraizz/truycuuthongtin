<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\ServicePackage;
use App\Models\CustomerService;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo khách hàng mẫu
        $customer1 = Customer::create([
            'name' => 'Nguyễn Văn A',
            'email' => 'nguyenvana@gmail.com',
            'phone' => '0901234567'
        ]);

        $customer2 = Customer::create([
            'name' => 'Trần Thị B',
            'email' => 'tranthib@gmail.com',
            'phone' => '0987654321'
        ]);

        $customer3 = Customer::create([
            'name' => 'Lê Văn C',
            'email' => 'levanc@gmail.com',
            'phone' => '0912345678'
        ]);

        // Lấy các gói dịch vụ
        $chatgptPlus = ServicePackage::where('name', 'ChatGPT Plus')->first();
        $geminiAdvanced = ServicePackage::where('name', 'Gemini Advanced')->first();
        $youtubePremium = ServicePackage::where('name', 'YouTube Premium')->first();
        $capcutPro = ServicePackage::where('name', 'CapCut Pro')->first();

        // Gán dịch vụ cho khách hàng 1
        if ($chatgptPlus) {
            CustomerService::create([
                'customer_id' => $customer1->id,
                'service_package_id' => $chatgptPlus->id,
                'login_email' => 'nguyenvana.chatgpt@gmail.com',
                'login_password' => 'password123',
                'activated_at' => now(),
                'expires_at' => now()->addDays(25), // Sắp hết hạn
                'status' => 'active',
                'internal_notes' => 'Khách hàng VIP, gia hạn tự động'
            ]);
        }

        if ($youtubePremium) {
            CustomerService::create([
                'customer_id' => $customer1->id,
                'service_package_id' => $youtubePremium->id,
                'login_email' => 'nguyenvana.youtube@gmail.com',
                'login_password' => 'password123',
                'activated_at' => now()->subDays(5),
                'expires_at' => now()->addDays(25),
                'status' => 'active',
                'internal_notes' => 'Tài khoản ổn định'
            ]);
        }

        // Gán dịch vụ cho khách hàng 2
        if ($geminiAdvanced) {
            CustomerService::create([
                'customer_id' => $customer2->id,
                'service_package_id' => $geminiAdvanced->id,
                'login_email' => 'tranthib.gemini@gmail.com',
                'login_password' => 'password456',
                'activated_at' => now()->subDays(10),
                'expires_at' => now()->addDays(20),
                'status' => 'active',
                'internal_notes' => 'Khách hàng thường xuyên'
            ]);
        }

        if ($capcutPro) {
            CustomerService::create([
                'customer_id' => $customer2->id,
                'service_package_id' => $capcutPro->id,
                'login_email' => 'tranthib.capcut@gmail.com',
                'login_password' => 'password456',
                'activated_at' => now()->subDays(15),
                'expires_at' => now()->addDays(15),
                'status' => 'active',
                'internal_notes' => 'Dùng cho công việc thiết kế'
            ]);
        }

        // Gán dịch vụ cho khách hàng 3 (có dịch vụ sắp hết hạn)
        if ($chatgptPlus) {
            CustomerService::create([
                'customer_id' => $customer3->id,
                'service_package_id' => $chatgptPlus->id,
                'login_email' => 'levanc.chatgpt@gmail.com',
                'login_password' => 'password789',
                'activated_at' => now()->subDays(25),
                'expires_at' => now()->addDays(3), // Sắp hết hạn trong 3 ngày
                'status' => 'active',
                'internal_notes' => 'Cần nhắc nhở gia hạn'
            ]);
        }
    }
}
