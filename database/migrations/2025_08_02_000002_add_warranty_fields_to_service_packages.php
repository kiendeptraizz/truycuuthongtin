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
        Schema::table('service_packages', function (Blueprint $table) {
            // Thêm trường bảo hành
            $table->string('warranty_type')->nullable()->after('description')->comment('Loại bảo hành: full, KBH, 1MONTH, 3 tháng');
            
            // Thêm trường ghi chú chi tiết
            $table->text('detailed_notes')->nullable()->after('warranty_type')->comment('Ghi chú chi tiết về gói dịch vụ');
            
            // Thêm trường thời hạn tùy chỉnh (để hỗ trợ các gói có thời hạn đặc biệt)
            $table->string('custom_duration')->nullable()->after('default_duration_days')->comment('Thời hạn tùy chỉnh như "15 ngày", "1 năm"');
            
            // Thêm trường để đánh dấu gói có thể gia hạn
            $table->boolean('is_renewable')->default(true)->after('is_active')->comment('Có thể gia hạn hay không');
            
            // Thêm trường số lượng thiết bị
            $table->integer('device_limit')->nullable()->after('is_renewable')->comment('Giới hạn số thiết bị, null = không giới hạn');
            
            // Thêm trường số người dùng chung
            $table->integer('shared_users_limit')->nullable()->after('device_limit')->comment('Số người dùng chung, null = không dùng chung');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_packages', function (Blueprint $table) {
            $table->dropColumn([
                'warranty_type',
                'detailed_notes', 
                'custom_duration',
                'is_renewable',
                'device_limit',
                'shared_users_limit'
            ]);
        });
    }
};
