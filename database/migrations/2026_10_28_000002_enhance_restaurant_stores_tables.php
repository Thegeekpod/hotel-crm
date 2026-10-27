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
        // 1. Add restaurant yield & POS linkage to store_items
        Schema::table('store_items', function (Blueprint $table) {
            if (!Schema::hasColumn('store_items', 'plates_per_unit')) {
                $table->decimal('plates_per_unit', 8, 2)->default(1.00)->after('unit');
            }
            if (!Schema::hasColumn('store_items', 'pos_item_id')) {
                $table->foreignId('pos_item_id')->nullable()->after('store_category_id')->constrained('pos_items')->nullOnDelete();
            }
        });

        // 2. Recipe Ingredient Mapping: Connects POS Dishes to Restaurant Store Ingredients
        if (!Schema::hasTable('pos_item_ingredients')) {
            Schema::create('pos_item_ingredients', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pos_item_id')->constrained('pos_items')->cascadeOnDelete();
                $table->foreignId('store_item_id')->constrained('store_items')->cascadeOnDelete();
                $table->decimal('quantity_per_plate', 8, 3)->default(0.250); // e.g. 0.250 kg per plate
                $table->string('unit')->default('Kg'); // Kg, Liters, Pcs, Bottles, Grams
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_item_ingredients');

        Schema::table('store_items', function (Blueprint $table) {
            if (Schema::hasColumn('store_items', 'pos_item_id')) {
                $table->dropForeign(['pos_item_id']);
                $table->dropColumn('pos_item_id');
            }
            if (Schema::hasColumn('store_items', 'plates_per_unit')) {
                $table->dropColumn('plates_per_unit');
            }
        });
    }
};
