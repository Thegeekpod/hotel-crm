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
            if (Schema::hasColumn('rooms', 'housekeeping_status')) {
                $table->string('housekeeping_status')->nullable()->default(null)->change();
            }
            if (Schema::hasColumn('rooms', 'status')) {
                $table->string('status')->nullable()->default('Active')->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('housekeeping_status')->nullable()->default('Cleaned')->change();
            $table->string('status')->default('Active')->change();
        });
    }
};
