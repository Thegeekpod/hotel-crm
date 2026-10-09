<?php

namespace App\Http\Controllers\Admin\Utilities;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function index(Request $request)
    {
        $query = Discount::query()->orderBy('discount_percentage', 'asc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('discount_percentage', 'like', "%{$search}%")
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

        return view('admin.utilities.frontoffice.discount', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'discount_percentage' => 'required|numeric|min:0|max:100|unique:discounts,discount_percentage',
            'status' => 'required|string|in:Active,Inactive',
        ], [
            'discount_percentage.unique' => 'This discount percentage already exists. Duplicates are not allowed.',
            'discount_percentage.required' => 'Discount percentage is required.',
            'discount_percentage.numeric' => 'Discount percentage must be a valid number.',
        ]);

        $item = Discount::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Discount percentage slab created successfully.',
            'data' => $item,
        ]);
    }

    public function update(Request $request, $id)
    {
        $item = Discount::find($id);
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Discount slab not found.'], 404);
        }

        $validated = $request->validate([
            'discount_percentage' => 'required|numeric|min:0|max:100|unique:discounts,discount_percentage,' . $id,
            'status' => 'required|string|in:Active,Inactive',
        ], [
            'discount_percentage.unique' => 'This discount percentage already exists. Duplicates are not allowed.',
            'discount_percentage.required' => 'Discount percentage is required.',
            'discount_percentage.numeric' => 'Discount percentage must be a valid number.',
        ]);

        $item->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Discount percentage slab updated successfully.',
            'data' => $item,
        ]);
    }

    public function destroy($id)
    {
        $item = Discount::find($id);
        if (!$item) {
            return response()->json([
                'success' => true,
                'message' => 'Discount slab already removed or does not exist.',
            ]);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Discount percentage slab deleted successfully.',
        ]);
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No records selected for deletion.'], 422);
        }

        $count = Discount::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => "{$count} Discount percentage slab(s) deleted successfully.",
        ]);
    }
}
