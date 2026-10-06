<?php

namespace App\Http\Controllers\Admin\Utilities;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceEngineer;
use Illuminate\Http\Request;

class MaintenanceEngineerController extends Controller
{
    public function index(Request $request)
    {
        $query = MaintenanceEngineer::query()->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
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

        return view('admin.utilities.roommanage.engineer', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department' => 'required|string|max:100',
            'phone' => 'nullable|string|max:50',
            'status' => 'required|string|in:Active,Inactive',
        ]);

        $item = MaintenanceEngineer::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Maintenance engineer registered successfully.',
            'data' => $item,
        ]);
    }

    public function update(Request $request, $id)
    {
        $item = MaintenanceEngineer::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department' => 'required|string|max:100',
            'phone' => 'nullable|string|max:50',
            'status' => 'required|string|in:Active,Inactive',
        ]);

        $item->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Maintenance engineer updated successfully.',
            'data' => $item,
        ]);
    }

    public function destroy($id)
    {
        $item = MaintenanceEngineer::findOrFail($id);
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Maintenance engineer removed successfully.',
        ]);
    }
}
