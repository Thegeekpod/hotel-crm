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
            'room.categoryRelation',
            'room.floorRelation',
            'room.beddingConfigRelation',
            'title',
            'nationality',
            'idCardType',
            'company',
            'paymentMode',
            'registrationType',
            'reservationMode',
        ])
        ->orderBy('id', 'desc');

        $today = now()->toDateString();

        // From & To Date Filter (based on check-in date: reserve_date)
        // By default today is selected if no date parameters are present
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
            $query->whereDate('reserve_date', '>=', $fromDate);
        }
        if (!empty($toDate)) {
            $query->whereDate('reserve_date', '<=', $toDate);
        }

        // Backend Filter: Status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Backend Filter: Registration Type
        if ($request->filled('registration_type') && $request->registration_type !== 'all') {
            $query->whereHas('registrationType', function ($q) use ($request) {
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
                  ->orWhere('reserve_id', 'like', "%{$s}%")
                  ->orWhere('folio_number', 'like', "%{$s}%")
                  ->orWhere('city', 'like', "%{$s}%")
                  ->orWhere('id_card_number', 'like', "%{$s}%")
                  ->orWhereHas('registrationType', function ($rq) use ($s) {
                      $rq->where('name', 'like', "%{$s}%");
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
            'confirmed' => Guest::where('status', 'Confirmed')->count(),
            'checked_in' => Guest::where('status', 'Checked In')->count(),
            'arrived' => Guest::where('status', 'Arrived')->count(),
            'stay_over' => Guest::where('status', 'Stay Over')->count(),
            'checked_out' => Guest::where('status', 'Checked Out')->count(),
            'cancelled' => Guest::where('status', 'Cancelled')->count(),
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
            'room.categoryRelation',
            'room.floorRelation',
            'room.beddingConfigRelation',
            'title',
            'nationality',
            'idCardType',
            'company',
            'paymentMode',
            'registrationType',
            'reservationMode',
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
