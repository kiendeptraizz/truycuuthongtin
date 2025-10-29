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
        Schema::create('message_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('message_campaigns')->onDelete('cascade');
            $table->foreignId('zalo_account_id')->constrained('zalo_accounts')->onDelete('cascade');
            $table->foreignId('group_member_id')->constrained('group_members')->onDelete('cascade');
            $table->text('message_content')->comment('Nội dung tin nhắn đã gửi');
            $table->enum('status', ['pending', 'sent', 'delivered', 'failed', 'blocked'])->default('pending');
            $table->text('error_message')->nullable()->comment('Thông báo lỗi (nếu có)');
            $table->timestamp('sent_at')->nullable()->comment('Thời gian gửi');
            $table->timestamp('delivered_at')->nullable()->comment('Thời gian gửi thành công');
            $table->timestamps();
            
            $table->index(['campaign_id', 'sent_at']);
            $table->index(['zalo_account_id', 'sent_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_logs');
    }
};

