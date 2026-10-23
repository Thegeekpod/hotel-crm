<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Booking;
use App\Models\Guest;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Title;
use App\Models\Nationality;
use App\Models\IdCardType;
use App\Models\RegistrationType;
use App\Models\ReservationMode;
use App\Models\Plan;
use App\Models\GstPercentage;

class FutureBookingsSeeder extends Seeder
{
    /**
     * Run the database seeds for future bookings with multiple room/guest allocations.
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $indianNat = Nationality::where('name', 'Indian')->first() ?? Nationality::first();
            $mrTitle = Title::where('name', 'Mr.')->first() ?? Title::first();
            $mrsTitle = Title::where('name', 'Mrs.')->first() ?? Title::first();
            $msTitle = Title::where('name', 'Ms.')->first() ?? Title::first();
            $drTitle = Title::where('name', 'Dr.')->first() ?? Title::first();

            $aadhaar = IdCardType::where('name', 'like', '%Aadhaar%')->first() ?? IdCardType::first();
            $pan = IdCardType::where('name', 'like', '%PAN%')->first() ?? IdCardType::first();
            $passport = IdCardType::where('name', 'like', '%Passport%')->first() ?? IdCardType::first();
            $license = IdCardType::where('name', 'like', '%Driving%')->first() ?? IdCardType::first();
            $voter = IdCardType::where('name', 'like', '%Voter%')->first() ?? IdCardType::first();

            $regRegular = RegistrationType::where('name', 'like', '%Regular%')->first() ?? RegistrationType::first();
            $regPrivilege = RegistrationType::where('name', 'like', '%Privilege%')->first() ?? RegistrationType::first();
            $regNew = RegistrationType::where('name', 'like', '%New%')->first() ?? RegistrationType::first();

            $modeWeb = ReservationMode::where('name', 'like', '%Website%')->first() ?? ReservationMode::first();
            $modeWalkin = ReservationMode::where('name', 'like', '%Walk-in%')->first() ?? ReservationMode::first();
            $modeOTA = ReservationMode::where('name', 'like', '%OTA%')->first() ?? ReservationMode::first();
            $modeCorp = ReservationMode::where('name', 'like', '%Corporate%')->first() ?? ReservationMode::first();

            $planEP = Plan::where('plan_name', 'like', '%None%')->orWhere('plan_name', 'like', '%Room Only%')->first();
            $planCP = Plan::where('plan_name', 'like', '%Breakfast%')->where('plan_name', 'not like', '%Lunch%')->first() ?? Plan::first();
            $planMAP = Plan::where('plan_name', 'like', '%Breakfast + Lunch%')->first() ?? Plan::first();

            $gst12 = GstPercentage::where('gst_percentage', 12.0)->first();
            $gst18 = GstPercentage::where('gst_percentage', 18.0)->first() ?? $gst12;

            /* =========================================================================
             * 1. SINGLE ROOM, SINGLE PERSON
             * Room 101, Checkin: 2026-10-25 to 2026-10-27 (2 nights)
             * ========================================================================= */
            $room101 = Room::where('room_number', '101')->firstOrFail();
            $resId1 = Booking::generateNextReserveId();
            $nights1 = 2;
            $rate1 = (float)$room101->rate;
            $planPrice1 = (float)($planCP?->price ?? 0);
            $totalAmt1 = ($rate1 + $planPrice1) * $nights1;
            $gstPct1 = $gst12 ? (float)$gst12->gst_percentage : 12.0;
            $gstAmt1 = round(($totalAmt1 * $gstPct1) / 100, 2);
            $payable1 = $totalAmt1 + $gstAmt1;
            $advance1 = 3000.00;
            $balance1 = max(0, $payable1 - $advance1);

            $guest1 = Guest::updateOrCreate(
                ['mobile' => '9830122410'],
                [
                    'title_id' => $mrTitle->id,
                    'guest_name' => 'Ananya Mukherjee',
                    'guest_address' => 'Salt Lake Sector 2, Block BD-45',
                    'nationality_id' => $indianNat->id,
                    'city' => 'Kolkata',
                    'email' => 'ananya.mukherjee@example.com',
                    'has_privilege_card' => false,
                    'id_card_type_id' => $aadhaar->id,
                    'id_card_number' => '5489-2104-7731',
                ]
            );

            $folio1 = Booking::generateFolioNumber($room101->room_number);
            $booking1 = Booking::create([
                'reserve_id' => $resId1,
                'folio_number' => $folio1,
                'guest_id' => $guest1->id,
                'room_id' => $room101->id,
                'registration_type_id' => $regRegular->id,
                'reservation_mode_id' => $modeWeb->id,
                'plan_id' => $planCP?->id,
                'checkin_date' => '2026-10-25',
                'checkin_time' => '12:00:00',
                'reserve_date' => '2026-10-25',
                'reserve_time' => '12:00:00',
                'checkout_date' => '2026-10-27',
                'checkout_time' => '11:00:00',
                'total_nights' => $nights1,
                'room_rate' => $rate1,
                'total_amount' => $totalAmt1,
                'discount_percentage' => 0.0,
                'discount_amount' => 0.00,
                'gst_type' => 'exclusive',
                'gst_percentage_id' => $gst12?->id,
                'gst_percentage' => $gstPct1,
                'gst_amount' => $gstAmt1,
                'payable_amount' => $payable1,
                'advance_amount' => $advance1,
                'balance' => $balance1,
                'status' => 'Confirmed',
                'primary_booking_id' => null,
                'remarks' => 'Single occupancy room reservation via hotel website',
            ]);

