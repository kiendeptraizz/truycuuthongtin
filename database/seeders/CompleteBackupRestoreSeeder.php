<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\CustomerService;
use App\Models\ServicePackage;

class CompleteBackupRestoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $backupFile = storage_path('app/backups/AUTO_BACKUP_manual_2025-08-03_01-24-45.json');
        
        if (!file_exists($backupFile)) {
            $this->command->error('Backup file not found!');
            return;
        }

        $this->command->info('🔄 Starting complete backup restoration...');
        
        $backup = json_decode(file_get_contents($backupFile), true);
        
        if (!$backup || !isset($backup['customers']) || !isset($backup['customer_services'])) {
            $this->command->error('Invalid backup file format!');
            return;
        }

        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            $this->restoreMissingCustomers($backup['customers']);
            $this->restoreMissingServices($backup['customer_services']);
        } finally {
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        $this->command->info('✅ Complete backup restoration finished!');
    }

    private function restoreMissingCustomers($backupCustomers)
    {
        $this->command->info('📋 Restoring missing customers...');
        
        $currentCustomerIds = Customer::pluck('id')->toArray();
        $restored = 0;
        $skipped = 0;

        foreach ($backupCustomers as $customerData) {
            if (in_array($customerData['id'], $currentCustomerIds)) {
                $skipped++;
                continue;
            }

            try {
                Customer::create([
                    'id' => $customerData['id'],
                    'customer_code' => $customerData['customer_code'],
                    'name' => $customerData['name'],
                    'email' => $customerData['email'] ?? null,
                    'phone' => $customerData['phone'] ?? null,
                    'notes' => $customerData['notes'] ?? null,
                    'created_at' => $customerData['created_at'],
                    'updated_at' => $customerData['updated_at'],
                ]);
                $restored++;
            } catch (\Exception $e) {
                $this->command->warn("Failed to restore customer {$customerData['id']}: " . $e->getMessage());
            }
        }

        $this->command->info("✅ Customers - Restored: {$restored}, Skipped: {$skipped}");
    }

    private function restoreMissingServices($backupServices)
    {
        $this->command->info('🔧 Restoring missing customer services...');
        
        $currentServiceIds = CustomerService::pluck('id')->toArray();
        $currentCustomerIds = Customer::pluck('id')->toArray();
        $currentPackageIds = ServicePackage::pluck('id')->toArray();
        
        $restored = 0;
        $skippedMissingCustomer = 0;
        $skippedInvalidPackage = 0;
        $skippedExists = 0;
        $skippedOther = 0;

        foreach ($backupServices as $serviceData) {
            if (in_array($serviceData['id'], $currentServiceIds)) {
                $skippedExists++;
                continue;
            }

            // Check if customer exists
            if (!in_array($serviceData['customer_id'], $currentCustomerIds)) {
                $skippedMissingCustomer++;
                continue;
            }

            // Check if package exists
            if (!in_array($serviceData['service_package_id'], $currentPackageIds)) {
                $skippedInvalidPackage++;
                continue;
            }

            try {
                $newServiceData = [
                    'id' => $serviceData['id'],
                    'customer_id' => $serviceData['customer_id'],
                    'service_package_id' => $serviceData['service_package_id'],
                    'assigned_by' => $serviceData['assigned_by'] ?? null,
                    'login_email' => $serviceData['login_email'] ?? null,
                    'login_password' => $serviceData['login_password'] ?? null,
                    'activated_at' => $serviceData['activated_at'] ?? null,
                    'expires_at' => $serviceData['expires_at'] ?? null,
                    'status' => $serviceData['status'] ?? 'active',
                    'internal_notes' => $serviceData['internal_notes'] ?? null,
                    'created_at' => $serviceData['created_at'] ?? now(),
                    'updated_at' => $serviceData['updated_at'] ?? now(),
                ];

                // Add supplier_id if valid (1-8)
                if (isset($serviceData['supplier_id']) && $serviceData['supplier_id'] && $serviceData['supplier_id'] <= 8) {
                    $newServiceData['supplier_id'] = $serviceData['supplier_id'];
                }

                // Add optional fields
                $optionalFields = [
                    'supplier_service_id', 'reminder_sent', 'reminder_sent_at', 'reminder_count',
                    'reminder_notes', 'two_factor_code', 'recovery_codes', 'shared_account_notes',
                    'customer_instructions', 'password_expires_at', 'two_factor_updated_at',
                    'is_password_shared', 'shared_with_customers'
                ];

                foreach ($optionalFields as $field) {
                    if (isset($serviceData[$field])) {
                        $newServiceData[$field] = $serviceData[$field];
                    }
                }

                CustomerService::create($newServiceData);
                $restored++;

            } catch (\Exception $e) {
                $this->command->warn("Failed to restore service {$serviceData['id']}: " . $e->getMessage());
                $skippedOther++;
            }
        }

        $this->command->info("✅ Services - Restored: {$restored}");
        $this->command->info("⚠️  Skipped - Exists: {$skippedExists}, Missing Customer: {$skippedMissingCustomer}, Invalid Package: {$skippedInvalidPackage}, Other: {$skippedOther}");
    }
}
