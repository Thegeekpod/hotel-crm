<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HotelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HotelServiceController extends Controller
{
    /**
     * Display a listing of hotel services.
     */
    public function index(Request $request)
    {
        $query = HotelService::query();

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhere('category', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $items = $query->orderBy('category')->orderBy('name')->get();

        $stats = [
            'total' => HotelService::count(),
            'active' => HotelService::where('status', 'Active')->count(),
            'inactive' => HotelService::where('status', 'Inactive')->count(),
            'categories' => HotelService::distinct('category')->whereNotNull('category')->count('category'),
        ];

        $allCategories = HotelService::distinct()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->pluck('category')
            ->toArray();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'items' => $items,
                'stats' => $stats,
            ]);
        }

        return view('admin.services.index', compact('items', 'stats', 'allCategories'));
    }

    /**
     * Store a newly created hotel service.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:Active,Inactive',
        ]);

        if (empty($validated['code'])) {
            $lastId = (int) (HotelService::max('id') ?? 0) + 1;
            $validated['code'] = 'SRV-' . str_pad($lastId, 4, '0', STR_PAD_LEFT);
        }

        if (empty($validated['unit'])) {
            $validated['unit'] = 'Per Order';
        }

        $service = HotelService::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Hotel service created successfully.',
                'service' => $service,
            ]);
        }

        return redirect()->route('admin.services.index')->with('success', 'Hotel service created successfully.');
    }

    /**
     * Display the specified hotel service details (JSON).
     */
    public function show($id)
    {
        $service = HotelService::findOrFail($id);

        return response()->json([
            'success' => true,
            'service' => $service,
        ]);
    }

    /**
     * Update the specified hotel service in storage.
     */
    public function update(Request $request, $id)
    {
        $service = HotelService::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:Active,Inactive',
        ]);

        if (empty($validated['unit'])) {
            $validated['unit'] = $service->unit ?: 'Per Order';
        }

        $service->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Hotel service updated successfully.',
                'service' => $service,
            ]);
        }

        return redirect()->route('admin.services.index')->with('success', 'Hotel service updated successfully.');
    }

    /**
     * Remove the specified hotel service from storage.
     */
    public function destroy(Request $request, $id)
    {
        $service = HotelService::findOrFail($id);
        $service->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Hotel service deleted successfully.',
            ]);
        }

        return redirect()->route('admin.services.index')->with('success', 'Hotel service deleted successfully.');
    }

    /**
     * Bulk delete hotel services.
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:hotel_services,id',
        ]);

        $count = HotelService::whereIn('id', $validated['ids'])->delete();

        return response()->json([
            'success' => true,
            'message' => "Successfully deleted {$count} service(s).",
        ]);
    }

    /**
     * Quick status toggle (Active / Inactive).
     */
    public function toggleStatus(Request $request, $id)
    {
        $service = HotelService::findOrFail($id);
        $service->status = ($service->status === 'Active') ? 'Inactive' : 'Active';
        $service->save();

        return response()->json([
            'success' => true,
            'status' => $service->status,
            'message' => "Service status switched to {$service->status}.",
        ]);
    }
}
