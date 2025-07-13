<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\CustomerService;
use Illuminate\Support\Facades\DB;

class RestoreCustomersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $backupDir = storage_path('app/backups');

        if (!is_dir($backupDir)) {
            $this->command->warn('Không có thư mục backup để khôi phục dữ liệu khách hàng.');
            return;
        }

        // Tìm file backup mới nhất
        $files = glob($backupDir . '/customer_backup_*.json');

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

            if (!$data || !isset($data['customers'])) {
                $this->command->error('File backup không đúng định dạng!');
                return;
            }

            $this->command->info('Đang khôi phục dữ liệu khách hàng từ backup...');

            // Disable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Xóa dữ liệu hiện tại
            CustomerService::truncate();
            Customer::truncate();

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // Khôi phục customers
            foreach ($data['customers'] as $customerData) {
                // Loại bỏ timestamps để tạo mới
                unset($customerData['created_at'], $customerData['updated_at']);
                Customer::create($customerData);
            }

            // Khôi phục customer services
            if (isset($data['customer_services'])) {
                foreach ($data['customer_services'] as $serviceData) {
                    // Loại bỏ timestamps để tạo mới
                    unset($serviceData['created_at'], $serviceData['updated_at']);

                    // Đảm bảo các trường reminder có giá trị mặc định nếu không tồn tại
                    $serviceData['reminder_sent'] = $serviceData['reminder_sent'] ?? false;
                    $serviceData['reminder_sent_at'] = $serviceData['reminder_sent_at'] ?? null;
                    $serviceData['reminder_count'] = $serviceData['reminder_count'] ?? 0;
                    $serviceData['reminder_notes'] = $serviceData['reminder_notes'] ?? null;

                    CustomerService::create($serviceData);
                }
            }

            $this->command->info("✅ Khôi phục thành công từ backup {$fileName}!");
            $this->command->info("- Khách hàng: " . count($data['customers']));
            $this->command->info("- Dịch vụ: " . (count($data['customer_services'] ?? [])));
        } catch (\Exception $e) {
            $this->command->error("❌ Lỗi khi khôi phục từ backup: " . $e->getMessage());
        }
    }
}
