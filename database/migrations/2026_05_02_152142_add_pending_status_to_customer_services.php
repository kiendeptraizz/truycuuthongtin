<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Thêm 'pending' vào enum status của customer_services.
 *
 * Cho hybrid flow: bot Telegram finalize → tạo CS với status='pending' (đã có
 * trên web nhưng chưa active). Khi Pay2S báo paid → đổi sang 'active'.
 */
return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE customer_services MODIFY COLUMN status ENUM('active', 'expired', 'cancelled', 'pending') NOT NULL DEFAULT 'active'");
    }

    public function down(): void
    {
        // Convert pending về active trước khi shrink enum để không vi phạm constraint
        DB::table('customer_services')->where('status', 'pending')->update(['status' => 'cancelled']);
        DB::statement("ALTER TABLE customer_services MODIFY COLUMN status ENUM('active', 'expired', 'cancelled') NOT NULL DEFAULT 'active'");
    }
};
