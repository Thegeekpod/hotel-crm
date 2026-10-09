<?php

namespace App\Http\Controllers\FrontOffice;

use App\Http\Controllers\Controller;
use App\Models\RoomCategory;
use App\Models\Floor;
use App\Models\Company;
use App\Models\IdCardType;
use App\Models\ReservationMode;
use App\Models\PaymentMode;
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

        $guests = [
            [
                'id' => 'GST-001',
                'name' => 'Sanjeev Kumar Singh',
                'phone' => '+91 98112 34567',
                'email' => 'sanjeev.ksingh@gmail.com',
                'id_proof' => 'AADHAR 9812 4567 8901',
                'total_stays' => 7,
                'tier' => 'Gold',
                'tier_style' => 'background: linear-gradient(135deg, rgba(245, 158, 11, 0.12), rgba(217, 119, 6, 0.08)); color: #b45309; border: 1px solid rgba(245, 158, 11, 0.3); font-weight: 700;',
                'tier_icon' => 'fa-crown',
                'last_visit' => '22 Sep 2026'
            ],
            [
                'id' => 'GST-002',
                'name' => 'AJEET BHENGRA',
                'phone' => '+91 97712 90123',
                'email' => 'ajeet.b@outlook.com',
                'id_proof' => 'PAN AAEPB8765K',
                'total_stays' => 3,
                'tier' => 'Silver',
                'tier_style' => 'background: rgba(148, 163, 184, 0.15); color: #475569; border: 1px solid rgba(148, 163, 184, 0.3); font-weight: 700;',
                'tier_icon' => 'fa-medal',
                'last_visit' => '21 Sep 2026'
            ],
            [
                'id' => 'GST-003',
                'name' => 'Vikramaditya Roy',
                'phone' => '+91 98300 77123',
                'email' => 'vikramaditya.roy@corp.in',
                'id_proof' => 'PASSPORT Z9812345',
                'total_stays' => 15,
                'tier' => 'Platinum',
                'tier_style' => 'background: linear-gradient(135deg, rgba(99, 102, 241, 0.12), rgba(139, 92, 246, 0.08)); color: #4f46e5; border: 1px solid rgba(99, 102, 241, 0.3); font-weight: 700;',
                'tier_icon' => 'fa-gem',
                'last_visit' => '23 Sep 2026'
            ],
            [
                'id' => 'GST-004',
                'name' => 'Dr. Ananya Sen',
                'phone' => '+91 98450 11982',
                'email' => 'dr.ananya.sen@hospital.org',
                'id_proof' => 'AADHAR 7654 3210 9876',
                'total_stays' => 12,
                'tier' => 'Gold',
                'tier_style' => 'background: linear-gradient(135deg, rgba(245, 158, 11, 0.12), rgba(217, 119, 6, 0.08)); color: #b45309; border: 1px solid rgba(245, 158, 11, 0.3); font-weight: 700;',
                'tier_icon' => 'fa-crown',
                'last_visit' => '21 Sep 2026'
            ],
        ];

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
