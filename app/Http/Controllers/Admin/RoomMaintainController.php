<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Floor;
use App\Models\RoomCategory;
use App\Models\RoomMaintenance;
use App\Models\OperationalStatus;
use Illuminate\Http\Request;

class RoomMaintainController extends Controller
{
    public function index(Request $request)
    {
        $query = RoomMaintenance::with('room')->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                  ->orWhere('assign', 'like', "%{$search}%")
                  ->orWhere('note', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhereHas('room', function ($rq) use ($search) {
                      $rq->where('room_number', 'like', "%{$search}%")
                         ->orWhere('floor', 'like', "%{$search}%")
                         ->orWhere('category', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('floor') && $request->input('floor') !== 'all') {
            $floor = $request->input('floor');
            $query->whereHas('room', function ($rq) use ($floor) {
                $rq->where('floor', $floor);
            });
        }

        if ($request->filled('category') && $request->input('category') !== 'all') {
            $cat = $request->input('category');
            $query->whereHas('room', function ($rq) use ($cat) {
                $rq->where('category', $cat);
            });
        }

        $items = $query->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $items,
                'count' => $items->count(),
            ]);
        }

        $rooms = Room::orderBy('room_number', 'asc')->get();
        $floors = Floor::where('status', 'Active')->orderBy('floor', 'asc')->get();
        $categories = RoomCategory::where('status', 'Active')->orderBy('name', 'asc')->get();

        $activeCount = RoomMaintenance::where('status', 'Active')->count();
        $checkingCount = RoomMaintenance::where('status', 'Checking')->count();
        $completedCount = RoomMaintenance::where('status', 'Completed')->count();

        return view('admin.roommaintain.index', compact(
            'items',
            'rooms',
            'floors',
            'categories',
            'activeCount',
            'checkingCount',
            'completedCount'
        ));
    }

    public function show($id)
    {
        $item = RoomMaintenance::with('room')->find($id);
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Maintenance record not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $item,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'reason' => 'required|string|max:255',
            'assign' => 'nullable|string|max:255',
            'expected_date_time' => 'nullable|string',
            'note' => 'nullable|string',
            'status' => 'required|string|in:Active,Checking,Completed',
        ]);

        if (!empty($validated['expected_date_time'])) {
            $validated['expected_date_time'] = date('Y-m-d H:i:s', strtotime($validated['expected_date_time']));
        }

        $item = RoomMaintenance::create($validated);

        // Sync room operational status
        $room = Room::find($validated['room_id']);
        if ($room) {
            if ($validated['status'] === 'Active' || $validated['status'] === 'Checking') {
                $ops = OperationalStatus::where('name', 'Under Maintenance')->orWhere('name', 'Maintenance')->first();
                $room->update([
                    'status' => $ops ? $ops->name : 'Under Maintenance',
                    'operational_status_id' => $ops ? $ops->id : $room->operational_status_id,
                ]);
            }
        }

        $item->load('room');

        return response()->json([
            'success' => true,
            'message' => 'Room maintenance task created successfully.',
            'data' => $item,
        ]);
    }

    public function update(Request $request, $id)
    {
        $item = RoomMaintenance::with('room')->find($id);
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Maintenance record not found.'], 404);
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:255',
            'assign' => 'nullable|string|max:255',
            'expected_date_time' => 'nullable|string',
            'note' => 'nullable|string',
            'status' => 'required|string|in:Active,Checking,Completed',
        ]);

        if (!empty($validated['expected_date_time'])) {
            $validated['expected_date_time'] = date('Y-m-d H:i:s', strtotime($validated['expected_date_time']));
        }

        $item->update($validated);

        // Sync Room operational status if maintenance marked Completed or Active
        if ($item->room) {
            if ($validated['status'] === 'Completed') {
                // Check if any other active/checking maintenance exists
                $hasOtherActive = RoomMaintenance::where('room_id', $item->room_id)
                    ->where('id', '!=', $item->id)
                    ->whereIn('status', ['Active', 'Checking'])
                    ->exists();

                if (!$hasOtherActive) {
                    $ops = OperationalStatus::where('name', 'Active')->orWhere('name', 'Active In-Service')->first();
                    $item->room->update([
                        'status' => 'Active',
                        'operational_status_id' => $ops ? $ops->id : $item->room->operational_status_id,
                    ]);
                }
            } elseif ($validated['status'] === 'Active' || $validated['status'] === 'Checking') {
                $ops = OperationalStatus::where('name', 'Under Maintenance')->orWhere('name', 'Maintenance')->first();
                $item->room->update([
                    'status' => $ops ? $ops->name : 'Under Maintenance',
                    'operational_status_id' => $ops ? $ops->id : $item->room->operational_status_id,
                ]);
            }
        }

        $item->load('room');

        return response()->json([
            'success' => true,
            'message' => 'Room maintenance status updated successfully.',
            'data' => $item,
        ]);
    }

    public function destroy($id)
    {
        $item = RoomMaintenance::find($id);
        if (!$item) {
            return response()->json([
                'success' => true,
                'message' => 'Maintenance record already removed.',
            ]);
        }

        $roomId = $item->room_id;
        $item->delete();

        // Check if room has remaining active maintenance
        $room = Room::find($roomId);
        if ($room) {
            $hasActive = RoomMaintenance::where('room_id', $roomId)
                ->whereIn('status', ['Active', 'Checking'])
                ->exists();

            if (!$hasActive && ($room->status === 'Under Maintenance' || $room->status === 'Maintenance')) {
                $ops = OperationalStatus::where('name', 'Active')->orWhere('name', 'Active In-Service')->first();
                $room->update([
                    'status' => 'Active',
                    'operational_status_id' => $ops ? $ops->id : null,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Maintenance record deleted successfully.',
        ]);
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No maintenance records selected.'], 422);
        }

        $records = RoomMaintenance::whereIn('id', $ids)->get();
        $roomIds = $records->pluck('room_id')->unique();

        $count = RoomMaintenance::whereIn('id', $ids)->delete();

        // Recheck statuses of affected rooms
        foreach ($roomIds as $rId) {
            $room = Room::find($rId);
            if ($room) {
                $hasActive = RoomMaintenance::where('room_id', $rId)
                    ->whereIn('status', ['Active', 'Checking'])
                    ->exists();

                if (!$hasActive && ($room->status === 'Under Maintenance' || $room->status === 'Maintenance')) {
                    $ops = OperationalStatus::where('name', 'Active')->orWhere('name', 'Active In-Service')->first();
                    $room->update([
                        'status' => 'Active',
                        'operational_status_id' => $ops ? $ops->id : null,
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => "{$count} maintenance record(s) deleted successfully.",
        ]);
    }
}
