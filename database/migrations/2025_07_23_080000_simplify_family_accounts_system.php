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
        // Remove unnecessary columns from family_accounts table
        Schema::table('family_accounts', function (Blueprint $table) {
            $table->dropColumn([
                'owner_password',
                'total_paid',
                'monthly_cost',
                'last_payment_date',
                'next_billing_date'
            ]);
        });

        // Remove unnecessary columns from family_members table
        Schema::table('family_members', function (Blueprint $table) {
            $table->dropColumn([
                'member_name',
                'joined_at'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back the columns if needed to rollback
        Schema::table('family_accounts', function (Blueprint $table) {
            $table->string('owner_password')->nullable()->comment('Mật khẩu đăng nhập family account');
            $table->decimal('total_paid', 10, 2)->default(0)->comment('Tổng số tiền đã thanh toán');
            $table->decimal('monthly_cost', 10, 2)->default(0)->comment('Chi phí hàng tháng');
            $table->datetime('last_payment_date')->nullable()->comment('Ngày thanh toán gần nhất');
            $table->datetime('next_billing_date')->nullable()->comment('Ngày thanh toán tiếp theo');
        });

        Schema::table('family_members', function (Blueprint $table) {
            $table->string('member_name')->comment('Tên thành viên trong family');
            $table->datetime('joined_at')->comment('Ngày tham gia family');
        });
    }
};