            Payment::create([
                'guest_id' => $guest1->id,
                'booking_id' => $booking1->id,
                'payment_mode_id' => 3, // UPI/Online
                'amount' => $advance1,
                'advance_amount' => $advance1,
                'balance' => $balance1,
                'folio_number' => $folio1,
                'payment_remarks' => 'Online website advance deposit',
                'payment_date' => '2026-10-23',
                'payment_time' => '11:15:00',
                'payment_type' => 'Advance',
                'status' => 'Success',
            ]);

            /* =========================================================================
             * 2. TWO PERSONS, SINGLE ROOM
             * Room 102, Checkin: 2026-10-26 to 2026-10-28 (2 nights)
             * Guest A (Primary) + Guest B (Secondary sharing room)
             * ========================================================================= */
            $room102 = Room::where('room_number', '102')->firstOrFail();
            $resId2 = Booking::generateNextReserveId();
            $nights2 = 2;
            $rate2 = (float)$room102->rate;
            $totalAmt2 = $rate2 * $nights2;
            $gstAmt2 = round(($totalAmt2 * 12.0) / 100, 2);
            $payable2 = $totalAmt2 + $gstAmt2;
            $advance2 = 2500.00;
            $balance2 = max(0, $payable2 - $advance2);

            $guest2A = Guest::updateOrCreate(
                ['mobile' => '9820561340'],
                [
                    'title_id' => $mrTitle->id,
                    'guest_name' => 'Rajesh Agarwal',
                    'guest_address' => 'Andheri West, Link Road',
                    'nationality_id' => $indianNat->id,
                    'city' => 'Mumbai',
                    'email' => 'rajesh.agarwal@example.com',
                    'has_privilege_card' => false,
                    'id_card_type_id' => $pan->id,
                    'id_card_number' => 'ABCDE1234F',
                ]
            );

            $folio2 = Booking::generateFolioNumber($room102->room_number);
            $primaryBooking2 = Booking::create([
                'reserve_id' => $resId2,
                'folio_number' => $folio2,
                'guest_id' => $guest2A->id,
                'room_id' => $room102->id,
                'registration_type_id' => $regRegular->id,
                'reservation_mode_id' => $modeWalkin->id,
                'plan_id' => $planEP?->id,
                'checkin_date' => '2026-10-26',
                'checkin_time' => '13:00:00',
                'reserve_date' => '2026-10-26',
                'reserve_time' => '13:00:00',
                'checkout_date' => '2026-10-28',
                'checkout_time' => '11:00:00',
                'total_nights' => $nights2,
                'room_rate' => $rate2,
                'total_amount' => $totalAmt2,
                'discount_percentage' => 0.0,
                'discount_amount' => 0.00,
                'gst_type' => 'exclusive',
                'gst_percentage_id' => $gst12?->id,
                'gst_percentage' => 12.0,
                'gst_amount' => $gstAmt2,
                'payable_amount' => $payable2,
                'advance_amount' => $advance2,
                'balance' => $balance2,
                'status' => 'Confirmed',
                'primary_booking_id' => null,
                'remarks' => 'Couple booking in single room',
            ]);

            Payment::create([
                'guest_id' => $guest2A->id,
                'booking_id' => $primaryBooking2->id,
                'payment_mode_id' => 1, // Cash / Counter
                'amount' => $advance2,
                'advance_amount' => $advance2,
                'balance' => $balance2,
                'folio_number' => $folio2,
                'payment_remarks' => 'Advance paid at front desk counter',
                'payment_date' => '2026-10-23',
                'payment_time' => '11:20:00',
                'payment_type' => 'Advance',
                'status' => 'Success',
            ]);

            // Guest 2B: Second occupant sharing Room 102
            $guest2B = Guest::updateOrCreate(
                ['mobile' => '9820561341'],
                [
                    'title_id' => $mrsTitle->id,
                    'guest_name' => 'Sneha Agarwal',
                    'guest_address' => 'Andheri West, Link Road',
                    'nationality_id' => $indianNat->id,
                    'city' => 'Mumbai',
                    'email' => 'sneha.agarwal@example.com',
                    'has_privilege_card' => false,
                    'id_card_type_id' => $aadhaar->id,
                    'id_card_number' => '7821-4459-9012',
                ]
            );

