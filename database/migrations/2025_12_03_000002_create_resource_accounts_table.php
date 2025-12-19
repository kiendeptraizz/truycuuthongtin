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
        Schema::create('resource_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_category_id')->constrained()->onDelete('cascade');
            $table->string('name')->nullable(); // Tên tài khoản (tùy chọn)
            $table->string('email')->nullable(); // Email đăng nhập
            $table->string('username')->nullable(); // Username (nếu có)
            $table->text('password')->nullable(); // Mật khẩu (plain text)
            $table->text('two_factor_secret')->nullable(); // 2FA secret/backup codes
            $table->text('recovery_codes')->nullable(); // Recovery codes/backup
            $table->date('start_date')->nullable(); // Ngày bắt đầu
            $table->date('end_date')->nullable(); // Ngày kết thúc
            $table->boolean('is_available')->default(true); // Còn khả dụng không
            $table->enum('status', ['active', 'expired', 'sold', 'reserved', 'suspended'])->default('active');
            $table->text('notes')->nullable(); // Ghi chú thêm
            $table->json('extra_fields')->nullable(); // Các trường bổ sung (JSON)
            $table->timestamps();

            // Index để tìm kiếm nhanh
            $table->index(['resource_category_id', 'is_available']);
            $table->index(['status']);
            $table->index(['end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_accounts');
    }
};
