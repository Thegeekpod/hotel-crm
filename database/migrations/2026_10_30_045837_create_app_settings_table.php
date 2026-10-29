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
        if (!Schema::hasTable('app_settings')) {
            Schema::create('app_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->timestamps();
            });

            // Seed initial default GST rates
            DB::table('app_settings')->insert([
                ['key' => 'default_gst_room', 'value' => '12.00', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'default_gst_pos', 'value' => '12.00', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'default_gst_extra_services', 'value' => '5.00', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'default_gst_banquet', 'value' => '5.00', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