            Booking::create([
                'reserve_id' => $resId2,
                'folio_number' => $folio2,
                'guest_id' => $guest2B->id,
                'room_id' => $room102->id,
                'registration_type_id' => $regRegular->id,
                'reservation_mode_id' => $modeWalkin->id,
                'plan_id' => $planEP?->id,
                'checkin_date' => '2026-10-26',
                'checkin_time' => '13:00:00',
                'reserve_date' => '2026-10-26',
                'reserve_time' => '13:00:00',
                'checkout_date' => '2026-10-28',
                'checkout_time' => '11:00:00',
                'total_nights' => $nights2,
                'room_rate' => $rate2,
                'total_amount' => 0.00,
                'discount_percentage' => 0.0,
                'discount_amount' => 0.00,
                'gst_type' => 'exclusive',
                'gst_percentage_id' => null,
                'gst_percentage' => 0.0,
                'gst_amount' => 0.00,
                'payable_amount' => 0.00,
                'advance_amount' => 0.00,
                'balance' => 0.00,
                'status' => 'Confirmed',
                'primary_booking_id' => $primaryBooking2->id,
                'remarks' => 'Co-occupant with Rajesh Agarwal in Room 102',
            ]);

            /* =========================================================================
             * 3. THREE PEOPLE IN TWO ROOMS (2 - 1)
             * Room 103 (2 persons) + Room 104 (1 person)
             * Checkin: 2026-10-28 to 2026-10-31 (3 nights)
             * ========================================================================= */
            $room103 = Room::where('room_number', '103')->firstOrFail();
            $room104 = Room::where('room_number', '104')->firstOrFail();
            $resId3 = Booking::generateNextReserveId();
            $nights3 = 3;
            $rate103 = (float)$room103->rate;
            $rate104 = (float)$room104->rate;
            // Total billing for both rooms across 3 nights
            $totalAmt3 = ($rate103 + $rate104) * $nights3;
            $gstAmt3 = round(($totalAmt3 * 18.0) / 100, 2);
            $payable3 = $totalAmt3 + $gstAmt3;
            $advance3 = 10000.00;
            $balance3 = max(0, $payable3 - $advance3);

            $guest3A = Guest::updateOrCreate(
                ['mobile' => '9433087123'],
                [
                    'title_id' => $drTitle->id,
                    'guest_name' => 'Subhash Chandra Sen',
                    'guest_address' => 'Ballygunge Circular Road, Flat 4B',
                    'nationality_id' => $indianNat->id,
                    'city' => 'Kolkata',
                    'email' => 'subhash.sen@example.com',
                    'has_privilege_card' => true,
                    'privilege_card_no' => 'PRIV-SEN-882',
                    'id_card_type_id' => $passport->id,
                    'id_card_number' => 'Z5189204',
                ]
            );

            $folio3A = Booking::generateFolioNumber($room103->room_number);
            $primaryBooking3 = Booking::create([
                'reserve_id' => $resId3,
                'folio_number' => $folio3A,
                'guest_id' => $guest3A->id,
                'room_id' => $room103->id,
                'registration_type_id' => $regPrivilege->id,
                'reservation_mode_id' => $modeOTA->id,
                'plan_id' => $planCP?->id,
                'checkin_date' => '2026-10-28',
                'checkin_time' => '12:30:00',
                'reserve_date' => '2026-10-28',
                'reserve_time' => '12:30:00',
                'checkout_date' => '2026-10-31',
                'checkout_time' => '11:00:00',
                'total_nights' => $nights3,
                'room_rate' => $rate103,
                'total_amount' => $totalAmt3,
                'discount_percentage' => 0.0,
                'discount_amount' => 0.00,
                'gst_type' => 'exclusive',
                'gst_percentage_id' => $gst18?->id,
                'gst_percentage' => 18.0,
                'gst_amount' => $gstAmt3,
                'payable_amount' => $payable3,
                'advance_amount' => $advance3,
                'balance' => $balance3,
                'status' => 'Confirmed',
                'primary_booking_id' => null,
                'remarks' => 'Family 2-room booking: Room 103 (2 persons) + Room 104 (1 person)',
            ]);

            Payment::create([
                'guest_id' => $guest3A->id,
                'booking_id' => $primaryBooking3->id,
                'payment_mode_id' => 3, // Online / OTA Gateway
                'amount' => $advance3,
                'advance_amount' => $advance3,
                'balance' => $balance3,
                'folio_number' => $folio3A,
                'payment_remarks' => 'OTA Pre-payment received',
                'payment_date' => '2026-10-23',
                'payment_time' => '11:22:00',
                'payment_type' => 'Advance',
                'status' => 'Success',
            ]);

            // Guest 3B: Sharing Room 103 with Subhash Sen
            $guest3B = Guest::updateOrCreate(
                ['mobile' => '9433087124'],
                [
                    'title_id' => $mrsTitle->id,
                    'guest_name' => 'Madhumita Sen',
                    'guest_address' => 'Ballygunge Circular Road, Flat 4B',
                    'nationality_id' => $indianNat->id,
                    'city' => 'Kolkata',
                    'email' => 'madhumita.sen@example.com',
                    'has_privilege_card' => false,
                    'id_card_type_id' => $aadhaar->id,
                    'id_card_number' => '4410-9832-6510',
                ]
            );

