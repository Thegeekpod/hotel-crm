<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Floor;
use App\Models\RoomCategory;
use App\Models\BeddingConfig;
use App\Models\PaxCapacity;
use App\Models\Amenity;
use App\Models\HousekeepingState;
use App\Models\OperationalStatus;
use App\Models\MaintenanceReason;
use App\Models\MaintenanceEngineer;
use Illuminate\Http\Request;

class RoomManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::query()->orderBy('room_number', 'asc');

        // Filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('room_number', 'like', "%{$search}%")
                  ->orWhere('floor', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('bedding_config', 'like', "%{$search}%")
                  ->orWhere('pax_capacity', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhere('housekeeping_status', 'like', "%{$search}%")
                  ->orWhere('guest_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category') && $request->input('category') !== 'ALL') {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('floor') && $request->input('floor') !== 'all') {
            $query->where('floor', $request->input('floor'));
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $st = $request->input('status');
            if ($st === 'Maintenance' || $st === 'Under Maintenance') {
                $query->where(function($q) {
                    $q->where('status', 'Maintenance')->orWhere('status', 'Under Maintenance');
                });
            } else {
                $query->where('status', $st);
            }
        }

        if ($request->filled('housekeeping') && $request->input('housekeeping') !== 'all') {
            $query->where('housekeeping_status', $request->input('housekeeping'));
        }

        $rooms = $query->get();

        // Masters for select2 & checkboxes
        $floors = Floor::where('status', 'Active')->orderBy('floor', 'asc')->get();
        $categories = RoomCategory::where('status', 'Active')->get();
        $beddingConfigs = BeddingConfig::where('status', 'Active')->get();
        $paxCapacities = PaxCapacity::where('status', 'Active')->get();
        $housekeepingStates = HousekeepingState::where('status', 'Active')->get();
        $operationalStatuses = OperationalStatus::where('status', 'Active')->get();
        $amenities = Amenity::where('status', 'Active')->get();
        $maintenanceReasons = MaintenanceReason::where('status', 'Active')->get();
        $maintenanceEngineers = MaintenanceEngineer::where('status', 'Active')->get();

        // Stats
        $totalRooms = Room::count();
        $activeRooms = Room::where('status', 'Active')->count();
        $maintenanceRooms = Room::where('status', 'Maintenance')->orWhere('status', 'Under Maintenance')->count();
        $cleanedRooms = Room::where('housekeeping_status', 'Cleaned')->count();
        $dirtyRooms = Room::where('housekeeping_status', 'Dirty')->count();
        $inspectingRooms = Room::where('housekeeping_status', 'Inspecting')->count();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $rooms,
                'count' => $rooms->count(),
                'stats' => [
                    'total' => $totalRooms,
                    'active' => $activeRooms,
                    'maintenance' => $maintenanceRooms,
                    'cleaned' => $cleanedRooms,
                    'dirty' => $dirtyRooms,
                    'inspecting' => $inspectingRooms,
                ]
            ]);
        }

        return view('admin.roommanagement.index', compact(
            'rooms',
            'floors',
            'categories',
            'beddingConfigs',
            'paxCapacities',
            'housekeepingStates',
            'operationalStatuses',
            'amenities',
            'maintenanceReasons',
            'maintenanceEngineers',
            'totalRooms',
            'activeRooms',
            'maintenanceRooms',
            'cleanedRooms',
            'dirtyRooms',
            'inspectingRooms'
        ));
    }

    public function show($id)
    {
        $room = Room::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $room,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_number' => 'required|string|max:50|unique:rooms,room_number',
            'floor' => 'required|string|max:100',
            'category' => 'required|string|max:100',
            'rate' => 'required|numeric|min:0',
            'bedding_config' => 'nullable|string|max:255',
            'pax_capacity' => 'nullable|string|max:255',
            'operational_status' => 'nullable|string|max:100',
            'housekeeping_state' => 'nullable|string|max:100',
            'amenities' => 'nullable|array',
            'status' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $status = $validated['operational_status'] ?? $validated['status'] ?? 'Active';
        $hkStatus = $validated['housekeeping_state'] ?? 'Cleaned';

        $fl = Floor::where('floor', $validated['floor'])->orWhere('name', 'like', '%' . $validated['floor'] . '%')->first();
        $cat = RoomCategory::where('name', $validated['category'])->orWhere('code', $validated['category'])->first();
        $bed = BeddingConfig::where('name', $validated['bedding_config'] ?? '')->first();
        $pax = PaxCapacity::where('name', $validated['pax_capacity'] ?? '')->first();
        $ops = OperationalStatus::where('name', $status)->first();
        $hks = HousekeepingState::where('name', $hkStatus)->first();

        $room = Room::create([
            'room_number' => $validated['room_number'],
            'floor_id' => $fl ? $fl->id : null,
            'category_id' => $cat ? $cat->id : null,
            'bedding_config_id' => $bed ? $bed->id : null,
            'pax_capacity_id' => $pax ? $pax->id : null,
            'operational_status_id' => $ops ? $ops->id : null,
            'housekeeping_state_id' => $hks ? $hks->id : null,
            'floor' => $validated['floor'],
            'category' => $validated['category'],
            'rate' => $validated['rate'],
            'bedding_config' => $validated['bedding_config'] ?? null,
            'pax_capacity' => $validated['pax_capacity'] ?? null,
            'status' => $status,
            'housekeeping_status' => $hkStatus,
            'amenities' => $validated['amenities'] ?? [],
            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Room #' . $room->room_number . ' registered successfully.',
            'data' => $room,
        ]);
    }

    public function update(Request $request, $id)
    {
        $room = Room::findOrFail($id);

        $validated = $request->validate([
            'room_number' => 'required|string|max:50|unique:rooms,room_number,' . $id,
            'floor' => 'required|string|max:100',
            'category' => 'required|string|max:100',
            'rate' => 'required|numeric|min:0',
            'bedding_config' => 'nullable|string|max:255',
            'pax_capacity' => 'nullable|string|max:255',
            'operational_status' => 'nullable|string|max:100',
            'housekeeping_state' => 'nullable|string|max:100',
            'amenities' => 'nullable|array',
            'status' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $status = $validated['operational_status'] ?? $validated['status'] ?? $room->status;
        $hkStatus = $validated['housekeeping_state'] ?? $room->housekeeping_status;

        $fl = Floor::where('floor', $validated['floor'])->orWhere('name', 'like', '%' . $validated['floor'] . '%')->first();
        $cat = RoomCategory::where('name', $validated['category'])->orWhere('code', $validated['category'])->first();
        $bed = BeddingConfig::where('name', $validated['bedding_config'] ?? '')->first();
        $pax = PaxCapacity::where('name', $validated['pax_capacity'] ?? '')->first();
        $ops = OperationalStatus::where('name', $status)->first();
        $hks = HousekeepingState::where('name', $hkStatus)->first();

        $room->update([
            'room_number' => $validated['room_number'],
            'floor_id' => $fl ? $fl->id : null,
            'category_id' => $cat ? $cat->id : null,
            'bedding_config_id' => $bed ? $bed->id : null,
            'pax_capacity_id' => $pax ? $pax->id : null,
            'operational_status_id' => $ops ? $ops->id : null,
            'housekeeping_state_id' => $hks ? $hks->id : null,
            'floor' => $validated['floor'],
            'category' => $validated['category'],
            'rate' => $validated['rate'],
            'bedding_config' => $validated['bedding_config'] ?? null,
            'pax_capacity' => $validated['pax_capacity'] ?? null,
            'status' => $status,
            'housekeeping_status' => $hkStatus,
            'amenities' => $validated['amenities'] ?? [],
            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Room #' . $room->room_number . ' updated successfully.',
            'data' => $room,
        ]);
    }

    public function destroy($id)
    {
        $room = Room::findOrFail($id);
        $number = $room->room_number;
        $room->delete();

        return response()->json([
            'success' => true,
            'message' => 'Room #' . $number . ' removed successfully.',
        ]);
    }

    public function toggleMaintenance(Request $request, $id)
    {
        $room = Room::findOrFail($id);
        if ($room->status === 'Maintenance' || $room->status === 'Under Maintenance') {
            $ops = OperationalStatus::where('name', 'Active')->first();
            $hks = HousekeepingState::where('name', 'Cleaned')->first();
            $room->update([
                'status' => 'Active',
                'operational_status_id' => $ops ? $ops->id : null,
                'housekeeping_status' => 'Cleaned',
                'housekeeping_state_id' => $hks ? $hks->id : null,
            ]);
            $msg = 'Room #' . $room->room_number . ' marked as Active & In-Service.';
        } else {
            $ops = OperationalStatus::where('name', 'Under Maintenance')->orWhere('name', 'Maintenance')->first();
            $room->update([
                'status' => $ops ? $ops->name : 'Under Maintenance',
                'operational_status_id' => $ops ? $ops->id : null,
            ]);
            $msg = 'Room #' . $room->room_number . ' marked as Under Maintenance.';
        }

        return response()->json([
            'success' => true,
            'message' => $msg,
            'data' => $room,
        ]);
    }
}
