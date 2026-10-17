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
        Schema::create('settlements', function (Blueprint $table) {
            $table->id();
            $table->string('settlement_number')->nullable()->index();
            $table->string('folio_number')->nullable()->index();
            
            // Relational linkage to Guest & Booking
            $table->foreignId('guest_id')->constrained('guests')->cascadeOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('payment_mode_id')->nullable()->constrained('payment_modes')->nullOnDelete();
            
            // Settlement Financial Details
            $table->decimal('total_room_charges', 12, 2)->default(0.00);
            $table->decimal('total_extra_charges', 12, 2)->default(0.00);
            $table->decimal('discount_amount', 12, 2)->default(0.00);
            $table->decimal('tax_amount', 12, 2)->default(0.00);
            $table->decimal('payable_amount', 12, 2)->default(0.00);
            $table->decimal('advance_adjusted', 12, 2)->default(0.00);
            $table->decimal('paid_amount', 12, 2)->default(0.00);
            $table->decimal('refund_amount', 12, 2)->default(0.00);
            $table->decimal('balance_amount', 12, 2)->default(0.00);
            
            // Settlement Metadata
            $table->date('settlement_date')->nullable()->index();
            $table->time('settlement_time')->nullable();
            $table->string('status')->default('Settled'); // Settled, Partial, Refunded, Cancelled
            $table->text('remarks')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settlements');
    }
};
