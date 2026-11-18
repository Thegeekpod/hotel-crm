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
        if (Schema::hasTable('pos_orders') && !Schema::hasColumn('pos_orders', 'customer_gstin')) {
            Schema::table('pos_orders', function (Blueprint $table) {
                $table->string('customer_gstin')->nullable()->after('customer_phone');
            });
        }

        if (Schema::hasTable('room_service_orders') && !Schema::hasColumn('room_service_orders', 'customer_gstin')) {
            Schema::table('room_service_orders', function (Blueprint $table) {
                $table->string('customer_gstin')->nullable()->after('table_number');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pos_orders') && Schema::hasColumn('pos_orders', 'customer_gstin')) {
            Schema::table('pos_orders', function (Blueprint $table) {
                $table->dropColumn('customer_gstin');
            });
        }

        if (Schema::hasTable('room_service_orders') && Schema::hasColumn('room_service_orders', 'customer_gstin')) {
            Schema::table('room_service_orders', function (Blueprint $table) {
                $table->dropColumn('customer_gstin');
            });
        }
    }
};
