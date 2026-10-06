<?php

namespace App\Http\Controllers\Admin\Utilities;

use App\Http\Controllers\Controller;
use App\Models\HousekeepingState;
use Illuminate\Http\Request;

class HousekeepingStateController extends Controller
{
    public function index(Request $request)
    {
        $query = HousekeepingState::query()->orderBy('id', 'asc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('badge_color', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        $items = $query->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $items,
                'count' => $items->count(),
            ]);
        }

        return view('admin.utilities.housekeeping.housekeeping_state', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100',
            'badge_color' => 'nullable|string|max:50',
            'status' => 'required|string|in:Active,Inactive',
        ]);

        if (empty($validated['badge_color'])) {
            $validated['badge_color'] = 'green';
        }

        $item = HousekeepingState::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Housekeeping state registered successfully.',
            'data' => $item,
        ]);
    }

    public function update(Request $request, $id)
    {
        $item = HousekeepingState::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100',
            'badge_color' => 'nullable|string|max:50',
            'status' => 'required|string|in:Active,Inactive',
        ]);

        if (empty($validated['badge_color'])) {
            $validated['badge_color'] = 'green';
        }

        $item->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Housekeeping state updated successfully.',
            'data' => $item,
        ]);
    }

    public function destroy($id)
    {
        $item = HousekeepingState::findOrFail($id);
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Housekeeping state deleted successfully.',
        ]);
    }
}
