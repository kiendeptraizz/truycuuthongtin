<?php

namespace App\Console\Commands;

use App\Models\Customer;
use Illuminate\Console\Command;

class FixCustomerNames extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'customers:fix-names {--dry-run : Chỉ hiển thị các thay đổi, không lưu vào database}';

    /**
     * The console command description.
     */
    protected $description = 'Sửa tên khách hàng về đúng định dạng Tiếng Việt (Viết Hoa Đầu Mỗi Từ)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('🔍 Chế độ dry-run: Chỉ hiển thị các thay đổi, không lưu vào database.');
        }

        $customers = Customer::all();
        $fixedCount = 0;

        $this->info("Đang kiểm tra {$customers->count()} khách hàng...\n");

        foreach ($customers as $customer) {
            $rawName = $customer->getRawNameAttribute();
            $formattedName = $this->formatVietnameseName($rawName);

            if ($rawName !== $formattedName) {
                $fixedCount++;
                
                $this->line("ID: {$customer->id} | {$customer->customer_code}");
                $this->line("  <fg=red>Trước:</> {$rawName}");
                $this->line("  <fg=green>Sau:</>   {$formattedName}");
                $this->newLine();

                if (!$dryRun) {
                    // Bypass accessor để lưu trực tiếp
                    Customer::where('id', $customer->id)->update(['name' => $formattedName]);
                }
            }
        }

        $this->newLine();
        if ($fixedCount === 0) {
            $this->info('✅ Tất cả tên khách hàng đã đúng định dạng!');
        } else {
            if ($dryRun) {
                $this->warn("📋 Có {$fixedCount} tên cần sửa. Chạy lại không có --dry-run để lưu thay đổi.");
            } else {
                $this->info("✅ Đã sửa thành công {$fixedCount} tên khách hàng!");
            }
        }

        return Command::SUCCESS;
    }

    /**
     * Format tên tiếng Việt - Viết hoa đầu mỗi từ
     */
    private function formatVietnameseName(?string $name): ?string
    {
        if (empty($name)) {
            return $name;
        }

        // Chuyển về lowercase trước, sau đó viết hoa đầu mỗi từ
        $name = mb_strtolower($name, 'UTF-8');
        
        // Tách các từ và viết hoa chữ cái đầu
        $words = explode(' ', $name);
        $formattedWords = array_map(function ($word) {
            return mb_convert_case($word, MB_CASE_TITLE, 'UTF-8');
        }, $words);

        return implode(' ', $formattedWords);
    }
}

