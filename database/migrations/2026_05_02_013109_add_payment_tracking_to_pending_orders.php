<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pending_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('pending_orders', 'paid_amount')) {
                $table->bigInteger('paid_amount')->nullable()->after('paid_at');
            }
            if (!Schema::hasColumn('pending_orders', 'bank_transaction_id')) {
                $table->string('bank_transaction_id', 100)->nullable()->unique()->after('paid_amount');
            }
            if (!Schema::hasColumn('pending_orders', 'bank_raw_payload')) {
                $table->text('bank_raw_payload')->nullable()->after('bank_transaction_id');
            }
            if (!Schema::hasIndex('pending_orders', 'pending_orders_paid_at_index')) {
                $table->index('paid_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pending_orders', function (Blueprint $table) {
            if (Schema::hasColumn('pending_orders', 'bank_transaction_id')) {
                $table->dropUnique(['bank_transaction_id']);
            }
            $table->dropColumn(['paid_amount', 'bank_transaction_id', 'bank_raw_payload']);
        });
    }
};
