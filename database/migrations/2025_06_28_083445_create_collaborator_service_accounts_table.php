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
        Schema::create('collaborator_service_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collaborator_service_id')->constrained()->onDelete('cascade');
            $table->text('account_info')->comment('Thông tin tài khoản (username, password, etc.)');
            $table->date('provided_date')->comment('Ngày cung cấp');
            $table->date('expiry_date')->nullable()->comment('Ngày hết hạn');
            $table->enum('status', ['active', 'expired', 'disabled'])->default('active')->comment('Trạng thái tài khoản');
            $table->text('notes')->nullable()->comment('Ghi chú');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collaborator_service_accounts');
    }
};
