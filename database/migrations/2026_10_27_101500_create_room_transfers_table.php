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
        Schema::create('room_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('primary_booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->foreignId('from_room_id')->constrained('rooms')->cascadeOnDelete();
            $table->foreignId('to_room_id')->constrained('rooms')->cascadeOnDelete();
            $table->date('transfer_date')->index();
            $table->time('transfer_time')->nullable();
            $table->decimal('old_room_rate', 12, 2)->default(0.00);
            $table->decimal('new_room_rate', 12, 2)->default(0.00);
            $table->integer('nights_in_old_room')->default(1);
            $table->integer('remaining_nights')->default(1);
            $table->decimal('tariff_difference', 12, 2)->default(0.00);
            $table->foreignId('transferred_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('transferred_guests')->nullable();
            $table->string('reason')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_transfers');
    }
};
