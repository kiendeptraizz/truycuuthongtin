<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use ZipArchive;

class BackupMonitorCommand extends Command
{
    protected $signature = 'backup:monitor {--report : Generate detailed report} {--check : Check backup health}';
    protected $description = 'Giám sát và báo cáo tình trạng backup';

    private $backupPath;

    public function handle()
    {
        $this->backupPath = storage_path('app/backups');

        $this->info('🔍 GIÁM SÁT HỆ THỐNG BACKUP');
        $this->info('============================');

        if ($this->option('report')) {
            $this->generateDetailedReport();
        } elseif ($this->option('check')) {
            $this->checkBackupHealth();
        } else {
            $this->showQuickStatus();
        }
    }

    private function showQuickStatus()
    {
        $files = glob($this->backupPath . '/*.{zip,json}', GLOB_BRACE);

        if (empty($files)) {
            $this->error('❌ Không tìm thấy backup nào!');
            return;
        }

        // Sắp xếp theo thời gian
        usort($files, function ($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        $latestFile = $files[0];
        $latestTime = filemtime($latestFile);
        $hoursAgo = (time() - $latestTime) / 3600;

        $this->info("📊 TÌNH TRẠNG BACKUP:");
        $this->info("- Tổng số backup: " . count($files));
        $this->info("- Backup mới nhất: " . basename($latestFile));
        $this->info("- Thời gian: " . date('Y-m-d H:i:s', $latestTime) . " (" . round($hoursAgo, 1) . " giờ trước)");

        // Cảnh báo nếu backup quá cũ
        if ($hoursAgo > 25) { // Hơn 25 giờ
            $this->error("⚠️ CẢNH BÁO: Backup quá cũ! Cần kiểm tra hệ thống.");
        } else {
            $this->info("✅ Backup trong thời gian cho phép");
        }

        // Kiểm tra dung lượng
        $totalSize = 0;
        foreach ($files as $file) {
            $totalSize += filesize($file);
        }

        $this->info("- Tổng dung lượng: " . $this->formatBytes($totalSize));
    }

    private function generateDetailedReport()
    {
        $this->info('📋 BÁO CÁO CHI TIẾT HỆ THỐNG BACKUP');
        $this->info('=====================================');

        $files = glob($this->backupPath . '/*.{zip,json}', GLOB_BRACE);

        if (empty($files)) {
            $this->error('❌ Không tìm thấy backup nào!');
            return;
        }

        // Phân loại backup
        $dailyBackups = [];
        $weeklyBackups = [];
        $quickBackups = [];
        $otherBackups = [];

        foreach ($files as $file) {
            $fileName = basename($file);

            if (strpos($fileName, 'daily') !== false) {
                $dailyBackups[] = $file;
            } elseif (strpos($fileName, 'weekly') !== false) {
                $weeklyBackups[] = $file;
            } elseif (strpos($fileName, 'quick') !== false) {
                $quickBackups[] = $file;
            } else {
                $otherBackups[] = $file;
            }
        }

        // Báo cáo từng loại
        $this->reportBackupType('📅 BACKUP HÀNG NGÀY', $dailyBackups);
        $this->reportBackupType('📆 BACKUP HÀNG TUẦN', $weeklyBackups);
        $this->reportBackupType('⚡ BACKUP NHANH', $quickBackups);
        $this->reportBackupType('📁 BACKUP KHÁC', $otherBackups);

        // Thống kê tổng quan
        $this->info("\n📊 THỐNG KÊ TỔNG QUAN:");
        $this->info("- Daily backups: " . count($dailyBackups));
        $this->info("- Weekly backups: " . count($weeklyBackups));
        $this->info("- Quick backups: " . count($quickBackups));
        $this->info("- Other backups: " . count($otherBackups));

        $totalSize = array_sum(array_map('filesize', $files));
        $this->info("- Tổng dung lượng: " . $this->formatBytes($totalSize));

        // Kiểm tra xu hướng
        $this->analyzeBackupTrends($files);
    }

    private function reportBackupType($title, $files)
    {
        $this->info("\n{$title}:");

        if (empty($files)) {
            $this->warn("  Không có backup nào");
            return;
        }

        // Sắp xếp theo thời gian
        usort($files, function ($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        $count = 0;
        foreach ($files as $file) {
            if ($count >= 5) break; // Chỉ hiển thị 5 backup mới nhất

            $fileName = basename($file);
            $size = $this->formatBytes(filesize($file));
            $time = date('Y-m-d H:i:s', filemtime($file));

            $this->info("  • {$fileName} ({$size}) - {$time}");
            $count++;
        }

        if (count($files) > 5) {
            $remaining = count($files) - 5;
            $this->info("  ... và {$remaining} backup khác");
        }
    }

    private function checkBackupHealth()
    {
        $this->info('🏥 KIỂM TRA SỨC KHỎE BACKUP');
        $this->info('============================');

        $files = glob($this->backupPath . '/*.{zip,json}', GLOB_BRACE);
        $healthScore = 100;
        $issues = [];

        if (empty($files)) {
            $this->error('❌ NGHIÊM TRỌNG: Không có backup nào!');
            return;
        }

        // Kiểm tra backup gần đây
        usort($files, function ($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        $latestFile = $files[0];
        $hoursAgo = (time() - filemtime($latestFile)) / 3600;

        if ($hoursAgo > 25) {
            $healthScore -= 30;
            $issues[] = "Backup mới nhất quá cũ (" . round($hoursAgo, 1) . " giờ)";
        }

        // Kiểm tra tính toàn vẹn của 3 backup mới nhất
        $corruptedCount = 0;
        for ($i = 0; $i < min(3, count($files)); $i++) {
            if (!$this->verifyBackupIntegrity($files[$i])) {
                $corruptedCount++;
            }
        }

        if ($corruptedCount > 0) {
            $healthScore -= ($corruptedCount * 20);
            $issues[] = "{$corruptedCount} backup bị lỗi trong 3 backup mới nhất";
        }

        // Kiểm tra số lượng backup
        if (count($files) < 5) {
            $healthScore -= 10;
            $issues[] = "Số lượng backup ít (" . count($files) . " < 5)";
        }

        // Báo cáo kết quả
        $this->info("\n📊 KẾT QUẢ KIỂM TRA:");
        $this->info("Điểm sức khỏe: {$healthScore}/100");

        if ($healthScore >= 90) {
            $this->info("✅ Hệ thống backup hoạt động tốt");
        } elseif ($healthScore >= 70) {
            $this->warn("⚠️ Hệ thống backup cần chú ý");
        } else {
            $this->error("❌ Hệ thống backup có vấn đề nghiêm trọng");
        }

        if (!empty($issues)) {
            $this->info("\n🔍 CÁC VẤN ĐỀ PHÁT HIỆN:");
            foreach ($issues as $issue) {
                $this->warn("  • {$issue}");
            }
        }
    }

    private function analyzeBackupTrends($files)
    {
        $this->info("\n📈 PHÂN TÍCH XU HƯỚNG:");

        // Phân tích theo ngày
        $dailyStats = [];
        foreach ($files as $file) {
            $date = date('Y-m-d', filemtime($file));
            if (!isset($dailyStats[$date])) {
                $dailyStats[$date] = ['count' => 0, 'size' => 0];
            }
            $dailyStats[$date]['count']++;
            $dailyStats[$date]['size'] += filesize($file);
        }

        // Lấy 7 ngày gần nhất
        krsort($dailyStats);
        $recentDays = array_slice($dailyStats, 0, 7, true);

        foreach ($recentDays as $date => $stats) {
            $size = $this->formatBytes($stats['size']);
            $this->info("  • {$date}: {$stats['count']} backup, {$size}");
        }

        // Tính trung bình
        $avgCount = array_sum(array_column($recentDays, 'count')) / count($recentDays);
        $avgSize = array_sum(array_column($recentDays, 'size')) / count($recentDays);

        $this->info("  Trung bình: " . round($avgCount, 1) . " backup/ngày, " . $this->formatBytes($avgSize) . "/ngày");
    }

    private function verifyBackupIntegrity($filePath)
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

        if ($extension === 'zip') {
            return $this->verifyZipIntegrity($filePath);
        } elseif ($extension === 'json') {
            return $this->verifyJsonIntegrity($filePath);
        }

        return false;
    }

    private function verifyZipIntegrity($zipPath)
    {
        if (!class_exists('ZipArchive')) {
            return true; // Không thể kiểm tra, coi như OK
        }

        $zip = new ZipArchive();
        $result = $zip->open($zipPath, ZipArchive::CHECKCONS);

        if ($result === TRUE) {
            $zip->close();
            return true;
        }

        return false;
    }

    private function verifyJsonIntegrity($jsonPath)
    {
        if (!file_exists($jsonPath)) {
            return false;
        }

        $data = json_decode(file_get_contents($jsonPath), true);

        if (!$data) {
            return false;
        }

        // Kiểm tra các trường bắt buộc
        return isset($data['backup_info']) &&
            (isset($data['customers']) || isset($data['customer_services']));
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
