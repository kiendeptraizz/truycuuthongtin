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
        Schema::create('family_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('family_name');
            $table->string('family_code')->unique();
            $table->unsignedBigInteger('service_package_id');
            $table->string('owner_email');
            $table->string('owner_name')->nullable();
            $table->unsignedTinyInteger('max_members')->default(1);
            $table->unsignedTinyInteger('current_members')->default(0);
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('status')->default('active')->index();
            $table->text('family_notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->json('family_settings')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('managed_by')->nullable();

            // Custom named foreign keys to avoid conflicts
            $table->foreign('service_package_id', 'fk_new_family_accounts_service_package_id')->references('id')->on('service_packages');
            $table->foreign('created_by', 'fk_new_family_accounts_created_by')->references('id')->on('admins')->onDelete('set null');
            $table->foreign('managed_by', 'fk_new_family_accounts_managed_by')->references('id')->on('admins')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_accounts');
    }
};
