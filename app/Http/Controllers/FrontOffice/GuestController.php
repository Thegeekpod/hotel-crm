<?php

namespace App\Http\Controllers\FrontOffice;

use App\Http\Controllers\Controller;
use App\Models\RoomCategory;
use App\Models\Floor;
use App\Models\Company;
use App\Models\IdCardType;
use App\Models\ReservationMode;
use App\Models\PaymentMode;
use App\Models\Guest;
use App\Models\Title;
use App\Models\Nationality;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Settlement;
use App\Models\Checkout;
use App\Models\RoomHousekeepingHistory;
use App\Models\HousekeepingState;
use App\Models\OperationalStatus;
use App\Models\RoomOperationalHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuestController extends Controller
{
    public function index(Request $request)
    {
        $categories = RoomCategory::where('status', 'Active')->get();
        $floors = Floor::where('status', 'Active')->orderBy('floor', 'asc')->get();
        $companies = Company::where('status', 'Active')->get();
        $idCardTypes = IdCardType::where('status', 'Active')->get();
        $registrationModes = ReservationMode::where('status', 'Active')->get();
        $registrationTypes = \App\Models\RegistrationType::where('status', 'Active')->get();
        $paymentModes = PaymentMode::where('status', 'Active')->get();
        $titles = Title::where('status', 'Active')->get();
        $nationalities = Nationality::where('status', 'Active')->get();
        $rackRooms = [];

        $query = Guest::with([
            'title',
            'nationality',
            'idCardType',
            'company',
            'latestBooking.room.categoryRelation',
            'latestBooking.room.floorRelation',
            'latestBooking.room.beddingConfigRelation',
            'latestBooking.registrationType',
            'latestBooking.reservationMode',
            'latestBooking.plan',
            'latestBooking.payments.paymentMode',
            'latestBooking.settlements',
            'bookings.room.categoryRelation',
            'payments.paymentMode',
            'settlements',
        ])
        ->orderBy('id', 'desc');

        $today = now()->toDateString();

        // From & To Date Filter (based on booking check-in date: reserve_date)
        if ($request->has('all_dates')) {
            $fromDate = null;
            $toDate = null;
        } elseif ($request->has('from_date') || $request->has('to_date')) {
            $fromDate = $request->query('from_date');
            $toDate = $request->query('to_date');
        } else {
            $fromDate = $today;
            $toDate = $today;
        }

        if (!empty($fromDate)) {
            $query->whereHas('bookings', function ($bq) use ($fromDate) {
                $bq->whereDate('reserve_date', '>=', $fromDate);
            });
        }
        if (!empty($toDate)) {
            $query->whereHas('bookings', function ($bq) use ($toDate) {
                $bq->whereDate('reserve_date', '<=', $toDate);
            });
        }

        // Backend Filter: Status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->whereHas('bookings', function ($bq) use ($request) {
                $bq->where('status', $request->status);
            });
        }

        // Backend Filter: Registration Type
        if ($request->filled('registration_type') && $request->registration_type !== 'all') {
            $query->whereHas('bookings.registrationType', function ($q) use ($request) {
                $q->where('name', $request->registration_type);
            });
        }

        // Backend Filter: Search
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('guest_name', 'like', "%{$s}%")
                  ->orWhere('mobile', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('city', 'like', "%{$s}%")
                  ->orWhere('id_card_number', 'like', "%{$s}%")
                  ->orWhereHas('bookings', function ($bq) use ($s) {
                      $cleanRoom = ltrim(ltrim($s, 'Rr'), '-');
                      $bq->where('reserve_id', 'like', "%{$s}%")
                         ->orWhere('settle_number', 'like', "%{$s}%")
                         ->orWhereHas('registrationType', function ($rq) use ($s) {
                             $rq->where('name', 'like', "%{$s}%");
                         })
                         ->orWhereHas('room', function ($rq) use ($s, $cleanRoom) {
                             $rq->where('room_number', 'like', "%{$s}%")
                                ->orWhere('room_number', 'like', "%{$cleanRoom}%");
                         });
                  });
            });
        }

        // Backend Pagination
        $perPage = $request->query('per_page', 10);
        if ($perPage === 'all') {
            $guests = $query->paginate(1000)->withQueryString();
        } else {
            $perPage = (int)$perPage > 0 ? (int)$perPage : 10;
            $guests = $query->paginate($perPage)->withQueryString();
        }

        // Overall stats from Database
        $stats = [
            'total' => Guest::count(),
            'confirmed' => Booking::where('status', 'Confirmed')->count(),
            'checked_in' => Booking::where('status', 'Checked In')->count(),
            'arrived' => Booking::where('status', 'Arrived')->count(),
            'stay_over' => Booking::where('status', 'Stay Over')->count(),
            'checked_out' => Booking::where('status', 'Checked Out')->count(),
            'cancelled' => Booking::where('status', 'Cancelled')->count(),
            'vip' => Guest::where('has_privilege_card', true)->count(),
            'corporate' => Guest::where(function($q) {
                $q->whereNotNull('company_id')->orWhereNotNull('new_company_name');
            })->count(),
        ];

        // Serialized guests for instant client-side dossier rendering
        $serializedGuests = $guests->getCollection()->map(function($item) {
            $lb = $item->latestBooking;
            $room = $lb?->room;
            $advanceAmount = (float)$item->advance_amount;
            $settlePayments = (float)($item->settlements?->sum('paid_amount') ?: $lb?->payments?->where('payment_type', 'Settlement')->sum('amount'));
            $totalPaid = round($advanceAmount + $settlePayments, 2);
            $balVal = (float)($item->balance ?? 0);
            $planObj = $lb?->plan;
            $planName = $lb?->plan_name ?: ($planObj?->plan_name ?: 'Room Only (EP)');
            $planCode = $planObj?->plan_code ?: '';
            $planPrice = (float)($lb?->plan_price ?? ($planObj?->price ?? 0));
            $planOriginalPrice = (float)($lb?->plan_original_price ?? ($planObj?->price ?? $planPrice));
            $planDiscountAmount = (float)($lb?->plan_discount_amount ?? max(0, $planOriginalPrice - $planPrice));

            $hasSettled = ($item->settlements && $item->settlements->where('status', 'Settled')->isNotEmpty())
                || ($lb?->settlements && $lb->settlements->where('status', 'Settled')->isNotEmpty())
                || ($lb?->payments && $lb->payments->where('payment_type', 'Settlement')->isNotEmpty())
                || strtolower($item->status ?? '') === 'settlement done'
                || ((float)$balVal <= 0.01 && (float)$item->payable_amount > 0 && ($advanceAmount > 0 || ($lb?->payments && $lb->payments->isNotEmpty())));

            $isLocked = $hasSettled || in_array($item->status, ['Settlement Done', 'Checked Out']);

            // Fetch all associated bookings for this guest / group to gather all rooms
            $allBookings = \App\Models\Booking::where(function($q) use ($item, $lb) {
                if ($lb?->primary_booking_id) {
                    $q->where('primary_booking_id', $lb->primary_booking_id)->orWhere('id', $lb->primary_booking_id);
                } elseif ($lb && $lb->additionalBookings()->exists()) {
                    $q->where('primary_booking_id', $lb->id)->orWhere('id', $lb->id);
                } else {
                    $q->where('guest_id', $item->id);
                }
            })->with(['room.categoryRelation', 'room.floorRelation', 'room.beddingConfigRelation', 'guest'])->get();

            $allRooms = $allBookings->map(function($b) {
                $rm = $b->room;
                return [
                    'room_number' => $rm?->room_number ? ('R' . preg_replace('/^r[-_\s]?/i', '', trim($rm->room_number))) : 'Unassigned',
                    'category' => $rm?->categoryRelation?->name ?? ($rm?->category ?? 'Standard'),
                    'floor' => $rm?->floorRelation?->floor ?? ($rm?->floor ?? '1'),
                    'bedding' => $rm?->beddingConfigRelation?->name ?? 'Standard',
                    'occupant_name' => ($b->guest?->title?->name ? $b->guest->title->name . ' ' : '') . ($b->guest?->guest_name ?? 'Primary Guest'),
                    'occupant_phone' => $b->guest?->mobile ?? 'N/A',
                    'status' => $b->status ?? 'Confirmed',
                ];
            })->values()->all();

            if (empty($allRooms) && $room) {
                $allRooms[] = [
                    'room_number' => 'R' . preg_replace('/^r[-_\s]?/i', '', trim($room->room_number)),
                    'category' => $room->categoryRelation?->name ?? ($room->category ?? 'Standard'),
                    'floor' => $room->floorRelation?->floor ?? ($room->floor ?? '1'),
                    'bedding' => $room->beddingConfigRelation?->name ?? 'Standard',
                    'occupant_name' => ($item->title?->name ? $item->title->name . ' ' : '') . $item->guest_name,
                    'occupant_phone' => $item->mobile ?? 'N/A',
                    'status' => $item->status ?: 'Confirmed',
                ];
            }

            return [
                'id' => $item->id,
                'title_id' => $item->title_id,
                'title' => $item->title?->name ?? '',
                'raw_name' => $item->guest_name,
                'guest_name' => ($item->title?->name ? $item->title->name . ' ' : '') . $item->guest_name,
                'reserve_id' => $item->reserve_id ?: ('RES-' . $item->id),
                'folio_number' => $item->folio_number ?: '-',
                'created_at_fmt' => $item->created_at ? $item->created_at->format('d M Y, h:i A') : 'N/A',
                'status' => $item->status ?: 'Confirmed',
                'is_locked' => $isLocked,
                'room_id' => $item->room_id,
                'room_number' => $room?->room_number ? ('R' . preg_replace('/^r[-_\s]?/i', '', trim($room->room_number))) : 'Unassigned',
                'room_category' => $room?->categoryRelation?->name ?? ($room?->category ?? 'Standard'),
                'room_floor' => $room?->floorRelation?->floor ?? ($room?->floor ?? '1'),
                'room_bedding' => $room?->beddingConfigRelation?->name ?? 'Standard',
                'total_nights' => (int)($item->total_nights ?: 1),
                'registration_type' => $lb?->registrationType?->name ?? ($item->registrationType?->name ?? 'Regular'),
                'mobile' => $item->mobile ?: 'N/A',
                'raw_mobile' => $item->mobile ?? '',
                'email' => $item->email ?: 'N/A',
                'raw_email' => $item->email ?? '',
                'city' => $item->city ?: 'N/A',
                'raw_city' => $item->city ?? '',
                'guest_address' => $item->guest_address ?: 'N/A',
                'raw_address' => $item->guest_address ?? '',
                'nationality_id' => $item->nationality_id,
                'nationality' => $item->nationality?->name ?? 'Indian',
                'id_card_type_id' => $item->id_card_type_id,
                'id_card_type' => $item->idCardType?->name ?? 'ID Proof',
                'id_card_number' => $item->id_card_number ?: 'N/A',
                'raw_id_card_number' => $item->id_card_number ?? '',
                'has_privilege_card' => (bool)$item->has_privilege_card,
                'privilege_card_no' => $item->privilege_card_no ?: '',
                'documents' => is_array($item->documents) ? $item->documents : (is_string($item->documents) ? json_decode($item->documents, true) : []),
                'company_id' => $item->company_id,
                'new_company_name' => $item->new_company_name ?? '',
                'new_company_address' => $item->new_company_address ?? '',
                'new_company_gstin' => $item->new_company_gstin ?? '',
                'new_company_phone' => $item->new_company_phone ?? '',
                'reserve_date' => $item->reserve_date ? \Carbon\Carbon::parse($item->reserve_date)->format('d M Y') : 'N/A',
                'reserve_time' => $item->reserve_time ? date('h:i A', strtotime($item->reserve_time)) : '12:00 PM',
                'checkout_date' => $item->checkout_date ? \Carbon\Carbon::parse($item->checkout_date)->format('d M Y') : 'N/A',
                'checkout_time' => $item->checkout_time ? date('h:i A', strtotime($item->checkout_time)) : '11:00 AM',
                'reservation_mode' => $lb?->reservationMode?->name ?? 'Front Desk',
                'plan_name' => $planName,
                'plan_code' => $planCode,
                'plan_price' => $planPrice,
                'plan_original_price' => $planOriginalPrice,
                'plan_discount_amount' => $planDiscountAmount,
                'company' => $item->company ? [
                    'name' => $item->company->name,
                    'gstin' => $item->company->gstin,
                    'discount_type' => $item->company->discount_type,
                    'discount_value' => $item->company->discount_value,
                ] : null,
                'room_rate' => (float)$item->room_rate,
                'total_amount' => (float)$item->total_amount,
                'advance_amount' => $advanceAmount,
                'settlement_paid' => $settlePayments,
                'total_paid' => $totalPaid,
                'balance' => $balVal,
                'discount_amount' => (float)$item->discount_amount,
                'discount_percentage' => (float)$item->discount_percentage,
                'gst_amount' => (float)$item->gst_amount,
                'gst_percentage' => (float)$item->gst_percentage,
                'gst_type' => $item->gst_type ?: 'exclusive',
                'payable_amount' => (float)$item->payable_amount,
                'payment_mode' => $lb?->payments?->last()?->paymentMode?->name ?? ($item->paymentMode?->name ?? 'Cash / Direct'),
                'payment_remarks' => $lb?->remarks ?: ($item->payments?->last()?->payment_remarks ?? 'None'),
                'remarks' => $lb?->remarks ?: '',
                'all_rooms' => $allRooms,
            ];
        });

        return view('frontoffice.guest.index', compact(
            'guests',
            'serializedGuests',
            'stats',
            'fromDate',
            'toDate',
            'today',
            'categories',
            'floors',
            'companies',
            'idCardTypes',
            'registrationModes',
            'registrationTypes',
            'paymentModes',
            'titles',
            'nationalities',
            'rackRooms'
        ));
    }

    public function getDetails($id)
    {
        $guest = Guest::with([
            'title',
            'nationality',
            'idCardType',
            'company',
            'latestBooking.room.categoryRelation',
            'latestBooking.room.floorRelation',
            'latestBooking.room.beddingConfigRelation',
            'latestBooking.registrationType',
            'latestBooking.reservationMode',
            'latestBooking.plan',
            'latestBooking.payments.paymentMode',
            'bookings.room',
            'payments.paymentMode',
            'checkouts',
            'settlements',
        ])->find($id);

        if (!$guest) {
            return response()->json(['error' => 'Guest record not found'], 404);
        }

        $lb = $guest->latestBooking;
        $room = $lb?->room;
        $advanceAmount = (float)$guest->advance_amount;
        $settlePayments = (float)($guest->settlements?->sum('paid_amount') ?: $lb?->payments?->where('payment_type', 'Settlement')->sum('amount'));
        $totalPaid = round($advanceAmount + $settlePayments, 2);
        $balVal = (float)($guest->balance ?? 0);
        $planObj = $lb?->plan;

        $hasSettled = ($guest->settlements && $guest->settlements->where('status', 'Settled')->isNotEmpty())
            || ($lb?->settlements && $lb->settlements->where('status', 'Settled')->isNotEmpty())
            || ($lb?->payments && $lb->payments->where('payment_type', 'Settlement')->isNotEmpty())
            || strtolower($guest->status ?? '') === 'settlement done'
            || ((float)$balVal <= 0.01 && (float)$guest->payable_amount > 0 && ($advanceAmount > 0 || ($lb?->payments && $lb->payments->isNotEmpty())));

        $isLocked = $hasSettled || in_array($guest->status, ['Settlement Done', 'Checked Out']);

        // Fetch all associated bookings for this guest / group to gather all rooms
        $allBookings = \App\Models\Booking::where(function($q) use ($guest, $lb) {
            if ($lb?->primary_booking_id) {
                $q->where('primary_booking_id', $lb->primary_booking_id)->orWhere('id', $lb->primary_booking_id);
            } elseif ($lb && $lb->additionalBookings()->exists()) {
                $q->where('primary_booking_id', $lb->id)->orWhere('id', $lb->id);
            } else {
                $q->where('guest_id', $guest->id);
            }
        })->with(['room.categoryRelation', 'room.floorRelation', 'room.beddingConfigRelation', 'guest'])->get();

        $allRooms = $allBookings->map(function($b) {
            $rm = $b->room;
            return [
                'room_number' => $rm?->room_number ? ('R' . preg_replace('/^r[-_\s]?/i', '', trim($rm->room_number))) : 'Unassigned',
                'category' => $rm?->categoryRelation?->name ?? ($rm?->category ?? 'Standard'),
                'floor' => $rm?->floorRelation?->floor ?? ($rm?->floor ?? '1'),
                'bedding' => $rm?->beddingConfigRelation?->name ?? 'Standard',
                'occupant_name' => ($b->guest?->title?->name ? $b->guest->title->name . ' ' : '') . ($b->guest?->guest_name ?? 'Primary Guest'),
                'occupant_phone' => $b->guest?->mobile ?? 'N/A',
                'status' => $b->status ?? 'Confirmed',
            ];
        })->values()->all();

        if (empty($allRooms) && $room) {
            $allRooms[] = [
                'room_number' => 'R' . preg_replace('/^r[-_\s]?/i', '', trim($room->room_number)),
                'category' => $room->categoryRelation?->name ?? ($room->category ?? 'Standard'),
                'floor' => $room->floorRelation?->floor ?? ($room->floor ?? '1'),
                'bedding' => $room->beddingConfigRelation?->name ?? 'Standard',
                'occupant_name' => ($guest->title?->name ? $guest->title->name . ' ' : '') . $guest->guest_name,
                'occupant_phone' => $guest->mobile ?? 'N/A',
                'status' => $guest->status ?: 'Confirmed',
            ];
        }

        $serialized = [
            'id' => $guest->id,
            'guest_name' => ($guest->title?->name ? $guest->title->name . ' ' : '') . $guest->guest_name,
            'reserve_id' => $guest->reserve_id ?: ('RES-' . $guest->id),
            'folio_number' => $guest->folio_number ?: '-',
            'created_at_fmt' => $guest->created_at ? $guest->created_at->format('d M Y, h:i A') : 'N/A',
            'status' => $guest->status ?: 'Confirmed',
            'is_locked' => $isLocked,
            'room_id' => $guest->room_id,
            'room_number' => $room?->room_number ? ('R' . preg_replace('/^r[-_\s]?/i', '', trim($room->room_number))) : 'Unassigned',
            'room_category' => $room?->categoryRelation?->name ?? ($room?->category ?? 'Standard'),
            'room_floor' => $room?->floorRelation?->floor ?? ($room?->floor ?? '1'),
            'room_bedding' => $room?->beddingConfigRelation?->name ?? 'Standard',
            'total_nights' => (int)($guest->total_nights ?: 1),
            'registration_type' => $lb?->registrationType?->name ?? ($guest->registrationType?->name ?? 'Regular'),
            'mobile' => $guest->mobile ?: 'N/A',
            'email' => $guest->email ?: 'N/A',
            'city' => $guest->city ?: 'N/A',
            'guest_address' => $guest->guest_address ?: 'N/A',
            'nationality' => $guest->nationality?->name ?? 'Indian',
            'id_card_type' => $guest->idCardType?->name ?? 'ID Proof',
            'id_card_number' => $guest->id_card_number ?: 'N/A',
            'has_privilege_card' => (bool)$guest->has_privilege_card,
            'privilege_card_no' => $guest->privilege_card_no ?: '',
            'reserve_date' => $guest->reserve_date ? \Carbon\Carbon::parse($guest->reserve_date)->format('d M Y') : 'N/A',
            'reserve_time' => $guest->reserve_time ? date('h:i A', strtotime($guest->reserve_time)) : '12:00 PM',
            'checkout_date' => $guest->checkout_date ? \Carbon\Carbon::parse($guest->checkout_date)->format('d M Y') : 'N/A',
            'checkout_time' => $guest->checkout_time ? date('h:i A', strtotime($guest->checkout_time)) : '11:00 AM',
            'reservation_mode' => $lb?->reservationMode?->name ?? 'Front Desk',
            'plan_name' => $lb?->plan_name ?: ($planObj?->plan_name ?? 'Room Only (EP)'),
            'plan_code' => $planObj?->plan_code ?? '',
            'plan_price' => (float)($lb?->plan_price ?? ($planObj?->price ?? 0)),
            'plan_original_price' => (float)($lb?->plan_original_price ?? ($planObj?->price ?? 0)),
            'plan_discount_amount' => (float)($lb?->plan_discount_amount ?? 0),
            'company' => $guest->company ? [
                'name' => $guest->company->name,
                'gstin' => $guest->company->gstin,
                'discount_type' => $guest->company->discount_type,
                'discount_value' => $guest->company->discount_value,
            ] : null,
            'room_rate' => (float)$guest->room_rate,
            'total_amount' => (float)$guest->total_amount,
            'advance_amount' => $advanceAmount,
            'settlement_paid' => $settlePayments,
            'total_paid' => $totalPaid,
            'balance' => $balVal,
            'discount_amount' => (float)$guest->discount_amount,
            'discount_percentage' => (float)$guest->discount_percentage,
            'gst_amount' => (float)$guest->gst_amount,
            'gst_percentage' => (float)$guest->gst_percentage,
            'gst_type' => $guest->gst_type ?: 'exclusive',
            'payable_amount' => (float)$guest->payable_amount,
            'payment_mode' => $lb?->payments?->last()?->paymentMode?->name ?? ($guest->paymentMode?->name ?? 'Cash / Direct'),
            'payment_remarks' => $lb?->remarks ?: ($guest->payments?->last()?->payment_remarks ?? 'None'),
            'remarks' => $lb?->remarks ?: '',
            'documents' => is_array($guest->documents) ? $guest->documents : (is_string($guest->documents) ? json_decode($guest->documents, true) : []),
            'all_rooms' => $allRooms,
        ];

        return response()->json([
            'success' => true,
            'guest' => $serialized,
            'raw_guest' => $guest
        ]);
    }

    public function update(Request $request, $id)
    {
        $guest = Guest::with(['latestBooking.settlements', 'latestBooking.payments', 'settlements'])->find($id);
        if (!$guest) {
            return response()->json([
                'success' => false,
                'message' => 'Guest record not found.',
                'error' => 'Guest record not found.'
            ], 404);
        }

        $booking = $guest->latestBooking;
        $balVal = (float)($guest->balance ?? 0);
        $advanceAmount = (float)$guest->advance_amount;
        $hasSettled = ($guest->settlements && $guest->settlements->where('status', 'Settled')->isNotEmpty())
            || ($booking?->settlements && $booking->settlements->where('status', 'Settled')->isNotEmpty())
            || ($booking?->payments && $booking->payments->where('payment_type', 'Settlement')->isNotEmpty())
            || strtolower($guest->status ?? '') === 'settlement done'
            || ((float)$balVal <= 0.01 && (float)$guest->payable_amount > 0 && ($advanceAmount > 0 || ($booking?->payments && $booking->payments->isNotEmpty())));

        $isLocked = $hasSettled || in_array($guest->status, ['Settlement Done', 'Checked Out']);

        if ($isLocked) {
            return response()->json([
                'success' => false,
                'message' => 'This guest record is settled / locked and cannot be edited.',
                'error' => 'This guest record is settled / locked and cannot be edited.'
            ], 403);
        }

        $validated = $request->validate([
            'title_id' => 'nullable|exists:titles,id',
            'guest_name' => 'required|string|max:191',
            'mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:191',
            'city' => 'nullable|string|max:100',
            'guest_address' => 'nullable|string|max:500',
            'nationality_id' => 'nullable|exists:nationalities,id',
            'id_card_type_id' => 'nullable|exists:id_card_types,id',
            'id_card_number' => 'nullable|string|max:100',
            'has_privilege_card' => 'nullable',
            'privilege_card_no' => 'nullable|string|max:100',
            'company_id' => 'nullable',
            'new_company_name' => 'nullable|string|max:191',
            'new_company_address' => 'nullable|string|max:500',
            'new_company_gstin' => 'nullable|string|max:50',
            'new_company_phone' => 'nullable|string|max:50',
        ]);

        $hasPrivilege = $request->boolean('has_privilege_card') || $request->input('has_privilege_card') === '1' || $request->input('has_privilege_card') === 1;
        $validated['has_privilege_card'] = $hasPrivilege;
        if (!$hasPrivilege) {
            $validated['privilege_card_no'] = null;
        }

        // Handle company selection: 'new' or numeric id or empty
        $compVal = $request->input('company_id');
        if ($compVal === 'new') {
            $validated['company_id'] = null;
        } elseif (is_numeric($compVal) && (int)$compVal > 0) {
            $validated['company_id'] = (int)$compVal;
            $validated['new_company_name'] = null;
            $validated['new_company_address'] = null;
            $validated['new_company_gstin'] = null;
            $validated['new_company_phone'] = null;
        } else {
            $validated['company_id'] = null;
        }

        // Update ONLY this particular guest details
        $guest->update($validated);

        // Optionally sync company fields on the latest booking
        if ($booking) {
            $booking->update([
                'company_id' => $validated['company_id'] ?? null,
                'new_company_name' => $validated['new_company_name'] ?? null,
                'new_company_address' => $validated['new_company_address'] ?? null,
                'new_company_gstin' => $validated['new_company_gstin'] ?? null,
                'new_company_phone' => $validated['new_company_phone'] ?? null,
            ]);
        }

        $guest->load(['title', 'nationality', 'idCardType', 'company']);

        return response()->json([
            'success' => true,
            'message' => 'Guest profile details updated successfully!',
            'guest' => [
                'id' => $guest->id,
                'title_id' => $guest->title_id,
                'title' => $guest->title?->name ?? '',
                'guest_name' => ($guest->title?->name ? $guest->title->name . ' ' : '') . $guest->guest_name,
                'raw_name' => $guest->guest_name,
                'mobile' => $guest->mobile ?: 'N/A',
                'raw_mobile' => $guest->mobile ?? '',
                'email' => $guest->email ?: 'N/A',
                'raw_email' => $guest->email ?? '',
                'city' => $guest->city ?: 'N/A',
                'raw_city' => $guest->city ?? '',
                'guest_address' => $guest->guest_address ?: 'N/A',
                'raw_address' => $guest->guest_address ?? '',
                'nationality_id' => $guest->nationality_id,
                'nationality' => $guest->nationality?->name ?? 'Indian',
                'id_card_type_id' => $guest->id_card_type_id,
                'id_card_type' => $guest->idCardType?->name ?? 'ID Proof',
                'id_card_number' => $guest->id_card_number ?: 'N/A',
                'raw_id_card_number' => $guest->id_card_number ?? '',
                'has_privilege_card' => (bool)$guest->has_privilege_card,
                'privilege_card_no' => $guest->privilege_card_no ?: '',
                'company_id' => $guest->company_id,
                'company' => $guest->company ? [
                    'name' => $guest->company->name,
                    'gstin' => $guest->company->gstin ?: 'N/A',
                    'address' => $guest->company->address ?: 'N/A',
                    'phone' => $guest->company->phone ?: 'N/A',
                ] : ($guest->new_company_name ? [
                    'name' => $guest->new_company_name,
                    'gstin' => $guest->new_company_gstin ?: 'N/A',
                    'address' => $guest->new_company_address ?: 'N/A',
                    'phone' => $guest->new_company_phone ?: 'N/A',
                ] : null),
                'new_company_name' => $guest->new_company_name ?? '',
                'new_company_address' => $guest->new_company_address ?? '',
                'new_company_gstin' => $guest->new_company_gstin ?? '',
                'new_company_phone' => $guest->new_company_phone ?? '',
            ]
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|string|max:50',
        ]);

        $guest = Guest::with(['latestBooking.room', 'latestBooking.settlements', 'latestBooking.payments', 'settlements'])->findOrFail($id);
        $booking = $guest->latestBooking;
        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'No active booking record found for this guest.',
                'error' => 'No active booking record found for this guest.'
            ], 404);
        }

        $oldStatus = $booking->status ?: 'Confirmed';
        $newStatus = $validated['status'];

        $hasSettled = $booking->settlements()->where('status', 'Settled')->exists()
            || ($guest->settlements()->where('status', 'Settled')->exists())
            || ($booking->settlements()->exists() && (float)$booking->balance <= 0.01)
            || ($guest->settlements()->exists() && (float)$guest->balance <= 0.01)
            || $booking->payments()->where('payment_type', 'Settlement')->exists()
            || in_array(strtolower($booking->status ?? ''), ['settlement done', 'checked out'])
            || ((float)$booking->balance <= 0.01 && (float)$booking->payable_amount > 0 && ((float)$booking->advance_amount > 0 || $booking->payments()->exists()));

        // Strict Lock: Disallow modifying record once Settlement Done, Settled, or Checked Out
        if ($hasSettled || in_array($booking->status, ['Settlement Done', 'Checked Out'])) {
            return response()->json([
                'success' => false,
                'message' => "Record is locked. This guest has completed settlement (status: '{$booking->status}') and cannot be modified.",
                'error' => "Record is locked. This guest has completed settlement (status: '{$booking->status}') and cannot be modified."
            ], 403);
        }

        // Prevent checkout if there is still outstanding balance (Allowed without settlement if registration type is Company)
        $regName = strtolower(trim($booking->registrationType?->name ?? ''));
        $isCompanyReg = ($regName === 'company' || !empty($booking->company_id));

        if ($newStatus === 'Checked Out' && (float)$booking->balance > 0.01 && !$isCompanyReg) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot check out guest with outstanding balance of ₹ ' . number_format($booking->balance, 2) . '. Complete settlement first.',
                'error' => 'Cannot check out guest with outstanding balance of ₹ ' . number_format($booking->balance, 2) . '. Complete settlement first.'
            ], 422);
        }

        // Find ALL related bookings under this reservation / room / guest dossier:
        $masterBookingId = $booking->primary_booking_id ?: $booking->id;
        $reserveId = $booking->reserve_id;
        $settleNumber = $booking->settle_number ?: $booking->folio_number;

        $allRelatedBookings = Booking::with(['room', 'guest', 'settlements'])
            ->where(function ($q) use ($masterBookingId, $reserveId, $settleNumber) {
                $q->where('id', $masterBookingId)
                  ->orWhere('primary_booking_id', $masterBookingId);
                if (!empty($reserveId)) {
                    $q->orWhere('reserve_id', $reserveId);
                }
                if (!empty($settleNumber)) {
                    $q->orWhere('settle_number', $settleNumber);
                }
            })
            ->get();

        if ($allRelatedBookings->isEmpty()) {
            $allRelatedBookings = collect([$booking]);
        }

        // Synchronize status across ALL related bookings and guests
        foreach ($allRelatedBookings as $b) {
            $b->status = $newStatus;
            if ($newStatus === 'Checked In') {
                if (empty($b->checkin_date)) {
                    $b->checkin_date = now()->toDateString();
                }
                if (empty($b->checkin_time)) {
                    $b->checkin_time = now()->format('H:i');
                }
            } elseif ($newStatus === 'Checked Out') {
                $b->checkout_date = now()->toDateString();
                $b->checkout_time = now()->format('H:i');
            }
            $b->save();
        }

        // Collect all distinct rooms allocated across this reservation
        $allRoomIds = $allRelatedBookings->pluck('room_id')->filter()->unique();

        // If Arrived or Checked In, update room operational state to Active for ALL related rooms
        if (in_array($newStatus, ['Arrived', 'Checked In']) && $allRoomIds->isNotEmpty()) {
            $activeOp = OperationalStatus::where('name', 'like', '%Active%')->first();
            if ($activeOp) {
                foreach ($allRoomIds as $rId) {
                    RoomOperationalHistory::create([
                        'room_id' => $rId,
                        'operational_status_id' => $activeOp->id,
                    ]);
                }
            }
        }

        // If Checked Out, create checkout records & set dirty housekeeping state for ALL related bookings & rooms
        if ($newStatus === 'Checked Out') {
            $latestSettlement = Settlement::whereIn('booking_id', $allRelatedBookings->pluck('id'))->latest('id')->first();
            $dirtyState = HousekeepingState::where('name', 'like', '%dirty%')->first();

            foreach ($allRelatedBookings as $b) {
                $totalPaid = round((float)$b->advance_amount + (float)Payment::where('booking_id', $b->id)->where('payment_type', 'Settlement')->sum('amount'), 2);
                Checkout::firstOrCreate(
                    ['booking_id' => $b->id],
                    [
                        'guest_id' => $b->guest_id,
                        'room_id' => $b->room_id,
                        'settlement_id' => $latestSettlement?->id,
                        'checkout_date' => now()->toDateString(),
                        'checkout_time' => now()->format('H:i'),
                        'actual_checkout_at' => now(),
                        'total_nights' => $b->total_nights ?: 1,
                        'room_charges' => $b->total_amount ?: 0.00,
                        'extra_charges' => 0.00,
                        'late_checkout_charge' => 0.00,
                        'discount_amount' => $b->discount_amount ?: 0.00,
                        'tax_amount' => $b->gst_amount ?: 0.00,
                        'grand_total' => $b->payable_amount ?: 0.00,
                        'paid_amount' => $totalPaid ?: $b->payable_amount,
                        'balance_amount' => 0.00,
                        'status' => 'Completed',
                        'remarks' => 'Checked out via Guest CRM Directory',
                    ]
                );
            }

            if ($dirtyState && $allRoomIds->isNotEmpty()) {
                foreach ($allRoomIds as $rId) {
                    RoomHousekeepingHistory::create([
                        'room_id' => $rId,
                        'housekeeping_status_id' => $dirtyState->id,
                        'start_time' => now(),
                        'status' => 'pending',
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Guest status updated to '{$newStatus}' successfully.",
            'status' => $newStatus
        ]);
    }

    public function settle(Request $request, $id)
    {
        $guest = Guest::with(['latestBooking.payments', 'company'])->find($id);
        if (!$guest) {
            return response()->json(['success' => false, 'error' => 'Guest record not found'], 404);
        }

        $booking = $guest->latestBooking;
        if (!$booking) {
            return response()->json(['success' => false, 'error' => 'No booking record found for this guest'], 400);
        }

        if (in_array($booking->status, ['Settlement Done', 'Checked Out'])) {
            return response()->json(['success' => false, 'error' => 'This reservation has already completed settlement and is locked.'], 400);
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_mode_id' => 'nullable|exists:payment_modes,id',
            'remarks' => 'nullable|string|max:500',
            'transaction_id' => 'nullable|string|max:100',
        ]);

        $settleAmount = (float)$request->input('amount');
        $currentBalance = (float)$booking->balance;

        if ($currentBalance <= 0) {
            return response()->json(['success' => false, 'error' => 'This booking balance has already been fully settled.'], 400);
        }

        if ($settleAmount > ($currentBalance + 0.01)) {
            return response()->json(['success' => false, 'error' => 'Settlement amount cannot exceed outstanding balance of ₹ ' . number_format($currentBalance, 2)], 422);
        }

        DB::beginTransaction();
        try {
            $newBalance = max(0, round($currentBalance - $settleAmount, 2));

            // 1. Update Booking balance & status (advance_amount is preserved!)
            $booking->balance = $newBalance;
            if ($newBalance <= 0) {
                $booking->status = 'Settlement Done';
            }
            $booking->save();

            // Also update any secondary bookings under the same reservation
            if ($booking->reserve_id && $newBalance <= 0) {
                Booking::where('reserve_id', $booking->reserve_id)
                    ->where('id', '!=', $booking->id)
                    ->update([
                        'balance' => 0.00,
                        'status' => 'Settlement Done',
                    ]);
            }

            // 2. Create Payment Record (payment_type = 'Settlement')
            $paymentModeId = $request->input('payment_mode_id') ?: PaymentMode::where('name', 'Cash')->value('id');
            Payment::create([
                'guest_id' => $guest->id,
                'booking_id' => $booking->id,
                'payment_mode_id' => $paymentModeId,
                'amount' => $settleAmount,
                'advance_amount' => (float)$booking->advance_amount, // Preserves initial advance
                'balance' => $newBalance,
                'folio_number' => $booking->folio_number,
                'payment_remarks' => $request->input('remarks', 'Settlement payment'),
                'payment_date' => now()->toDateString(),
                'payment_time' => now()->format('H:i:s'),
                'payment_type' => 'Settlement',
                'transaction_id' => $request->input('transaction_id') ?: ('TXN-' . strtoupper(uniqid())),
                'status' => 'Success',
            ]);

            // 3. Create Settlement Record in settlements table
            $settlementNumber = 'SET-' . date('Ymd') . '-' . sprintf('%04d', $guest->id);
            Settlement::create([
                'settlement_number' => $settlementNumber,
                'folio_number' => $booking->folio_number,
                'guest_id' => $guest->id,
                'booking_id' => $booking->id,
                'payment_mode_id' => $paymentModeId,
                'total_room_charges' => $booking->total_amount,
                'total_extra_charges' => 0.00,
                'discount_amount' => $booking->discount_amount,
                'tax_amount' => $booking->gst_amount,
                'payable_amount' => $booking->payable_amount,
                'advance_adjusted' => (float)$booking->advance_amount, // The actual advance adjusted
                'paid_amount' => $settleAmount, // The settlement amount paid now!
                'balance_amount' => $newBalance,
                'settlement_date' => now()->toDateString(),
                'settlement_time' => now()->format('H:i:s'),
                'status' => $newBalance <= 0 ? 'Settled' : 'Partial',
                'remarks' => $request->input('remarks', 'Settlement received'),
            ]);

            DB::commit();

            $totalSettled = (float)Payment::where('booking_id', $booking->id)->where('payment_type', 'Settlement')->sum('amount');
            $totalPaid = round((float)$booking->advance_amount + $totalSettled, 2);

            return response()->json([
                'success' => true,
                'message' => 'Settlement of ₹ ' . number_format($settleAmount, 2) . ' processed successfully! Status is now Settlement Done (Locked).',
                'new_balance' => $newBalance,
                'advance_amount' => (float)$booking->advance_amount,
                'settlement_paid' => $totalSettled,
                'total_paid' => $totalPaid,
                'status' => $booking->status,
                'is_fully_settled' => ($newBalance <= 0),
                'settlement_number' => $settlementNumber,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => 'Settlement failed: ' . $e->getMessage()], 500);
        }
    }

    public function checkout(Request $request, $id)
    {
        $guest = Guest::with(['latestBooking.room', 'latestBooking.registrationType', 'company'])->find($id);
        if (!$guest) {
            return response()->json(['success' => false, 'error' => 'Guest record not found'], 404);
        }

        $booking = $guest->latestBooking;
        if (!$booking) {
            return response()->json(['success' => false, 'error' => 'No active booking found for this guest'], 400);
        }

        if ($booking->status === 'Checked Out') {
            return response()->json(['success' => false, 'error' => 'This reservation is already Checked Out and locked.'], 400);
        }

        $currentBalance = (float)$booking->balance;
        $regName = strtolower(trim($booking->registrationType?->name ?? ($guest->registrationType?->name ?? '')));
        $isCompanyReg = ($regName === 'company' || !empty($booking->company_id) || !empty($guest->company_id));

        if ($currentBalance > 0.01 && !$isCompanyReg) {
            return response()->json([
                'success' => false,
                'error' => 'Cannot check out guest with outstanding balance of ₹ ' . number_format($currentBalance, 2) . '. Complete settlement first.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $checkoutDate = now()->toDateString();
            $checkoutTime = now()->format('H:i:s');

            // 1. Update Booking status to Checked Out
            $booking->status = 'Checked Out';
            $booking->checkout_date = $checkoutDate;
            $booking->checkout_time = $checkoutTime;
            $booking->save();

            // 2. Create Checkout record
            $latestSettlement = Settlement::where('booking_id', $booking->id)->latest('id')->first();
            $totalPaid = round((float)$booking->advance_amount + (float)Payment::where('booking_id', $booking->id)->where('payment_type', 'Settlement')->sum('amount'), 2);
            Checkout::create([
                'guest_id' => $guest->id,
                'booking_id' => $booking->id,
                'room_id' => $booking->room_id,
                'settlement_id' => $latestSettlement?->id,
                'checkout_date' => $checkoutDate,
                'checkout_time' => $checkoutTime,
                'actual_checkout_at' => now(),
                'total_nights' => $booking->total_nights,
                'room_charges' => $booking->total_amount,
                'extra_charges' => 0.00,
                'late_checkout_charge' => 0.00,
                'discount_amount' => $booking->discount_amount,
                'tax_amount' => $booking->gst_amount,
                'grand_total' => $booking->payable_amount,
                'paid_amount' => $totalPaid ?: $booking->payable_amount,
                'balance_amount' => 0.00,
                'status' => 'Completed',
                'remarks' => $request->input('remarks', 'Checked out via Front Desk CRM'),
            ]);

            // 3. Update room housekeeping state to dirty
            if ($booking->room_id) {
                $dirtyState = HousekeepingState::where('name', 'like', '%dirty%')->first();
                if ($dirtyState) {
                    RoomHousekeepingHistory::create([
                        'room_id' => $booking->room_id,
                        'housekeeping_status_id' => $dirtyState->id,
                        'start_time' => now(),
                        'status' => 'pending',
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Guest successfully checked out! Room has been scheduled for housekeeping.',
                'status' => 'Checked Out',
                'checkout_date' => \Carbon\Carbon::parse($checkoutDate)->format('d M Y'),
                'checkout_time' => date('h:i A', strtotime($checkoutTime)),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => 'Checkout failed: ' . $e->getMessage()], 500);
        }
    }
}
