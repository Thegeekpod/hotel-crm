<?php

namespace App\Http\Controllers\Admin\Utilities;

use App\Http\Controllers\Controller;
use App\Models\OperationalStatus;
use Illuminate\Http\Request;

class OperationalStatusController extends Controller
{
    public function index(Request $request)
    {
        $query = OperationalStatus::query()->orderBy('id', 'asc');

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

        return view('admin.utilities.housekeeping.operational_status', compact('items'));
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
            $validated['badge_color'] = 'blue';
        }

        $item = OperationalStatus::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Operational status registered successfully.',
            'data' => $item,
        ]);
    }

    public function update(Request $request, $id)
    {
        $item = OperationalStatus::find($id);
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Operational status not found.'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100',
            'badge_color' => 'nullable|string|max:50',
            'status' => 'required|string|in:Active,Inactive',
        ]);

        if (empty($validated['badge_color'])) {
            $validated['badge_color'] = 'blue';
        }

        $item->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Operational status updated successfully.',
            'data' => $item,
        ]);
    }

    public function destroy($id)
    {
        $item = OperationalStatus::find($id);
        if (!$item) {
            return response()->json([
                'success' => true,
                'message' => 'Operational status already removed or does not exist.',
            ]);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Operational status deleted successfully.',
        ]);
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No records selected for deletion.'], 422);
        }

        $count = OperationalStatus::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => "{$count} operational status(es) deleted successfully.",
        ]);
    }
}
