<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lưu structured fields vào pending_orders để Pay2S webhook auto-create
 * CustomerService khi paid (thay vì admin phải fill thủ công).
 *
 * Cũng thêm warranty_days cho customer_services.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pending_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('pending_orders', 'account_email')) {
                $table->string('account_email', 255)->nullable()->after('service_package_id');
            }
            if (!Schema::hasColumn('pending_orders', 'family_code')) {
                $table->string('family_code', 100)->nullable()->after('account_email');
            }
            if (!Schema::hasColumn('pending_orders', 'duration_days')) {
                $table->integer('duration_days')->nullable()->after('family_code');
            }
            if (!Schema::hasColumn('pending_orders', 'warranty_days')) {
                $table->integer('warranty_days')->nullable()->after('duration_days');
            }
            if (!Schema::hasColumn('pending_orders', 'profit_amount')) {
                $table->bigInteger('profit_amount')->nullable()->after('warranty_days');
            }
        });

        Schema::table('customer_services', function (Blueprint $table) {
            if (!Schema::hasColumn('customer_services', 'warranty_days')) {
                $table->integer('warranty_days')->nullable()->after('duration_days');
            }
            if (!Schema::hasColumn('customer_services', 'pending_order_id')) {
                $table->foreignId('pending_order_id')
                    ->nullable()
                    ->constrained('pending_orders')
                    ->nullOnDelete()
                    ->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customer_services', function (Blueprint $table) {
            if (Schema::hasColumn('customer_services', 'pending_order_id')) {
                $table->dropForeign(['pending_order_id']);
                $table->dropColumn('pending_order_id');
            }
            if (Schema::hasColumn('customer_services', 'warranty_days')) {
                $table->dropColumn('warranty_days');
            }
        });

        Schema::table('pending_orders', function (Blueprint $table) {
            foreach (['profit_amount', 'warranty_days', 'duration_days', 'family_code', 'account_email'] as $col) {
                if (Schema::hasColumn('pending_orders', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
