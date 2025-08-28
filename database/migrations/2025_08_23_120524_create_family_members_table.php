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
        Schema::create('family_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('family_account_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('member_name')->nullable(); // Can be derived from customer or entered manually
            $table->string('member_email');
            $table->string('member_role')->default('member'); // e.g., member, admin
            $table->string('status')->default('active')->index(); // e.g., active, inactive, removed
            $table->json('permissions')->nullable();
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamp('removed_at')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('member_notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->unsignedBigInteger('added_by')->nullable();
            $table->unsignedBigInteger('removed_by')->nullable();
            $table->timestamps();

            // Custom named foreign keys to avoid conflicts
            $table->foreign('family_account_id', 'fk_new_family_members_family_account_id')->references('id')->on('family_accounts')->onDelete('cascade');
            $table->foreign('customer_id', 'fk_new_family_members_customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->foreign('added_by', 'fk_new_family_members_added_by')->references('id')->on('admins')->onDelete('set null');
            $table->foreign('removed_by', 'fk_new_family_members_removed_by')->references('id')->on('admins')->onDelete('set null');

            $table->unique(['family_account_id', 'member_email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_members');
    }
};
