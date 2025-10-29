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

// Chạy backup database hàng ngày vào lúc 2:00 AM
Schedule::command('backup:run --type=daily')
    ->dailyAt('02:00')
    ->withoutOverlapping()
    ->runInBackground();

// Chạy backup toàn bộ hệ thống hàng tuần vào Chủ nhật lúc 1:00 AM
Schedule::command('backup:complete --type=weekly')
    ->weeklyOn(0, '01:00') // 0 = Sunday
    ->withoutOverlapping()
    ->runInBackground();

// Chạy backup toàn bộ hệ thống hàng ngày vào lúc 3:00 AM (sau backup database)
Schedule::command('backup:complete --type=daily')
    ->dailyAt('03:00')
    ->withoutOverlapping()
    ->runInBackground();

// ============================================================================
// 🔄 CẬP NHẬT STATUS DỊCH VỤ HẾT HẠN
// ============================================================================

// Tự động cập nhật status của các dịch vụ đã hết hạn từ 'active' sang 'expired'
// Chạy hàng ngày vào lúc 00:05 AM (sau nửa đêm 5 phút)
Schedule::command('services:update-expired')
    ->dailyAt('00:05')
    ->withoutOverlapping()
    ->runInBackground();

// ============================================================================
// 📧 CONTENT REMINDERS
// ============================================================================

// Schedule content reminders check every 15 minutes
Schedule::command('content:check-reminders')->everyFifteenMinutes();
