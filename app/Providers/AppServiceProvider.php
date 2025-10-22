<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Console\Commands\DeleteAllCustomers;
use App\Console\Commands\CompleteBackupCommand;

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

        // Helper function để format tiền VND
        if (!function_exists('format_currency')) {
            function format_currency($amount)
            {
                return number_format($amount, 0, ',', '.') . ' VNĐ';
            }
        }
    }
}
