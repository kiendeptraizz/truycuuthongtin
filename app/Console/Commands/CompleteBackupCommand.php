<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use ZipArchive;

class CompleteBackupCommand extends Command
{
    protected $signature = 'backup:complete {--type=manual : Type of backup (manual/daily/weekly)}';
    protected $description = 'Tạo backup toàn bộ hệ thống: Database + Files + Config + Logs';

    private $backupPath;
    private $tempPath;

    public function handle()
    {
        $this->info('🚀 Bắt đầu quá trình backup toàn bộ hệ thống...');

        $type = $this->option('type');
        $this->backupPath = storage_path('app/backups');
        $this->tempPath = storage_path('app/temp-backup');

        try {
            // Tạo thư mục backup nếu chưa có
            $this->ensureDirectories();

            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $backupName = "COMPLETE_BACKUP_{$type}_{$timestamp}";

            $this->info("📦 Tạo backup: {$backupName}");

            // Tạo thư mục tạm
            $this->createTempDirectory($backupName);

            // 1. Backup Database
            $this->backupDatabase($backupName);

            // 2. Backup Files (Storage)
            $this->backupFiles($backupName);

            // 3. Backup Configuration
            $this->backupConfiguration($backupName);

            // 4. Backup Environment
            $this->backupEnvironment($backupName);

            // 5. Backup Logs
            $this->backupLogs($backupName);

            // 6. Backup Public Uploads
            $this->backupPublicUploads($backupName);

            // 7. Tạo file ZIP tổng hợp
            $this->createCompleteZip($backupName);

            // 8. Cleanup temp files
            $this->cleanupTemp($backupName);

            // 9. Cleanup old backups nếu cần
            if ($type === 'daily') {
                $this->cleanupOldBackups();
            }

            $this->sendNotification(true, "{$backupName}.zip");

            $this->info('✅ Backup toàn bộ hệ thống hoàn thành thành công!');
            $this->info("📁 File backup: {$backupName}.zip");
            $this->info("📊 Kích thước: " . $this->formatBytes(filesize($this->backupPath . "/{$backupName}.zip")));
        } catch (\Exception $e) {
            $this->error('❌ Lỗi khi tạo backup: ' . $e->getMessage());
            $this->sendNotification(false, null, $e->getMessage());

            Log::error('Complete backup failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    private function ensureDirectories()
    {
        if (!file_exists($this->backupPath)) {
            mkdir($this->backupPath, 0755, true);
        }
        if (!file_exists($this->tempPath)) {
            mkdir($this->tempPath, 0755, true);
        }
    }

    private function createTempDirectory($backupName)
    {
        $tempDir = $this->tempPath . "/{$backupName}";
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Tạo các thư mục con
        mkdir($tempDir . '/database', 0755, true);
        mkdir($tempDir . '/files', 0755, true);
        mkdir($tempDir . '/config', 0755, true);
        mkdir($tempDir . '/logs', 0755, true);
        mkdir($tempDir . '/public', 0755, true);
    }

    private function backupDatabase($backupName)
    {
        $this->info('🗄️ Backup Database...');

        $tempDir = $this->tempPath . "/{$backupName}";
        $sqlFile = $tempDir . '/database/database.sql';

        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port', 3306);

        // Tìm đường dẫn mysqldump cho Windows
        $mysqldumpPath = $this->findMysqlDumpPath();

        $command = sprintf(
            '"%s" --host=%s --port=%s --user=%s --password="%s" --default-character-set=utf8mb4 --single-transaction --routines --triggers --column-statistics=0 --result-file="%s" %s',
            $mysqldumpPath,
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            $password,
            $sqlFile,
            escapeshellarg($database)
        );

        exec($command, $output, $returnCode);

        if ($returnCode === 0 && file_exists($sqlFile) && filesize($sqlFile) > 0) {
            // Thêm charset declaration vào đầu file backup
            $this->addCharsetToBackupFile($sqlFile);

            $this->info("✅ Database backup: " . $this->formatBytes(filesize($sqlFile)));
        } else {
            throw new \Exception("Failed to create database backup. mysqldump exited with code: {$returnCode}");
        }
    }

    private function backupFiles($backupName)
    {
        $this->info('📁 Backup Storage Files...');

        $tempDir = $this->tempPath . "/{$backupName}";
        $storageSource = storage_path('app');
        $storageTarget = $tempDir . '/files';

        // Copy toàn bộ storage/app (trừ temp và backups)
        $this->copyDirectory($storageSource, $storageTarget, ['temp-backup', 'backups']);

        $this->info("✅ Storage files backup completed");
    }

    private function backupConfiguration($backupName)
    {
        $this->info('⚙️ Backup Configuration...');

        $tempDir = $this->tempPath . "/{$backupName}";
        $configSource = base_path('config');
        $configTarget = $tempDir . '/config';

        $this->copyDirectory($configSource, $configTarget);

        // Backup routes
        if (is_dir(base_path('routes'))) {
            $this->copyDirectory(base_path('routes'), $tempDir . '/config/routes');
        }

        // Backup composer files
        if (file_exists(base_path('composer.json'))) {
            copy(base_path('composer.json'), $tempDir . '/config/composer.json');
        }
        if (file_exists(base_path('composer.lock'))) {
            copy(base_path('composer.lock'), $tempDir . '/config/composer.lock');
        }

        // Backup package.json
        if (file_exists(base_path('package.json'))) {
            copy(base_path('package.json'), $tempDir . '/config/package.json');
        }

        $this->info("✅ Configuration backup completed");
    }

    private function backupEnvironment($backupName)
    {
        $this->info('🔧 Backup Environment...');

        $tempDir = $this->tempPath . "/{$backupName}";

        // Backup .env (nhưng mask sensitive data)
        if (file_exists(base_path('.env'))) {
            $envContent = file_get_contents(base_path('.env'));

            // Mask sensitive data
            $envContent = preg_replace('/^(.*PASSWORD.*=).*/m', '$1***MASKED***', $envContent);
            $envContent = preg_replace('/^(.*KEY.*=).*/m', '$1***MASKED***', $envContent);
            $envContent = preg_replace('/^(.*SECRET.*=).*/m', '$1***MASKED***', $envContent);

            file_put_contents($tempDir . '/config/env-template.txt', $envContent);
        }

        $this->info("✅ Environment backup completed");
    }

    private function backupLogs($backupName)
    {
        $this->info('📋 Backup Logs...');

        $tempDir = $this->tempPath . "/{$backupName}";
        $logsSource = storage_path('logs');
        $logsTarget = $tempDir . '/logs';

        if (is_dir($logsSource)) {
            $this->copyDirectory($logsSource, $logsTarget);
        }

        $this->info("✅ Logs backup completed");
    }

    private function backupPublicUploads($backupName)
    {
        $this->info('🖼️ Backup Public Uploads...');

        $tempDir = $this->tempPath . "/{$backupName}";

        // Backup public/storage
        if (is_dir(public_path('storage'))) {
            $this->copyDirectory(public_path('storage'), $tempDir . '/public/storage');
        }

        // Backup other uploaded files in public
        $publicDirs = ['css', 'js', 'images', 'uploads', 'files'];
        foreach ($publicDirs as $dir) {
            if (is_dir(public_path($dir))) {
                $this->copyDirectory(public_path($dir), $tempDir . "/public/{$dir}");
            }
        }

        $this->info("✅ Public uploads backup completed");
    }

    private function createCompleteZip($backupName)
    {
        $this->info('📦 Creating ZIP archive...');

        $tempDir = $this->tempPath . "/{$backupName}";
        $zipFile = $this->backupPath . "/{$backupName}.zip";

        $zip = new ZipArchive();
        if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            throw new \Exception("Cannot create ZIP file: {$zipFile}");
        }

        // Thêm metadata
        $metadata = [
            'backup_date' => Carbon::now()->toISOString(),
            'backup_type' => $this->option('type'),
            'app_name' => config('app.name'),
            'app_version' => '1.0.0',
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
        ];
        $zip->addFromString('backup-info.json', json_encode($metadata, JSON_PRETTY_PRINT));

        // Thêm tất cả files vào ZIP
        $this->addDirectoryToZip($zip, $tempDir, '');

        $zip->close();

        $this->info("✅ ZIP archive created: " . $this->formatBytes(filesize($zipFile)));
    }

    private function cleanupTemp($backupName)
    {
        $tempDir = $this->tempPath . "/{$backupName}";
        if (is_dir($tempDir)) {
            $this->removeDirectory($tempDir);
        }
    }

    private function cleanupOldBackups()
    {
        $this->info('🧹 Cleanup old backups...');

        $files = glob($this->backupPath . "/COMPLETE_BACKUP_daily_*.zip");
        if (empty($files)) {
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
            $this->info("✅ Cleaned up {$deletedCount} old backups.");
        }
    }

    private function findMysqlDumpPath()
    {
        $possiblePaths = [
            'C:\\laragon\\bin\\mysql\\mysql-8.4.3-winx64\\bin\\mysqldump.exe',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.30\\bin\\mysqldump.exe',
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            'C:\\wamp64\\bin\\mysql\\mysql8.0.31\\bin\\mysqldump.exe',
            'mysqldump' // fallback to system PATH
        ];

        foreach ($possiblePaths as $path) {
            if ($path === 'mysqldump' || file_exists($path)) {
                return $path;
            }
        }

        throw new \Exception('mysqldump not found. Please install MySQL client tools.');
    }

    private function copyDirectory($source, $destination, $exclude = [])
    {
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $sourceItemPath = $item->getRealPath();
            $relativePath = str_replace($source . DIRECTORY_SEPARATOR, '', $sourceItemPath);
            $relativePath = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);

            // Skip excluded directories
            $skip = false;
            foreach ($exclude as $excludeDir) {
                if (strpos($relativePath, $excludeDir) === 0) {
                    $skip = true;
                    break;
                }
            }
            if ($skip) continue;

            $targetPath = $destination . DIRECTORY_SEPARATOR . $relativePath;

            if ($item->isDir()) {
                if (!is_dir($targetPath)) {
                    mkdir($targetPath, 0755, true);
                }
            } else {
                copy($item->getRealPath(), $targetPath);
            }
        }
    }

