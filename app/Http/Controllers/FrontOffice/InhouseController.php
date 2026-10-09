<?php

namespace App\Http\Controllers\FrontOffice;

use App\Http\Controllers\Controller;
use App\Models\RoomCategory;
use App\Models\Floor;
use App\Models\Company;
use App\Models\IdCardType;
use App\Models\ReservationMode;
use App\Models\PaymentMode;
use Illuminate\Http\Request;

class InhouseController extends Controller
{
    public function index(Request $request)
    {
        $categories = RoomCategory::where('status', 'Active')->get();
        $floors = Floor::where('status', 'Active')->orderBy('floor', 'asc')->get();
        $companies = Company::where('status', 'Active')->get();
        $idCardTypes = IdCardType::where('status', 'Active')->get();
        $reservationModes = ReservationMode::where('status', 'Active')->get();
        $paymentModes = PaymentMode::where('status', 'Active')->get();
        $rackRooms = [];

        $inhouseGuests = [
            ['room' => '102', 'type' => 'DELUXE', 'name' => 'Sanjeev Kumar Singh', 'phone' => '+91 98112 34567', 'checkin' => '22 Sep 14:30', 'departure' => '25 Sep 11:00', 'pax' => 2, 'status' => 'Arrived', 'status_class' => 'green', 'balance' => '10,400', 'folio' => 'FOL-102-882'],
            ['room' => '202', 'type' => 'DELUXE', 'name' => 'AJEET BHENGRA', 'phone' => '+91 97712 90123', 'checkin' => '21 Sep 11:15', 'departure' => '24 Sep 12:00', 'pax' => 1, 'status' => 'Stay Over', 'status_class' => 'yellow', 'balance' => '7,800', 'folio' => 'FOL-202-710'],
            ['room' => '205', 'type' => 'SUPER DELUXE', 'name' => 'KHAGESWAR ROUT', 'phone' => '+91 94370 55123', 'checkin' => '20 Sep 18:00', 'departure' => '25 Sep 10:00', 'pax' => 2, 'status' => 'Stay Over', 'status_class' => 'yellow', 'balance' => '14,200', 'folio' => 'FOL-205-551'],
            ['room' => '206', 'type' => 'DELUXE', 'name' => 'Raj kumar Bhunia', 'phone' => '+91 98321 44091', 'checkin' => '23 Sep 09:30', 'departure' => '24 Sep 12:00', 'pax' => 1, 'status' => 'Arrived', 'status_class' => 'green', 'balance' => '3,920', 'folio' => 'FOL-206-339'],
            ['room' => '302', 'type' => 'DELUXE', 'name' => 'SANGADA RAJUBHAI NAL', 'phone' => '+91 99042 18273', 'checkin' => '22 Sep 15:45', 'departure' => '25 Sep 11:00', 'pax' => 2, 'status' => 'Stay Over', 'status_class' => 'yellow', 'balance' => '8,100', 'folio' => 'FOL-302-991'],
            ['room' => '304', 'type' => 'SUPER DELUXE', 'name' => 'HITENDRA NINAWE', 'phone' => '+91 98230 44910', 'checkin' => '21 Sep 16:00', 'departure' => '24 Sep 10:00', 'pax' => 1, 'status' => 'Stay Over', 'status_class' => 'yellow', 'balance' => '11,900', 'folio' => 'FOL-304-102'],
            ['room' => '305', 'type' => 'SUPER DELUXE', 'name' => 'ADVAIT CHAVAN', 'phone' => '+91 97654 32189', 'checkin' => '21 Sep 13:00', 'departure' => '24 Sep 11:30', 'pax' => 2, 'status' => 'Stay Over', 'status_class' => 'yellow', 'balance' => '12,400', 'folio' => 'FOL-305-673'],
            ['room' => '307', 'type' => 'SUPER DELUXE', 'name' => 'BIKRAM KR SAHOO', 'phone' => '+91 94380 99182', 'checkin' => '20 Sep 20:00', 'departure' => '24 Sep 09:00', 'pax' => 2, 'status' => 'Stay Over', 'status_class' => 'yellow', 'balance' => '16,500', 'folio' => 'FOL-307-889'],
            ['room' => '401', 'type' => 'EXECUTIVE', 'name' => 'Vikramaditya Roy', 'phone' => '+91 98300 77123', 'checkin' => '23 Sep 08:00', 'departure' => '26 Sep 12:00', 'pax' => 2, 'status' => 'Arrived', 'status_class' => 'green', 'balance' => '14,500', 'folio' => 'FOL-401-440'],
            ['room' => '408', 'type' => 'SUITE', 'name' => 'Dr. Ananya Sen', 'phone' => '+91 98450 11982', 'checkin' => '21 Sep 14:00', 'departure' => '25 Sep 12:00', 'pax' => 2, 'status' => 'Stay Over', 'status_class' => 'yellow', 'balance' => '19,500', 'folio' => 'FOL-408-204'],
        ];

        return view('frontoffice.inhouse.index', compact(
            'inhouseGuests',
            'categories',
            'floors',
            'companies',
            'idCardTypes',
            'reservationModes',
            'paymentModes',
            'rackRooms'
        ));
    }
}
