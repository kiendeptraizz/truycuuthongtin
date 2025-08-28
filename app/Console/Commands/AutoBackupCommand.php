<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AutoBackupCommand extends Command
{
    protected $signature = 'backup:run {--type=manual : Type of backup (manual/daily)}';
    protected $description = 'Tạo backup toàn bộ cơ sở dữ liệu và dọn dẹp các bản sao lưu cũ.';

    private $backupPath;

    public function handle()
    {
        $this->info('🚀 Bắt đầu quá trình backup toàn bộ cơ sở dữ liệu...');

        $type = $this->option('type');
        $this->backupPath = storage_path('app/backups');

        try {
            if (!file_exists($this->backupPath)) {
                mkdir($this->backupPath, 0755, true);
            }

            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $backupName = "DB_BACKUP_{$type}_{$timestamp}";

            $this->info("📦 Tạo backup: {$backupName}.sql");

            $this->createSqlBackup($backupName);

            if ($type === 'daily') {
                $this->cleanupOldBackups();
            }

            $this->sendNotification(true, "{$backupName}.sql");

            $this->info('✅ Backup hoàn thành thành công!');
        } catch (\Exception $e) {
            $this->error('❌ Lỗi khi tạo backup: ' . $e->getMessage());
            $this->sendNotification(false, null, $e->getMessage());

            Log::error('Backup failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    private function createSqlBackup($backupName)
    {
        $this->info('🗄️ Tạo SQL backup...');
        $sqlFile = $this->backupPath . "/{$backupName}.sql";

        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port', 3306);

        // Tìm đường dẫn mysqldump cho Windows
        $mysqldumpPath = 'mysqldump';
        $possiblePaths = [
            'C:\\laragon\\bin\\mysql\\mysql-8.4.3-winx64\\bin\\mysqldump.exe',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.30\\bin\\mysqldump.exe',
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            'C:\\wamp64\\bin\\mysql\\mysql8.0.31\\bin\\mysqldump.exe'
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $mysqldumpPath = $path;
                break;
            }
        }

        $command = sprintf(
            '"%s" --host=%s --port=%s --user=%s --password="%s" --single-transaction --routines --triggers %s > "%s"',
            $mysqldumpPath,
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            $password,
            escapeshellarg($database),
            $sqlFile
        );

        // Thêm --column-statistics=0 để tránh lỗi với MySQL 8.0+
        $command = str_replace(
            '--single-transaction',
            '--single-transaction --column-statistics=0',
            $command
        );

        exec($command, $output, $returnCode);

        if ($returnCode === 0 && file_exists($sqlFile) && filesize($sqlFile) > 0) {
            $this->info("✅ SQL backup saved: " . basename($sqlFile));
        } else {
            if (file_exists($sqlFile)) {
                unlink($sqlFile);
            }
            throw new \Exception("Failed to create SQL backup. mysqldump exited with code: {$returnCode}. Output: " . implode("\n", $output));
        }
    }

    private function cleanupOldBackups()
    {
        $this->info('🧹 Dọn dẹp backup cũ...');

        $files = glob($this->backupPath . "/DB_BACKUP_daily_*.sql");
        if (empty($files)) {
            $this->info("✅ Không có backup cũ để dọn dẹp.");
            return;
        }

        $retentionDays = 30;
        $cutoffDate = Carbon::now()->subDays($retentionDays);
        $deletedCount = 0;

        foreach ($files as $file) {
            if (Carbon::createFromTimestamp(filemtime($file))->lt($cutoffDate)) {
                if (unlink($file)) {
                    $deletedCount++;
                    $this->info("  🗑️ Deleted: " . basename($file));
                }
            }
        }

        if ($deletedCount > 0) {
            $this->info("✅ Dọn dẹp xong {$deletedCount} backup cũ.");
        } else {
            $this->info("✅ Không có backup cũ nào cần dọn dẹp.");
        }
    }

    private function sendNotification($success, $backupName = null, $error = null)
    {
        $message = $success
            ? "✅ Backup thành công: {$backupName}"
            : "❌ Backup thất bại: {$error}";

        Log::info('Backup notification', [
            'success' => $success,
            'backup_name' => $backupName,
            'error' => $error
        ]);

        $this->info("📧 " . $message);
    }
}
