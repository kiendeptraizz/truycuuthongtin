<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Đồng bộ field web ↔ bot Telegram:
 *  - order_amount: số tiền đơn hàng (tương đương PendingOrder.amount). Web sửa được.
 *  - family_code: mã nhóm-gia đình free text (tương đương bot bước 5). KHÁC family_account_id
 *    là foreign key tới module Family Accounts (giữ riêng cho compat).
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('customer_services', function (Blueprint $t) {
            if (!Schema::hasColumn('customer_services', 'order_amount')) {
                $t->decimal('order_amount', 15, 2)->nullable()->after('warranty_days');
            }
            if (!Schema::hasColumn('customer_services', 'family_code')) {
                $t->string('family_code', 100)->nullable()->after('order_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customer_services', function (Blueprint $t) {
            if (Schema::hasColumn('customer_services', 'family_code')) {
                $t->dropColumn('family_code');
            }
            if (Schema::hasColumn('customer_services', 'order_amount')) {
                $t->dropColumn('order_amount');
            }
        });
    }
};
