<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RestoreDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔄 Restoring data from backup...');

        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            // Restore customer services first
            if (Schema::hasTable('customer_services_backup')) {
                DB::table('customer_services')->truncate();
                DB::statement('INSERT INTO customer_services (id, customer_id, service_package_id, login_email, login_password, activated_at, expires_at, status, internal_notes, created_at, updated_at) SELECT * FROM customer_services_backup');
                $this->command->info('✅ Customer services restored');
            }

            // Restore packages
            if (Schema::hasTable('service_packages_backup')) {
                DB::table('service_packages')->truncate();
                DB::statement('INSERT INTO service_packages (id, category_id, name, account_type, default_duration_days, price, cost_price, description, is_active, created_at, updated_at) SELECT * FROM service_packages_backup');
                $this->command->info('✅ Packages restored');
            }

            // Restore categories
            if (Schema::hasTable('service_categories_backup')) {
                DB::table('service_categories')->truncate();
                DB::statement('INSERT INTO service_categories SELECT * FROM service_categories_backup');
                $this->command->info('✅ Categories restored');
            }
        } finally {
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        $this->command->info('🎉 Data restoration completed!');
    }
}
