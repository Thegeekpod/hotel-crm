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
        if (Schema::hasTable('pos_orders') && !Schema::hasColumn('pos_orders', 'customer_phone')) {
            Schema::table('pos_orders', function (Blueprint $table) {
                $table->string('customer_phone')->nullable()->after('customer_name');
            });
        }

        if (Schema::hasTable('room_service_orders') && !Schema::hasColumn('room_service_orders', 'table_number')) {
            Schema::table('room_service_orders', function (Blueprint $table) {
                $table->string('table_number')->nullable()->after('room_number');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pos_orders') && Schema::hasColumn('pos_orders', 'customer_phone')) {
            Schema::table('pos_orders', function (Blueprint $table) {
                $table->dropColumn('customer_phone');
            });
        }

        if (Schema::hasTable('room_service_orders') && Schema::hasColumn('room_service_orders', 'table_number')) {
            Schema::table('room_service_orders', function (Blueprint $table) {
                $table->dropColumn('table_number');
            });
        }
    }
};

