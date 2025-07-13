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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('zalo')->nullable();
            $table->string('email')->nullable();
            $table->text('needs')->nullable(); // Nhu cầu của khách hàng
            $table->enum('status', ['new', 'contacted', 'interested', 'not_interested', 'converted'])->default('new');
            $table->text('notes')->nullable();
            $table->string('source')->nullable(); // Nguồn khách hàng (Facebook, Website, etc.)
            $table->decimal('potential_value', 10, 2)->nullable(); // Giá trị tiềm năng
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
