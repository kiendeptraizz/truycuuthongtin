<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use ZipArchive;

class RestoreBackupCommand extends Command
{
    protected $signature = 'backup:restore {file? : Backup file name} {--list : List available backups} {--format=json : Restore format (json/sql)} {--confirm : Skip confirmation}';
    protected $description = 'Khôi phục dữ liệu từ backup';

    private $backupPath;

    public function handle()
    {
        $this->backupPath = storage_path('app/backups');
        
        if ($this->option('list')) {
            $this->listAvailableBackups();
            return;
        }
        
        $file = $this->argument('file');
        
        if (!$file) {
            $file = $this->selectBackupFile();
        }
        
        if (!$file) {
            $this->error('❌ Không có file backup nào được chọn');
            return;
        }
        
        $this->restoreFromBackup($file);
    }
    
    private function listAvailableBackups()
    {
        $this->info('📋 DANH SÁCH BACKUP KHẢ DỤNG:');
        $this->info('================================');
        
        $files = glob($this->backupPath . '/*.{zip,json}', GLOB_BRACE);
        
        if (empty($files)) {
            $this->warn('Không tìm thấy file backup nào');
            return;
        }
        
        // Sắp xếp theo thời gian (mới nhất trước)
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        
        $headers = ['#', 'File Name', 'Size', 'Created', 'Type'];
        $rows = [];
        
        foreach ($files as $index => $file) {
            $fileName = basename($file);
            $size = $this->formatBytes(filesize($file));
            $created = date('Y-m-d H:i:s', filemtime($file));
            $type = pathinfo($file, PATHINFO_EXTENSION);
            
            // Thêm thông tin từ ZIP nếu có
            if ($type === 'zip') {
                $info = $this->getZipBackupInfo($file);
                if ($info) {
                    $type .= " ({$info['type']})";
                }
            }
            
            $rows[] = [$index + 1, $fileName, $size, $created, $type];
        }
        
        $this->table($headers, $rows);
    }
    
    private function selectBackupFile()
    {
        $files = glob($this->backupPath . '/*.{zip,json}', GLOB_BRACE);
        
        if (empty($files)) {
            $this->error('❌ Không tìm thấy file backup nào');
            return null;
        }
        
        // Sắp xếp theo thời gian (mới nhất trước)
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        
        $this->info('📋 Chọn file backup để khôi phục:');
        
        $choices = [];
        foreach ($files as $index => $file) {
            $fileName = basename($file);
            $created = date('Y-m-d H:i:s', filemtime($file));
            $choices[] = "{$fileName} ({$created})";
        }
        
        $selected = $this->choice('Chọn backup file:', $choices, 0);
        
        // Tìm file tương ứng
        foreach ($files as $index => $file) {
            if ($choices[$index] === $selected) {
                return basename($file);
            }
        }
        
        return null;
    }
    
    private function restoreFromBackup($fileName)
    {
        $filePath = $this->backupPath . '/' . $fileName;
        
        if (!file_exists($filePath)) {
            $this->error("❌ File backup không tồn tại: {$fileName}");
            return;
        }
        
        $this->info("🔄 Khôi phục từ backup: {$fileName}");
        
        // Xác nhận trước khi khôi phục
        if (!$this->option('confirm')) {
            if (!$this->confirm('⚠️ CẢNH BÁO: Thao tác này sẽ ghi đè toàn bộ dữ liệu hiện tại. Bạn có chắc chắn?')) {
                $this->info('❌ Hủy bỏ khôi phục');
                return;
            }
        }
        
        try {
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
            
            if ($extension === 'zip') {
                $this->restoreFromZip($filePath);
            } elseif ($extension === 'json') {
                $this->restoreFromJson($filePath);
            } else {
                throw new \Exception('Định dạng file không được hỗ trợ');
            }
            
            $this->info('✅ Khôi phục hoàn tất thành công!');
            
        } catch (\Exception $e) {
            $this->error('❌ Lỗi khi khôi phục: ' . $e->getMessage());
        }
    }
    
    private function restoreFromZip($zipPath)
    {
        $this->info('📦 Giải nén ZIP backup...');
        
        $zip = new ZipArchive();
        $tempDir = sys_get_temp_dir() . '/backup_restore_' . time();
        
        if ($zip->open($zipPath) === TRUE) {
            mkdir($tempDir, 0755, true);
            $zip->extractTo($tempDir);
            $zip->close();
            
            // Tìm file JSON hoặc SQL trong thư mục tạm
            $format = $this->option('format');
            
            if ($format === 'json' || $format === 'auto') {
                $jsonFiles = glob($tempDir . '/*.json');
                foreach ($jsonFiles as $jsonFile) {
                    if (basename($jsonFile) !== 'backup_info.json') {
                        $this->restoreFromJson($jsonFile);
                        break;
                    }
                }
            }
            
            if ($format === 'sql') {
                $sqlFiles = glob($tempDir . '/*.sql');
                if (!empty($sqlFiles)) {
                    $this->restoreFromSql($sqlFiles[0]);
                }
            }
            
            // Dọn dẹp thư mục tạm
            $this->deleteDirectory($tempDir);
            
        } else {
            throw new \Exception('Không thể mở file ZIP');
        }
    }
    
    private function restoreFromJson($jsonPath)
    {
        $this->info('📄 Khôi phục từ JSON backup...');
        
        $data = json_decode(file_get_contents($jsonPath), true);
        
        if (!$data) {
            throw new \Exception('File JSON không hợp lệ');
        }
        
        Schema::disableForeignKeyConstraints();
        
        try {
            // Thứ tự khôi phục quan trọng (để tránh lỗi foreign key)
            $restoreOrder = [
                'categories',
                'suppliers', 
                'service_packages',
                'customers',
                'customer_services',
                'leads'
            ];
            
            foreach ($restoreOrder as $table) {
                if (isset($data[$table]) && !empty($data[$table])) {
                    $this->restoreTable($table, $data[$table]);
                }
            }
            
            Schema::enableForeignKeyConstraints();
            
        } catch (\Exception $e) {
            Schema::enableForeignKeyConstraints();
            throw $e;
        }
    }
    
    private function restoreFromSql($sqlPath)
    {
        $this->info('🗄️ Khôi phục từ SQL backup...');
        
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port', 3306);
        
        $command = sprintf(
            'mysql --host=%s --port=%s --user=%s --password=%s %s < %s',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database),
            escapeshellarg($sqlPath)
        );
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new \Exception("SQL restore failed. Return code: {$returnCode}");
        }
    }
    
    private function restoreTable($tableName, $data)
    {
        $this->info("  🔄 Khôi phục bảng: {$tableName}");
        
        // Xóa dữ liệu hiện tại
        DB::table($tableName)->truncate();
        
        // Chèn dữ liệu từ backup
        $chunks = array_chunk($data, 100); // Chèn theo batch để tránh lỗi memory
        
        foreach ($chunks as $chunk) {
            DB::table($tableName)->insert($chunk);
        }
        
        $count = count($data);
        $this->info("    ✅ Đã khôi phục {$count} records");
    }
    
    private function getZipBackupInfo($zipPath)
    {
        $zip = new ZipArchive();
        
        if ($zip->open($zipPath) === TRUE) {
            $infoContent = $zip->getFromName('backup_info.json');
            $zip->close();
            
            if ($infoContent) {
                return json_decode($infoContent, true);
            }
        }
        
        return null;
    }
    
    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }
    
    private function deleteDirectory($dir)
    {
        if (!is_dir($dir)) return;
        
        $files = array_diff(scandir($dir), ['.', '..']);
        
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        
        rmdir($dir);
    }
}
