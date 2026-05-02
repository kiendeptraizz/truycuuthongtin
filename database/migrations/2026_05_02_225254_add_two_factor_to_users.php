<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 2FA TOTP cho admin login. Optional — admin tự enable trong settings.
 *   - two_factor_secret: base32 secret (16 chars) sinh khi setup
 *   - two_factor_enabled_at: null = chưa bật; có giá trị = đã verify code 1 lần
 *   - two_factor_recovery_codes: JSON 8 mã backup nếu mất authenticator
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'two_factor_secret')) {
                $table->string('two_factor_secret', 64)->nullable()->after('password');
            }
            if (!Schema::hasColumn('users', 'two_factor_enabled_at')) {
                $table->timestamp('two_factor_enabled_at')->nullable()->after('two_factor_secret');
            }
            if (!Schema::hasColumn('users', 'two_factor_recovery_codes')) {
                $table->json('two_factor_recovery_codes')->nullable()->after('two_factor_enabled_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['two_factor_recovery_codes', 'two_factor_enabled_at', 'two_factor_secret'] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
