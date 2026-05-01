<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Đảm bảo có 1 backup database mỗi ngày bạn dùng app.
 *
 * Cơ chế:
 *  - Khi 1 admin truy cập dashboard, nếu hôm nay chưa có file DB_BACKUP_*_<today>_*.sql.gz
 *    → spawn background process chạy `php artisan backup:run --type=daily`.
 *  - Quá trình spawn KHÔNG BLOCK request → user vào dashboard ngay không bị chậm.
 *  - Cache flag để các request sau cùng ngày không kiểm tra lại.
 *
 * Phù hợp với máy local không bật 24/7 — không cần Task Scheduler.
 */
class EnsureDailyBackup
{
    public function handle(Request $request, Closure $next)
    {
        // Chỉ trigger 1 lần/ngày + chỉ với GET request (tránh trigger trên POST AJAX)
        if ($request->isMethod('GET')) {
            $this->maybeKickUpdateExpired();  // chạy trước cleanup để có status đúng
            $this->maybeKickBackup();
            $this->maybeKickCleanupExpired();
        }

        return $next($request);
    }

    /**
     * Tự chạy update-expired để đồng bộ status DB ('active' → 'expired') với reality.
     * Tránh số liệu dashboard sai (đếm cả những cái status='active' nhưng đã hết hạn).
     */
    private function maybeKickUpdateExpired(): void
    {
        $today = now()->format('Y-m-d');
        $cacheKey = "daily_update_expired_done_{$today}";

        if (Cache::has($cacheKey)) {
            return;
        }

        try {
            Cache::put($cacheKey, true, now()->endOfDay());
            $this->spawnArtisanCommand('services:update-expired');
            Log::info('EnsureDailyBackup: spawned update-expired', ['date' => $today]);
        } catch (\Throwable $e) {
            Log::warning('EnsureDailyBackup: update-expired spawn failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Tự chạy cleanup expired services (đẩy vào thùng rác sau 30 ngày).
     * Cùng cơ chế cache + spawn ngầm như backup.
     */
    private function maybeKickCleanupExpired(): void
    {
        $today = now()->format('Y-m-d');
        $cacheKey = "daily_cleanup_done_{$today}";

        if (Cache::has($cacheKey)) {
            return;
        }

        try {
            Cache::put($cacheKey, true, now()->endOfDay());
            $this->spawnArtisanCommand('services:cleanup-expired --days=30');
            Log::info('EnsureDailyBackup: spawned cleanup-expired', ['date' => $today]);
        } catch (\Throwable $e) {
            Log::warning('EnsureDailyBackup: cleanup spawn failed', ['error' => $e->getMessage()]);
        }
    }

    private function maybeKickBackup(): void
    {
        $today = now()->format('Y-m-d');
        $cacheKey = "daily_backup_done_{$today}";

        // Cache 24h — đã chạy hôm nay rồi thì không cần check disk nữa
        if (Cache::has($cacheKey)) {
            return;
        }

        try {
            $backupPath = config('backup.path', storage_path('app/backups'));

            // Có file backup nào của hôm nay chưa? (cả manual lẫn daily đều tính)
            $todayFiles = glob($backupPath . "/DB_BACKUP_*_{$today}_*.sql.gz") ?: [];
            if (!empty($todayFiles)) {
                Cache::put($cacheKey, true, now()->endOfDay());
                return;
            }

            // Set flag NGAY trước khi spawn — tránh nhiều request đồng thời cùng spawn
            // (TTL ngắn 5 phút phòng trường hợp spawn fail thì lần sau có thể thử lại)
            Cache::put($cacheKey . '_in_progress', true, now()->addMinutes(5));

            $this->spawnArtisanCommand('backup:run --type=daily');

            // Đánh dấu đã chạy — backup process sẽ chạy ngầm và tạo file thật
            Cache::put($cacheKey, true, now()->endOfDay());

            Log::info('EnsureDailyBackup: spawned background backup', ['date' => $today]);
        } catch (\Throwable $e) {
            // Không bao giờ throw từ middleware này — backup fail KHÔNG được phá request
            Log::warning('EnsureDailyBackup: failed to spawn', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Spawn 1 artisan command chạy ngầm, KHÔNG đợi.
     *
     * Trên Windows phải dùng wrapper .bat vì `start /B` qua popen() bị mất arguments
     * khi path có space hoặc nhiều lớp quote.
     */
    private function spawnArtisanCommand(string $artisanArgs): void
    {
        $phpBinary = $this->resolvePhpBinary();
        $artisan = base_path('artisan');
        $logFile = storage_path('logs/backup-spawned.log');

        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        if ($isWindows) {
            // Tạo wrapper .bat — cmd.exe parse trực tiếp file này, tránh quote nhiều lớp
            $wrapperDir = storage_path('app/backups/.tmp');
            if (!is_dir($wrapperDir)) {
                @mkdir($wrapperDir, 0755, true);
            }
            $wrapper = $wrapperDir . DIRECTORY_SEPARATOR . 'spawn-' . uniqid() . '.bat';

            $batContent = "@echo off\r\n"
                . 'cd /D "' . base_path() . '"' . "\r\n"
                . sprintf('"%s" "%s" %s >> "%s" 2>&1' . "\r\n", $phpBinary, $artisan, $artisanArgs, $logFile)
                . 'del "%~f0"' . "\r\n"; // self-delete sau khi chạy xong
            file_put_contents($wrapper, $batContent);

            $cmd = sprintf('start /B "" "%s"', $wrapper);
            pclose(popen($cmd, 'r'));
        } else {
            $cmd = sprintf(
                'nohup %s %s %s >> %s 2>&1 &',
                escapeshellarg($phpBinary),
                escapeshellarg($artisan),
                $artisanArgs,
                escapeshellarg($logFile)
            );
            exec($cmd);
        }
    }

    private function resolvePhpBinary(): string
    {
        // PHP_BINARY là path tới PHP đang chạy request hiện tại — luôn đúng version
        if (defined('PHP_BINARY') && file_exists(PHP_BINARY)) {
            return PHP_BINARY;
        }
        return 'php';
    }
}
