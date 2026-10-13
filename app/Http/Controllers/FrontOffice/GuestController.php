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

        $guests = Guest::with([
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
        ->orderBy('id', 'desc')
        ->get();

        $stats = [
            'total' => $guests->count(),
            'confirmed' => $guests->where('status', 'Confirmed')->count(),
            'checked_in' => $guests->where('status', 'Checked In')->count(),
            'arrived' => $guests->where('status', 'Arrived')->count(),
            'stay_over' => $guests->where('status', 'Stay Over')->count(),
            'checked_out' => $guests->where('status', 'Checked Out')->count(),
            'cancelled' => $guests->where('status', 'Cancelled')->count(),
            'vip' => $guests->where('has_privilege_card', true)->count(),
            'corporate' => $guests->filter(fn($g) => $g->company_id || $g->new_company_name)->count(),
        ];

        return view('frontoffice.guest.index', compact(
            'guests',
            'stats',
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
