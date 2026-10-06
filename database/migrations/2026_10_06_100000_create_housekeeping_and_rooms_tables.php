<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('housekeeping_states')) {
            Schema::create('housekeeping_states', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->nullable();
                $table->string('badge_color')->default('green');
                $table->string('status')->default('Active');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('operational_statuses')) {
            Schema::create('operational_statuses', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->nullable();
                $table->string('badge_color')->default('blue');
                $table->string('status')->default('Active');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('rooms')) {
            Schema::create('rooms', function (Blueprint $table) {
                $table->id();
                $table->string('room_number')->unique();
                
                // Foreign Keys matching Add Room Modal
                $table->foreignId('floor_id')->nullable()->constrained('floors')->nullOnDelete();
                $table->foreignId('category_id')->nullable()->constrained('room_categories')->nullOnDelete();
                $table->decimal('rate', 10, 2)->default(0.00);
                $table->foreignId('bedding_config_id')->nullable()->constrained('bedding_configs')->nullOnDelete();
                $table->foreignId('pax_capacity_id')->nullable()->constrained('pax_capacities')->nullOnDelete();
                $table->foreignId('operational_status_id')->nullable()->constrained('operational_statuses')->nullOnDelete();
                $table->foreignId('housekeeping_state_id')->nullable()->constrained('housekeeping_states')->nullOnDelete();
                
                // Direct cached descriptors
                $table->string('floor')->nullable();
                $table->string('category')->nullable();
                $table->string('bedding_config')->nullable();
                $table->string('pax_capacity')->nullable();
                $table->string('status')->default('Active');
                $table->string('housekeeping_status')->default('Cleaned');
                
                // Amenities and notes
                $table->json('amenities')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('operational_statuses');
        Schema::dropIfExists('housekeeping_states');
    }
};
