<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Verify file backup .sql.gz có hợp lệ và restore được KHÔNG cần đụng DB thật.
 *
 * Cách dùng:
 *   php artisan backup:verify                   → verify file mới nhất
 *   php artisan backup:verify --file=name.sql.gz
 *   php artisan backup:verify --restore-test    → restore vào DB tạm để xác minh
 */
class BackupVerifyCommand extends Command
{
    protected $signature = 'backup:verify
        {--file= : Tên file backup (mặc định: file mới nhất)}
        {--restore-test : Tạo DB tạm và restore thử (xoá sau khi xong)}';

    protected $description = 'Kiểm tra tính toàn vẹn của file backup (checksum + cấu trúc SQL).';

    public function handle(): int
    {
        $backupPath = config('backup.path', storage_path('app/backups'));
        $file = $this->option('file');

        if (!$file) {
            $candidates = glob($backupPath . '/DB_BACKUP_*.sql.gz') ?: [];
            if (empty($candidates)) {
                $this->error('❌ Không tìm thấy file backup .sql.gz nào.');
                return 1;
            }
            usort($candidates, fn($a, $b) => filemtime($b) <=> filemtime($a));
            $file = basename($candidates[0]);
        }

        $fullPath = $backupPath . DIRECTORY_SEPARATOR . $file;
        if (!file_exists($fullPath)) {
            $this->error("❌ File không tồn tại: {$fullPath}");
            return 1;
        }

        $this->info("🔍 Kiểm tra: {$file}");
        $size = filesize($fullPath);
        $this->line('   Kích thước: ' . $this->formatBytes($size));

        // 1. Verify checksum nếu có file .sha256 đi kèm
        $shaFile = $fullPath . '.sha256';
        if (file_exists($shaFile)) {
            $expected = trim(explode(' ', file_get_contents($shaFile))[0] ?? '');
            $actual = hash_file('sha256', $fullPath);
            if (hash_equals($expected, $actual)) {
                $this->info('✅ Checksum SHA256 khớp.');
            } else {
                $this->error('❌ Checksum KHÔNG khớp! File có thể bị hỏng.');
                $this->line("   Expected: {$expected}");
                $this->line("   Actual:   {$actual}");
                return 2;
            }
        } else {
            $this->warn('⚠️  Không có file .sha256 đi kèm — bỏ qua checksum.');
        }

        // 2. Verify nội dung gzip — đọc thử + check chữ ký SQL
        $this->info('🗜️  Giải nén thử và kiểm tra cấu trúc SQL...');
        $gz = @gzopen($fullPath, 'rb');
        if (!$gz) {
            $this->error('❌ Không mở được file gzip.');
            return 3;
        }

        $headerSample = '';
        $tableCount = 0;
        $insertCount = 0;
        $bytesRead = 0;
        $maxScan = 10 * 1024 * 1024; // scan tối đa 10MB đầu — đủ cho hầu hết file

        while (!gzeof($gz) && $bytesRead < $maxScan) {
            $chunk = gzread($gz, 65536);
            if ($chunk === false) {
                gzclose($gz);
                $this->error('❌ Lỗi khi đọc file gzip — file có thể bị hỏng.');
                return 3;
            }
            if ($bytesRead === 0) {
                $headerSample = substr($chunk, 0, 200);
            }
            $tableCount  += substr_count($chunk, 'CREATE TABLE');
            $insertCount += substr_count($chunk, 'INSERT INTO');
            $bytesRead   += strlen($chunk);
        }
        gzclose($gz);

        if (!str_contains($headerSample, 'MySQL') && !str_contains($headerSample, 'mysqldump')) {
            $this->error('❌ Header file không giống mysqldump output.');
            return 4;
        }

        $this->info("✅ Cấu trúc SQL hợp lệ:");
        $this->line("   - CREATE TABLE: {$tableCount}");
        $this->line("   - INSERT INTO:  {$insertCount}");

        // 3. Restore test (optional) — tạo DB tạm rồi xoá
        if ($this->option('restore-test')) {
            $this->info('🧪 Restore vào DB tạm để xác minh...');
            return $this->runRestoreTest($fullPath);
        }

        $this->info('🎉 Backup hợp lệ.');
        return 0;
    }

