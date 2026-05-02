<?php

namespace App\Providers;

use App\Console\Commands\CompleteBackupCommand;
use App\Console\Commands\DeleteAllCustomers;
use App\Models\PendingOrder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
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
        });

        PendingOrder::saved(function (PendingOrder $order) {
            if ($order->wasChanged('status') || $order->wasRecentlyCreated) {
                Cache::forget('admin.pending_orders_count');
            }
        });
        PendingOrder::deleted(fn () => Cache::forget('admin.pending_orders_count'));
    }
}
