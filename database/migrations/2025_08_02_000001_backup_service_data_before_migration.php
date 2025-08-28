<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tạo bảng backup cho service_categories
        Schema::create('service_categories_backup', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Tạo bảng backup cho service_packages
        Schema::create('service_packages_backup', function (Blueprint $table) {
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

        // Tạo bảng backup cho customer_services
        Schema::create('customer_services_backup', function (Blueprint $table) {
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

        // Backup dữ liệu hiện tại
        DB::statement('INSERT INTO service_categories_backup SELECT * FROM service_categories');
        DB::statement('INSERT INTO service_packages_backup SELECT * FROM service_packages');
        DB::statement('INSERT INTO customer_services_backup SELECT id, customer_id, service_package_id, login_email, login_password, activated_at, expires_at, status, internal_notes, created_at, updated_at FROM customer_services');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_services_backup');
        Schema::dropIfExists('service_packages_backup');
        Schema::dropIfExists('service_categories_backup');
    }
};
