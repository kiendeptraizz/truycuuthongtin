<?php

namespace App\Providers;

use App\Console\Commands\CompleteBackupCommand;
use App\Console\Commands\DeleteAllCustomers;
use App\Models\CustomerService;
use App\Models\PendingOrder;
use App\Models\RefundRequest;
use App\Observers\CustomerServiceObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Sử dụng Bootstrap cho pagination
        Paginator::useBootstrapFive();

        // Set timezone và locale cho Carbon
        \Carbon\Carbon::setLocale('vi');        // Register custom commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                DeleteAllCustomers::class,
                CompleteBackupCommand::class,
            ]);
        }

        // Share badge "Đơn chờ fill" — cache 60s để tránh count query mỗi page admin.
        // PendingOrder updating callback dưới đây sẽ flush cache khi status đổi.
        View::composer('layouts.admin', function ($view) {
            $count = Cache::remember(
                'admin.pending_orders_count',
                60,
                fn () => PendingOrder::where('status', 'pending')->count()
            );
            $view->with('pendingOrdersCount', $count);

            // Badge "Yêu cầu hoàn tiền" đang chờ xử lý.
            // Guard hasTable() để admin không vỡ nếu migration refund_requests chưa chạy.
            $refundPending = Schema::hasTable('refund_requests')
                ? Cache::remember('admin.refund_pending_count', 60, fn () => RefundRequest::where('status', 'pending')->count())
                : 0;
            $view->with('refundPendingCount', $refundPending);
        });

        PendingOrder::saved(function (PendingOrder $order) {
            if ($order->wasChanged('status') || $order->wasRecentlyCreated) {
                Cache::forget('admin.pending_orders_count');
            }
        });
        PendingOrder::deleted(fn () => Cache::forget('admin.pending_orders_count'));

        RefundRequest::saved(function (RefundRequest $req) {
            if ($req->wasChanged('status') || $req->wasRecentlyCreated) {
                Cache::forget('admin.refund_pending_count');
            }
        });
        RefundRequest::deleted(fn () => Cache::forget('admin.refund_pending_count'));

        // Audit log mọi thay đổi của CustomerService — ghi vào customer_service_audits
        CustomerService::observe(CustomerServiceObserver::class);
    }
}
