<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pending_orders', function (Blueprint $table) {
            // % giảm giá áp cho LÔ đơn (các đơn cùng group_code mang cùng giá trị).
            // 0 = không giảm (đơn lẻ luôn 0). Tổng khách phải trả = Σ amount * (1 - %/100).
            $table->decimal('discount_percent', 5, 2)->default(0)->after('amount');
        });
    }

    public function down(): void
    {
        Schema::table('pending_orders', function (Blueprint $table) {
            $table->dropColumn('discount_percent');
        });
    }
};
