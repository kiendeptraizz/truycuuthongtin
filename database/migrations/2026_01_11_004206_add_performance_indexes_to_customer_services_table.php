<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds indexes to improve query performance on customer_services table
     */
    public function up(): void
    {
        Schema::table('customer_services', function (Blueprint $table) {
            // Index for filtering by expiry date (used in expiring soon, expired filters)
            $table->index('expires_at', 'idx_cs_expires_at');
            
            // Index for filtering by activation date
            $table->index('activated_at', 'idx_cs_activated_at');
            
            // Index for status filtering
            $table->index('status', 'idx_cs_status');
            
            // Composite index for common filter combinations
            $table->index(['expires_at', 'status'], 'idx_cs_expires_status');
            
            // Index for reminder queries
            $table->index('reminder_sent', 'idx_cs_reminder_sent');
            
            // Index for login_email searches
            $table->index('login_email', 'idx_cs_login_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_services', function (Blueprint $table) {
            $table->dropIndex('idx_cs_expires_at');
            $table->dropIndex('idx_cs_activated_at');
            $table->dropIndex('idx_cs_status');
            $table->dropIndex('idx_cs_expires_status');
            $table->dropIndex('idx_cs_reminder_sent');
            $table->dropIndex('idx_cs_login_email');
        });
    }
};
