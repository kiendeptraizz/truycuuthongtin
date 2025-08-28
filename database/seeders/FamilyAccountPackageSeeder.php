<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceCategory;
use App\Models\ServicePackage;

class FamilyAccountPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy hoặc tạo categories
        $aiCategory = ServiceCategory::firstOrCreate(
            ['name' => 'AI & Productivity'],
            ['description' => 'Dịch vụ AI và năng suất']
        );

        $entertainmentCategory = ServiceCategory::firstOrCreate(
            ['name' => 'Entertainment'],
            ['description' => 'Dịch vụ giải trí']
        );

        $designCategory = ServiceCategory::firstOrCreate(
            ['name' => 'Design & Creative'],
            ['description' => 'Dịch vụ thiết kế và sáng tạo']
        );

        $cloudCategory = ServiceCategory::firstOrCreate(
            ['name' => 'Cloud & Storage'],
            ['description' => 'Dịch vụ lưu trữ đám mây']
        );

        // Tạo các gói Family Account cho AI & Productivity
        ServicePackage::create([
            'category_id' => $aiCategory->id,
            'name' => 'ChatGPT Plus Family',
            'account_type' => 'Tài khoản add family',
            'default_duration_days' => 30,
            'price' => 120000,
            'cost_price' => 80000,
            'description' => 'ChatGPT Plus Family Plan - Thêm vào gia đình',
            'detailed_notes' => 'Có thể thêm tối đa 5 thành viên vào family plan',
            'warranty_type' => 'full',
            'device_limit' => null,
            'is_active' => true,
            'is_renewable' => true
        ]);

        ServicePackage::create([
            'category_id' => $aiCategory->id,
            'name' => 'Claude Pro Family',
            'account_type' => 'Tài khoản add family',
            'default_duration_days' => 30,
            'price' => 100000,
            'cost_price' => 70000,
            'description' => 'Claude Pro Family Plan - Thêm vào gia đình',
            'detailed_notes' => 'Có thể thêm tối đa 5 thành viên vào family plan',
            'warranty_type' => 'full',
            'device_limit' => null,
            'is_active' => true,
            'is_renewable' => true
        ]);

        ServicePackage::create([
            'category_id' => $aiCategory->id,
            'name' => 'Gemini Advanced Family',
            'account_type' => 'Tài khoản add family',
            'default_duration_days' => 30,
            'price' => 90000,
            'cost_price' => 60000,
            'description' => 'Google Gemini Advanced Family Plan',
            'detailed_notes' => 'Có thể thêm tối đa 6 thành viên vào family plan',
            'warranty_type' => 'full',
            'device_limit' => null,
            'is_active' => true,
            'is_renewable' => true
        ]);

        // Tạo các gói Family Account cho Entertainment
        ServicePackage::create([
            'category_id' => $entertainmentCategory->id,
            'name' => 'YouTube Premium Family',
            'account_type' => 'Tài khoản add family',
            'default_duration_days' => 30,
            'price' => 35000,
            'cost_price' => 25000,
            'description' => 'YouTube Premium Family Plan - Thêm vào gia đình',
            'detailed_notes' => 'Có thể thêm tối đa 5 thành viên vào family plan',
            'warranty_type' => 'full',
            'device_limit' => null,
            'is_active' => true,
            'is_renewable' => true
        ]);

        ServicePackage::create([
            'category_id' => $entertainmentCategory->id,
            'name' => 'Spotify Premium Family',
            'account_type' => 'Tài khoản add family',
            'default_duration_days' => 30,
            'price' => 40000,
            'cost_price' => 30000,
            'description' => 'Spotify Premium Family Plan - Thêm vào gia đình',
            'detailed_notes' => 'Có thể thêm tối đa 6 thành viên vào family plan',
            'warranty_type' => 'full',
            'device_limit' => null,
            'is_active' => true,
            'is_renewable' => true
        ]);

        ServicePackage::create([
            'category_id' => $entertainmentCategory->id,
            'name' => 'Netflix Premium Family',
            'account_type' => 'Tài khoản add family',
            'default_duration_days' => 30,
            'price' => 50000,
            'cost_price' => 35000,
            'description' => 'Netflix Premium Family Plan - Thêm vào gia đình',
            'detailed_notes' => 'Có thể thêm tối đa 4 thành viên vào family plan',
            'warranty_type' => 'full',
            'device_limit' => null,
            'is_active' => true,
            'is_renewable' => true
        ]);

        // Tạo các gói Family Account cho Design & Creative
        ServicePackage::create([
            'category_id' => $designCategory->id,
            'name' => 'Canva Pro Family',
            'account_type' => 'Tài khoản add family',
            'default_duration_days' => 30,
            'price' => 60000,
            'cost_price' => 45000,
            'description' => 'Canva Pro Family Plan - Thêm vào gia đình',
            'detailed_notes' => 'Có thể thêm tối đa 5 thành viên vào family plan',
            'warranty_type' => 'full',
            'device_limit' => null,
            'is_active' => true,
            'is_renewable' => true
        ]);

        ServicePackage::create([
            'category_id' => $designCategory->id,
            'name' => 'Adobe Creative Cloud Family',
            'account_type' => 'Tài khoản add family',
            'default_duration_days' => 30,
            'price' => 150000,
            'cost_price' => 120000,
            'description' => 'Adobe Creative Cloud Family Plan - Thêm vào gia đình',
            'detailed_notes' => 'Có thể thêm tối đa 6 thành viên vào family plan',
            'warranty_type' => 'full',
            'device_limit' => null,
            'is_active' => true,
            'is_renewable' => true
        ]);

        // Tạo các gói Family Account cho Cloud & Storage
        ServicePackage::create([
            'category_id' => $cloudCategory->id,
            'name' => 'Google One Family (2TB)',
            'account_type' => 'Tài khoản add family',
            'default_duration_days' => 30,
            'price' => 45000,
            'cost_price' => 35000,
            'description' => 'Google One 2TB Family Plan - Thêm vào gia đình',
            'detailed_notes' => 'Có thể thêm tối đa 5 thành viên vào family plan',
            'warranty_type' => 'full',
            'device_limit' => null,
            'is_active' => true,
            'is_renewable' => true
        ]);

        ServicePackage::create([
            'category_id' => $cloudCategory->id,
            'name' => 'iCloud+ Family (2TB)',
            'account_type' => 'Tài khoản add family',
            'default_duration_days' => 30,
            'price' => 50000,
            'cost_price' => 40000,
            'description' => 'iCloud+ 2TB Family Plan - Thêm vào gia đình',
            'detailed_notes' => 'Có thể thêm tối đa 6 thành viên vào family plan',
            'warranty_type' => 'full',
            'device_limit' => null,
            'is_active' => true,
            'is_renewable' => true
        ]);

        $this->command->info('✅ Đã tạo thành công ' . ServicePackage::where('account_type', 'Tài khoản add family')->count() . ' gói Family Account!');
    }
}
