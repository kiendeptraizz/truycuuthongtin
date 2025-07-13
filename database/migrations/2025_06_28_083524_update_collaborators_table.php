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
        Schema::table('collaborators', function (Blueprint $table) {
            // Check if columns don't exist before adding
            if (!Schema::hasColumn('collaborators', 'collaborator_code')) {
                $table->string('collaborator_code')->unique()->comment('Mã cộng tác viên tự sinh');
            }
            if (!Schema::hasColumn('collaborators', 'name')) {
                $table->string('name')->comment('Tên cộng tác viên');
            }
            if (!Schema::hasColumn('collaborators', 'email')) {
                $table->string('email')->nullable()->comment('Email liên hệ');
            }
            if (!Schema::hasColumn('collaborators', 'phone')) {
                $table->string('phone')->nullable()->comment('Số điện thoại');
            }
            if (!Schema::hasColumn('collaborators', 'address')) {
                $table->text('address')->nullable()->comment('Địa chỉ');
            }
            if (!Schema::hasColumn('collaborators', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active')->comment('Trạng thái');
            }
            if (!Schema::hasColumn('collaborators', 'notes')) {
                $table->text('notes')->nullable()->comment('Ghi chú');
            }
            if (!Schema::hasColumn('collaborators', 'commission_rate')) {
                $table->decimal('commission_rate', 5, 2)->default(0)->comment('Tỷ lệ hoa hồng (%)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('collaborators', function (Blueprint $table) {
            $columns = ['collaborator_code', 'name', 'email', 'phone', 'address', 'status', 'notes', 'commission_rate'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('collaborators', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
