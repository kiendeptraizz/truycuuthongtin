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
        Schema::create('supplier_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->string('product_name')->comment('Tên sản phẩm');
            $table->decimal('price', 15, 2)->comment('Giá tiền');
            $table->text('description')->nullable()->comment('Mô tả sản phẩm');
            $table->string('unit')->nullable()->comment('Đơn vị (cái, chiếc, kg...)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_products');
    }
};
