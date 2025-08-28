<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BackupCurrentDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo bảng backup nếu chưa tồn tại
        if (!Schema::hasTable('service_categories_backup')) {
            Schema::create('service_categories_backup', function ($table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('service_packages_backup')) {
            Schema::create('service_packages_backup', function ($table) {
                $table->id();
                $table->foreignId('category_id');
                $table->string('name');
                $table->string('account_type');
                $table->integer('default_duration_days');
                $table->decimal('price', 10, 2);
                $table->decimal('cost_price', 10, 2)->nullable();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('customer_services_backup')) {
            Schema::create('customer_services_backup', function ($table) {
                $table->id();
                $table->foreignId('customer_id');
                $table->foreignId('service_package_id');
                $table->string('login_email')->nullable();
                $table->string('login_password')->nullable();
                $table->datetime('activated_at')->nullable();
                $table->datetime('expires_at')->nullable();
                $table->enum('status', ['active', 'expired', 'suspended', 'cancelled'])->default('active');
                $table->text('internal_notes')->nullable();
                $table->timestamps();
            });
        }

        // Xóa dữ liệu backup cũ
        DB::table('customer_services_backup')->truncate();
        DB::table('service_packages_backup')->truncate();
        DB::table('service_categories_backup')->truncate();

        // Backup dữ liệu hiện tại
        DB::statement('INSERT INTO service_categories_backup SELECT * FROM service_categories');
        DB::statement('INSERT INTO service_packages_backup SELECT id, category_id, name, account_type, default_duration_days, price, cost_price, description, is_active, created_at, updated_at FROM service_packages');
        DB::statement('INSERT INTO customer_services_backup SELECT id, customer_id, service_package_id, login_email, login_password, activated_at, expires_at, status, internal_notes, created_at, updated_at FROM customer_services');

        $this->command->info('✅ Data backup completed successfully!');
    }
}