            Booking::create([
                'reserve_id' => $resId3,
                'folio_number' => $folio3A,
                'guest_id' => $guest3B->id,
                'room_id' => $room103->id,
                'registration_type_id' => $regPrivilege->id,
                'reservation_mode_id' => $modeOTA->id,
                'plan_id' => $planCP?->id,
                'checkin_date' => '2026-10-28',
                'checkin_time' => '12:30:00',
                'reserve_date' => '2026-10-28',
                'reserve_time' => '12:30:00',
                'checkout_date' => '2026-10-31',
                'checkout_time' => '11:00:00',
                'total_nights' => $nights3,
                'room_rate' => $rate103,
                'total_amount' => 0.00,
                'discount_percentage' => 0.0,
                'discount_amount' => 0.00,
                'gst_type' => 'exclusive',
                'gst_percentage_id' => null,
                'gst_percentage' => 0.0,
                'gst_amount' => 0.00,
                'payable_amount' => 0.00,
                'advance_amount' => 0.00,
                'balance' => 0.00,
                'status' => 'Confirmed',
                'primary_booking_id' => $primaryBooking3->id,
                'remarks' => 'Co-occupant in Room 103',
            ]);

            // Guest 3C: Solo in Room 104
            $guest3C = Guest::updateOrCreate(
                ['mobile' => '9433087125'],
                [
                    'title_id' => $mrTitle->id,
                    'guest_name' => 'Arka Sen',
                    'guest_address' => 'Koramangala 4th Block',
                    'nationality_id' => $indianNat->id,
                    'city' => 'Bengaluru',
                    'email' => 'arka.sen@example.com',
                    'has_privilege_card' => false,
                    'id_card_type_id' => $license->id,
                    'id_card_number' => 'KA03-2022-0045192',
                ]
            );

            $folio3B = Booking::generateFolioNumber($room104->room_number);
            Booking::create([
                'reserve_id' => $resId3,
                'folio_number' => $folio3B,
                'guest_id' => $guest3C->id,
                'room_id' => $room104->id,
                'registration_type_id' => $regPrivilege->id,
                'reservation_mode_id' => $modeOTA->id,
                'plan_id' => $planCP?->id,
                'checkin_date' => '2026-10-28',
                'checkin_time' => '12:30:00',
                'reserve_date' => '2026-10-28',
                'reserve_time' => '12:30:00',
                'checkout_date' => '2026-10-31',
                'checkout_time' => '11:00:00',
                'total_nights' => $nights3,
                'room_rate' => $rate104,
                'total_amount' => 0.00,
                'discount_percentage' => 0.0,
                'discount_amount' => 0.00,
                'gst_type' => 'exclusive',
                'gst_percentage_id' => null,
                'gst_percentage' => 0.0,
                'gst_amount' => 0.00,
                'payable_amount' => 0.00,
                'advance_amount' => 0.00,
                'balance' => 0.00,
                'status' => 'Confirmed',
                'primary_booking_id' => $primaryBooking3->id,
                'remarks' => '2nd Room (Room 104) under Subhash Sen reservation',
            ]);

            /* =========================================================================
             * 4A. FIVE PEOPLE ACROSS 3 ROOMS (2 - 1 - 2)
             * Room 105 (2 persons) + Room 106 (1 person) + Room 107 (2 persons)
             * Checkin: 2026-11-02 to 2026-11-05 (3 nights)
             * ========================================================================= */
            $room105 = Room::where('room_number', '105')->firstOrFail();
            $room106 = Room::where('room_number', '106')->firstOrFail();
            $room107 = Room::where('room_number', '107')->firstOrFail();
            $resId4A = Booking::generateNextReserveId();
            $nights4A = 3;
            $rate105 = (float)$room105->rate;
            $rate106 = (float)$room106->rate;
            $rate107 = (float)$room107->rate;
            $totalAmt4A = ($rate105 + $rate106 + $rate107) * $nights4A;
            $gstAmt4A = round(($totalAmt4A * 18.0) / 100, 2);
            $payable4A = $totalAmt4A + $gstAmt4A;
            $advance4A = 15000.00;
            $balance4A = max(0, $payable4A - $advance4A);

            // Room 105: Guest 4A1 (Primary) + Guest 4A2
            $guest4A1 = Guest::updateOrCreate(
                ['mobile' => '9831155660'],
                [
                    'title_id' => $mrTitle->id,
                    'guest_name' => 'Pradeep Roy Chowdhury',
                    'guest_address' => 'Chittaranjan Park, Pocket 40',
                    'nationality_id' => $indianNat->id,
                    'city' => 'New Delhi',
                    'email' => 'pradeep.rc@example.com',
                    'has_privilege_card' => false,
                    'id_card_type_id' => $voter->id,
                    'id_card_number' => 'DL/04/052/981245',
                ]
            );

