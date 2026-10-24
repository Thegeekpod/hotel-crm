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
        // 1. Banquet Halls & Event Venues (Crystal Ballroom, Boardroom, Palm Terrace Lawn, etc.)
        Schema::create('banquet_halls', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('type')->default('Ballroom'); // Ballroom, Boardroom, Lawn, Conference Hall, Terrace
            $table->integer('capacity_pax')->default(50);
            $table->decimal('base_tariff', 10, 2)->default(0.00);
            $table->text('amenities')->nullable(); // Projector, Sound, Stage, Catering Setup
            $table->string('location')->nullable();
            $table->string('status')->default('Available'); // Available, Maintenance, Reserved
            $table->timestamps();
        });

        // 2. Service Catalog (Room Service Menu & Hotel Guest Services: Dining, Laundry, Spa, Extra Bed, Airport Transfer, etc.)
        Schema::create('hotel_services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('category')->default('Room Dining'); // Room Dining, Laundry & Dry Clean, Spa & Wellness, Concierge, Banquet Addon
            $table->decimal('price', 10, 2)->default(0.00);
            $table->string('unit')->default('Per Order'); // Per Order, Per Item, Per Hour, Per Trip, Per Day
            $table->text('description')->nullable();
            $table->string('status')->default('Active'); // Active, Inactive
            $table->timestamps();
        });

        // 3. Room Service & Amenity Orders (relate directly to Room & Active Booking Guest)
        Schema::create('room_service_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // ORD-2026-001
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->string('guest_name')->nullable();
            $table->string('room_number')->nullable();
            $table->decimal('subtotal', 10, 2)->default(0.00);
            $table->decimal('tax_amount', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->string('payment_status')->default('Pending'); // Charged to Room Folio, Paid Cash, Paid UPI/Card, Pending
            $table->string('order_status')->default('Received'); // Received, Preparing, Out for Delivery, Delivered, Cancelled
            $table->string('delivery_time')->nullable();
            $table->text('special_instructions')->nullable();
            $table->string('served_by')->nullable();
            $table->timestamps();
        });

        // 4. Room Service Order Line Items
        Schema::create('room_service_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_service_order_id')->constrained('room_service_orders')->cascadeOnDelete();
            $table->foreignId('hotel_service_id')->nullable()->constrained('hotel_services')->nullOnDelete();
            $table->string('item_name');
            $table->decimal('unit_price', 10, 2)->default(0.00);
            $table->integer('quantity')->default(1);
            $table->decimal('line_total', 10, 2)->default(0.00);
            $table->string('instructions')->nullable();
            $table->timestamps();
        });

        // 5. Banquet Hall Bookings & Function Sheets
        Schema::create('banquet_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_reference')->unique(); // BNQ-2026-001
            $table->foreignId('banquet_hall_id')->constrained('banquet_halls')->cascadeOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete(); // Optional room guest folio link
            $table->string('client_name');
            $table->string('company_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('event_name'); // Annual Gala, Wedding Reception, Conference, Birthday
            $table->date('event_date');
            $table->string('time_slot')->default('Evening (6:00 PM - 11:00 PM)'); // Morning, Afternoon, Evening, Full Day
            $table->integer('guaranteed_pax')->default(50);
            $table->decimal('hall_rental', 10, 2)->default(0.00);
            $table->decimal('catering_per_plate', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->decimal('advance_paid', 10, 2)->default(0.00);
            $table->decimal('balance_due', 10, 2)->default(0.00);
            $table->string('status')->default('Confirmed'); // Inquired, Tentative, Confirmed, Completed, Cancelled
            $table->text('function_sheet_notes')->nullable(); // Menu, AV setup, Seating style
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banquet_bookings');
        Schema::dropIfExists('room_service_order_items');
        Schema::dropIfExists('room_service_orders');
        Schema::dropIfExists('hotel_services');
        Schema::dropIfExists('banquet_halls');
    }
};
