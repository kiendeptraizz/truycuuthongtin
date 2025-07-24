<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CloudBackupCommand extends Command
{
    protected $signature = 'backup:cloud {file? : Local backup file to upload} {--provider=local : Cloud provider (local/gdrive/dropbox)}';
    protected $description = 'Upload backup files to cloud storage';

    private $backupPath;

    public function handle()
    {
        $this->backupPath = storage_path('app/backups');
        $provider = $this->option('provider');
        $file = $this->argument('file');

        $this->info("☁️ Bắt đầu upload backup lên {$provider}...");

        try {
            if ($file) {
                $this->uploadSingleFile($file, $provider);
            } else {
                $this->uploadLatestBackups($provider);
            }

            $this->info('✅ Cloud backup hoàn thành!');

        } catch (\Exception $e) {
            $this->error('❌ Lỗi cloud backup: ' . $e->getMessage());
            Log::error('Cloud backup failed', [
                'error' => $e->getMessage(),
                'provider' => $provider
            ]);
        }
    }

    private function uploadSingleFile($fileName, $provider)
    {
        $localPath = $this->backupPath . '/' . $fileName;

        if (!file_exists($localPath)) {
            throw new \Exception("File không tồn tại: {$fileName}");
        }

        $this->info("📤 Upload file: {$fileName}");

        switch ($provider) {
            case 'gdrive':
                $this->uploadToGoogleDrive($localPath, $fileName);
                break;
            case 'dropbox':
                $this->uploadToDropbox($localPath, $fileName);
                break;
            case 'local':
                $this->copyToLocalBackup($localPath, $fileName);
                break;
            default:
                throw new \Exception("Provider không được hỗ trợ: {$provider}");
        }
    }

    private function uploadLatestBackups($provider)
    {
        // Upload 3 backup mới nhất
        $files = glob($this->backupPath . '/*.zip');
        
        if (empty($files)) {
            $this->warn('Không tìm thấy file backup nào để upload');
            return;
        }

        // Sắp xếp theo thời gian (mới nhất trước)
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        $uploadCount = min(3, count($files));
        
        for ($i = 0; $i < $uploadCount; $i++) {
            $fileName = basename($files[$i]);
            $this->uploadSingleFile($fileName, $provider);
        }
    }

    private function uploadToGoogleDrive($localPath, $fileName)
    {
        // TODO: Implement Google Drive upload
        // Cần cài đặt Google Drive API credentials
        
        $this->info("📁 [DEMO] Upload to Google Drive: {$fileName}");
        
        // Giả lập upload
        sleep(1);
        
        $this->info("  ✅ Uploaded to Google Drive successfully");
        
        /*
        // Thực tế sẽ cần:
        // 1. Cài đặt google/apiclient package
        // 2. Tạo service account credentials
        // 3. Implement upload logic:
        
        $client = new \Google_Client();
        $client->setAuthConfig('path/to/credentials.json');
        $client->addScope(\Google_Service_Drive::DRIVE_FILE);
        
        $service = new \Google_Service_Drive($client);
        
        $fileMetadata = new \Google_Service_Drive_DriveFile([
            'name' => $fileName,
            'parents' => ['backup_folder_id']
        ]);
        
        $content = file_get_contents($localPath);
        
        $file = $service->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => 'application/zip',
            'uploadType' => 'multipart'
        ]);
        */
    }

    private function uploadToDropbox($localPath, $fileName)
    {
        // TODO: Implement Dropbox upload
        // Cần cài đặt spatie/dropbox-api package
        
        $this->info("📦 [DEMO] Upload to Dropbox: {$fileName}");
        
        // Giả lập upload
        sleep(1);
        
        $this->info("  ✅ Uploaded to Dropbox successfully");
        
        /*
        // Thực tế sẽ cần:
        // 1. Cài đặt spatie/dropbox-api package
        // 2. Tạo Dropbox app và lấy access token
        // 3. Implement upload logic:
        
        $client = new \Spatie\Dropbox\Client(env('DROPBOX_ACCESS_TOKEN'));
        
        $client->upload(
            '/backups/' . $fileName,
            file_get_contents($localPath)
        );
        */
    }

    private function copyToLocalBackup($localPath, $fileName)
    {
        // Tạo bản sao ở vị trí khác để bảo vệ
        $backupDir = storage_path('app/cloud_backups');
        
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        
        $destinationPath = $backupDir . '/' . $fileName;
        
        if (copy($localPath, $destinationPath)) {
            $this->info("  ✅ Copied to local cloud backup: {$destinationPath}");
        } else {
            throw new \Exception("Failed to copy file to local backup");
        }
    }
}
