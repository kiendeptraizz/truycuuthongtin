<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;


class BackupController extends Controller
{
    private $backupPath;

    public function __construct()
    {
        $this->backupPath = storage_path('app/backups');
    }

    /**
     * Dashboard backup - trang chính
     */
    public function index()
    {
        $backupStats = $this->getBackupStatistics();
        $healthScore = $this->calculateHealthScore();
        $recentBackups = $this->getRecentBackups(5);

        return view('admin.backup.index', compact('backupStats', 'healthScore', 'recentBackups'));
    }

    /**
     * Danh sách tất cả backup
     */
    public function list()
    {
        $backups = $this->getAllBackups();

        return view('admin.backup.list', compact('backups'));
    }

    /**
     * Tạo backup manual
     */
    public function create(Request $request)
    {
        try {
            $backupType = $request->input('backup_type', 'database'); // 'database' hoặc 'complete'

            if ($backupType === 'complete') {
                // Chạy lệnh backup toàn bộ
                Artisan::call('backup:complete', ['--type' => 'manual']);
                $message = 'Backup toàn bộ hệ thống đã được tạo thành công!';
            } else {
                // Chạy lệnh backup database
                Artisan::call('backup:run', ['--type' => 'manual']);
                $message = 'Backup cơ sở dữ liệu đã được tạo thành công!';
            }

            $output = Artisan::output();

            // Parse output để lấy tên file backup
            preg_match('/✅ Backup.*: (.+)/', $output, $matches);
            $backupName = $matches[1] ?? 'Không xác định';

            return response()->json([
                'success' => true,
                'message' => $message,
                'backup_name' => $backupName,
                'backup_type' => $backupType,
                'output' => $output
            ]);
        } catch (\Exception $e) {
            Log::error('Manual backup failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tải xuống backup file
     */
    public function download($filename)
    {
        $filePath = $this->backupPath . '/' . $filename;

        if (!file_exists($filePath)) {
            abort(404, 'File backup không tồn tại');
        }

        return Response::download($filePath);
    }

    /**
     * Xóa backup file
     */
    public function delete($filename)
    {
        $filePath = $this->backupPath . '/' . $filename;

        if (!file_exists($filePath)) {
            return response()->json([
                'success' => false,
                'message' => 'File không tồn tại'
            ], 404);
        }

        try {
            unlink($filePath);

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa backup thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Khôi phục từ backup
     */
    public function restore(Request $request)
    {
        $filename = $request->input('filename');
        $filePath = $this->backupPath . '/' . $filename;

        if (!file_exists($filePath) || pathinfo($filePath, PATHINFO_EXTENSION) !== 'sql') {
            return response()->json(['success' => false, 'message' => 'File backup không hợp lệ hoặc không tồn tại.'], 404);
        }

        try {
            $connection = config('database.default');
            $dbConfig = config("database.connections.{$connection}");

            // Đảm bảo set charset UTF8MB4 trước khi restore
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::statement('SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;');

            $command = sprintf(
                'mysql --host=%s --port=%s --user=%s --password="%s" --default-character-set=utf8mb4 %s < %s',
                escapeshellarg($dbConfig['host']),
                escapeshellarg($dbConfig['port']),
                escapeshellarg($dbConfig['username']),
                $dbConfig['password'],
                escapeshellarg($dbConfig['database']),
                escapeshellarg($filePath)
            );

            exec($command, $output, $returnCode);

            if ($returnCode === 0) {
                // Sau khi restore, đảm bảo lại charset cho các bảng quan trọng
                $this->fixEncodingAfterRestore();

                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                Log::info("Database restored successfully from {$filename}.");
                return response()->json(['success' => true, 'message' => 'Cơ sở dữ liệu đã được khôi phục thành công!']);
            } else {
                throw new \Exception("Lỗi khi khôi phục CSDL. Mã lỗi: {$returnCode}. Chi tiết: " . implode("\n", $output));
            }
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            Log::error("Failed to restore database from {$filename}.", ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Báo cáo chi tiết
     */
    public function report()
    {
        try {
            Artisan::call('backup:monitor', ['--report' => true]);
            $reportOutput = Artisan::output();

            $backupTrends = $this->getBackupTrends();
            $storageUsage = $this->getStorageUsage();

            return view('admin.backup.report', compact('reportOutput', 'backupTrends', 'storageUsage'));
        } catch (\Exception $e) {
            return view('admin.backup.report', [
                'reportOutput' => 'Lỗi khi tạo báo cáo: ' . $e->getMessage(),
                'backupTrends' => [],
                'storageUsage' => []
            ]);
        }
    }

    /**
     * Lịch sử backup
     */
    public function history()
    {
        $logs = $this->getBackupLogs();

        return view('admin.backup.history', compact('logs'));
    }

    /**
     * Cài đặt backup
     */
    public function settings()
    {
        $currentSettings = $this->getCurrentSettings();

        return view('admin.backup.settings', compact('currentSettings'));
    }

    /**
     * Cập nhật cài đặt
     */
    public function updateSettings()
    {
        // TODO: Implement settings update
        return response()->json([
            'success' => true,
            'message' => 'Cài đặt đã được cập nhật'
        ]);
    }

    /**
     * API endpoint để lấy trạng thái backup real-time
     */
    public function status()
    {
        $stats = $this->getBackupStatistics();
        $health = $this->calculateHealthScore();

        return response()->json([
            'stats' => $stats,
            'health' => $health,
            'last_updated' => now()->toISOString()
        ]);
    }

    // ========================================================================
    // PRIVATE HELPER METHODS
    // ========================================================================

    private function getBackupStatistics()
    {
        // Lấy cả file .sql và .zip
        $sqlFiles = glob($this->backupPath . '/*.sql');
        $zipFiles = glob($this->backupPath . '/*.zip');
        $files = array_merge($sqlFiles, $zipFiles);

        if (empty($files)) {
            return [
                'total_backups' => 0,
                'total_size' => 0,
                'latest_backup' => null,
                'latest_time' => null,
                'latest_time_formatted' => null,
                'hours_ago' => 0,
                'daily_count' => 0,
                'manual_count' => 0,
                'complete_count' => 0,
                'database_count' => 0,
            ];
        }

        // Sắp xếp theo thời gian
        usort($files, function ($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        $totalSize = array_sum(array_map('filesize', $files));
        $latestFile = $files[0];

        // Đếm theo loại
        $dailyCount = count(array_filter($files, fn($f) => strpos(basename($f), '_daily_') !== false));
        $manualCount = count($files) - $dailyCount;
        $completeCount = count(array_filter($files, fn($f) => strpos(basename($f), 'COMPLETE_BACKUP') !== false));
        $databaseCount = count($files) - $completeCount;

        return [
            'total_backups' => count($files),
            'total_size' => $totalSize,
            'total_size_formatted' => $this->formatBytes($totalSize),
            'latest_backup' => basename($latestFile),
            'latest_time' => filemtime($latestFile),
            'latest_time_formatted' => date('Y-m-d H:i:s', filemtime($latestFile)),
            'hours_ago' => round((time() - filemtime($latestFile)) / 3600, 1),
            'daily_count' => $dailyCount,
            'manual_count' => $manualCount,
            'complete_count' => $completeCount,
            'database_count' => $databaseCount,
        ];
    }

    private function calculateHealthScore()
    {
        // Lấy cả file .sql và .zip
        $sqlFiles = glob($this->backupPath . '/*.sql');
        $zipFiles = glob($this->backupPath . '/*.zip');
        $files = array_merge($sqlFiles, $zipFiles);

        if (empty($files)) {
            return [
                'score' => 0,
                'status' => 'critical',
                'issues' => ['Không có backup nào']
            ];
        }

        $score = 100;
        $issues = [];

        // Kiểm tra backup gần đây
        usort($files, function ($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        $latestFile = $files[0];
        $hoursAgo = (time() - filemtime($latestFile)) / 3600;

        if ($hoursAgo > 25) {
            $score -= 30;
            $issues[] = "Backup mới nhất quá cũ (" . round($hoursAgo, 1) . " giờ)";
        }

        // Kiểm tra số lượng backup
        if (count($files) < 5) {
            $score -= 10;
            $issues[] = "Số lượng backup ít (" . count($files) . " < 5)";
        }

        // Ưu tiên cho complete backup
        $completeBackups = array_filter($files, fn($f) => strpos(basename($f), 'COMPLETE_BACKUP') !== false);
        if (empty($completeBackups)) {
            $score -= 20;
            $issues[] = "Không có backup toàn bộ hệ thống";
        }

        $status = $score >= 90 ? 'excellent' : ($score >= 70 ? 'good' : ($score >= 50 ? 'warning' : 'critical'));

        return [
            'score' => $score,
            'status' => $status,
            'issues' => $issues
        ];
    }

    private function getRecentBackups($limit = 5)
    {
        // Lấy cả file .sql và .zip
        $sqlFiles = glob($this->backupPath . '/*.sql');
        $zipFiles = glob($this->backupPath . '/*.zip');
        $files = array_merge($sqlFiles, $zipFiles);

        if (empty($files)) {
            return [];
        }

        // Sắp xếp theo thời gian
        usort($files, function ($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        $files = array_slice($files, 0, $limit);
        $backups = [];

        foreach ($files as $file) {
            $filename = basename($file);
            $extension = pathinfo($file, PATHINFO_EXTENSION);

            // Xác định loại backup
            $type = 'Database';
            if (strpos($filename, 'COMPLETE_BACKUP') !== false) {
                $type = 'Complete System';
            }

            $backups[] = [
                'filename' => $filename,
                'size' => filesize($file),
                'size_formatted' => $this->formatBytes(filesize($file)),
                'created_at' => filemtime($file),
                'created_at_formatted' => date('Y-m-d H:i:s', filemtime($file)),
                'created_at_human' => Carbon::createFromTimestamp(filemtime($file))->diffForHumans(),
                'type' => $type,
                'extension' => $extension,
                'status' => $this->verifyBackupIntegrity($file) ? 'success' : 'error'
            ];
        }

        return $backups;
    }

    private function getAllBackups()
    {
        // Lấy cả file .sql và .zip
        $sqlFiles = glob($this->backupPath . '/*.sql');
        $zipFiles = glob($this->backupPath . '/*.zip');
        $files = array_merge($sqlFiles, $zipFiles);

        if (empty($files)) {
            return [];
        }

        $backups = [];
        foreach ($files as $file) {
            $filename = basename($file);
            $extension = pathinfo($file, PATHINFO_EXTENSION);

            // Xác định loại backup
            $type = 'database';
            if (strpos($filename, 'COMPLETE_BACKUP') !== false) {
                $type = 'complete';
            }

            // Xác định frequency
            $frequency = 'manual';
            if (strpos($filename, '_daily_') !== false) {
                $frequency = 'daily';
            } elseif (strpos($filename, '_weekly_') !== false) {
                $frequency = 'weekly';
            }

            $backups[] = [
                'filename' => $filename,
                'path' => $file,
                'size' => filesize($file),
                'size_formatted' => $this->formatBytes(filesize($file)),
                'created_at' => filemtime($file),
                'created_at_formatted' => date('Y-m-d H:i:s', filemtime($file)),
                'created_at_human' => Carbon::createFromTimestamp(filemtime($file))->diffForHumans(),
                'type' => $type,
                'frequency' => $frequency,
                'extension' => $extension,
                'can_restore' => $extension === 'sql', // Chỉ restore được file SQL
                'status' => $this->verifyBackupIntegrity($file) ? 'success' : 'error'
            ];
        }

        // Sắp xếp theo thời gian tạo (mới nhất trước)
        usort($backups, function ($a, $b) {
            return $b['created_at'] - $a['created_at'];
        });

        return $backups;
    }

    private function getBackupType($filename)
    {
        if (strpos($filename, '_daily_') !== false) return 'Tự động';
        if (strpos($filename, '_manual_') !== false) return 'Thủ công';
        return 'Không xác định';
    }

    private function verifyBackupIntegrity($filePath)
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

        if ($extension === 'sql') {
            return file_exists($filePath) && filesize($filePath) > 0;
        } elseif ($extension === 'zip') {
            if (!file_exists($filePath) || filesize($filePath) === 0) {
                return false;
            }

            // Kiểm tra tính toàn vẹn của ZIP file
            $zip = new \ZipArchive();
            $result = $zip->open($filePath, \ZipArchive::CHECKCONS);
            $zip->close();

            return $result === true;
        }

        return false;
    }

    private function getBackupTrends()
    {
        // TODO: Implement backup trends analysis
        return [];
    }

    private function getStorageUsage()
    {
        // TODO: Implement storage usage analysis
        return [];
    }

    private function getBackupLogs()
    {
        // TODO: Implement backup logs parsing
        return [];
    }

    private function getCurrentSettings()
    {
        // TODO: Implement settings retrieval
        return [
            'daily_backup_time' => '02:00',
            'weekly_backup_day' => 'sunday',
            'weekly_backup_time' => '01:00',
            'quick_backup_interval' => 6,
            'max_backups_to_keep' => 30,
            'backup_format' => 'json',
            'enable_cloud_backup' => false
        ];
    }

    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }

        return round($size, $precision) . ' ' . $units[$i];
    }

    /**
     * Sửa encoding sau khi restore để đảm bảo UTF8MB4
     */
    private function fixEncodingAfterRestore()
    {
        try {
            // Đảm bảo database có charset utf8mb4
            DB::statement('ALTER DATABASE ' . config('database.connections.mysql.database') . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

            // Sửa charset cho các bảng quan trọng có chứa tiếng Việt
            $tables = [
                'customers',
                'family_members',
                'content_posts',
                'leads',
                'suppliers',
                'collaborators',
                'admins',
                'users'
            ];

            foreach ($tables as $table) {
                DB::statement("ALTER TABLE {$table} CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                Log::info("Fixed encoding for table: {$table}");
            }

            Log::info('Encoding fixed after restore completed.');
        } catch (\Exception $e) {
            Log::warning('Error fixing encoding after restore: ' . $e->getMessage());
        }
    }
}
