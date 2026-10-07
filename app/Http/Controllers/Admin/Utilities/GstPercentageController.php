<?php

namespace App\Http\Controllers\Admin\Utilities;

use App\Http\Controllers\Controller;
use App\Models\GstPercentage;
use Illuminate\Http\Request;

class GstPercentageController extends Controller
{
    public function index(Request $request)
    {
        $query = GstPercentage::query()->orderBy('gst_percentage', 'asc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('gst_percentage', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
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

        return view('admin.utilities.frontoffice.gst', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'gst_percentage' => 'required|numeric|min:0|max:100',
            'status' => 'required|string|in:Active,Inactive',
        ]);

        $item = GstPercentage::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'GST percentage slab created successfully.',
            'data' => $item,
        ]);
    }

    public function update(Request $request, $id)
    {
        $item = GstPercentage::find($id);
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'GST slab not found.'], 404);
        }

        $validated = $request->validate([
            'gst_percentage' => 'required|numeric|min:0|max:100',
            'status' => 'required|string|in:Active,Inactive',
        ]);

        $item->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'GST percentage slab updated successfully.',
            'data' => $item,
        ]);
    }

    public function destroy($id)
    {
        $item = GstPercentage::find($id);
        if (!$item) {
            return response()->json([
                'success' => true,
                'message' => 'GST slab already removed or does not exist.',
            ]);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'GST percentage slab deleted successfully.',
        ]);
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No records selected for deletion.'], 422);
        }

        $count = GstPercentage::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => "{$count} GST percentage slab(s) deleted successfully.",
        ]);
    }
}
