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
        Schema::create('shared_account_logout_logs', function (Blueprint $table) {
            $table->id();
            $table->string('login_email')->index()->comment('Email tài khoản dùng chung');
            $table->string('service_package_name')->comment('Tên gói dịch vụ');
            $table->timestamp('logout_at')->comment('Thời điểm logout');
            $table->string('performed_by')->comment('Người thực hiện logout');
            $table->string('reason')->nullable()->comment('Lý do logout');
            $table->text('notes')->nullable()->comment('Ghi chú thêm');
            $table->json('affected_customers')->nullable()->comment('Danh sách khách hàng bị ảnh hưởng');
            $table->integer('affected_count')->default(0)->comment('Số lượng khách hàng bị ảnh hưởng');
            $table->string('ip_address')->nullable()->comment('Địa chỉ IP thực hiện');
            $table->string('user_agent')->nullable()->comment('User agent');
            $table->timestamps();

            // Indexes for better performance
            $table->index(['login_email', 'logout_at']);
            $table->index('logout_at');
            $table->index('performed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shared_account_logout_logs');
    }
};
