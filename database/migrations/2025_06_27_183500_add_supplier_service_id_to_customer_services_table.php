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
        Schema::table('customer_services', function (Blueprint $table) {
            $table->foreignId('supplier_id')->nullable()->after('assigned_by')->constrained('suppliers')->onDelete('set null');
            $table->foreignId('supplier_service_id')->nullable()->after('supplier_id')->constrained('supplier_products')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_services', function (Blueprint $table) {
            $table->dropForeign(['supplier_service_id']);
            $table->dropColumn('supplier_service_id');
            $table->dropForeign(['supplier_id']);
            $table->dropColumn('supplier_id');
        });
    }
};
