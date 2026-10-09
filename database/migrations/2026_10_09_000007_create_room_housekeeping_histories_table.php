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
        if (!Schema::hasTable('room_housekeeping_histories')) {
            Schema::create('room_housekeeping_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
                $table->foreignId('housekeeping_status_id')->nullable()->constrained('housekeeping_states')->nullOnDelete();
                $table->dateTime('start_time')->nullable();
                $table->dateTime('completion_time')->nullable();
                $table->string('status')->default('pending'); // pending, complete
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_housekeeping_histories');
    }
};
