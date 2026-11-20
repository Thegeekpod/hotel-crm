<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\FoodAdvance;
use App\Models\RoomServiceOrder;

class FoodAdvanceService
{
    /**
     * Get the active FoodAdvance for a given booking or its primary reservation.
     */
    public static function getFoodAdvanceForBooking($booking): ?FoodAdvance
    {
        if (!$booking) {
            return null;
        }

        // Direct relation or primary booking relation
        if ($booking->foodAdvance) {
            return $booking->foodAdvance;
        }

        if ($booking->primary_booking_id && $booking->primaryBooking?->foodAdvance) {
            return $booking->primaryBooking->foodAdvance;
        }

        if ($booking->reserve_id) {
            $master = Booking::where('reserve_id', $booking->reserve_id)->whereNull('primary_booking_id')->first();
            if ($master?->foodAdvance) {
                return $master->foodAdvance;
            }
        }

        return null;
    }

    /**
     * Check if booking belongs to a Company registration type.
     */
    public static function isCompanyBooking($booking): bool
    {
        if (!$booking) {
            return false;
        }

        $reg = $booking->registrationType ?? $booking->primaryBooking?->registrationType;
        if ($reg && (stripos($reg->name, 'company') !== false || stripos($reg->name, 'corporate') !== false)) {
            return true;
        }

        if ($booking->company_id || $booking->primaryBooking?->company_id) {
            return true;
        }

        return false;
    }

    /**
     * Calculate comprehensive Food Advance summary for a booking and its associated stay.
     */
    public static function calculateSummary($booking, $allBookingIds = null, $roomServiceOrders = null): array
    {
        $foodAdvance = self::getFoodAdvanceForBooking($booking);

        if (!$foodAdvance || (float)$foodAdvance->advance_amount <= 0) {
            return [
                'has_food_advance' => false,
                'advance_amount' => 0.00,
                'total_food_subtotal' => 0.00,
                'used_amount' => 0.00,
                'remaining_advance' => 0.00,
                'unused_advance' => 0.00,
                'excess_food' => 0.00,
                'excess_gst' => 0.00,
                'excess_total' => 0.00,
                'net_adjustment' => 0.00,
            ];
        }

        $advanceAmount = (float)$foodAdvance->advance_amount;

        if (is_null($roomServiceOrders)) {
            if (is_null($allBookingIds)) {
                $allBookingIds = collect([$booking->id])
                    ->concat($booking->additionalBookings?->pluck('id') ?? collect())
                    ->filter()
                    ->all();
                if ($booking->primary_booking_id) {
                    $allBookingIds[] = $booking->primary_booking_id;
                    $allBookingIds = array_unique($allBookingIds);
                }
            }

            $roomServiceOrders = RoomServiceOrder::whereIn('booking_id', $allBookingIds)
                ->where('order_status', '!=', 'Cancelled')
                ->get();
        }

        $totalFoodSubtotal = (float)$roomServiceOrders->sum('subtotal');

        if ($totalFoodSubtotal < $advanceAmount) {
            $unusedAdvance = round($advanceAmount - $totalFoodSubtotal, 2);
            $excessFood = 0.00;
            $excessGst = 0.00;
            $excessCgst = 0.00;
            $excessSgst = 0.00;
            $excessTotal = 0.00;
            $netAdjustment = -1 * $unusedAdvance; // Deduct unused advance from pending bill (-₹40)
            $usedAmount = $totalFoodSubtotal;
            $remainingAdvance = $unusedAdvance;
        } else {
            $unusedAdvance = 0.00;
            $excessFood = round($totalFoodSubtotal - $advanceAmount, 2);
            $excessGst = round($excessFood * 0.05, 2); // 5% GST on excess (CGST 2.5% + SGST 2.5%)
            $excessCgst = round($excessGst / 2, 2);
            $excessSgst = round($excessGst - $excessCgst, 2);
            $excessTotal = round($excessFood + $excessGst, 2);
            $netAdjustment = $excessTotal; // Add excess bill with 5% GST (+₹52.50)
            $usedAmount = $advanceAmount;
            $remainingAdvance = 0.00;
        }

        return [
            'has_food_advance' => true,
            'advance_amount' => $advanceAmount,
            'total_food_subtotal' => $totalFoodSubtotal,
            'used_amount' => $usedAmount,
            'remaining_advance' => $remainingAdvance,
            'unused_advance' => $unusedAdvance,
            'excess_food' => $excessFood,
            'excess_gst' => $excessGst,
            'excess_cgst' => $excessCgst,
            'excess_sgst' => $excessSgst,
            'excess_total' => $excessTotal,
            'net_adjustment' => $netAdjustment,
        ];
    }

