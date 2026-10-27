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
            if (!Schema::hasColumn('store_movements', 'pos_item_id')) {
                $table->foreignId('pos_item_id')->nullable()->after('store_item_id')->constrained('pos_items')->nullOnDelete();
            }
            // Make store_item_id nullable if it isn't
            $table->unsignedBigInteger('store_item_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_movements', function (Blueprint $table) {
            if (Schema::hasColumn('store_movements', 'pos_item_id')) {
                $table->dropForeign(['pos_item_id']);
                $table->dropColumn('pos_item_id');
            }
        });
    }
};
