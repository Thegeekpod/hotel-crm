<?php

namespace App\Http\Controllers\Admin\Utilities;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use Illuminate\Http\Request;

class AmenityController extends Controller
{
    public function index(Request $request)
    {
        $query = Amenity::query()->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('icon', 'like', "%{$search}%");
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

        return view('admin.utilities.roommanage.amenity', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'icon' => 'required|string|max:100',
            'name' => 'required|string|max:255',
            'status' => 'required|string|in:Active,Inactive',
        ]);

        $item = Amenity::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Amenity created successfully.',
            'data' => $item,
        ]);
    }

    public function update(Request $request, $id)
    {
        $item = Amenity::find($id);
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Amenity not found or already removed.',
            ], 404);
        }

        $validated = $request->validate([
            'icon' => 'required|string|max:100',
            'name' => 'required|string|max:255',
            'status' => 'required|string|in:Active,Inactive',
        ]);

        $item->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Amenity updated successfully.',
            'data' => $item,
        ]);
    }

    public function destroy($id)
    {
        $item = Amenity::find($id);
        if (!$item) {
            return response()->json([
                'success' => true,
                'message' => 'Amenity already removed or does not exist.',
            ]);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Amenity deleted successfully.',
        ]);
    }
}
