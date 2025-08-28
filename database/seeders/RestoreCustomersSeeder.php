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

            // Chỉ khôi phục customers, không động vào customer services
            $existingCustomerIds = Customer::pluck('id')->toArray();
            $restoredCount = 0;
            $skippedCount = 0;

            // Khôi phục customers
            foreach ($data['customers'] as $customerData) {
                if (in_array($customerData['id'], $existingCustomerIds)) {
                    $skippedCount++;
                    continue;
                }

                try {
                    Customer::create($customerData);
                    $restoredCount++;
                } catch (\Exception $e) {
                    $this->command->warn("Bỏ qua customer ID {$customerData['id']}: " . $e->getMessage());
                    $skippedCount++;
                }
            }

            $this->command->info("✅ Khôi phục customers từ backup {$fileName}!");
            $this->command->info("- Khôi phục: {$restoredCount} customers");
            $this->command->info("- Bỏ qua: {$skippedCount} customers (đã tồn tại)");
        } catch (\Exception $e) {
            $this->command->error("❌ Lỗi khi khôi phục từ backup: " . $e->getMessage());
        }
    }
}
