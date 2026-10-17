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
        Schema::create('checkouts', function (Blueprint $table) {
            $table->id();
            
            // Relational linkage
            $table->foreignId('guest_id')->constrained('guests')->cascadeOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->foreignId('settlement_id')->nullable()->constrained('settlements')->nullOnDelete();
            
            // Checkout Timings
            $table->date('checkout_date')->nullable()->index();
            $table->time('checkout_time')->nullable();
            $table->dateTime('actual_checkout_at')->nullable();
            $table->integer('total_nights')->default(1);
            
            // Charges Breakdown
            $table->decimal('room_charges', 12, 2)->default(0.00);
            $table->decimal('extra_charges', 12, 2)->default(0.00);
            $table->decimal('late_checkout_charge', 12, 2)->default(0.00);
            $table->decimal('discount_amount', 12, 2)->default(0.00);
            $table->decimal('tax_amount', 12, 2)->default(0.00);
            $table->decimal('grand_total', 12, 2)->default(0.00);
            $table->decimal('paid_amount', 12, 2)->default(0.00);
            $table->decimal('balance_amount', 12, 2)->default(0.00);
            
            // Checkout Status
            $table->string('status')->default('Completed'); // Completed, Pending Settlement
            $table->text('remarks')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkouts');
    }
};
