<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if member_name column exists and is nullable
        if (Schema::hasColumn('family_members', 'member_name')) {
            // Make member_name nullable and populate with customer names
            Schema::table('family_members', function (Blueprint $table) {
                $table->string('member_name')->nullable()->change();
            });
            
            // Populate member_name from customer names where it's empty
            DB::statement("
                UPDATE family_members fm 
                JOIN customers c ON fm.customer_id = c.id 
                SET fm.member_name = c.name 
                WHERE fm.member_name IS NULL OR fm.member_name = ''
            ");
        } else {
            // Add member_name column if it doesn't exist
            Schema::table('family_members', function (Blueprint $table) {
                $table->string('member_name')->nullable()->after('customer_id');
            });
            
            // Populate with customer names
            DB::statement("
                UPDATE family_members fm 
                JOIN customers c ON fm.customer_id = c.id 
                SET fm.member_name = c.name
            ");
        }
        
        // Add index for better performance
        Schema::table('family_members', function (Blueprint $table) {
            if (!Schema::hasIndex('family_members', 'family_members_member_name_index')) {
                $table->index('member_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't drop the column as it might contain important data
        // Just make it nullable if needed
        Schema::table('family_members', function (Blueprint $table) {
            $table->dropIndex(['member_name']);
        });
    }
};
