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
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Settlement;
use App\Models\Checkout;
use App\Models\HousekeepingState;
use App\Models\OperationalStatus;
use App\Models\RoomHousekeepingHistory;
use App\Models\RoomOperationalHistory;
use App\Models\GstPercentage;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Date filter handling: default to today (now()->toDateString())
        $selectedDate = $request->query('date', now()->toDateString());
        try {
            $selectedCarbon = \Carbon\Carbon::parse($selectedDate);
            $selectedDate = $selectedCarbon->toDateString();
        } catch (\Exception $e) {
            $selectedDate = now()->toDateString();
            $selectedCarbon = \Carbon\Carbon::parse($selectedDate);
        }
        $isToday = ($selectedDate === now()->toDateString());
        $prevDate = $selectedCarbon->copy()->subDay()->toDateString();
        $nextDate = $selectedCarbon->copy()->addDay()->toDateString();

        // Calculate dynamic shift based on current hour
        $hour = (int)date('H');
        if ($hour >= 7 && $hour < 15) {
            $currentShift = 'Morning Shift (07:00 - 15:00)';
            $shiftIcon = 'fa-sun';
            $shiftColor = '#fbc531';
        } elseif ($hour >= 15 && $hour < 23) {
            $currentShift = 'Evening Shift (15:00 - 23:00)';
            $shiftIcon = 'fa-cloud-sun';
            $shiftColor = '#f97316';
        } else {
            $currentShift = 'Night Shift (23:00 - 07:00)';
            $shiftIcon = 'fa-moon';
            $shiftColor = '#a78bfa';
        }

        $dbRooms = Room::with([
            'floorRelation',
            'categoryRelation',
            'beddingConfigRelation',
            'planRelation',
            'housekeepingHistories.housekeepingStatus',
            'operationalHistories.operationalStatus',
        ])->orderByRaw('CAST(room_number AS UNSIGNED) ASC, room_number ASC')->get();

        // Preload all active bookings covering selectedDate grouped by room_id
        $activeGuestsByRoom = Booking::where(function ($q) use ($selectedDate) {
            $q->whereDate('reserve_date', '<=', $selectedDate)
              ->where(function ($co) use ($selectedDate) {
                  $co->whereDate('checkout_date', '>=', $selectedDate)
                     ->orWhereNull('checkout_date');
              });
        })
        ->whereIn('status', ['Confirmed', 'Arrived', 'Stay Over', 'Checked In', 'Settlement Done'])
        ->with(['guest.title', 'guest.nationality', 'guest.idCardType', 'registrationType', 'company', 'plan', 'roomServiceOrders.items'])
        ->orderByRaw("CASE WHEN status = 'Checked In' THEN 1 WHEN status = 'Stay Over' THEN 2 WHEN status = 'Arrived' THEN 3 WHEN status = 'Confirmed' THEN 4 ELSE 5 END")
        ->orderBy('id', 'desc')
        ->get()
        ->groupBy('room_id');

        $floors = Floor::where('status', 'Active')->orderBy('floor', 'asc')->get();
        $categories = RoomCategory::where('status', 'Active')->get();
        $beddingConfigs = BeddingConfig::where('status', 'Active')->get();
        $amenitiesList = Amenity::where('status', 'Active')->get();
        $companies = Company::where('status', 'Active')->get();
        $idCardTypes = IdCardType::where('status', 'Active')->get();
        $reservationModes = ReservationMode::where('status', 'Active')->get();
        $paymentModes = PaymentMode::where('status', 'Active')->get();
        $plans = Plan::where('status', 'Active')->get();
        $registrationTypes = RegistrationType::where('status', 'Active')->get();
        $titles = Title::where('status', 'Active')->get();
        $nationalities = Nationality::where('status', 'Active')->get();
        $housekeepingStates = HousekeepingState::where('status', 'Active')->orderBy('name', 'asc')->get();
        $operationalStatuses = OperationalStatus::where('status', 'Active')->orderBy('name', 'asc')->get();
        $amenityMap = Amenity::pluck('name', 'id')->toArray();
        $nextReserveId = Booking::generateNextReserveId();

        $rackRooms = [];
        if ($dbRooms->isNotEmpty()) {
            foreach ($dbRooms as $r) {
                $roomNum = (string)$r->room_number;
                $floor = (string)($r->floorRelation?->floor ?? $r->floor ?? '1');
                $type = strtoupper($r->categoryRelation?->name ?? $r->category ?? 'DELUXE');
                $rate = (float)($r->rate > 0 ? $r->rate : 4500);

                $latestOp = $r->operationalHistories->sortByDesc('id')->first();
                $latestHk = $r->housekeepingHistories->sortByDesc('id')->first();
                
                // Get active guest(s) for this room on the selected date
                $activeGuests = $activeGuestsByRoom->get($r->id, collect());

                // If current guest has reserve_id, also find any other linked guests with same reserve_id
                $firstActiveGuest = $activeGuests->first();
                if ($firstActiveGuest && $firstActiveGuest->reserve_id) {
                    $resId = $firstActiveGuest->reserve_id;
                    $linkedGuests = Booking::where('reserve_id', $resId)
                        ->where('room_id', $r->id)
                        ->whereIn('status', ['Confirmed', 'Arrived', 'Stay Over', 'Checked In', 'Settlement Done'])
                        ->with(['guest.title', 'guest.nationality', 'guest.idCardType', 'registrationType', 'company', 'plan', 'roomServiceOrders.items'])
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

                    $resDateStr = $gModel->reserve_date ? (is_string($gModel->reserve_date) ? date('d M, Y', strtotime($gModel->reserve_date)) : $gModel->reserve_date->format('d M, Y')) : null;
                    $resTimeStr = $gModel->reserve_time ? date('h:i A', strtotime($gModel->reserve_time)) : null;
                    $outDateStr = $gModel->checkout_date ? (is_string($gModel->checkout_date) ? date('d M, Y', strtotime($gModel->checkout_date)) : $gModel->checkout_date->format('d M, Y')) : null;
                    $outTimeStr = $gModel->checkout_time ? date('h:i A', strtotime($gModel->checkout_time)) : null;

                    $checkinDisplay = $resDateStr ? ($resDateStr . ($resTimeStr ? ' • ' . $resTimeStr : '')) : null;
                    $checkoutDisplay = $outDateStr ? ($outDateStr . ($outTimeStr ? ' • ' . $outTimeStr : '')) : null;

                    // Query room service & extra services orders for this booking/room
                    $resBookingIds = collect([$gModel->id]);
                    if ($gModel->reserve_id) {
                        $otherResIds = Booking::where('reserve_id', $gModel->reserve_id)->pluck('id');
                        $resBookingIds = $resBookingIds->concat($otherResIds)->unique();
                    }

                    $roomServiceOrders = \App\Models\RoomServiceOrder::with('items')
                        ->where(function ($q) use ($resBookingIds, $r, $gModel) {
                            $q->whereIn('booking_id', $resBookingIds);
                            if ($r && $r->id) {
                                $q->orWhere(function ($sq) use ($r, $gModel) {
                                    $sq->where('room_id', $r->id);
                                    if ($gModel->reserve_date) {
                                        $sq->whereDate('created_at', '>=', $gModel->reserve_date);
                                    }
                                    if ($gModel->checkout_date) {
                                        $sq->whereDate('created_at', '<=', $gModel->checkout_date);
                                    }
                                });
                            }
                        })
                        ->where('order_status', '!=', 'Cancelled')
                        ->latest()
                        ->get();

                    $extraServiceCharges = (float) $roomServiceOrders->where('payment_status', 'Charged to Room Folio')->sum('total_amount');
                    $serviceOrdersList = $roomServiceOrders->map(function ($ro) {
                        return [
                            'id' => $ro->id,
                            'order_number' => $ro->order_number,
                            'room_number' => $ro->room_number,
                            'table_number' => $ro->table_number,
                            'guest_name' => $ro->guest_name,
                            'date' => $ro->created_at ? $ro->created_at->format('d M Y, h:i A') : 'N/A',
                            'subtotal' => (float)$ro->subtotal,
                            'tax_amount' => (float)$ro->tax_amount,
                            'total_amount' => (float)$ro->total_amount,
                            'payment_status' => $ro->payment_status,
                            'order_status' => $ro->order_status,
                            'items' => $ro->items->map(function ($it) {
                                return [
                                    'item_name' => $it->item_name,
                                    'unit_price' => (float)$it->unit_price,
                                    'quantity' => (int)$it->quantity,
                                    'line_total' => (float)$it->line_total,
                                ];
                            })->all(),
                        ];
                    })->values()->all();

                    $guestsList[] = [
                        'id' => $gModel->id,
                        'name' => $fullName,
                        'raw_name' => $gModel->guest_name,
                        'title' => $gModel->title?->name ?? '',
                        'state' => $gModel->status ?: 'Confirmed',
                        'folio' => $gModel->settle_number ?: $gModel->folio_number ?: ('SET-' . $roomNum . '-100'),
                        'settle_number' => $gModel->settle_number ?: $gModel->folio_number ?: ('SET-' . $roomNum . '-100'),
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
                        'total_amount' => (float)$gModel->total_amount,
                        'discount_percentage' => (float)$gModel->discount_percentage,
                        'discount_amount' => (float)$gModel->discount_amount,
                        'gst_type' => $gModel->gst_type ?: 'exclusive',
                        'gst_percentage' => (float)$gModel->gst_percentage,
                        'gst_amount' => (float)$gModel->gst_amount,
                        'payable_amount' => (float)$gModel->payable_amount,
                        'is_primary' => empty($gModel->primary_guest_id) || $idx === 0,
                        'registration_type_id' => $gModel->registration_type_id,
                        'registration_type' => $regName,
                        'company' => $compName,
                        'plan_id' => $gModel->plan_id,
                        'plan_name' => $gModel->plan_name ?: ($gModel->plan?->plan_name ?? ''),
                        'plan_price' => (float)($gModel->plan_price ?? 0),
                        'plan_original_price' => (float)($gModel->plan_original_price ?? ($gModel->plan_price ?? 0)),
                        'plan_discount_amount' => (float)($gModel->plan_discount_amount ?? 0),
                        'reserve_date' => $resDateStr,
                        'reserve_time' => $resTimeStr,
                        'checkout_date' => $outDateStr,
                        'checkout_time' => $outTimeStr,
                        'checkin_display' => $checkinDisplay,
                        'checkout_display' => $checkoutDisplay,
                        'total_nights' => $gModel->total_nights,
                        'extra_service_charges' => $extraServiceCharges,
                        'room_service_orders' => $serviceOrdersList,
                    ];
                }

                $guest = !empty($guestsList) ? $guestsList[0] : null;

                $opName = $latestOp?->operationalStatus?->name ?? '';
                $hkName = $latestHk?->housekeepingStatus?->name ?? '';

                $isOpBlocked = (bool)preg_match('/block|blok|maintenance|order/i', $opName);

                if ($isOpBlocked) {
                    $status = 'blocked';
                    $cleaning = 'Blocked';
                } elseif ($guest) {
                    $status = 'occupied';
                    $cleaning = 'Cleaned';
                } elseif (stripos($hkName, 'Dirty') !== false || ($latestHk && $latestHk->status === 'pending')) {
                    $status = 'dirty';
                    $cleaning = 'Dirty';
                } elseif ($r->status === 'Inactive' && empty($opName)) {
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
                    'plan_id' => $r->plan_id,
                    'plan_name' => $r->planRelation?->plan_name,
                    'plan_price' => (float)($r->planRelation?->price ?? 0),
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
                    'checkin_display' => $guest ? ($guest['checkin_display'] ?? null) : null,
                    'checkout_display' => $guest ? ($guest['checkout_display'] ?? null) : null,
                    'reserve_date' => $guest ? ($guest['reserve_date'] ?? null) : null,
                    'reserve_time' => $guest ? ($guest['reserve_time'] ?? null) : null,
                    'checkout_date' => $guest ? ($guest['checkout_date'] ?? null) : null,
                    'checkout_time' => $guest ? ($guest['checkout_time'] ?? null) : null,
                    'total_nights' => $guest ? ($guest['total_nights'] ?? null) : null,
                    'bedding' => $r->beddingConfigRelation?->name ?? $r->bedding_config ?? 'King Size Master (72x78)',
                    'bedding_id' => $r->bedding_config_id,
                    'max_adults' => (int)($r->beddingConfigRelation?->max_adults ?? 2),
                    'max_children' => (int)($r->beddingConfigRelation?->max_children ?? 1),
                    'max_pax' => (int)($r->beddingConfigRelation?->max_total ?? 3),
                    'amenity_ids' => is_array($r->amenities) ? array_map('strval', $r->amenities) : [],
                    'amenities' => $roomAmenitiesNames,
                    'extra_service_charges' => $guest ? ($guest['extra_service_charges'] ?? 0) : 0,
                    'room_service_orders' => $guest ? ($guest['room_service_orders'] ?? []) : [],
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
            'expected_arrival' => Booking::whereDate('reserve_date', $selectedDate)->where('status', 'Confirmed')->count(),
            'expected_departure' => Booking::whereDate('checkout_date', $selectedDate)->whereIn('status', ['Stay Over', 'Arrived', 'Checked In', 'Settlement Done'])->count(),
            'rooms_to_sale' => $vacantCount,
            'checked_in' => Booking::whereDate('reserve_date', $selectedDate)->whereIn('status', ['Arrived', 'Stay Over', 'Checked In', 'Settlement Done'])->count(),
            'checked_out' => Booking::whereDate('checkout_date', $selectedDate)->where('status', 'Checked Out')->count(),
            'total_pax' => $totalPax > 0 ? $totalPax : 0,
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
        $gstPercentages = GstPercentage::where('status', 'Active')->orderBy('gst_percentage', 'asc')->get();

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
            'plans',
            'gstPercentages',
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
            'searchQuery',
            'selectedDate',
            'isToday',
            'prevDate',
            'nextDate',
            'currentShift',
            'shiftIcon',
            'shiftColor'
        ));
    }

    /**
     * Dedicated Reserve Page: View only booked / checked-in rooms with From & To date filter
     */
    public function reserve(Request $request)
    {
        $today = now()->toDateString();
        $fromDate = $request->query('from_date', $request->query('date', $today));
        $toDate = $request->query('to_date', $request->query('date', $today));

        try {
            $fromCarbon = \Carbon\Carbon::parse($fromDate);
            $fromDate = $fromCarbon->toDateString();
        } catch (\Exception $e) {
            $fromDate = $today;
            $fromCarbon = \Carbon\Carbon::parse($fromDate);
        }

        try {
            $toCarbon = \Carbon\Carbon::parse($toDate);
            $toDate = $toCarbon->toDateString();
        } catch (\Exception $e) {
            $toDate = $fromDate;
            $toCarbon = \Carbon\Carbon::parse($toDate);
        }

        // Ensure fromDate <= toDate
        if ($fromDate > $toDate) {
            $temp = $fromDate;
            $fromDate = $toDate;
            $toDate = $temp;
            $fromCarbon = \Carbon\Carbon::parse($fromDate);
            $toCarbon = \Carbon\Carbon::parse($toDate);
        }

        $isToday = ($fromDate === $today && $toDate === $today);

        // Calculate dynamic shift based on current hour
        $hour = (int)date('H');
        if ($hour >= 7 && $hour < 15) {
            $currentShift = 'Morning Shift (07:00 - 15:00)';
            $shiftIcon = 'fa-sun';
            $shiftColor = '#fbc531';
        } elseif ($hour >= 15 && $hour < 23) {
            $currentShift = 'Evening Shift (15:00 - 23:00)';
            $shiftIcon = 'fa-cloud-sun';
            $shiftColor = '#f97316';
        } else {
            $currentShift = 'Night Shift (23:00 - 07:00)';
            $shiftIcon = 'fa-moon';
            $shiftColor = '#a78bfa';
        }

        $dbRooms = Room::with([
            'floorRelation',
            'categoryRelation',
            'beddingConfigRelation',
            'planRelation',
            'housekeepingHistories.housekeepingStatus',
            'operationalHistories.operationalStatus',
        ])->orderByRaw('CAST(room_number AS UNSIGNED) ASC, room_number ASC')->get();

        $totalRoomsCount = $dbRooms->count();

        // Preload all active bookings covering the from_date to to_date range grouped by room_id
        $activeGuestsByRoom = Booking::where(function ($q) use ($fromDate, $toDate) {
            $q->whereDate('reserve_date', '<=', $toDate)
              ->where(function ($co) use ($fromDate) {
                  $co->whereDate('checkout_date', '>=', $fromDate)
                     ->orWhereNull('checkout_date');
              });
        })
        ->whereIn('status', ['Confirmed', 'Arrived', 'Stay Over', 'Checked In', 'Settlement Done'])
        ->whereNotNull('room_id')
        ->with(['guest.title', 'guest.nationality', 'guest.idCardType', 'registrationType', 'company', 'plan'])
        ->orderByRaw("CASE WHEN status = 'Checked In' THEN 1 WHEN status = 'Stay Over' THEN 2 WHEN status = 'Arrived' THEN 3 WHEN status = 'Confirmed' THEN 4 ELSE 5 END")
        ->orderBy('id', 'desc')
        ->get()
        ->groupBy('room_id');

        $floors = Floor::where('status', 'Active')->orderBy('floor', 'asc')->get();
        $categories = RoomCategory::where('status', 'Active')->get();
        $beddingConfigs = BeddingConfig::where('status', 'Active')->get();
        $amenitiesList = Amenity::where('status', 'Active')->get();
        $companies = Company::where('status', 'Active')->get();
        $idCardTypes = IdCardType::where('status', 'Active')->get();
        $reservationModes = ReservationMode::where('status', 'Active')->get();
        $paymentModes = PaymentMode::where('status', 'Active')->get();
        $plans = Plan::where('status', 'Active')->get();
        $registrationTypes = RegistrationType::where('status', 'Active')->get();
        $titles = Title::where('status', 'Active')->get();
        $nationalities = Nationality::where('status', 'Active')->get();
        $housekeepingStates = HousekeepingState::where('status', 'Active')->orderBy('name', 'asc')->get();
        $operationalStatuses = OperationalStatus::where('status', 'Active')->orderBy('name', 'asc')->get();
        $amenityMap = Amenity::pluck('name', 'id')->toArray();
        $nextReserveId = Booking::generateNextReserveId();

        $rackRooms = [];
        if ($dbRooms->isNotEmpty()) {
            foreach ($dbRooms as $r) {
                // In Reserve view: ONLY include rooms that are booked / checked-in!
                if (!$activeGuestsByRoom->has($r->id)) {
                    continue;
                }

                $activeGuests = $activeGuestsByRoom->get($r->id, collect());
                if ($activeGuests->isEmpty()) {
                    continue;
                }

                $roomNum = (string)$r->room_number;
                $floor = (string)($r->floorRelation?->floor ?? $r->floor ?? '1');
                $type = strtoupper($r->categoryRelation?->name ?? $r->category ?? 'DELUXE');
                $rate = (float)($r->rate > 0 ? $r->rate : 4500);

                $latestOp = $r->operationalHistories->sortByDesc('id')->first();
                $latestHk = $r->housekeepingHistories->sortByDesc('id')->first();

                // If current guest has reserve_id, also find any other linked guests with same reserve_id
                $firstActiveGuest = $activeGuests->first();
                if ($firstActiveGuest && $firstActiveGuest->reserve_id) {
                    $resId = $firstActiveGuest->reserve_id;
                    $linkedGuests = Booking::where('reserve_id', $resId)
                        ->where('room_id', $r->id)
                        ->whereIn('status', ['Confirmed', 'Arrived', 'Stay Over', 'Checked In', 'Settlement Done'])
                        ->with(['guest.title', 'guest.nationality', 'guest.idCardType', 'registrationType', 'company'])
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

                    $resDateStr = $gModel->reserve_date ? (is_string($gModel->reserve_date) ? date('d M, Y', strtotime($gModel->reserve_date)) : $gModel->reserve_date->format('d M, Y')) : null;
                    $resTimeStr = $gModel->reserve_time ? date('h:i A', strtotime($gModel->reserve_time)) : null;
                    $outDateStr = $gModel->checkout_date ? (is_string($gModel->checkout_date) ? date('d M, Y', strtotime($gModel->checkout_date)) : $gModel->checkout_date->format('d M, Y')) : null;
                    $outTimeStr = $gModel->checkout_time ? date('h:i A', strtotime($gModel->checkout_time)) : null;

                    $checkinDisplay = $resDateStr ? ($resDateStr . ($resTimeStr ? ' • ' . $resTimeStr : '')) : null;
                    $checkoutDisplay = $outDateStr ? ($outDateStr . ($outTimeStr ? ' • ' . $outTimeStr : '')) : null;

                    $guestsList[] = [
                        'id' => $gModel->id,
                        'name' => $fullName,
                        'raw_name' => $gModel->guest_name,
                        'title' => $gModel->title?->name ?? '',
                        'state' => $gModel->status ?: 'Confirmed',
                        'folio' => $gModel->settle_number ?: $gModel->folio_number ?: ('SET-' . $roomNum . '-100'),
                        'settle_number' => $gModel->settle_number ?: $gModel->folio_number ?: ('SET-' . $roomNum . '-100'),
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
                        'total_amount' => (float)$gModel->total_amount,
                        'discount_percentage' => (float)$gModel->discount_percentage,
                        'discount_amount' => (float)$gModel->discount_amount,
                        'gst_type' => $gModel->gst_type ?: 'exclusive',
                        'gst_percentage' => (float)$gModel->gst_percentage,
                        'gst_amount' => (float)$gModel->gst_amount,
                        'payable_amount' => (float)$gModel->payable_amount,
                        'is_primary' => empty($gModel->primary_guest_id) || $idx === 0,
                        'registration_type_id' => $gModel->registration_type_id,
                        'registration_type' => $regName,
                        'company' => $compName,
                        'plan_id' => $gModel->plan_id,
                        'plan_name' => $gModel->plan_name ?: ($gModel->plan?->plan_name ?? ''),
                        'plan_price' => (float)($gModel->plan_price ?? 0),
                        'plan_original_price' => (float)($gModel->plan_original_price ?? ($gModel->plan_price ?? 0)),
                        'plan_discount_amount' => (float)($gModel->plan_discount_amount ?? 0),
                        'reserve_date' => $resDateStr,
                        'reserve_time' => $resTimeStr,
                        'checkout_date' => $outDateStr,
                        'checkout_time' => $outTimeStr,
                        'checkin_display' => $checkinDisplay,
                        'checkout_display' => $checkoutDisplay,
                        'total_nights' => $gModel->total_nights,
                    ];
                }

                $guest = !empty($guestsList) ? $guestsList[0] : null;

                $opName = $latestOp?->operationalStatus?->name ?? '';
                $hkName = $latestHk?->housekeepingStatus?->name ?? '';

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

                $guestState = $guest['state'] ?? 'Occupied';

                $rackRooms[] = [
                    'id' => $r->id,
                    'room' => $roomNum,
                    'floor_id' => $r->floor_id ?? ($r->floorRelation?->id),
                    'floor' => (string)$floor,
                    'floor_name' => $r->floorRelation?->name ?? ('Floor ' . $floor),
                    'category_id' => $r->category_id ?? ($r->categoryRelation?->id),
                    'category' => $r->categoryRelation?->name ?? $r->category ?? 'Deluxe',
                    'plan_id' => $r->plan_id,
                    'plan_name' => $r->planRelation?->plan_name,
                    'plan_price' => (float)($r->planRelation?->price ?? 0),
                    'type' => $type,
                    'cleaning' => 'Cleaned',
                    'status' => 'occupied',
                    'guest_status' => $guestState,
                    'operational_status_id' => $latestOp?->operational_status_id,
                    'operational_status' => $opName ?: 'Active In-Service',
                    'housekeeping_status_id' => $latestHk?->housekeeping_status_id,
                    'housekeeping_status' => $hkName ?: 'Cleaned & Inspected',
                    'rate' => $rate,
                    'guest' => $guest,
                    'guests' => $guestsList,
                    'registration_type' => $guest ? ($guest['registration_type'] ?? 'New') : null,
                    'registration_type_id' => $guest ? ($guest['registration_type_id'] ?? null) : null,
                    'company' => $guest ? ($guest['company'] ?? null) : null,
                    'checkin_display' => $guest ? ($guest['checkin_display'] ?? null) : null,
                    'checkout_display' => $guest ? ($guest['checkout_display'] ?? null) : null,
                    'reserve_date' => $guest ? ($guest['reserve_date'] ?? null) : null,
                    'reserve_time' => $guest ? ($guest['reserve_time'] ?? null) : null,
                    'checkout_date' => $guest ? ($guest['checkout_date'] ?? null) : null,
                    'checkout_time' => $guest ? ($guest['checkout_time'] ?? null) : null,
                    'total_nights' => $guest ? ($guest['total_nights'] ?? null) : null,
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

        // Stats for Reserve Page
        $totalBooked = count($rackRooms);
        $checkedInCount = count(array_filter($rackRooms, fn($rm) => strtolower($rm['guest_status'] ?? '') === 'checked in'));
        $stayOverCount = count(array_filter($rackRooms, fn($rm) => strtolower($rm['guest_status'] ?? '') === 'stay over'));
        $confirmedCount = count(array_filter($rackRooms, fn($rm) => strtolower($rm['guest_status'] ?? '') === 'confirmed'));
        $arrivedCount = count(array_filter($rackRooms, fn($rm) => strtolower($rm['guest_status'] ?? '') === 'arrived'));

        $totalPax = 0;
        foreach ($rackRooms as $rm) {
            $totalPax += !empty($rm['guest']) ? 2 : 1;
        }

        $stats = [
            'total_booked' => $totalBooked,
            'total_rooms' => $totalRoomsCount,
            'checked_in' => $checkedInCount,
            'stay_over' => $stayOverCount,
            'confirmed' => $confirmedCount,
            'arrived' => $arrivedCount,
            'total_pax' => $totalPax,
            'expected_arrival' => Booking::whereDate('reserve_date', '>=', $fromDate)->whereDate('reserve_date', '<=', $toDate)->where('status', 'Confirmed')->count(),
            'expected_departure' => Booking::whereDate('checkout_date', '>=', $fromDate)->whereDate('checkout_date', '<=', $toDate)->whereIn('status', ['Stay Over', 'Arrived', 'Checked In', 'Settlement Done'])->count(),
            'occupancy_percentage' => $totalRoomsCount > 0 ? round(($totalBooked / $totalRoomsCount) * 100, 1) : 0,
            'percentages' => [
                'checked_in' => $totalBooked > 0 ? round(($checkedInCount / $totalBooked) * 100, 1) : 0,
                'stay_over' => $totalBooked > 0 ? round(($stayOverCount / $totalBooked) * 100, 1) : 0,
                'confirmed' => $totalBooked > 0 ? round(($confirmedCount / $totalBooked) * 100, 1) : 0,
                'arrived' => $totalBooked > 0 ? round(($arrivedCount / $totalBooked) * 100, 1) : 0,
            ],
        ];

        $selectedStatus = strtolower($request->query('status', 'all'));
        $selectedType = strtoupper($request->query('type', 'ALL'));
        $selectedFloor = $request->query('floor', 'ALL');
        $selectedBedding = $request->query('bedding', 'ALL');
        $selectedPax = $request->query('pax', 'ALL');
        $selectedAmenity = $request->query('amenity', 'ALL');
        $searchQuery = trim($request->query('search', ''));

        // Apply filters
        $filteredRooms = array_filter($rackRooms, function($rm) use ($selectedStatus, $selectedType, $selectedFloor, $selectedBedding, $selectedPax, $selectedAmenity, $searchQuery) {
            // Status Filter (checked in, stay over, confirmed, arrived)
            if ($selectedStatus !== 'all' && $selectedStatus !== '') {
                $rmStatus = strtolower($rm['guest_status'] ?? '');
                if ($rmStatus !== $selectedStatus) {
                    return false;
                }
            }

            // Type Filter
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

            // Bedding Filter
            if ($selectedBedding !== 'ALL' && $selectedBedding !== '') {
                if (strval($rm['bedding_id']) !== strval($selectedBedding) && stripos($rm['bedding'], $selectedBedding) === false) {
                    return false;
                }
            }

            // Pax Filter
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

            // Search Query
            if ($searchQuery !== '') {
                $q = strtolower($searchQuery);
                $matchRoom = str_contains(strtolower($rm['room']), $q);
                $matchType = str_contains(strtolower($rm['type']), $q) || str_contains(strtolower($rm['category']), $q);
                $matchGuest = !empty($rm['guest']['name']) && str_contains(strtolower($rm['guest']['name']), $q);
                $matchBedding = str_contains(strtolower($rm['bedding']), $q);
                $matchFolio = !empty($rm['guest']['folio']) && str_contains(strtolower($rm['guest']['folio']), $q);
                $matchReserveId = !empty($rm['guest']['reserve_id']) && str_contains(strtolower($rm['guest']['reserve_id']), $q);

                if (!$matchRoom && !$matchType && !$matchGuest && !$matchBedding && !$matchFolio && !$matchReserveId) {
                    return false;
                }
            }

            return true;
        });

        $rackRooms = array_values($filteredRooms);
        $gstPercentages = GstPercentage::where('status', 'Active')->orderBy('gst_percentage', 'asc')->get();

        return view('frontoffice.reserve.index', compact(
            'rackRooms',
            'floors',
            'categories',
            'beddingConfigs',
            'amenitiesList',
            'companies',
            'idCardTypes',
            'reservationModes',
            'paymentModes',
            'plans',
            'gstPercentages',
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
            'searchQuery',
            'fromDate',
            'toDate',
            'today',
            'isToday',
            'currentShift',
            'shiftIcon',
            'shiftColor'
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
     * Check real-time room availability & conflict for given dates and suggest matching alternative rooms
     */
    public function checkRoomAvailability(Request $request)
    {
        $roomId = $request->query('room_id');
        $checkIn = $request->query('checkin_date');
        $checkOut = $request->query('checkout_date');

        if (empty($checkIn) || empty($checkOut)) {
            return response()->json(['available' => true]);
        }

        try {
            $checkInDate = \Carbon\Carbon::parse($checkIn)->toDateString();
            $checkOutDate = \Carbon\Carbon::parse($checkOut)->toDateString();
        } catch (\Exception $e) {
            return response()->json(['available' => true]);
        }

        if ($checkInDate >= $checkOutDate) {
            $checkOutDate = \Carbon\Carbon::parse($checkInDate)->addDay()->toDateString();
        }

        // Find conflicting bookings across all rooms for this date window
        $conflictingGuests = Booking::whereNotNull('room_id')
            ->whereIn('status', ['Confirmed', 'Checked In', 'Arrived', 'Stay Over'])
            ->where(function ($q) use ($checkInDate, $checkOutDate) {
                $q->whereDate('reserve_date', '<', $checkOutDate)
                  ->where(function ($sub) use ($checkInDate) {
                      $sub->whereDate('checkout_date', '>', $checkInDate)
                          ->orWhereNull('checkout_date');
                  });
            })
            ->with(['room', 'guest.title'])
            ->get();

        $conflictingRoomIds = $conflictingGuests->pluck('room_id')->unique()->toArray();

        // Check if the selected room is in conflict
        $isConflict = false;
        $targetConflict = null;
        $targetRoom = null;

        if (!empty($roomId)) {
            $targetRoom = Room::where('id', $roomId)->orWhere('room_number', (string)$roomId)->first();
            if ($targetRoom) {
                $targetConflict = $conflictingGuests->firstWhere('room_id', $targetRoom->id);
                if ($targetConflict) {
                    $isConflict = true;
                }
            }
        }

        // Get available alternative rooms (rooms without conflict for this date window)
        $availableRoomsQuery = Room::with(['categoryRelation', 'floorRelation', 'beddingConfigRelation'])
            ->where('status', 'Active')
            ->whereNotIn('id', $conflictingRoomIds);

        // If a specific room category was selected, prioritize same category first
        if ($targetRoom && $targetRoom->category_id) {
            $catId = (int)$targetRoom->category_id;
            $availableRoomsQuery->orderByRaw("CASE WHEN category_id = {$catId} THEN 1 ELSE 2 END");
        }

        $availableRooms = $availableRoomsQuery
            ->orderByRaw('CAST(room_number AS UNSIGNED) ASC, room_number ASC')
            ->get()
            ->map(function ($r) {
                return [
                    'id' => $r->id,
                    'room' => (string)$r->room_number,
                    'category' => $r->categoryRelation?->name ?? 'Deluxe',
                    'category_id' => $r->category_id ?? ($r->categoryRelation?->id),
                    'floor' => (string)($r->floorRelation?->floor ?? '1'),
                    'floor_id' => $r->floor_id ?? ($r->floorRelation?->id),
                    'rate' => (float)($r->rate > 0 ? $r->rate : 4500),
                    'bedding' => $r->beddingConfigRelation?->name ?? $r->bedding_config ?? 'King Size Master (72x78)',
                    'max_adults' => (int)($r->beddingConfigRelation?->max_adults ?? 2),
                    'max_children' => (int)($r->beddingConfigRelation?->max_children ?? 1),
                    'max_pax' => (int)($r->beddingConfigRelation?->max_total ?? 3),
                ];
            });

        if ($isConflict && $targetConflict) {
            $cInStr = $targetConflict->reserve_date ? \Carbon\Carbon::parse($targetConflict->reserve_date)->format('d M Y') : $checkInDate;
            $cOutStr = $targetConflict->checkout_date ? \Carbon\Carbon::parse($targetConflict->checkout_date)->format('d M Y') : $checkOutDate;
            $guestTitle = $targetConflict->title?->name ? $targetConflict->title->name . ' ' : '';
            $guestName = $guestTitle . ($targetConflict->guest_name ?? 'Another Guest');

            return response()->json([
                'available' => false,
                'room' => $targetRoom ? (string)$targetRoom->room_number : (string)$roomId,
                'conflict' => [
                    'guest_name' => $guestName,
                    'reserve_id' => $targetConflict->reserve_id ?: ('RES-' . $targetConflict->id),
                    'checkin_date' => $cInStr,
                    'checkout_date' => $cOutStr,
                    'status' => $targetConflict->status ?? 'Booked',
                ],
                'message' => "Room #{$targetRoom->room_number} is already booked from {$cInStr} to {$cOutStr} by {$guestName}.",
                'available_rooms' => $availableRooms,
                'available_count' => $availableRooms->count(),
            ]);
        }

        return response()->json([
            'available' => true,
            'message' => 'Room is available for the selected dates.',
            'available_rooms' => $availableRooms,
            'available_count' => $availableRooms->count(),
        ]);
    }

    /**
     * Store new check-in / reservation dynamically into guests table
     */
    public function storeReservation(Request $request)
    {
        DB::beginTransaction();
        try {
            // Auto generate reserve_id if not provided or to ensure strict sequence
            $reserveId = Booking::generateNextReserveId();

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
                    'discount_type' => $request->input('new_company_discount_type', 'percentage'),
                    'discount_value' => (float)$request->input('new_company_discount_value', 0),
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

            $planId = $request->input('plan_id');
            $planId = ($planId && is_numeric($planId) && Plan::where('id', $planId)->exists()) ? (int)$planId : null;

            $advanceAmount = (float)($request->input('advance_amount', 0));
            $paymentRemarks = $request->input('payment_remarks');
            $reserveDate = $request->input('reserve_date', date('Y-m-d'));
            $reserveTime = $request->input('reserve_time', date('H:i'));
            $checkoutDate = $request->input('checkout_date', date('Y-m-d', strtotime('+1 day')));
            $checkoutTime = $request->input('checkout_time', '11:00');

            // Date-based automatic status rule:
            // Today's date checkin => 'Checked In'
            // Future date checkin => 'Confirmed'
            $todayDate = now()->toDateString();
            $reserveDateOnly = \Carbon\Carbon::parse($reserveDate)->toDateString();
            $autoStatus = ($reserveDateOnly <= $todayDate) ? 'Checked In' : 'Confirmed';

            // Calculate stay duration (nights)
            $dIn = new \DateTime($reserveDate);
            $dOut = new \DateTime($checkoutDate);
            $diffNights = (int)$dIn->diff($dOut)->format('%r%a');
            $totalNights = max(1, $diffNights);

            // Discount calculation for reservation (Supports % or Solid Amount across all registration types)
            $discountType = $request->input('discount_type', 'percentage');
            $reqDiscountPct = (float)$request->input('discount_percentage', 0);
            $reqDiscountAmt = (float)$request->input('discount_amount', 0);

            // GST configuration (Inclusive vs Exclusive, dynamic GST percentage)
            $gstType = $request->input('gst_type', 'exclusive');
            $gstPercentageId = $request->input('gst_percentage_id');
            $gstPercentageModel = ($gstPercentageId && is_numeric($gstPercentageId)) ? GstPercentage::find($gstPercentageId) : null;
            $gstPercentage = $gstPercentageModel ? (float)$gstPercentageModel->gst_percentage : (float)($request->input('gst_percentage', 0));

            $companyModel = $companyId ? Company::find($companyId) : null;
            if ($reqDiscountAmt <= 0 && $reqDiscountPct <= 0 && $companyModel && (float)$companyModel->discount_value > 0) {
                $discountType = $companyModel->discount_type ?? 'percentage';
                if ($discountType === 'amount') {
                    $reqDiscountAmt = (float)$companyModel->discount_value;
                } else {
                    $reqDiscountPct = (float)$companyModel->discount_value;
                }
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
                        'status' => $autoStatus,
                        'has_privilege_card' => $request->has('has_privilege_card') || $request->has('chk_privilege'),
                        'privilege_card_no' => $request->input('privilege_card_no'),
                        'room_id' => $request->input('room_id'),
                        'id_card_type_id' => $request->input('id_card_type_id'),
                        'id_card_number' => $request->input('id_card_number'),
                    ]
                ];
            }

            $primaryBooking = null;
            $savedGuests = [];
            $createdBookingIds = [];

            // Captured overall reservation totals from client if present
            $reqTotalAmount = $request->filled('total_amount') ? (float)$request->input('total_amount') : null;
            $reqPayableAmount = $request->filled('payable_amount') ? (float)$request->input('payable_amount') : null;
            $reqAdvanceAmount = (float)($request->input('advance_amount', 0));
            $reqGstAmount = $request->filled('gst_amount') ? (float)$request->input('gst_amount') : null;

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

                // Verify no double-booking conflict for this room from OTHER reservations
                if ($roomId && $roomObj) {
                    $conflictBooking = Booking::where('room_id', $roomId)
                        ->where('reserve_id', '!=', $reserveId)
                        ->when(!empty($createdBookingIds), function ($q) use ($createdBookingIds) {
                            $q->whereNotIn('id', $createdBookingIds);
                        })
                        ->whereIn('status', ['Confirmed', 'Checked In', 'Arrived', 'Stay Over'])
                        ->where(function ($q) use ($reserveDate, $checkoutDate) {
                            $q->whereDate('reserve_date', '<', $checkoutDate)
                              ->where(function ($sub) use ($reserveDate) {
                                  $sub->whereDate('checkout_date', '>', $reserveDate)
                                      ->orWhereNull('checkout_date');
                              });
                        })
                        ->with(['guest'])
                        ->first();
                    if ($conflictBooking) {
                        DB::rollBack();
                        $cIn = $conflictBooking->reserve_date ? \Carbon\Carbon::parse($conflictBooking->reserve_date)->format('d M Y') : $reserveDate;
                        $cOut = $conflictBooking->checkout_date ? \Carbon\Carbon::parse($conflictBooking->checkout_date)->format('d M Y') : $checkoutDate;
                        $gName = $conflictBooking->guest?->guest_name ?? 'Another guest';
                        $errMsg = "Room #{$roomObj->room_number} is already booked from {$cIn} to {$cOut} by {$gName}. Please choose an alternative room.";
                        return response()->json([
                            'success' => false,
                            'message' => $errMsg,
                            'error' => $errMsg
                        ], 422);
                    }
                }

                $roomRate = (float)($roomObj?->rate ?? 4500);
                $planModel = $planId ? Plan::find($planId) : null;
                $planOriginalPrice = (float)($planModel?->price ?? 0);
                $planPrice = $planOriginalPrice;
                $planDiscountAmt = 0.0;
                $planName = $planModel?->plan_name ?? 'None';

                if ($companyModel && !empty($companyModel->meal_discounts) && is_array($companyModel->meal_discounts)) {
                    foreach ($companyModel->meal_discounts as $md) {
                        $mPlanId = $md['meal_plan_id'] ?? $md['plan_id'] ?? null;
                        if ($mPlanId !== null && (int)$mPlanId === (int)$planId) {
                            $mType = $md['discount_type'] ?? 'percentage';
                            $mVal = (float)($md['discount_value'] ?? 0);
                            if ($mType === 'amount') {
                                $planDiscountAmt = min($planOriginalPrice, $mVal);
                                $planPrice = max(0, $planOriginalPrice - $planDiscountAmt);
                            } else {
                                $planDiscountAmt = round($planOriginalPrice * ($mVal / 100), 2);
                                $planPrice = max(0, $planOriginalPrice - $planDiscountAmt);
                            }
                            break;
                        }
                    }
                }
                $singleRoomTotal = ($roomRate + $planPrice) * $totalNights;

                if ($idx === 0) {
                    // Primary guest booking holds the billing folio for the entire reservation
                    $totalAmount = $reqTotalAmount !== null ? $reqTotalAmount : $singleRoomTotal;
                    if ($discountType === 'amount' && $reqDiscountAmt > 0) {
                        $discountAmount = min($totalAmount, $reqDiscountAmt);
                        $discountPercentage = $totalAmount > 0 ? round(($discountAmount / $totalAmount) * 100, 2) : 0.0;
                    } elseif ($reqDiscountPct > 0) {
                        $discountPercentage = $reqDiscountPct;
                        $discountAmount = round(($totalAmount * $discountPercentage) / 100.0, 2);
                    } else {
                        $discountAmount = 0.0;
                        $discountPercentage = 0.0;
                    }

                    $netAfterDiscount = max(0, $totalAmount - $discountAmount);
                    if ($gstPercentage > 0) {
                        if ($gstType === 'inclusive') {
                            $gstAmount = round(($netAfterDiscount - ($netAfterDiscount / (1 + ($gstPercentage / 100)))), 2);
                            $payableAmount = $netAfterDiscount;
                        } else {
                            $gstAmount = round((($netAfterDiscount * $gstPercentage) / 100), 2);
                            $payableAmount = $netAfterDiscount + $gstAmount;
                        }
                    } else {
                        $gstAmount = 0.00;
                        $payableAmount = $netAfterDiscount;
                    }

                    if ($reqPayableAmount !== null) {
                        $payableAmount = $reqPayableAmount;
                    }
                    if ($reqGstAmount !== null) {
                        $gstAmount = $reqGstAmount;
                    }

                    $calculatedBalance = max(0, $payableAmount - $advanceAmount);
                } else {
                    // Secondary guests sharing room or grouped in same reservation have 0 billing balance attached
                    $totalAmount = 0.00;
                    $discountPercentage = 0.0;
                    $discountAmount = 0.00;
                    $gstAmount = 0.00;
                    $payableAmount = 0.00;
                    $calculatedBalance = 0.00;
                }

                $folioNo = Booking::generateSettleNumber($roomObj?->room_number ?? '100');

                $titleId = (!empty($gData['title_id']) && Title::where('id', $gData['title_id'])->exists()) ? (int)$gData['title_id'] : null;
                $nationalityId = (!empty($gData['nationality_id']) && Nationality::where('id', $gData['nationality_id'])->exists()) ? (int)$gData['nationality_id'] : null;
                $idCardTypeId = (!empty($gData['id_card_type_id']) && IdCardType::where('id', $gData['id_card_type_id'])->exists()) ? (int)$gData['id_card_type_id'] : null;

                // 1. Find or create the Guest profile
                $guestFields = [
                    'title_id' => $titleId,
                    'guest_name' => $gData['guest_name'],
                    'guest_address' => $gData['guest_address'] ?? null,
                    'nationality_id' => $nationalityId,
                    'city' => $gData['city'] ?? null,
                    'email' => $gData['email'] ?? null,
                    'has_privilege_card' => !empty($gData['has_privilege_card']),
                    'privilege_card_no' => $gData['privilege_card_no'] ?? null,
                    'id_card_type_id' => $idCardTypeId,
                    'id_card_number' => $gData['id_card_number'] ?? null,
                    'company_id' => $companyId,
                    'new_company_name' => $request->input('new_company_name'),
                    'new_company_address' => $request->input('new_company_address'),
                    'new_company_gstin' => $request->input('new_company_gstin'),
                    'new_company_phone' => $request->input('new_company_phone'),
                ];

                if (!empty($gData['mobile'])) {
                    $guestModel = Guest::firstOrCreate(
                        ['mobile' => $gData['mobile']],
                        $guestFields
                    );
                } else {
                    $guestModel = Guest::create($guestFields);
                }

                // 2. Create the Booking record linked with guest_id & room_id
                $bookingModel = Booking::create([
                    'reserve_id' => $reserveId,
                    'settle_number' => $folioNo,
                    'folio_number' => $folioNo,
                    'guest_id' => $guestModel->id,
                    'room_id' => $roomId,
                    'registration_type_id' => $registrationTypeId,
                    'reservation_mode_id' => $reservationModeId,
                    'company_id' => $companyId,
                    'new_company_name' => $request->input('new_company_name'),
                    'new_company_address' => $request->input('new_company_address'),
                    'new_company_gstin' => $request->input('new_company_gstin'),
                    'new_company_phone' => $request->input('new_company_phone'),
                    'plan_id' => $planId,
                    'plan_name' => $planName,
                    'plan_original_price' => $planOriginalPrice,
                    'plan_price' => $planPrice,
                    'plan_discount_amount' => $planDiscountAmt,
                    'checkin_date' => $reserveDate,
                    'checkin_time' => $reserveTime,
                    'reserve_date' => $reserveDate,
                    'reserve_time' => $reserveTime,
                    'checkout_date' => $checkoutDate,
                    'checkout_time' => $checkoutTime,
                    'total_nights' => $totalNights,
                    'room_rate' => $roomRate,
                    'total_amount' => $totalAmount,
                    'discount_percentage' => ($idx === 0) ? $discountPercentage : 0.0,
                    'discount_amount' => ($idx === 0) ? $discountAmount : 0.00,
                    'gst_type' => ($idx === 0) ? $gstType : 'exclusive',
                    'gst_percentage_id' => ($idx === 0 && $gstPercentageModel) ? $gstPercentageModel->id : null,
                    'gst_percentage' => ($idx === 0) ? $gstPercentage : 0.0,
                    'gst_amount' => ($idx === 0) ? $gstAmount : 0.00,
                    'payable_amount' => ($idx === 0) ? $payableAmount : 0.00,
                    'advance_amount' => ($idx === 0) ? $advanceAmount : 0.00,
                    'balance' => $calculatedBalance,
                    'status' => (empty($gData['status']) || in_array($gData['status'], ['Confirmed', 'Checked In'])) ? $autoStatus : $gData['status'],
                    'primary_booking_id' => $primaryBooking ? $primaryBooking->id : null,
                    'remarks' => ($idx === 0) ? $paymentRemarks : null,
                ]);

                $createdBookingIds[] = $bookingModel->id;

                // 3. Create Payment record if advance paid
                if ($idx === 0) {
                    $primaryBooking = $bookingModel;

                    if ($advanceAmount > 0 || $paymentModeId) {
                        Payment::create([
                            'guest_id' => $guestModel->id,
                            'booking_id' => $bookingModel->id,
                            'payment_mode_id' => $paymentModeId,
                            'amount' => $advanceAmount,
                            'advance_amount' => $advanceAmount,
                            'balance' => $calculatedBalance,
                            'settle_number' => $folioNo,
                            'folio_number' => $folioNo,
                            'payment_remarks' => $paymentRemarks,
                            'payment_date' => $reserveDate,
                            'payment_time' => $reserveTime,
                            'payment_type' => 'Advance',
                            'status' => 'Success',
                        ]);
                    }
                }

                // 4. Create Checkout record if Checked Out
                if (($gData['status'] ?? '') === 'Checked Out') {
                    Checkout::create([
                        'guest_id' => $guestModel->id,
                        'booking_id' => $bookingModel->id,
                        'room_id' => $roomId,
                        'checkout_date' => $checkoutDate,
                        'checkout_time' => $checkoutTime,
                        'actual_checkout_at' => now(),
                        'total_nights' => $totalNights,
                        'room_charges' => $totalAmount,
                        'extra_charges' => 0.00,
                        'late_checkout_charge' => 0.00,
                        'discount_amount' => $discountAmount,
                        'tax_amount' => $gstAmount,
                        'grand_total' => $payableAmount,
                        'paid_amount' => $advanceAmount,
                        'balance_amount' => $calculatedBalance,
                        'status' => 'Completed',
                        'remarks' => $paymentRemarks,
                    ]);
                }

                $savedGuests[] = $bookingModel;

                // If room is assigned and check-in is today (status Checked In), update housekeeping & operational state
                if ($roomObj && $bookingModel->status === 'Checked In') {
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
                'message' => 'Reservation #' . $reserveId . ' saved successfully with status "' . $autoStatus . '" for ' . count($savedGuests) . ' guest(s)!',
                'status' => $autoStatus,
                'reserve_id' => $reserveId,
                'next_reserve_id' => Booking::generateNextReserveId(),
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
                    $isCompletedType = ($hkState->type === 'completed_task');
                    RoomHousekeepingHistory::create([
                        'room_id' => $room->id,
                        'housekeeping_status_id' => $hkState->id,
                        'start_time' => now(),
                        'completion_time' => ($isCompletedType ? now() : null),
                        'status' => ($isCompletedType ? 'complete' : 'pending'),
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
            $isOpBlocked = (bool)preg_match('/block|blok|maintenance|order/i', $opName);

            if ($hasActiveGuests) {
                $calculatedStatus = 'occupied';
                $cleaningText = (stripos($hkName, 'Dirty') !== false) ? 'Dirty' : 'Cleaned';
            } elseif ($isOpBlocked) {
                $calculatedStatus = 'blocked';
                $cleaningText = 'Blocked';
            } elseif (stripos($hkName, 'Dirty') !== false) {
                $calculatedStatus = 'dirty';
                $cleaningText = 'Dirty';
            } else {
                $calculatedStatus = 'available';
                $cleaningText = 'Cleaned';
            }

            // Sync room model status (Active vs Inactive)
            if ($calculatedStatus === 'blocked') {
                $room->status = 'Inactive';
            } else {
                $room->status = 'Active';
            }
            $room->save();

            DB::commit();

            $currentHkId = !empty($validated['housekeeping_status_id']) ? $validated['housekeeping_status_id'] : ($latestHk?->housekeeping_status_id ?? null);
            $currentOpId = !empty($validated['operational_status_id']) ? $validated['operational_status_id'] : ($latestOp?->operational_status_id ?? null);

            return response()->json([
                'success' => true,
                'message' => "Room #{$room->room_number} status updated successfully!",
                'room' => [
                    'id' => $room->id,
                    'room' => (string)$room->room_number,
                    'status' => $calculatedStatus,
                    'cleaning' => $cleaningText,
                    'housekeeping_status_id' => $currentHkId,
                    'housekeeping_status' => $hkName,
                    'operational_status_id' => $currentOpId,
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
