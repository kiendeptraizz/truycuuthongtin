<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('refund_requests', function (Blueprint $table) {
            $table->date('error_date')->nullable()->after('bank_name');                  // ngày tài khoản lỗi (mốc tính hoàn)
            $table->unsignedInteger('computed_refund')->nullable()->after('error_date');  // số tiền hoàn đã tính (VND)
        });
    }

    public function down(): void
    {
        Schema::table('refund_requests', function (Blueprint $table) {
            $table->dropColumn(['error_date', 'computed_refund']);
        });
    }
};
