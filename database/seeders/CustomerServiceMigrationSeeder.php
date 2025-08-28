<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServicePackage;
use App\Models\CustomerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerServiceMigrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mapping logic từ gói cũ sang gói mới
        $packageMapping = [
            // AI Category mappings
            'ChatGPT Plus' => 'ChatGPT Plus chính chủ (cá nhân)',
            'ChatGPT Plus (Add Mail)' => 'ChatGPT Plus chính chủ (add mail)',
            'Gemini Advanced' => 'Gemini Pro + 2TB drive chính chủ',
            'Perplexity Pro' => 'Perplexity chính chủ',
            
            // Entertainment mappings
            'YouTube Premium' => 'YouTube Premium',
            'YouTube Premium (Family)' => 'YouTube Premium', // Merge to single YouTube Premium
            
            // Design/Work tools mappings
            'CapCut Pro' => 'CapCut Pro',
            'Canva Pro' => 'Canva Pro',
        ];

        // Lấy tất cả customer services hiện tại từ backup
        $existingServices = DB::table('customer_services_backup')->get();
        
        foreach ($existingServices as $service) {
            // Lấy thông tin gói dịch vụ cũ
            $oldPackage = DB::table('service_packages_backup')->where('id', $service->service_package_id)->first();
            
            if (!$oldPackage) {
                Log::warning("Không tìm thấy gói dịch vụ cũ với ID: {$service->service_package_id}");
                continue;
            }

            // Tìm gói dịch vụ mới tương ứng
            $newPackageName = $packageMapping[$oldPackage->name] ?? $this->findSimilarPackage($oldPackage->name);
            
            if (!$newPackageName) {
                Log::warning("Không tìm thấy mapping cho gói: {$oldPackage->name}");
                continue;
            }

            $newPackage = ServicePackage::where('name', $newPackageName)->first();
            
            if (!$newPackage) {
                Log::warning("Không tìm thấy gói mới: {$newPackageName}");
                continue;
            }

            // Tạo customer service mới với gói mới
            try {
                CustomerService::create([
                    'customer_id' => $service->customer_id,
                    'service_package_id' => $newPackage->id,
                    'login_email' => $service->login_email,
                    'login_password' => $service->login_password,
                    'activated_at' => $service->activated_at,
                    'expires_at' => $service->expires_at,
                    'status' => $service->status,
                    'internal_notes' => $service->internal_notes . " [Migrated from: {$oldPackage->name}]",
                    'created_at' => $service->created_at,
                    'updated_at' => now(),
                ]);
                
                Log::info("Migrated service: {$oldPackage->name} -> {$newPackage->name} for customer {$service->customer_id}");
                
            } catch (\Exception $e) {
                Log::error("Error migrating service for customer {$service->customer_id}: " . $e->getMessage());
            }
        }
    }

    /**
     * Tìm gói dịch vụ tương tự dựa trên tên
     */
    private function findSimilarPackage($oldPackageName)
    {
        // Logic tìm kiếm thông minh dựa trên từ khóa
        $keywords = [
            'ChatGPT' => 'ChatGPT Plus chính chủ (cá nhân)',
            'Gemini' => 'Gemini Pro + 2TB drive chính chủ',
            'YouTube' => 'YouTube Premium',
            'CapCut' => 'CapCut Pro',
            'Canva' => 'Canva Pro',
            'Perplexity' => 'Perplexity chính chủ',
            'Claude' => 'Claude AI chính chủ',
            'Netflix' => 'Netflix (hồ sơ riêng)',
            'Cursor' => 'Cursor Pro',
            'Github' => 'Github Copilot',
            'Duolingo' => 'Duolingo Super',
            'Coursera' => 'Coursera Business',
        ];

        foreach ($keywords as $keyword => $newPackageName) {
            if (stripos($oldPackageName, $keyword) !== false) {
                return $newPackageName;
            }
        }

        return null;
    }
}
