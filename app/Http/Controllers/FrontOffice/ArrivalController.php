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

        $dbGuests = Guest::with(['room.categoryRelation', 'title'])
            ->whereIn('status', ['Confirmed', 'Guaranteed', 'Waitlisted', 'Tentative'])
            ->latest('id')
            ->get();

        $arrivals = [];
        foreach ($dbGuests as $g) {
            $arrivals[] = [
                'ref' => $g->reserve_id ?: ('RES-' . $g->id),
                'guest_name' => ($g->title?->name ? $g->title->name . ' ' : '') . $g->guest_name,
                'category' => strtoupper($g->room?->categoryRelation?->name ?? 'DELUXE'),
                'room' => (string)($g->room?->room_number ?? 'Pending'),
                'stay_dates' => ($g->reserve_date ? $g->reserve_date->format('d M') : 'Today') . ' - ' . now()->addDays(2)->format('d M'),
                'pax' => 2,
                'advance' => '₹ ' . number_format($g->advance_amount),
                'status' => $g->status ?: 'Confirmed',
                'status_class' => ($g->status === 'Guaranteed' ? 'blue' : ($g->status === 'Confirmed' ? 'green' : 'yellow')),
            ];
        }

        if (empty($arrivals)) {
            $arrivals = [
                [
                    'ref' => '830\\2026-2027',
                    'guest_name' => 'Manish Khemka',
                    'category' => 'SUPER DELUXE',
                    'room' => '101',
                    'stay_dates' => now()->format('d M') . ' - ' . now()->addDays(2)->format('d M'),
                    'pax' => 2,
                    'advance' => '₹ 3,000',
                    'status' => 'Confirmed',
                    'status_class' => 'green'
                ],
            ];
        }

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