            $folio4A1 = Booking::generateFolioNumber($room105->room_number);
            $primaryBooking4A = Booking::create([
                'reserve_id' => $resId4A,
                'folio_number' => $folio4A1,
                'guest_id' => $guest4A1->id,
                'room_id' => $room105->id,
                'registration_type_id' => $regRegular->id,
                'reservation_mode_id' => $modeCorp->id,
                'plan_id' => $planMAP?->id,
                'checkin_date' => '2026-11-02',
                'checkin_time' => '14:00:00',
                'reserve_date' => '2026-11-02',
                'reserve_time' => '14:00:00',
                'checkout_date' => '2026-11-05',
                'checkout_time' => '11:00:00',
                'total_nights' => $nights4A,
                'room_rate' => $rate105,
                'total_amount' => $totalAmt4A,
                'discount_percentage' => 0.0,
                'discount_amount' => 0.00,
                'gst_type' => 'exclusive',
                'gst_percentage_id' => $gst18?->id,
                'gst_percentage' => 18.0,
                'gst_amount' => $gstAmt4A,
                'payable_amount' => $payable4A,
                'advance_amount' => $advance4A,
                'balance' => $balance4A,
                'status' => 'Confirmed',
                'primary_booking_id' => null,
                'remarks' => 'Group reservation: 5 guests in 3 rooms (2-1-2 breakdown)',
            ]);

            Payment::create([
                'guest_id' => $guest4A1->id,
                'booking_id' => $primaryBooking4A->id,
                'payment_mode_id' => 2, // NEFT / Bank Transfer
                'amount' => $advance4A,
                'advance_amount' => $advance4A,
                'balance' => $balance4A,
                'folio_number' => $folio4A1,
                'payment_remarks' => 'Bank NEFT Advance payment received',
                'payment_date' => '2026-10-23',
                'payment_time' => '11:25:00',
                'payment_type' => 'Advance',
                'status' => 'Success',
            ]);

            // Guest 4A2 in Room 105
            $guest4A2 = Guest::updateOrCreate(
                ['mobile' => '9831155661'],
                [
                    'title_id' => $mrsTitle->id,
                    'guest_name' => 'Sharmistha Roy Chowdhury',
                    'guest_address' => 'Chittaranjan Park, Pocket 40',
                    'nationality_id' => $indianNat->id,
                    'city' => 'New Delhi',
                    'email' => 'sharmistha.rc@example.com',
                    'has_privilege_card' => false,
                    'id_card_type_id' => $aadhaar->id,
                    'id_card_number' => '6612-4521-8903',
                ]
            );

            Booking::create([
                'reserve_id' => $resId4A,
                'folio_number' => $folio4A1,
                'guest_id' => $guest4A2->id,
                'room_id' => $room105->id,
                'registration_type_id' => $regRegular->id,
                'reservation_mode_id' => $modeCorp->id,
                'plan_id' => $planMAP?->id,
                'checkin_date' => '2026-11-02',
                'checkin_time' => '14:00:00',
                'reserve_date' => '2026-11-02',
                'reserve_time' => '14:00:00',
                'checkout_date' => '2026-11-05',
                'checkout_time' => '11:00:00',
                'total_nights' => $nights4A,
                'room_rate' => $rate105,
                'total_amount' => 0.00,
                'discount_percentage' => 0.0,
                'discount_amount' => 0.00,
                'gst_type' => 'exclusive',
                'gst_percentage_id' => null,
                'gst_percentage' => 0.0,
                'gst_amount' => 0.00,
                'payable_amount' => 0.00,
                'advance_amount' => 0.00,
                'balance' => 0.00,
                'status' => 'Confirmed',
                'primary_booking_id' => $primaryBooking4A->id,
                'remarks' => 'Co-occupant in Room 105',
            ]);

            // Room 106: Guest 4A3 (Solo)
            $guest4A3 = Guest::updateOrCreate(
                ['mobile' => '9831155662'],
                [
                    'title_id' => $msTitle->id,
                    'guest_name' => 'Debolina Roy Chowdhury',
                    'guest_address' => 'Koregaon Park, Lane 7',
                    'nationality_id' => $indianNat->id,
                    'city' => 'Pune',
                    'email' => 'debolina.rc@example.com',
                    'has_privilege_card' => false,
                    'id_card_type_id' => $pan->id,
                    'id_card_number' => 'PQRCS5678K',
                ]
            );

            $folio4A2 = Booking::generateFolioNumber($room106->room_number);
            Booking::create([
                'reserve_id' => $resId4A,
                'folio_number' => $folio4A2,
                'guest_id' => $guest4A3->id,
                'room_id' => $room106->id,
                'registration_type_id' => $regRegular->id,
                'reservation_mode_id' => $modeCorp->id,
                'plan_id' => $planMAP?->id,
                'checkin_date' => '2026-11-02',
                'checkin_time' => '14:00:00',
                'reserve_date' => '2026-11-02',
                'reserve_time' => '14:00:00',
                'checkout_date' => '2026-11-05',
                'checkout_time' => '11:00:00',
                'total_nights' => $nights4A,
                'room_rate' => $rate106,
                'total_amount' => 0.00,
                'discount_percentage' => 0.0,
                'discount_amount' => 0.00,
                'gst_type' => 'exclusive',
                'gst_percentage_id' => null,
                'gst_percentage' => 0.0,
                'gst_amount' => 0.00,
                'payable_amount' => 0.00,
                'advance_amount' => 0.00,
                'balance' => 0.00,
                'status' => 'Confirmed',
                'primary_booking_id' => $primaryBooking4A->id,
                'remarks' => 'Solo occupant in Room 106 under group reservation',
            ]);

