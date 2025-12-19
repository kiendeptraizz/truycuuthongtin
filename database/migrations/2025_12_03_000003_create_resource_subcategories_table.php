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
        // Tạo bảng danh mục con
        Schema::create('resource_subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('color')->nullable()->default('secondary');
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Unique name trong mỗi category
            $table->unique(['resource_category_id', 'name']);
        });

        // Thêm subcategory_id vào resource_accounts
        Schema::table('resource_accounts', function (Blueprint $table) {
            $table->foreignId('resource_subcategory_id')->nullable()->after('resource_category_id')
                ->constrained('resource_subcategories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resource_accounts', function (Blueprint $table) {
            $table->dropForeign(['resource_subcategory_id']);
            $table->dropColumn('resource_subcategory_id');
        });

        Schema::dropIfExists('resource_subcategories');
    }
};
