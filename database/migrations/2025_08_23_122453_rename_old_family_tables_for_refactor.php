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
        Schema::rename('family_accounts', 'family_accounts_old');
        Schema::rename('family_members', 'family_members_old');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('family_accounts_old', 'family_accounts');
        Schema::rename('family_members_old', 'family_members');
    }
};
