<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\ServicePackage;
use App\Models\CustomerService;

class TestEmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo khách hàng sử dụng email gaschburdab0@outlook.com
        $customer1 = Customer::create([
            'name' => 'Phạm Văn D',
            'email' => 'phamvand@gmail.com',
            'phone' => '0934567890'
        ]);

        $customer2 = Customer::create([
            'name' => 'Hoàng Thị E',
            'email' => 'hoangthie@gmail.com',
            'phone' => '0945678901'
        ]);

        $customer3 = Customer::create([
            'name' => 'Vũ Văn F',
            'email' => 'vuvanf@gmail.com',
            'phone' => '0956789012'
        ]);

        // Lấy các gói dịch vụ
        $chatgptPlus = ServicePackage::where('name', 'ChatGPT Plus')->first();
        $geminiAdvanced = ServicePackage::where('name', 'Gemini Advanced')->first();
        $youtubePremium = ServicePackage::where('name', 'YouTube Premium')->first();

        // Tạo nhiều khách hàng sử dụng cùng email đăng nhập gaschburdab0@outlook.com
        if ($chatgptPlus) {
            CustomerService::create([
                'customer_id' => $customer1->id,
                'service_package_id' => $chatgptPlus->id,
                'login_email' => 'gaschburdab0@outlook.com',
                'login_password' => 'sharedpassword123',
                'activated_at' => now(),
                'expires_at' => now()->addDays(30),
                'status' => 'active',
                'internal_notes' => 'Tài khoản dùng chung - Người 1'
            ]);
        }

        if ($geminiAdvanced) {
            CustomerService::create([
                'customer_id' => $customer2->id,
                'service_package_id' => $geminiAdvanced->id,
                'login_email' => 'gaschburdab0@outlook.com',
                'login_password' => 'sharedpassword123',
                'activated_at' => now()->subDays(5),
                'expires_at' => now()->addDays(25),
                'status' => 'active',
                'internal_notes' => 'Tài khoản dùng chung - Người 2'
            ]);
        }

        if ($youtubePremium) {
            CustomerService::create([
                'customer_id' => $customer3->id,
                'service_package_id' => $youtubePremium->id,
                'login_email' => 'gaschburdab0@outlook.com',
                'login_password' => 'sharedpassword123',
                'activated_at' => now()->subDays(10),
                'expires_at' => now()->addDays(20),
                'status' => 'active',
                'internal_notes' => 'Tài khoản dùng chung - Người 3'
            ]);
        }

        // Thêm một khách hàng khác cũng dùng email này
        $customer4 = Customer::create([
            'name' => 'Đặng Thị G',
            'email' => 'dangthig@gmail.com',
            'phone' => '0967890123'
        ]);

        if ($chatgptPlus) {
            CustomerService::create([
                'customer_id' => $customer4->id,
                'service_package_id' => $chatgptPlus->id,
                'login_email' => 'gaschburdab0@outlook.com',
                'login_password' => 'sharedpassword123',
                'activated_at' => now()->subDays(3),
                'expires_at' => now()->addDays(27),
                'status' => 'active',
                'internal_notes' => 'Tài khoản dùng chung - Người 4'
            ]);
        }
    }
}