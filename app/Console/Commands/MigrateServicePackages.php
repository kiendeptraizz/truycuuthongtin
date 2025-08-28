<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Database\Seeders\CompleteMigrationSeeder;

class MigrateServicePackages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:service-packages 
                            {--force : Force migration without confirmation}
                            {--dry-run : Show what would be migrated without actually doing it}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate service packages to new structure with categories and enhanced features';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Service Package Migration Tool');
        $this->info('=====================================');

        if ($this->option('dry-run')) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
            return $this->dryRun();
        }

        // Show current state
        $this->showCurrentState();

        // Confirmation
        if (!$this->option('force')) {
            if (!$this->confirm('Do you want to proceed with the migration? This will backup current data and replace it with new structure.')) {
                $this->info('Migration cancelled.');
                return 0;
            }
        }

        // Run migration
        try {
            $this->info('🔄 Starting migration process...');
            
            // Run migrations first
            $this->call('migrate');
            
            // Run the complete migration seeder
            $this->call('db:seed', ['--class' => CompleteMigrationSeeder::class]);
            
            $this->info('✅ Migration completed successfully!');
            $this->showNewState();
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ Migration failed: ' . $e->getMessage());
            Log::error('Service package migration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return 1;
        }
    }

    /**
     * Show current state of the system
     */
    private function showCurrentState()
    {
        $this->info('📊 Current System State:');
        
        $categoryCount = DB::table('service_categories')->count();
        $packageCount = DB::table('service_packages')->count();
        $customerServiceCount = DB::table('customer_services')->count();
        
        $this->table(['Item', 'Count'], [
            ['Service Categories', $categoryCount],
            ['Service Packages', $packageCount],
            ['Customer Services', $customerServiceCount],
        ]);

        // Show current categories
        $categories = DB::table('service_categories')->get(['name']);
        if ($categories->count() > 0) {
            $this->info('Current Categories:');
            foreach ($categories as $category) {
                $this->line("  • {$category->name}");
            }
        }
    }

    /**
     * Show new state after migration
     */
    private function showNewState()
    {
        $this->info('📊 New System State:');
        
        $categoryCount = DB::table('service_categories')->count();
        $packageCount = DB::table('service_packages')->count();
        $customerServiceCount = DB::table('customer_services')->count();
        
        $this->table(['Item', 'Count'], [
            ['Service Categories', $categoryCount],
            ['Service Packages', $packageCount],
            ['Customer Services', $customerServiceCount],
        ]);

        // Show new categories
        $categories = DB::table('service_categories')->get(['name']);
        $this->info('New Categories:');
        foreach ($categories as $category) {
            $packageCount = DB::table('service_packages')
                ->where('category_id', DB::table('service_categories')->where('name', $category->name)->value('id'))
                ->count();
            $this->line("  • {$category->name} ({$packageCount} packages)");
        }
    }

    /**
     * Dry run to show what would be migrated
     */
    private function dryRun()
    {
        $this->info('This migration would:');
        $this->line('1. ✅ Backup current service categories, packages, and customer services');
        $this->line('2. ✅ Create 7 new service categories:');
        $this->line('   • AI');
        $this->line('   • AI làm video');
        $this->line('   • AI giọng đọc');
        $this->line('   • AI coding');
        $this->line('   • Công cụ làm việc');
        $this->line('   • Công cụ học tập');
        $this->line('   • Công cụ giải trí và xem phim');
        $this->line('3. ✅ Create ~40+ new service packages with enhanced features');
        $this->line('4. ✅ Migrate existing customer services to new packages');
        $this->line('5. ✅ Add new fields: warranty_type, detailed_notes, custom_duration, etc.');
        $this->line('6. ✅ Validate data integrity');
        
        $this->warn('⚠️  Current data will be backed up but replaced with new structure');
        $this->info('💡 Use --force to skip confirmation or run without --dry-run to execute');
        
        return 0;
    }
}
