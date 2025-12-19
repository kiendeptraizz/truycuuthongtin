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

        if ($includeToday) {
            $this->info('Đang kiểm tra và cập nhật các dịch vụ hết hạn (BAO GỒM HÔM NAY)...');
            // Bao gồm cả dịch vụ hết hạn hôm nay
            $cutoffDate = Carbon::now()->endOfDay();
        } else {
            $this->info('Đang kiểm tra và cập nhật các dịch vụ hết hạn...');
            // Chỉ lấy dịch vụ hết hạn từ hôm qua trở về trước (mặc định)
            $cutoffDate = Carbon::now()->subDay()->endOfDay();
        }

        // Tìm các dịch vụ có status = 'active' nhưng đã hết hạn
        $expiredServices = CustomerService::where('status', 'active')
            ->where('expires_at', '<=', $cutoffDate)
            ->get();

        $count = $expiredServices->count();

        if ($count === 0) {
            $this->info('✓ Không có dịch vụ nào cần cập nhật.');
            return 0;
        }

        $this->info("Tìm thấy {$count} dịch vụ cần cập nhật status...");

        // Hiển thị progress bar
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $updatedCount = 0;

        foreach ($expiredServices as $service) {
            $service->status = 'expired';
            if ($service->save()) {
                $updatedCount++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

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
