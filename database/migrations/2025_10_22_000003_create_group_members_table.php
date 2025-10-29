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
        Schema::create('group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('target_group_id')->constrained('target_groups')->onDelete('cascade');
            $table->string('zalo_id')->comment('Zalo ID của thành viên');
            $table->string('display_name')->nullable()->comment('Tên hiển thị');
            $table->string('phone_number')->nullable()->comment('Số điện thoại (nếu có)');
            $table->text('avatar_url')->nullable()->comment('URL avatar');
            $table->enum('status', ['new', 'contacted', 'converted', 'failed', 'blocked'])->default('new');
            $table->timestamp('joined_at')->nullable()->comment('Ngày tham gia nhóm');
            $table->timestamp('last_contacted_at')->nullable()->comment('Lần liên hệ cuối');
            $table->integer('contact_count')->default(0)->comment('Số lần đã liên hệ');
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['target_group_id', 'zalo_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_members');
    }
};