            // Room 107: Guest 4A4 + Guest 4A5 (2 persons)
            $guest4A4 = Guest::updateOrCreate(
                ['mobile' => '9831155663'],
                [
                    'title_id' => $mrTitle->id,
                    'guest_name' => 'Sourav Banerjee',
                    'guest_address' => 'Sevoke Road, Hakim Para',
                    'nationality_id' => $indianNat->id,
                    'city' => 'Siliguri',
                    'email' => 'sourav.banerjee@example.com',
                    'has_privilege_card' => false,
                    'id_card_type_id' => $license->id,
                    'id_card_number' => 'WB74-2019-0034182',
                ]
            );

            $folio4A3 = Booking::generateFolioNumber($room107->room_number);
            Booking::create([
                'reserve_id' => $resId4A,
                'folio_number' => $folio4A3,
                'guest_id' => $guest4A4->id,
                'room_id' => $room107->id,
                'registration_type_id' => $regRegular->id,
                'reservation_mode_id' => $modeCorp->id,
                'plan_id' => $planMAP?->id,
                'checkin_date' => '2026-11-02',
                'checkin_time' => '14:00:00',
                'reserve_date' => '2026-11-02',
                'reserve_time' => '14:00:00',
                'checkout_date' => '2026-11-05',
                'checkout_time' => '11:00:00',
                'total_nights' => $nights4A,
                'room_rate' => $rate107,
                'total_amount' => 0.00,
                'discount_percentage' => 0.0,
                'discount_amount' => 0.00,
                'gst_type' => 'exclusive',
                'gst_percentage_id' => null,
                'gst_percentage' => 0.0,
                'gst_amount' => 0.00,
                'payable_amount' => 0.00,
                'advance_amount' => 0.00,
                'balance' => 0.00,
                'status' => 'Confirmed',
                'primary_booking_id' => $primaryBooking4A->id,
                'remarks' => 'Occupant in Room 107 under group reservation',
            ]);

            $guest4A5 = Guest::updateOrCreate(
                ['mobile' => '9831155664'],
                [
                    'title_id' => $mrsTitle->id,
                    'guest_name' => 'Rupa Banerjee',
                    'guest_address' => 'Sevoke Road, Hakim Para',
                    'nationality_id' => $indianNat->id,
                    'city' => 'Siliguri',
                    'email' => 'rupa.banerjee@example.com',
                    'has_privilege_card' => false,
                    'id_card_type_id' => $aadhaar->id,
                    'id_card_number' => '9012-3456-7890',
                ]
            );

            Booking::create([
                'reserve_id' => $resId4A,
                'folio_number' => $folio4A3,
                'guest_id' => $guest4A5->id,
                'room_id' => $room107->id,
                'registration_type_id' => $regRegular->id,
                'reservation_mode_id' => $modeCorp->id,
                'plan_id' => $planMAP?->id,
                'checkin_date' => '2026-11-02',
                'checkin_time' => '14:00:00',
                'reserve_date' => '2026-11-02',
                'reserve_time' => '14:00:00',
                'checkout_date' => '2026-11-05',
                'checkout_time' => '11:00:00',
                'total_nights' => $nights4A,
                'room_rate' => $rate107,
                'total_amount' => 0.00,
                'discount_percentage' => 0.0,
                'discount_amount' => 0.00,
                'gst_type' => 'exclusive',
                'gst_percentage_id' => null,
                'gst_percentage' => 0.0,
                'gst_amount' => 0.00,
                'payable_amount' => 0.00,
                'advance_amount' => 0.00,
                'balance' => 0.00,
                'status' => 'Confirmed',
                'primary_booking_id' => $primaryBooking4A->id,
                'remarks' => 'Co-occupant with Sourav Banerjee in Room 107',
            ]);

            /* =========================================================================
             * 4B. FIVE PEOPLE IN TWO ROOMS (3 in Room 201 + 2 in Room 202)
             * Room 201 Suite + Room 202 Deluxe
             * Checkin: 2026-11-06 to 2026-11-09 (3 nights)
             * ========================================================================= */
            $room201 = Room::where('room_number', '201')->firstOrFail();
            $room202 = Room::where('room_number', '202')->firstOrFail();
            $resId4B = Booking::generateNextReserveId();
            $nights4B = 3;
            $rate201 = (float)$room201->rate;
            $rate202 = (float)$room202->rate;
            $totalAmt4B = ($rate201 + $rate202) * $nights4B;
            $gstAmt4B = round(($totalAmt4B * 18.0) / 100, 2);
            $payable4B = $totalAmt4B + $gstAmt4B;
            $advance4B = 20000.00;
            $balance4B = max(0, $payable4B - $advance4B);

