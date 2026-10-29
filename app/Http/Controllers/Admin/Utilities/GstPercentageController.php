<?php

namespace App\Http\Controllers\Admin\Utilities;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
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

        $defaultGstSettings = [
            'default_gst_room' => (float) AppSetting::get('default_gst_room', 12.00),
            'default_gst_pos' => (float) AppSetting::get('default_gst_pos', 12.00),
            'default_gst_extra_services' => (float) AppSetting::get('default_gst_extra_services', 5.00),
            'default_gst_banquet' => (float) AppSetting::get('default_gst_banquet', 5.00),
        ];

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $items,
                'count' => $items->count(),
                'default_gst_settings' => $defaultGstSettings,
            ]);
        }

        return view('admin.utilities.frontoffice.gst', compact('items', 'defaultGstSettings'));
    }

    public function updateDefaultSettings(Request $request)
    {
        $validated = $request->validate([
            'default_gst_room' => 'nullable|numeric|min:0|max:100',
            'default_gst_pos' => 'nullable|numeric|min:0|max:100',
            'default_gst_extra_services' => 'nullable|numeric|min:0|max:100',
            'default_gst_banquet' => 'nullable|numeric|min:0|max:100',
        ]);

        foreach ($validated as $key => $value) {
            if ($value !== null) {
                AppSetting::set($key, number_format((float)$value, 2, '.', ''));
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Default GST rates updated successfully.',
            'data' => [
                'default_gst_room' => (float) AppSetting::get('default_gst_room', 12.00),
                'default_gst_pos' => (float) AppSetting::get('default_gst_pos', 12.00),
                'default_gst_extra_services' => (float) AppSetting::get('default_gst_extra_services', 5.00),
                'default_gst_banquet' => (float) AppSetting::get('default_gst_banquet', 5.00),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'gst_percentage' => 'required|numeric|min:0|max:100|unique:gst_percentages,gst_percentage',
            'status' => 'required|string|in:Active,Inactive',
        ], [
            'gst_percentage.unique' => 'This GST percentage rate already exists. Duplicates are not allowed.',
            'gst_percentage.required' => 'GST percentage is required.',
            'gst_percentage.numeric' => 'GST percentage must be a valid number.',
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
            'gst_percentage' => 'required|numeric|min:0|max:100|unique:gst_percentages,gst_percentage,' . $id,
            'status' => 'required|string|in:Active,Inactive',
        ], [
            'gst_percentage.unique' => 'This GST percentage rate already exists. Duplicates are not allowed.',
            'gst_percentage.required' => 'GST percentage is required.',
            'gst_percentage.numeric' => 'GST percentage must be a valid number.',
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
