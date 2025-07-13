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
        Schema::table('customer_services', function (Blueprint $table) {
            // Thêm các trường cho tài khoản dùng chung
            $table->string('two_factor_code', 100)->nullable()->comment('Mã 2FA của tài khoản dùng chung');
            $table->text('recovery_codes')->nullable()->comment('Danh sách mã khôi phục (JSON format)');
            $table->text('shared_account_notes')->nullable()->comment('Ghi chú riêng cho tài khoản dùng chung');
            $table->text('customer_instructions')->nullable()->comment('Hướng dẫn/ghi chú gửi cho khách hàng');
            $table->datetime('password_expires_at')->nullable()->comment('Ngày hết hạn mật khẩu');
            $table->datetime('two_factor_updated_at')->nullable()->comment('Ngày cập nhật 2FA gần nhất');
            $table->boolean('is_password_shared')->default(false)->comment('Có phải mật khẩu được chia sẻ không');
            $table->json('shared_with_customers')->nullable()->comment('Danh sách khách hàng đã chia sẻ thông tin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_services', function (Blueprint $table) {
            // Xóa các trường đã thêm (cho phép rollback an toàn)
            $table->dropColumn([
                'two_factor_code',
                'recovery_codes',
                'shared_account_notes',
                'customer_instructions',
                'password_expires_at',
                'two_factor_updated_at',
                'is_password_shared',
                'shared_with_customers'
            ]);
        });
    }
};
