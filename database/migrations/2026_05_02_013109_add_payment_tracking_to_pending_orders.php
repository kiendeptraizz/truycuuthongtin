<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pending_orders', function (Blueprint $table) {
            $table->timestamp('paid_at')->nullable()->after('status');
            $table->bigInteger('paid_amount')->nullable()->after('paid_at');
            $table->string('bank_transaction_id', 100)->nullable()->unique()->after('paid_amount');
            $table->text('bank_raw_payload')->nullable()->after('bank_transaction_id');

            $table->index('paid_at');
        });
    }

    public function down(): void
    {
        Schema::table('pending_orders', function (Blueprint $table) {
            $table->dropIndex(['paid_at']);
            $table->dropUnique(['bank_transaction_id']);
            $table->dropColumn(['paid_at', 'paid_amount', 'bank_transaction_id', 'bank_raw_payload']);
        });
    }
};
