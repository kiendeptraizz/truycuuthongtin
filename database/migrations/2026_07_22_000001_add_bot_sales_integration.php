<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Tích hợp bán hàng tự động (bot Python + web shop) vào CRM.
 *
 * - pending_orders.channel: kênh bán tự động (shop_web / bot_le / bot_si / dropship).
 *   NULL = đơn CRM gốc (web admin / bot Telegram của CRM).
 * - bot_product_map: ánh xạ product_id bên bot → service_package_id bên CRM.
 * - Seed 1 ServiceCategory riêng "🤖 Bán tự động (Bot/Web)" để gom các gói bot,
 *   không lẫn với dịch vụ CRM quản lý tay.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('pending_orders', 'channel')) {
            Schema::table('pending_orders', function (Blueprint $table) {
                // Kênh bán tự động. NULL = đơn CRM gốc.
                $table->string('channel', 32)->nullable()->after('created_via')->index();
            });
        }

        if (!Schema::hasTable('bot_product_map')) {
            Schema::create('bot_product_map', function (Blueprint $table) {
                $table->id();
                // product_id bên bot Python (vd 'chatgpt_plus_dung_chung_1_thang')
                $table->string('bot_product_id', 191)->unique();
                $table->foreignId('service_package_id')
                    ->constrained('service_packages')
                    ->cascadeOnDelete();
                // Lưu lại thông tin bot lần cuối để tiện đối chiếu (không bắt buộc)
                $table->string('last_name')->nullable();
                $table->timestamps();
            });
        }

        // Seed category riêng cho SP bot (idempotent theo tên).
        $catName = '🤖 Bán tự động (Bot/Web)';
        $exists = DB::table('service_categories')->where('name', $catName)->exists();
        if (!$exists) {
            DB::table('service_categories')->insert([
                'name' => $catName,
                'description' => 'Các sản phẩm bán tự động qua bot Telegram & web shop (đồng bộ tự động, không nhập tay).',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('bot_product_map')) {
            Schema::dropIfExists('bot_product_map');
        }
        if (Schema::hasColumn('pending_orders', 'channel')) {
            Schema::table('pending_orders', function (Blueprint $table) {
                $table->dropIndex(['channel']);
                $table->dropColumn('channel');
            });
        }
        // Không xoá category khi rollback (tránh mất dữ liệu nếu đã gắn gói).
    }
};
