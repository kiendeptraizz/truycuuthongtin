<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Collaborator;
use App\Models\CollaboratorService;
use App\Models\CollaboratorServiceAccount;

class CollaboratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo cộng tác viên 1
        $collaborator1 = Collaborator::create([
            'name' => 'Nguyễn Văn Dương',
            'email' => 'duong.nguyen@example.com',
            'phone' => '0123456789',
            'address' => 'Hà Nội',
            'status' => 'active',
            'notes' => 'Cộng tác viên chuyên về dịch vụ AI',
            'commission_rate' => 10.00,
        ]);

        // Dịch vụ của cộng tác viên 1
        $service1 = $collaborator1->services()->create([
            'service_name' => 'Netflix Premium',
            'price' => 50000,
            'quantity' => 10,
            'warranty_period' => 30,
            'description' => 'Tài khoản Netflix Premium chất lượng cao'
        ]);

        $service2 = $collaborator1->services()->create([
            'service_name' => 'ChatGPT Plus',
            'price' => 120000,
            'quantity' => 5,
            'warranty_period' => 15,
            'description' => 'Tài khoản ChatGPT Plus với nhiều tính năng'
        ]);

        // Tài khoản cho Netflix
        $service1->accounts()->create([
            'account_info' => 'Email: netflix1@example.com | Password: abc123',
            'provided_date' => now()->subDays(5),
            'expiry_date' => now()->addDays(25),
            'status' => 'active'
        ]);

        $service1->accounts()->create([
            'account_info' => 'Email: netflix2@example.com | Password: def456',
            'provided_date' => now()->subDays(3),
            'expiry_date' => now()->addDays(27),
            'status' => 'active'
        ]);

        // Tài khoản cho ChatGPT
        $service2->accounts()->create([
            'account_info' => 'Email: chatgpt1@example.com | Password: gpt123',
            'provided_date' => now()->subDays(2),
            'expiry_date' => now()->addDays(13),
            'status' => 'active'
        ]);

        // Tạo cộng tác viên 2
        $collaborator2 = Collaborator::create([
            'name' => 'Trần Thị Linh',
            'email' => 'linh.tran@example.com',
            'phone' => '0987654321',
            'address' => 'TP. Hồ Chí Minh',
            'status' => 'active',
            'notes' => 'Cộng tác viên chuyên về tài khoản game',
            'commission_rate' => 8.50,
        ]);

        // Dịch vụ của cộng tác viên 2
        $service3 = $collaborator2->services()->create([
            'service_name' => 'Spotify Premium',
            'price' => 45000,
            'quantity' => 8,
            'warranty_period' => 30,
            'description' => 'Tài khoản Spotify Premium nghe nhạc không giới hạn'
        ]);

        $service4 = $collaborator2->services()->create([
            'service_name' => 'Office 365',
            'price' => 150000,
            'quantity' => 3,
            'warranty_period' => 60,
            'description' => 'Tài khoản Microsoft Office 365 đầy đủ tính năng'
        ]);

        // Tài khoản cho Spotify
        $service3->accounts()->create([
            'account_info' => 'Email: spotify1@example.com | Password: music123',
            'provided_date' => now()->subDays(10),
            'expiry_date' => now()->addDays(20),
            'status' => 'active'
        ]);

        // Tài khoản cho Office 365
        $service4->accounts()->create([
            'account_info' => 'Email: office1@example.com | Password: office123',
            'provided_date' => now()->subDays(7),
            'expiry_date' => now()->addDays(53),
            'status' => 'active'
        ]);

        // Tạo cộng tác viên 3 (không hoạt động)
        $collaborator3 = Collaborator::create([
            'name' => 'Phạm Minh Tuấn',
            'email' => 'tuan.pham@example.com',
            'phone' => '0369852147',
            'address' => 'Đà Nẵng',
            'status' => 'inactive',
            'notes' => 'Cộng tác viên tạm ngưng hoạt động',
            'commission_rate' => 12.00,
        ]);

        $service5 = $collaborator3->services()->create([
            'service_name' => 'Adobe Creative Cloud',
            'price' => 200000,
            'quantity' => 2,
            'warranty_period' => 90,
            'description' => 'Tài khoản Adobe CC đầy đủ ứng dụng thiết kế'
        ]);

        // Tài khoản hết hạn
        $service5->accounts()->create([
            'account_info' => 'Email: adobe1@example.com | Password: design123',
            'provided_date' => now()->subDays(95),
            'expiry_date' => now()->subDays(5),
            'status' => 'expired'
        ]);
    }
}
