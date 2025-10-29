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
        Schema::create('target_groups', function (Blueprint $table) {
            $table->id();
            $table->string('group_name')->comment('Tên nhóm');
            $table->text('group_link')->comment('Link nhóm Zalo');
            $table->string('group_id')->nullable()->comment('ID nhóm Zalo');
            $table->string('topic')->nullable()->comment('Chủ đề nhóm');
            $table->integer('total_members')->default(0)->comment('Tổng số thành viên');
            $table->date('opening_date')->nullable()->comment('Ngày khai giảng (nếu có)');
            $table->enum('group_type', ['competitor', 'own'])->default('competitor')->comment('Loại nhóm: đối thủ hoặc của mình');
            $table->enum('status', ['active', 'inactive', 'completed'])->default('active');
            $table->timestamp('last_scanned_at')->nullable()->comment('Lần quét thành viên cuối');
            $table->text('description')->nullable()->comment('Mô tả nhóm');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('target_groups');
    }
};

