<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Floor;
use App\Models\RoomCategory;
use App\Models\BeddingConfig;
use App\Models\Amenity;
use App\Models\HousekeepingState;
use App\Models\OperationalStatus;
use App\Models\RoomMaintenance;
use Illuminate\Http\Request;

class RoomManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::with('latestMaintenance')->orderBy('room_number', 'asc');

        // Filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('room_number', 'like', "%{$search}%")
                  ->orWhere('floor', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('bedding_config', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhere('housekeeping_status', 'like', "%{$search}%");
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
        $housekeepingStates = HousekeepingState::where('status', 'Active')->get();
        $operationalStatuses = OperationalStatus::where('status', 'Active')->get();
        $amenities = Amenity::where('status', 'Active')->get();

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
            'housekeepingStates',
            'operationalStatuses',
            'amenities',
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
            'operational_status' => 'nullable|string|max:100',
            'housekeeping_state' => 'nullable|string|max:100',
            'amenities' => 'nullable|array',
            'status' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $status = $validated['status'] ?? 'Active';
        $operationalStatus = $validated['operational_status'] ?? null;
        $hkStatus = $validated['housekeeping_state'] ?? null;

        $fl = Floor::where('floor', $validated['floor'])->orWhere('name', 'like', '%' . $validated['floor'] . '%')->first();
        $cat = RoomCategory::where('name', $validated['category'])->first();
        $bed = BeddingConfig::where('name', $validated['bedding_config'] ?? '')->first();
        $ops = $operationalStatus ? OperationalStatus::where('name', $operationalStatus)->first() : null;
        $hks = $hkStatus ? HousekeepingState::where('name', $hkStatus)->first() : null;

        $room = Room::create([
            'room_number' => $validated['room_number'],
            'floor_id' => $fl ? $fl->id : null,
            'category_id' => $cat ? $cat->id : null,
            'bedding_config_id' => $bed ? $bed->id : null,
            'operational_status_id' => $ops ? $ops->id : null,
            'housekeeping_state_id' => $hks ? $hks->id : null,
            'floor' => $validated['floor'],
            'category' => $validated['category'],
            'rate' => $validated['rate'],
            'bedding_config' => $validated['bedding_config'] ?? null,
            'status' => $status,
            'housekeeping_status' => $hkStatus,
            'amenities' => $validated['amenities'] ?? [],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Auto update floor count (1+1+1)
        if ($fl) {
            $fl->rooms = Room::where('floor_id', $fl->id)->orWhere('floor', $fl->floor)->orWhere('floor', $fl->name)->count();
            $fl->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Room #' . $room->room_number . ' registered successfully.',
            'data' => $room,
        ]);
    }

    public function update(Request $request, $id)
    {
        $room = Room::findOrFail($id);
        $oldFloor = $room->floor;
        $oldFloorId = $room->floor_id;

        $validated = $request->validate([
            'room_number' => 'required|string|max:50|unique:rooms,room_number,' . $id,
            'floor' => 'required|string|max:100',
            'category' => 'required|string|max:100',
            'rate' => 'required|numeric|min:0',
            'bedding_config' => 'nullable|string|max:255',
            'operational_status' => 'nullable|string|max:100',
            'housekeeping_state' => 'nullable|string|max:100',
            'amenities' => 'nullable|array',
            'status' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $status = $validated['status'] ?? $room->status ?? 'Active';
        $operationalStatus = $validated['operational_status'] ?? null;
        $hkStatus = $validated['housekeeping_state'] ?? null;

        $fl = Floor::where('floor', $validated['floor'])->orWhere('name', 'like', '%' . $validated['floor'] . '%')->first();
        $cat = RoomCategory::where('name', $validated['category'])->first();
        $bed = BeddingConfig::where('name', $validated['bedding_config'] ?? '')->first();
        $ops = $operationalStatus ? OperationalStatus::where('name', $operationalStatus)->first() : null;
        $hks = $hkStatus ? HousekeepingState::where('name', $hkStatus)->first() : null;

        $room->update([
            'room_number' => $validated['room_number'],
            'floor_id' => $fl ? $fl->id : null,
            'category_id' => $cat ? $cat->id : null,
            'bedding_config_id' => $bed ? $bed->id : null,
            'operational_status_id' => $ops ? $ops->id : $room->operational_status_id,
            'housekeeping_state_id' => $hks ? $hks->id : $room->housekeeping_state_id,
            'floor' => $validated['floor'],
            'category' => $validated['category'],
            'rate' => $validated['rate'],
            'bedding_config' => $validated['bedding_config'] ?? null,
            'status' => $status,
            'housekeeping_status' => $hkStatus ?? $room->housekeeping_status,
            'amenities' => $validated['amenities'] ?? [],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Sync floor count for both old and new floor
        if ($oldFloorId) {
            $oldF = Floor::find($oldFloorId);
            if ($oldF) {
                $oldF->rooms = Room::where('floor_id', $oldF->id)->orWhere('floor', $oldF->floor)->orWhere('floor', $oldF->name)->count();
                $oldF->save();
            }
        }
        if ($fl) {
            $fl->rooms = Room::where('floor_id', $fl->id)->orWhere('floor', $fl->floor)->orWhere('floor', $fl->name)->count();
            $fl->save();
        }

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
        $floorId = $room->floor_id;
        $room->delete();

        if ($floorId) {
            $fl = Floor::find($floorId);
            if ($fl) {
                $fl->rooms = Room::where('floor_id', $fl->id)->orWhere('floor', $fl->floor)->orWhere('floor', $fl->name)->count();
                $fl->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Room #' . $number . ' removed successfully.',
        ]);
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No rooms selected for deletion.'], 422);
        }

        $count = Room::whereIn('id', $ids)->delete();

        // Recalculate room counts across all floors
        $floors = Floor::all();
        foreach ($floors as $fl) {
            $fl->rooms = Room::where('floor_id', $fl->id)->orWhere('floor', $fl->floor)->orWhere('floor', $fl->name)->count();
            $fl->save();
        }

        return response()->json([
            'success' => true,
            'message' => "{$count} room asset(s) deleted successfully.",
        ]);
    }

    public function toggleMaintenance(Request $request, $id)
    {
        $room = Room::findOrFail($id);
        if ($room->status === 'Maintenance' || $room->status === 'Under Maintenance') {
            $ops = OperationalStatus::where('name', 'Active')->orWhere('name', 'Active In-Service')->first();
            $hks = HousekeepingState::where('name', 'Cleaned')->orWhere('name', 'Cleaned & Inspected')->first();
            $room->update([
                'status' => 'Active',
                'operational_status_id' => $ops ? $ops->id : null,
                'housekeeping_status' => 'Cleaned',
                'housekeeping_state_id' => $hks ? $hks->id : null,
            ]);

            // Update active maintenance data status to Completed (preserve data, do NOT delete)
            RoomMaintenance::where('room_id', $room->id)
                ->where('status', '!=', 'Completed')
                ->update(['status' => 'Completed']);

            $msg = 'Room #' . $room->room_number . ' marked as Active & In-Service.';
        } else {
            $ops = OperationalStatus::where('name', 'Under Maintenance')->orWhere('name', 'Maintenance')->first();
            $room->update([
                'status' => $ops ? $ops->name : 'Under Maintenance',
                'operational_status_id' => $ops ? $ops->id : null,
                'notes' => $request->input('note') ?? $room->notes,
            ]);

            // Create new RoomMaintenance entry with status Active
            RoomMaintenance::create([
                'room_id' => $room->id,
                'reason' => $request->input('reason'),
                'assign' => $request->input('assign') ?? $request->input('engineer'),
                'expected_date_time' => $request->input('expected_date_time') ?? $request->input('completion'),
                'note' => $request->input('note'),
                'status' => $request->input('status', 'Active'), // Active, Checking, Completed
            ]);

            $msg = 'Room #' . $room->room_number . ' marked as Under Maintenance.';
        }

        $room->load('latestMaintenance');

        return response()->json([
            'success' => true,
            'message' => $msg,
            'data' => $room,
        ]);
    }
}
