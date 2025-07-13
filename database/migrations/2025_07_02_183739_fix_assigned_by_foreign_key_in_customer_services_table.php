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
            // Drop existing foreign key constraint
            $table->dropForeign(['assigned_by']);

            // Add new foreign key constraint to admins table
            $table->foreign('assigned_by')->references('id')->on('admins')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_services', function (Blueprint $table) {
            // Drop the admins foreign key constraint
            $table->dropForeign(['assigned_by']);

            // Restore the users foreign key constraint
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('set null');
        });
    }
};