    /**
     * Calculate impact of a newly placed POS food order on the Food Advance.
     */
    public static function calculateOrderImpact($booking, float $newSubtotal): array
    {
        $foodAdvance = self::getFoodAdvanceForBooking($booking);

        if (!$foodAdvance || (float)$foodAdvance->advance_amount <= 0) {
            return [
                'has_food_advance' => false,
                'advance_amount' => 0.00,
                'prev_subtotal' => 0.00,
                'new_total_subtotal' => $newSubtotal,
                'covered_amount' => 0.00,
                'excess_amount' => $newSubtotal,
                'gst_rate' => 12.00, // standard POS GST
                'gst_amount' => round($newSubtotal * 0.12, 2),
                'charged_total' => round($newSubtotal * 1.12, 2),
            ];
        }

        $advAmount = (float)$foodAdvance->advance_amount;

        $allBookingIds = collect([$booking->id])
            ->concat($booking->additionalBookings?->pluck('id') ?? collect())
            ->filter()
            ->all();
        if ($booking->primary_booking_id) {
            $allBookingIds[] = $booking->primary_booking_id;
            $allBookingIds = array_unique($allBookingIds);
        }

        $prevSubtotal = (float)RoomServiceOrder::whereIn('booking_id', $allBookingIds)
            ->where('order_status', '!=', 'Cancelled')
            ->sum('subtotal');

        $newTotalSubtotal = round($prevSubtotal + $newSubtotal, 2);

        if ($prevSubtotal >= $advAmount) {
            // Food advance already exhausted by prior orders
            $coveredAmount = 0.00;
            $excessAmount = $newSubtotal;
        } elseif ($newTotalSubtotal <= $advAmount) {
            // Entire order is covered by food advance
            $coveredAmount = $newSubtotal;
            $excessAmount = 0.00;
        } else {
            // Partially covered, remainder is excess
            $coveredAmount = round($advAmount - $prevSubtotal, 2);
            $excessAmount = round($newSubtotal - $coveredAmount, 2);
        }

        // 5% GST specifically on the excess food amount as per company food allowance rules (CGST 2.5% + SGST 2.5%)
        $gstAmount = ($excessAmount > 0) ? round($excessAmount * 0.05, 2) : 0.00;
        $cgstAmount = round($gstAmount / 2, 2);
        $sgstAmount = round($gstAmount - $cgstAmount, 2);
        $chargedTotal = round($excessAmount + $gstAmount, 2);

        return [
            'has_food_advance' => true,
            'advance_amount' => $advAmount,
            'prev_subtotal' => $prevSubtotal,
            'new_total_subtotal' => $newTotalSubtotal,
            'covered_amount' => $coveredAmount,
            'excess_amount' => $excessAmount,
            'gst_rate' => 5.00,
            'cgst_rate' => 2.50,
            'sgst_rate' => 2.50,
            'gst_amount' => $gstAmount,
            'cgst_amount' => $cgstAmount,
            'sgst_amount' => $sgstAmount,
            'charged_total' => $chargedTotal,
        ];
    }

    /**
     * Calculate Corporate Food Discount for a booking based on its overall food charges.
     * Evaluated on the total food bill at settlement rather than per individual POS order,
     * so solid fixed amount discounts are applied only once across the reservation.
     */
    public static function calculateCorporateFoodDiscount($booking, float $foodCharges): array
    {
        if (!$booking || $foodCharges <= 0) {
            return [
                'has_discount' => false,
                'discount_type' => 'percentage',
                'discount_value' => 0.00,
                'discount_amount' => 0.00,
                'net_food_charges' => $foodCharges,
                'label' => null,
            ];
        }

        $primaryBooking = $booking->primary_booking_id ? $booking->primaryBooking : $booking;

        $discountType = $booking->food_discount 
            ?? $primaryBooking?->food_discount 
            ?? $booking->company?->food_discount 
            ?? $primaryBooking?->company?->food_discount 
            ?? 'percentage';

        $discountValue = (float)($booking->food_value 
            ?? $primaryBooking?->food_value 
            ?? 0);

        if ($discountValue <= 0) {
            $comp = $booking->company ?? $primaryBooking?->company;
            if ($comp && (float)$comp->food_value > 0) {
                $discountValue = (float)$comp->food_value;
                $discountType = $comp->food_discount ?? $discountType;
            }
        }

        if ($discountValue <= 0) {
            return [
                'has_discount' => false,
                'discount_type' => $discountType,
                'discount_value' => 0.00,
                'discount_amount' => 0.00,
                'net_food_charges' => $foodCharges,
                'label' => null,
            ];
        }

        if ($discountType === 'amount') {
            $discountAmount = min($foodCharges, round($discountValue, 2));
            $label = '₹' . number_format($discountValue, 2);
        } else {
            $discountAmount = round($foodCharges * ($discountValue / 100), 2);
            $label = $discountValue . '%';
        }

        $netFoodCharges = max(0, round($foodCharges - $discountAmount, 2));

        return [
            'has_discount' => ($discountAmount > 0),
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'discount_amount' => $discountAmount,
            'net_food_charges' => $netFoodCharges,
            'label' => $label,
        ];
    }
}

