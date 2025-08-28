<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\CustomerService;
use App\Models\ServicePackage;

class ApplyPackageMappingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔄 Starting package mapping application...');
        
        // Load mapping table
        $mappingFile = base_path('package_mapping.json');
        if (!file_exists($mappingFile)) {
            $this->command->error('❌ Mapping file not found! Please run create_mapping_table.php first.');
            return;
        }
        
        $mappingTable = json_decode(file_get_contents($mappingFile), true);
        
        if (empty($mappingTable)) {
            $this->command->error('❌ Empty mapping table!');
            return;
        }
        
        $this->command->info("📋 Loaded mapping table with " . count($mappingTable) . " mappings");
        
        // Verify all new package IDs exist
        $newPackageIds = array_values($mappingTable);
        $existingPackageIds = ServicePackage::whereIn('id', $newPackageIds)->pluck('id')->toArray();
        $missingPackageIds = array_diff($newPackageIds, $existingPackageIds);
        
        if (!empty($missingPackageIds)) {
            $this->command->error('❌ Missing new package IDs: ' . implode(', ', $missingPackageIds));
            return;
        }
        
        // Get all customer services that need mapping
        $servicesToUpdate = CustomerService::whereIn('service_package_id', array_keys($mappingTable))->get();
        
        $this->command->info("🎯 Found {$servicesToUpdate->count()} services to update");
        
        if ($servicesToUpdate->isEmpty()) {
            $this->command->warn('⚠️ No services found to update');
            return;
        }
        
        // Show sample of what will be updated
        $this->command->info("\n📝 Sample mappings to be applied:");
        $sampleCount = 0;
        foreach ($mappingTable as $oldId => $newId) {
            if ($sampleCount >= 5) break;
            
            $oldPackage = $this->getOldPackageName($oldId);
            $newPackage = ServicePackage::find($newId);
            
            if ($newPackage) {
                $this->command->info("  {$oldId} -> {$newId}: '{$oldPackage}' -> '{$newPackage->name}'");
                $sampleCount++;
            }
        }
        
        // Confirm before proceeding
        if (!$this->command->confirm("\n❓ Proceed with updating {$servicesToUpdate->count()} customer services?")) {
            $this->command->info('❌ Operation cancelled by user');
            return;
        }
        
        // Apply mapping
        $this->applyMapping($servicesToUpdate, $mappingTable);
        
        $this->command->info('✅ Package mapping application completed!');
    }
    
    private function applyMapping($services, $mappingTable)
    {
        $this->command->info("\n🔧 Applying package mapping...");
        
        $updated = 0;
        $skipped = 0;
        $errors = 0;
        
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        try {
            foreach ($services as $service) {
                $oldPackageId = $service->service_package_id;
                $newPackageId = $mappingTable[$oldPackageId] ?? null;
                
                if (!$newPackageId) {
                    $this->command->warn("⚠️ No mapping found for package ID {$oldPackageId}");
                    $skipped++;
                    continue;
                }
                
                if ($oldPackageId == $newPackageId) {
                    $skipped++;
                    continue; // No change needed
                }
                
                try {
                    // CRITICAL: Only update service_package_id, preserve ALL other data
                    DB::table('customer_services')
                        ->where('id', $service->id)
                        ->update([
                            'service_package_id' => $newPackageId,
                            'updated_at' => now() // Only update timestamp
                        ]);
                    
                    $updated++;
                    
                    if ($updated % 50 == 0) {
                        $this->command->info("  ✅ Updated {$updated} services...");
                    }
                    
                } catch (\Exception $e) {
                    $this->command->error("❌ Error updating service {$service->id}: " . $e->getMessage());
                    $errors++;
                }
            }
            
        } finally {
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
        
        $this->command->info("\n📊 MAPPING RESULTS:");
        $this->command->info("✅ Updated: {$updated} services");
        $this->command->info("⏭️ Skipped: {$skipped} services");
        $this->command->info("❌ Errors: {$errors} services");
        
        // Verify data integrity
        $this->verifyDataIntegrity();
    }
    
    private function verifyDataIntegrity()
    {
        $this->command->info("\n🔍 Verifying data integrity...");
        
        // Check all services have valid packages
        $invalidServices = CustomerService::whereNotIn('service_package_id', 
            ServicePackage::pluck('id'))->count();
        
        if ($invalidServices > 0) {
            $this->command->error("❌ Found {$invalidServices} services with invalid package IDs!");
        } else {
            $this->command->info("✅ All services have valid package IDs");
        }
        
        // Check time data preservation
        $servicesWithNullDates = CustomerService::where(function($query) {
            $query->whereNull('activated_at')->orWhereNull('expires_at');
        })->count();
        
        $this->command->info("📅 Services with preserved time data: " . 
            (CustomerService::count() - $servicesWithNullDates) . "/" . CustomerService::count());
        
        // Sample verification
        $sampleServices = CustomerService::with(['customer', 'servicePackage.category'])
            ->take(5)->get();
        
        $this->command->info("\n📋 Sample updated services:");
        foreach ($sampleServices as $service) {
            $this->command->info("  Service {$service->id}: {$service->customer->name} -> " .
                "{$service->servicePackage->name} ({$service->servicePackage->category->name})");
            $this->command->info("    Activated: {$service->activated_at} | Expires: {$service->expires_at}");
        }
    }
    
    private function getOldPackageName($packageId)
    {
        // Get from backup
        $backupFile = storage_path('app/backups/AUTO_BACKUP_manual_2025-08-03_01-24-45.json');
        if (file_exists($backupFile)) {
            $backup = json_decode(file_get_contents($backupFile), true);
            foreach ($backup['service_packages'] as $pkg) {
                if ($pkg['id'] == $packageId) {
                    return $pkg['name'];
                }
            }
        }
        return "Unknown Package {$packageId}";
    }
}
