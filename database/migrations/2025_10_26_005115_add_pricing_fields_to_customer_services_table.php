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
        // Thêm các cột mới vào customer_services
        Schema::table('customer_services', function (Blueprint $table) {
            $table->integer('duration_days')->nullable()->after('status');
            $table->decimal('cost_price', 10, 2)->nullable()->after('duration_days');
            $table->decimal('price', 10, 2)->nullable()->after('cost_price');
        });

        // Làm các cột trong service_packages nullable
        Schema::table('service_packages', function (Blueprint $table) {
            $table->integer('default_duration_days')->nullable()->change();
            $table->decimal('price', 10, 2)->nullable()->change();
            $table->decimal('cost_price', 10, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_services', function (Blueprint $table) {
            $table->dropColumn(['duration_days', 'cost_price', 'price']);
        });

        Schema::table('service_packages', function (Blueprint $table) {
            $table->integer('default_duration_days')->nullable(false)->change();
            $table->decimal('price', 10, 2)->nullable(false)->change();
            // cost_price was already nullable, so keep it nullable
        });
    }
};
