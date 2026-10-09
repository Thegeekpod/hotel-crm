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
use App\Models\BeddingConfig;
use App\Models\Guest;
use App\Models\HousekeepingState;
use App\Models\OperationalStatus;
use App\Models\RoomHousekeepingHistory;
use App\Models\RoomOperationalHistory;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $dbRooms = Room::with([
            'floorRelation',
            'categoryRelation',
            'beddingConfigRelation',
            'housekeepingHistories.housekeepingStatus',
            'operationalHistories.operationalStatus',
            'activeGuests.title',
            'activeGuests.nationality',
            'activeGuests.idCardType',
            'activeGuests.registrationType',
            'activeGuests.company',
            'currentGuest.title',
            'currentGuest.nationality',
            'currentGuest.idCardType',
            'currentGuest.registrationType',
            'currentGuest.company',
        ])->orderByRaw('CAST(room_number AS UNSIGNED) ASC, room_number ASC')->get();

        $floors = Floor::where('status', 'Active')->orderBy('floor', 'asc')->get();
        $categories = RoomCategory::where('status', 'Active')->get();
        $beddingConfigs = BeddingConfig::where('status', 'Active')->get();
        $amenitiesList = Amenity::where('status', 'Active')->get();
        $companies = Company::where('status', 'Active')->get();
        $idCardTypes = IdCardType::where('status', 'Active')->get();
        $reservationModes = ReservationMode::where('status', 'Active')->get();
        $paymentModes = PaymentMode::where('status', 'Active')->get();
        $discounts = Discount::where('status', 'Active')->orderBy('discount_percentage', 'asc')->get();
        $registrationTypes = RegistrationType::where('status', 'Active')->get();
        $titles = Title::where('status', 'Active')->get();
        $nationalities = Nationality::where('status', 'Active')->get();
        $housekeepingStates = HousekeepingState::where('status', 'Active')->orderBy('name', 'asc')->get();
        $operationalStatuses = OperationalStatus::where('status', 'Active')->orderBy('name', 'asc')->get();
        $amenityMap = Amenity::pluck('name', 'id')->toArray();
        $nextReserveId = Guest::generateNextReserveId();

        $rackRooms = [];
        if ($dbRooms->isNotEmpty()) {
            foreach ($dbRooms as $r) {
                $roomNum = (string)$r->room_number;
                $floor = (string)($r->floorRelation?->floor ?? $r->floor ?? '1');
                $type = strtoupper($r->categoryRelation?->name ?? $r->category ?? 'DELUXE');
                $rate = (float)($r->rate > 0 ? $r->rate : 4500);

                $latestOp = $r->operationalHistories->sortByDesc('id')->first();
                $latestHk = $r->housekeepingHistories->sortByDesc('id')->first();
                
                $activeGuests = $r->activeGuests ?? collect();
                if ($activeGuests->isEmpty() && $r->currentGuest) {
                    $activeGuests = collect([$r->currentGuest]);
                }

                // If current guest has reserve_id, also find any other guests with same reserve_id
                if ($r->currentGuest && $r->currentGuest->reserve_id) {
                    $resId = $r->currentGuest->reserve_id;
                    $linkedGuests = Guest::where('reserve_id', $resId)
                        ->whereIn('status', ['Confirmed', 'Arrived', 'Stay Over'])
                        ->with(['title', 'nationality', 'idCardType', 'registrationType', 'company'])
                        ->get();
                    if ($linkedGuests->isNotEmpty()) {
                        $activeGuests = $linkedGuests;
                    }
                }

                $guestsList = [];
                foreach ($activeGuests as $idx => $gModel) {
                    $fullName = ($gModel->title?->name ? $gModel->title->name . ' ' : '') . $gModel->guest_name;
                    $regName = $gModel->registrationType?->name ?? 'New';
                    $compName = $gModel->company?->name ?? $gModel->new_company_name ?? '';

                    $guestsList[] = [
                        'id' => $gModel->id,
                        'name' => $fullName,
                        'raw_name' => $gModel->guest_name,
                        'title' => $gModel->title?->name ?? '',
                        'state' => $gModel->status ?: 'Confirmed',
                        'folio' => $gModel->folio_number ?: ('FOL-' . $roomNum . '-100'),
                        'balance' => (float)$gModel->balance,
                        'mobile' => $gModel->mobile,
                        'email' => $gModel->email,
                        'city' => $gModel->city,
                        'address' => $gModel->guest_address,
                        'id_card_type' => $gModel->idCardType?->name,
                        'id_card_number' => $gModel->id_card_number,
                        'has_privilege_card' => (bool)$gModel->has_privilege_card,
                        'privilege_card_no' => $gModel->privilege_card_no,
                        'reserve_id' => $gModel->reserve_id,
                        'advance' => (float)$gModel->advance_amount,
                        'is_primary' => empty($gModel->primary_guest_id) || $idx === 0,
                        'registration_type_id' => $gModel->registration_type_id,
                        'registration_type' => $regName,
                        'company' => $compName,
                    ];
                }

                $guest = !empty($guestsList) ? $guestsList[0] : null;

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
                    'operational_status_id' => $latestOp?->operational_status_id,
                    'operational_status' => $opName ?: ($status === 'blocked' ? 'Out of Order / Blocked' : 'Active In-Service'),
                    'housekeeping_status_id' => $latestHk?->housekeeping_status_id,
                    'housekeeping_status' => $hkName ?: ($status === 'dirty' ? 'Dirty / Cleaning Due' : 'Cleaned & Inspected'),
                    'rate' => $rate,
                    'guest' => $guest,
                    'guests' => $guestsList,
                    'registration_type' => $guest ? ($guest['registration_type'] ?? 'New') : null,
                    'registration_type_id' => $guest ? ($guest['registration_type_id'] ?? null) : null,
                    'company' => $guest ? ($guest['company'] ?? null) : null,
                    'bedding' => $r->beddingConfigRelation?->name ?? $r->bedding_config ?? 'King Size Master (72x78)',
                    'bedding_id' => $r->bedding_config_id,
                    'max_adults' => (int)($r->beddingConfigRelation?->max_adults ?? 2),
                    'max_children' => (int)($r->beddingConfigRelation?->max_children ?? 1),
                    'max_pax' => (int)($r->beddingConfigRelation?->max_total ?? 3),
                    'amenity_ids' => is_array($r->amenities) ? array_map('strval', $r->amenities) : [],
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
            'expected_arrival' => Guest::where('status', 'Confirmed')->count() ?: 1,
            'expected_departure' => Guest::where('status', 'Stay Over')->count() ?: 5,
            'rooms_to_sale' => $vacantCount,
            'checked_in' => Guest::whereIn('status', ['Arrived', 'Stay Over'])->count() ?: $occupiedCount,
            'checked_out' => Guest::where('status', 'Checked Out')->count() ?: 0,
            'total_pax' => $totalPax > 0 ? $totalPax : 18,
            'cleaned_ratio' => $cleanRatio,
            'percentages' => [
                'occupied' => $totalRooms > 0 ? round(($occupiedCount / $totalRooms) * 100, 2) : 0,
                'available' => $totalRooms > 0 ? round(($availableCount / $totalRooms) * 100, 2) : 0,
                'blocked' => $totalRooms > 0 ? round(($blockedCount / $totalRooms) * 100, 2) : 0,
                'dirty' => $totalRooms > 0 ? round(($dirtyCount / $totalRooms) * 100, 2) : 0,
            ],
        ];

        $selectedStatus = strtolower($request->query('status', 'all'));
        $selectedType = strtoupper($request->query('type', 'ALL'));
        $selectedFloor = $request->query('floor', 'ALL');
        $selectedBedding = $request->query('bedding', 'ALL');
        $selectedPax = $request->query('pax', 'ALL');
        $selectedAmenity = $request->query('amenity', 'ALL');
        $searchQuery = trim($request->query('search', ''));

        // Filter $rackRooms collection based on backend request parameters
        $filteredRooms = array_filter($rackRooms, function($rm) use ($selectedStatus, $selectedType, $selectedFloor, $selectedBedding, $selectedPax, $selectedAmenity, $searchQuery) {
            // Status Filter
            if ($selectedStatus !== 'all' && $selectedStatus !== '') {
                if ($selectedStatus === 'vacant') {
                    if ($rm['status'] !== 'available' && $rm['status'] !== 'dirty') {
                        return false;
                    }
                } elseif ($rm['status'] !== $selectedStatus) {
                    return false;
                }
            }

            // Type / Category Filter
            if ($selectedType !== 'ALL' && $selectedType !== '') {
                $rmType = strtoupper($rm['type']);
                $rmCat = strtoupper($rm['category']);
                if ($rmType !== $selectedType && $rmCat !== $selectedType && strval($rm['category_id']) !== strval($selectedType)) {
                    return false;
                }
            }

            // Floor Filter
            if ($selectedFloor !== 'ALL' && $selectedFloor !== '') {
                if (strval($rm['floor']) !== strval($selectedFloor) && strval($rm['floor_id']) !== strval($selectedFloor)) {
                    return false;
                }
            }

            // Bedding Config Filter
            if ($selectedBedding !== 'ALL' && $selectedBedding !== '') {
                if (strval($rm['bedding_id']) !== strval($selectedBedding) && stripos($rm['bedding'], $selectedBedding) === false) {
                    return false;
                }
            }

            // Pax Capacity Filter
            if ($selectedPax !== 'ALL' && $selectedPax !== '') {
                if (str_ends_with($selectedPax, '+')) {
                    $minPax = (int)$selectedPax;
                    if ($rm['max_pax'] < $minPax) {
                        return false;
                    }
                } else {
                    if (strval($rm['max_pax']) !== strval($selectedPax)) {
                        return false;
                    }
                }
            }

            // Amenity Filter
            if ($selectedAmenity !== 'ALL' && $selectedAmenity !== '') {
                $hasAmenity = false;
                if (in_array(strval($selectedAmenity), $rm['amenity_ids'] ?? [], true)) {
                    $hasAmenity = true;
                } else {
                    foreach ($rm['amenities'] as $aName) {
                        if (stripos($aName, $selectedAmenity) !== false || strval($selectedAmenity) === strval($aName)) {
                            $hasAmenity = true;
                            break;
                        }
                    }
                }
                if (!$hasAmenity) {
                    return false;
                }
            }

            // Search Query (Room Number, Type, Guest Name, Bedding)
            if ($searchQuery !== '') {
                $q = strtolower($searchQuery);
                $matchRoom = str_contains(strtolower($rm['room']), $q);
                $matchType = str_contains(strtolower($rm['type']), $q) || str_contains(strtolower($rm['category']), $q);
                $matchGuest = !empty($rm['guest']['name']) && str_contains(strtolower($rm['guest']['name']), $q);
                $matchBedding = str_contains(strtolower($rm['bedding']), $q);

                if (!$matchRoom && !$matchType && !$matchGuest && !$matchBedding) {
                    return false;
                }
            }

            return true;
        });

        $rackRooms = array_values($filteredRooms);

        return view('frontoffice.dashboard.index', compact(
            'rackRooms',
            'floors',
            'categories',
            'beddingConfigs',
            'amenitiesList',
            'companies',
            'idCardTypes',
            'reservationModes',
            'paymentModes',
            'discounts',
            'registrationTypes',
            'titles',
            'nationalities',
            'housekeepingStates',
            'operationalStatuses',
            'stats',
            'nextReserveId',
            'selectedStatus',
            'selectedType',
            'selectedFloor',
            'selectedBedding',
            'selectedPax',
            'selectedAmenity',
            'searchQuery'
        ));
    }

    /**
     * Get dynamic next auto-generated reserve ID
     */
    public function getNextReserveId()
    {
        return response()->json([
            'success' => true,
            'reserve_id' => Guest::generateNextReserveId(),
        ]);
    }

    /**
     * Search regular guest profile by phone
     */
    public function searchGuest(Request $request)
    {
        $phone = $request->query('mobile') ?? $request->query('phone');
        if (!$phone) {
            return response()->json(['success' => false, 'message' => 'Please provide a valid mobile number'], 422);
        }

        $guest = Guest::where('mobile', 'like', "%{$phone}%")
            ->with(['title', 'nationality', 'idCardType'])
            ->latest('id')
            ->first();

        if ($guest) {
            return response()->json([
                'success' => true,
                'guest' => [
                    'name' => $guest->guest_name,
                    'title_id' => $guest->title_id,
                    'address' => $guest->guest_address,
                    'city' => $guest->city,
                    'mobile' => $guest->mobile,
                    'email' => $guest->email,
                    'dob' => $guest->dob?->format('Y-m-d'),
                    'anniversary' => $guest->anniversary?->format('Y-m-d'),
                    'nationality_id' => $guest->nationality_id,
                    'id_card_type_id' => $guest->id_card_type_id,
                    'id_card_number' => $guest->id_card_number,
                    'has_privilege_card' => (bool)$guest->has_privilege_card,
                    'privilege_card_no' => $guest->privilege_card_no,
                ]
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No prior profile found for this mobile number.']);
    }

    /**
     * Store new check-in / reservation dynamically into guests table
     */
    public function storeReservation(Request $request)
    {
        DB::beginTransaction();
        try {
            // Auto generate reserve_id if not provided or to ensure strict sequence
            $reserveId = Guest::generateNextReserveId();

            $registrationTypeId = $request->input('registration_type_id') ?? $request->input('res_type');
            if ($registrationTypeId && is_numeric($registrationTypeId)) {
                $registrationTypeId = (int)$registrationTypeId;
            } elseif ($registrationTypeId && is_string($registrationTypeId)) {
                $foundReg = RegistrationType::where('name', 'like', "%{$registrationTypeId}%")->first();
                $registrationTypeId = $foundReg?->id;
            } else {
                $defaultReg = RegistrationType::where('name', 'like', '%New%')->first() ?? RegistrationType::first();
                $registrationTypeId = $defaultReg?->id;
            }
            if ($registrationTypeId && !RegistrationType::where('id', $registrationTypeId)->exists()) {
                $registrationTypeId = null;
            }

            $companyId = $request->input('company_id');
            if ($companyId === 'new' || ($request->filled('new_company_name') && !$companyId)) {
                $newComp = Company::create([
                    'name' => $request->input('new_company_name'),
                    'address' => $request->input('new_company_address'),
                    'gstin' => $request->input('new_company_gstin'),
                    'phone' => $request->input('new_company_phone'),
                    'status' => 'Active',
                ]);
                $companyId = $newComp->id;
            } elseif (!is_numeric($companyId) || !Company::where('id', $companyId)->exists()) {
                $companyId = null;
            }

            $reservationModeId = $request->input('reservation_mode_id');
            $reservationModeId = ($reservationModeId && is_numeric($reservationModeId) && ReservationMode::where('id', $reservationModeId)->exists()) ? (int)$reservationModeId : null;

            $paymentModeId = $request->input('payment_mode_id');
            $paymentModeId = ($paymentModeId && is_numeric($paymentModeId) && PaymentMode::where('id', $paymentModeId)->exists()) ? (int)$paymentModeId : null;

            $advanceAmount = (float)($request->input('advance_amount', 0));
            $paymentRemarks = $request->input('payment_remarks');
            $reserveDate = $request->input('reserve_date', date('Y-m-d'));
            $reserveTime = $request->input('reserve_time', date('H:i'));
            $checkoutDate = $request->input('checkout_date', date('Y-m-d', strtotime('+1 day')));
            $checkoutTime = $request->input('checkout_time', '11:00');

            // Calculate stay duration (nights)
            $dIn = new \DateTime($reserveDate);
            $dOut = new \DateTime($checkoutDate);
            $diffNights = (int)$dIn->diff($dOut)->format('%r%a');
            $totalNights = max(1, $diffNights);

            // Discount calculation
            $discountId = $request->input('discount_id');
            $discountPercentage = 0.0;
            if ($discountId && is_numeric($discountId)) {
                $discountModel = Discount::find($discountId);
                if ($discountModel) {
                    $discountPercentage = (float)$discountModel->discount_percentage;
                    $discountId = $discountModel->id;
                } else {
                    $discountId = null;
                }
            } else {
                $discountId = null;
                $discountPercentage = (float)($request->input('discount_percentage', 0));
            }

            // Check if guests data is passed as array or flat inputs
            $rawGuests = $request->input('guests');
            if (!is_array($rawGuests) || empty($rawGuests)) {
                $rawGuests = [
                    [
                        'title_id' => $request->input('title_id'),
                        'guest_name' => $request->input('guest_name'),
                        'guest_address' => $request->input('guest_address'),
                        'nationality_id' => $request->input('nationality_id'),
                        'city' => $request->input('city'),
                        'mobile' => $request->input('mobile'),
                        'email' => $request->input('email'),
                        'dob' => $request->input('dob'),
                        'anniversary' => $request->input('anniversary'),
                        'status' => $request->input('status', 'Confirmed'),
                        'has_privilege_card' => $request->has('has_privilege_card') || $request->has('chk_privilege'),
                        'privilege_card_no' => $request->input('privilege_card_no'),
                        'room_id' => $request->input('room_id'),
                        'id_card_type_id' => $request->input('id_card_type_id'),
                        'id_card_number' => $request->input('id_card_number'),
                    ]
                ];
            }

            $primaryGuest = null;
            $savedGuests = [];

            foreach ($rawGuests as $idx => $gData) {
                if (empty($gData['guest_name'])) {
                    continue;
                }

                $roomId = $gData['room_id'] ?? null;
                $roomObj = null;
                if ($roomId) {
                    $roomObj = is_numeric($roomId) 
                        ? Room::find($roomId) 
                        : Room::where('room_number', (string)$roomId)->first();
                    $roomId = $roomObj?->id;
                }

                $roomRate = (float)($roomObj?->rate ?? 4500);
                $totalAmount = $roomRate * $totalNights;
                $discountAmount = round(($totalAmount * $discountPercentage) / 100.0, 2);
                $payableAmount = max(0, $totalAmount - $discountAmount);
                $calculatedBalance = max(0, $payableAmount - ($idx === 0 ? $advanceAmount : 0));
                $folioNo = Guest::generateFolioNumber($roomObj?->room_number ?? '100');

                $titleId = (!empty($gData['title_id']) && Title::where('id', $gData['title_id'])->exists()) ? (int)$gData['title_id'] : null;
                $nationalityId = (!empty($gData['nationality_id']) && Nationality::where('id', $gData['nationality_id'])->exists()) ? (int)$gData['nationality_id'] : null;
                $idCardTypeId = (!empty($gData['id_card_type_id']) && IdCardType::where('id', $gData['id_card_type_id'])->exists()) ? (int)$gData['id_card_type_id'] : null;

                $guestModel = Guest::create([
                    'reserve_id' => $reserveId,
                    'registration_type_id' => $registrationTypeId,
                    'reservation_mode_id' => $reservationModeId,
                    'company_id' => $companyId,
                    'new_company_name' => $request->input('new_company_name'),
                    'new_company_address' => $request->input('new_company_address'),
                    'new_company_gstin' => $request->input('new_company_gstin'),
                    'new_company_phone' => $request->input('new_company_phone'),
                    'reserve_date' => $reserveDate,
                    'reserve_time' => $reserveTime,
                    'checkout_date' => $checkoutDate,
                    'checkout_time' => $checkoutTime,
                    'total_nights' => $totalNights,
                    'room_rate' => $roomRate,
                    'total_amount' => $totalAmount,
                    'title_id' => $titleId,
                    'guest_name' => $gData['guest_name'],
                    'guest_address' => $gData['guest_address'] ?? null,
                    'nationality_id' => $nationalityId,
                    'city' => $gData['city'] ?? null,
                    'mobile' => $gData['mobile'] ?? null,
                    'email' => $gData['email'] ?? null,
                    'dob' => !empty($gData['dob']) ? $gData['dob'] : null,
                    'anniversary' => !empty($gData['anniversary']) ? $gData['anniversary'] : null,
                    'status' => $gData['status'] ?? 'Confirmed',
                    'has_privilege_card' => !empty($gData['has_privilege_card']),
                    'privilege_card_no' => $gData['privilege_card_no'] ?? null,
                    'room_id' => $roomId,
                    'id_card_type_id' => $idCardTypeId,
                    'id_card_number' => $gData['id_card_number'] ?? null,
                    'payment_mode_id' => ($idx === 0) ? $paymentModeId : null,
                    'discount_id' => ($idx === 0) ? $discountId : null,
                    'discount_percentage' => ($idx === 0) ? $discountPercentage : 0.0,
                    'discount_amount' => ($idx === 0) ? $discountAmount : 0.00,
                    'payable_amount' => ($idx === 0) ? $payableAmount : 0.00,
                    'advance_amount' => ($idx === 0) ? $advanceAmount : 0.00,
                    'payment_remarks' => ($idx === 0) ? $paymentRemarks : null,
                    'primary_guest_id' => $primaryGuest ? $primaryGuest->id : null,
                    'folio_number' => $folioNo,
                    'balance' => $calculatedBalance,
                ]);

                if ($idx === 0) {
                    $primaryGuest = $guestModel;
                }
                $savedGuests[] = $guestModel;

                // If room is assigned, update housekeeping & operational state to ready/in-service
                if ($roomObj) {
                    $cleanedState = HousekeepingState::where('name', 'like', '%Cleaned%')->first();
                    $activeOp = OperationalStatus::where('name', 'like', '%Active%')->first();

                    if ($cleanedState) {
                        RoomHousekeepingHistory::create([
                            'room_id' => $roomObj->id,
                            'housekeeping_status_id' => $cleanedState->id,
                            'start_time' => now(),
                            'completion_time' => now(),
                            'status' => 'complete',
                        ]);
                    }

                    if ($activeOp) {
                        RoomOperationalHistory::create([
                            'room_id' => $roomObj->id,
                            'operational_status_id' => $activeOp->id,
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reservation #' . $reserveId . ' saved successfully for ' . count($savedGuests) . ' guest(s)!',
                'reserve_id' => $reserveId,
                'next_reserve_id' => Guest::generateNextReserveId(),
                'guests' => $savedGuests,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Reservation store error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'Error saving reservation: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update Room Housekeeping & Operational Status dynamically and record history
     */
    public function updateRoomStatus(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'nullable',
            'room_no' => 'nullable',
            'housekeeping_status_id' => 'nullable|exists:housekeeping_states,id',
            'operational_status_id' => 'nullable|exists:operational_statuses,id',
        ]);

        $room = null;
        if (!empty($validated['room_id'])) {
            $room = is_numeric($validated['room_id']) ? Room::find($validated['room_id']) : null;
        }
        if (!$room && !empty($validated['room_no'])) {
            $room = Room::where('room_number', (string)$validated['room_no'])->first();
        }

        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'Room not found.',
            ], 404);
        }

        DB::beginTransaction();
        try {
            $hkName = '';
            $opName = '';

            if (!empty($validated['housekeeping_status_id'])) {
                $hkState = HousekeepingState::find($validated['housekeeping_status_id']);
                if ($hkState) {
                    $hkName = $hkState->name;
                    RoomHousekeepingHistory::create([
                        'room_id' => $room->id,
                        'housekeeping_status_id' => $hkState->id,
                        'start_time' => now(),
                        'completion_time' => now(),
                        'status' => 'complete',
                    ]);
                }
            } else {
                $latestHk = $room->housekeepingHistories()->latest('id')->first();
                $hkName = $latestHk?->housekeepingStatus?->name ?? 'Cleaned & Inspected';
            }

            if (!empty($validated['operational_status_id'])) {
                $opState = OperationalStatus::find($validated['operational_status_id']);
                if ($opState) {
                    $opName = $opState->name;
                    RoomOperationalHistory::create([
                        'room_id' => $room->id,
                        'operational_status_id' => $opState->id,
                    ]);
                }
            } else {
                $latestOp = $room->operationalHistories()->latest('id')->first();
                $opName = $latestOp?->operationalStatus?->name ?? 'Active In-Service';
            }

            // Determine calculated rack status
            $hasActiveGuests = $room->activeGuests()->exists();
            if ($hasActiveGuests) {
                $calculatedStatus = 'occupied';
                $cleaningText = 'Cleaned';
            } elseif (stripos($opName, 'Blocked') !== false || stripos($opName, 'Maintenance') !== false || stripos($opName, 'Order') !== false) {
                $calculatedStatus = 'blocked';
                $cleaningText = 'Blocked';
            } elseif (stripos($hkName, 'Dirty') !== false) {
                $calculatedStatus = 'dirty';
                $cleaningText = 'Dirty';
            } elseif ($room->status === 'Inactive') {
                $calculatedStatus = 'blocked';
                $cleaningText = 'Blocked';
            } else {
                $calculatedStatus = 'available';
                $cleaningText = 'Cleaned';
            }

            // Sync room model status
            if ($calculatedStatus === 'available' || $calculatedStatus === 'occupied') {
                $room->status = 'Active';
            } elseif ($calculatedStatus === 'blocked') {
                $room->status = 'Inactive';
            }
            $room->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Room #{$room->room_number} status updated successfully!",
                'room' => [
                    'id' => $room->id,
                    'room' => (string)$room->room_number,
                    'status' => $calculatedStatus,
                    'cleaning' => $cleaningText,
                    'housekeeping_status_id' => $validated['housekeeping_status_id'] ?? null,
                    'housekeeping_status' => $hkName,
                    'operational_status_id' => $validated['operational_status_id'] ?? null,
                    'operational_status' => $opName,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating room status: ' . $e->getMessage(),
            ], 500);
        }
    }
}
