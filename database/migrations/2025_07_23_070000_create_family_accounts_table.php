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
        Schema::create('family_accounts', function (Blueprint $table) {
            $table->id();
            
            // Basic family account info
            $table->string('family_name')->comment('Tên family plan');
            $table->string('family_code')->unique()->comment('Mã family plan duy nhất');
            
            // Service package relationship
            $table->foreignId('service_package_id')->constrained('service_packages')->onDelete('cascade');
            
            // Family owner info
            $table->string('owner_email')->comment('Email đăng nhập chính của family owner');
            $table->string('owner_password')->nullable()->comment('Mật khẩu đăng nhập family account');
            $table->string('owner_name')->nullable()->comment('Tên chủ gia đình');
            
            // Family plan details
            $table->integer('max_members')->default(6)->comment('Số thành viên tối đa cho phép');
            $table->integer('current_members')->default(0)->comment('Số thành viên hiện tại');
            
            // Dates and status
            $table->datetime('activated_at')->comment('Ngày kích hoạt family plan');
            $table->datetime('expires_at')->comment('Ngày hết hạn family plan');
            $table->enum('status', ['active', 'expired', 'suspended', 'cancelled'])->default('active');
            
            // Payment and billing
            $table->decimal('monthly_cost', 10, 2)->default(0)->comment('Chi phí hàng tháng');
            $table->decimal('total_paid', 10, 2)->default(0)->comment('Tổng số tiền đã thanh toán');
            $table->datetime('last_payment_date')->nullable()->comment('Ngày thanh toán gần nhất');
            $table->datetime('next_billing_date')->nullable()->comment('Ngày thanh toán tiếp theo');
            
            // Additional info
            $table->text('family_notes')->nullable()->comment('Ghi chú về family account');
            $table->text('internal_notes')->nullable()->comment('Ghi chú nội bộ');
            $table->json('family_settings')->nullable()->comment('Cài đặt family (JSON)');
            
            // Management info
            $table->foreignId('created_by')->nullable()->constrained('admins')->onDelete('set null');
            $table->foreignId('managed_by')->nullable()->constrained('admins')->onDelete('set null');
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index(['status', 'expires_at']);
            $table->index(['service_package_id', 'status']);
            $table->index('owner_email');
            $table->index('family_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_accounts');
    }
};
