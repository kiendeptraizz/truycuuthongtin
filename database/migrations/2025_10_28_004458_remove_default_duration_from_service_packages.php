<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Xóa duration_days mặc định từ service_packages
     * KHÔNG ảnh hưởng đến customer_services (dịch vụ của khách hàng)
     */
    public function up(): void
    {
        // Backup dữ liệu cũ vào file log để có thể rollback
        $packages = DB::table('service_packages')
            ->select('id', 'name', 'default_duration_days', 'custom_duration')
            ->whereNotNull('default_duration_days')
            ->get();

        if ($packages->count() > 0) {
            $backupFile = storage_path('logs/service_packages_duration_backup_' . date('Y_m_d_His') . '.json');
            file_put_contents($backupFile, json_encode($packages, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            echo "✅ Backed up " . $packages->count() . " packages to: " . $backupFile . "\n";
        }

        // Set default_duration_days = NULL và custom_duration = NULL
        // ONLY cho service_packages - KHÔNG chạm vào customer_services
        DB::table('service_packages')->update([
            'default_duration_days' => null,
            'custom_duration' => null
        ]);

        echo "✅ Removed default_duration_days and custom_duration from service_packages\n";
        echo "⚠️  Customer services (dịch vụ của khách hàng) KHÔNG bị ảnh hưởng\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tìm file backup gần nhất
        $backupFiles = glob(storage_path('logs/service_packages_duration_backup_*.json'));

        if (empty($backupFiles)) {
            echo "⚠️  No backup file found. Cannot restore default_duration_days\n";
            return;
        }

        // Lấy file backup mới nhất
        rsort($backupFiles);
        $latestBackup = $backupFiles[0];

        $packages = json_decode(file_get_contents($latestBackup), true);

        foreach ($packages as $package) {
            DB::table('service_packages')
                ->where('id', $package['id'])
                ->update([
                    'default_duration_days' => $package['default_duration_days'] ?? null,
                    'custom_duration' => $package['custom_duration'] ?? null
                ]);
        }

        echo "✅ Restored default_duration_days and custom_duration from: " . $latestBackup . "\n";
    }
};
