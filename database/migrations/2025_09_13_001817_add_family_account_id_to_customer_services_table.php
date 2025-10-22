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
        Schema::table('customer_services', function (Blueprint $table) {
            $table->unsignedBigInteger('family_account_id')->nullable()->after('service_package_id');
            $table->foreign('family_account_id')->references('id')->on('family_accounts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_services', function (Blueprint $table) {
            $table->dropForeign(['family_account_id']);
            $table->dropColumn('family_account_id');
        });
    }
};
