<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('home_settings')) {
            Schema::create('home_settings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('customers_override')->nullable();
                $table->unsignedBigInteger('services_override')->nullable();
                $table->unsignedBigInteger('packages_override')->nullable();
                $table->timestamps();
            });
        }

        // Singleton row id=1, mọi cột NULL = dùng số thực từ DB count()
        DB::table('home_settings')->updateOrInsert(
            ['id' => 1],
            [
                'customers_override' => null,
                'services_override' => null,
                'packages_override' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('home_settings');
    }
};
