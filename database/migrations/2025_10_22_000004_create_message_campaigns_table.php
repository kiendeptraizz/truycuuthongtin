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
        Schema::create('message_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('campaign_name')->comment('Tên chiến dịch');
            $table->foreignId('target_group_id')->constrained('target_groups')->onDelete('cascade');
            $table->foreignId('own_group_id')->nullable()->constrained('target_groups')->onDelete('set null')->comment('Nhóm của mình để kéo về');
            $table->text('message_template')->comment('Mẫu tin nhắn');
            $table->date('start_date')->comment('Ngày bắt đầu');
            $table->date('end_date')->nullable()->comment('Ngày kết thúc');
            $table->integer('daily_target')->default(50)->comment('Mục tiêu gửi tin/ngày');
            $table->enum('status', ['draft', 'active', 'paused', 'completed', 'cancelled'])->default('draft');
            $table->integer('total_sent')->default(0)->comment('Tổng số tin đã gửi');
            $table->integer('total_delivered')->default(0)->comment('Tổng số tin đã gửi thành công');
            $table->integer('total_failed')->default(0)->comment('Tổng số tin thất bại');
            $table->integer('total_converted')->default(0)->comment('Tổng số người đã join nhóm');
            $table->decimal('conversion_rate', 5, 2)->default(0)->comment('Tỷ lệ chuyển đổi %');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_campaigns');
    }
};

