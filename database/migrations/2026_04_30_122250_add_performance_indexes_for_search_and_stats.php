<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Index nào hữu ích cho:
     *  - customers: search by name/email/phone (controller dùng `like` 3 cột này)
     *  - customer_services: filter/group by created_at (báo cáo lợi nhuận `whereBetween` rất nhiều)
     *
     * Lưu ý: profits đã có composite index (customer_service_id, created_at), bỏ qua.
     *        customer_services đã có nhiều idx_cs_*, chỉ thêm idx_cs_created_at còn thiếu.
     */
    public function up(): void
    {
        // customers: index cho search
        if (Schema::hasTable('customers')) {
            $existing = $this->existingIndexes('customers');

            Schema::table('customers', function (Blueprint $t) use ($existing) {
                if (!in_array('idx_customers_name', $existing, true)) {
                    $t->index('name', 'idx_customers_name');
                }
                if (!in_array('idx_customers_email', $existing, true)) {
                    $t->index('email', 'idx_customers_email');
                }
                if (!in_array('idx_customers_phone', $existing, true)) {
                    $t->index('phone', 'idx_customers_phone');
                }
            });
        }

        // customer_services: index cho thống kê theo thời gian tạo
        if (Schema::hasTable('customer_services')) {
            $existing = $this->existingIndexes('customer_services');

            Schema::table('customer_services', function (Blueprint $t) use ($existing) {
                if (!in_array('idx_cs_created_at', $existing, true)) {
                    $t->index('created_at', 'idx_cs_created_at');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('customers')) {
            $existing = $this->existingIndexes('customers');
            Schema::table('customers', function (Blueprint $t) use ($existing) {
                foreach (['idx_customers_name', 'idx_customers_email', 'idx_customers_phone'] as $name) {
                    if (in_array($name, $existing, true)) {
                        $t->dropIndex($name);
                    }
                }
            });
        }

        if (Schema::hasTable('customer_services')) {
            $existing = $this->existingIndexes('customer_services');
            Schema::table('customer_services', function (Blueprint $t) use ($existing) {
                if (in_array('idx_cs_created_at', $existing, true)) {
                    $t->dropIndex('idx_cs_created_at');
                }
            });
        }
    }

    private function existingIndexes(string $table): array
    {
        return collect(DB::select("SHOW INDEX FROM `{$table}`"))
            ->pluck('Key_name')
            ->unique()
            ->values()
            ->all();
    }
};
