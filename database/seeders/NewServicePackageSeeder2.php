<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceCategory;
use App\Models\ServicePackage;

class NewServicePackageSeeder2 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy các danh mục
        $aiVoiceCategory = ServiceCategory::where('name', 'AI giọng đọc')->first();
        $aiCodingCategory = ServiceCategory::where('name', 'AI coding')->first();
        $workToolsCategory = ServiceCategory::where('name', 'Công cụ làm việc')->first();
        $studyToolsCategory = ServiceCategory::where('name', 'Công cụ học tập')->first();
        $entertainmentCategory = ServiceCategory::where('name', 'Công cụ giải trí và xem phim')->first();

        // AI Voice Category Packages
        if ($aiVoiceCategory) {
            // Elevenlab
            ServicePackage::create([
                'category_id' => $aiVoiceCategory->id,
                'name' => 'Elevenlab Creator',
                'account_type' => 'Tài khoản chính chủ',
                'default_duration_days' => 90,
                'custom_duration' => '3 tháng',
                'price' => 550000,
                'description' => 'Elevenlab Creator plan',
                'detailed_notes' => 'ổn định',
                'warranty_type' => 'full',
                'device_limit' => null,
                'is_active' => true,
                'is_renewable' => true
            ]);

            // MiniMax packages
            ServicePackage::create([
                'category_id' => $aiVoiceCategory->id,
                'name' => 'Minimax Creator',
                'account_type' => 'Tài khoản chính chủ',
                'default_duration_days' => 30,
                'custom_duration' => '1 tháng',
                'price' => 199000,
                'description' => 'Minimax Creator plan',
                'detailed_notes' => 'ổn định',
                'warranty_type' => 'full',
                'device_limit' => null,
                'is_active' => true,
                'is_renewable' => true
            ]);

            ServicePackage::create([
                'category_id' => $aiVoiceCategory->id,
                'name' => 'MiniMax Standard',
                'account_type' => 'Tài khoản chính chủ',
                'default_duration_days' => 30,
                'custom_duration' => '1 tháng',
                'price' => 349000,
                'description' => 'MiniMax Standard plan',
                'detailed_notes' => 'ổn định',
                'warranty_type' => 'full',
                'device_limit' => null,
                'is_active' => true,
                'is_renewable' => true
            ]);
        }

        // AI Coding Category Packages
        if ($aiCodingCategory) {
            // Cursor
            ServicePackage::create([
                'category_id' => $aiCodingCategory->id,
                'name' => 'Cursor Pro',
                'account_type' => 'Tài khoản chính chủ',
                'default_duration_days' => 365,
                'custom_duration' => '1 năm',
                'price' => 450000,
                'description' => 'Cursor Pro plan',
                'detailed_notes' => 'ổn định',
                'warranty_type' => '1MONTH',
                'device_limit' => null,
                'is_active' => true,
                'is_renewable' => true
            ]);

            // Augment packages
            ServicePackage::create([
                'category_id' => $aiCodingCategory->id,
                'name' => 'Augment 15k request',
                'account_type' => 'Tài khoản chính chủ',
                'default_duration_days' => 15,
                'custom_duration' => '15 ngày',
                'price' => 99000,
                'description' => 'Augment với 15k request',
                'detailed_notes' => 'ổn định',
                'warranty_type' => 'full',
                'device_limit' => null,
                'is_active' => true,
                'is_renewable' => true
            ]);

            ServicePackage::create([
                'category_id' => $aiCodingCategory->id,
                'name' => 'Augment 30k request',
                'account_type' => 'Tài khoản chính chủ',
                'default_duration_days' => 30,
                'custom_duration' => '1 tháng',
                'price' => 239000,
                'description' => 'Augment với 30k request',
                'detailed_notes' => 'ổn định',
                'warranty_type' => 'full',
                'device_limit' => null,
                'is_active' => true,
                'is_renewable' => true
            ]);

            // Github Copilot
            ServicePackage::create([
                'category_id' => $aiCodingCategory->id,
                'name' => 'Github Copilot',
                'account_type' => 'Tài khoản cấp (dùng riêng)',
                'default_duration_days' => 365,
                'custom_duration' => '1 năm',
                'price' => 389000,
                'description' => 'Github Copilot',
                'detailed_notes' => 'ổn định',
                'warranty_type' => '3 tháng',
                'device_limit' => null,
                'is_active' => true,
                'is_renewable' => true
            ]);
        }

        // Work Tools Category Packages
        if ($workToolsCategory) {
            // CapCut
            ServicePackage::create([
                'category_id' => $workToolsCategory->id,
                'name' => 'CapCut Pro',
                'account_type' => 'Tài khoản cấp (dùng riêng)',
                'default_duration_days' => 30,
                'custom_duration' => '1 tháng',
                'price' => 49000,
                'description' => 'CapCut Pro',
                'detailed_notes' => 'ổn định',
                'warranty_type' => 'full',
                'device_limit' => null,
                'is_active' => true,
                'is_renewable' => true
            ]);

            // Canva Pro
            ServicePackage::create([
                'category_id' => $workToolsCategory->id,
                'name' => 'Canva Pro',
                'account_type' => 'Tài khoản chính chủ',
                'default_duration_days' => 365,
                'custom_duration' => '1 năm',
                'price' => 199000,
                'description' => 'Canva Pro',
                'detailed_notes' => 'ổn định',
                'warranty_type' => 'full',
                'device_limit' => null,
                'is_active' => true,
                'is_renewable' => true
            ]);
        }

        // Study Tools Category Packages
        if ($studyToolsCategory) {
            // Duolingo
            ServicePackage::create([
                'category_id' => $studyToolsCategory->id,
                'name' => 'Duolingo Super',
                'account_type' => 'Tài khoản chính chủ',
                'default_duration_days' => 365,
                'custom_duration' => '1 năm',
                'price' => 200000,
                'description' => 'Duolingo Super',
                'detailed_notes' => 'ổn định',
                'warranty_type' => 'full',
                'device_limit' => null,
                'is_active' => true,
                'is_renewable' => true
            ]);

            // Quizlet
            ServicePackage::create([
                'category_id' => $studyToolsCategory->id,
                'name' => 'Quizlet Plus',
                'account_type' => 'Tài khoản chính chủ',
                'default_duration_days' => 365,
                'custom_duration' => '1 năm',
                'price' => 250000,
                'description' => 'Quizlet Plus',
                'detailed_notes' => 'ổn định',
                'warranty_type' => 'full',
                'device_limit' => null,
                'is_active' => true,
                'is_renewable' => true
            ]);

            // Drive 2TB + Gemini Pro
            ServicePackage::create([
                'category_id' => $studyToolsCategory->id,
                'name' => 'Drive 2TB + Gemini Pro',
                'account_type' => 'Tài khoản chính chủ',
                'default_duration_days' => 365,
                'custom_duration' => '1 năm',
                'price' => 350000,
                'description' => 'Google Drive 2TB + Gemini Pro',
                'detailed_notes' => 'ổn định',
                'warranty_type' => 'full',
                'device_limit' => null,
                'is_active' => true,
                'is_renewable' => true
            ]);

            // Coursera
            ServicePackage::create([
                'category_id' => $studyToolsCategory->id,
                'name' => 'Coursera Business',
                'account_type' => 'Tài khoản chính chủ',
                'default_duration_days' => 30,
                'custom_duration' => '1 tháng',
                'price' => 130000,
                'description' => 'Coursera Business',
                'detailed_notes' => 'ổn định',
                'warranty_type' => 'full',
                'device_limit' => null,
                'is_active' => true,
                'is_renewable' => true
            ]);
        }

        // Entertainment Category Packages
        if ($entertainmentCategory) {
            // YouTube Premium
            ServicePackage::create([
                'category_id' => $entertainmentCategory->id,
                'name' => 'YouTube Premium',
                'account_type' => 'Tài khoản chính chủ',
                'default_duration_days' => 365,
                'custom_duration' => '1 năm',
                'price' => 450000,
                'description' => 'YouTube Premium',
                'detailed_notes' => 'ổn định',
                'warranty_type' => 'full',
                'device_limit' => null,
                'is_active' => true,
                'is_renewable' => true
            ]);

            // Netflix
            ServicePackage::create([
                'category_id' => $entertainmentCategory->id,
                'name' => 'Netflix (hồ sơ riêng)',
                'account_type' => 'Tài khoản cấp (dùng riêng)',
                'default_duration_days' => 30,
                'custom_duration' => '1 tháng',
                'price' => 85000,
                'description' => 'Netflix với hồ sơ riêng',
                'detailed_notes' => 'ổn định',
                'warranty_type' => 'full',
                'device_limit' => null,
                'is_active' => true,
                'is_renewable' => true
            ]);

            // Vieon
            ServicePackage::create([
                'category_id' => $entertainmentCategory->id,
                'name' => 'Vieon VIP',
                'account_type' => 'Tài khoản chính chủ',
                'default_duration_days' => 30,
                'custom_duration' => '1 tháng',
                'price' => 49000,
                'description' => 'Vieon VIP',
                'detailed_notes' => 'ổn định',
                'warranty_type' => 'full',
                'device_limit' => null,
                'is_active' => true,
                'is_renewable' => true
            ]);
        }
    }
}
