<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\RegistrationType;
use App\Models\Title;
use App\Models\Nationality;
use App\Models\ReservationMode;
use App\Models\IdCardType;
use App\Models\PaymentMode;
use App\Models\Discount;
use App\Models\GstPercentage;
use App\Models\Company;
use App\Models\BeddingConfig;
use App\Models\RoomCategory;
use App\Models\Floor;
use App\Models\Amenity;
use App\Models\HousekeepingState;
use App\Models\OperationalStatus;
use App\Models\Room;
use App\Models\RoomMaintenance;
use App\Models\RoomHousekeepingHistory;
use App\Models\RoomOperationalHistory;
use App\Models\Guest;

class MasterUtilitiesSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Default Administrator & Staff Users
        $users = [
            [
                'name' => 'Debashis Roy',
                'email' => 'admin@hotelsagarsonnet.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Front Desk Executive',
                'email' => 'frontdesk@hotelsagarsonnet.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Housekeeping Supervisor',
                'email' => 'housekeeping@hotelsagarsonnet.com',
                'password' => Hash::make('password'),
            ],
        ];
        foreach ($users as $u) {
            User::firstOrCreate(['email' => $u['email']], $u);
        }

        
        // 3. Titles / Salutations
        $titles = [
            ['name' => 'Mr.', 'status' => 'Active'],
            ['name' => 'Mrs.', 'status' => 'Active'],
            ['name' => 'Ms.', 'status' => 'Active'],
            ['name' => 'Dr.', 'status' => 'Active'],
            ['name' => 'Prof.', 'status' => 'Active'],
            ['name' => 'Hon.', 'status' => 'Active'],
            ['name' => 'Capt.', 'status' => 'Active'],
            ['name' => 'Shri', 'status' => 'Active'],
            ['name' => 'Smt', 'status' => 'Active'],
        ];
        foreach ($titles as $item) {
            Title::firstOrCreate(['name' => $item['name']], $item);
        }

        // 4. Nationalities
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
            ['name' => 'Singaporean', 'status' => 'Active'],
            ['name' => 'Bangladeshi', 'status' => 'Active'],
            ['name' => 'Nepalese', 'status' => 'Active'],
            ['name' => 'Swiss', 'status' => 'Active'],
            ['name' => 'Italian', 'status' => 'Active'],
        ];
        foreach ($nationalities as $item) {
            Nationality::firstOrCreate(['name' => $item['name']], $item);
        }

        // 5. Reservation Modes
        $reservationModes = [
            ['name' => 'Phone Call', 'status' => 'Active'],
            ['name' => 'Physical / Walk-in', 'status' => 'Active'],
            ['name' => 'Hotel Website', 'status' => 'Active'],
            ['name' => 'OTA / Online Portal (MakeMyTrip / Goibibo)', 'status' => 'Active'],
            ['name' => 'Booking.com / Agoda', 'status' => 'Active'],
            ['name' => 'Travel Agent / Desk', 'status' => 'Active'],
            ['name' => 'Corporate Direct Desk', 'status' => 'Active'],
            ['name' => 'Email Enquiry', 'status' => 'Active'],
        ];
        foreach ($reservationModes as $item) {
            ReservationMode::firstOrCreate(['name' => $item['name']], $item);
        }

        // 6. ID Card Types
        $idCardTypes = [
            ['name' => 'Aadhaar Card (UIDAI)', 'status' => 'Active'],
            ['name' => 'Passport (International)', 'status' => 'Active'],
            ['name' => 'Driving License', 'status' => 'Active'],
            ['name' => 'Voter ID Card (Election Commission)', 'status' => 'Active'],
            ['name' => 'PAN Card (Income Tax)', 'status' => 'Active'],
            ['name' => 'OCI / PIO Card', 'status' => 'Active'],
            ['name' => 'Government Employee ID', 'status' => 'Active'],
        ];
        foreach ($idCardTypes as $item) {
            IdCardType::firstOrCreate(['name' => $item['name']], $item);
        }

        // 7. Payment Modes
        $paymentModes = [
            ['name' => 'UPI / QR Scanner', 'status' => 'Active'],
            ['name' => 'Cash', 'status' => 'Active'],
            ['name' => 'Credit / Debit Card (POS Terminal)', 'status' => 'Active'],
            ['name' => 'PhonePe', 'status' => 'Active'],
            ['name' => 'Google Pay', 'status' => 'Active'],
            ['name' => 'Paytm', 'status' => 'Active'],
            ['name' => 'Net Banking / NEFT / RTGS', 'status' => 'Active'],
            ['name' => 'Cheque / Bank Draft', 'status' => 'Active'],
            ['name' => 'Bill to Company (BTC Credit)', 'status' => 'Active'],
        ];
        foreach ($paymentModes as $item) {
            PaymentMode::firstOrCreate(['name' => $item['name']], $item);
        }

        // 8. Discounts Master
        $discounts = [
            ['discount_percentage' => 0.00, 'status' => 'Active'],
            ['discount_percentage' => 5.00, 'status' => 'Active'],
            ['discount_percentage' => 10.00, 'status' => 'Active'],
            ['discount_percentage' => 15.00, 'status' => 'Active'],
            ['discount_percentage' => 20.00, 'status' => 'Active'],
            ['discount_percentage' => 25.00, 'status' => 'Active'],
            ['discount_percentage' => 30.00, 'status' => 'Active'],
            ['discount_percentage' => 50.00, 'status' => 'Active'],
        ];
        foreach ($discounts as $item) {
            Discount::firstOrCreate(['discount_percentage' => $item['discount_percentage']], $item);
        }

        // 9. GST % Slabs
        $gstSlabs = [
            ['gst_percentage' => 0.00, 'status' => 'Active'],
            ['gst_percentage' => 5.00, 'status' => 'Active'],
            ['gst_percentage' => 12.00, 'status' => 'Active'],
            ['gst_percentage' => 18.00, 'status' => 'Active'],
            ['gst_percentage' => 28.00, 'status' => 'Active'],
        ];
        foreach ($gstSlabs as $item) {
            GstPercentage::firstOrCreate(['gst_percentage' => $item['gst_percentage']], $item);
        }

        // 10. Corporate Companies
        $companies = [
            [
                'name' => 'Tata Consultancy Services Ltd.',
                'address' => 'TCS House, Raveline Street, Fort, Mumbai - 400001',
                'gstin' => '27AAACT2727Q1ZW',
                'phone' => '+91 22 6778 9999',
                'status' => 'Active',
            ],
            [
                'name' => 'Infosys Technologies Pvt. Ltd.',
                'address' => 'Electronics City, Hosur Road, Bengaluru - 560100',
                'gstin' => '29AAACI4567M1ZX',
                'phone' => '+91 80 2852 0261',
                'status' => 'Active',
            ],
            [
                'name' => 'Reliance Industries Corporate',
                'address' => 'Maker Chambers IV, 222 Nariman Point, Mumbai - 400021',
                'gstin' => '27AAACR1234A1ZP',
                'phone' => '+91 22 3555 5000',
                'status' => 'Active',
            ],
            [
                'name' => 'Wipro Technologies India',
                'address' => 'Doddakannelli, Sarjapur Road, Bengaluru - 560035',
                'gstin' => '29AAACW0123L1ZK',
                'phone' => '+91 80 2844 0011',
                'status' => 'Active',
            ],
            [
                'name' => 'HDFC Bank Corporate Banking',
                'address' => 'HDFC Bank House, Senapati Bapat Marg, Lower Parel, Mumbai - 400013',
                'gstin' => '27AAACH2702H1ZX',
                'phone' => '+91 22 6652 1000',
                'status' => 'Active',
            ],
            [
                'name' => 'Larsen & Toubro Limited',
                'address' => 'L&T House, Ballard Estate, Mumbai - 400001',
                'gstin' => '27AAACL0149P1ZA',
                'phone' => '+91 22 6752 5656',
                'status' => 'Active',
            ],
        ];
        foreach ($companies as $item) {
            Company::firstOrCreate(['name' => $item['name']], $item);
        }

        // 11. Bedding Configurations
        $beddingConfigs = [
            ['name' => 'King Size Master (72x78)', 'max_adults' => 2, 'max_children' => 1, 'max_total' => 3, 'status' => 'Active'],
            ['name' => 'Queen Size Double (60x78)', 'max_adults' => 2, 'max_children' => 0, 'max_total' => 2, 'status' => 'Active'],
            ['name' => 'Twin Single Beds (36x78 x 2)', 'max_adults' => 2, 'max_children' => 0, 'max_total' => 2, 'status' => 'Active'],
            ['name' => 'Single Bed (36x78)', 'max_adults' => 1, 'max_children' => 0, 'max_total' => 1, 'status' => 'Active'],
            ['name' => 'Suite Triple / Extra Bed', 'max_adults' => 3, 'max_children' => 1, 'max_total' => 4, 'status' => 'Active'],
            ['name' => 'Family Suite 4 Pax', 'max_adults' => 4, 'max_children' => 2, 'max_total' => 6, 'status' => 'Active'],
        ];
        $beddingModels = [];
        foreach ($beddingConfigs as $item) {
            $beddingModels[$item['name']] = BeddingConfig::updateOrCreate(['name' => $item['name']], $item);
        }

        // 12. Room Categories
        $categories = [
            ['name' => 'Deluxe', 'status' => 'Active'],
            ['name' => 'Super Deluxe', 'status' => 'Active'],
            ['name' => 'Suite', 'status' => 'Active'],
            ['name' => 'Executive', 'status' => 'Active'],
        ];
        $categoryModels = [];
        foreach ($categories as $item) {
            $categoryModels[$item['name']] = RoomCategory::updateOrCreate(['name' => $item['name']], $item);
        }

        // 13. Floors
        $floors = [
            ['floor' => '1', 'name' => 'Floor 1 (Ground / Wing A)', 'rooms' => 10, 'status' => 'Active'],
            ['floor' => '2', 'name' => 'Floor 2 (Sea Wing)', 'rooms' => 10, 'status' => 'Active'],
            ['floor' => '3', 'name' => 'Floor 3 (Club Wing)', 'rooms' => 10, 'status' => 'Active'],
            ['floor' => '4', 'name' => 'Floor 4 (Executive Wing)', 'rooms' => 10, 'status' => 'Active'],
            ['floor' => '5', 'name' => 'Floor 5 (Royal Penthouse)', 'rooms' => 10, 'status' => 'Active'],
        ];
        $floorModels = [];
        foreach ($floors as $item) {
            $floorModels[$item['floor']] = Floor::updateOrCreate(['floor' => $item['floor']], $item);
        }

        // Clean up legacy/orphan rooms if any
        $validFloorIds = array_filter(array_map(fn($f) => $f?->id, $floorModels));
        Room::whereNotIn('floor_id', $validFloorIds)
            ->orWhereIn('room_number', ['1', '100'])
            ->delete();

        // 14. Amenities Master
        $amenities = [
            ['icon' => 'fa-solid fa-wifi', 'name' => 'Free Wi-Fi', 'status' => 'Active'],
            ['icon' => 'fa-solid fa-water', 'name' => 'Ocean View', 'status' => 'Active'],
            ['icon' => 'fa-solid fa-door-open', 'name' => 'Private Balcony', 'status' => 'Active'],
            ['icon' => 'fa-solid fa-bath', 'name' => 'Jacuzzi Bath', 'status' => 'Active'],
            ['icon' => 'fa-solid fa-wine-bottle', 'name' => 'Mini-Bar & Snacks', 'status' => 'Active'],
            ['icon' => 'fa-solid fa-tv', 'name' => 'Smart 55" 4K TV', 'status' => 'Active'],
            ['icon' => 'fa-solid fa-faucet-drip', 'name' => '24h Hot & Cold Water', 'status' => 'Active'],
            ['icon' => 'fa-solid fa-snowflake', 'name' => 'Dual Climate AC', 'status' => 'Active'],
            ['icon' => 'fa-solid fa-vault', 'name' => 'Electronic Safe Locker', 'status' => 'Active'],
            ['icon' => 'fa-solid fa-mug-hot', 'name' => 'Electric Kettle & Tea Kit', 'status' => 'Active'],
            ['icon' => 'fa-solid fa-wind', 'name' => 'Hair Dryer', 'status' => 'Active'],
            ['icon' => 'fa-solid fa-bell-concierge', 'name' => '24/7 Room Service', 'status' => 'Active'],
        ];
        $amenityModels = [];
        foreach ($amenities as $item) {
            $amenityModels[$item['name']] = Amenity::firstOrCreate(['name' => $item['name']], $item);
        }

        $defaultAmenityNames = ['Free Wi-Fi', 'Smart 55" 4K TV', 'Dual Climate AC', '24h Hot & Cold Water'];
        $defaultAmenityIds = [];
        foreach ($defaultAmenityNames as $aname) {
            if (isset($amenityModels[$aname])) {
                $defaultAmenityIds[] = (int)$amenityModels[$aname]->id;
            }
        }

        // 15. Housekeeping States
        $housekeepingStates = [
            ['name' => 'Cleaned & Inspected', 'badge_color' => 'green', 'status' => 'Active'],
            ['name' => 'Dirty / Cleaning Due', 'badge_color' => 'red', 'status' => 'Active'],
            ['name' => 'Inspecting / Touch-up', 'badge_color' => 'yellow', 'status' => 'Active'],
            ['name' => 'Touch-up Required', 'badge_color' => 'purple', 'status' => 'Active'],
            ['name' => 'Sanitized & Ready', 'badge_color' => 'green', 'status' => 'Active'],
        ];
        $hkStateModels = [];
        foreach ($housekeepingStates as $item) {
            $hkStateModels[$item['name']] = HousekeepingState::firstOrCreate(['name' => $item['name']], $item);
        }

        // 16. Operational Statuses
        $operationalStatuses = [
            ['name' => 'Active In-Service', 'badge_color' => 'green', 'status' => 'Active'],
            ['name' => 'Under Maintenance', 'badge_color' => 'yellow', 'status' => 'Active'],
            ['name' => 'Out of Order / Blocked', 'badge_color' => 'red', 'status' => 'Active'],
            ['name' => 'Management Reserved', 'badge_color' => 'blue', 'status' => 'Active'],
        ];
        $opStatusModels = [];
        foreach ($operationalStatuses as $item) {
            $opStatusModels[$item['name']] = OperationalStatus::firstOrCreate(['name' => $item['name']], $item);
        }

        // 17. Seed 50 Rooms (5 Floors x 10 Rooms)
        $roomsData = [
            // Floor 1 (101 - 110)
            ['room_number' => '101', 'floor' => '1', 'category' => 'Super Deluxe', 'bedding' => 'King Size Master (72x78)', 'rate' => 5200.00],
            ['room_number' => '102', 'floor' => '1', 'category' => 'Deluxe', 'bedding' => 'Queen Size Double (60x78)', 'rate' => 3500.00],
            ['room_number' => '103', 'floor' => '1', 'category' => 'Super Deluxe', 'bedding' => 'Twin Single Beds (36x78 x 2)', 'rate' => 5200.00],
            ['room_number' => '104', 'floor' => '1', 'category' => 'Super Deluxe', 'bedding' => 'King Size Master (72x78)', 'rate' => 5200.00],
            ['room_number' => '105', 'floor' => '1', 'category' => 'Deluxe', 'bedding' => 'Queen Size Double (60x78)', 'rate' => 3500.00],
            ['room_number' => '106', 'floor' => '1', 'category' => 'Deluxe', 'bedding' => 'Twin Single Beds (36x78 x 2)', 'rate' => 3500.00],
            ['room_number' => '107', 'floor' => '1', 'category' => 'Super Deluxe', 'bedding' => 'King Size Master (72x78)', 'rate' => 5200.00],
            ['room_number' => '108', 'floor' => '1', 'category' => 'Super Deluxe', 'bedding' => 'King Size Master (72x78)', 'rate' => 5200.00],
            ['room_number' => '109', 'floor' => '1', 'category' => 'Deluxe', 'bedding' => 'Queen Size Double (60x78)', 'rate' => 3500.00],
            ['room_number' => '110', 'floor' => '1', 'category' => 'Suite', 'bedding' => 'Family Suite 4 Pax', 'rate' => 8500.00],

            // Floor 2 (201 - 210)
            ['room_number' => '201', 'floor' => '2', 'category' => 'Suite', 'bedding' => 'Family Suite 4 Pax', 'rate' => 8500.00],
            ['room_number' => '202', 'floor' => '2', 'category' => 'Deluxe', 'bedding' => 'Queen Size Double (60x78)', 'rate' => 3500.00],
            ['room_number' => '203', 'floor' => '2', 'category' => 'Super Deluxe', 'bedding' => 'King Size Master (72x78)', 'rate' => 5200.00],
            ['room_number' => '204', 'floor' => '2', 'category' => 'Super Deluxe', 'bedding' => 'Twin Single Beds (36x78 x 2)', 'rate' => 5200.00],
            ['room_number' => '205', 'floor' => '2', 'category' => 'Super Deluxe', 'bedding' => 'King Size Master (72x78)', 'rate' => 5200.00],
            ['room_number' => '206', 'floor' => '2', 'category' => 'Deluxe', 'bedding' => 'Queen Size Double (60x78)', 'rate' => 3500.00],
            ['room_number' => '207', 'floor' => '2', 'category' => 'Super Deluxe', 'bedding' => 'King Size Master (72x78)', 'rate' => 5200.00],
            ['room_number' => '208', 'floor' => '2', 'category' => 'Super Deluxe', 'bedding' => 'King Size Master (72x78)', 'rate' => 5200.00],
            ['room_number' => '209', 'floor' => '2', 'category' => 'Super Deluxe', 'bedding' => 'Twin Single Beds (36x78 x 2)', 'rate' => 5200.00],
            ['room_number' => '210', 'floor' => '2', 'category' => 'Suite', 'bedding' => 'Suite Triple / Extra Bed', 'rate' => 8500.00],

            // Floor 3 (301 - 310)
            ['room_number' => '301', 'floor' => '3', 'category' => 'Suite', 'bedding' => 'Suite Triple / Extra Bed', 'rate' => 8500.00],
            ['room_number' => '302', 'floor' => '3', 'category' => 'Deluxe', 'bedding' => 'Queen Size Double (60x78)', 'rate' => 3500.00],
            ['room_number' => '303', 'floor' => '3', 'category' => 'Super Deluxe', 'bedding' => 'King Size Master (72x78)', 'rate' => 5200.00],
            ['room_number' => '304', 'floor' => '3', 'category' => 'Super Deluxe', 'bedding' => 'King Size Master (72x78)', 'rate' => 5200.00],
            ['room_number' => '305', 'floor' => '3', 'category' => 'Super Deluxe', 'bedding' => 'King Size Master (72x78)', 'rate' => 5200.00],
            ['room_number' => '306', 'floor' => '3', 'category' => 'Deluxe', 'bedding' => 'Twin Single Beds (36x78 x 2)', 'rate' => 3500.00],
            ['room_number' => '307', 'floor' => '3', 'category' => 'Super Deluxe', 'bedding' => 'King Size Master (72x78)', 'rate' => 5200.00],
            ['room_number' => '308', 'floor' => '3', 'category' => 'Super Deluxe', 'bedding' => 'King Size Master (72x78)', 'rate' => 5200.00],
            ['room_number' => '309', 'floor' => '3', 'category' => 'Deluxe', 'bedding' => 'Single Bed (36x78)', 'rate' => 3500.00],
            ['room_number' => '310', 'floor' => '3', 'category' => 'Suite', 'bedding' => 'Family Suite 4 Pax', 'rate' => 8500.00],

            // Floor 4 (401 - 410)
            ['room_number' => '401', 'floor' => '4', 'category' => 'Executive', 'bedding' => 'King Size Master (72x78)', 'rate' => 11000.00],
            ['room_number' => '402', 'floor' => '4', 'category' => 'Executive', 'bedding' => 'King Size Master (72x78)', 'rate' => 11000.00],
            ['room_number' => '403', 'floor' => '4', 'category' => 'Super Deluxe', 'bedding' => 'King Size Master (72x78)', 'rate' => 5200.00],
            ['room_number' => '404', 'floor' => '4', 'category' => 'Super Deluxe', 'bedding' => 'King Size Master (72x78)', 'rate' => 5200.00],
            ['room_number' => '405', 'floor' => '4', 'category' => 'Deluxe', 'bedding' => 'Queen Size Double (60x78)', 'rate' => 3500.00],
            ['room_number' => '406', 'floor' => '4', 'category' => 'Deluxe', 'bedding' => 'Queen Size Double (60x78)', 'rate' => 3500.00],
            ['room_number' => '407', 'floor' => '4', 'category' => 'Super Deluxe', 'bedding' => 'Twin Single Beds (36x78 x 2)', 'rate' => 5200.00],
            ['room_number' => '408', 'floor' => '4', 'category' => 'Suite', 'bedding' => 'Suite Triple / Extra Bed', 'rate' => 8500.00],
            ['room_number' => '409', 'floor' => '4', 'category' => 'Executive', 'bedding' => 'King Size Master (72x78)', 'rate' => 11000.00],
            ['room_number' => '410', 'floor' => '4', 'category' => 'Suite', 'bedding' => 'Family Suite 4 Pax', 'rate' => 8500.00],

            // Floor 5 (501 - 510)
            ['room_number' => '501', 'floor' => '5', 'category' => 'Executive', 'bedding' => 'King Size Master (72x78)', 'rate' => 11000.00],
            ['room_number' => '502', 'floor' => '5', 'category' => 'Executive', 'bedding' => 'King Size Master (72x78)', 'rate' => 11000.00],
            ['room_number' => '503', 'floor' => '5', 'category' => 'Suite', 'bedding' => 'Suite Triple / Extra Bed', 'rate' => 8500.00],
            ['room_number' => '504', 'floor' => '5', 'category' => 'Suite', 'bedding' => 'Family Suite 4 Pax', 'rate' => 8500.00],
            ['room_number' => '505', 'floor' => '5', 'category' => 'Super Deluxe', 'bedding' => 'King Size Master (72x78)', 'rate' => 5200.00],
            ['room_number' => '506', 'floor' => '5', 'category' => 'Super Deluxe', 'bedding' => 'King Size Master (72x78)', 'rate' => 5200.00],
            ['room_number' => '507', 'floor' => '5', 'category' => 'Super Deluxe', 'bedding' => 'Twin Single Beds (36x78 x 2)', 'rate' => 5200.00],
            ['room_number' => '508', 'floor' => '5', 'category' => 'Executive', 'bedding' => 'King Size Master (72x78)', 'rate' => 11000.00],
            ['room_number' => '509', 'floor' => '5', 'category' => 'Executive', 'bedding' => 'King Size Master (72x78)', 'rate' => 11000.00],
            ['room_number' => '510', 'floor' => '5', 'category' => 'Suite', 'bedding' => 'Family Suite 4 Pax', 'rate' => 8500.00],
        ];

        $createdRooms = [];
        foreach ($roomsData as $r) {
            $floorModel = $floorModels[$r['floor']] ?? null;
            $catModel = $categoryModels[$r['category']] ?? null;
            $bedModel = $beddingModels[$r['bedding']] ?? null;

            $room = Room::updateOrCreate(
                ['room_number' => $r['room_number']],
                [
                    'floor_id' => $floorModel?->id,
                    'category_id' => $catModel?->id,
                    'bedding_config_id' => $bedModel?->id,
                    'rate' => $r['rate'],
                    'status' => 'Active',
                    'amenities' => $defaultAmenityIds,
                    'notes' => 'Luxury view room in ' . ($floorModel?->name ?? 'Main Wing'),
                ]
            );

            $createdRooms[$r['room_number']] = $room;
        }

        // 18. Housekeeping Histories for All Rooms
        $cleanedHk = HousekeepingState::where('name', 'like', '%Cleaned%')->first();
        $dirtyHk = HousekeepingState::where('name', 'like', '%Dirty%')->first();

        $dirtyRoomNumbers = ['103', '107', '201', '203', '204', '207', '301', '309', '407', '503', '507'];

        foreach ($createdRooms as $rNum => $roomObj) {
            $isDirty = in_array($rNum, $dirtyRoomNumbers);
            $hkId = $isDirty ? $dirtyHk?->id : $cleanedHk?->id;
            $hkStatus = $isDirty ? 'pending' : 'complete';

            if ($hkId) {
                RoomHousekeepingHistory::updateOrCreate(
                    [
                        'room_id' => $roomObj->id,
                    ],
                    [
                        'housekeeping_status_id' => $hkId,
                        'start_time' => now()->subHours($isDirty ? 1 : 4),
                        'completion_time' => $isDirty ? null : now()->subHours(2),
                        'status' => $hkStatus,
                    ]
                );
            }
        }

        // 19. Operational Histories for All Rooms
        $activeOp = OperationalStatus::where('name', 'like', '%Active%')->first();
        $maintOp = OperationalStatus::where('name', 'like', '%Maintenance%')->first();
        $blockedOp = OperationalStatus::where('name', 'like', '%Blocked%')->orWhere('name', 'like', '%Order%')->first();

        foreach ($createdRooms as $rNum => $roomObj) {
            $opId = $activeOp?->id;
            if (in_array($rNum, ['104', '504']) && $blockedOp) {
                $opId = $blockedOp->id;
            } elseif (in_array($rNum, ['404', '508']) && $maintOp) {
                $opId = $maintOp->id;
            }

            if ($opId) {
                RoomOperationalHistory::updateOrCreate(
                    [
                        'room_id' => $roomObj->id,
                    ],
                    [
                        'operational_status_id' => $opId,
                    ]
                );
            }
        }

        // 20. Sample Room Maintenances
        if (!empty($createdRooms['104'])) {
            RoomMaintenance::firstOrCreate(
                ['room_id' => $createdRooms['104']->id, 'reason' => 'AC Compressor Servicing'],
                [
                    'assign' => 'Suresh Electricals',
                    'expected_date_time' => now()->addDays(2),
                    'note' => 'Scheduled inspection and refrigerant top-up for luxury wing unit.',
                    'status' => 'Active',
                ]
            );
        }

        // 21. Seed Initial Guests with Dynamic IDs and Auto-Generated Reserve IDs
        $defaultRegType = RegistrationType::where('name', 'like', '%Regular%')->first() ?? RegistrationType::first();
        $vipRegType = RegistrationType::where('name', 'like', '%VIP%')->first();
        $corpRegType = RegistrationType::where('name', 'like', '%Corporate%')->first();
        $phoneResMode = ReservationMode::where('name', 'like', '%Phone%')->first() ?? ReservationMode::first();
        $otaResMode = ReservationMode::where('name', 'like', '%OTA%')->first() ?? ReservationMode::first();
        $webResMode = ReservationMode::where('name', 'like', '%Website%')->first() ?? ReservationMode::first();
        $indianNat = Nationality::where('name', 'like', '%Indian%')->first() ?? Nationality::first();
        $mrTitle = Title::where('name', 'like', '%Mr%')->first() ?? Title::first();
        $mrsTitle = Title::where('name', 'like', '%Mrs%')->first() ?? Title::first();
        $drTitle = Title::where('name', 'like', '%Dr%')->first() ?? Title::first();
        $aadhaarId = IdCardType::where('name', 'like', '%Aadhaar%')->first() ?? IdCardType::first();
        $panId = IdCardType::where('name', 'like', '%PAN%')->first() ?? IdCardType::first();
        $passportId = IdCardType::where('name', 'like', '%Passport%')->first() ?? IdCardType::first();
        $upiPayment = PaymentMode::where('name', 'like', '%UPI%')->first() ?? PaymentMode::first();
        $cardPayment = PaymentMode::where('name', 'like', '%Card%')->first() ?? PaymentMode::first();
        $cashPayment = PaymentMode::where('name', 'like', '%Cash%')->first() ?? PaymentMode::first();
        $tcsComp = Company::where('name', 'like', '%Tata%')->first();

        $initialGuestsData = [
            ['room' => '102', 'name' => 'Sanjeev Kumar Singh', 'mobile' => '+91 98112 34567', 'email' => 'sanjeev.ksingh@gmail.com', 'title' => $mrTitle, 'status' => 'Arrived', 'balance' => 10400.00, 'advance' => 5000.00, 'folio' => 'FOL-102-882', 'id_type' => $aadhaarId, 'id_no' => '9812 4567 8901', 'reg' => $defaultRegType, 'mode' => $phoneResMode, 'payment' => $upiPayment, 'seq' => 815],
            ['room' => '202', 'name' => 'AJEET BHENGRA', 'mobile' => '+91 97712 90123', 'email' => 'ajeet.b@outlook.com', 'title' => $mrTitle, 'status' => 'Stay Over', 'balance' => 7800.00, 'advance' => 4000.00, 'folio' => 'FOL-202-710', 'id_type' => $panId, 'id_no' => 'AAEPB8765K', 'reg' => $defaultRegType, 'mode' => $otaResMode, 'payment' => $cardPayment, 'seq' => 816],
            ['room' => '205', 'name' => 'KHAGESWAR ROUT', 'mobile' => '+91 94370 55123', 'email' => 'khageswar.rout@gmail.com', 'title' => $mrTitle, 'status' => 'Stay Over', 'balance' => 14200.00, 'advance' => 6000.00, 'folio' => 'FOL-205-551', 'id_type' => $aadhaarId, 'id_no' => '8892 1102 4432', 'reg' => $defaultRegType, 'mode' => $webResMode, 'payment' => $upiPayment, 'seq' => 817],
            ['room' => '206', 'name' => 'Raj kumar Bhunia', 'mobile' => '+91 98321 44091', 'email' => 'raj.bhunia@gmail.com', 'title' => $mrTitle, 'status' => 'Arrived', 'balance' => 3920.00, 'advance' => 3000.00, 'folio' => 'FOL-206-339', 'id_type' => $aadhaarId, 'id_no' => '4401 2291 0032', 'reg' => $defaultRegType, 'mode' => $phoneResMode, 'payment' => $cashPayment, 'seq' => 818],
            ['room' => '302', 'name' => 'SANGADA RAJUBHAI NAL', 'mobile' => '+91 99042 18273', 'email' => 'sangada.raju@gmail.com', 'title' => $mrTitle, 'status' => 'Stay Over', 'balance' => 8100.00, 'advance' => 4500.00, 'folio' => 'FOL-302-991', 'id_type' => $panId, 'id_no' => 'BKPSR9901M', 'reg' => $defaultRegType, 'mode' => $otaResMode, 'payment' => $upiPayment, 'seq' => 819],
            ['room' => '304', 'name' => 'HITENDRA NINAWE', 'mobile' => '+91 98230 44910', 'email' => 'hitendra.n@gmail.com', 'title' => $mrTitle, 'status' => 'Stay Over', 'balance' => 11900.00, 'advance' => 5000.00, 'folio' => 'FOL-304-102', 'id_type' => $aadhaarId, 'id_no' => '5540 1928 3341', 'reg' => $defaultRegType, 'mode' => $phoneResMode, 'payment' => $cardPayment, 'seq' => 820],
            ['room' => '305', 'name' => 'ADVAIT CHAVAN', 'mobile' => '+91 97654 32189', 'email' => 'advait.chavan@yahoo.com', 'title' => $mrTitle, 'status' => 'Stay Over', 'balance' => 12400.00, 'advance' => 5000.00, 'folio' => 'FOL-305-673', 'id_type' => $aadhaarId, 'id_no' => '7761 9902 1145', 'reg' => $defaultRegType, 'mode' => $webResMode, 'payment' => $upiPayment, 'seq' => 821],
            ['room' => '307', 'name' => 'BIKRAM KR SAHOO', 'mobile' => '+91 94380 99182', 'email' => 'bikram.sahoo@rediffmail.com', 'title' => $mrTitle, 'status' => 'Stay Over', 'balance' => 16500.00, 'advance' => 6500.00, 'folio' => 'FOL-307-889', 'id_type' => $panId, 'id_no' => 'AQPRS1120K', 'reg' => $defaultRegType, 'mode' => $otaResMode, 'payment' => $upiPayment, 'seq' => 822],
            ['room' => '401', 'name' => 'Vikramaditya Roy', 'mobile' => '+91 98300 77123', 'email' => 'vikramaditya.roy@corp.in', 'title' => $mrTitle, 'status' => 'Arrived', 'balance' => 14500.00, 'advance' => 8000.00, 'folio' => 'FOL-401-440', 'id_type' => $passportId, 'id_no' => 'Z9812345', 'reg' => $corpRegType ?? $defaultRegType, 'mode' => $phoneResMode, 'payment' => $cardPayment, 'seq' => 823, 'company' => $tcsComp],
            ['room' => '403', 'name' => 'Priyanka Mukherjee', 'mobile' => '+91 98311 55442', 'email' => 'priyanka.m@gmail.com', 'title' => $mrsTitle, 'status' => 'Stay Over', 'balance' => 6200.00, 'advance' => 4500.00, 'folio' => 'FOL-403-109', 'id_type' => $aadhaarId, 'id_no' => '6651 8890 2231', 'reg' => $vipRegType ?? $defaultRegType, 'mode' => $webResMode, 'payment' => $upiPayment, 'seq' => 824],
            ['room' => '405', 'name' => 'Rahul Verma', 'mobile' => '+91 98711 00921', 'email' => 'rahul.verma@techmail.com', 'title' => $mrTitle, 'status' => 'Arrived', 'balance' => 4100.00, 'advance' => 3500.00, 'folio' => 'FOL-405-772', 'id_type' => $aadhaarId, 'id_no' => '3310 9921 5542', 'reg' => $defaultRegType, 'mode' => $phoneResMode, 'payment' => $upiPayment, 'seq' => 825],
            ['room' => '408', 'name' => 'Dr. Ananya Sen', 'mobile' => '+91 98450 11982', 'email' => 'dr.ananya.sen@hospital.org', 'title' => $drTitle, 'status' => 'Stay Over', 'balance' => 19500.00, 'advance' => 10000.00, 'folio' => 'FOL-408-204', 'id_type' => $aadhaarId, 'id_no' => '7654 3210 9876', 'reg' => $vipRegType ?? $defaultRegType, 'mode' => $phoneResMode, 'payment' => $cardPayment, 'seq' => 826],
            ['room' => '502', 'name' => 'Rajendra Narayan Malhotra', 'mobile' => '+91 98200 44981', 'email' => 'rn.malhotra@luxurygroup.com', 'title' => $mrTitle, 'status' => 'Arrived', 'balance' => 22000.00, 'advance' => 12000.00, 'folio' => 'FOL-502-301', 'id_type' => $passportId, 'id_no' => 'K8871290', 'reg' => $corpRegType ?? $defaultRegType, 'mode' => $phoneResMode, 'payment' => $cardPayment, 'seq' => 827],
            ['room' => '506', 'name' => 'Sourav Ganguly', 'mobile' => '+91 98300 00199', 'email' => 'sourav.ganguly@cricket.in', 'title' => $mrTitle, 'status' => 'Stay Over', 'balance' => 17800.00, 'advance' => 8500.00, 'folio' => 'FOL-506-440', 'id_type' => $aadhaarId, 'id_no' => '9900 1122 3344', 'reg' => $vipRegType ?? $defaultRegType, 'mode' => $webResMode, 'payment' => $upiPayment, 'seq' => 828],
            ['room' => '509', 'name' => 'Meera Nambiar', 'mobile' => '+91 98470 33219', 'email' => 'meera.nambiar@heritage.com', 'title' => $mrsTitle, 'status' => 'Arrived', 'balance' => 24500.00, 'advance' => 15000.00, 'folio' => 'FOL-509-912', 'id_type' => $aadhaarId, 'id_no' => '8810 4452 9901', 'reg' => $vipRegType ?? $defaultRegType, 'mode' => $phoneResMode, 'payment' => $cardPayment, 'seq' => 829],
        ];

        $finYear = (date('n') >= 4) ? date('Y') . '-' . (date('Y') + 1) : (date('Y') - 1) . '-' . date('Y');

        foreach ($initialGuestsData as $gd) {
            $roomObj = $createdRooms[$gd['room']] ?? null;
            if (!$roomObj) continue;

            $reserveCode = "{$gd['seq']}\\{$finYear}";

            Guest::updateOrCreate(
                ['reserve_id' => $reserveCode],
                [
                    'registration_type_id' => $gd['reg']?->id,
                    'reservation_mode_id' => $gd['mode']?->id,
                    'company_id' => isset($gd['company']) ? $gd['company']?->id : null,
                    'reserve_date' => now()->toDateString(),
                    'reserve_time' => now()->format('H:i'),
                    'title_id' => $gd['title']?->id,
                    'guest_name' => $gd['name'],
                    'guest_address' => 'Flat / Suite ' . $gd['room'] . ', Sagar Sonnet Residency',
                    'nationality_id' => $indianNat?->id,
                    'city' => 'Kolkata',
                    'mobile' => $gd['mobile'],
                    'email' => $gd['email'],
                    'status' => $gd['status'],
                    'has_privilege_card' => ($gd['reg']?->id === $vipRegType?->id),
                    'privilege_card_no' => ($gd['reg']?->id === $vipRegType?->id) ? 'PRIV-' . rand(1000, 9999) : null,
                    'room_id' => $roomObj->id,
                    'id_card_type_id' => $gd['id_type']?->id,
                    'id_card_number' => $gd['id_no'],
                    'payment_mode_id' => $gd['payment']?->id,
                    'advance_amount' => $gd['advance'],
                    'payment_remarks' => 'Advance confirmed via ' . ($gd['payment']?->name ?? 'Online'),
                    'folio_number' => $gd['folio'],
                    'balance' => $gd['balance'],
                ]
            );
        }
    }
}
