<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds indexes to improve query performance on frequently queried columns
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->index('email', 'idx_customers_email');
            $table->index('phone', 'idx_customers_phone');
        });

        Schema::table('service_packages', function (Blueprint $table) {
            $table->index('category_id', 'idx_sp_category_id');
            $table->index('is_active', 'idx_sp_is_active');
            $table->index('account_type', 'idx_sp_account_type');
        });

        Schema::table('message_campaigns', function (Blueprint $table) {
            $table->index('status', 'idx_mc_status');
            $table->index('target_group_id', 'idx_mc_target_group_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('idx_customers_email');
            $table->dropIndex('idx_customers_phone');
        });

        Schema::table('service_packages', function (Blueprint $table) {
            $table->dropIndex('idx_sp_category_id');
            $table->dropIndex('idx_sp_is_active');
            $table->dropIndex('idx_sp_account_type');
        });

        Schema::table('message_campaigns', function (Blueprint $table) {
            $table->dropIndex('idx_mc_status');
            $table->dropIndex('idx_mc_target_group_id');
        });
    }
};
