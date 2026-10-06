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
        Schema::create('bedding_configs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('dimensions')->nullable();
            $table->string('status')->default('Active');
            $table->timestamps();
        });

        Schema::create('pax_capacities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedSmallInteger('max_adults')->default(2);
            $table->unsignedSmallInteger('max_children')->default(0);
            $table->unsignedSmallInteger('max_total')->default(2);
            $table->string('status')->default('Active');
            $table->timestamps();
        });

        Schema::table('room_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('room_categories', 'bedding_config')) {
                $table->string('bedding_config')->nullable()->after('code');
            }
            if (!Schema::hasColumn('room_categories', 'pax_capacity')) {
                $table->string('pax_capacity')->nullable()->after('bedding_config');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_categories', function (Blueprint $table) {
            if (Schema::hasColumn('room_categories', 'bedding_config')) {
                $table->dropColumn('bedding_config');
            }
            if (Schema::hasColumn('room_categories', 'pax_capacity')) {
                $table->dropColumn('pax_capacity');
            }
        });

        Schema::dropIfExists('pax_capacities');
        Schema::dropIfExists('bedding_configs');
    }
};
