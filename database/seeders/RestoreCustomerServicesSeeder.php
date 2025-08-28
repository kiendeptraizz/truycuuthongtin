<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\CustomerService;
use App\Models\ServicePackage;

class RestoreCustomerServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $backupDir = storage_path('app/backups');

        if (!is_dir($backupDir)) {
            $this->command->warn('Không có thư mục backup để khôi phục dữ liệu.');
            return;
        }

        // Tìm file backup mới nhất
        $files = glob($backupDir . '/AUTO_BACKUP_*.json');

        if (empty($files)) {
            $this->command->warn('Không tìm thấy file backup nào để khôi phục.');
            return;
        }

        // Sắp xếp theo thời gian tạo file (mới nhất trước)
        usort($files, function ($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        $latestBackup = $files[0];
        $fileName = basename($latestBackup);

        $this->command->info("Tìm thấy backup: {$fileName}");

        try {
            $data = json_decode(file_get_contents($latestBackup), true);

            if (!$data || !isset($data['customer_services'])) {
                $this->command->error('File backup không chứa dữ liệu customer services!');
                return;
            }

            $this->command->info('Đang khôi phục customer services từ backup...');

            // Disable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            try {
                // Xóa dữ liệu cũ
                CustomerService::truncate();

                $restored = 0;
                $skipped = 0;

                foreach ($data['customer_services'] as $serviceData) {
                    try {
                        // Kiểm tra customer tồn tại
                        if (!Customer::find($serviceData['customer_id'])) {
                            $this->command->warn("Bỏ qua service ID {$serviceData['id']}: Customer {$serviceData['customer_id']} không tồn tại");
                            $skipped++;
                            continue;
                        }

                        // Kiểm tra service package tồn tại
                        if (!ServicePackage::find($serviceData['service_package_id'])) {
                            $this->command->warn("Bỏ qua service ID {$serviceData['id']}: Service Package {$serviceData['service_package_id']} không tồn tại");
                            $skipped++;
                            continue;
                        }

                        // Tạo customer service mới, bỏ qua supplier_id và supplier_service_id nếu không hợp lệ
                        $newServiceData = [
                            'id' => $serviceData['id'],
                            'customer_id' => $serviceData['customer_id'],
                            'service_package_id' => $serviceData['service_package_id'],
                            'assigned_by' => $serviceData['assigned_by'] ?? null,
                            'login_email' => $serviceData['login_email'] ?? null,
                            'login_password' => $serviceData['login_password'] ?? null,
                            'activated_at' => $serviceData['activated_at'] ?? null,
                            'expires_at' => $serviceData['expires_at'] ?? null,
                            'status' => $serviceData['status'] ?? 'active',
                            'internal_notes' => $serviceData['internal_notes'] ?? null,
                            'created_at' => $serviceData['created_at'] ?? now(),
                            'updated_at' => $serviceData['updated_at'] ?? now(),
                        ];

                        // Chỉ thêm supplier_id nếu hợp lệ
                        if (isset($serviceData['supplier_id']) && $serviceData['supplier_id'] && $serviceData['supplier_id'] <= 8) {
                            $newServiceData['supplier_id'] = $serviceData['supplier_id'];
                        }

                        // Thêm các trường khác nếu có
                        $optionalFields = [
                            'supplier_service_id', 'reminder_sent', 'reminder_sent_at', 'reminder_count',
                            'reminder_notes', 'two_factor_code', 'recovery_codes', 'shared_account_notes',
                            'customer_instructions', 'password_expires_at', 'two_factor_updated_at',
                            'is_password_shared', 'shared_with_customers'
                        ];

                        foreach ($optionalFields as $field) {
                            if (isset($serviceData[$field])) {
                                $newServiceData[$field] = $serviceData[$field];
                            }
                        }

                        CustomerService::create($newServiceData);
                        $restored++;

                    } catch (\Exception $e) {
                        $this->command->error("Lỗi khi khôi phục service ID {$serviceData['id']}: " . $e->getMessage());
                        $skipped++;
                    }
                }

                $this->command->info("✅ Khôi phục thành công {$restored} customer services");
                if ($skipped > 0) {
                    $this->command->warn("⚠️ Bỏ qua {$skipped} services do lỗi dữ liệu");
                }

            } finally {
                // Re-enable foreign key checks
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }

        } catch (\Exception $e) {
            $this->command->error('❌ Lỗi khi khôi phục từ backup: ' . $e->getMessage());
        }
    }
}
