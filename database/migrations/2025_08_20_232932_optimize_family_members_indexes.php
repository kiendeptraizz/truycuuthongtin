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
        Schema::table('family_members', function (Blueprint $table) {
            // Add composite index for optimized member counting
            $table->index(['status', 'family_account_id'], 'idx_status_family_account');

            // Add index for member role queries
            $table->index(['member_role', 'status'], 'idx_role_status');

            // Add index for date-based queries
            $table->index(['created_at', 'status'], 'idx_created_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('family_members', function (Blueprint $table) {
            $table->dropIndex('idx_status_family_account');
            $table->dropIndex('idx_role_status');
            $table->dropIndex('idx_created_status');
        });
    }
};
