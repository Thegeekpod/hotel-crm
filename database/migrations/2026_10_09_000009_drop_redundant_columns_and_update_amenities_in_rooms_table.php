<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Room;
use App\Models\Floor;
use App\Models\RoomCategory;
use App\Models\BeddingConfig;
use App\Models\Amenity;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Backfill foreign keys and convert amenities from string names to IDs
        try {
            $amenityMap = Amenity::pluck('id', 'name')->toArray();

            foreach (Room::all() as $room) {
                $updates = [];

                if (!$room->floor_id && !empty($room->floor)) {
                    $fl = Floor::where('floor', $room->floor)->orWhere('name', $room->floor)->first();
                    if ($fl) $updates['floor_id'] = $fl->id;
                }

                if (!$room->category_id && !empty($room->category)) {
                    $cat = RoomCategory::where('name', $room->category)->first();
                    if ($cat) $updates['category_id'] = $cat->id;
                }

                if (!$room->bedding_config_id && !empty($room->bedding_config)) {
                    $bed = BeddingConfig::where('name', $room->bedding_config)->first();
                    if ($bed) $updates['bedding_config_id'] = $bed->id;
                }

                // Convert amenities array of strings to array of integer IDs
                if (is_array($room->amenities) && !empty($room->amenities)) {
                    $newAmenityIds = [];
                    foreach ($room->amenities as $amn) {
                        if (is_numeric($amn)) {
                            $newAmenityIds[] = (int)$amn;
                        } elseif (isset($amenityMap[$amn])) {
                            $newAmenityIds[] = (int)$amenityMap[$amn];
                        } else {
                            $found = Amenity::where('name', 'like', '%' . $amn . '%')->first();
                            if ($found) {
                                $newAmenityIds[] = (int)$found->id;
                            }
                        }
                    }
                    $updates['amenities'] = array_values(array_unique($newAmenityIds));
                }

                if (!empty($updates)) {
                    $room->update($updates);
                }
            }
        } catch (\Throwable $e) {
            // continue migration even if table is empty
        }

        // 2. Drop redundant legacy columns: floor, category, bedding_config
        Schema::table('rooms', function (Blueprint $table) {
            $dropCols = [];
            if (Schema::hasColumn('rooms', 'floor')) {
                $dropCols[] = 'floor';
            }
            if (Schema::hasColumn('rooms', 'category')) {
                $dropCols[] = 'category';
            }
            if (Schema::hasColumn('rooms', 'bedding_config')) {
                $dropCols[] = 'bedding_config';
            }
            if (!empty($dropCols)) {
                $table->dropColumn($dropCols);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            if (!Schema::hasColumn('rooms', 'floor')) {
                $table->string('floor')->nullable();
            }
            if (!Schema::hasColumn('rooms', 'category')) {
                $table->string('category')->nullable();
            }
            if (!Schema::hasColumn('rooms', 'bedding_config')) {
                $table->string('bedding_config')->nullable();
            }
        });
    }
};
