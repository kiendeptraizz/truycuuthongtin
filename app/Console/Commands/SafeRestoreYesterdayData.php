<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use App\Models\CustomerService;
use App\Models\ServicePackage;
use Illuminate\Support\Facades\DB;

class SafeRestoreYesterdayData extends Command
{
    protected $signature = 'restore:yesterday {--dry-run : Chỉ kiểm tra, không thực sự khôi phục} {--force : Bỏ qua xác nhận}';
    protected $description = 'Khôi phục dữ liệu từ backup ngày hôm qua (10/07/2025)';

    public function handle()
    {
        $backupFile = 'customer_backup_with-reminders_2025-07-10_17-34-31.json';
        $backupPath = storage_path('app/backups/' . $backupFile);

        if (!file_exists($backupPath)) {
            $this->error("❌ File backup hôm qua không tồn tại: {$backupPath}");
            return 1;
        }

        $this->info("📁 Đang khôi phục từ backup ngày 10/07/2025...");

        $backupData = json_decode(file_get_contents($backupPath), true);

        if (!$backupData) {
            $this->error('❌ File backup không hợp lệ!');
            return 1;
        }

        $this->info("📊 Thống kê backup hôm qua:");
        $this->info("   • Ngày tạo: " . $backupData['backup_info']['created_at']);
        $this->info("   • Khách hàng: " . count($backupData['customers']));
        $this->info("   • Dịch vụ: " . count($backupData['customer_services']));

        if ($this->option('dry-run')) {
            $this->info("🔍 CHẾ ĐỘ KIỂM TRA - Không thay đổi dữ liệu:");
            $this->line("");

            // Hiển thị mẫu khách hàng
            $this->info("👥 Khách hàng sẽ được khôi phục:");
            foreach (array_slice($backupData['customers'], 0, 5) as $customer) {
                $this->line("   • {$customer['name']} ({$customer['customer_code']})");
            }
            if (count($backupData['customers']) > 5) {
                $this->line("   ... và " . (count($backupData['customers']) - 5) . " khách hàng khác");
            }

            // Hiển thị mẫu dịch vụ
            $this->line("");
            $this->info("🔧 Dịch vụ sẽ được khôi phục:");
            foreach (array_slice($backupData['customer_services'], 0, 5) as $service) {
                $customer = collect($backupData['customers'])->firstWhere('id', $service['customer_id']);
                $this->line("   • {$customer['name']}: {$service['login_email']} (Package ID: {$service['service_package_id']})");
            }
            if (count($backupData['customer_services']) > 5) {
                $this->line("   ... và " . (count($backupData['customer_services']) - 5) . " dịch vụ khác");
            }

            return 0;
        }

        if (!$this->option('force')) {
            $this->warn("⚠️ CẢNH BÁO: Thao tác này sẽ:");
            $this->warn("   1. XÓA TẤT CẢ khách hàng và dịch vụ hiện tại");
            $this->warn("   2. Khôi phục " . count($backupData['customers']) . " khách hàng từ ngày 10/07");
            $this->warn("   3. Khôi phục " . count($backupData['customer_services']) . " dịch vụ từ ngày 10/07");

            if (!$this->confirm('Bạn có chắc chắn muốn tiếp tục?')) {
                $this->info('Đã hủy.');
                return 0;
            }
        }

        try {
            DB::beginTransaction();

            $this->info("🗑️ Xóa dữ liệu hiện tại...");
            CustomerService::query()->delete();
            Customer::query()->delete();

            $this->info("👥 Khôi phục khách hàng...");
            $restoredCustomers = 0;
            foreach ($backupData['customers'] as $customer) {
                // Loại bỏ ID để tránh xung đột
                $customerData = $customer;
                unset($customerData['id']);

                // Chuyển đổi định dạng datetime
                if (isset($customerData['created_at'])) {
                    $customerData['created_at'] = \Carbon\Carbon::parse($customerData['created_at'])->format('Y-m-d H:i:s');
                }
                if (isset($customerData['updated_at'])) {
                    $customerData['updated_at'] = \Carbon\Carbon::parse($customerData['updated_at'])->format('Y-m-d H:i:s');
                }

                // Tạo với ID cụ thể
                DB::table('customers')->insert(array_merge($customerData, ['id' => $customer['id']]));
                $restoredCustomers++;

                if ($restoredCustomers % 10 === 0) {
                    $this->line("   📈 Đã khôi phục: {$restoredCustomers}/" . count($backupData['customers']) . " khách hàng");
                }
            }

            $this->info("🔧 Khôi phục dịch vụ...");
            $restoredServices = 0;
            $skippedServices = 0;

            foreach ($backupData['customer_services'] as $service) {
                // Kiểm tra customer và package tồn tại
                $customerExists = DB::table('customers')->where('id', $service['customer_id'])->exists();
                $packageExists = DB::table('service_packages')->where('id', $service['service_package_id'])->exists();

                if (!$customerExists || !$packageExists) {
                    $skippedServices++;
                    continue;
                }

                // Loại bỏ ID để tránh xung đột
                $serviceData = $service;
                unset($serviceData['id']);

                // Đảm bảo các trường bắt buộc
                $serviceData['login_email'] = $serviceData['login_email'] ?? '';
                $serviceData['login_password'] = $serviceData['login_password'] ?? '';
                $serviceData['reminder_sent'] = $serviceData['reminder_sent'] ?? false;
                $serviceData['reminder_count'] = $serviceData['reminder_count'] ?? 0;

                // Chuyển đổi định dạng datetime
                if (isset($serviceData['created_at'])) {
                    $serviceData['created_at'] = \Carbon\Carbon::parse($serviceData['created_at'])->format('Y-m-d H:i:s');
                }
                if (isset($serviceData['updated_at'])) {
                    $serviceData['updated_at'] = \Carbon\Carbon::parse($serviceData['updated_at'])->format('Y-m-d H:i:s');
                }
                if (isset($serviceData['activated_at'])) {
                    $serviceData['activated_at'] = \Carbon\Carbon::parse($serviceData['activated_at'])->format('Y-m-d H:i:s');
                }
                if (isset($serviceData['expires_at'])) {
                    $serviceData['expires_at'] = \Carbon\Carbon::parse($serviceData['expires_at'])->format('Y-m-d H:i:s');
                }
                if (isset($serviceData['reminder_sent_at']) && $serviceData['reminder_sent_at']) {
                    $serviceData['reminder_sent_at'] = \Carbon\Carbon::parse($serviceData['reminder_sent_at'])->format('Y-m-d H:i:s');
                }

                CustomerService::create($serviceData);
                $restoredServices++;

                if ($restoredServices % 10 === 0) {
                    $this->line("   📈 Đã khôi phục: {$restoredServices}/" . (count($backupData['customer_services']) - $skippedServices) . " dịch vụ");
                }
            }

            DB::commit();

            $this->info("✅ KHÔI PHỤC THÀNH CÔNG!");
            $this->info("📊 Kết quả:");
            $this->info("   • Khách hàng đã khôi phục: {$restoredCustomers}");
            $this->info("   • Dịch vụ đã khôi phục: {$restoredServices}");
            if ($skippedServices > 0) {
                $this->warn("   • Dịch vụ bỏ qua (không hợp lệ): {$skippedServices}");
            }

            // Thống kê sau khôi phục
            $totalCustomers = Customer::count();
            $totalServices = CustomerService::count();
            $activeServices = CustomerService::where('status', 'active')->count();
            $expiredServices = CustomerService::where('status', 'expired')->count();

            $this->line("");
            $this->info("📈 Trạng thái sau khôi phục:");
            $this->info("   • Tổng khách hàng: {$totalCustomers}");
            $this->info("   • Tổng dịch vụ: {$totalServices}");
            $this->info("   • Dịch vụ hoạt động: {$activeServices}");
            $this->info("   • Dịch vụ hết hạn: {$expiredServices}");

            return 0;
        } catch (\Exception $e) {
            DB::rollback();
            $this->error("❌ Lỗi khi khôi phục: " . $e->getMessage());
            $this->error("Dữ liệu đã được rollback về trạng thái trước khi khôi phục.");
            return 1;
        }
    }
}
