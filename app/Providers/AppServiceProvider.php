<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Console\Commands\DeleteAllCustomers;

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

        // Set timezone cho Carbon
        \Carbon\Carbon::setLocale('vi');

        // Register custom commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                DeleteAllCustomers::class,
            ]);
        }
    }
}
