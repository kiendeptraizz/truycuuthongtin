<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Cho phép activated_at + expires_at NULL trong customer_services.
 *
 * Lý do: hybrid flow của bot Telegram tạo CustomerService với status='pending'
 * NGAY khi user finalize đơn (chưa thanh toán). Lúc này chưa biết khách CK khi
 * nào nên activated_at/expires_at chưa có giá trị. Khi Pay2S báo paid, webhook
 * mới set activated_at = now, expires_at = now + duration_days.
 *
 * Schema cũ: cả 2 cột NOT NULL → bot fail "Column 'activated_at' cannot be null".
 *
 * Index vẫn giữ (MUL) sau ALTER.
 */
return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE customer_services MODIFY COLUMN activated_at TIMESTAMP NULL DEFAULT NULL");
        DB::statement("ALTER TABLE customer_services MODIFY COLUMN expires_at TIMESTAMP NULL DEFAULT NULL");
    }

    public function down(): void
    {
        // Trước khi shrink về NOT NULL, fill các NULL bằng created_at làm placeholder
        // (tránh fail constraint khi rollback). Customer service nào pending sẽ thành active "ảo"
        // theo created_at — không khôi phục được logic pending.
        DB::statement("UPDATE customer_services SET activated_at = created_at WHERE activated_at IS NULL");
        DB::statement("UPDATE customer_services SET expires_at = DATE_ADD(created_at, INTERVAL 30 DAY) WHERE expires_at IS NULL");
        DB::statement("ALTER TABLE customer_services MODIFY COLUMN activated_at TIMESTAMP NOT NULL");
        DB::statement("ALTER TABLE customer_services MODIFY COLUMN expires_at TIMESTAMP NOT NULL");
    }
};
