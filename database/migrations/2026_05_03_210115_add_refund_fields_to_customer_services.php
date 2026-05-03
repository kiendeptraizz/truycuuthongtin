<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Refund tracking — admin tính tiền hoàn theo % thời gian còn lại,
 * xác nhận trả thủ công, hệ thống lưu ghi nhận + huỷ đơn.
 *
 * Phân biệt với cancel thường: refunded_at IS NOT NULL
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_services', function (Blueprint $table) {
            if (!Schema::hasColumn('customer_services', 'refund_amount')) {
                $table->bigInteger('refund_amount')->nullable()->after('order_amount')
                    ->comment('Số tiền đã hoàn cho khách (admin trả thủ công)');
            }
            if (!Schema::hasColumn('customer_services', 'refunded_at')) {
                $table->timestamp('refunded_at')->nullable()->after('expires_at')
                    ->comment('Thời điểm admin xác nhận hoàn — phân biệt với cancel thường');
            }
            if (!Schema::hasColumn('customer_services', 'refund_reason')) {
                $table->text('refund_reason')->nullable()->after('internal_notes')
                    ->comment('Lý do hoàn tiền (admin nhập khi confirm)');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customer_services', function (Blueprint $table) {
            foreach (['refund_reason', 'refunded_at', 'refund_amount'] as $col) {
                if (Schema::hasColumn('customer_services', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
