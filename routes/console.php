<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ============================================================================
// 🔄 HỆ THỐNG BACKUP TỰ ĐỘNG TOÀN DIỆN
// ============================================================================

// Backup hàng ngày vào lúc 2:00 AM (JSON + SQL)
Schedule::command('backup:auto --type=daily --format=both')
    ->dailyAt('02:00')
    ->withoutOverlapping()
    ->runInBackground();

// Backup hàng tuần vào Chủ nhật lúc 1:00 AM (JSON + SQL)
Schedule::command('backup:auto --type=weekly --format=both')
    ->weeklyOn(0, '01:00')
    ->withoutOverlapping()
    ->runInBackground();

// Backup nhanh mỗi 6 giờ (chỉ JSON để tiết kiệm dung lượng)
Schedule::command('backup:auto --type=quick --format=json')
    ->everySixHours()
    ->withoutOverlapping()
    ->runInBackground();

// ============================================================================
// 📧 CONTENT REMINDERS
// ============================================================================

// Schedule content reminders check every 15 minutes
Schedule::command('content:check-reminders')->everyFifteenMinutes();

// ============================================================================
// ☁️ CLOUD BACKUP TỰ ĐỘNG
// ============================================================================

// Upload backup lên cloud sau khi tạo backup hàng ngày
Schedule::command('backup:cloud --provider=local')
    ->dailyAt('02:30')
    ->withoutOverlapping()
    ->runInBackground();

// Giám sát backup hàng ngày
Schedule::command('backup:monitor --check')
    ->dailyAt('08:00')
    ->withoutOverlapping();

// Báo cáo backup hàng tuần
Schedule::command('backup:monitor --report')
    ->weeklyOn(1, '09:00')
    ->withoutOverlapping();

// ============================================================================
// 🔧 LEGACY BACKUP (Giữ lại để tương thích)
// ============================================================================

// Schedule automatic customer data backup daily at 2 AM
Schedule::command('app:backup-customer-data --auto')->dailyAt('02:00');
