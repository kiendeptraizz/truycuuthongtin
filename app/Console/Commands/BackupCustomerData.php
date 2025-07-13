<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use App\Models\CustomerService;
use Illuminate\Support\Facades\Storage;

class BackupCustomerData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backup-customer-data {--auto : Chạy tự động không cần xác nhận}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sao lưu toàn bộ dữ liệu khách hàng và dịch vụ';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isAuto = $this->option('auto');

        if (!$isAuto && !$this->confirm('Bạn có muốn tạo backup dữ liệu khách hàng?')) {
            $this->info('Hủy backup.');
            return 0;
        }

        $this->info('Đang tạo backup dữ liệu...');

        try {
            // Tạo thư mục backup nếu chưa có
            $backupDir = storage_path('app/backups');
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            // Lấy dữ liệu
            $customers = Customer::all()->toArray();
            $customerServices = CustomerService::all()->toArray();

            $backupData = [
                'backup_date' => now()->toDateTimeString(),
                'customers_count' => count($customers),
                'services_count' => count($customerServices),
                'customers' => $customers,
                'customer_services' => $customerServices,
            ];

            // Tạo tên file
            $fileName = 'customer_backup_' . now()->format('Y_m_d_H_i_s') . '.json';
            $filePath = $backupDir . '/' . $fileName;

            // Lưu file
            file_put_contents($filePath, json_encode($backupData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            $fileSize = number_format(filesize($filePath) / 1024, 2);

            $this->info("✅ Backup thành công!");
            $this->info("📁 File: {$fileName}");
            $this->info("📊 Dữ liệu: {$backupData['customers_count']} khách hàng, {$backupData['services_count']} dịch vụ");
            $this->info("💾 Kích thước: {$fileSize}KB");
            $this->info("📍 Đường dẫn: {$filePath}");

            // Dọn dẹp backup cũ (giữ lại 10 file gần nhất)
            $this->cleanupOldBackups($backupDir);
        } catch (\Exception $e) {
            $this->error("❌ Lỗi khi backup: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function cleanupOldBackups($backupDir)
    {
        $files = glob($backupDir . '/customer_backup_*.json');

        if (count($files) <= 10) {
            return;
        }

        // Sắp xếp theo thời gian tạo file (cũ nhất trước)
        usort($files, function ($a, $b) {
            return filemtime($a) - filemtime($b);
        });

        // Xóa những file cũ nhất (giữ lại 10 file)
        $filesToDelete = array_slice($files, 0, count($files) - 10);

        foreach ($filesToDelete as $file) {
            unlink($file);
            $this->line("🗑️  Đã xóa backup cũ: " . basename($file));
        }
    }
}
