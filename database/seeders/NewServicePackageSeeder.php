<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ServiceCategory;
use App\Models\ServicePackage;

class NewServicePackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            // Xóa tất cả gói dịch vụ cũ
            ServicePackage::truncate();

            // Lấy các danh mục
            $aiCategory = ServiceCategory::where('name', 'AI')->first();
            $aiVideoCategory = ServiceCategory::where('name', 'AI làm video')->first();
            $aiVoiceCategory = ServiceCategory::where('name', 'AI giọng đọc')->first();
            $aiCodingCategory = ServiceCategory::where('name', 'AI coding')->first();
            $workToolsCategory = ServiceCategory::where('name', 'Công cụ làm việc')->first();
            $studyToolsCategory = ServiceCategory::where('name', 'Công cụ học tập')->first();
            $entertainmentCategory = ServiceCategory::where('name', 'Công cụ giải trí và xem phim')->first();

            // Helper function để parse giá
            $parsePrice = function ($priceStr) {
                if ($priceStr === 'báo giá riêng') {
                    return 0;
                }
                return (float) str_replace(['.', ' VND', 'VND', ' '], '', $priceStr);
            };

            // Helper function để parse thời hạn
            $parseDuration = function ($duration) {
                if (empty($duration) || $duration === null) {
                    return 30; // Default 30 days
                }

                $duration = trim($duration);
                if (strpos($duration, 'tháng') !== false) {
                    $months = (int) filter_var($duration, FILTER_SANITIZE_NUMBER_INT);
                    return $months * 30;
                } elseif (strpos($duration, 'năm') !== false) {
                    $years = (int) filter_var($duration, FILTER_SANITIZE_NUMBER_INT);
                    return $years * 365;
                } elseif (strpos($duration, 'ngày') !== false) {
                    return (int) filter_var($duration, FILTER_SANITIZE_NUMBER_INT);
                }

                return 30; // Default
            };

            // Helper function để chuẩn hóa account type
            $normalizeAccountType = function ($status) {
                if (empty($status)) return 'Tài khoản chính chủ';

                $status = trim($status);
                switch ($status) {
                    case 'dùng chung':
                        return 'Tài khoản dùng chung';
                    case 'chính chủ':
                    case 'tài khoản chính chủ':
                        return 'Tài khoản chính chủ';
                    case 'tài khoản cấp':
                    case 'tài khoản cấp ( dùng riêng)':
                    case 'tài khoản cấp ( dùng riêng) ':
                    case 'tài khoản cấp ':
                        return 'Tài khoản cấp (dùng riêng)';
                    default:
                        return 'Tài khoản chính chủ';
                }
            };

            // AI Category Packages
            if ($aiCategory) {
                // ChatGPT Plus packages
                ServicePackage::create([
                    'category_id' => $aiCategory->id,
                    'name' => 'ChatGPT Plus dùng chung',
                    'account_type' => 'Tài khoản dùng chung',
                    'default_duration_days' => 30,
                    'custom_duration' => '1 tháng',
                    'price' => 99000,
                    'description' => 'ChatGPT Plus dùng chung',
                    'detailed_notes' => 'dùng chung 4 người, sử dụng 1 thiết bị',
                    'warranty_type' => 'full',
                    'shared_users_limit' => 4,
                    'device_limit' => 1,
                    'is_active' => true,
                    'is_renewable' => true
                ]);

                ServicePackage::create([
                    'category_id' => $aiCategory->id,
                    'name' => 'ChatGPT Plus chính chủ (add mail)',
                    'account_type' => 'Tài khoản chính chủ',
                    'default_duration_days' => 30,
                    'price' => 149000,
                    'description' => 'ChatGPT Plus chính chủ add mail',
                    'detailed_notes' => 'ổn định, không giới hạn thiết bị, không thể gia hạn',
                    'warranty_type' => 'full',
                    'device_limit' => null,
                    'is_active' => true,
                    'is_renewable' => false
                ]);

                ServicePackage::create([
                    'category_id' => $aiCategory->id,
                    'name' => 'ChatGPT Plus chính chủ (cá nhân)',
                    'account_type' => 'Tài khoản chính chủ',
                    'default_duration_days' => 30,
                    'price' => 399000,
                    'description' => 'ChatGPT Plus chính chủ cá nhân',
                    'detailed_notes' => 'gia hạn được, không giới hạn thiết bị',
                    'warranty_type' => 'full',
                    'device_limit' => null,
                    'is_active' => true,
                    'is_renewable' => true
                ]);

                // Supper Grok packages
                ServicePackage::create([
                    'category_id' => $aiCategory->id,
                    'name' => 'Supper Grok dùng chung',
                    'account_type' => 'Tài khoản dùng chung',
                    'default_duration_days' => 30,
                    'custom_duration' => '1 tháng',
                    'price' => 70000,
                    'description' => 'Supper Grok dùng chung',
                    'detailed_notes' => 'dùng chung 5 người, sử dụng 2 thiết bị',
                    'warranty_type' => 'full',
                    'shared_users_limit' => 5,
                    'device_limit' => 2,
                    'is_active' => true,
                    'is_renewable' => true
                ]);

                ServicePackage::create([
                    'category_id' => $aiCategory->id,
                    'name' => 'Supper Grok chính chủ',
                    'account_type' => 'Tài khoản chính chủ',
                    'default_duration_days' => 30,
                    'custom_duration' => '1 tháng',
                    'price' => 400000,
                    'description' => 'Supper Grok chính chủ',
                    'detailed_notes' => 'không giới hạn thiết bị',
                    'warranty_type' => 'full',
                    'device_limit' => null,
                    'is_active' => true,
                    'is_renewable' => true
                ]);

                // Perplexity Pro
                ServicePackage::create([
                    'category_id' => $aiCategory->id,
                    'name' => 'Perplexity chính chủ',
                    'account_type' => 'Tài khoản chính chủ',
                    'default_duration_days' => 365,
                    'custom_duration' => '1 năm',
                    'price' => 299000,
                    'description' => 'Perplexity Pro chính chủ',
                    'detailed_notes' => 'không giới hạn thiết bị, mail cá nhân',
                    'warranty_type' => '3 tháng',
                    'device_limit' => null,
                    'is_active' => true,
                    'is_renewable' => true
                ]);

                // Gemini Pro + 2TB Drive
                ServicePackage::create([
                    'category_id' => $aiCategory->id,
                    'name' => 'Gemini Pro + 2TB drive chính chủ',
                    'account_type' => 'Tài khoản chính chủ',
                    'default_duration_days' => 365,
                    'custom_duration' => '1 năm',
                    'price' => 350000,
                    'description' => 'Gemini Pro + 2TB Drive (tặng Youtube Premium)',
                    'detailed_notes' => 'mail cá nhân, dùng ổn định',
                    'warranty_type' => 'full',
                    'device_limit' => null,
                    'is_active' => true,
                    'is_renewable' => true
                ]);

                // Claude AI
                ServicePackage::create([
                    'category_id' => $aiCategory->id,
                    'name' => 'Claude AI chính chủ',
                    'account_type' => 'Tài khoản chính chủ',
                    'default_duration_days' => 30,
                    'custom_duration' => '1 tháng',
                    'price' => 420000,
                    'description' => 'Claude AI chính chủ',
                    'detailed_notes' => 'mail cá nhân, dùng ổn định',
                    'warranty_type' => 'full',
                    'device_limit' => null,
                    'is_active' => true,
                    'is_renewable' => true
                ]);
            }

            // AI Video Category Packages
            if ($aiVideoCategory) {
                // Veo 3 packages
                ServicePackage::create([
                    'category_id' => $aiVideoCategory->id,
                    'name' => 'Veo 3 (1k credit) tài khoản cấp',
                    'account_type' => 'Tài khoản cấp (dùng riêng)',
                    'default_duration_days' => 450, // 15 tháng
                    'custom_duration' => '15 tháng',
                    'price' => 99000,
                    'description' => 'Veo 3 với 1k credit',
                    'detailed_notes' => 'ổn định, không giới hạn thiết bị',
                    'warranty_type' => 'KBH',
                    'device_limit' => null,
                    'is_active' => true,
                    'is_renewable' => true
                ]);

                ServicePackage::create([
                    'category_id' => $aiVideoCategory->id,
                    'name' => 'Veo 3 (12.5k credit) tài khoản cấp',
                    'account_type' => 'Tài khoản cấp (dùng riêng)',
                    'default_duration_days' => 30,
                    'custom_duration' => '1 tháng',
                    'price' => 489000,
                    'description' => 'Veo 3 với 12.5k credit',
                    'detailed_notes' => 'ổn định, không giới hạn thiết bị',
                    'warranty_type' => 'full',
                    'device_limit' => null,
                    'is_active' => true,
                    'is_renewable' => true
                ]);

                // Kling packages
                ServicePackage::create([
                    'category_id' => $aiVideoCategory->id,
                    'name' => 'Kling Standard',
                    'account_type' => 'Tài khoản chính chủ',
                    'default_duration_days' => 30,
                    'custom_duration' => '1 tháng',
                    'price' => 120000,
                    'description' => 'Kling Standard plan',
                    'detailed_notes' => 'ổn định, không giới hạn thiết bị',
                    'warranty_type' => 'full',
                    'device_limit' => null,
                    'is_active' => true,
                    'is_renewable' => true
                ]);

                ServicePackage::create([
                    'category_id' => $aiVideoCategory->id,
                    'name' => 'Kling Pro',
                    'account_type' => 'Tài khoản chính chủ',
                    'default_duration_days' => 30,
                    'custom_duration' => '1 tháng',
                    'price' => 389000,
                    'description' => 'Kling Pro plan',
                    'detailed_notes' => 'ổn định không giới hạn thiết bị',
                    'warranty_type' => 'full',
                    'device_limit' => null,
                    'is_active' => true,
                    'is_renewable' => true
                ]);

                ServicePackage::create([
                    'category_id' => $aiVideoCategory->id,
                    'name' => 'Kling Premium',
                    'account_type' => 'Tài khoản chính chủ',
                    'default_duration_days' => 30,
                    'custom_duration' => '1 tháng',
                    'price' => 645000,
                    'description' => 'Kling Premium plan',
                    'detailed_notes' => 'ổn định không giới hạn thiết bị',
                    'warranty_type' => 'full',
                    'device_limit' => null,
                    'is_active' => true,
                    'is_renewable' => true
                ]);

                ServicePackage::create([
                    'category_id' => $aiVideoCategory->id,
                    'name' => 'Kling Standard buff credit (1k-39k credit)',
                    'account_type' => 'Tài khoản cấp (dùng riêng)',
                    'default_duration_days' => 30,
                    'custom_duration' => '1 tháng',
                    'price' => 0, // báo giá riêng
                    'description' => 'Kling Standard với credit tùy chỉnh',
                    'detailed_notes' => 'ổn định không giới hạn thiết bị',
                    'warranty_type' => 'full',
                    'device_limit' => null,
                    'is_active' => true,
                    'is_renewable' => true
                ]);

                // Hailuo packages
                ServicePackage::create([
                    'category_id' => $aiVideoCategory->id,
                    'name' => 'Hailuo Standard',
                    'account_type' => 'Tài khoản chính chủ',
                    'default_duration_days' => 30,
                    'custom_duration' => '1 tháng',
                    'price' => 170000,
                    'description' => 'Hailuo Standard plan',
                    'detailed_notes' => 'ổn định',
                    'warranty_type' => 'full',
                    'device_limit' => null,
                    'is_active' => true,
                    'is_renewable' => true
                ]);

                ServicePackage::create([
                    'category_id' => $aiVideoCategory->id,
                    'name' => 'Hailuo Unlimited/Pro',
                    'account_type' => 'Tài khoản chính chủ',
                    'default_duration_days' => 30,
                    'custom_duration' => '1 tháng',
                    'price' => 0, // báo giá riêng
                    'description' => 'Hailuo Unlimited hoặc Pro plan',
                    'detailed_notes' => 'ổn định',
                    'warranty_type' => 'full',
                    'device_limit' => null,
                    'is_active' => true,
                    'is_renewable' => true
                ]);

                // Gamma AI packages
                ServicePackage::create([
                    'category_id' => $aiVideoCategory->id,
                    'name' => 'Gamma Plus',
                    'account_type' => 'Tài khoản chính chủ',
                    'default_duration_days' => 30,
                    'custom_duration' => '1 tháng',
                    'price' => 170000,
                    'description' => 'Gamma Plus plan',
                    'detailed_notes' => 'ổn định',
                    'warranty_type' => 'full',
                    'device_limit' => null,
                    'is_active' => true,
                    'is_renewable' => true
                ]);

                ServicePackage::create([
                    'category_id' => $aiVideoCategory->id,
                    'name' => 'Gamma Pro',
                    'account_type' => 'Tài khoản chính chủ',
                    'default_duration_days' => 30,
                    'custom_duration' => '1 tháng',
                    'price' => 350000,
                    'description' => 'Gamma Pro plan',
                    'detailed_notes' => 'ổn định',
                    'warranty_type' => 'full',
                    'device_limit' => null,
                    'is_active' => true,
                    'is_renewable' => true
                ]);

                // HeyGen AI
                ServicePackage::create([
                    'category_id' => $aiVideoCategory->id,
                    'name' => 'HeyGen Creator',
                    'account_type' => 'Tài khoản chính chủ',
                    'default_duration_days' => 30,
                    'custom_duration' => '1 tháng',
                    'price' => 489000,
                    'description' => 'HeyGen Creator plan',
                    'detailed_notes' => 'ổn định',
                    'warranty_type' => 'full',
                    'device_limit' => null,
                    'is_active' => true,
                    'is_renewable' => true
                ]);

                // VidIQ boost
                ServicePackage::create([
                    'category_id' => $aiVideoCategory->id,
                    'name' => 'VidIQ Boost',
                    'account_type' => 'Tài khoản chính chủ',
                    'default_duration_days' => 30,
                    'custom_duration' => '1 tháng',
                    'price' => 199000,
                    'description' => 'VidIQ Boost plan',
                    'detailed_notes' => 'ổn định',
                    'warranty_type' => 'full',
                    'device_limit' => null,
                    'is_active' => true,
                    'is_renewable' => true
                ]);
            }
        } finally {
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}
