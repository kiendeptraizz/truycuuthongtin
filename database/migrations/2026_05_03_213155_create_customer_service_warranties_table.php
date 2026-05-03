<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lịch sử bảo hành của 1 CustomerService — mỗi record = 1 lần admin
 * thực hiện bảo hành (đổi TK mới, gia hạn, hoặc chỉ ghi chú).
 *
 * Khi tạo record:
 *   - replacement_email/password có giá trị → tự update CS.login_email/password
 *   - extended_days > 0 → tự cộng vào CS.expires_at
 *   - note: bắt buộc
 *
 * Audit log của CS tự ghi lại các thay đổi qua observer.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_service_warranties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_service_id')->constrained()->cascadeOnDelete();
            $table->string('replacement_email', 255)->nullable()
                ->comment('Email TK mới khi đổi tài khoản bảo hành');
            $table->string('replacement_password', 255)->nullable()
                ->comment('Password TK mới khi đổi tài khoản bảo hành');
            $table->integer('extended_days')->nullable()
                ->comment('Số ngày gia hạn thêm — null = không gia hạn');
            $table->text('note')
                ->comment('Ghi chú bảo hành (lý do, mô tả lỗi, ...)');
            $table->string('actor_type', 32)->nullable()->comment('user | bot');
            $table->unsignedBigInteger('actor_id')->nullable()->comment('user_id nếu actor=user');
            $table->string('actor_label', 100)->nullable()->comment('vd: admin@truycuu, telegram:7351...');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['customer_service_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_service_warranties');
    }
};
