<?php

namespace App\Http\Controllers\Admin\Utilities;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index(Request $request)
    {
        $query = Plan::query()->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('plan_name', 'like', "%{$search}%");
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

        return view('admin.utilities.frontoffice.plans', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plan_name' => 'required|string|max:255|unique:plans,plan_name',
            'status' => 'nullable|string|in:Active,Inactive',
        ]);

        if (empty($validated['status'])) {
            $validated['status'] = 'Active';
        }

        $item = Plan::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Plan created successfully.',
            'data' => $item,
        ]);
    }

    public function update(Request $request, $id)
    {
        $item = Plan::find($id);
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Plan not found.'], 404);
        }

        $validated = $request->validate([
            'plan_name' => 'required|string|max:255|unique:plans,plan_name,' . $id,
            'status' => 'nullable|string|in:Active,Inactive',
        ]);

        if (empty($validated['status'])) {
            $validated['status'] = 'Active';
        }

        $item->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Plan updated successfully.',
            'data' => $item,
        ]);
    }

    public function destroy($id)
    {
        $item = Plan::find($id);
        if (!$item) {
            return response()->json([
                'success' => true,
                'message' => 'Plan already removed or does not exist.',
            ]);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Plan deleted successfully.',
        ]);
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No records selected for deletion.'], 422);
        }

        $count = Plan::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => "{$count} Plan(s) deleted successfully.",
        ]);
    }
}
