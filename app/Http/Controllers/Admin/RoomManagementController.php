<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Floor;
use App\Models\RoomCategory;
use App\Models\BeddingConfig;
use App\Models\Amenity;
use App\Models\HousekeepingState;
use App\Models\OperationalStatus;
use App\Models\RoomMaintenance;
use Illuminate\Http\Request;

class RoomManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::with(['floorRelation', 'categoryRelation', 'beddingConfigRelation', 'latestMaintenance'])
            ->orderBy('room_number', 'asc');

        // Filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('room_number', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhereHas('floorRelation', function ($fq) use ($search) {
                      $fq->where('floor', 'like', "%{$search}%")->orWhere('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('categoryRelation', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('beddingConfigRelation', function ($bq) use ($search) {
                      $bq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('category') && $request->input('category') !== 'ALL') {
            $catVal = $request->input('category');
            $query->where(function ($q) use ($catVal) {
                $q->where('category_id', $catVal)
                  ->orWhereHas('categoryRelation', fn ($cq) => $cq->where('name', $catVal));
            });
        }

        if ($request->filled('floor') && $request->input('floor') !== 'all') {
            $flVal = $request->input('floor');
            $query->where(function ($q) use ($flVal) {
                $q->where('floor_id', $flVal)
                  ->orWhereHas('floorRelation', fn ($fq) => $fq->where('floor', $flVal)->orWhere('name', $flVal));
            });
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('amenity') && $request->input('amenity') !== 'all') {
            $amenityVal = $request->input('amenity');
            $amnObj = is_numeric($amenityVal) ? Amenity::find($amenityVal) : Amenity::where('name', $amenityVal)->first();
            $amnId = $amnObj?->id ?? (is_numeric($amenityVal) ? (int)$amenityVal : null);

            if ($amnId) {
                $query->where(function ($q) use ($amnId, $amenityVal) {
                    $q->whereJsonContains('amenities', $amnId)
                      ->orWhereJsonContains('amenities', (string)$amnId)
                      ->orWhereJsonContains('amenities', $amenityVal);
                });
            }
        }

        $rooms = $query->get();

        // Masters for select2 & checkboxes
        $floors = Floor::where('status', 'Active')->orderBy('floor', 'asc')->get();
        $categories = RoomCategory::where('status', 'Active')->get();
        $beddingConfigs = BeddingConfig::where('status', 'Active')->get();
        $housekeepingStates = HousekeepingState::where('status', 'Active')->get();
        $operationalStatuses = OperationalStatus::where('status', 'Active')->get();
        $amenities = Amenity::where('status', 'Active')->get();

        // Dynamic stats computed on filtered rooms
        $totalRooms = $rooms->count();
        $activeRooms = $rooms->where('status', 'Active')->count();
        $inactiveRooms = $rooms->where('status', 'Inactive')->count();

        // Category wise room counts
        $categoryWise = [];
        foreach ($categories as $cat) {
            $categoryWise[$cat->name] = 0;
        }
        foreach ($rooms as $r) {
            $cName = $r->categoryRelation?->name ?? $r->category;
            if (!empty($cName)) {
                $categoryWise[$cName] = ($categoryWise[$cName] ?? 0) + 1;
            }
        }
        $categoriesCount = count($categoryWise);

        // Floor wise room counts
        $floorWise = [];
        foreach ($floors as $fl) {
            $floorWise[$fl->floor] = 0;
        }
        foreach ($rooms as $r) {
            $fNum = $r->floorRelation?->floor ?? $r->floor;
            if ($fNum !== null && $fNum !== '') {
                $floorWise[$fNum] = ($floorWise[$fNum] ?? 0) + 1;
            }
        }
        $floorsCount = count($floorWise);

        // Amenities wise room counts
        $amenityWise = [];
        foreach ($amenities as $amn) {
            $amenityWise[$amn->name] = 0;
        }
        foreach ($rooms as $r) {
            if (is_array($r->amenities)) {
                foreach ($r->amenities as $amnItem) {
                    if (is_numeric($amnItem)) {
                        $matchedAmn = $amenities->firstWhere('id', (int)$amnItem);
                        if ($matchedAmn) {
                            $amenityWise[$matchedAmn->name] = ($amenityWise[$matchedAmn->name] ?? 0) + 1;
                        }
                    } else {
                        $amenityWise[$amnItem] = ($amenityWise[$amnItem] ?? 0) + 1;
                    }
                }
            }
        }
        $amenitiesCount = count($amenityWise);

        $stats = [
            'total' => $totalRooms,
            'active' => $activeRooms,
            'inactive' => $inactiveRooms,
            'categories_count' => $categoriesCount,
            'category_wise' => $categoryWise,
            'floors_count' => $floorsCount,
            'floor_wise' => $floorWise,
            'amenities_count' => $amenitiesCount,
            'amenity_wise' => $amenityWise,
        ];

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $rooms,
                'count' => $totalRooms,
                'stats' => $stats,
            ]);
        }

        return view('admin.utilities.roommanage.room_add', compact(
            'rooms',
            'floors',
            'categories',
            'beddingConfigs',
            'housekeepingStates',
            'operationalStatuses',
            'amenities',
            'totalRooms',
            'activeRooms',
            'inactiveRooms',
            'categoriesCount',
            'categoryWise',
            'floorsCount',
            'floorWise',
            'amenitiesCount',
            'amenityWise',
            'stats'
        ));
    }

    public function show($id)
    {
        $room = Room::with(['floorRelation', 'categoryRelation', 'beddingConfigRelation'])->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $room,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_number' => 'required|string|max:50|unique:rooms,room_number',
            'floor' => 'nullable|string|max:100',
            'floor_id' => 'nullable|exists:floors,id',
            'category' => 'nullable|string|max:100',
            'category_id' => 'nullable|exists:room_categories,id',
            'rate' => 'required|numeric|min:0',
            'bedding_config' => 'nullable|string|max:255',
            'bedding_config_id' => 'nullable|exists:bedding_configs,id',
            'amenities' => 'nullable|array',
            'status' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $status = $validated['status'] ?? 'Active';

        $fl = !empty($validated['floor_id']) 
            ? Floor::find($validated['floor_id']) 
            : Floor::where('floor', $validated['floor'] ?? '')->orWhere('name', $validated['floor'] ?? '')->first();

        $cat = !empty($validated['category_id']) 
            ? RoomCategory::find($validated['category_id']) 
            : RoomCategory::where('name', $validated['category'] ?? '')->first();

        $bed = !empty($validated['bedding_config_id']) 
            ? BeddingConfig::find($validated['bedding_config_id']) 
            : BeddingConfig::where('name', $validated['bedding_config'] ?? '')->first();

        // Convert amenities to integer IDs
        $amenityIds = [];
        if (!empty($validated['amenities']) && is_array($validated['amenities'])) {
            foreach ($validated['amenities'] as $amn) {
                if (is_numeric($amn)) {
                    $amenityIds[] = (int)$amn;
                } else {
                    $found = Amenity::where('name', $amn)->first();
                    if ($found) $amenityIds[] = (int)$found->id;
                }
            }
        }

        $room = Room::create([
            'room_number' => $validated['room_number'],
            'floor_id' => $fl ? $fl->id : null,
            'category_id' => $cat ? $cat->id : null,
            'bedding_config_id' => $bed ? $bed->id : null,
            'rate' => $validated['rate'],
            'status' => $status,
            'amenities' => array_values(array_unique($amenityIds)),
            'notes' => $validated['notes'] ?? null,
        ]);

        // Auto update floor count
        if ($fl) {
            $fl->rooms = Room::where('floor_id', $fl->id)->count();
            $fl->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Room #' . $room->room_number . ' registered successfully.',
            'data' => $room->load(['floorRelation', 'categoryRelation', 'beddingConfigRelation']),
        ]);
    }

    public function update(Request $request, $id)
    {
        $room = Room::findOrFail($id);
        $oldFloorId = $room->floor_id;

        $validated = $request->validate([
            'room_number' => 'required|string|max:50|unique:rooms,room_number,' . $id,
            'floor' => 'nullable|string|max:100',
            'floor_id' => 'nullable|exists:floors,id',
            'category' => 'nullable|string|max:100',
            'category_id' => 'nullable|exists:room_categories,id',
            'rate' => 'required|numeric|min:0',
            'bedding_config' => 'nullable|string|max:255',
            'bedding_config_id' => 'nullable|exists:bedding_configs,id',
            'amenities' => 'nullable|array',
            'status' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $status = $validated['status'] ?? $room->status ?? 'Active';

        $fl = !empty($validated['floor_id']) 
            ? Floor::find($validated['floor_id']) 
            : Floor::where('floor', $validated['floor'] ?? '')->orWhere('name', $validated['floor'] ?? '')->first();

        $cat = !empty($validated['category_id']) 
            ? RoomCategory::find($validated['category_id']) 
            : RoomCategory::where('name', $validated['category'] ?? '')->first();

        $bed = !empty($validated['bedding_config_id']) 
            ? BeddingConfig::find($validated['bedding_config_id']) 
            : BeddingConfig::where('name', $validated['bedding_config'] ?? '')->first();

        // Convert amenities to integer IDs
        $amenityIds = [];
        if (!empty($validated['amenities']) && is_array($validated['amenities'])) {
            foreach ($validated['amenities'] as $amn) {
                if (is_numeric($amn)) {
                    $amenityIds[] = (int)$amn;
                } else {
                    $found = Amenity::where('name', $amn)->first();
                    if ($found) $amenityIds[] = (int)$found->id;
                }
            }
        }

        $room->update([
            'room_number' => $validated['room_number'],
            'floor_id' => $fl ? $fl->id : null,
            'category_id' => $cat ? $cat->id : null,
            'bedding_config_id' => $bed ? $bed->id : null,
            'rate' => $validated['rate'],
            'status' => $status,
            'amenities' => array_values(array_unique($amenityIds)),
            'notes' => $validated['notes'] ?? null,
        ]);

        // Sync floor count for both old and new floor
        if ($oldFloorId && $oldFloorId != $room->floor_id) {
            $oldF = Floor::find($oldFloorId);
            if ($oldF) {
                $oldF->rooms = Room::where('floor_id', $oldF->id)->count();
                $oldF->save();
            }
        }
        if ($fl) {
            $fl->rooms = Room::where('floor_id', $fl->id)->count();
            $fl->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Room #' . $room->room_number . ' updated successfully.',
            'data' => $room->load(['floorRelation', 'categoryRelation', 'beddingConfigRelation']),
        ]);
    }

    public function destroy($id)
    {
        $room = Room::findOrFail($id);
        $number = $room->room_number;
        $floorId = $room->floor_id;
        $room->delete();

        if ($floorId) {
            $fl = Floor::find($floorId);
            if ($fl) {
                $fl->rooms = Room::where('floor_id', $fl->id)->count();
                $fl->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Room #' . $number . ' removed successfully.',
        ]);
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No rooms selected for deletion.'], 422);
        }

        $count = Room::whereIn('id', $ids)->delete();

        // Recalculate room counts across all floors
        $floors = Floor::all();
        foreach ($floors as $fl) {
            $fl->rooms = Room::where('floor_id', $fl->id)->count();
            $fl->save();
        }

        return response()->json([
            'success' => true,
            'message' => "{$count} room asset(s) deleted successfully.",
        ]);
    }

    public function toggleMaintenance(Request $request, $id)
    {
        $room = Room::findOrFail($id);
        if ($room->status === 'Maintenance' || $room->status === 'Under Maintenance') {
            $room->update([
                'status' => 'Active',
            ]);

            // Update active maintenance data status to Completed (preserve data, do NOT delete)
            RoomMaintenance::where('room_id', $room->id)
                ->where('status', '!=', 'Completed')
                ->update(['status' => 'Completed']);

            $msg = 'Room #' . $room->room_number . ' marked as Active.';
        } else {
            $room->update([
                'status' => 'Under Maintenance',
                'notes' => $request->input('note') ?? $room->notes,
            ]);

            // Create new RoomMaintenance entry with status Active
            RoomMaintenance::create([
                'room_id' => $room->id,
                'reason' => $request->input('reason'),
                'assign' => $request->input('assign') ?? $request->input('engineer'),
                'expected_date_time' => $request->input('expected_date_time') ?? $request->input('completion'),
                'note' => $request->input('note'),
                'status' => $request->input('status', 'Active'), // Active, Checking, Completed
            ]);

            $msg = 'Room #' . $room->room_number . ' marked as Under Maintenance.';
        }

        $room->load('latestMaintenance');

        return response()->json([
            'success' => true,
            'message' => $msg,
            'data' => $room,
        ]);
    }
}
