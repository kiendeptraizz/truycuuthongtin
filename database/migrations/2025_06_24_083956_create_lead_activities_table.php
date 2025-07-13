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
        Schema::create('lead_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Người thực hiện
            $table->enum('type', ['call', 'email', 'meeting', 'note', 'quote', 'follow_up', 'converted', 'lost']);
            // call: gọi điện, email: gửi email, meeting: họp, note: ghi chú, quote: báo giá
            // follow_up: theo dõi, converted: chuyển đổi, lost: mất lead
            $table->text('notes'); // Ghi chú hoạt động (bắt buộc)
            $table->json('data')->nullable(); // Dữ liệu bổ sung dạng JSON
            $table->timestamps();

            // Indexes
            $table->index(['lead_id', 'created_at']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_activities');
    }
};
