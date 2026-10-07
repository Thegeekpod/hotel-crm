<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReservationMode;
use App\Models\RegistrationType;
use App\Models\Title;
use App\Models\Nationality;
use App\Models\IdCardType;
use App\Models\PaymentMode;
use App\Models\RoomCategory;
use App\Models\Floor;
use App\Models\BeddingConfig;
use App\Models\Amenity;
use App\Models\HousekeepingState;
use App\Models\OperationalStatus;

class MasterUtilitiesSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Registration Types
        $registrationTypes = [
            ['name' => 'Regular Check-in', 'status' => 'Active'],
            ['name' => 'Corporate / Business', 'status' => 'Active'],
            ['name' => 'VIP / High Priority', 'status' => 'Active'],
            ['name' => 'Walk-In Guest', 'status' => 'Active'],
            ['name' => 'Complimentary Stay', 'status' => 'Active'],
        ];
        foreach ($registrationTypes as $item) {
            RegistrationType::firstOrCreate(['name' => $item['name']], $item);
        }

        // 1. Titles / Salutations
        $titles = [
            ['name' => 'Mr.', 'status' => 'Active'],
            ['name' => 'Mrs.', 'status' => 'Active'],
            ['name' => 'Ms.', 'status' => 'Active'],
            ['name' => 'Dr.', 'status' => 'Active'],
            ['name' => 'Prof.', 'status' => 'Active'],
            ['name' => 'Hon.', 'status' => 'Active'],
        ];
        foreach ($titles as $item) {
            Title::firstOrCreate(['name' => $item['name']], $item);
        }

        // 2. Nationalities
        $nationalities = [
            ['name' => 'Indian', 'status' => 'Active'],
            ['name' => 'American', 'status' => 'Active'],
            ['name' => 'British', 'status' => 'Active'],
            ['name' => 'Canadian', 'status' => 'Active'],
            ['name' => 'Australian', 'status' => 'Active'],
            ['name' => 'German', 'status' => 'Active'],
            ['name' => 'French', 'status' => 'Active'],
            ['name' => 'Japanese', 'status' => 'Active'],
            ['name' => 'Emirati (UAE)', 'status' => 'Active'],
        ];
        foreach ($nationalities as $item) {
            Nationality::firstOrCreate(['name' => $item['name']], $item);
        }

        // 3. Reservation Modes
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

        // 4. Bedding Configurations (with pax capacity)
        $beddingConfigs = [
            ['name' => 'King Size Master (72x78)', 'code' => 'KING-7278', 'dimensions' => '72 x 78 inches', 'max_adults' => 2, 'max_children' => 1, 'max_total' => 3, 'status' => 'Active'],
            ['name' => 'Queen Size Double (60x78)', 'code' => 'QUEEN-6078', 'dimensions' => '60 x 78 inches', 'max_adults' => 2, 'max_children' => 0, 'max_total' => 2, 'status' => 'Active'],
            ['name' => 'Twin Single Beds (36x78 x 2)', 'code' => 'TWIN-3678', 'dimensions' => '36 x 78 inches (2 Beds)', 'max_adults' => 2, 'max_children' => 0, 'max_total' => 2, 'status' => 'Active'],
            ['name' => 'Single Bed (36x78)', 'code' => 'SINGLE-3678', 'dimensions' => '36 x 78 inches', 'max_adults' => 1, 'max_children' => 0, 'max_total' => 1, 'status' => 'Active'],
            ['name' => 'Suite Triple / Extra Bed', 'code' => 'TRIPLE-SUITE', 'dimensions' => '72x78 + Extra Bed', 'max_adults' => 3, 'max_children' => 1, 'max_total' => 4, 'status' => 'Active'],
            ['name' => 'Family Suite 4 Pax', 'code' => 'FAMILY-4', 'dimensions' => 'Two King Beds', 'max_adults' => 4, 'max_children' => 2, 'max_total' => 6, 'status' => 'Active'],
        ];
        foreach ($beddingConfigs as $item) {
            BeddingConfig::updateOrCreate(['name' => $item['name']], $item);
        }

        // 5. Room Categories (only category name & status)
        $categories = [
            ['name' => 'Deluxe Room', 'status' => 'Active'],
            ['name' => 'Super Deluxe', 'status' => 'Active'],
            ['name' => 'Suite Luxury', 'status' => 'Active'],
            ['name' => 'Executive Penthouse', 'status' => 'Active'],
        ];
        foreach ($categories as $item) {
            RoomCategory::updateOrCreate(['name' => $item['name']], $item);
        }

        // 6. Floors & Wings
        $floors = [
            ['floor' => '1', 'name' => 'Floor 1 (Ground / Wing A)', 'rooms' => 0, 'status' => 'Active'],
            ['floor' => '2', 'name' => 'Floor 2 (Sea Wing)', 'rooms' => 0, 'status' => 'Active'],
            ['floor' => '3', 'name' => 'Floor 3 (Club Wing)', 'rooms' => 0, 'status' => 'Active'],
            ['floor' => '4', 'name' => 'Floor 4 (Royal Penthouse)', 'rooms' => 0, 'status' => 'Active'],
        ];
        foreach ($floors as $item) {
            Floor::firstOrCreate(['floor' => $item['floor']], $item);
        }

        // 7. Amenities Master
        $amenities = [
            ['icon' => 'fa-solid fa-wifi', 'name' => 'Free Wi-Fi', 'status' => 'Active'],
            ['icon' => 'fa-solid fa-water', 'name' => 'Ocean View', 'status' => 'Active'],
            ['icon' => 'fa-solid fa-door-open', 'name' => 'Balcony', 'status' => 'Active'],
            ['icon' => 'fa-solid fa-bath', 'name' => 'Jacuzzi Bath', 'status' => 'Active'],
            ['icon' => 'fa-solid fa-wine-bottle', 'name' => 'Mini-Bar', 'status' => 'Active'],
            ['icon' => 'fa-solid fa-tv', 'name' => 'Smart 55" TV', 'status' => 'Active'],
            ['icon' => 'fa-solid fa-faucet-drip', 'name' => 'Hot Water & Cold Water', 'status' => 'Active'],
            ['icon' => 'fa-solid fa-snowflake', 'name' => 'Air Conditioning (AC)', 'status' => 'Active'],
            ['icon' => 'fa-solid fa-vault', 'name' => 'Safe Locker', 'status' => 'Active'],
            ['icon' => 'fa-solid fa-mug-hot', 'name' => 'Coffee / Tea Maker', 'status' => 'Active'],
        ];
        foreach ($amenities as $item) {
            Amenity::firstOrCreate(['name' => $item['name']], $item);
        }

        // 8. Housekeeping States
        $housekeepingStates = [
            ['name' => 'Cleaned & Inspected', 'code' => 'CLEANED', 'badge_color' => 'green', 'status' => 'Active'],
            ['name' => 'Dirty / Cleaning Due', 'code' => 'DIRTY', 'badge_color' => 'red', 'status' => 'Active'],
            ['name' => 'Inspecting / Touch-up', 'code' => 'INSPECTING', 'badge_color' => 'yellow', 'status' => 'Active'],
            ['name' => 'Touch-up Required', 'code' => 'TOUCHUP', 'badge_color' => 'purple', 'status' => 'Active'],
        ];
        foreach ($housekeepingStates as $item) {
            HousekeepingState::firstOrCreate(['name' => $item['name']], $item);
        }

        // 9. Operational Statuses
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
