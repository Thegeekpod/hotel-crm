<?php

namespace App\Http\Controllers\FrontOffice;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Floor;
use App\Models\RoomCategory;
use App\Models\HousekeepingState;
use App\Models\OperationalStatus;
use App\Models\RoomHousekeepingHistory;
use App\Models\RoomOperationalHistory;
use App\Models\RoomMaintenance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HousekeepingController extends Controller
{
    /**
     * Display live Housekeeping Management Dashboard
     */
    public function index(Request $request)
    {
        $selectedFloor = $request->query('floor');
        $selectedCategory = $request->query('category');
        $selectedHkState = $request->query('hk_state');
        $selectedOpStatus = $request->query('op_status');
        $selectedOccupancy = $request->query('occupancy');
        $search = $request->query('search');

        // Fetch all rooms with relations
        $query = Room::with([
            'floorRelation',
            'categoryRelation',
            'beddingConfigRelation',
            'latestHousekeepingHistory.housekeepingStatus',
            'latestOperationalHistory.operationalStatus',
            'currentBooking.guest.title',
            'latestMaintenance'
        ])->orderBy('room_number', 'asc');

        if ($request->filled('floor') && $selectedFloor !== 'all') {
            $query->where('floor_id', $selectedFloor);
        }

        if ($request->filled('category') && $selectedCategory !== 'all') {
            $query->where('category_id', $selectedCategory);
        }

        if ($request->filled('search')) {
            $s = trim($search);
            $query->where(function ($q) use ($s) {
                $q->where('room_number', 'like', "%{$s}%")
                  ->orWhereHas('currentBooking.guest', function ($gq) use ($s) {
                      $gq->where('guest_name', 'like', "%{$s}%");
                  });
            });
        }

        $allRooms = $query->get();

        // Additional in-memory filtering for HK / OP / Occupancy if selected
        $filteredRooms = $allRooms->filter(function ($room) use ($selectedHkState, $selectedOpStatus, $selectedOccupancy) {
            $latestHk = $room->latestHousekeepingHistory;
            $hkId = $latestHk?->housekeeping_status_id;

            $latestOp = $room->latestOperationalHistory;
            $opId = $latestOp?->operational_status_id;

            $isOccupied = !empty($room->currentBooking);

            if ($selectedHkState && $selectedHkState !== 'all' && $hkId != $selectedHkState) {
                return false;
            }

            if ($selectedOpStatus && $selectedOpStatus !== 'all' && $opId != $selectedOpStatus) {
                return false;
            }

            if ($selectedOccupancy && $selectedOccupancy !== 'all') {
                if ($selectedOccupancy === 'occupied' && !$isOccupied) return false;
                if ($selectedOccupancy === 'vacant' && $isOccupied) return false;
            }

            return true;
        });

        // Summary Counters
        $totalRooms = Room::count();
        $floors = Floor::where('status', 'Active')->orderBy('floor', 'asc')->get();
        $categories = RoomCategory::where('status', 'Active')->get();
        $housekeepingStates = HousekeepingState::where('status', 'Active')->orderBy('id', 'asc')->get();
        $operationalStatuses = OperationalStatus::where('status', 'Active')->orderBy('id', 'asc')->get();

        // Separate filtered rooms into Pending Tasks vs Completed Tasks based on HousekeepingState type
        $pendingRooms = collect();
        $completedRooms = collect();

        foreach ($filteredRooms as $rm) {
            $latestHk = $rm->latestHousekeepingHistory;
            $stateObj = $latestHk?->housekeepingStatus;
            $stateType = $stateObj?->type ?? (preg_match('/clean|sanit|ready/i', $stateObj?->name ?? 'clean') ? 'completed_task' : 'assign_task');

            // Categorize by the Master State Type
            if ($stateType === 'completed_task') {
                $completedRooms->push($rm);
            } else {
                $pendingRooms->push($rm);
            }
        }

        // Calculate KPI metrics across all rooms
        $cleanCount = 0;
        $dirtyCount = 0;
        $inspectCount = 0;
        $occupiedCount = 0;
        $vacantCount = 0;
        $maintenanceCount = 0;

        foreach ($allRooms as $rm) {
            $hkName = strtolower($rm->latestHousekeepingHistory?->housekeepingStatus?->name ?? 'clean');
            if (str_contains($hkName, 'dirty') || str_contains($hkName, 'due') || str_contains($hkName, 'cleaning')) {
                $dirtyCount++;
            } elseif (str_contains($hkName, 'inspect') || str_contains($hkName, 'touch')) {
                $inspectCount++;
            } else {
                $cleanCount++;
            }

            if (!empty($rm->currentBooking)) {
                $occupiedCount++;
            } else {
                $vacantCount++;
            }

            $opName = strtolower($rm->latestOperationalHistory?->operationalStatus?->name ?? 'active');
            if (str_contains($opName, 'maintenance') || str_contains($opName, 'block') || str_contains($opName, 'order')) {
                $maintenanceCount++;
            }
        }

        // Today's Housekeeping History Logs
        $todayLogs = RoomHousekeepingHistory::with(['room.categoryRelation', 'room.floorRelation', 'housekeepingStatus'])
            ->whereDate('created_at', Carbon::today())
            ->orderBy('id', 'desc')
            ->take(50)
            ->get();

        return view('frontoffice.housekeeping.index', compact(
            'filteredRooms',
            'pendingRooms',
            'completedRooms',
            'allRooms',
            'floors',
            'categories',
            'housekeepingStates',
            'operationalStatuses',
            'totalRooms',
            'cleanCount',
            'dirtyCount',
            'inspectCount',
            'occupiedCount',
            'vacantCount',
            'maintenanceCount',
            'selectedFloor',
            'selectedCategory',
            'selectedHkState',
            'selectedOpStatus',
            'selectedOccupancy',
            'search',
            'todayLogs'
        ));
    }

    /**
     * Update room housekeeping state dynamically
     */
    public function updateStatus(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'housekeeping_status_id' => 'required|exists:housekeeping_states,id',
            'operational_status_id' => 'nullable|exists:operational_statuses,id',
            'task_status' => 'nullable|string|in:pending,in_progress,complete',
            'remarks' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();
        try {
            $room = Room::findOrFail($validated['room_id']);
            $hkState = HousekeepingState::findOrFail($validated['housekeeping_status_id']);

            $isCompletedType = ($hkState->type === 'completed_task');
            $taskStatus = $validated['task_status'] ?? ($isCompletedType ? 'complete' : 'pending');

            $hkHistory = RoomHousekeepingHistory::create([
                'room_id' => $room->id,
                'housekeeping_status_id' => $hkState->id,
                'start_time' => now(),
                'completion_time' => ($isCompletedType || $taskStatus === 'complete' ? now() : null),
                'status' => $taskStatus,
            ]);

            // Update operational status if provided
            if (!empty($validated['operational_status_id'])) {
                RoomOperationalHistory::create([
                    'room_id' => $room->id,
                    'operational_status_id' => $validated['operational_status_id'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Room {$room->room_number} status updated to \"{$hkState->name}\"!",
                'room_id' => $room->id,
                'room_number' => $room->room_number,
                'is_complete' => $isCompletedType,
                'housekeeping_status' => $hkState->name,
                'history_id' => $hkHistory->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update housekeeping status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update multiple rooms housekeeping status
     */
    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'room_ids' => 'required|array|min:1',
            'room_ids.*' => 'exists:rooms,id',
            'housekeeping_status_id' => 'required|exists:housekeeping_states,id',
            'operational_status_id' => 'nullable|exists:operational_statuses,id',
        ]);

        DB::beginTransaction();
        try {
            $hkState = HousekeepingState::findOrFail($validated['housekeeping_status_id']);
            $isClean = (bool)preg_match('/clean|sanit|ready|inspected/i', $hkState->name);

            foreach ($validated['room_ids'] as $rId) {
                RoomHousekeepingHistory::create([
                    'room_id' => $rId,
                    'housekeeping_status_id' => $hkState->id,
                    'start_time' => now(),
                    'completion_time' => ($isClean ? now() : null),
                    'status' => ($isClean ? 'complete' : 'pending'),
                ]);

                if (!empty($validated['operational_status_id'])) {
                    RoomOperationalHistory::create([
                        'room_id' => $rId,
                        'operational_status_id' => $validated['operational_status_id'],
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($validated['room_ids']) . " room(s) updated to \"{$hkState->name}\" successfully!"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Bulk update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get room history logs for quick drawer / modal view
     */
    public function getRoomHistory($roomId)
    {
        $room = Room::with(['categoryRelation', 'floorRelation'])->findOrFail($roomId);
        
        $hkLogs = RoomHousekeepingHistory::with('housekeepingStatus')
            ->where('room_id', $roomId)
            ->orderBy('id', 'desc')
            ->take(20)
            ->get();

        $opLogs = RoomOperationalHistory::with('operationalStatus')
            ->where('room_id', $roomId)
            ->orderBy('id', 'desc')
            ->take(20)
            ->get();

        return response()->json([
            'success' => true,
            'room' => $room,
            'hk_logs' => $hkLogs,
            'op_logs' => $opLogs
        ]);
    }
}
