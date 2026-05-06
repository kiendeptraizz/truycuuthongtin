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
// 🗑️ TỰ ĐỘNG XÓA DỊCH VỤ HẾT HẠN QUÁ 30 NGÀY
// ============================================================================

// Soft delete các dịch vụ đã hết hạn quá 30 ngày
// Chạy hàng ngày vào lúc 00:30 AM
Schedule::command('services:cleanup-expired --days=30')
    ->dailyAt('00:30')
    ->withoutOverlapping()
    ->runInBackground();

// ============================================================================
// 📧 CONTENT REMINDERS
// ============================================================================

// Schedule content reminders check every 15 minutes
Schedule::command('content:check-reminders')->everyFifteenMinutes();

// ============================================================================
// 🔔 TELEGRAM BOT — NHẮC ĐƠN HẾT HẠN 9H SÁNG
// ============================================================================

// Mỗi 9h sáng gửi list đơn hết hạn HÔM NAY + đã quá hạn + sắp hết hạn 3 ngày
// tới cho admin (lấy chat_id từ TELEGRAM_ADMIN_IDS).
Schedule::command('bot:notify-expirations')
    ->dailyAt('09:00')
    ->timezone('Asia/Ho_Chi_Minh')
    ->withoutOverlapping();

// Mỗi 30 phút check đơn pending stale > 30 phút chưa CK → nhắc admin.
// Chỉ chạy trong giờ làm việc (8h-22h) để không spam ban đêm.
Schedule::command('bot:notify-stale-pending')
    ->everyThirtyMinutes()
    ->between('8:00', '22:00')
    ->timezone('Asia/Ho_Chi_Minh')
    ->withoutOverlapping();
