<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update constraint to allow max_members up to 50
        try {
            DB::statement('ALTER TABLE family_accounts DROP CHECK chk_family_members');
        } catch (\Exception $e) {
            // Constraint might not exist, continue
        }

        DB::statement('ALTER TABLE family_accounts ADD CONSTRAINT chk_family_members CHECK (max_members > 0 AND max_members <= 50 AND current_members >= 0 AND current_members <= max_members)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original constraint (max 20)
        try {
            DB::statement('ALTER TABLE family_accounts DROP CHECK chk_family_members');
        } catch (\Exception $e) {
            // Constraint might not exist, continue
        }

        DB::statement('ALTER TABLE family_accounts ADD CONSTRAINT chk_family_members CHECK (max_members > 0 AND max_members <= 20 AND current_members >= 0 AND current_members <= max_members)');
    }
};
