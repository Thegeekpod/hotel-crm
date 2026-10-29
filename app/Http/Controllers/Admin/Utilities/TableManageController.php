<?php

namespace App\Http\Controllers\Admin\Utilities;

use App\Http\Controllers\Controller;
use App\Models\TableManage;
use Illuminate\Http\Request;

class TableManageController extends Controller
{
    public function index(Request $request)
    {
        $query = TableManage::query()->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('table_number', 'like', "%{$search}%")
                  ->orWhere('table_name', 'like', "%{$search}%");
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

        return view('admin.utilities.frontoffice.table_manage', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'table_number' => 'required|string|max:50|unique:table_manages,table_number',
            'table_name' => 'required|string|max:255',
            'status' => 'nullable|string|in:available,booked,inactive',
        ]);

        $status = $validated['status'] ?? 'available';

        $item = TableManage::create([
            'table_number' => $validated['table_number'],
            'table_name' => $validated['table_name'],
            'status' => $status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Table created successfully.',
            'data' => $item,
        ]);
    }

    public function update(Request $request, $id)
    {
        $item = TableManage::find($id);
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Table record not found.'], 404);
        }

        $validated = $request->validate([
            'table_number' => 'required|string|max:50|unique:table_manages,table_number,' . $id,
            'table_name' => 'required|string|max:255',
            'status' => 'required|string|in:available,booked,inactive',
        ]);

        $item->update([
            'table_number' => $validated['table_number'],
            'table_name' => $validated['table_name'],
            'status' => $validated['status'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Table updated successfully.',
            'data' => $item,
        ]);
    }

    public function destroy($id)
    {
        $item = TableManage::find($id);
        if (!$item) {
            return response()->json([
                'success' => true,
                'message' => 'Table already removed or does not exist.',
            ]);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Table deleted successfully.',
        ]);
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No tables selected for deletion.'], 422);
        }

        $count = TableManage::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => "Successfully deleted {$count} table(s).",
        ]);
    }
}