            // Room 201: 3 People (Guest 4B1 [Primary], Guest 4B2, Guest 4B3)
            $guest4B1 = Guest::updateOrCreate(
                ['mobile' => '9711034890'],
                [
                    'title_id' => $mrTitle->id,
                    'guest_name' => 'Harsh Vardhan Singhania',
                    'guest_address' => 'Civil Lines, Raj Bhavan Road',
                    'nationality_id' => $indianNat->id,
                    'city' => 'Jaipur',
                    'email' => 'harsh.singhania@example.com',
                    'has_privilege_card' => true,
                    'privilege_card_no' => 'PRIV-SNG-109',
                    'id_card_type_id' => $passport->id,
                    'id_card_number' => 'K8901234',
                ]
            );

            $folio4B1 = Booking::generateFolioNumber($room201->room_number);
            $primaryBooking4B = Booking::create([
                'reserve_id' => $resId4B,
                'folio_number' => $folio4B1,
                'guest_id' => $guest4B1->id,
                'room_id' => $room201->id,
                'registration_type_id' => $regPrivilege->id,
                'reservation_mode_id' => $modeWeb->id,
                'plan_id' => $planCP?->id,
                'checkin_date' => '2026-11-06',
                'checkin_time' => '12:00:00',
                'reserve_date' => '2026-11-06',
                'reserve_time' => '12:00:00',
                'checkout_date' => '2026-11-09',
                'checkout_time' => '11:00:00',
                'total_nights' => $nights4B,
                'room_rate' => $rate201,
                'total_amount' => $totalAmt4B,
                'discount_percentage' => 0.0,
                'discount_amount' => 0.00,
                'gst_type' => 'exclusive',
                'gst_percentage_id' => $gst18?->id,
                'gst_percentage' => 18.0,
                'gst_amount' => $gstAmt4B,
                'payable_amount' => $payable4B,
                'advance_amount' => $advance4B,
                'balance' => $balance4B,
                'status' => 'Confirmed',
                'primary_booking_id' => null,
                'remarks' => '2-Room 5-Guest reservation (3 guests in Room 201 Suite, 2 in Room 202 Deluxe)',
            ]);

            Payment::create([
                'guest_id' => $guest4B1->id,
                'booking_id' => $primaryBooking4B->id,
                'payment_mode_id' => 3, // Credit Card / Online
                'amount' => $advance4B,
                'advance_amount' => $advance4B,
                'balance' => $balance4B,
                'folio_number' => $folio4B1,
                'payment_remarks' => 'Online card advance deposit',
                'payment_date' => '2026-10-23',
                'payment_time' => '11:27:00',
                'payment_type' => 'Advance',
                'status' => 'Success',
            ]);

            // Guest 4B2 in Room 201
            $guest4B2 = Guest::updateOrCreate(
                ['mobile' => '9711034891'],
                [
                    'title_id' => $mrsTitle->id,
                    'guest_name' => 'Kavita Singhania',
                    'guest_address' => 'Civil Lines, Raj Bhavan Road',
                    'nationality_id' => $indianNat->id,
                    'city' => 'Jaipur',
                    'email' => 'kavita.singhania@example.com',
                    'has_privilege_card' => false,
                    'id_card_type_id' => $aadhaar->id,
                    'id_card_number' => '3321-7788-9901',
                ]
            );

            Booking::create([
                'reserve_id' => $resId4B,
                'folio_number' => $folio4B1,
                'guest_id' => $guest4B2->id,
                'room_id' => $room201->id,
                'registration_type_id' => $regPrivilege->id,
                'reservation_mode_id' => $modeWeb->id,
                'plan_id' => $planCP?->id,
                'checkin_date' => '2026-11-06',
                'checkin_time' => '12:00:00',
                'reserve_date' => '2026-11-06',
                'reserve_time' => '12:00:00',
                'checkout_date' => '2026-11-09',
                'checkout_time' => '11:00:00',
                'total_nights' => $nights4B,
                'room_rate' => $rate201,
                'total_amount' => 0.00,
                'discount_percentage' => 0.0,
                'discount_amount' => 0.00,
                'gst_type' => 'exclusive',
                'gst_percentage_id' => null,
                'gst_percentage' => 0.0,
                'gst_amount' => 0.00,
                'payable_amount' => 0.00,
                'advance_amount' => 0.00,
                'balance' => 0.00,
                'status' => 'Confirmed',
                'primary_booking_id' => $primaryBooking4B->id,
                'remarks' => 'Co-occupant in Room 201 Suite',
            ]);

            // Guest 4B3 in Room 201
            $guest4B3 = Guest::updateOrCreate(
                ['mobile' => '9711034892'],
                [
                    'title_id' => $mrTitle->id,
                    'guest_name' => 'Yash Singhania',
                    'guest_address' => 'Civil Lines, Raj Bhavan Road',
                    'nationality_id' => $indianNat->id,
                    'city' => 'Jaipur',
                    'email' => 'yash.singhania@example.com',
                    'has_privilege_card' => false,
                    'id_card_type_id' => $license->id,
                    'id_card_number' => 'RJ14-2021-0099881',
                ]
            );

