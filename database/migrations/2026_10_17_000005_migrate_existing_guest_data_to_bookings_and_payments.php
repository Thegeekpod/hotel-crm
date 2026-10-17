<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('guests') || !Schema::hasTable('bookings')) {
            return;
        }

        // Only migrate if guests still has the booking columns
        if (!Schema::hasColumn('guests', 'reserve_id')) {
            return;
        }

        $guests = DB::table('guests')->get();
        $guestToBookingMap = [];

        // First pass: Create bookings and payments for each guest record
        foreach ($guests as $g) {
            $bookingId = DB::table('bookings')->insertGetId([
                'reserve_id' => $g->reserve_id,
                'folio_number' => $g->folio_number,
                'guest_id' => $g->id,
                'room_id' => $g->room_id,
                'registration_type_id' => $g->registration_type_id,
                'reservation_mode_id' => $g->reservation_mode_id,
                'company_id' => $g->company_id,
                'new_company_name' => $g->new_company_name,
                'new_company_address' => $g->new_company_address,
                'new_company_gstin' => $g->new_company_gstin,
                'new_company_phone' => $g->new_company_phone,
                'plan_id' => $g->plan_id ?? null,
                'checkin_date' => $g->reserve_date,
                'checkin_time' => $g->reserve_time,
                'reserve_date' => $g->reserve_date,
                'reserve_time' => $g->reserve_time,
                'checkout_date' => $g->checkout_date,
                'checkout_time' => $g->checkout_time,
                'total_nights' => $g->total_nights ?? 1,
                'room_rate' => $g->room_rate ?? 0.00,
                'total_amount' => $g->total_amount ?? 0.00,
                'discount_percentage' => $g->discount_percentage ?? 0.00,
                'discount_amount' => $g->discount_amount ?? 0.00,
                'gst_type' => $g->gst_type ?? 'exclusive',
                'gst_percentage_id' => $g->gst_percentage_id ?? null,
                'gst_percentage' => $g->gst_percentage ?? 0.00,
                'gst_amount' => $g->gst_amount ?? 0.00,
                'payable_amount' => $g->payable_amount ?? 0.00,
                'advance_amount' => $g->advance_amount ?? 0.00,
                'balance' => $g->balance ?? 0.00,
                'status' => $g->status ?? 'Confirmed',
                'primary_booking_id' => null, // will update in 2nd pass
                'remarks' => $g->payment_remarks ?? null,
                'created_at' => $g->created_at ?? now(),
                'updated_at' => $g->updated_at ?? now(),
            ]);

            $guestToBookingMap[$g->id] = $bookingId;

            // Migrate payment record if payment details exist
            if (!empty($g->payment_mode_id) || (float)($g->advance_amount ?? 0) > 0) {
                DB::table('payments')->insert([
                    'guest_id' => $g->id,
                    'booking_id' => $bookingId,
                    'payment_mode_id' => $g->payment_mode_id,
                    'amount' => $g->advance_amount ?? 0.00,
                    'advance_amount' => $g->advance_amount ?? 0.00,
                    'balance' => $g->balance ?? 0.00,
                    'folio_number' => $g->folio_number,
                    'payment_remarks' => $g->payment_remarks,
                    'payment_date' => $g->reserve_date,
                    'payment_time' => $g->reserve_time,
                    'payment_type' => 'Advance',
                    'transaction_id' => null,
                    'status' => 'Success',
                    'created_at' => $g->created_at ?? now(),
                    'updated_at' => $g->updated_at ?? now(),
                ]);
            }

            // Migrate checkout record if status is Checked Out
            if (($g->status ?? '') === 'Checked Out') {
                DB::table('checkouts')->insert([
                    'guest_id' => $g->id,
                    'booking_id' => $bookingId,
                    'room_id' => $g->room_id,
                    'settlement_id' => null,
                    'checkout_date' => $g->checkout_date,
                    'checkout_time' => $g->checkout_time,
                    'actual_checkout_at' => $g->checkout_date ? ($g->checkout_date . ' ' . ($g->checkout_time ?? '11:00:00')) : now(),
                    'total_nights' => $g->total_nights ?? 1,
                    'room_charges' => $g->total_amount ?? 0.00,
                    'extra_charges' => 0.00,
                    'late_checkout_charge' => 0.00,
                    'discount_amount' => $g->discount_amount ?? 0.00,
                    'tax_amount' => $g->gst_amount ?? 0.00,
                    'grand_total' => $g->payable_amount ?? 0.00,
                    'paid_amount' => $g->advance_amount ?? 0.00,
                    'balance_amount' => $g->balance ?? 0.00,
                    'status' => 'Completed',
                    'remarks' => 'Automated migration from guests table',
                    'created_at' => $g->updated_at ?? now(),
                    'updated_at' => $g->updated_at ?? now(),
                ]);
            }
        }

        // Second pass: Link primary_booking_id for secondary bookings
        foreach ($guests as $g) {
            if (!empty($g->primary_guest_id) && isset($guestToBookingMap[$g->primary_guest_id])) {
                $currBookingId = $guestToBookingMap[$g->id] ?? null;
                $primBookingId = $guestToBookingMap[$g->primary_guest_id] ?? null;
                if ($currBookingId && $primBookingId) {
                    DB::table('bookings')
                        ->where('id', $currBookingId)
                        ->update(['primary_booking_id' => $primBookingId]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('checkouts')->truncate();
        DB::table('payments')->truncate();
        DB::table('bookings')->truncate();
    }
};
