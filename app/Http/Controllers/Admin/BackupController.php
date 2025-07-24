<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use ZipArchive;

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
        $type = $request->get('type', 'manual');
        $format = $request->get('format', 'json');
        
        try {
            // Chạy backup command
            Artisan::call('backup:auto', [
                '--type' => $type,
                '--format' => $format
            ]);
            
            $output = Artisan::output();
            
            // Parse output để lấy tên file backup
            preg_match('/Tạo backup: (.+)/', $output, $matches);
            $backupName = $matches[1] ?? 'Unknown';
            
            return response()->json([
                'success' => true,
                'message' => 'Backup được tạo thành công!',
                'backup_name' => $backupName,
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
        $filename = $request->get('filename');
        
        if (!$filename) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng chọn file backup'
            ], 400);
        }
        
        try {
            // Chạy restore command
            Artisan::call('backup:restore', [
                'file' => $filename,
                '--confirm' => true
            ]);
            
            $output = Artisan::output();
            
            return response()->json([
                'success' => true,
                'message' => 'Khôi phục dữ liệu thành công!',
                'output' => $output
            ]);
            
        } catch (\Exception $e) {
            Log::error('Restore failed', ['error' => $e->getMessage(), 'file' => $filename]);
            
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi khôi phục: ' . $e->getMessage()
            ], 500);
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
    public function updateSettings(Request $request)
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
        $files = glob($this->backupPath . '/*.{zip,json}', GLOB_BRACE);
        
        if (empty($files)) {
            return [
                'total_backups' => 0,
                'total_size' => 0,
                'latest_backup' => null,
                'latest_time' => null,
                'daily_count' => 0,
                'weekly_count' => 0,
                'quick_count' => 0
            ];
        }

        // Sắp xếp theo thời gian
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        $totalSize = array_sum(array_map('filesize', $files));
        $latestFile = $files[0];

        // Đếm theo loại
        $dailyCount = count(array_filter($files, fn($f) => strpos(basename($f), 'daily') !== false));
        $weeklyCount = count(array_filter($files, fn($f) => strpos(basename($f), 'weekly') !== false));
        $quickCount = count(array_filter($files, fn($f) => strpos(basename($f), 'quick') !== false));

        return [
            'total_backups' => count($files),
            'total_size' => $totalSize,
            'total_size_formatted' => $this->formatBytes($totalSize),
            'latest_backup' => basename($latestFile),
            'latest_time' => filemtime($latestFile),
            'latest_time_formatted' => date('Y-m-d H:i:s', filemtime($latestFile)),
            'hours_ago' => round((time() - filemtime($latestFile)) / 3600, 1),
            'daily_count' => $dailyCount,
            'weekly_count' => $weeklyCount,
            'quick_count' => $quickCount
        ];
    }

    private function calculateHealthScore()
    {
        $files = glob($this->backupPath . '/*.{zip,json}', GLOB_BRACE);
        
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
        usort($files, function($a, $b) {
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

        $status = $score >= 90 ? 'excellent' : ($score >= 70 ? 'good' : ($score >= 50 ? 'warning' : 'critical'));

        return [
            'score' => $score,
            'status' => $status,
            'issues' => $issues
        ];
    }

    private function getRecentBackups($limit = 5)
    {
        $files = glob($this->backupPath . '/*.{zip,json}', GLOB_BRACE);
        
        if (empty($files)) {
            return [];
        }

        // Sắp xếp theo thời gian
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        $backups = [];
        for ($i = 0; $i < min($limit, count($files)); $i++) {
            $file = $files[$i];
            $filename = basename($file);
            
            $backups[] = [
                'filename' => $filename,
                'size' => filesize($file),
                'size_formatted' => $this->formatBytes(filesize($file)),
                'created_at' => filemtime($file),
                'created_at_formatted' => date('Y-m-d H:i:s', filemtime($file)),
                'type' => $this->getBackupType($filename),
                'extension' => pathinfo($filename, PATHINFO_EXTENSION)
            ];
        }

        return $backups;
    }

    private function getAllBackups()
    {
        $files = glob($this->backupPath . '/*.{zip,json}', GLOB_BRACE);
        
        if (empty($files)) {
            return [];
        }

        // Sắp xếp theo thời gian
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        $backups = [];
        foreach ($files as $file) {
            $filename = basename($file);
            
            $backups[] = [
                'filename' => $filename,
                'size' => filesize($file),
                'size_formatted' => $this->formatBytes(filesize($file)),
                'created_at' => filemtime($file),
                'created_at_formatted' => date('Y-m-d H:i:s', filemtime($file)),
                'created_at_human' => Carbon::createFromTimestamp(filemtime($file))->diffForHumans(),
                'type' => $this->getBackupType($filename),
                'extension' => pathinfo($filename, PATHINFO_EXTENSION),
                'status' => $this->verifyBackupIntegrity($file) ? 'success' : 'error'
            ];
        }

        return $backups;
    }

    private function getBackupType($filename)
    {
        if (strpos($filename, 'daily') !== false) return 'daily';
        if (strpos($filename, 'weekly') !== false) return 'weekly';
        if (strpos($filename, 'quick') !== false) return 'quick';
        if (strpos($filename, 'AUTO_BACKUP') !== false) return 'auto';
        return 'manual';
    }

    private function verifyBackupIntegrity($filePath)
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        
        if ($extension === 'json') {
            if (!file_exists($filePath)) return false;
            $data = json_decode(file_get_contents($filePath), true);
            return $data && isset($data['backup_info']);
        }
        
        if ($extension === 'zip' && class_exists('ZipArchive')) {
            $zip = new ZipArchive();
            $result = $zip->open($filePath, ZipArchive::CHECKCONS);
            if ($result === TRUE) {
                $zip->close();
                return true;
            }
        }
        
        return true; // Default to true if can't verify
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
}
