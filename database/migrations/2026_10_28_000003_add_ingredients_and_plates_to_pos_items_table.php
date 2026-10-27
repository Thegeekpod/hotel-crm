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
        Schema::table('pos_items', function (Blueprint $table) {
            if (!Schema::hasColumn('pos_items', 'ingredient_name')) {
                $table->string('ingredient_name')->nullable()->after('description');
            }
            if (!Schema::hasColumn('pos_items', 'raw_stock_qty')) {
                $table->decimal('raw_stock_qty', 10, 2)->default(0.00)->after('ingredient_name');
            }
            if (!Schema::hasColumn('pos_items', 'raw_stock_unit')) {
                $table->string('raw_stock_unit')->default('Kg')->after('raw_stock_qty'); // Kg, Liters, Pcs, Bottles
            }
            if (!Schema::hasColumn('pos_items', 'per_plate_qty')) {
                $table->decimal('per_plate_qty', 10, 2)->default(1.00)->after('raw_stock_unit'); // e.g. 250
            }
            if (!Schema::hasColumn('pos_items', 'per_plate_unit')) {
                $table->string('per_plate_unit')->default('gm')->after('per_plate_qty'); // gm, ml, pcs, bottle
            }
            if (!Schema::hasColumn('pos_items', 'available_plates')) {
                $table->integer('available_plates')->default(50)->after('per_plate_unit');
            }
            if (!Schema::hasColumn('pos_items', 'min_alert_plates')) {
                $table->integer('min_alert_plates')->default(10)->after('available_plates');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_items', function (Blueprint $table) {
            $table->dropColumn([
                'ingredient_name',
                'raw_stock_qty',
                'raw_stock_unit',
                'per_plate_qty',
                'per_plate_unit',
                'available_plates',
                'min_alert_plates'
            ]);
        });
    }
};
