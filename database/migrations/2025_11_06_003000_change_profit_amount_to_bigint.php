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
            // Thay đổi profit_amount từ decimal(15,2) thành unsignedBigInteger
            // Để lưu số tiền nguyên (VD: 120000 thay vì 120000.00)
            $table->unsignedBigInteger('profit_amount')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profits', function (Blueprint $table) {
            // Rollback về decimal(15,2)
            $table->decimal('profit_amount', 15, 2)->default(0)->change();
        });
    }
};

