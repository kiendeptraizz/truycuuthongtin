<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // Thêm các cột mới nếu chưa có
            if (!Schema::hasColumn('leads', 'priority')) {
                $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->after('status');
            }

            if (!Schema::hasColumn('leads', 'requirements')) {
                $table->text('requirements')->nullable()->after('priority');
            }

            if (!Schema::hasColumn('leads', 'estimated_value')) {
                $table->decimal('estimated_value', 15, 2)->nullable()->after('requirements');
            }

            if (!Schema::hasColumn('leads', 'service_package_id')) {
                $table->unsignedBigInteger('service_package_id')->nullable()->after('estimated_value');
            }

            if (!Schema::hasColumn('leads', 'assigned_to')) {
                $table->unsignedBigInteger('assigned_to')->nullable()->after('service_package_id');
            }

            if (!Schema::hasColumn('leads', 'last_contact_at')) {
                $table->timestamp('last_contact_at')->nullable()->after('assigned_to');
            }

            if (!Schema::hasColumn('leads', 'next_follow_up_at')) {
                $table->timestamp('next_follow_up_at')->nullable()->after('last_contact_at');
            }

            if (!Schema::hasColumn('leads', 'converted_at')) {
                $table->timestamp('converted_at')->nullable()->after('next_follow_up_at');
            }

            if (!Schema::hasColumn('leads', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable()->after('converted_at');
            }
        });

        // Cập nhật enum status nếu cần
        try {
            DB::statement("ALTER TABLE leads MODIFY COLUMN status ENUM('new', 'contacted', 'interested', 'quoted', 'negotiating', 'won', 'lost', 'follow_up') DEFAULT 'new'");
        } catch (Exception $e) {
            // Ignore if already updated
        }

        // Thêm foreign keys
        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'service_package_id') && Schema::hasTable('service_packages')) {
                try {
                    $table->foreign('service_package_id')->references('id')->on('service_packages')->onDelete('set null');
                } catch (Exception $e) {
                    // Ignore if already exists
                }
            }

            if (Schema::hasColumn('leads', 'assigned_to') && Schema::hasTable('users')) {
                try {
                    $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
                } catch (Exception $e) {
                    // Ignore if already exists
                }
            }

            if (Schema::hasColumn('leads', 'customer_id') && Schema::hasTable('customers')) {
                try {
                    $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
                } catch (Exception $e) {
                    // Ignore if already exists
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // Drop foreign keys
            try {
                $table->dropForeign(['service_package_id']);
                $table->dropForeign(['assigned_to']);
                $table->dropForeign(['customer_id']);
            } catch (Exception $e) {
                // Ignore if not exists
            }

            // Drop new columns
            $columnsToCheck = [
                'priority',
                'requirements',
                'estimated_value',
                'service_package_id',
                'assigned_to',
                'last_contact_at',
                'next_follow_up_at',
                'converted_at',
                'customer_id'
            ];

            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('leads', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
