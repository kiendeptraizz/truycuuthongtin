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
        Schema::create('family_members', function (Blueprint $table) {
            $table->id();
            
            // Relationships
            $table->foreignId('family_account_id')->constrained('family_accounts')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            
            // Member info
            $table->string('member_name')->comment('Tên thành viên trong family');
            $table->string('member_email')->nullable()->comment('Email cá nhân của thành viên');
            $table->string('member_role')->default('member')->comment('Vai trò: owner, admin, member');
            
            // Status and permissions
            $table->enum('status', ['active', 'inactive', 'removed', 'suspended'])->default('active');
            $table->json('permissions')->nullable()->comment('Quyền hạn của thành viên (JSON)');
            
            // Dates
            $table->datetime('joined_at')->comment('Ngày tham gia family');
            $table->datetime('last_active_at')->nullable()->comment('Lần hoạt động cuối');
            $table->datetime('removed_at')->nullable()->comment('Ngày bị xóa khỏi family');
            
            // Usage tracking
            $table->integer('usage_count')->default(0)->comment('Số lần sử dụng dịch vụ');
            $table->datetime('first_usage_at')->nullable()->comment('Lần sử dụng đầu tiên');
            $table->datetime('last_usage_at')->nullable()->comment('Lần sử dụng cuối');
            
            // Notes
            $table->text('member_notes')->nullable()->comment('Ghi chú về thành viên');
            $table->text('internal_notes')->nullable()->comment('Ghi chú nội bộ');
            
            // Management
            $table->foreignId('added_by')->nullable()->constrained('admins')->onDelete('set null');
            $table->foreignId('removed_by')->nullable()->constrained('admins')->onDelete('set null');
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index(['family_account_id', 'status']);
            $table->index(['customer_id', 'status']);
            $table->index('member_email');
            $table->index('joined_at');
            
            // Unique constraint: một customer chỉ có thể là thành viên của một family account tại một thời điểm
            $table->unique(['family_account_id', 'customer_id'], 'unique_family_customer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_members');
    }
};
