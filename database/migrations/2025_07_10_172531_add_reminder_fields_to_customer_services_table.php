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
            $table->boolean('reminder_sent')->default(false)->comment('Đã gửi nhắc nhở');
            $table->timestamp('reminder_sent_at')->nullable()->comment('Thời gian gửi nhắc nhở gần nhất');
            $table->integer('reminder_count')->default(0)->comment('Số lần đã nhắc nhở');
            $table->text('reminder_notes')->nullable()->comment('Ghi chú về các lần nhắc nhở');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_services', function (Blueprint $table) {
            $table->dropColumn(['reminder_sent', 'reminder_sent_at', 'reminder_count', 'reminder_notes']);
        });
    }
};
