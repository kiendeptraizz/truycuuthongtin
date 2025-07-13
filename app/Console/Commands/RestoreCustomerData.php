<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use App\Models\CustomerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RestoreCustomerData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:restore-customer-data {file?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Khôi phục dữ liệu khách hàng từ file backup';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $backupFile = $this->argument('file');

        if (!$backupFile) {
            // Hiển thị danh sách file backup có sẵn
            $this->showAvailableBackups();
            $this->line('');
            $backupFile = $this->ask('Nhập tên file backup muốn khôi phục');
        }

        if (!$backupFile) {
            $this->error('Vui lòng chọn file backup!');
            return 1;
        }

        $backupPath = storage_path('app/backups/' . $backupFile);

        if (!file_exists($backupPath)) {
            $this->error("File backup không tồn tại: {$backupPath}");
            return 1;
        }

        if (!$this->confirm("Bạn có chắc muốn khôi phục từ file {$backupFile}? Dữ liệu hiện tại sẽ bị thay thế!")) {
            $this->info('Hủy khôi phục dữ liệu.');
            return 0;
        }

        $this->info('Đang khôi phục dữ liệu...');

        try {
            $data = json_decode(file_get_contents($backupPath), true);

            if (!$data || !isset($data['customers'])) {
                $this->error('File backup không đúng định dạng!');
                return 1;
            }

            DB::beginTransaction();

            // Disable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Xóa dữ liệu hiện tại
            $this->info('Xóa dữ liệu hiện tại...');
            CustomerService::truncate();
            Customer::truncate();

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // Khôi phục customers
            $this->info('Khôi phục khách hàng...');
            foreach ($data['customers'] as $customerData) {
                Customer::create($customerData);
            }

            // Khôi phục customer services
            if (isset($data['customer_services'])) {
                $this->info('Khôi phục dịch vụ khách hàng...');
                foreach ($data['customer_services'] as $serviceData) {
                    CustomerService::create($serviceData);
                }
            }

            DB::commit();

            $this->info("✅ Khôi phục thành công!");
            $this->info("- Khách hàng: " . count($data['customers']));
            $this->info("- Dịch vụ: " . (count($data['customer_services'] ?? [])));
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Lỗi khi khôi phục: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function showAvailableBackups()
    {
        $backupDir = storage_path('app/backups');

        if (!is_dir($backupDir)) {
            $this->warn('Chưa có thư mục backup. Chạy lệnh backup trước!');
            return;
        }

        $files = glob($backupDir . '/*.json');

        if (empty($files)) {
            $this->warn('Không có file backup nào.');
            return;
        }

        $this->info('Danh sách file backup có sẵn:');
        foreach ($files as $file) {
            $fileName = basename($file);
            $fileSize = number_format(filesize($file) / 1024, 2);
            $fileTime = date('d/m/Y H:i:s', filemtime($file));
            $this->line("  📁 {$fileName} ({$fileSize}KB) - {$fileTime}");
        }
    }
}
