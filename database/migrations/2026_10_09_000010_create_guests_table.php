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
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->string('reserve_id')->nullable()->index();
            $table->foreignId('registration_type_id')->nullable()->constrained('registration_types')->nullOnDelete();
            $table->foreignId('reservation_mode_id')->nullable()->constrained('reservation_modes')->nullOnDelete();
            
            // Corporate / Company
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->string('new_company_name')->nullable();
            $table->string('new_company_address')->nullable();
            $table->string('new_company_gstin')->nullable();
            $table->string('new_company_phone')->nullable();
            
            // Dates & Times
            $table->date('reserve_date')->nullable();
            $table->time('reserve_time')->nullable();
            
            // Guest Personal Details
            $table->foreignId('title_id')->nullable()->constrained('titles')->nullOnDelete();
            $table->string('guest_name');
            $table->text('guest_address')->nullable();
            $table->foreignId('nationality_id')->nullable()->constrained('nationalities')->nullOnDelete();
            $table->string('city')->nullable();
            $table->string('mobile')->nullable()->index();
            $table->string('email')->nullable();
            $table->date('dob')->nullable();
            $table->date('anniversary')->nullable();
            $table->string('status')->default('Confirmed'); // Confirmed, Arrived, Stay Over, Checked Out, Waitlisted, Tentative
            
            // Privilege Card
            $table->boolean('has_privilege_card')->default(false);
            $table->string('privilege_card_no')->nullable();
            
            // Room Assignment
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            
            // Identification
            $table->foreignId('id_card_type_id')->nullable()->constrained('id_card_types')->nullOnDelete();
            $table->string('id_card_number')->nullable();
            
            // Payment & Folio
            $table->foreignId('payment_mode_id')->nullable()->constrained('payment_modes')->nullOnDelete();
            $table->decimal('advance_amount', 12, 2)->default(0.00);
            $table->text('payment_remarks')->nullable();
            $table->string('folio_number')->nullable()->index();
            $table->decimal('balance', 12, 2)->default(0.00);
            
            // Multiple guests linkage in single reservation
            $table->foreignId('primary_guest_id')->nullable()->constrained('guests')->cascadeOnDelete();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
