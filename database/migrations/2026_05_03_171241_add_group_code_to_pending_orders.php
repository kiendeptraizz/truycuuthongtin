<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Group code cho lô đơn mua nhiều dịch vụ cùng lúc.
 *
 * Khách CK 1 lần → Pay2S match GR-yymmdd-XXX → mark TẤT CẢ PendingOrder
 * có cùng group_code = paid → tạo N CustomerService riêng biệt (mỗi service
 * vẫn là 1 row riêng, chỉ chia sẻ group_code metadata).
 *
 * Format: GR-yymmdd-XXX (XXX = sequence trong ngày, 3 chữ số).
 * NULL = đơn lẻ (default — 1 đơn 1 dịch vụ).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pending_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('pending_orders', 'group_code')) {
                $table->string('group_code', 20)->nullable()->after('order_code');
                $table->index('group_code', 'pending_orders_group_code_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pending_orders', function (Blueprint $table) {
            if (Schema::hasColumn('pending_orders', 'group_code')) {
                $table->dropIndex('pending_orders_group_code_index');
                $table->dropColumn('group_code');
            }
        });
    }
};
