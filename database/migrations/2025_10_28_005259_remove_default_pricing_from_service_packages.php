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
     * Xóa giá nhập và giá bán mặc định từ service_packages
     * KHÔNG ảnh hưởng đến customer_services (dịch vụ của khách hàng)
     */
    public function up(): void
    {
        // Backup dữ liệu cũ vào file log để có thể rollback
        $packages = DB::table('service_packages')
            ->select('id', 'name', 'price', 'cost_price')
            ->where(function ($query) {
                $query->whereNotNull('price')
                    ->orWhereNotNull('cost_price');
            })
            ->get();

        if ($packages->count() > 0) {
            $backupFile = storage_path('logs/service_packages_pricing_backup_' . date('Y_m_d_His') . '.json');
            file_put_contents($backupFile, json_encode($packages, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            echo "✅ Backed up " . $packages->count() . " packages pricing to: " . $backupFile . "\n";
        }

        // Set price và cost_price = NULL ONLY cho service_packages
        // KHÔNG chạm vào customer_services
        DB::table('service_packages')->update([
            'price' => null,
            'cost_price' => null
        ]);

        echo "✅ Removed default price and cost_price from service_packages\n";
        echo "⚠️  Customer services (dịch vụ của khách hàng) KHÔNG bị ảnh hưởng\n";
        echo "💰 Lợi nhuận sẽ được tính từ phần điền thủ công khi gán dịch vụ\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tìm file backup gần nhất
        $backupFiles = glob(storage_path('logs/service_packages_pricing_backup_*.json'));

        if (empty($backupFiles)) {
            echo "⚠️  No backup file found. Cannot restore pricing\n";
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
                    'price' => $package['price'] ?? null,
                    'cost_price' => $package['cost_price'] ?? null
                ]);
        }

        echo "✅ Restored price and cost_price from: " . $latestBackup . "\n";
    }
};
