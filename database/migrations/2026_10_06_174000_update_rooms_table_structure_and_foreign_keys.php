<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Room;
use App\Models\Floor;
use App\Models\RoomCategory;
use App\Models\BeddingConfig;
use App\Models\PaxCapacity;
use App\Models\OperationalStatus;
use App\Models\HousekeepingState;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            if (!Schema::hasColumn('rooms', 'floor_id')) {
                $table->foreignId('floor_id')->nullable()->constrained('floors')->nullOnDelete();
            }
            if (!Schema::hasColumn('rooms', 'category_id')) {
                $table->foreignId('category_id')->nullable()->constrained('room_categories')->nullOnDelete();
            }
            if (!Schema::hasColumn('rooms', 'bedding_config_id')) {
                $table->foreignId('bedding_config_id')->nullable()->constrained('bedding_configs')->nullOnDelete();
            }
            if (!Schema::hasColumn('rooms', 'pax_capacity_id')) {
                $table->foreignId('pax_capacity_id')->nullable()->constrained('pax_capacities')->nullOnDelete();
            }
            if (!Schema::hasColumn('rooms', 'operational_status_id')) {
                $table->foreignId('operational_status_id')->nullable()->constrained('operational_statuses')->nullOnDelete();
            }
            if (!Schema::hasColumn('rooms', 'housekeeping_state_id')) {
                $table->foreignId('housekeeping_state_id')->nullable()->constrained('housekeeping_states')->nullOnDelete();
            }

            // Drop non-modal fields
            $dropCols = [
                'maintenance_reason',
                'assigned_engineer',
                'expected_completion',
                'guest_name',
                'guest_phone',
                'guest_id_number',
                'booking_id',
                'checkin_date',
                'checkout_date'
            ];

            foreach ($dropCols as $col) {
                if (Schema::hasColumn('rooms', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        // Backfill IDs from descriptors
        try {
            foreach (Room::all() as $room) {
                $fl = Floor::where('floor', $room->floor)->orWhere('name', 'like', '%' . $room->floor . '%')->first();
                $cat = RoomCategory::where('name', $room->category)->orWhere('code', $room->category)->first();
                $bed = BeddingConfig::where('name', $room->bedding_config)->first();
                $pax = PaxCapacity::where('name', $room->pax_capacity)->first();
                $ops = OperationalStatus::where('name', $room->status)->first();
                $hks = HousekeepingState::where('name', $room->housekeeping_status)->first();

                $room->update([
                    'floor_id' => $fl ? $fl->id : null,
                    'category_id' => $cat ? $cat->id : null,
                    'bedding_config_id' => $bed ? $bed->id : null,
                    'pax_capacity_id' => $pax ? $pax->id : null,
                    'operational_status_id' => $ops ? $ops->id : null,
                    'housekeeping_state_id' => $hks ? $hks->id : null,
                ]);
            }
        } catch (\Throwable $e) {
            // ignore backfill error if any
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign(['floor_id']);
            $table->dropForeign(['category_id']);
            $table->dropForeign(['bedding_config_id']);
            $table->dropForeign(['pax_capacity_id']);
            $table->dropForeign(['operational_status_id']);
            $table->dropForeign(['housekeeping_state_id']);

            $table->dropColumn([
                'floor_id',
                'category_id',
                'bedding_config_id',
                'pax_capacity_id',
                'operational_status_id',
                'housekeeping_state_id'
            ]);
        });
    }
};
