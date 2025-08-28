<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompleteMigrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Log::info('Starting complete service package migration...');

        try {
            // Bước 1: Backup dữ liệu hiện tại
            $this->command->info('Step 1: Creating backup...');
            $this->call(BackupCurrentDataSeeder::class);

            // Bước 2: Tạo danh mục mới
            $this->command->info('Step 2: Creating new service categories...');
            $this->call(NewServiceCategorySeeder::class);

            // Bước 3: Tạo gói dịch vụ mới
            $this->command->info('Step 3: Creating new service packages...');
            $this->call(NewServicePackageSeeder::class);
            $this->call(NewServicePackageSeeder2::class);

            // Bước 4: Migration customer services
            $this->command->info('Step 4: Migrating customer services...');
            $this->call(CustomerServiceMigrationSeeder::class);

            // Bước 5: Cleanup và validation
            $this->command->info('Step 5: Validation and cleanup...');
            $this->validateMigration();

            Log::info('Migration completed successfully!');
            $this->command->info('✅ Migration completed successfully!');

        } catch (\Exception $e) {
            Log::error('Migration failed: ' . $e->getMessage());
            $this->command->error('❌ Migration failed: ' . $e->getMessage());
            
            // Rollback nếu có lỗi
            $this->rollbackMigration();
        }
    }

    /**
     * Validate migration results
     */
    private function validateMigration()
    {
        // Kiểm tra số lượng danh mục
        $categoryCount = DB::table('service_categories')->count();
        if ($categoryCount !== 7) {
            throw new \Exception("Expected 7 categories, found {$categoryCount}");
        }

        // Kiểm tra số lượng gói dịch vụ
        $packageCount = DB::table('service_packages')->count();
        $this->command->info("Created {$packageCount} service packages");

        // Kiểm tra customer services
        $customerServiceCount = DB::table('customer_services')->count();
        $backupServiceCount = DB::table('customer_services_backup')->count();
        
        $this->command->info("Migrated {$customerServiceCount} customer services from {$backupServiceCount} original services");

        // Kiểm tra foreign key constraints
        $orphanedServices = DB::table('customer_services')
            ->leftJoin('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
            ->whereNull('service_packages.id')
            ->count();

        if ($orphanedServices > 0) {
            throw new \Exception("Found {$orphanedServices} orphaned customer services");
        }

        $this->command->info('✅ All validations passed!');
    }

    /**
     * Rollback migration if something goes wrong
     */
    private function rollbackMigration()
    {
        $this->command->warn('🔄 Rolling back migration...');
        
        try {
            // Restore from backup
            DB::statement('DELETE FROM customer_services');
            DB::statement('DELETE FROM service_packages');
            DB::statement('DELETE FROM service_categories');

            // Restore categories
            DB::statement('INSERT INTO service_categories SELECT * FROM service_categories_backup');
            
            // Restore packages
            DB::statement('INSERT INTO service_packages SELECT * FROM service_packages_backup');
            
            // Restore customer services
            DB::statement('INSERT INTO customer_services (id, customer_id, service_package_id, login_email, login_password, activated_at, expires_at, status, internal_notes, created_at, updated_at) SELECT * FROM customer_services_backup');

            $this->command->info('✅ Rollback completed successfully!');
            
        } catch (\Exception $e) {
            $this->command->error('❌ Rollback failed: ' . $e->getMessage());
            Log::error('Rollback failed: ' . $e->getMessage());
        }
    }
}
