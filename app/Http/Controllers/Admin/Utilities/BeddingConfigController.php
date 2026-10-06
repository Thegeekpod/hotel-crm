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
            'status' => 'required|string|in:Active,Inactive',
        ]);

        $item = BeddingConfig::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Bedding configuration created successfully.',
            'data' => $item,
        ]);
    }

    public function update(Request $request, $id)
    {
        $item = BeddingConfig::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|string|in:Active,Inactive',
        ]);

        $item->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Bedding configuration updated successfully.',
            'data' => $item,
        ]);
    }

    public function destroy($id)
    {
        $item = BeddingConfig::findOrFail($id);
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bedding configuration deleted successfully.',
        ]);
    }
}
