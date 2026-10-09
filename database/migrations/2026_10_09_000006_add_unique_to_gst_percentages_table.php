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
        Schema::table('gst_percentages', function (Blueprint $table) {
            $table->unique('gst_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gst_percentages', function (Blueprint $table) {
            $table->dropUnique(['gst_percentage']);
        });
    }
};
