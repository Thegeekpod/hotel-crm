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
        // 1. Remove discount_id foreign key and column from guests table
        if (Schema::hasTable('guests') && Schema::hasColumn('guests', 'discount_id')) {
            Schema::table('guests', function (Blueprint $table) {
                try {
                    $table->dropForeign(['discount_id']);
                } catch (\Throwable $e) {
                    // Ignore if foreign key did not exist
                }
                $table->dropColumn('discount_id');
            });
        }

        // 2. Drop discounts table
        Schema::dropIfExists('discounts');

        // 3. Add discount_type and discount_value to companies table
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'discount_type')) {
                $table->string('discount_type')->default('percentage')->after('phone');
            }
            if (!Schema::hasColumn('companies', 'discount_value')) {
                $table->decimal('discount_value', 12, 2)->default(0.00)->after('discount_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'discount_value')) {
                $table->dropColumn('discount_value');
            }
            if (Schema::hasColumn('companies', 'discount_type')) {
                $table->dropColumn('discount_type');
            }
        });

        if (!Schema::hasTable('discounts')) {
            Schema::create('discounts', function (Blueprint $table) {
                $table->id();
                $table->decimal('discount_percentage', 5, 2)->unique();
                $table->string('status')->default('Active');
                $table->timestamps();
            });
        }

        if (Schema::hasTable('guests') && !Schema::hasColumn('guests', 'discount_id')) {
            Schema::table('guests', function (Blueprint $table) {
                $table->foreignId('discount_id')->nullable()->after('payment_mode_id')->constrained('discounts')->nullOnDelete();
            });
        }
    }
};
