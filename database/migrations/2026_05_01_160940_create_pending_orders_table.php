<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pending_orders', function (Blueprint $t) {
            $t->id();
            $t->string('order_code', 32)->unique();   // DH-260501-001
            $t->decimal('amount', 15, 2)->default(0); // số tiền user gõ trên Telegram
            $t->string('note', 255)->nullable();      // ghi chú ngắn (tuỳ chọn)
            $t->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $t->enum('created_via', ['telegram', 'web'])->default('web');
            $t->boolean('is_paid')->default(false);
            $t->timestamp('paid_at')->nullable();
            $t->foreignId('customer_service_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->string('telegram_chat_id', 64)->nullable();
            $t->timestamps();

            $t->index(['status', 'created_at']);
            $t->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pending_orders');
    }
};
