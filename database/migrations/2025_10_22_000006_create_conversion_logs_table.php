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
        Schema::create('conversion_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('message_campaigns')->onDelete('cascade');
            $table->foreignId('group_member_id')->constrained('group_members')->onDelete('cascade');
            $table->foreignId('message_log_id')->nullable()->constrained('message_logs')->onDelete('set null');
            $table->foreignId('own_group_id')->constrained('target_groups')->onDelete('cascade')->comment('Nhóm của mình mà user đã join');
            $table->timestamp('joined_at')->comment('Thời gian join nhóm');
            $table->integer('days_to_convert')->nullable()->comment('Số ngày từ lúc gửi tin đến khi join');
            $table->text('notes')->nullable()->comment('Ghi chú');
            $table->timestamps();
            
            $table->index(['campaign_id', 'joined_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversion_logs');
    }
};