    private function runRestoreTest(string $gzFile): int
    {
        $tempDb = '_backup_verify_' . substr(md5(uniqid('', true)), 0, 8);
        $this->line("   Tạo DB tạm: {$tempDb}");

        $cfg = config('database.connections.mysql');

        try {
            // Tạo DB tạm
            $pdoServer = new \PDO(
                sprintf('mysql:host=%s;port=%s', $cfg['host'], $cfg['port'] ?? 3306),
                $cfg['username'], $cfg['password']
            );
            $pdoServer->exec("CREATE DATABASE `{$tempDb}` DEFAULT CHARACTER SET utf8mb4");

            // Giải nén + tạo defaults file vào storage để tránh path có space (vd. C:\Users\Trung Kiên\Temp)
            $tmpDir = storage_path('app/backups/.tmp');
            if (!is_dir($tmpDir)) mkdir($tmpDir, 0755, true);
            $tmpSql   = $tmpDir . DIRECTORY_SEPARATOR . 'restoretest_' . uniqid() . '.sql';
            $defaults = $tmpDir . DIRECTORY_SEPARATOR . 'mysql_' . uniqid() . '.cnf';

            $gz = gzopen($gzFile, 'rb');
            $fh = fopen($tmpSql, 'wb');
            while (!gzeof($gz)) {
                fwrite($fh, gzread($gz, 1024 * 256));
            }
            gzclose($gz);
            fclose($fh);

            $mysqlBin = $this->resolveMysqlBin();
            file_put_contents(
                $defaults,
                "[client]\nhost={$cfg['host']}\nport={$cfg['port']}\nuser={$cfg['username']}\npassword=\"" . str_replace('"', '\\"', (string) $cfg['password']) . "\"\n"
            );
            @chmod($defaults, 0600);

            $cmd = sprintf(
                '"%s" --defaults-extra-file="%s" --default-character-set=utf8mb4 %s < "%s" 2>&1',
                $mysqlBin, $defaults, escapeshellarg($tempDb), $tmpSql
            );
            exec($cmd, $out, $rc);

            @unlink($defaults);
            @unlink($tmpSql);

            if ($rc !== 0) {
                $this->error("❌ Restore test thất bại (exit={$rc}). " . implode(' | ', $out));
                $pdoServer->exec("DROP DATABASE `{$tempDb}`");
                return 5;
            }

            // Verify có ít nhất 1 bảng + customers có data
            $pdoTemp = new \PDO(
                sprintf('mysql:host=%s;port=%s;dbname=%s', $cfg['host'], $cfg['port'] ?? 3306, $tempDb),
                $cfg['username'], $cfg['password']
            );
            $tableCount = $pdoTemp->query('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE()')->fetchColumn();
            $customerCount = (int) $pdoTemp->query('SELECT COUNT(*) FROM customers')->fetchColumn();

            $this->info("✅ Restore thành công: {$tableCount} tables, {$customerCount} customers.");

            // Dọn DB tạm
            $pdoTemp = null;
            $pdoServer->exec("DROP DATABASE `{$tempDb}`");
            $this->line("   Đã xoá DB tạm.");

            return 0;
        } catch (\Throwable $e) {
            $this->error('❌ Restore test exception: ' . $e->getMessage());
            try { $pdoServer->exec("DROP DATABASE IF EXISTS `{$tempDb}`"); } catch (\Throwable $ignore) {}
            return 6;
        }
    }

    private function resolveMysqlBin(): string
    {
        $candidates = [
            'C:\\laragon\\bin\\mysql\\mysql-8.4.3-winx64\\bin\\mysql.exe',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.30\\bin\\mysql.exe',
            '/usr/bin/mysql',
        ];
        foreach ($candidates as $p) {
            if (file_exists($p)) return $p;
        }
        $glob = glob('C:\\laragon\\bin\\mysql\\*\\bin\\mysql.exe');
        return !empty($glob) ? $glob[0] : 'mysql';
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < 3) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
