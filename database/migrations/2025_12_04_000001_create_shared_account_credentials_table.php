<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shared_account_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_package_id')->constrained()->onDelete('cascade');
            $table->string('email');
            $table->text('password')->nullable();
            $table->text('two_factor_secret')->nullable();
            $table->text('recovery_codes')->nullable();
            $table->text('notes')->nullable();
            $table->integer('max_users')->default(10); // Số người dùng tối đa
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->enum('status', ['active', 'expired', 'suspended', 'full'])->default('active');
            $table->timestamps();

            // Index
            $table->index(['service_package_id', 'is_active']);
            $table->index(['email']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shared_account_credentials');
    }
};

