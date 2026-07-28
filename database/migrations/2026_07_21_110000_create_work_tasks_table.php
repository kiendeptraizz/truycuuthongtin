<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique();        // CV-XXXXXX — mốc đánh dấu việc, dán vào chat Zalo
            $table->text('note')->nullable();            // ghi chú việc (optional)
            $table->string('status', 16)->default('pending')->index(); // pending | done
            $table->string('created_via', 16)->nullable(); // telegram | web
            $table->string('created_by', 64)->nullable();  // telegram chat id / admin
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_tasks');
    }
};
