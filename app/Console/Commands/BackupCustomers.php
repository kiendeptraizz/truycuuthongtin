<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use App\Models\CustomerService;
use Illuminate\Support\Facades\Storage;

class BackupCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:customers {--name= : Tên file backup tùy chỉnh}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup dữ liệu khách hàng và dịch vụ';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $customName = $this->option('name');
        $timestamp = now()->format('Y-m-d_H-i-s');
        $fileName = $customName ? "customer_backup_{$customName}_{$timestamp}.json" : "customer_backup_{$timestamp}.json";

        $this->info('🔄 Đang backup dữ liệu khách hàng...');

        try {
            // Lấy tất cả dữ liệu
            $customers = Customer::all()->toArray();
            $customerServices = CustomerService::with(['customer', 'servicePackage'])->get()->toArray();

            $backupData = [
                'backup_info' => [
                    'created_at' => now()->toISOString(),
                    'version' => '1.0',
                    'includes_reminder_fields' => true,
                    'description' => 'Backup bao gồm dữ liệu khách hàng và dịch vụ với trường nhắc nhở'
                ],
                'customers' => $customers,
                'customer_services' => $customerServices,
                'statistics' => [
                    'total_customers' => count($customers),
                    'total_services' => count($customerServices),
                    'services_with_reminders' => CustomerService::where('reminder_sent', true)->count(),
                    'expiring_soon' => CustomerService::expiringSoon(5)->count(),
                ]
            ];

            // Tạo thư mục backup nếu chưa có
            $backupDir = storage_path('app/backups');
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            // Lưu file backup
            $filePath = $backupDir . '/' . $fileName;
            file_put_contents($filePath, json_encode($backupData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            $fileSize = number_format(filesize($filePath) / 1024, 2);

            $this->info("✅ Backup thành công!");
            $this->info("📁 File: {$fileName}");
            $this->info("📊 Thống kê:");
            $this->line("   • Khách hàng: " . count($customers));
            $this->line("   • Dịch vụ: " . count($customerServices));
            $this->line("   • Đã nhắc nhở: " . $backupData['statistics']['services_with_reminders']);
            $this->line("   • Sắp hết hạn: " . $backupData['statistics']['expiring_soon']);
            $this->line("   • Kích thước: {$fileSize} KB");

            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Lỗi khi backup: " . $e->getMessage());
            return 1;
        }
    }
}
