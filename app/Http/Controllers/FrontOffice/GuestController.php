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
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Settlement;
use App\Models\Checkout;
use Illuminate\Http\Request;

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
            'bookings.room.categoryRelation',
            'payments.paymentMode',
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
                         ->orWhere('folio_number', 'like', "%{$s}%")
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

        return view('frontoffice.guest.index', compact(
            'guests',
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

        return response()->json([
            'success' => true,
            'guest' => $guest
        ]);
    }
}
