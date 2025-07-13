<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule content reminders check every 15 minutes
Schedule::command('content:check-reminders')->everyFifteenMinutes();

// Schedule automatic customer data backup daily at 2 AM
Schedule::command('app:backup-customer-data --auto')->dailyAt('02:00');

// Schedule automatic customer data backup every 6 hours (for safety)
Schedule::command('app:backup-customer-data --auto')->everySixHours();
