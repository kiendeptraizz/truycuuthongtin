<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CustomerService;

class ReminderReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:report 
                            {--days=5 : Số ngày trước khi hết hạn để kiểm tra}
                            {--reset : Reset trạng thái nhắc nhở cho tất cả}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Xem báo cáo trạng thái nhắc nhở khách hàng sắp hết hạn';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $reset = $this->option('reset');

        if ($reset) {
            if ($this->confirm('Bạn có chắc chắn muốn reset tất cả trạng thái nhắc nhở?')) {
                CustomerService::where('reminder_sent', true)->update([
                    'reminder_sent' => false,
                    'reminder_sent_at' => null,
                    'reminder_count' => 0,
                    'reminder_notes' => null
                ]);
                $this->info('✅ Đã reset tất cả trạng thái nhắc nhở.');
            }
            return 0;
        }

        $this->info("📊 BÁO CÁO TRẠNG THÁI NHẮC NHỞ (Trong {$days} ngày tới)");
        $this->info(str_repeat('=', 60));

        // Tổng quan
        $expiringSoon = CustomerService::expiringSoon($days)->count();
        $reminded = CustomerService::expiringSoon($days)->where('reminder_sent', true)->count();
        $notReminded = CustomerService::expiringSoon($days)->where('reminder_sent', false)->count();

        $this->info("📈 TỔNG QUAN:");
        $this->line("   • Tổng dịch vụ sắp hết hạn: {$expiringSoon}");
        $this->line("   • Đã nhắc nhở: {$reminded}");
        $this->line("   • Chưa nhắc nhở: {$notReminded}");
        $this->line("");

        // Chi tiết dịch vụ sắp hết hạn
        $services = CustomerService::with(['customer', 'servicePackage'])
            ->expiringSoon($days)
            ->orderBy('expires_at', 'asc') // Gần hết hạn nhất lên trước
            ->orderBy('reminder_sent', 'asc') // Chưa nhắc lên trước
            ->get();

        if ($services->isEmpty()) {
            $this->info('✅ Không có dịch vụ nào sắp hết hạn.');
            return 0;
        }

        $this->info("📋 CHI TIẾT DỊCH VỤ SẮP HẾT HẠN:");

        // Nhóm theo trạng thái nhắc nhở
        $reminded = $services->where('reminder_sent', true);
        $notReminded = $services->where('reminder_sent', false);

        if ($notReminded->isNotEmpty()) {
            $this->error("❌ CHƯA ĐƯỢC NHẮC NHỞ ({$notReminded->count()}):");
            $table = [];
            foreach ($notReminded as $service) {
                $table[] = [
                    'Khách hàng' => $service->customer->name,
                    'Email' => $service->customer->email,
                    'Dịch vụ' => $service->servicePackage->name ?? 'N/A',
                    'Hết hạn' => $service->expires_at->format('d/m/Y'),
                    'Còn lại' => $service->getDaysRemaining() . ' ngày',
                ];
            }
            $this->table(['Khách hàng', 'Email', 'Dịch vụ', 'Hết hạn', 'Còn lại'], $table);
            $this->line("");
        }

        if ($reminded->isNotEmpty()) {
            $this->info("✅ ĐÃ ĐƯỢC NHẮC NHỞ ({$reminded->count()}):");
            $table = [];
            foreach ($reminded as $service) {
                $needsAgain = $service->needsReminderAgain() ? '🔄 Cần nhắc lại' : '✅ OK';

                $table[] = [
                    'Khách hàng' => $service->customer->name,
                    'Email' => $service->customer->email,
                    'Dịch vụ' => $service->servicePackage->name ?? 'N/A',
                    'Hết hạn' => $service->expires_at->format('d/m/Y'),
                    'Còn lại' => $service->getDaysRemaining() . ' ngày',
                    'Số lần nhắc' => $service->reminder_count,
                    'Lần cuối' => $service->reminder_sent_at->format('d/m H:i'),
                    'Trạng thái' => $needsAgain,
                ];
            }
            $this->table([
                'Khách hàng',
                'Email',
                'Dịch vụ',
                'Hết hạn',
                'Còn lại',
                'Số lần nhắc',
                'Lần cuối',
                'Trạng thái'
            ], $table);
        }

        // Thống kê theo số ngày còn lại
        $this->line("");
        $this->info("📊 THỐNG KÊ THEO SỐ NGÀY CÒN LẠI:");
        $stats = [];
        for ($i = 0; $i <= $days; $i++) {
            $count = $services->filter(function ($service) use ($i) {
                return $service->getDaysRemaining() == $i;
            })->count();

            if ($count > 0) {
                $reminded_count = $services->filter(function ($service) use ($i) {
                    return $service->getDaysRemaining() == $i && $service->reminder_sent;
                })->count();

                $stats[] = [
                    'Ngày còn lại' => $i == 0 ? 'Hôm nay' : "{$i} ngày",
                    'Tổng số' => $count,
                    'Đã nhắc' => $reminded_count,
                    'Chưa nhắc' => $count - $reminded_count,
                ];
            }
        }

        if (!empty($stats)) {
            $this->table(['Ngày còn lại', 'Tổng số', 'Đã nhắc', 'Chưa nhắc'], $stats);
        }

        return 0;
    }
}
