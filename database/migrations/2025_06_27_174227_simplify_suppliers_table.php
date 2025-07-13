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
        // Xóa ràng buộc khóa ngoại trước khi xóa bảng suppliers (nếu tồn tại)
        if (Schema::hasColumn('customer_services', 'supplier_id')) {
            Schema::table('customer_services', function (Blueprint $table) {
                $table->dropForeign(['supplier_id']);
                $table->dropColumn('supplier_id');
            });
        }

        // Xóa bảng suppliers cũ và tạo lại với cấu trúc đơn giản
        Schema::dropIfExists('suppliers');

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_code')->unique()->comment('Mã người cấp hàng (tự động sinh)');
            $table->string('supplier_name')->comment('Tên người cấp');
            $table->string('product_name')->comment('Tên sản phẩm');
            $table->decimal('price', 15, 2)->comment('Giá tiền');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');

        // Khôi phục bảng suppliers cũ nếu cần
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_code')->unique();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('website')->nullable();
            $table->string('tax_code')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('notes')->nullable();
            $table->decimal('credit_limit', 15, 2)->default(0);
            $table->decimal('current_debt', 15, 2)->default(0);
            $table->json('payment_terms')->nullable();
            $table->timestamps();
        });
    }
};