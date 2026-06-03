<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cho phép NULL 3 field thường required của customer_services:
     *   - customer_id
     *   - service_package_id
     *   - login_email
     *
     * Mục đích: cho phép tạo CS placeholder cho đơn nhanh CTV (bấm nút "Hoàn thành"
     * trên /admin/pending-orders mà không cần fill chi tiết gói/email). CS placeholder
     * này vẫn hiển thị ở /admin/customer-services với badge "🏃 Đơn nhanh", có
     * order_code + activated_at + expires_at (nếu nhập duration ở bot bước 3/4) +
     * status='active' để xuất hiện trong list, nhưng không có gói/email cụ thể.
     *
     * View + controller code đã được null-safe để handle các CS này.
     */
    public function up(): void
    {
        // Cần doctrine/dbal cho ->change(). Project có sẵn (Laravel 12) — nếu không thì
        // raw SQL fallback.
        try {
            Schema::table('customer_services', function (Blueprint $table) {
                $table->unsignedBigInteger('customer_id')->nullable()->change();
                $table->unsignedBigInteger('service_package_id')->nullable()->change();
                $table->string('login_email', 255)->nullable()->change();
            });
        } catch (\Throwable $e) {
            // Fallback raw SQL nếu doctrine/dbal không có
            \DB::statement('ALTER TABLE customer_services MODIFY customer_id BIGINT UNSIGNED NULL');
            \DB::statement('ALTER TABLE customer_services MODIFY service_package_id BIGINT UNSIGNED NULL');
            \DB::statement('ALTER TABLE customer_services MODIFY login_email VARCHAR(255) NULL');
        }
    }

    public function down(): void
    {
        // KHÔNG rollback — sẽ fail nếu có CS đã NULL các field này.
        // Nếu thực sự cần rollback, phải xoá hết CS placeholder trước:
        //   DELETE FROM customer_services WHERE service_package_id IS NULL;
        // Sau đó mới chạy được ALTER MODIFY NOT NULL.
        // Để an toàn — no-op.
    }
};
