<?php

namespace App\Http\Controllers\Admin\Utilities;

use App\Http\Controllers\Controller;
use App\Models\Floor;
use App\Models\Room;
use Illuminate\Http\Request;

class FloorController extends Controller
{
    public function index(Request $request)
    {
        $query = Floor::query()->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('floor', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        $items = $query->get()->map(function ($floor) {
            $roomCount = Room::where('floor_id', $floor->id)
                ->orWhere('floor', $floor->floor)
                ->orWhere('floor', $floor->name)
                ->count();
            $floor->rooms = $roomCount;
            return $floor;
        });

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $items,
                'count' => $items->count(),
            ]);
        }

        return view('admin.utilities.roommanage.floor', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'floor' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'status' => 'required|string|in:Active,Maintenance,Inactive',
        ]);

        $item = Floor::create([
            'floor' => $validated['floor'],
            'name' => $validated['name'],
            'rooms' => 0,
            'status' => $validated['status'],
        ]);

        // Auto count if any existing rooms match this floor
        $roomCount = Room::where('floor_id', $item->id)
            ->orWhere('floor', $item->floor)
            ->orWhere('floor', $item->name)
            ->count();
        $item->rooms = $roomCount;
        $item->save();

        return response()->json([
            'success' => true,
            'message' => 'Floor created successfully.',
            'data' => $item,
        ]);
    }

    public function update(Request $request, $id)
    {
        $item = Floor::find($id);
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Floor not found.',
            ], 404);
        }

        $validated = $request->validate([
            'floor' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'status' => 'required|string|in:Active,Maintenance,Inactive',
        ]);

        $item->update([
            'floor' => $validated['floor'],
            'name' => $validated['name'],
            'status' => $validated['status'],
        ]);

        $roomCount = Room::where('floor_id', $item->id)
            ->orWhere('floor', $item->floor)
            ->orWhere('floor', $item->name)
            ->count();
        $item->rooms = $roomCount;
        $item->save();

        return response()->json([
            'success' => true,
            'message' => 'Floor updated successfully.',
            'data' => $item,
        ]);
    }

    public function destroy($id)
    {
        $item = Floor::find($id);
        if (!$item) {
            return response()->json([
                'success' => true,
                'message' => 'Floor already removed or does not exist.',
            ]);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Floor deleted successfully.',
        ]);
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No records selected for deletion.'], 422);
        }

        $count = Floor::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => "{$count} floor(s) deleted successfully.",
        ]);
    }
}
