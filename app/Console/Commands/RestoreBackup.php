<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RestoreBackup extends Command
{
    protected $signature = 'backup:restore {file}';
    protected $description = 'Restore database from JSON backup file';

    public function handle()
    {
        $backupFile = $this->argument('file');

        if (!file_exists($backupFile)) {
            $this->error("Backup file not found: $backupFile");
            return 1;
        }

        $this->info("Starting database restore from backup...");

        $backupData = json_decode(file_get_contents($backupFile), true);
        if (!$backupData) {
            $this->error("Failed to parse backup file");
            return 1;
        }

        $this->info("Backup file loaded successfully");
        $this->info("Backup created at: " . $backupData['backup_info']['created_at']);

        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Restore tables in order (considering foreign key dependencies)
        $tablesToRestore = [
            'suppliers' => $backupData['suppliers'] ?? [],
            'service_packages' => $backupData['service_packages'] ?? [],
            'customers' => $backupData['customers'] ?? [],
            'customer_services' => $backupData['customer_services'] ?? [],
            'leads' => $backupData['leads'] ?? []
        ];

        foreach ($tablesToRestore as $tableName => $tableData) {
            $this->restoreTable($tableName, $tableData);
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info("\nDatabase restore completed successfully!");
        $this->info("Summary:");
        foreach ($tablesToRestore as $tableName => $tableData) {
            $this->info("- $tableName: " . count($tableData) . " records restored");
        }

        $this->info("\nAll customer data and services have been restored from backup.");
        return 0;
    }

    private function restoreTable($tableName, $data)
    {
        if (empty($data)) {
            $this->info("No data to restore for table: $tableName");
            return;
        }

        $this->info("Restoring table: $tableName (" . count($data) . " records)");

        // Check if table exists
        if (!Schema::hasTable($tableName)) {
            $this->warn("Table $tableName does not exist, skipping...");
            return;
        }

        // Truncate table first
        DB::table($tableName)->truncate();

        // Process and fix datetime formats
        $processedData = $this->processDatetimeFields($data);

        // Insert data in chunks to avoid memory issues
        $chunks = array_chunk($processedData, 100);
        $bar = $this->output->createProgressBar(count($chunks));

        foreach ($chunks as $chunk) {
            try {
                DB::table($tableName)->insert($chunk);
                $bar->advance();
            } catch (\Exception $e) {
                $this->warn("Error inserting chunk for $tableName: " . $e->getMessage());
                // Continue with next chunk
            }
        }

        $bar->finish();
        $this->info("\nCompleted restoring table: $tableName");
    }

    private function processDatetimeFields($data)
    {
        $processedData = [];

        foreach ($data as $record) {
            $processedRecord = [];

            foreach ($record as $field => $value) {
                // Convert ISO 8601 datetime format to MySQL format
                if (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{6}Z$/', $value)) {
                    $processedRecord[$field] = date('Y-m-d H:i:s', strtotime($value));
                } else {
                    $processedRecord[$field] = $value;
                }
            }

            $processedData[] = $processedRecord;
        }

        return $processedData;
    }
}
