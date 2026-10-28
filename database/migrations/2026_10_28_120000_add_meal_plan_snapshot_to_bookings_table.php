<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Booking;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('plan_name')->nullable()->after('plan_id');
            $table->decimal('plan_original_price', 12, 2)->default(0.00)->after('plan_name');
            $table->decimal('plan_price', 12, 2)->default(0.00)->after('plan_original_price');
            $table->decimal('plan_discount_amount', 12, 2)->default(0.00)->after('plan_price');
        });

        // Backfill existing bookings with permanent historical meal snapshot data
        $bookings = Booking::with(['plan', 'company'])->get();
        foreach ($bookings as $b) {
            $planModel = $b->plan;
            $planName = $planModel?->plan_name ?: 'None';
            $origPrice = (float)($planModel?->price ?? 0);
            $planPrice = $origPrice;
            $discAmt = 0.0;

            $company = $b->company;
            if ($company && !empty($company->meal_discounts) && is_array($company->meal_discounts)) {
                foreach ($company->meal_discounts as $md) {
                    $mPlanId = $md['meal_plan_id'] ?? $md['plan_id'] ?? null;
                    if ($mPlanId !== null && (int)$mPlanId === (int)$b->plan_id) {
                        $mType = $md['discount_type'] ?? 'percentage';
                        $mVal = (float)($md['discount_value'] ?? 0);
                        if ($mType === 'amount') {
                            $discAmt = min($origPrice, $mVal);
                            $planPrice = max(0, $origPrice - $discAmt);
                        } else {
                            $discAmt = round($origPrice * ($mVal / 100), 2);
                            $planPrice = max(0, $origPrice - $discAmt);
                        }
                        break;
                    }
                }
            }

            $b->update([
                'plan_name' => $planName,
                'plan_original_price' => $origPrice,
                'plan_price' => $planPrice,
                'plan_discount_amount' => $discAmt,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'plan_name',
                'plan_original_price',
                'plan_price',
                'plan_discount_amount',
            ]);
        });
    }
};

