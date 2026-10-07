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
        // 1. Update room_categories table: drop code, bedding_config, pax_capacity if they exist
        Schema::table('room_categories', function (Blueprint $table) {
            $dropCols = [];
            if (Schema::hasColumn('room_categories', 'code')) {
                $dropCols[] = 'code';
            }
            if (Schema::hasColumn('room_categories', 'bedding_config')) {
                $dropCols[] = 'bedding_config';
            }
            if (Schema::hasColumn('room_categories', 'pax_capacity')) {
                $dropCols[] = 'pax_capacity';
            }
            if (!empty($dropCols)) {
                $table->dropColumn($dropCols);
            }
        });

        // 2. Update floors table: change rooms column to integer
        // First convert non-numeric string data to 0
        try {
            DB::statement("UPDATE `floors` SET `rooms` = '0' WHERE `rooms` IS NULL OR `rooms` NOT REGEXP '^[0-9]+$'");
        } catch (\Throwable $e) {
            // ignore if regex unsupported
        }

        Schema::table('floors', function (Blueprint $table) {
            if (Schema::hasColumn('floors', 'rooms')) {
                $table->unsignedInteger('rooms')->default(0)->change();
            } else {
                $table->unsignedInteger('rooms')->default(0);
            }
        });

        // 3. Update bedding_configs table: add max_adults, max_children, max_total
        Schema::table('bedding_configs', function (Blueprint $table) {
            if (!Schema::hasColumn('bedding_configs', 'max_adults')) {
                $table->unsignedSmallInteger('max_adults')->default(2)->after('name');
            }
            if (!Schema::hasColumn('bedding_configs', 'max_children')) {
                $table->unsignedSmallInteger('max_children')->default(0)->after('max_adults');
            }
            if (!Schema::hasColumn('bedding_configs', 'max_total')) {
                $table->unsignedSmallInteger('max_total')->default(2)->after('max_children');
            }
        });

        // 4. Update rooms table: drop foreign key to pax_capacities if exists
        Schema::table('rooms', function (Blueprint $table) {
            if (Schema::hasColumn('rooms', 'pax_capacity_id')) {
                try {
                    $table->dropForeign(['pax_capacity_id']);
                } catch (\Throwable $e) {}
                $table->dropColumn('pax_capacity_id');
            }
            if (Schema::hasColumn('rooms', 'pax_capacity')) {
                $table->dropColumn('pax_capacity');
            }
        });

        // 5. Drop tables: pax_capacities, maintenance_reasons, maintenance_engineers
        Schema::dropIfExists('pax_capacities');
        Schema::dropIfExists('maintenance_reasons');
        Schema::dropIfExists('maintenance_engineers');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op rollback for master cleanup
    }
};
