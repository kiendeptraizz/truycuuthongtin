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
        Schema::create('potential_supplier_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('potential_supplier_id')->constrained()->onDelete('cascade');
            $table->string('service_name')->comment('Tên dịch vụ');
            $table->decimal('estimated_price', 15, 2)->comment('Giá ước tính');
            $table->text('description')->nullable()->comment('Mô tả dịch vụ');
            $table->string('unit')->nullable()->comment('Đơn vị (cái, chiếc, tháng...)');
            $table->integer('warranty_days')->nullable()->comment('Số ngày bảo hành');
            $table->text('notes')->nullable()->comment('Ghi chú về dịch vụ');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('potential_supplier_services');
    }
};
