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
        Schema::create('potential_suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_code')->unique();
            $table->string('supplier_name');
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('website')->nullable();
            $table->text('notes')->nullable()->comment('Ghi chú về lý do tiềm năng');
            $table->text('reason_potential')->nullable()->comment('Lý do được coi là tiềm năng');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium')->comment('Mức độ ưu tiên');
            $table->date('expected_cooperation_date')->nullable()->comment('Ngày dự kiến hợp tác');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('potential_suppliers');
    }
};
