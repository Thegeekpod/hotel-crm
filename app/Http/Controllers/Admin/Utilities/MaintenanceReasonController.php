<?php

namespace App\Http\Controllers\Admin\Utilities;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceReason;
use Illuminate\Http\Request;

class MaintenanceReasonController extends Controller
{
    public function index(Request $request)
    {
        $query = MaintenanceReason::query()->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('dept', 'like', "%{$search}%")
                  ->orWhere('priority', 'like', "%{$search}%")
                  ->orWhere('sla', 'like', "%{$search}%");
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

        return view('admin.utilities.roommanage.maintenance_reason', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'dept' => 'required|string|max:100',
            'priority' => 'required|string|in:Low,Medium,High,Immediate',
            'sla' => 'nullable|string|max:100',
            'status' => 'required|string|in:Active,Inactive',
        ]);

        $item = MaintenanceReason::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Maintenance reason created successfully.',
            'data' => $item,
        ]);
    }

    public function update(Request $request, $id)
    {
        $item = MaintenanceReason::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'dept' => 'required|string|max:100',
            'priority' => 'required|string|in:Low,Medium,High,Immediate',
            'sla' => 'nullable|string|max:100',
            'status' => 'required|string|in:Active,Inactive',
        ]);

        $item->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Maintenance reason updated successfully.',
            'data' => $item,
        ]);
    }

    public function destroy($id)
    {
        $item = MaintenanceReason::findOrFail($id);
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Maintenance reason deleted successfully.',
        ]);
    }
}
