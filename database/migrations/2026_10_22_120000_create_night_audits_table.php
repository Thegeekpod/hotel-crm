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
        Schema::create('night_audits', function (Blueprint $table) {
            $table->id();
            
            // Audit Meta
            $table->date('audit_date')->index();
            $table->dateTime('completed_at')->nullable();
            $table->foreignId('auditor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('auditor_name')->nullable();
            $table->string('status')->default('Completed')->index(); // Completed, In Progress, Closed
            
            // Financial Collections & Revenue (Ajker Dine Koto Ki Payments Elo)
            $table->decimal('total_payments', 12, 2)->default(0.00);
            $table->decimal('advance_payments', 12, 2)->default(0.00);
            $table->decimal('settlement_payments', 12, 2)->default(0.00);
            $table->decimal('cash_collection', 12, 2)->default(0.00);
            $table->decimal('upi_collection', 12, 2)->default(0.00);
            $table->decimal('card_collection', 12, 2)->default(0.00);
            $table->decimal('bank_collection', 12, 2)->default(0.00);
            $table->decimal('other_collection', 12, 2)->default(0.00);
            
            // Billing & Revenue (Rooms & Food / Plans)
            $table->decimal('room_revenue', 12, 2)->default(0.00);
            $table->decimal('food_revenue', 12, 2)->default(0.00); // Khabar / Meal Plan Revenue
            $table->decimal('extra_revenue', 12, 2)->default(0.00);
            $table->decimal('tax_amount', 12, 2)->default(0.00);
            $table->decimal('discount_amount', 12, 2)->default(0.00);
            $table->decimal('total_revenue', 12, 2)->default(0.00);
            
            // Rooms & Inventory Status Data (Rooms Data)
            $table->integer('total_rooms')->default(0);
            $table->integer('occupied_rooms')->default(0);
            $table->integer('vacant_rooms')->default(0);
            $table->integer('maintenance_rooms')->default(0);
            $table->integer('dirty_rooms')->default(0);
            $table->integer('clean_rooms')->default(0);
            $table->decimal('occupancy_rate', 5, 2)->default(0.00);
            $table->decimal('adr', 10, 2)->default(0.00);
            $table->decimal('revpar', 10, 2)->default(0.00);
            
            // Guest Movement Data (Guests Sob Kichu)
            $table->integer('arrivals_count')->default(0);
            $table->integer('departures_count')->default(0);
            $table->integer('inhouse_guests_count')->default(0);
            $table->integer('stay_over_count')->default(0);
            $table->integer('cancelled_count')->default(0);
            $table->integer('pending_arrivals')->default(0);
            $table->integer('pending_departures')->default(0);
            
            // Food / Dining / Meal Plan Breakdown (Khabar Breakdown)
            $table->integer('ep_plan_count')->default(0); // Room Only
            $table->integer('cp_plan_count')->default(0); // Breakfast
            $table->integer('map_plan_count')->default(0); // Half Board
            $table->integer('ap_plan_count')->default(0); // Full Board
            
            // Auditor Notes and Full Detailed Snapshot
            $table->text('remarks')->nullable();
            $table->json('audit_snapshot')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('night_audits');
    }
};
