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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('reserve_id')->nullable()->index();
            $table->string('folio_number')->nullable()->index();
            
            // Relational linkage to Guest & Room
            $table->foreignId('guest_id')->constrained('guests')->cascadeOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            
            // Configuration & Classification
            $table->foreignId('registration_type_id')->nullable()->constrained('registration_types')->nullOnDelete();
            $table->foreignId('reservation_mode_id')->nullable()->constrained('reservation_modes')->nullOnDelete();
            
            // Corporate / Company link
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->string('new_company_name')->nullable();
            $table->string('new_company_address')->nullable();
            $table->string('new_company_gstin')->nullable();
            $table->string('new_company_phone')->nullable();
            
            // Package / Plan
            $table->foreignId('plan_id')->nullable()->constrained('plans')->nullOnDelete();
            
            // Check-in and Check-out stay dates & times
            $table->date('checkin_date')->nullable()->index();
            $table->time('checkin_time')->nullable();
            $table->date('reserve_date')->nullable()->index(); // Kept for backwards-compatible reservation querying
            $table->time('reserve_time')->nullable();
            $table->date('checkout_date')->nullable()->index();
            $table->time('checkout_time')->nullable();
            $table->integer('total_nights')->default(1);
            
            // Pricing & Billing
            $table->decimal('room_rate', 12, 2)->default(0.00);
            $table->decimal('total_amount', 12, 2)->default(0.00);
            $table->decimal('discount_percentage', 5, 2)->default(0.00);
            $table->decimal('discount_amount', 12, 2)->default(0.00);
            
            // GST
            $table->string('gst_type')->default('exclusive');
            $table->foreignId('gst_percentage_id')->nullable()->constrained('gst_percentages')->nullOnDelete();
            $table->decimal('gst_percentage', 5, 2)->default(0.00);
            $table->decimal('gst_amount', 12, 2)->default(0.00);
            
            // Payment Totals & Balance
            $table->decimal('payable_amount', 12, 2)->default(0.00);
            $table->decimal('advance_amount', 12, 2)->default(0.00);
            $table->decimal('balance', 12, 2)->default(0.00);
            
            // Booking Status: Confirmed, Arrived, Checked In, Stay Over, Checked Out, Cancelled, Waitlisted, Tentative
            $table->string('status')->default('Confirmed')->index();
            
            // Linkage for multiple rooms/guests under one reservation dossier
            $table->foreignId('primary_booking_id')->nullable()->constrained('bookings')->cascadeOnDelete();
            
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
