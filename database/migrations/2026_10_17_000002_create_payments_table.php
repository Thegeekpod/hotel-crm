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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            
            // Linkage to Guest & Booking
            $table->foreignId('guest_id')->constrained('guests')->cascadeOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->cascadeOnDelete();
            
            // Payment method / mode
            $table->foreignId('payment_mode_id')->nullable()->constrained('payment_modes')->nullOnDelete();
            
            // Payment details matching current guests table structure
            $table->decimal('amount', 12, 2)->default(0.00);
            $table->decimal('advance_amount', 12, 2)->default(0.00);
            $table->decimal('balance', 12, 2)->default(0.00);
            $table->string('folio_number')->nullable()->index();
            $table->text('payment_remarks')->nullable();
            
            // Transaction meta
            $table->date('payment_date')->nullable()->index();
            $table->time('payment_time')->nullable();
            $table->string('payment_type')->default('Advance'); // Advance, Running, Settlement, Refund
            $table->string('transaction_id')->nullable()->index();
            $table->string('status')->default('Success'); // Success, Pending, Failed, Refunded
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
