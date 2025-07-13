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
        Schema::create('collaborator_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collaborator_id')->constrained()->onDelete('cascade');
            $table->string('service_name')->comment('Tên dịch vụ cung cấp');
            $table->decimal('price', 15, 2)->comment('Giá dịch vụ');
            $table->integer('quantity')->default(1)->comment('Số lượng');
            $table->integer('warranty_period')->default(0)->comment('Thời gian bảo hành (ngày)');
            $table->enum('status', ['active', 'inactive', 'expired'])->default('active')->comment('Trạng thái');
            $table->text('description')->nullable()->comment('Mô tả dịch vụ');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collaborator_services');
    }
};
