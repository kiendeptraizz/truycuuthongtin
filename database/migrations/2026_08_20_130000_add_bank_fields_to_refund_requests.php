<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('refund_requests', function (Blueprint $table) {
            $table->string('account_holder')->nullable()->after('bank_account'); // tên chủ tài khoản
            $table->string('bank_name', 100)->nullable()->after('account_holder'); // tên ngân hàng
        });
    }

    public function down(): void
    {
        Schema::table('refund_requests', function (Blueprint $table) {
            $table->dropColumn(['account_holder', 'bank_name']);
        });
    }
};
