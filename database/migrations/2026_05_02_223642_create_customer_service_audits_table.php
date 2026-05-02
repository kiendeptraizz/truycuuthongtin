<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Audit log mọi thay đổi của customer_services — trace bug khi data sai.
 * Mỗi record = 1 sự kiện (created/updated/deleted) với JSON old + new values.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_service_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_service_id')->constrained()->cascadeOnDelete();
            $table->enum('event', ['created', 'updated', 'deleted', 'restored']);
            $table->json('old_values')->nullable()->comment('Giá trị trước khi update — null nếu created');
            $table->json('new_values')->nullable()->comment('Giá trị sau khi update — null nếu deleted');
            $table->json('changed_fields')->nullable()->comment('Tên các field thay đổi (cho updated event)');
            $table->string('actor_type', 32)->nullable()->comment('user / bot / webhook / cli');
            $table->unsignedBigInteger('actor_id')->nullable()->comment('user_id nếu actor=user');
            $table->string('actor_label', 100)->nullable()->comment('Label dễ đọc (vd "admin@truycuu", "Pay2S webhook", "Bot Telegram")');
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->text('note')->nullable()->comment('Ghi chú tự do (vd "manual mark paid")');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['customer_service_id', 'created_at']);
            $table->index(['event', 'created_at']);
            $table->index('actor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_service_audits');
    }
};
