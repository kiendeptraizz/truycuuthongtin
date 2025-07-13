<?php

namespace App\Console\Commands;

use App\Models\CustomerService;
use App\Models\ServicePackage;
use Illuminate\Console\Command;

class ViewSharedAccountsData extends Command
{
    protected $signature = 'shared:view-data {--email= : Lọc theo email cụ thể}';
    protected $description = 'Xem dữ liệu tài khoản dùng chung THẬT (CHỈ ĐỌC, KHÔNG SỬA)';

    public function handle()
    {
        $this->info('📊 XEM DỮ LIỆU TÀI KHOẢN DÙNG CHUNG THẬT (CHỈ ĐỌC)');
        $this->info('═══════════════════════════════════════════════════');

        $query = CustomerService::with(['customer', 'servicePackage'])
            ->whereHas('servicePackage', function ($q) {
                $q->where('account_type', 'Team dùng chung');
            });

        if ($email = $this->option('email')) {
            $query->where('login_email', $email);
            $this->info("🔍 Lọc theo email: {$email}");
        }

        $services = $query->orderBy('login_email')
            ->orderBy('customer_id')
            ->get();

        if ($services->isEmpty()) {
            $this->warn('⚠️ Không tìm thấy dữ liệu tài khoản dùng chung nào.');
            $this->info('Điều này có thể là do:');
            $this->info('1. Chưa có dữ liệu thật nào');
            $this->info('2. Không có gói dịch vụ nào có account_type = "Team dùng chung"');
            return 0;
        }

        // Nhóm theo email
        $groupedByEmail = $services->groupBy('login_email');

        $this->info("📈 Tổng quan:");
        $this->info("   • Số email dùng chung: " . $groupedByEmail->count());
        $this->info("   • Tổng số dịch vụ: " . $services->count());
        $this->line('');

        foreach ($groupedByEmail as $email => $emailServices) {
            $this->info("📧 Email: {$email}");
            $this->info("   👥 Số khách hàng: " . $emailServices->count());

            foreach ($emailServices as $service) {
                $status = $service->status;
                $statusIcon = $status === 'active' ? '✅' : ($status === 'expired' ? '❌' : '⏸️');

                $this->line("   {$statusIcon} {$service->customer->name} | {$service->servicePackage->name} | {$status}");
                if ($service->expires_at) {
                    $this->line("      📅 Hết hạn: {$service->expires_at->format('d/m/Y')}");
                }
                if ($service->activated_at) {
                    $this->line("      🚀 Kích hoạt: {$service->activated_at->format('d/m/Y')}");
                }
                if ($service->reminder_sent) {
                    $reminderDate = $service->reminder_sent_at ? $service->reminder_sent_at->format('d/m/Y H:i') : 'N/A';
                    $this->line("      🔔 Đã nhắc nhở: {$service->reminder_count} lần (lần cuối: {$reminderDate})");
                }
                $this->line('');
            }
            $this->line('─────────────────────────────────────────');
        }

        $this->info('✅ Hoàn thành xem dữ liệu thật. KHÔNG CÓ DỮ LIỆU NÀO BỊ THAY ĐỔI.');

        return 0;
    }
}
