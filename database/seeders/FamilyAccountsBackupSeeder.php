<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FamilyAccountsBackupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- Start of Generated Seeder Content ---

        // Disable foreign key checks to avoid errors during seeding
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // The tables are created fresh by 'migrate:fresh', no need to truncate.

        DB::table('family_accounts')->insert([
            [
                'id' => '9',
                'family_name' => 'team 1',
                'family_code' => 'FAM-6IEQGKHK',
                'service_package_id' => '48',
                'owner_email' => 'kiendtph49182@gmail.com',
                'owner_name' => 'đỗ trung kiên',
                'max_members' => '4',
                'current_members' => '0',
                'activated_at' => '2025-08-04 00:00:00',
                'expires_at' => '2025-09-04 00:00:00',
                'status' => 'active',
                'family_notes' => null,
                'internal_notes' => null,
                'family_settings' => null,
                'created_by' => null,
                'managed_by' => null,
                'created_at' => '2025-08-04 00:01:08',
                'updated_at' => '2025-08-04 00:01:08',
            ],
            [
                'id' => '17',
                'family_name' => 'Gia đình 1',
                'family_code' => 'FAM-6836EC1F',
                'service_package_id' => '48',
                'owner_email' => 'hoangthanh13232@gmail.com',
                'owner_name' => 'Chủ gia đình 1',
                'max_members' => '6',
                'current_members' => '6',
                'activated_at' => '2025-08-16 17:38:12',
                'expires_at' => '2026-08-16 17:38:12',
                'status' => 'active',
                'family_notes' => 'Nhóm gia đình 1 - Tự động tạo',
                'internal_notes' => 'Được tạo tự động từ script trực tiếp\n[20/08/2025 23:44] AUTO FIX: Chuyển từ \'ChatGPT Plus dùng chung\' sang \'Cha\' để sửa lỗi phân loại gói dịch vụ.',
                'family_settings' => null,
                'created_by' => null,
                'managed_by' => null,
                'created_at' => '2025-08-16 17:38:12',
                'updated_at' => '2025-08-20 23:44:25',
            ],
        ]);

        DB::table('family_members')->insert([
            [
                'id' => '3',
                'family_account_id' => '17',
                'customer_id' => '360',
                'member_name' => 'Chủ gia đình 1',
                'member_email' => 'hoangthanh13232@gmail.com',
                'member_role' => 'admin',
                'status' => 'active',
                'permissions' => null,
                'last_active_at' => null,
                'removed_at' => null,
                'usage_count' => '0',
                'first_usage_at' => null,
                'last_usage_at' => null,
                'member_notes' => 'Test update at 2025-08-23 10:37:44',
                'expires_at' => null,
                'start_date' => '2025-08-16',
                'end_date' => '2025-09-17',
                'internal_notes' => 'Được tạo tự động từ script trực tiếp',
                'added_by' => null,
                'removed_by' => null,
                'created_at' => '2025-08-16 17:38:12',
                'updated_at' => '2025-08-23 10:59:40',
            ],
        ]);

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // --- End of Generated Seeder Content ---
    }
}
