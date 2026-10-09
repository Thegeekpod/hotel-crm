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

class InhouseController extends Controller
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

        $dbInhouse = Guest::with(['room.categoryRelation', 'title'])
            ->whereIn('status', ['Arrived', 'Stay Over'])
            ->get();

        $discounts = \App\Models\Discount::where('status', 'Active')->orderBy('discount_percentage', 'asc')->get();

        $inhouseGuests = [];
        foreach ($dbInhouse as $g) {
            $departureStr = $g->checkout_date ? ($g->checkout_date->format('d M') . ' ' . ($g->checkout_time ?: '11:00')) : now()->addDays(1)->format('d M 11:00');
            $inhouseGuests[] = [
                'room' => (string)($g->room?->room_number ?? '101'),
                'type' => strtoupper($g->room?->categoryRelation?->name ?? 'DELUXE'),
                'name' => ($g->title?->name ? $g->title->name . ' ' : '') . $g->guest_name,
                'phone' => $g->mobile ?: '+91 98765 43210',
                'checkin' => ($g->reserve_date ? $g->reserve_date->format('d M') : now()->subDays(1)->format('d M')) . ' ' . ($g->reserve_time ?: '14:00'),
                'departure' => $departureStr,
                'pax' => 2,
                'status' => $g->status ?: 'Arrived',
                'status_class' => ($g->status === 'Arrived' ? 'green' : 'yellow'),
                'balance' => number_format($g->balance),
                'folio' => $g->folio_number ?: ('FOL-' . ($g->room?->room_number ?? '100') . '-990'),
            ];
        }

        return view('frontoffice.inhouse.index', compact(
            'inhouseGuests',
            'categories',
            'floors',
            'companies',
            'idCardTypes',
            'reservationModes',
            'paymentModes',
            'discounts',
            'rackRooms'
        ));
    }
}
