<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pending_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('pending_orders', 'service_package_id')) {
                $table->foreignId('service_package_id')
                    ->nullable()
                    ->after('customer_id')
                    ->constrained('service_packages')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('pending_orders', function (Blueprint $table) {
            if (Schema::hasColumn('pending_orders', 'service_package_id')) {
                $table->dropForeign(['service_package_id']);
                $table->dropColumn('service_package_id');
            }
        });
    }
};
