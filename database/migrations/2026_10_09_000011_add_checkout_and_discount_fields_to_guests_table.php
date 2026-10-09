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
            $table->date('checkout_date')->nullable()->after('reserve_time');
            $table->time('checkout_time')->nullable()->after('checkout_date');
            $table->integer('total_nights')->default(1)->after('checkout_time');
            $table->decimal('room_rate', 12, 2)->default(0.00)->after('total_nights');
            $table->decimal('total_amount', 12, 2)->default(0.00)->after('room_rate');
            $table->foreignId('discount_id')->nullable()->after('payment_mode_id')->constrained('discounts')->nullOnDelete();
            $table->decimal('discount_percentage', 5, 2)->default(0.00)->after('discount_id');
            $table->decimal('discount_amount', 12, 2)->default(0.00)->after('discount_percentage');
            $table->decimal('payable_amount', 12, 2)->default(0.00)->after('discount_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropForeign(['discount_id']);
            $table->dropColumn([
                'checkout_date',
                'checkout_time',
                'total_nights',
                'room_rate',
                'total_amount',
                'discount_id',
                'discount_percentage',
                'discount_amount',
                'payable_amount',
            ]);
        });
    }
};
