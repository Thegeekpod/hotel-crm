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
use App\Models\RegistrationType;
use App\Models\Title;
use App\Models\Nationality;
use App\Models\Amenity;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $dbRooms = Room::with([
            'floorRelation',
            'categoryRelation',
            'beddingConfigRelation',
            'housekeepingHistories.housekeepingStatus',
            'operationalHistories.operationalStatus'
        ])->orderByRaw('CAST(room_number AS UNSIGNED) ASC, room_number ASC')->get();

        $floors = Floor::where('status', 'Active')->orderBy('floor', 'asc')->get();
        $categories = RoomCategory::where('status', 'Active')->get();
        $companies = Company::where('status', 'Active')->get();
        $idCardTypes = IdCardType::where('status', 'Active')->get();
        $reservationModes = ReservationMode::where('status', 'Active')->get();
        $paymentModes = PaymentMode::where('status', 'Active')->get();
        $registrationTypes = RegistrationType::where('status', 'Active')->get();
        $titles = Title::where('status', 'Active')->get();
        $nationalities = Nationality::where('status', 'Active')->get();
        $amenityMap = Amenity::pluck('name', 'id')->toArray();

        // Sample in-house residing guests mapping for active rooms
        $occupiedSampleGuests = [
            '102' => ['name' => 'Sanjeev Kumar Singh', 'state' => 'Arrived', 'folio' => 'FOL-102-882', 'balance' => 10400],
            '202' => ['name' => 'AJEET BHENGRA', 'state' => 'Stay Over', 'folio' => 'FOL-202-710', 'balance' => 7800],
            '205' => ['name' => 'KHAGESWAR ROUT', 'state' => 'Stay Over', 'folio' => 'FOL-205-551', 'balance' => 14200],
            '206' => ['name' => 'Raj kumar Bhunia', 'state' => 'Arrived', 'folio' => 'FOL-206-339', 'balance' => 3920],
            '302' => ['name' => 'SANGADA RAJUBHAI NAL', 'state' => 'Stay Over', 'folio' => 'FOL-302-991', 'balance' => 8100],
            '304' => ['name' => 'HITENDRA NINAWE', 'state' => 'Stay Over', 'folio' => 'FOL-304-102', 'balance' => 11900],
            '305' => ['name' => 'ADVAIT CHAVAN', 'state' => 'Stay Over', 'folio' => 'FOL-305-673', 'balance' => 12400],
            '307' => ['name' => 'BIKRAM KR SAHOO', 'state' => 'Stay Over', 'folio' => 'FOL-307-889', 'balance' => 16500],
            '401' => ['name' => 'Vikramaditya Roy', 'state' => 'Arrived', 'folio' => 'FOL-401-440', 'balance' => 14500],
            '403' => ['name' => 'Priyanka Mukherjee', 'state' => 'Stay Over', 'folio' => 'FOL-403-109', 'balance' => 6200],
            '405' => ['name' => 'Rahul Verma', 'state' => 'Arrived', 'folio' => 'FOL-405-772', 'balance' => 4100],
            '408' => ['name' => 'Dr. Ananya Sen', 'state' => 'Stay Over', 'folio' => 'FOL-408-204', 'balance' => 19500],
            '502' => ['name' => 'Rajendra Narayan Malhotra', 'state' => 'Arrived', 'folio' => 'FOL-502-301', 'balance' => 22000],
            '506' => ['name' => 'Sourav Ganguly', 'state' => 'Stay Over', 'folio' => 'FOL-506-440', 'balance' => 17800],
            '509' => ['name' => 'Meera Nambiar', 'state' => 'Arrived', 'folio' => 'FOL-509-912', 'balance' => 24500],
        ];

        $rackRooms = [];
        if ($dbRooms->isNotEmpty()) {
            foreach ($dbRooms as $r) {
                $roomNum = (string)$r->room_number;
                $floor = (string)($r->floorRelation?->floor ?? $r->floor ?? '1');
                $type = strtoupper($r->categoryRelation?->name ?? $r->category ?? 'DELUXE');
                $rate = (float)($r->rate > 0 ? $r->rate : 4500);

                $latestOp = $r->operationalHistories->sortByDesc('id')->first();
                $latestHk = $r->housekeepingHistories->sortByDesc('id')->first();
                $guest = $occupiedSampleGuests[$roomNum] ?? null;

                $opName = $latestOp?->operationalStatus?->name ?? '';
                $hkName = $latestHk?->housekeepingStatus?->name ?? '';

                if (stripos($opName, 'Blocked') !== false || stripos($opName, 'Maintenance') !== false || stripos($opName, 'Order') !== false) {
                    $status = 'blocked';
                    $cleaning = 'Blocked';
                } elseif ($guest) {
                    $status = 'occupied';
                    $cleaning = 'Cleaned';
                } elseif (stripos($hkName, 'Dirty') !== false || ($latestHk && $latestHk->status === 'pending')) {
                    $status = 'dirty';
                    $cleaning = 'Dirty';
                } elseif ($r->status === 'Inactive') {
                    $status = 'blocked';
                    $cleaning = 'Blocked';
                } else {
                    $status = 'available';
                    $cleaning = 'Cleaned';
                }

                $roomAmenitiesNames = [];
                if (is_array($r->amenities)) {
                    foreach ($r->amenities as $amnId) {
                        if (isset($amenityMap[$amnId])) {
                            $roomAmenitiesNames[] = $amenityMap[$amnId];
                        } elseif (is_string($amnId)) {
                            $roomAmenitiesNames[] = $amnId;
                        }
                    }
                }

                $rackRooms[] = [
                    'id' => $r->id,
                    'room' => $roomNum,
                    'floor_id' => $r->floor_id ?? ($r->floorRelation?->id),
                    'floor' => (string)$floor,
                    'floor_name' => $r->floorRelation?->name ?? ('Floor ' . $floor),
                    'category_id' => $r->category_id ?? ($r->categoryRelation?->id),
                    'category' => $r->categoryRelation?->name ?? $r->category ?? 'Deluxe',
                    'type' => $type,
                    'cleaning' => $cleaning,
                    'status' => $status,
                    'operational_status' => $opName ?: ($status === 'blocked' ? 'Out of Order / Blocked' : 'Active In-Service'),
                    'housekeeping_status' => $hkName ?: ($status === 'dirty' ? 'Dirty / Cleaning Due' : 'Cleaned & Inspected'),
                    'rate' => $rate,
                    'guest' => $guest,
                    'bedding' => $r->beddingConfigRelation?->name ?? $r->bedding_config ?? 'King Size',
                    'amenities' => $roomAmenitiesNames,
                ];
            }
        }

        $totalRooms = count($rackRooms);
        $occupiedCount = count(array_filter($rackRooms, fn($rm) => $rm['status'] === 'occupied'));
        $blockedCount = count(array_filter($rackRooms, fn($rm) => $rm['status'] === 'blocked'));
        $dirtyCount = count(array_filter($rackRooms, fn($rm) => $rm['status'] === 'dirty'));
        $availableCount = count(array_filter($rackRooms, fn($rm) => $rm['status'] === 'available'));
        $vacantCount = $availableCount + $dirtyCount;

        $totalPax = 0;
        foreach ($rackRooms as $rm) {
            if ($rm['status'] === 'occupied') {
                $totalPax += !empty($rm['guest']) ? 2 : 1;
            }
        }

        $cleanedCount = $availableCount + $occupiedCount;
        $cleanRatio = ($dirtyCount + $cleanedCount > 0) ? ($cleanedCount . '/' . ($dirtyCount + $cleanedCount)) : '0/0';

        $stats = [
            'total' => $totalRooms,
            'occupied' => $occupiedCount,
            'blocked' => $blockedCount,
            'dirty' => $dirtyCount,
            'available' => $availableCount,
            'vacant' => $vacantCount,
            'expected_arrival' => 1,
            'expected_departure' => 5,
            'rooms_to_sale' => $vacantCount,
            'checked_in' => 3,
            'checked_out' => 6,
            'total_pax' => $totalPax > 0 ? $totalPax : 18,
            'cleaned_ratio' => $cleanRatio,
            'percentages' => [
                'occupied' => $totalRooms > 0 ? round(($occupiedCount / $totalRooms) * 100, 2) : 0,
                'available' => $totalRooms > 0 ? round(($availableCount / $totalRooms) * 100, 2) : 0,
                'blocked' => $totalRooms > 0 ? round(($blockedCount / $totalRooms) * 100, 2) : 0,
                'dirty' => $totalRooms > 0 ? round(($dirtyCount / $totalRooms) * 100, 2) : 0,
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
            'registrationTypes',
            'titles',
            'nationalities',
            'stats'
        ));
    }
}

