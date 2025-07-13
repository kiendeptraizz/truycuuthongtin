<?php

namespace App\Console\Commands;

use App\Models\CustomerService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class ViewTodayActivationsData extends Command
{
    protected $signature = 'today:view-data {--date= : Xem ngày cụ thể (Y-m-d)}';
    protected $description = 'Xem dữ liệu dịch vụ kích hoạt hôm nay THẬT (CHỈ ĐỌC, KHÔNG SỬA)';

    public function handle()
    {
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::today();
        $dateStr = $date->format('d/m/Y');

        $this->info("📊 XEM DỮ LIỆU KÍCH HOẠT NGÀY {$dateStr} (CHỈ ĐỌC)");
        $this->info('═══════════════════════════════════════════════════');

        $services = CustomerService::with(['customer', 'servicePackage'])
            ->whereDate('activated_at', $date)
            ->orderBy('activated_at', 'desc')
            ->get();

        if ($services->isEmpty()) {
            $this->warn("⚠️ Không có dịch vụ nào được kích hoạt ngày {$dateStr}");
            return 0;
        }

        $this->info("📈 Tổng quan ngày {$dateStr}:");
        $this->info("   • Tổng số dịch vụ kích hoạt: " . $services->count());

        // Thống kê theo trạng thái
        $statusStats = $services->groupBy('status');
        foreach ($statusStats as $status => $statusServices) {
            $icon = $status === 'active' ? '✅' : ($status === 'expired' ? '❌' : '⏸️');
            $this->info("   {$icon} {$status}: " . $statusServices->count());
        }

        // Thống kê theo gói dịch vụ
        $packageStats = $services->groupBy('servicePackage.name');
        $this->line('');
        $this->info("📦 Theo gói dịch vụ:");
        foreach ($packageStats as $packageName => $packageServices) {
            $this->info("   • {$packageName}: " . $packageServices->count());
        }

        $this->line('');
        $this->info("📋 Chi tiết từng dịch vụ:");
        $this->line('───────────────────────────────────────────────────');

        foreach ($services as $service) {
            $status = $service->status;
            $statusIcon = $status === 'active' ? '✅' : ($status === 'expired' ? '❌' : '⏸️');

            $this->line("{$statusIcon} {$service->customer->name}");
            $this->line("   📦 Dịch vụ: {$service->servicePackage->name}");
            $this->line("   📧 Email đăng nhập: {$service->login_email}");
            $this->line("   🚀 Kích hoạt: {$service->activated_at->format('d/m/Y H:i')}");
            if ($service->expires_at) {
                $expiryStatus = $service->expires_at->isPast() ? '❌ Đã hết hạn' : '✅ Còn hiệu lực';
                $this->line("   📅 Hết hạn: {$service->expires_at->format('d/m/Y')} ({$expiryStatus})");
            }
            if ($service->reminder_sent) {
                $reminderDate = $service->reminder_sent_at ? $service->reminder_sent_at->format('d/m/Y H:i') : 'N/A';
                $this->line("   🔔 Đã nhắc nhở: {$service->reminder_count} lần (lần cuối: {$reminderDate})");
            }
            $this->line('');
        }

        $this->info('✅ Hoàn thành xem dữ liệu thật. KHÔNG CÓ DỮ LIỆU NÀO BỊ THAY ĐỔI.');

        return 0;
    }
}
