<?php

namespace App\Http\Controllers\FrontOffice;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Floor;
use App\Models\RoomCategory;
use App\Models\Company;
use App\Models\IdCardType;
use App\Models\ReservationMode;
use App\Models\PaymentMode;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $dbRooms = Room::orderBy('room_number', 'asc')->get();
        $floors = Floor::where('status', 'Active')->orderBy('floor', 'asc')->get();
        $categories = RoomCategory::where('status', 'Active')->get();
        $companies = Company::where('status', 'Active')->get();
        $idCardTypes = IdCardType::where('status', 'Active')->get();
        $reservationModes = ReservationMode::where('status', 'Active')->get();
        $paymentModes = PaymentMode::where('status', 'Active')->get();

        // Sample / Live Front Office Room Rack list
        $staticSampleRooms = [
            // Floor 1
            ['room' => '101', 'floor' => '1', 'type' => 'SUPER DELUXE', 'cleaning' => 'Cleaned', 'status' => 'available', 'rate' => 5200, 'guest' => null],
            ['room' => '102', 'floor' => '1', 'type' => 'DELUXE', 'cleaning' => 'Cleaned', 'status' => 'occupied', 'rate' => 3500, 'guest' => ['name' => 'Sanjeev Kumar Singh', 'state' => 'Arrived', 'folio' => 'FOL-102-882', 'balance' => 10400]],
            ['room' => '103', 'floor' => '1', 'type' => 'SUPER DELUXE', 'cleaning' => 'Dirty', 'status' => 'dirty', 'rate' => 5200, 'guest' => null],
            ['room' => '104', 'floor' => '1', 'type' => 'SUPER DELUXE', 'cleaning' => 'Blocked', 'status' => 'blocked', 'rate' => 5200, 'guest' => null],
            
            // Floor 2
            ['room' => '201', 'floor' => '2', 'type' => 'SUITE', 'cleaning' => 'Dirty', 'status' => 'dirty', 'rate' => 8500, 'guest' => null],
            ['room' => '202', 'floor' => '2', 'type' => 'DELUXE', 'cleaning' => 'Cleaned', 'status' => 'occupied', 'rate' => 3500, 'guest' => ['name' => 'AJEET BHENGRA', 'state' => 'Stay Over', 'folio' => 'FOL-202-710', 'balance' => 7800]],
            ['room' => '203', 'floor' => '2', 'type' => 'SUPER DELUXE', 'cleaning' => 'Dirty', 'status' => 'dirty', 'rate' => 5200, 'guest' => null],
            ['room' => '204', 'floor' => '2', 'type' => 'SUPER DELUXE', 'cleaning' => 'Dirty', 'status' => 'dirty', 'rate' => 5200, 'guest' => null],
            ['room' => '205', 'floor' => '2', 'type' => 'SUPER DELUXE', 'cleaning' => 'Cleaned', 'status' => 'occupied', 'rate' => 5200, 'guest' => ['name' => 'KHAGESWAR ROUT', 'state' => 'Stay Over', 'folio' => 'FOL-205-551', 'balance' => 14200]],
            ['room' => '206', 'floor' => '2', 'type' => 'DELUXE', 'cleaning' => 'Cleaned', 'status' => 'occupied', 'rate' => 3500, 'guest' => ['name' => 'Raj kumar Bhunia', 'state' => 'Arrived', 'folio' => 'FOL-206-339', 'balance' => 3920]],
            ['room' => '207', 'floor' => '2', 'type' => 'SUPER DELUXE', 'cleaning' => 'Dirty', 'status' => 'dirty', 'rate' => 5200, 'guest' => null],
            ['room' => '208', 'floor' => '2', 'type' => 'SUPER DELUXE', 'cleaning' => 'Cleaned', 'status' => 'available', 'rate' => 5200, 'guest' => null],
            ['room' => '209', 'floor' => '2', 'type' => 'SUPER DELUXE', 'cleaning' => 'Cleaned', 'status' => 'available', 'rate' => 5200, 'guest' => null],
            ['room' => '210', 'floor' => '2', 'type' => 'SUITE', 'cleaning' => 'Cleaned', 'status' => 'available', 'rate' => 8500, 'guest' => null],
            
            // Floor 3
            ['room' => '301', 'floor' => '3', 'type' => 'SUITE', 'cleaning' => 'Dirty', 'status' => 'dirty', 'rate' => 8500, 'guest' => null],
            ['room' => '302', 'floor' => '3', 'type' => 'DELUXE', 'cleaning' => 'Cleaned', 'status' => 'occupied', 'rate' => 3500, 'guest' => ['name' => 'SANGADA RAJUBHAI NAL', 'state' => 'Stay Over', 'folio' => 'FOL-302-991', 'balance' => 8100]],
            ['room' => '303', 'floor' => '3', 'type' => 'SUPER DELUXE', 'cleaning' => 'Cleaned', 'status' => 'available', 'rate' => 5200, 'guest' => null],
            ['room' => '304', 'floor' => '3', 'type' => 'SUPER DELUXE', 'cleaning' => 'Cleaned', 'status' => 'occupied', 'rate' => 5200, 'guest' => ['name' => 'HITENDRA NINAWE', 'state' => 'Stay Over', 'folio' => 'FOL-304-102', 'balance' => 11900]],
            ['room' => '305', 'floor' => '3', 'type' => 'SUPER DELUXE', 'cleaning' => 'Cleaned', 'status' => 'occupied', 'rate' => 5200, 'guest' => ['name' => 'ADVAIT CHAVAN', 'state' => 'Stay Over', 'folio' => 'FOL-305-673', 'balance' => 12400]],
            ['room' => '306', 'floor' => '3', 'type' => 'DELUXE', 'cleaning' => 'Cleaned', 'status' => 'available', 'rate' => 3500, 'guest' => null],
            ['room' => '307', 'floor' => '3', 'type' => 'SUPER DELUXE', 'cleaning' => 'Cleaned', 'status' => 'occupied', 'rate' => 5200, 'guest' => ['name' => 'BIKRAM KR SAHOO', 'state' => 'Stay Over', 'folio' => 'FOL-307-889', 'balance' => 16500]],
            ['room' => '308', 'floor' => '3', 'type' => 'SUPER DELUXE', 'cleaning' => 'Cleaned', 'status' => 'available', 'rate' => 5200, 'guest' => null],
            ['room' => '309', 'floor' => '3', 'type' => 'DELUXE', 'cleaning' => 'Dirty', 'status' => 'dirty', 'rate' => 3500, 'guest' => null],
            ['room' => '310', 'floor' => '3', 'type' => 'SUITE', 'cleaning' => 'Cleaned', 'status' => 'available', 'rate' => 8500, 'guest' => null],

            // Floor 4
            ['room' => '401', 'floor' => '4', 'type' => 'EXECUTIVE', 'cleaning' => 'Cleaned', 'status' => 'occupied', 'rate' => 11000, 'guest' => ['name' => 'Vikramaditya Roy', 'state' => 'Arrived', 'folio' => 'FOL-401-440', 'balance' => 14500]],
            ['room' => '402', 'floor' => '4', 'type' => 'EXECUTIVE', 'cleaning' => 'Cleaned', 'status' => 'available', 'rate' => 11000, 'guest' => null],
            ['room' => '403', 'floor' => '4', 'type' => 'SUPER DELUXE', 'cleaning' => 'Cleaned', 'status' => 'occupied', 'rate' => 5200, 'guest' => ['name' => 'Priyanka Mukherjee', 'state' => 'Stay Over', 'folio' => 'FOL-403-109', 'balance' => 6200]],
            ['room' => '404', 'floor' => '4', 'type' => 'SUPER DELUXE', 'cleaning' => 'Blocked', 'status' => 'blocked', 'rate' => 5200, 'guest' => null],
            ['room' => '405', 'floor' => '4', 'type' => 'DELUXE', 'cleaning' => 'Cleaned', 'status' => 'occupied', 'rate' => 3500, 'guest' => ['name' => 'Rahul Verma', 'state' => 'Arrived', 'folio' => 'FOL-405-772', 'balance' => 4100]],
            ['room' => '406', 'floor' => '4', 'type' => 'DELUXE', 'cleaning' => 'Cleaned', 'status' => 'available', 'rate' => 3500, 'guest' => null],
            ['room' => '407', 'floor' => '4', 'type' => 'SUPER DELUXE', 'cleaning' => 'Dirty', 'status' => 'dirty', 'rate' => 5200, 'guest' => null],
            ['room' => '408', 'floor' => '4', 'type' => 'SUITE', 'cleaning' => 'Cleaned', 'status' => 'occupied', 'rate' => 8500, 'guest' => ['name' => 'Dr. Ananya Sen', 'state' => 'Stay Over', 'folio' => 'FOL-408-204', 'balance' => 19500]],
        ];

        // Merge DB rooms or use static rack if DB has few entries
        $rackRooms = $staticSampleRooms;

        $totalRooms = count($rackRooms);
        $occupiedCount = count(array_filter($rackRooms, fn($r) => $r['status'] === 'occupied'));
        $blockedCount = count(array_filter($rackRooms, fn($r) => $r['status'] === 'blocked'));
        $dirtyCount = count(array_filter($rackRooms, fn($r) => $r['status'] === 'dirty'));
        $availableCount = count(array_filter($rackRooms, fn($r) => $r['status'] === 'available'));
        $vacantCount = $availableCount + $dirtyCount;

        $stats = [
            'total' => $totalRooms,
            'occupied' => $occupiedCount,
            'blocked' => $blockedCount,
            'dirty' => $dirtyCount,
            'available' => $availableCount,
            'vacant' => $vacantCount,
            'expected_arrival' => 1,
            'expected_departure' => 5,
            'rooms_to_sale' => 36,
            'checked_in' => 3,
            'checked_out' => 6,
            'total_pax' => 18,
            'cleaned_ratio' => '2/9',
            'percentages' => [
                'occupied' => round(($occupiedCount / $totalRooms) * 100, 2),
                'available' => round(($availableCount / $totalRooms) * 100, 2),
                'blocked' => round(($blockedCount / $totalRooms) * 100, 2),
                'dirty' => round(($dirtyCount / $totalRooms) * 100, 2),
            ],
        ];

        return view('frontoffice.dashboard.index', compact(
            'rackRooms',
            'floors',
            'categories',
            'companies',
            'idCardTypes',
            'reservationModes',
            'paymentModes',
            'stats'
        ));
    }
}
