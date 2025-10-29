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
    protected $signature = 'services:update-expired';

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
        $this->info('Đang kiểm tra và cập nhật các dịch vụ hết hạn...');

        // Lấy thời điểm hôm qua cuối ngày (23:59:59)
        $yesterday = Carbon::now()->subDay()->endOfDay();

        // Tìm các dịch vụ có status = 'active' nhưng đã hết hạn
        $expiredServices = CustomerService::where('status', 'active')
            ->where('expires_at', '<=', $yesterday)
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
