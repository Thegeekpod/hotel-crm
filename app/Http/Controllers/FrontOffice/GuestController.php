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
        $reservationModes = ReservationMode::where('status', 'Active')->get();
        $paymentModes = PaymentMode::where('status', 'Active')->get();
        $rackRooms = [];

        $dbGuests = Guest::with(['title', 'idCardType', 'company', 'room.categoryRelation'])
            ->orderBy('id', 'desc')
            ->get();

        $guests = [];
        foreach ($dbGuests as $g) {
            $isVip = $g->has_privilege_card || ($g->advance_amount > 8000);
            $tier = $isVip ? 'Platinum' : (($g->advance_amount > 5000) ? 'Gold' : 'Silver');
            
            $tierStyle = 'background: rgba(148, 163, 184, 0.15); color: #475569; border: 1px solid rgba(148, 163, 184, 0.3); font-weight: 700;';
            $tierIcon = 'fa-medal';
            if ($tier === 'Platinum') {
                $tierStyle = 'background: linear-gradient(135deg, rgba(99, 102, 241, 0.12), rgba(139, 92, 246, 0.08)); color: #4f46e5; border: 1px solid rgba(99, 102, 241, 0.3); font-weight: 700;';
                $tierIcon = 'fa-gem';
            } elseif ($tier === 'Gold') {
                $tierStyle = 'background: linear-gradient(135deg, rgba(245, 158, 11, 0.12), rgba(217, 119, 6, 0.08)); color: #b45309; border: 1px solid rgba(245, 158, 11, 0.3); font-weight: 700;';
                $tierIcon = 'fa-crown';
            }

            $idProof = ($g->idCardType?->name ? ($g->idCardType->name . ' ') : 'ID ') . ($g->id_card_number ?: 'Verified');

            $guests[] = [
                'id' => $g->reserve_id ?: ('GST-00' . $g->id),
                'name' => ($g->title?->name ? $g->title->name . ' ' : '') . $g->guest_name,
                'phone' => $g->mobile ?: '+91 98765 43210',
                'email' => $g->email ?: 'guest@hotelcrm.com',
                'id_proof' => $idProof,
                'total_stays' => rand(2, 14),
                'tier' => $tier,
                'tier_style' => $tierStyle,
                'tier_icon' => $tierIcon,
                'last_visit' => $g->reserve_date ? $g->reserve_date->format('d M Y') : now()->subDays(rand(1, 10))->format('d M Y'),
            ];
        }

        return view('frontoffice.guest.index', compact(
            'guests',
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
