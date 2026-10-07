<?php

namespace App\Http\Controllers\Admin\Utilities;

use App\Http\Controllers\Controller;
use App\Models\IdCardType;
use Illuminate\Http\Request;

class IdCardTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = IdCardType::query()->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
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

        return view('admin.utilities.frontoffice.idcard_type', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100',
            'status' => 'required|string|in:Active,Inactive',
        ]);

        $item = IdCardType::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'ID Card type created successfully.',
            'data' => $item,
        ]);
    }

    public function update(Request $request, $id)
    {
        $item = IdCardType::find($id);
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'ID Card type not found.'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100',
            'status' => 'required|string|in:Active,Inactive',
        ]);

        $item->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'ID Card type updated successfully.',
            'data' => $item,
        ]);
    }

    public function destroy($id)
    {
        $item = IdCardType::find($id);
        if (!$item) {
            return response()->json([
                'success' => true,
                'message' => 'ID Card type already removed or does not exist.',
            ]);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'ID Card type deleted successfully.',
        ]);
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No records selected for deletion.'], 422);
        }

        $count = IdCardType::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => "{$count} ID Card type(s) deleted successfully.",
        ]);
    }
}
