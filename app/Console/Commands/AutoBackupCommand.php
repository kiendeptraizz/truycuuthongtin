<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use ZipArchive;

class AutoBackupCommand extends Command
{
    protected $signature = 'backup:auto {--type=daily : Type of backup (daily/weekly)} {--format=both : Format (json/sql/both)}';
    protected $description = 'Tạo backup tự động với xác minh tính toàn vẹn';

    private $backupPath;
    private $backupInfo = [];
    private $errors = [];

    public function handle()
    {
        $this->info('🚀 Bắt đầu quá trình backup tự động...');

        $type = $this->option('type');
        $format = $this->option('format');

        $this->backupPath = storage_path('app/backups');

        try {
            // Tạo thư mục backup nếu chưa có
            if (!file_exists($this->backupPath)) {
                mkdir($this->backupPath, 0755, true);
            }

            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $backupName = "AUTO_BACKUP_{$type}_{$timestamp}";

            $this->info("📦 Tạo backup: {$backupName}");

            // Tạo backup theo định dạng
            if ($format === 'json' || $format === 'both') {
                $this->createJsonBackup($backupName);
            }

            if ($format === 'sql' || $format === 'both') {
                $this->createSqlBackup($backupName);
            }

            // Xác minh tính toàn vẹn
            $this->verifyBackupIntegrity($backupName);

            // Tạo file ZIP chứa tất cả backup (nếu có extension)
            if (class_exists('ZipArchive')) {
                $this->createZipBackup($backupName);
            } else {
                $this->warn('⚠️ ZipArchive extension không có, bỏ qua tạo ZIP file');
            }

            // Dọn dẹp backup cũ
            $this->cleanupOldBackups($type);

            // Gửi thông báo thành công
            $this->sendNotification(true, $backupName);

            $this->info('✅ Backup hoàn thành thành công!');
        } catch (\Exception $e) {
            $this->error('❌ Lỗi khi tạo backup: ' . $e->getMessage());
            $this->errors[] = $e->getMessage();
            $this->sendNotification(false, null, $e->getMessage());

            Log::error('Backup failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    private function createJsonBackup($backupName)
    {
        $this->info('📄 Tạo JSON backup...');

        $data = [
            'backup_info' => [
                'created_at' => Carbon::now()->toISOString(),
                'type' => $this->option('type'),
                'version' => '2.0',
                'description' => 'Backup tự động toàn diện'
            ]
        ];

        // Backup tất cả bảng quan trọng (chỉ những bảng tồn tại)
        $allTables = [
            'customers' => 'App\Models\Customer',
            'customer_services' => 'App\Models\CustomerService',
            'service_packages' => 'App\Models\ServicePackage',
            'categories' => 'App\Models\Category',
            'suppliers' => 'App\Models\Supplier',
            'leads' => 'App\Models\Lead'
        ];

        // Lọc chỉ những bảng thực sự tồn tại
        $tables = [];
        foreach ($allTables as $tableName => $modelClass) {
            if (Schema::hasTable($tableName)) {
                $tables[$tableName] = $modelClass;
            } else {
                $this->warn("  ⚠️ Bỏ qua bảng không tồn tại: {$tableName}");
            }
        }

        foreach ($tables as $tableName => $modelClass) {
            if (class_exists($modelClass)) {
                $data[$tableName] = $modelClass::all()->toArray();
                $count = count($data[$tableName]);
                $this->backupInfo[$tableName] = $count;
                $this->info("  ✓ {$tableName}: {$count} records");
            } else {
                // Fallback to raw DB query
                $data[$tableName] = DB::table($tableName)->get()->toArray();
                $count = count($data[$tableName]);
                $this->backupInfo[$tableName] = $count;
                $this->info("  ✓ {$tableName}: {$count} records (raw)");
            }
        }

        // Thêm thống kê
        $data['statistics'] = [
            'total_customers' => $this->backupInfo['customers'] ?? 0,
            'total_services' => $this->backupInfo['customer_services'] ?? 0,
            'total_packages' => $this->backupInfo['service_packages'] ?? 0,
            'total_categories' => $this->backupInfo['categories'] ?? 0,
            'total_suppliers' => $this->backupInfo['suppliers'] ?? 0,
            'total_leads' => $this->backupInfo['leads'] ?? 0,
        ];

        $jsonFile = $this->backupPath . "/{$backupName}.json";
        file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info("✅ JSON backup saved: " . basename($jsonFile));
    }

    private function createSqlBackup($backupName)
    {
        $this->info('🗄️ Tạo SQL backup...');

        $sqlFile = $this->backupPath . "/{$backupName}.sql";

        // Lấy thông tin database
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port', 3306);

        // Tạo SQL dump (chỉ các bảng tồn tại)
        $allTables = ['customers', 'customer_services', 'service_packages', 'categories', 'suppliers', 'leads'];
        $existingTables = [];

        foreach ($allTables as $table) {
            if (Schema::hasTable($table)) {
                $existingTables[] = $table;
            }
        }

        $tableList = implode(' ', $existingTables);

        $command = sprintf(
            'mysqldump --host=%s --port=%s --user=%s --password=%s --single-transaction --routines --triggers %s %s > %s',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database),
            $tableList,
            escapeshellarg($sqlFile)
        );

        exec($command, $output, $returnCode);

        if ($returnCode === 0 && file_exists($sqlFile)) {
            $this->info("✅ SQL backup saved: " . basename($sqlFile));
        } else {
            throw new \Exception("Failed to create SQL backup. Return code: {$returnCode}");
        }
    }

    private function verifyBackupIntegrity($backupName)
    {
        $this->info('🔍 Xác minh tính toàn vẹn backup...');

        $jsonFile = $this->backupPath . "/{$backupName}.json";

        if (file_exists($jsonFile)) {
            $data = json_decode(file_get_contents($jsonFile), true);

            if (!$data) {
                throw new \Exception('JSON backup is corrupted');
            }

            // Kiểm tra các bảng quan trọng
            $requiredTables = ['customers', 'customer_services', 'service_packages'];
            foreach ($requiredTables as $table) {
                if (!isset($data[$table])) {
                    throw new \Exception("Missing table in backup: {$table}");
                }
            }

            // Kiểm tra số lượng records
            $currentCounts = [
                'customers' => DB::table('customers')->count(),
                'customer_services' => DB::table('customer_services')->count(),
                'service_packages' => DB::table('service_packages')->count(),
            ];

            foreach ($currentCounts as $table => $currentCount) {
                $backupCount = count($data[$table]);
                if ($backupCount !== $currentCount) {
                    $this->warn("⚠️ Record count mismatch for {$table}: backup={$backupCount}, current={$currentCount}");
                }
            }

            $this->info('✅ Backup integrity verified');
        }
    }

    private function createZipBackup($backupName)
    {
        $this->info('📦 Tạo ZIP archive...');

        $zipFile = $this->backupPath . "/{$backupName}.zip";
        $zip = new ZipArchive();

        if ($zip->open($zipFile, ZipArchive::CREATE) === TRUE) {
            // Thêm JSON file
            $jsonFile = $this->backupPath . "/{$backupName}.json";
            if (file_exists($jsonFile)) {
                $zip->addFile($jsonFile, basename($jsonFile));
            }

            // Thêm SQL file
            $sqlFile = $this->backupPath . "/{$backupName}.sql";
            if (file_exists($sqlFile)) {
                $zip->addFile($sqlFile, basename($sqlFile));
            }

            // Thêm metadata
            $metadata = [
                'created_at' => Carbon::now()->toISOString(),
                'type' => $this->option('type'),
                'statistics' => $this->backupInfo,
                'files' => []
            ];

            if (file_exists($jsonFile)) {
                $metadata['files'][] = [
                    'name' => basename($jsonFile),
                    'size' => filesize($jsonFile),
                    'type' => 'json'
                ];
            }

            if (file_exists($sqlFile)) {
                $metadata['files'][] = [
                    'name' => basename($sqlFile),
                    'size' => filesize($sqlFile),
                    'type' => 'sql'
                ];
            }

            $zip->addFromString('backup_info.json', json_encode($metadata, JSON_PRETTY_PRINT));
            $zip->close();

            $this->info("✅ ZIP archive created: " . basename($zipFile));

            // Xóa file riêng lẻ để tiết kiệm dung lượng
            if (file_exists($jsonFile)) unlink($jsonFile);
            if (file_exists($sqlFile)) unlink($sqlFile);
        } else {
            throw new \Exception('Cannot create ZIP archive');
        }
    }

    private function cleanupOldBackups($type)
    {
        $this->info('🧹 Dọn dẹp backup cũ...');

        $files = glob($this->backupPath . "/AUTO_BACKUP_{$type}_*.zip");

        // Sắp xếp theo thời gian (mới nhất trước)
        usort($files, function ($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        $keepCount = 30; // Giữ lại 30 backup gần nhất
        $deletedCount = 0;

        for ($i = $keepCount; $i < count($files); $i++) {
            if (unlink($files[$i])) {
                $deletedCount++;
                $this->info("  🗑️ Deleted: " . basename($files[$i]));
            }
        }

        if ($deletedCount > 0) {
            $this->info("✅ Cleaned up {$deletedCount} old backups");
        } else {
            $this->info("✅ No old backups to clean up");
        }
    }

    private function sendNotification($success, $backupName = null, $error = null)
    {
        $message = $success
            ? "✅ Backup thành công: {$backupName}"
            : "❌ Backup thất bại: {$error}";

        // Log notification
        Log::info('Backup notification', [
            'success' => $success,
            'backup_name' => $backupName,
            'error' => $error,
            'statistics' => $this->backupInfo
        ]);

        $this->info("📧 " . $message);

        // TODO: Implement email notification if needed
        // Mail::raw($message, function($mail) {
        //     $mail->to('admin@example.com')->subject('Backup Notification');
        // });
    }
}
