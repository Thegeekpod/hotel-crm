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

class ArrivalController extends Controller
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

        $arrivals = [
            [
                'ref' => 'RES-901',
                'guest_name' => 'Manish Khemka',
                'category' => 'SUPER DELUXE',
                'room' => '101',
                'stay_dates' => '24 Sep - 26 Sep',
                'pax' => 2,
                'advance' => '₹ 3,000',
                'status' => 'Confirmed',
                'status_class' => 'green'
            ],
            [
                'ref' => 'RES-902',
                'guest_name' => 'Natasha Roy',
                'category' => 'SUITE',
                'room' => '210',
                'stay_dates' => '25 Sep - 27 Sep',
                'pax' => 2,
                'advance' => '₹ 5,000',
                'status' => 'Guaranteed',
                'status_class' => 'blue'
            ],
            [
                'ref' => 'RES-903',
                'guest_name' => 'Sunil Narang',
                'category' => 'DELUXE',
                'room' => '106',
                'stay_dates' => '24 Sep - 25 Sep',
                'pax' => 1,
                'advance' => '₹ 3,500',
                'status' => 'Confirmed',
                'status_class' => 'green'
            ],
        ];

        return view('frontoffice.arrivals.index', compact(
            'arrivals',
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
