<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ============================================================================
// 🔄 HỆ THỐNG BACKUP TỰ ĐỘNG
// ============================================================================

// Chạy backup toàn bộ CSDL và dọn dẹp file cũ hàng ngày vào lúc 2:00 AM
Schedule::command('backup:run --type=daily')
    ->dailyAt('02:00')
    ->withoutOverlapping()
    ->runInBackground();

// ============================================================================
// 📧 CONTENT REMINDERS
// ============================================================================

// Schedule content reminders check every 15 minutes
Schedule::command('content:check-reminders')->everyFifteenMinutes();
