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
        Schema::table('bedding_configs', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('bedding_configs', 'code')) {
                $columnsToDrop[] = 'code';
            }
            if (Schema::hasColumn('bedding_configs', 'dimensions')) {
                $columnsToDrop[] = 'dimensions';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bedding_configs', function (Blueprint $table) {
            if (!Schema::hasColumn('bedding_configs', 'code')) {
                $table->string('code')->nullable();
            }
            if (!Schema::hasColumn('bedding_configs', 'dimensions')) {
                $table->string('dimensions')->nullable();
            }
        });
    }
};
