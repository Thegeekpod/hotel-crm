<?php

namespace App\Http\Controllers\Admin\Utilities;

use App\Http\Controllers\Controller;
use App\Models\RegistrationType;
use Illuminate\Http\Request;

class RegistrationTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = RegistrationType::query()->orderBy('id', 'desc');

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

        return view('admin.utilities.frontoffice.registration_type', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|string|in:Active,Inactive',
        ]);

        $item = RegistrationType::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Registration type created successfully.',
            'data' => $item,
        ]);
    }

    public function update(Request $request, $id)
    {
        $item = RegistrationType::find($id);
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Registration type not found.'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|string|in:Active,Inactive',
        ]);

        $item->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Registration type updated successfully.',
            'data' => $item,
        ]);
    }

    public function destroy($id)
    {
        $item = RegistrationType::find($id);
        if (!$item) {
            return response()->json([
                'success' => true,
                'message' => 'Registration type already removed or does not exist.',
            ]);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Registration type deleted successfully.',
        ]);
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No records selected for deletion.'], 422);
        }

        $count = RegistrationType::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => "{$count} registration type(s) deleted successfully.",
        ]);
    }
}
