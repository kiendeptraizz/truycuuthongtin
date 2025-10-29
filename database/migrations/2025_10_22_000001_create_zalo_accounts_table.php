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
        Schema::create('zalo_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_name')->comment('Tên tài khoản Zalo');
            $table->string('email_or_phone')->unique()->comment('Email hoặc SĐT');
            $table->string('password')->nullable()->comment('Mật khẩu (encrypted)');
            $table->text('access_token')->nullable()->comment('Access token');
            $table->text('refresh_token')->nullable()->comment('Refresh token');
            $table->timestamp('token_expires_at')->nullable()->comment('Token expiry time');
            $table->enum('status', ['active', 'inactive', 'blocked', 'error'])->default('active');
            $table->integer('daily_message_limit')->default(100)->comment('Giới hạn tin nhắn/ngày');
            $table->integer('messages_sent_today')->default(0)->comment('Số tin đã gửi hôm nay');
            $table->date('last_message_date')->nullable()->comment('Ngày gửi tin cuối');
            $table->text('notes')->nullable()->comment('Ghi chú');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zalo_accounts');
    }
};

