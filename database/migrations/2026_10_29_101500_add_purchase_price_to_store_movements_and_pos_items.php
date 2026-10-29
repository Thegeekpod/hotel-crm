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
        Schema::table('store_movements', function (Blueprint $table) {
            if (!Schema::hasColumn('store_movements', 'total_cost')) {
                $table->decimal('total_cost', 10, 2)->nullable()->after('unit_cost');
            }
        });

        Schema::table('pos_items', function (Blueprint $table) {
            if (!Schema::hasColumn('pos_items', 'last_purchase_price')) {
                $table->decimal('last_purchase_price', 10, 2)->nullable()->after('price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_movements', function (Blueprint $table) {
            if (Schema::hasColumn('store_movements', 'total_cost')) {
                $table->dropColumn('total_cost');
            }
        });

        Schema::table('pos_items', function (Blueprint $table) {
            if (Schema::hasColumn('pos_items', 'last_purchase_price')) {
                $table->dropColumn('last_purchase_price');
            }
        });
    }
};

