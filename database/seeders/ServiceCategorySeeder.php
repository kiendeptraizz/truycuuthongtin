<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceCategory;
use App\Models\ServicePackage;

class ServiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo các danh mục dịch vụ
        $aiCategory = ServiceCategory::create([
            'name' => 'AI & Trí tuệ nhân tạo',
            'description' => 'Các dịch vụ AI như ChatGPT, Gemini, Claude...'
        ]);

        $entertainmentCategory = ServiceCategory::create([
            'name' => 'Giải trí',
            'description' => 'YouTube Premium, Netflix, Spotify...'
        ]);

        $designCategory = ServiceCategory::create([
            'name' => 'Thiết kế & Sáng tạo',
            'description' => 'CapCut Pro, Canva Pro, Adobe...'
        ]);

        // Tạo các gói dịch vụ AI
        ServicePackage::create([
            'category_id' => $aiCategory->id,
            'name' => 'ChatGPT Plus',
            'account_type' => 'Tài khoản chính chủ',
            'default_duration_days' => 30,
            'price' => 150000,
            'description' => 'Tài khoản ChatGPT Plus chính chủ, full quyền'
        ]);

        ServicePackage::create([
            'category_id' => $aiCategory->id,
            'name' => 'ChatGPT Plus (Add Mail)',
            'account_type' => 'Add mail',
            'default_duration_days' => 30,
            'price' => 80000,
            'description' => 'Tài khoản ChatGPT Plus add mail'
        ]);

        ServicePackage::create([
            'category_id' => $aiCategory->id,
            'name' => 'Gemini Advanced',
            'account_type' => 'Tài khoản chính chủ',
            'default_duration_days' => 30,
            'price' => 120000,
            'description' => 'Google Gemini Advanced'
        ]);

        ServicePackage::create([
            'category_id' => $aiCategory->id,
            'name' => 'Perplexity Pro',
            'account_type' => 'Tài khoản chính chủ',
            'default_duration_days' => 30,
            'price' => 100000,
            'description' => 'Perplexity Pro - AI search engine'
        ]);

        // Tạo các gói dịch vụ giải trí
        ServicePackage::create([
            'category_id' => $entertainmentCategory->id,
            'name' => 'YouTube Premium',
            'account_type' => 'Tài khoản chính chủ',
            'default_duration_days' => 30,
            'price' => 50000,
            'description' => 'YouTube Premium không quảng cáo'
        ]);

        ServicePackage::create([
            'category_id' => $entertainmentCategory->id,
            'name' => 'YouTube Premium (Family)',
            'account_type' => 'Team dùng chung',
            'default_duration_days' => 30,
            'price' => 25000,
            'description' => 'YouTube Premium Family - dùng chung'
        ]);

        // Tạo các gói dịch vụ thiết kế
        ServicePackage::create([
            'category_id' => $designCategory->id,
            'name' => 'CapCut Pro',
            'account_type' => 'Tài khoản chính chủ',
            'default_duration_days' => 30,
            'price' => 60000,
            'description' => 'CapCut Pro - chỉnh sửa video chuyên nghiệp'
        ]);

        ServicePackage::create([
            'category_id' => $designCategory->id,
            'name' => 'Canva Pro',
            'account_type' => 'Tài khoản chính chủ',
            'default_duration_days' => 30,
            'price' => 80000,
            'description' => 'Canva Pro - thiết kế đồ họa'
        ]);
    }
}
