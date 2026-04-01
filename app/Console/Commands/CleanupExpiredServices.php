<?php

namespace App\Console\Commands;

use App\Models\CustomerService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupExpiredServices extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'services:cleanup-expired 
                            {--days=30 : Số ngày hết hạn trước khi xóa}
                            {--dry-run : Chỉ hiển thị, không xóa thật}';

    /**
     * The console command description.
     */
    protected $description = 'Tự động xóa (soft delete) các dịch vụ đã hết hạn quá X ngày';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info("🔍 Chế độ dry-run: Chỉ hiển thị, không xóa thật.\n");
        }

        $this->info("Đang tìm các dịch vụ đã hết hạn quá {$days} ngày...\n");

        // Lấy các dịch vụ hết hạn > X ngày
        $expiredServices = CustomerService::expiredMoreThanDays($days)
            ->with(['customer', 'servicePackage'])
            ->get();

        if ($expiredServices->isEmpty()) {
            $this->info("✅ Không có dịch vụ nào cần xóa.");
            return Command::SUCCESS;
        }

        $this->info("Tìm thấy {$expiredServices->count()} dịch vụ cần xóa:\n");

        // Hiển thị danh sách
        $tableData = [];
        foreach ($expiredServices as $service) {
            $daysExpired = $service->expires_at 
                ? $service->expires_at->diffInDays(now()) 
                : 0;

            $tableData[] = [
                $service->id,
                $service->customer->name ?? 'N/A',
                $service->customer->customer_code ?? 'N/A',
                $service->servicePackage->name ?? 'N/A',
                $service->expires_at?->format('d/m/Y') ?? 'N/A',
                $daysExpired . ' ngày',
            ];
        }

        $this->table(
            ['ID', 'Khách hàng', 'Mã KH', 'Gói dịch vụ', 'Ngày hết hạn', 'Đã hết hạn'],
            $tableData
        );

        if ($dryRun) {
            $this->newLine();
            $this->warn("📋 Có {$expiredServices->count()} dịch vụ sẽ bị xóa.");
            $this->warn("Chạy lại không có --dry-run để xóa thật.");
            return Command::SUCCESS;
        }

        // Xác nhận nếu chạy từ terminal
        if ($this->input->isInteractive()) {
            if (!$this->confirm("Bạn có chắc muốn xóa {$expiredServices->count()} dịch vụ này?")) {
                $this->info('Đã hủy.');
                return Command::SUCCESS;
            }
        }

        // Thực hiện soft delete
        $deletedCount = 0;
        $errors = [];

        foreach ($expiredServices as $service) {
            try {
                $service->delete(); // Soft delete
                $deletedCount++;

                Log::info("Đã xóa dịch vụ hết hạn", [
                    'service_id' => $service->id,
                    'customer_id' => $service->customer_id,
                    'customer_code' => $service->customer->customer_code ?? 'N/A',
                    'service_package' => $service->servicePackage->name ?? 'N/A',
                    'expires_at' => $service->expires_at?->format('Y-m-d'),
                    'days_expired' => $service->expires_at?->diffInDays(now()),
                ]);
            } catch (\Exception $e) {
                $errors[] = "ID {$service->id}: " . $e->getMessage();
                Log::error("Lỗi xóa dịch vụ", [
                    'service_id' => $service->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->newLine();

        if ($deletedCount > 0) {
            $this->info("✅ Đã xóa thành công {$deletedCount} dịch vụ.");
        }

        if (!empty($errors)) {
            $this->error("❌ Có {$errors} lỗi xảy ra:");
            foreach ($errors as $error) {
                $this->line("  - {$error}");
            }
        }

        return Command::SUCCESS;
    }
}

