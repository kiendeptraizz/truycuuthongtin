<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refund_requests', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique();            // RF-YYMMDD-XXXX — mã theo dõi yêu cầu hoàn tiền
            $table->string('customer_name');                 // Tên khách hàng
            $table->string('order_code', 64)->index();       // Mã đơn hàng cần hoàn tiền
            $table->string('bank_account', 64);              // Số tài khoản nhận hoàn
            $table->string('qr_image_path')->nullable();     // Ảnh QR nhận tiền (public disk)
            $table->string('status', 16)->default('pending')->index(); // pending | done | rejected
            $table->text('admin_note')->nullable();          // ghi chú của admin khi xử lý
            $table->string('ip_address', 45)->nullable();    // audit / chống spam
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refund_requests');
    }
};
