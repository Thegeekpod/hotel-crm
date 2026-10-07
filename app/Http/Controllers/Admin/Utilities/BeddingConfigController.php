<?php

namespace App\Http\Controllers\Admin\Utilities;

use App\Http\Controllers\Controller;
use App\Models\BeddingConfig;
use Illuminate\Http\Request;

class BeddingConfigController extends Controller
{
    public function index(Request $request)
    {
        $query = BeddingConfig::query()->orderBy('id', 'desc');

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

        return view('admin.utilities.roommanage.bedding_config', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'max_adults' => 'required|integer|min:1',
            'max_children' => 'nullable|integer|min:0',
            'max_total' => 'nullable|integer|min:1',
            'status' => 'required|string|in:Active,Inactive',
        ]);

        $maxAdults = (int) $validated['max_adults'];
        $maxChildren = (int) ($validated['max_children'] ?? 0);
        $maxTotal = $maxAdults + $maxChildren;

        $item = BeddingConfig::create([
            'name' => $validated['name'],
            'max_adults' => $maxAdults,
            'max_children' => $maxChildren,
            'max_total' => $maxTotal,
            'status' => $validated['status'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bedding configuration created successfully.',
            'data' => $item,
        ]);
    }

    public function update(Request $request, $id)
    {
        $item = BeddingConfig::find($id);
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Bedding configuration not found.',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'max_adults' => 'required|integer|min:1',
            'max_children' => 'nullable|integer|min:0',
            'max_total' => 'nullable|integer|min:1',
            'status' => 'required|string|in:Active,Inactive',
        ]);

        $maxAdults = (int) $validated['max_adults'];
        $maxChildren = (int) ($validated['max_children'] ?? 0);
        $maxTotal = $maxAdults + $maxChildren;

        $item->update([
            'name' => $validated['name'],
            'max_adults' => $maxAdults,
            'max_children' => $maxChildren,
            'max_total' => $maxTotal,
            'status' => $validated['status'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bedding configuration updated successfully.',
            'data' => $item,
        ]);
    }

    public function destroy($id)
    {
        $item = BeddingConfig::find($id);
        if (!$item) {
            return response()->json([
                'success' => true,
                'message' => 'Bedding configuration already removed or does not exist.',
            ]);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bedding configuration deleted successfully.',
        ]);
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No records selected for deletion.'], 422);
        }

        $count = BeddingConfig::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => "{$count} bedding configuration(s) deleted successfully.",
        ]);
    }
}
