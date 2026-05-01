<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CustomerService;
use Carbon\Carbon;

class UpdateExpiredServices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:update-expired {--include-today : Bao gồm cả dịch vụ hết hạn trong ngày hôm nay}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động cập nhật status của các dịch vụ đã hết hạn từ "active" sang "expired"';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $includeToday = $this->option('include-today');

        // Đồng bộ với filter UI và isExpired(): dịch vụ hết hạn 30/4 = đã hết hạn từ 00:00 ngày 30/4
        // → cron chạy 00:05 ngày 30/4 sẽ set status=expired luôn cho các dịch vụ hết hạn 30/4.
        // Flag --include-today giữ lại để tương thích ngược nhưng hành vi giống nhau.
        $this->info('Đang kiểm tra và cập nhật các dịch vụ hết hạn' . ($includeToday ? ' (bao gồm hôm nay)' : '') . '...');
        $cutoffDate = Carbon::now()->endOfDay();

        // Đếm số dịch vụ cần cập nhật
        $count = CustomerService::where('status', 'active')
            ->where('expires_at', '<=', $cutoffDate)
            ->count();

        if ($count === 0) {
            $this->info('✓ Không có dịch vụ nào cần cập nhật.');
            return 0;
        }

        $this->info("Tìm thấy {$count} dịch vụ cần cập nhật status...");

        // Batch update thay vì loop từng record
        $updatedCount = CustomerService::where('status', 'active')
            ->where('expires_at', '<=', $cutoffDate)
            ->update(['status' => 'expired']);

        $this->info("✓ Đã cập nhật thành công {$updatedCount} dịch vụ từ 'active' sang 'expired'.");

        // Hiển thị thống kê
        $this->newLine();
        $this->table(
            ['Status', 'Số lượng'],
            [
                ['Đã cập nhật', $updatedCount],
                ['Thất bại', $count - $updatedCount],
            ]
        );

        return 0;
    }
}