    private function addDirectoryToZip($zip, $source, $prefix)
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $sourceItemPath = $item->getRealPath();
            $relativePath = str_replace($source . DIRECTORY_SEPARATOR, '', $sourceItemPath);
            $relativePath = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);
            $zipPath = $prefix ? $prefix . '/' . $relativePath : $relativePath;

            if ($item->isDir()) {
                $zip->addEmptyDir($zipPath);
            } else {
                $zip->addFile($item->getRealPath(), $zipPath);
            }
        }
    }

    private function removeDirectory($dir)
    {
        if (!is_dir($dir)) return;

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $item) {
            if ($item->isDir()) {
                rmdir($item->getRealPath());
            } else {
                unlink($item->getRealPath());
            }
        }

        rmdir($dir);
    }

    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }

        return round($size, $precision) . ' ' . $units[$i];
    }

    private function sendNotification($success, $backupName = null, $error = null)
    {
        $message = $success
            ? "✅ Complete backup successful: {$backupName}"
            : "❌ Complete backup failed: {$error}";

        Log::info('Complete backup notification', [
            'success' => $success,
            'backup_name' => $backupName,
            'error' => $error
        ]);

        $this->info("📧 " . $message);
    }

    /**
     * Thêm charset declaration vào đầu file backup SQL
     */
    private function addCharsetToBackupFile($sqlFile)
    {
        try {
            $content = file_get_contents($sqlFile);

            // Kiểm tra xem đã có charset declaration chưa
            if (strpos($content, 'SET NAMES utf8mb4') === false) {
                // Thêm charset declarations vào đầu file sau dòng comment đầu tiên
                $charsetDeclarations = "\n-- Charset declarations for Vietnamese support\n";
                $charsetDeclarations .= "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;\n";
                $charsetDeclarations .= "SET character_set_client = utf8mb4;\n";
                $charsetDeclarations .= "SET character_set_connection = utf8mb4;\n";
                $charsetDeclarations .= "SET character_set_results = utf8mb4;\n";
                $charsetDeclarations .= "SET collation_connection = utf8mb4_unicode_ci;\n\n";

                // Tìm vị trí sau dòng đầu tiên có mysqldump comment
                $pos = strpos($content, "-- MySQL dump");
                if ($pos !== false) {
                    $endLine = strpos($content, "\n", $pos);
                    if ($endLine !== false) {
                        $content = substr($content, 0, $endLine + 1) . $charsetDeclarations . substr($content, $endLine + 1);
                        file_put_contents($sqlFile, $content);
                    }
                }
            }
        } catch (\Exception $e) {
            $this->error("Warning: Could not add charset to backup file: " . $e->getMessage());
        }
    }
}
