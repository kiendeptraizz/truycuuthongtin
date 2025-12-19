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
        // Xóa constraint cũ có giới hạn max_members <= 50
        try {
            DB::statement('ALTER TABLE family_accounts DROP CHECK chk_family_members');
        } catch (\Exception $e) {
            // Constraint might not exist, continue
        }

        // Thêm constraint mới - CHỈ kiểm tra max_members > 0 và current_members hợp lệ, KHÔNG giới hạn max
        DB::statement('ALTER TABLE family_accounts ADD CONSTRAINT chk_family_members CHECK (max_members > 0 AND current_members >= 0 AND current_members <= max_members)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Khôi phục lại constraint có giới hạn 50
        try {
            DB::statement('ALTER TABLE family_accounts DROP CHECK chk_family_members');
        } catch (\Exception $e) {
            // Constraint might not exist, continue
        }

        DB::statement('ALTER TABLE family_accounts ADD CONSTRAINT chk_family_members CHECK (max_members > 0 AND max_members <= 50 AND current_members >= 0 AND current_members <= max_members)');
    }
};
