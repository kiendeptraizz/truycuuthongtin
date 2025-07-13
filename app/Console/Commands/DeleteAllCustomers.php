<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\CustomerService;
use Illuminate\Console\Command;

class DeleteAllCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customers:delete-all {--force : Force delete without confirmation} {--i-understand-this-will-delete-real-data : Xác nhận hiểu rõ command này sẽ xóa dữ liệu thật}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all customers and their related services (NGUY HIỂM - CHỈ DÙNG KHI CHẮC CHẮN!)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('i-understand-this-will-delete-real-data')) {
            $this->error('❌ NGUY HIỂM: Command này sẽ XÓA TẤT CẢ dữ liệu khách hàng thật!');
            $this->error('Nếu bạn chắc chắn muốn xóa, hãy chạy:');
            $this->error('php artisan customers:delete-all --i-understand-this-will-delete-real-data --force');
            return 1;
        }

        $customerCount = Customer::count();
        $serviceCount = CustomerService::count();

        if ($customerCount === 0) {
            $this->info('Không có khách hàng nào để xóa.');
            return;
        }

        $this->line("Sẽ xóa:");
        $this->line("- {$customerCount} khách hàng");
        $this->line("- {$serviceCount} dịch vụ khách hàng");

        if (!$this->option('force')) {
            if (!$this->confirm('Bạn có chắc chắn muốn xóa TẤT CẢ khách hàng? Thao tác này KHÔNG THỂ hoàn tác!')) {
                $this->info('Đã hủy thao tác xóa.');
                return;
            }
        }

        $this->info('Đang xóa tất cả khách hàng...');

        try {
            // Xóa tất cả khách hàng (dịch vụ sẽ tự động xóa theo cascade)
            Customer::query()->delete();

            $this->info("✅ Đã xóa thành công {$customerCount} khách hàng và {$serviceCount} dịch vụ.");
        } catch (\Exception $e) {
            $this->error('❌ Có lỗi xảy ra khi xóa khách hàng: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
