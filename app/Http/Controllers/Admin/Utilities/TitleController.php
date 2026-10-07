<?php

namespace App\Http\Controllers\Admin\Utilities;

use App\Http\Controllers\Controller;
use App\Models\Title;
use Illuminate\Http\Request;

class TitleController extends Controller
{
    public function index(Request $request)
    {
        $query = Title::query()->orderBy('id', 'desc');

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

        return view('admin.utilities.frontoffice.title', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|string|in:Active,Inactive',
        ]);

        $item = Title::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Title created successfully.',
            'data' => $item,
        ]);
    }

    public function update(Request $request, $id)
    {
        $item = Title::find($id);
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Title not found.'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|string|in:Active,Inactive',
        ]);

        $item->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Title updated successfully.',
            'data' => $item,
        ]);
    }

    public function destroy($id)
    {
        $item = Title::find($id);
        if (!$item) {
            return response()->json([
                'success' => true,
                'message' => 'Title already removed or does not exist.',
            ]);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Title deleted successfully.',
        ]);
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No records selected for deletion.'], 422);
        }

        $count = Title::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => "{$count} title(s) deleted successfully.",
        ]);
    }
}
