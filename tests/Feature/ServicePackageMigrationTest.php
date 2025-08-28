<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use App\Models\ServiceCategory;
use App\Models\ServicePackage;
use App\Models\CustomerService;
use Database\Seeders\CompleteMigrationSeeder;

class ServicePackageMigrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that migration creates correct number of categories
     */
    public function test_migration_creates_correct_categories()
    {
        // Run migration
        $this->seed(CompleteMigrationSeeder::class);

        // Check categories count
        $this->assertEquals(7, ServiceCategory::count());

        // Check specific categories exist
        $expectedCategories = [
            'AI',
            'AI làm video',
            'AI giọng đọc',
            'AI coding',
            'Công cụ làm việc',
            'Công cụ học tập',
            'Công cụ giải trí và xem phim'
        ];

        foreach ($expectedCategories as $categoryName) {
            $this->assertTrue(
                ServiceCategory::where('name', $categoryName)->exists(),
                "Category '{$categoryName}' should exist"
            );
        }
    }

    /**
     * Test that migration creates service packages
     */
    public function test_migration_creates_service_packages()
    {
        // Run migration
        $this->seed(CompleteMigrationSeeder::class);

        // Should have created multiple packages
        $this->assertGreaterThan(30, ServicePackage::count());

        // Check some specific packages exist
        $this->assertTrue(ServicePackage::where('name', 'ChatGPT Plus dùng chung')->exists());
        $this->assertTrue(ServicePackage::where('name', 'Gemini Pro + 2TB drive chính chủ')->exists());
        $this->assertTrue(ServicePackage::where('name', 'YouTube Premium')->exists());
    }

    /**
     * Test that packages have correct category relationships
     */
    public function test_packages_have_correct_categories()
    {
        // Run migration
        $this->seed(CompleteMigrationSeeder::class);

        // Check AI category packages
        $aiCategory = ServiceCategory::where('name', 'AI')->first();
        $this->assertNotNull($aiCategory);
        
        $aiPackages = $aiCategory->servicePackages;
        $this->assertGreaterThan(0, $aiPackages->count());

        // Check that ChatGPT packages are in AI category
        $chatgptPackage = ServicePackage::where('name', 'like', '%ChatGPT%')->first();
        $this->assertEquals($aiCategory->id, $chatgptPackage->category_id);
    }

    /**
     * Test that new fields are properly set
     */
    public function test_packages_have_new_fields()
    {
        // Run migration
        $this->seed(CompleteMigrationSeeder::class);

        $package = ServicePackage::where('name', 'ChatGPT Plus dùng chung')->first();
        $this->assertNotNull($package);

        // Check new fields exist and have values
        $this->assertNotNull($package->warranty_type);
        $this->assertNotNull($package->detailed_notes);
        $this->assertNotNull($package->custom_duration);
        $this->assertIsInt($package->shared_users_limit);
        $this->assertEquals('full', $package->warranty_type);
        $this->assertEquals(4, $package->shared_users_limit);
    }

    /**
     * Test foreign key constraints
     */
    public function test_foreign_key_constraints()
    {
        // Run migration
        $this->seed(CompleteMigrationSeeder::class);

        // All packages should have valid category_id
        $orphanedPackages = ServicePackage::whereNotIn('category_id', 
            ServiceCategory::pluck('id')
        )->count();
        
        $this->assertEquals(0, $orphanedPackages, 'No packages should have invalid category_id');
    }

    /**
     * Test data integrity after migration
     */
    public function test_data_integrity()
    {
        // Run migration
        $this->seed(CompleteMigrationSeeder::class);

        // All packages should have required fields
        $packagesWithoutName = ServicePackage::whereNull('name')->count();
        $this->assertEquals(0, $packagesWithoutName);

        $packagesWithoutPrice = ServicePackage::whereNull('price')->count();
        $this->assertEquals(0, $packagesWithoutPrice);

        $packagesWithoutAccountType = ServicePackage::whereNull('account_type')->count();
        $this->assertEquals(0, $packagesWithoutAccountType);

        // All categories should have description
        $categoriesWithoutDescription = ServiceCategory::whereNull('description')->count();
        $this->assertEquals(0, $categoriesWithoutDescription);
    }

    /**
     * Test that backup tables are created
     */
    public function test_backup_tables_created()
    {
        // Run migration
        $this->seed(CompleteMigrationSeeder::class);

        // Check backup tables exist
        $this->assertTrue(DB::getSchemaBuilder()->hasTable('service_categories_backup'));
        $this->assertTrue(DB::getSchemaBuilder()->hasTable('service_packages_backup'));
        $this->assertTrue(DB::getSchemaBuilder()->hasTable('customer_services_backup'));
    }
}
