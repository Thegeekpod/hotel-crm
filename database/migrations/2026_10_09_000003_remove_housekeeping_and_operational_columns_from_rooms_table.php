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
        if (Schema::hasTable('rooms')) {
            Schema::table('rooms', function (Blueprint $table) {
                // Drop foreign keys if they exist
                try {
                    $table->dropForeign(['operational_status_id']);
                } catch (\Throwable $e) {}

                try {
                    $table->dropForeign(['housekeeping_state_id']);
                } catch (\Throwable $e) {}

                // Drop columns
                $columns = [
                    'operational_status_id',
                    'housekeeping_state_id',
                    'housekeeping_status',
                    'housekeeping_state',
                    'operational_status'
                ];

                foreach ($columns as $col) {
                    if (Schema::hasColumn('rooms', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('rooms')) {
            Schema::table('rooms', function (Blueprint $table) {
                if (!Schema::hasColumn('rooms', 'operational_status_id')) {
                    $table->foreignId('operational_status_id')->nullable()->constrained('operational_statuses')->nullOnDelete();
                }
                if (!Schema::hasColumn('rooms', 'housekeeping_state_id')) {
                    $table->foreignId('housekeeping_state_id')->nullable()->constrained('housekeeping_states')->nullOnDelete();
                }
                if (!Schema::hasColumn('rooms', 'housekeeping_status')) {
                    $table->string('housekeeping_status')->nullable();
                }
            });
        }
    }
};
