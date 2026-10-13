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
use App\Models\HousekeepingState;
use App\Models\OperationalStatus;
use App\Models\RoomHousekeepingHistory;
use App\Models\RoomOperationalHistory;
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

        // Fetch all guests ordered latest first
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
        ])->latest('id');

        if ($request->filled('status') && $request->status !== 'All') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('guest_name', 'like', "%{$s}%")
                  ->orWhere('mobile', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('reserve_id', 'like', "%{$s}%")
                  ->orWhere('folio_number', 'like', "%{$s}%")
                  ->orWhereHas('room', function ($rq) use ($s) {
                      $rq->where('room_number', 'like', "%{$s}%");
                  });
            });
        }

        $guests = $query->get();

        // Calculate statistics
        $stats = [
            'total' => Guest::count(),
            'confirmed' => Guest::where('status', 'Confirmed')->count(),
            'arrived' => Guest::where('status', 'Arrived')->count(),
            'checked_in' => Guest::where('status', 'Checked In')->count(),
            'stay_over' => Guest::where('status', 'Stay Over')->count(),
            'checked_out' => Guest::where('status', 'Checked Out')->count(),
            'cancelled' => Guest::where('status', 'Cancelled')->count(),
        ];

        return view('frontoffice.arrivals.index', compact(
            'guests',
            'stats',
            'categories',
            'floors',
            'companies',
            'idCardTypes',
            'reservationModes',
            'paymentModes',
            'rackRooms'
        ));
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|string|max:50',
        ]);

        $guest = Guest::with(['room'])->findOrFail($id);
        $oldStatus = $guest->status ?: 'Confirmed';
        $newStatus = $validated['status'];

        $guest->status = $newStatus;
        $guest->save();

        if (in_array($newStatus, ['Arrived', 'Checked In']) && $guest->room) {
            $activeOp = OperationalStatus::where('name', 'like', '%Active%')->first();
            if ($activeOp) {
                RoomOperationalHistory::create([
                    'room_id' => $guest->room->id,
                    'operational_status_id' => $activeOp->id,
                ]);
            }
        }

        $badgeClass = match ($newStatus) {
            'Confirmed' => 'blue',
            'Arrived' => 'green',
            'Checked In' => 'cyan',
            'Stay Over' => 'yellow',
            'Checked Out' => 'purple',
            'Cancelled' => 'red',
            default => 'gray',
        };

        return response()->json([
            'success' => true,
            'message' => "Guest {$guest->guest_name}'s status successfully updated from '{$oldStatus}' to '{$newStatus}'.",
            'status' => $newStatus,
            'badge_class' => $badgeClass,
        ]);
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
            'primaryGuest',
            'additionalGuests',
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'guest' => $guest,
        ]);
    }
}