            Booking::create([
                'reserve_id' => $resId4B,
                'folio_number' => $folio4B1,
                'guest_id' => $guest4B3->id,
                'room_id' => $room201->id,
                'registration_type_id' => $regPrivilege->id,
                'reservation_mode_id' => $modeWeb->id,
                'plan_id' => $planCP?->id,
                'checkin_date' => '2026-11-06',
                'checkin_time' => '12:00:00',
                'reserve_date' => '2026-11-06',
                'reserve_time' => '12:00:00',
                'checkout_date' => '2026-11-09',
                'checkout_time' => '11:00:00',
                'total_nights' => $nights4B,
                'room_rate' => $rate201,
                'total_amount' => 0.00,
                'discount_percentage' => 0.0,
                'discount_amount' => 0.00,
                'gst_type' => 'exclusive',
                'gst_percentage_id' => null,
                'gst_percentage' => 0.0,
                'gst_amount' => 0.00,
                'payable_amount' => 0.00,
                'advance_amount' => 0.00,
                'balance' => 0.00,
                'status' => 'Confirmed',
                'primary_booking_id' => $primaryBooking4B->id,
                'remarks' => '3rd occupant in Room 201 Suite',
            ]);

            // Room 202: 2 People (Guest 4B4, Guest 4B5)
            $guest4B4 = Guest::updateOrCreate(
                ['mobile' => '9711034893'],
                [
                    'title_id' => $mrTitle->id,
                    'guest_name' => 'Alok Mathur',
                    'guest_address' => 'Navrangpura, Near Stadium',
                    'nationality_id' => $indianNat->id,
                    'city' => 'Ahmedabad',
                    'email' => 'alok.mathur@example.com',
                    'has_privilege_card' => false,
                    'id_card_type_id' => $pan->id,
                    'id_card_number' => 'KLMNO4321D',
                ]
            );

            $folio4B2 = Booking::generateFolioNumber($room202->room_number);
            Booking::create([
                'reserve_id' => $resId4B,
                'folio_number' => $folio4B2,
                'guest_id' => $guest4B4->id,
                'room_id' => $room202->id,
                'registration_type_id' => $regPrivilege->id,
                'reservation_mode_id' => $modeWeb->id,
                'plan_id' => $planCP?->id,
                'checkin_date' => '2026-11-06',
                'checkin_time' => '12:00:00',
                'reserve_date' => '2026-11-06',
                'reserve_time' => '12:00:00',
                'checkout_date' => '2026-11-09',
                'checkout_time' => '11:00:00',
                'total_nights' => $nights4B,
                'room_rate' => $rate202,
                'total_amount' => 0.00,
                'discount_percentage' => 0.0,
                'discount_amount' => 0.00,
                'gst_type' => 'exclusive',
                'gst_percentage_id' => null,
                'gst_percentage' => 0.0,
                'gst_amount' => 0.00,
                'payable_amount' => 0.00,
                'advance_amount' => 0.00,
                'balance' => 0.00,
                'status' => 'Confirmed',
                'primary_booking_id' => $primaryBooking4B->id,
                'remarks' => 'Occupant in 2nd room (Room 202) under Singhania group reservation',
            ]);

            $guest4B5 = Guest::updateOrCreate(
                ['mobile' => '9711034894'],
                [
                    'title_id' => $mrsTitle->id,
                    'guest_name' => 'Neha Mathur',
                    'guest_address' => 'Navrangpura, Near Stadium',
                    'nationality_id' => $indianNat->id,
                    'city' => 'Ahmedabad',
                    'email' => 'neha.mathur@example.com',
                    'has_privilege_card' => false,
                    'id_card_type_id' => $aadhaar->id,
                    'id_card_number' => '5544-6677-8899',
                ]
            );

            Booking::create([
                'reserve_id' => $resId4B,
                'folio_number' => $folio4B2,
                'guest_id' => $guest4B5->id,
                'room_id' => $room202->id,
                'registration_type_id' => $regPrivilege->id,
                'reservation_mode_id' => $modeWeb->id,
                'plan_id' => $planCP?->id,
                'checkin_date' => '2026-11-06',
                'checkin_time' => '12:00:00',
                'reserve_date' => '2026-11-06',
                'reserve_time' => '12:00:00',
                'checkout_date' => '2026-11-09',
                'checkout_time' => '11:00:00',
                'total_nights' => $nights4B,
                'room_rate' => $rate202,
                'total_amount' => 0.00,
                'discount_percentage' => 0.0,
                'discount_amount' => 0.00,
                'gst_type' => 'exclusive',
                'gst_percentage_id' => null,
                'gst_percentage' => 0.0,
                'gst_amount' => 0.00,
                'payable_amount' => 0.00,
                'advance_amount' => 0.00,
                'balance' => 0.00,
                'status' => 'Confirmed',
                'primary_booking_id' => $primaryBooking4B->id,
                'remarks' => 'Co-occupant with Alok Mathur in Room 202',
            ]);

            DB::commit();

            echo "All future bookings seeded successfully!\n";
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
