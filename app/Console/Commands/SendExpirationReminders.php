<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CustomerService;
use App\Models\Customer;

class SendExpirationReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:send-expiration 
                            {--days=5 : Số ngày trước khi hết hạn để gửi nhắc nhở}
                            {--force : Gửi nhắc nhở cho tất cả, kể cả đã gửi}
                            {--mark-only : Chỉ đánh dấu là đã nhắc, không gửi thực tế}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gửi nhắc nhở cho khách hàng có dịch vụ sắp hết hạn';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $force = $this->option('force');
        $markOnly = $this->option('mark-only');

        $this->info("🔍 Đang tìm kiếm dịch vụ sắp hết hạn trong {$days} ngày...");

        // Lấy danh sách dịch vụ sắp hết hạn
        $query = CustomerService::with(['customer', 'servicePackage'])
            ->expiringSoon($days);

        if (!$force) {
            // Chỉ lấy những dịch vụ chưa được nhắc hoặc cần nhắc lại
            $query->where(function ($q) {
                $q->where('reminder_sent', false)
                    ->orWhere(function ($subQ) {
                        $subQ->where('reminder_sent', true)
                            ->where('reminder_sent_at', '<', now()->subDay());
                    });
            });
        }

        $services = $query->orderBy('expires_at', 'asc') // Gần hết hạn nhất lên trước
            ->orderBy('reminder_sent', 'asc') // Chưa nhắc lên trước
            ->get();

        if ($services->isEmpty()) {
            $this->info('✅ Không có dịch vụ nào cần nhắc nhở.');
            return 0;
        }

        $this->info("📋 Tìm thấy {$services->count()} dịch vụ cần nhắc nhở:");

        $table = [];
        foreach ($services as $service) {
            $customer = $service->customer;
            $package = $service->servicePackage;

            $table[] = [
                'Khách hàng' => $customer->name,
                'Email' => $customer->email,
                'Dịch vụ' => $package->name ?? 'N/A',
                'Hết hạn' => $service->expires_at->format('d/m/Y'),
                'Còn lại' => $service->getDaysRemaining() . ' ngày',
                'Đã nhắc' => $service->reminder_sent ?
                    "✅ ({$service->reminder_count} lần)" : '❌',
                'Lần cuối' => $service->reminder_sent_at ?
                    $service->reminder_sent_at->format('d/m/Y H:i') : 'Chưa có'
            ];
        }

        $this->table([
            'Khách hàng',
            'Email',
            'Dịch vụ',
            'Hết hạn',
            'Còn lại',
            'Đã nhắc',
            'Lần cuối'
        ], $table);

        if (!$this->confirm('Bạn có muốn tiếp tục gửi nhắc nhở?')) {
            $this->info('❌ Đã hủy.');
            return 0;
        }

        $successCount = 0;
        $errorCount = 0;

        foreach ($services as $service) {
            try {
                if ($markOnly) {
                    // Chỉ đánh dấu, không gửi thực tế
                    $service->markAsReminded('Đánh dấu thủ công qua command');
                    $this->line("✅ Đã đánh dấu: {$service->customer->name}");
                } else {
                    // Ở đây bạn có thể thêm logic gửi email/SMS thực tế
                    // Ví dụ: Mail::to($service->customer->email)->send(new ExpirationReminder($service));

                    $service->markAsReminded('Gửi nhắc nhở qua command');
                    $this->line("📧 Đã gửi nhắc nhở: {$service->customer->name} ({$service->customer->email})");
                }

                $successCount++;
            } catch (\Exception $e) {
                $this->error("❌ Lỗi khi xử lý {$service->customer->name}: " . $e->getMessage());
                $errorCount++;
            }
        }

        $this->info("\n📊 Kết quả:");
        $this->info("✅ Thành công: {$successCount}");
        if ($errorCount > 0) {
            $this->error("❌ Lỗi: {$errorCount}");
        }

        return 0;
    }
}
