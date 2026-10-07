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
        Schema::table('rooms', function (Blueprint $table) {
            if (!Schema::hasColumn('rooms', 'status')) {
                $table->string('status')->default('Active')->after('rate');
            }
            if (!Schema::hasColumn('rooms', 'operational_status_id')) {
                $table->foreignId('operational_status_id')->nullable()->after('status')->constrained('operational_statuses')->nullOnDelete();
            }
            if (!Schema::hasColumn('rooms', 'housekeeping_state_id')) {
                $table->foreignId('housekeeping_state_id')->nullable()->after('operational_status_id')->constrained('housekeeping_states')->nullOnDelete();
            }
            if (!Schema::hasColumn('rooms', 'housekeeping_status')) {
                $table->string('housekeeping_status')->nullable()->after('housekeeping_state_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            if (Schema::hasColumn('rooms', 'operational_status_id')) {
                try {
                    $table->dropForeign(['operational_status_id']);
                } catch (\Throwable $e) {}
                $table->dropColumn('operational_status_id');
            }
            if (Schema::hasColumn('rooms', 'housekeeping_state_id')) {
                try {
                    $table->dropForeign(['housekeeping_state_id']);
                } catch (\Throwable $e) {}
                $table->dropColumn('housekeeping_state_id');
            }
            if (Schema::hasColumn('rooms', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('rooms', 'housekeeping_status')) {
                $table->dropColumn('housekeeping_status');
            }
        });
    }
};
