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
        // 1. Companies Table
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'food_discount')) {
                $table->string('food_discount')->nullable()->default('percentage')->after('discount_value');
            }
            if (!Schema::hasColumn('companies', 'food_value')) {
                $table->decimal('food_value', 10, 2)->default(0.00)->after('food_discount');
            }
        });

        // 2. Bookings Table
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'food_discount')) {
                $table->string('food_discount')->nullable()->default('percentage')->after('discount_amount');
            }
            if (!Schema::hasColumn('bookings', 'food_value')) {
                $table->decimal('food_value', 10, 2)->default(0.00)->after('food_discount');
            }
        });

        // 3. POS Orders Table
        Schema::table('pos_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('pos_orders', 'discount_type')) {
                $table->string('discount_type')->nullable()->after('subtotal');
            }
            if (!Schema::hasColumn('pos_orders', 'discount_percentage')) {
                $table->decimal('discount_percentage', 5, 2)->default(0.00)->after('discount_type');
            }
            if (!Schema::hasColumn('pos_orders', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0.00)->after('discount_percentage');
            }
        });

        // 4. Room Service Orders Table
        Schema::table('room_service_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('room_service_orders', 'discount_type')) {
                $table->string('discount_type')->nullable()->after('subtotal');
            }
            if (!Schema::hasColumn('room_service_orders', 'discount_percentage')) {
                $table->decimal('discount_percentage', 5, 2)->default(0.00)->after('discount_type');
            }
            if (!Schema::hasColumn('room_service_orders', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0.00)->after('discount_percentage');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'food_value')) {
                $table->dropColumn('food_value');
            }
            if (Schema::hasColumn('companies', 'food_discount')) {
                $table->dropColumn('food_discount');
            }
        });

        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'food_value')) {
                $table->dropColumn('food_value');
            }
            if (Schema::hasColumn('bookings', 'food_discount')) {
                $table->dropColumn('food_discount');
            }
        });

        Schema::table('pos_orders', function (Blueprint $table) {
            if (Schema::hasColumn('pos_orders', 'discount_amount')) {
                $table->dropColumn('discount_amount');
            }
            if (Schema::hasColumn('pos_orders', 'discount_percentage')) {
                $table->dropColumn('discount_percentage');
            }
            if (Schema::hasColumn('pos_orders', 'discount_type')) {
                $table->dropColumn('discount_type');
            }
        });

        Schema::table('room_service_orders', function (Blueprint $table) {
            if (Schema::hasColumn('room_service_orders', 'discount_amount')) {
                $table->dropColumn('discount_amount');
            }
            if (Schema::hasColumn('room_service_orders', 'discount_percentage')) {
                $table->dropColumn('discount_percentage');
            }
            if (Schema::hasColumn('room_service_orders', 'discount_type')) {
                $table->dropColumn('discount_type');
            }
        });
    }
};
