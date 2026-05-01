<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AutoBackupCommand extends Command
{
    protected $signature = 'backup:run {--type=manual : Type of backup (manual/daily)}';
    protected $description = 'Tạo backup database (gzip + checksum + mirror sang ổ phụ).';

    private string $backupPath;
    private int $retentionDays;
    private ?string $mirrorPath;

    public function handle()
    {
        $this->info('🚀 Bắt đầu quá trình backup database...');

        $type = $this->option('type');
        $this->backupPath  = config('backup.path', storage_path('app/backups'));
        $this->retentionDays = (int) config('backup.retention_days', 30);
        $this->mirrorPath  = config('backup.mirror_path') ?: null;

        try {
            if (!file_exists($this->backupPath)) {
                mkdir($this->backupPath, 0755, true);
            }

            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $backupName = "DB_BACKUP_{$type}_{$timestamp}";
            $sqlFile = $this->backupPath . "/{$backupName}.sql";
            $gzFile  = $sqlFile . '.gz';

            $this->info("📦 Tạo backup: {$backupName}.sql.gz");

            $this->createSqlBackup($sqlFile);
            $this->compressFile($sqlFile, $gzFile);
            @unlink($sqlFile); // bỏ file .sql plain sau khi đã nén

            $checksum = $this->writeChecksum($gzFile);
            $size = filesize($gzFile);

            // Mirror sang folder phụ (vd. ổ D, network drive, USB)
            if ($this->mirrorPath) {
                $this->mirrorFile($gzFile);
            }

            // Dọn backup cũ — daily mới dọn, manual giữ tay
            if ($type === 'daily') {
                $this->cleanupOldBackups();
            }

            $this->sendNotification(true, basename($gzFile), null, [
                'size' => $this->formatBytes($size),
                'sha256' => substr($checksum, 0, 16) . '…',
                'mirror' => $this->mirrorPath ? 'yes' : 'no',
            ]);

            $this->info('✅ Backup hoàn thành: ' . $this->formatBytes($size) . ', sha256=' . substr($checksum, 0, 16) . '…');
        } catch (\Exception $e) {
            $this->error('❌ Lỗi khi tạo backup: ' . $e->getMessage());
            $this->sendNotification(false, null, $e->getMessage());

            Log::error('Backup failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }

        return 0;
    }

    /**
     * Dump SQL bằng mysqldump.
     * Dùng defaults-extra-file để truyền password qua file tạm — không lộ trên command line.
     */
    private function createSqlBackup(string $sqlFile): void
    {
        $this->info('🗄️  Đang dump SQL...');

        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host     = config('database.connections.mysql.host');
        $port     = config('database.connections.mysql.port', 3306);

        $mysqldumpPath = $this->resolveMysqldumpPath();

        // Tạo file config tạm để mysqldump đọc credentials (không lộ password trên CLI).
        // Đặt trong storage/app/backups/.tmp để tránh path có space (vd. C:\Users\Trung Kiên\Temp)
        $tmpDir = $this->backupPath . DIRECTORY_SEPARATOR . '.tmp';
        if (!is_dir($tmpDir)) {
            @mkdir($tmpDir, 0755, true);
        }
        $defaultsFile = $tmpDir . DIRECTORY_SEPARATOR . 'mysqldump_' . uniqid() . '.cnf';
        $defaultsContent = "[client]\n"
            . "host={$host}\n"
            . "port={$port}\n"
            . "user={$username}\n"
            . "password=\"" . str_replace('"', '\\"', (string) $password) . "\"\n";
        file_put_contents($defaultsFile, $defaultsContent);
        @chmod($defaultsFile, 0600);

        try {
            $command = sprintf(
                '"%s" --defaults-extra-file="%s" --default-character-set=utf8mb4 --single-transaction --quick --routines --triggers --column-statistics=0 --result-file="%s" %s 2>&1',
                $mysqldumpPath,
                $defaultsFile,
                $sqlFile,
                escapeshellarg($database)
            );

            exec($command, $output, $returnCode);

            if ($returnCode !== 0 || !file_exists($sqlFile) || filesize($sqlFile) === 0) {
                @unlink($sqlFile);
                throw new \RuntimeException("mysqldump thất bại (exit={$returnCode}). " . implode(' | ', $output));
            }

            $this->info("✅ Dump xong: " . $this->formatBytes(filesize($sqlFile)));
        } finally {
            // Luôn xoá file credential tạm
            @unlink($defaultsFile);
        }
    }

    private function compressFile(string $source, string $target): void
    {
        $this->info('🗜️  Đang nén gzip...');

        $fpIn  = fopen($source, 'rb');
        $fpOut = gzopen($target, 'wb9');
        if (!$fpIn || !$fpOut) {
            throw new \RuntimeException("Không mở được file để nén.");
        }
        while (!feof($fpIn)) {
            gzwrite($fpOut, fread($fpIn, 1024 * 512));
        }
        fclose($fpIn);
        gzclose($fpOut);

        $ratio = round((1 - filesize($target) / filesize($source)) * 100, 1);
        $this->info("✅ Nén xong, giảm {$ratio}% kích thước");
    }

    private function writeChecksum(string $file): string
    {
        $sha = hash_file('sha256', $file);
        file_put_contents($file . '.sha256', $sha . '  ' . basename($file) . PHP_EOL);
        return $sha;
    }

    private function mirrorFile(string $source): void
    {
        if (!is_dir($this->mirrorPath)) {
            if (!@mkdir($this->mirrorPath, 0755, true) && !is_dir($this->mirrorPath)) {
                $this->warn("⚠️  Không tạo được mirror folder: {$this->mirrorPath}");
                return;
            }
        }
        $dest = rtrim($this->mirrorPath, '/\\') . DIRECTORY_SEPARATOR . basename($source);
        if (!@copy($source, $dest)) {
            $this->warn("⚠️  Mirror thất bại: {$dest}");
            return;
        }
        @copy($source . '.sha256', $dest . '.sha256');
        $this->info("📋 Đã copy sang mirror: {$dest}");
    }

    private function resolveMysqldumpPath(): string
    {
        $configured = env('MYSQLDUMP_PATH');
        if ($configured && file_exists($configured)) {
            return $configured;
        }

        $candidates = [
            'C:\\laragon\\bin\\mysql\\mysql-8.4.3-winx64\\bin\\mysqldump.exe',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.30\\bin\\mysqldump.exe',
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
        ];
        foreach ($candidates as $p) {
            if (file_exists($p)) {
                return $p;
            }
        }
        // Auto-detect dưới Laragon
        $laragonMysql = glob('C:\\laragon\\bin\\mysql\\*\\bin\\mysqldump.exe');
        if (!empty($laragonMysql)) {
            return $laragonMysql[0];
        }
        // Fallback PATH
        return 'mysqldump';
    }

    private function cleanupOldBackups(): void
    {
        $this->info('🧹 Dọn dẹp backup cũ...');

        // Cả file .sql.gz lẫn .sha256 đi kèm
        $files = array_merge(
            glob($this->backupPath . "/DB_BACKUP_daily_*.sql.gz") ?: [],
            glob($this->backupPath . "/DB_BACKUP_daily_*.sql.gz.sha256") ?: [],
            glob($this->backupPath . "/DB_BACKUP_daily_*.sql") ?: [], // legacy
        );

        if (empty($files)) {
            $this->info("✅ Không có backup cũ.");
            return;
        }

        $cutoffDate = Carbon::now()->subDays($this->retentionDays);
        $deletedCount = 0;

        foreach ($files as $file) {
            if (Carbon::createFromTimestamp(filemtime($file))->lt($cutoffDate)) {
                if (@unlink($file)) {
                    $deletedCount++;
                    $this->line("  🗑️ Xoá: " . basename($file));
                }
            }
        }

        $this->info("✅ Đã xoá {$deletedCount} file cũ (giữ {$this->retentionDays} ngày).");
    }

    private function sendNotification(bool $success, ?string $backupName = null, ?string $error = null, array $extra = []): void
    {
        Log::info('Backup ' . ($success ? 'success' : 'failure'), array_merge([
            'success' => $success,
            'backup_name' => $backupName,
            'error' => $error,
        ], $extra));
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
