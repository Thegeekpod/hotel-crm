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
        if (Schema::hasTable('pos_orders')) {
            Schema::table('pos_orders', function (Blueprint $table) {
                if (!Schema::hasColumn('pos_orders', 'is_kot')) {
                    $table->boolean('is_kot')->default(false)->after('order_type');
                }
                if (!Schema::hasColumn('pos_orders', 'kot_number')) {
                    $table->string('kot_number')->nullable()->after('is_kot');
                }
                if (!Schema::hasColumn('pos_orders', 'kot_sequence')) {
                    $table->integer('kot_sequence')->default(1)->after('kot_number');
                }
                if (!Schema::hasColumn('pos_orders', 'table_id')) {
                    $table->foreignId('table_id')->nullable()->after('table_number')->constrained('table_manages')->nullOnDelete();
                }
                if (!Schema::hasColumn('pos_orders', 'parent_order_id')) {
                    $table->foreignId('parent_order_id')->nullable()->after('room_service_order_id')->constrained('pos_orders')->nullOnDelete();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pos_orders')) {
            Schema::table('pos_orders', function (Blueprint $table) {
                if (Schema::hasColumn('pos_orders', 'parent_order_id')) {
                    $table->dropForeign(['parent_order_id']);
                    $table->dropColumn('parent_order_id');
                }
                if (Schema::hasColumn('pos_orders', 'table_id')) {
                    $table->dropForeign(['table_id']);
                    $table->dropColumn('table_id');
                }
                if (Schema::hasColumn('pos_orders', 'kot_sequence')) {
                    $table->dropColumn('kot_sequence');
                }
                if (Schema::hasColumn('pos_orders', 'kot_number')) {
                    $table->dropColumn('kot_number');
                }
                if (Schema::hasColumn('pos_orders', 'is_kot')) {
                    $table->dropColumn('is_kot');
                }
            });
        }
    }
};

