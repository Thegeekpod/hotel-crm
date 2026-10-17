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
        Schema::table('guests', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['registration_type_id']);
            $table->dropForeign(['reservation_mode_id']);
            $table->dropForeign(['room_id']);
            $table->dropForeign(['payment_mode_id']);
            $table->dropForeign(['plan_id']);
            $table->dropForeign(['gst_percentage_id']);
            $table->dropForeign(['primary_guest_id']);

            // Drop booking & payment columns from guests table
            $table->dropColumn([
                'reserve_id',
                'registration_type_id',
                'reservation_mode_id',
                'reserve_date',
                'reserve_time',
                'checkout_date',
                'checkout_time',
                'total_nights',
                'room_rate',
                'total_amount',
                'status',
                'room_id',
                'payment_mode_id',
                'plan_id',
                'discount_percentage',
                'discount_amount',
                'gst_type',
                'gst_percentage_id',
                'gst_percentage',
                'gst_amount',
                'payable_amount',
                'advance_amount',
                'payment_remarks',
                'folio_number',
                'balance',
                'primary_guest_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->string('reserve_id')->nullable()->index();
            $table->foreignId('registration_type_id')->nullable()->constrained('registration_types')->nullOnDelete();
            $table->foreignId('reservation_mode_id')->nullable()->constrained('reservation_modes')->nullOnDelete();
            $table->date('reserve_date')->nullable();
            $table->time('reserve_time')->nullable();
            $table->date('checkout_date')->nullable();
            $table->time('checkout_time')->nullable();
            $table->integer('total_nights')->default(1);
            $table->decimal('room_rate', 12, 2)->default(0.00);
            $table->decimal('total_amount', 12, 2)->default(0.00);
            $table->string('status')->default('Confirmed');
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->foreignId('payment_mode_id')->nullable()->constrained('payment_modes')->nullOnDelete();
            $table->foreignId('plan_id')->nullable()->constrained('plans')->nullOnDelete();
            $table->decimal('discount_percentage', 5, 2)->default(0.00);
            $table->decimal('discount_amount', 12, 2)->default(0.00);
            $table->string('gst_type')->default('exclusive');
            $table->foreignId('gst_percentage_id')->nullable()->constrained('gst_percentages')->nullOnDelete();
            $table->decimal('gst_percentage', 5, 2)->default(0.00);
            $table->decimal('gst_amount', 12, 2)->default(0.00);
            $table->decimal('payable_amount', 12, 2)->default(0.00);
            $table->decimal('advance_amount', 12, 2)->default(0.00);
            $table->text('payment_remarks')->nullable();
            $table->string('folio_number')->nullable()->index();
            $table->decimal('balance', 12, 2)->default(0.00);
            $table->foreignId('primary_guest_id')->nullable()->constrained('guests')->cascadeOnDelete();
        });
    }
};
