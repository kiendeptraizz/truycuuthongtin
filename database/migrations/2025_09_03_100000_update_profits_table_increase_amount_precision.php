<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('profits', function (Blueprint $table) {
            // Mở rộng profit_amount từ decimal(10,2) thành decimal(15,2)
            // Cho phép nhập số lên đến 9,999,999,999,999.99 (gần 10 nghìn tỷ)
            $table->decimal('profit_amount', 15, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profits', function (Blueprint $table) {
            // Rollback về decimal(10,2)
            $table->decimal('profit_amount', 10, 2)->default(0)->change();
        });
    }
};
