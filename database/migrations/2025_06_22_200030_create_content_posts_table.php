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
        Schema::create('content_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('image_path')->nullable();
            $table->string('image_url')->nullable(); // For external image links
            $table->json('target_groups'); // Array of group names/IDs
            $table->timestamp('scheduled_at');
            $table->enum('status', ['scheduled', 'posted', 'cancelled'])->default('scheduled');
            $table->boolean('notification_sent')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_posts');
    }
};
