<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReservationMode;
use App\Models\IdCardType;
use App\Models\PaymentMode;
use App\Models\RoomCategory;
use App\Models\Floor;
use App\Models\BeddingConfig;
use App\Models\PaxCapacity;
use App\Models\Amenity;
use App\Models\MaintenanceReason;
use App\Models\HousekeepingState;
use App\Models\OperationalStatus;

class MasterUtilitiesSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Reservation Modes
        $reservationModes = [
            ['name' => 'Phone Call', 'status' => 'Active'],
            ['name' => 'Physical', 'status' => 'Active'],
            ['name' => 'Website', 'status' => 'Active'],
            ['name' => 'Online', 'status' => 'Active'],
        ];
        foreach ($reservationModes as $item) {
            ReservationMode::firstOrCreate(['name' => $item['name']], $item);
        }

        // 2. ID Card Types
        $idCardTypes = [
            ['name' => 'Aadhaar Card (UIDAI)', 'code' => 'AADHAAR', 'status' => 'Active'],
            ['name' => 'Passport (International)', 'code' => 'PASSPORT', 'status' => 'Active'],
            ['name' => 'Driving License', 'code' => 'DL', 'status' => 'Active'],
            ['name' => 'Voter ID Card', 'code' => 'VOTER', 'status' => 'Active'],
            ['name' => 'PAN Card (Income Tax)', 'code' => 'PAN', 'status' => 'Active'],
        ];
        foreach ($idCardTypes as $item) {
            IdCardType::firstOrCreate(['name' => $item['name']], $item);
        }

        // 3. Payment Modes
        $paymentModes = [
            ['name' => 'QR Scanner', 'status' => 'Active'],
            ['name' => 'Cash', 'status' => 'Active'],
            ['name' => 'Phone pay', 'status' => 'Active'],
            ['name' => 'G pay', 'status' => 'Active'],
            ['name' => 'Paytm', 'status' => 'Active'],
            ['name' => 'Bank Transfer', 'status' => 'Active'],
            ['name' => 'Check Payment', 'status' => 'Active'],
        ];
        foreach ($paymentModes as $item) {
            PaymentMode::firstOrCreate(['name' => $item['name']], $item);
        }

        // 4. Bedding Configurations
        $beddingConfigs = [
            ['name' => 'King Size Master (72x78)', 'code' => 'KING-7278', 'dimensions' => '72 x 78 inches', 'status' => 'Active'],
            ['name' => 'Queen Size Double (60x78)', 'code' => 'QUEEN-6078', 'dimensions' => '60 x 78 inches', 'status' => 'Active'],
            ['name' => 'Twin Single Beds (36x78 x 2)', 'code' => 'TWIN-3678', 'dimensions' => '36 x 78 inches (2 Beds)', 'status' => 'Active'],
            ['name' => 'Single Bed (36x78)', 'code' => 'SINGLE-3678', 'dimensions' => '36 x 78 inches', 'status' => 'Active'],
            ['name' => 'Suite Triple / Extra Bed', 'code' => 'TRIPLE-SUITE', 'dimensions' => '72x78 + Extra Bed', 'status' => 'Active'],
        ];
        foreach ($beddingConfigs as $item) {
            BeddingConfig::firstOrCreate(['name' => $item['name']], $item);
        }

        // 5. Pax Capacities
        $paxCapacities = [
            ['name' => '2 Adults', 'max_adults' => 2, 'max_children' => 0, 'max_total' => 2, 'status' => 'Active'],
            ['name' => '2 Adults + 1 Child', 'max_adults' => 2, 'max_children' => 1, 'max_total' => 3, 'status' => 'Active'],
            ['name' => '1 Adult (Single)', 'max_adults' => 1, 'max_children' => 0, 'max_total' => 1, 'status' => 'Active'],
            ['name' => '3 Adults', 'max_adults' => 3, 'max_children' => 0, 'max_total' => 3, 'status' => 'Active'],
            ['name' => '4 Adults / Family', 'max_adults' => 4, 'max_children' => 2, 'max_total' => 6, 'status' => 'Active'],
        ];
        foreach ($paxCapacities as $item) {
            PaxCapacity::firstOrCreate(['name' => $item['name']], $item);
        }

        // 6. Room Categories
        $categories = [
            ['name' => 'Deluxe Room', 'code' => 'DELUXE', 'bedding_config' => 'King Size Master (72x78)', 'pax_capacity' => '2 Adults', 'status' => 'Active'],
            ['name' => 'Super Deluxe', 'code' => 'SUPER DELUXE', 'bedding_config' => 'King Size Master (72x78)', 'pax_capacity' => '2 Adults + 1 Child', 'status' => 'Active'],
            ['name' => 'Suite Luxury', 'code' => 'SUITE', 'bedding_config' => 'Suite Triple / Extra Bed', 'pax_capacity' => '3 Adults', 'status' => 'Active'],
            ['name' => 'Executive Penthouse', 'code' => 'EXECUTIVE', 'bedding_config' => 'King Size Master (72x78)', 'pax_capacity' => '4 Adults / Family', 'status' => 'Active'],
        ];
        foreach ($categories as $item) {
            RoomCategory::updateOrCreate(['name' => $item['name']], $item);
        }

        // 7. Floors & Wings
        $floors = [
            ['floor' => '1', 'name' => 'Floor 1 (Ground / Wing A)', 'rooms' => '12 Rooms (101 - 112)', 'status' => 'Active'],
            ['floor' => '2', 'name' => 'Floor 2 (Sea Wing)', 'rooms' => '12 Rooms (201 - 212)', 'status' => 'Active'],
            ['floor' => '3', 'name' => 'Floor 3 (Club Wing)', 'rooms' => '12 Rooms (301 - 312)', 'status' => 'Active'],
            ['floor' => '4', 'name' => 'Floor 4 (Royal Penthouse)', 'rooms' => '12 Rooms (401 - 412)', 'status' => 'Active'],
        ];
        foreach ($floors as $item) {
            Floor::firstOrCreate(['name' => $item['name']], $item);
        }

        // 8. Amenities Master
        $amenities = [
            ['icon' => 'fa-wifi', 'name' => 'Free Wi-Fi', 'status' => 'Active'],
            ['icon' => 'fa-water', 'name' => 'Ocean View', 'status' => 'Active'],
            ['icon' => 'fa-door-open', 'name' => 'Balcony', 'status' => 'Active'],
            ['icon' => 'fa-bath', 'name' => 'Jacuzzi Bath', 'status' => 'Active'],
            ['icon' => 'fa-wine-bottle', 'name' => 'Mini-Bar', 'status' => 'Active'],
            ['icon' => 'fa-tv', 'name' => 'Smart 55" TV', 'status' => 'Active'],
            ['icon' => 'fa-faucet-drip', 'name' => 'Hot Water & Cold Water', 'status' => 'Active'],
            ['icon' => 'fa-snowflake', 'name' => 'AC', 'status' => 'Active'],
            ['icon' => 'fa-vault', 'name' => 'Safe Locker', 'status' => 'Active'],
            ['icon' => 'fa-mug-hot', 'name' => 'Coffee / Tea Maker', 'status' => 'Active'],
        ];
        foreach ($amenities as $item) {
            Amenity::firstOrCreate(['name' => $item['name']], $item);
        }

        // 9. Maintenance Reasons
        $maintenanceReasons = [
            ['name' => 'HVAC Air Conditioning & Compressor Service', 'dept' => 'Engineering', 'priority' => 'High', 'sla' => '4 Hours', 'status' => 'Active'],
            ['name' => 'Bathroom Plumbing & Water Pressure', 'dept' => 'Facilities', 'priority' => 'High', 'sla' => '2 Hours', 'status' => 'Active'],
            ['name' => 'Deep Steam Cleaning & Sanitization', 'dept' => 'Housekeeping', 'priority' => 'Medium', 'sla' => '3 Hours', 'status' => 'Active'],
            ['name' => 'Interior Wall Painting & Touch-up', 'dept' => 'Civil / Maintenance', 'priority' => 'Low', 'sla' => '24 Hours', 'status' => 'Active'],
            ['name' => 'RFID Smart Lock & Reader Repair', 'dept' => 'IT / Security', 'priority' => 'Immediate', 'sla' => '1 Hour', 'status' => 'Active'],
        ];
        foreach ($maintenanceReasons as $item) {
            MaintenanceReason::firstOrCreate(['name' => $item['name']], $item);
        }

        // 10. Housekeeping States
        $housekeepingStates = [
            ['name' => 'Cleaned & Inspected', 'code' => 'CLEANED', 'badge_color' => 'green', 'status' => 'Active'],
            ['name' => 'Dirty / Cleaning Due', 'code' => 'DIRTY', 'badge_color' => 'red', 'status' => 'Active'],
            ['name' => 'Inspecting / Touch-up', 'code' => 'INSPECTING', 'badge_color' => 'yellow', 'status' => 'Active'],
            ['name' => 'Touch-up Required', 'code' => 'TOUCHUP', 'badge_color' => 'purple', 'status' => 'Active'],
        ];
        foreach ($housekeepingStates as $item) {
            HousekeepingState::firstOrCreate(['name' => $item['name']], $item);
        }

        // 11. Operational Statuses
        $operationalStatuses = [
            ['name' => 'Active In-Service', 'code' => 'ACTIVE', 'badge_color' => 'green', 'status' => 'Active'],
            ['name' => 'Under Maintenance', 'code' => 'MAINTENANCE', 'badge_color' => 'yellow', 'status' => 'Active'],
            ['name' => 'Out of Order / Blocked', 'code' => 'BLOCKED', 'badge_color' => 'red', 'status' => 'Active'],
            ['name' => 'Management Reserved', 'code' => 'RESERVED', 'badge_color' => 'blue', 'status' => 'Active'],
        ];
        foreach ($operationalStatuses as $item) {
            OperationalStatus::firstOrCreate(['name' => $item['name']], $item);
        }
    }
}
