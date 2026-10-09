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
        if (Schema::hasTable('housekeeping_states') && Schema::hasColumn('housekeeping_states', 'code')) {
            Schema::table('housekeeping_states', function (Blueprint $table) {
                $table->dropColumn('code');
            });
        }

        if (Schema::hasTable('id_card_types') && Schema::hasColumn('id_card_types', 'code')) {
            Schema::table('id_card_types', function (Blueprint $table) {
                $table->dropColumn('code');
            });
        }

        if (Schema::hasTable('operational_statuses') && Schema::hasColumn('operational_statuses', 'code')) {
            Schema::table('operational_statuses', function (Blueprint $table) {
                $table->dropColumn('code');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('housekeeping_states') && !Schema::hasColumn('housekeeping_states', 'code')) {
            Schema::table('housekeeping_states', function (Blueprint $table) {
                $table->string('code')->nullable();
            });
        }

        if (Schema::hasTable('id_card_types') && !Schema::hasColumn('id_card_types', 'code')) {
            Schema::table('id_card_types', function (Blueprint $table) {
                $table->string('code')->nullable();
            });
        }

        if (Schema::hasTable('operational_statuses') && !Schema::hasColumn('operational_statuses', 'code')) {
            Schema::table('operational_statuses', function (Blueprint $table) {
                $table->string('code')->nullable();
            });
        }
    }
};
