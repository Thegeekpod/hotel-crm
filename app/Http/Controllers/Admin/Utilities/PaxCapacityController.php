<?php

namespace App\Http\Controllers\Admin\Utilities;

use App\Http\Controllers\Controller;
use App\Models\PaxCapacity;
use Illuminate\Http\Request;

class PaxCapacityController extends Controller
{
    public function index(Request $request)
    {
        $query = PaxCapacity::query()->orderBy('id', 'desc');

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

        return view('admin.utilities.roommanage.pax_capacity', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'max_adults' => 'required|integer|min:1|max:20',
            'max_children' => 'nullable|integer|min:0|max:20',
            'max_total' => 'required|integer|min:1|max:30',
            'status' => 'required|string|in:Active,Inactive',
        ]);

        $item = PaxCapacity::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pax capacity created successfully.',
            'data' => $item,
        ]);
    }

    public function update(Request $request, $id)
    {
        $item = PaxCapacity::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'max_adults' => 'required|integer|min:1|max:20',
            'max_children' => 'nullable|integer|min:0|max:20',
            'max_total' => 'required|integer|min:1|max:30',
            'status' => 'required|string|in:Active,Inactive',
        ]);

        $item->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pax capacity updated successfully.',
            'data' => $item,
        ]);
    }

    public function destroy($id)
    {
        $item = PaxCapacity::findOrFail($id);
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pax capacity deleted successfully.',
        ]);
    }
}
