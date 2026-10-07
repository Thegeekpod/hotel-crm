<?php

namespace App\Http\Controllers\Admin\Utilities;

use App\Http\Controllers\Controller;
use App\Models\RoomCategory;
use Illuminate\Http\Request;

class RoomCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = RoomCategory::query()->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
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

        return view('admin.utilities.roommanage.category', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|string|in:Active,Inactive',
        ]);

        $item = RoomCategory::create([
            'name' => $validated['name'],
            'status' => $validated['status'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Room category created successfully.',
            'data' => $item,
        ]);
    }

    public function update(Request $request, $id)
    {
        $item = RoomCategory::find($id);
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Room category not found.',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|string|in:Active,Inactive',
        ]);

        $item->update([
            'name' => $validated['name'],
            'status' => $validated['status'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Room category updated successfully.',
            'data' => $item,
        ]);
    }

    public function destroy($id)
    {
        $item = RoomCategory::find($id);
        if (!$item) {
            return response()->json([
                'success' => true,
                'message' => 'Room category already removed or does not exist.',
            ]);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Room category deleted successfully.',
        ]);
    }
}
