<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BanquetHall;
use App\Models\HotelService;
use App\Models\BanquetBooking;
use App\Models\RoomServiceOrder;
use App\Models\RoomServiceOrderItem;
use App\Models\Room;
use App\Models\Booking;

class BanquetAndServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Banquet Halls
        $halls = [
            [
                'name' => 'Sagar Sonnet Grand Crystal Ballroom',
                'code' => 'BALLROOM-1',
                'type' => 'Ballroom',
                'capacity_pax' => 350,
                'base_tariff' => 150000.00,
                'amenities' => 'High-gain 4K Laser Projector, Bose Sound Reinforcement, Dynamic LED Uplighting, Stage & Podiums, Green Room',
                'location' => 'Ground Floor East Wing',
                'status' => 'Reserved',
            ],
            [
                'name' => 'Sonnet Executive Boardroom',
                'code' => 'BOARDROOM-A',
                'type' => 'Boardroom',
                'capacity_pax' => 25,
                'base_tariff' => 25000.00,
                'amenities' => '85" 4K Video Conference Display, Cisco Webex Integration, Leather Ergonomic Seating, High-speed Fibre Wi-Fi',
                'location' => 'Mezzanine Floor',
                'status' => 'Available',
            ],
            [
                'name' => 'Royal Sagar Palm Terrace Lawn',
                'code' => 'LAWN-1',
                'type' => 'Lawn',
                'capacity_pax' => 500,
                'base_tariff' => 220000.00,
                'amenities' => 'Lush Green Landscaped Garden, Fairylight Canopy Trussing, Dedicated Live Buffet Islands, Valet Driveway Access',
                'location' => 'West Gardens',
                'status' => 'Reserved',
            ],
            [
                'name' => 'Summit Conference Hall B',
                'code' => 'CONF-B',
                'type' => 'Conference Hall',
                'capacity_pax' => 120,
                'base_tariff' => 65000.00,
                'amenities' => 'Theatre or Cluster seating, Dual Microphones, Presentation Clicker, Buffet Antechamber',
                'location' => 'First Floor Convention Hub',
                'status' => 'Available',
            ],
        ];

        $createdHalls = [];
        foreach ($halls as $h) {
            $createdHalls[$h['name']] = BanquetHall::updateOrCreate(['name' => $h['name']], $h);
        }

        // 2. Hotel Services (In-Room Dining Menu & Services)
        $services = [
            // Dining / Room Service Food
            ['name' => 'Royal Club Sandwich & Crispy Fries', 'code' => 'RS-01', 'category' => 'Room Dining', 'price' => 380.00, 'unit' => 'Per Order', 'description' => 'Triple-decker toasted sourdough, grilled chicken/paneer, cheese, house dip', 'status' => 'Active'],
            ['name' => 'Paneer Butter Masala with 2 Butter Naan', 'code' => 'RS-02', 'category' => 'Room Dining', 'price' => 450.00, 'unit' => 'Per Order', 'description' => 'Cottage cheese cubes simmered in rich creamy tomato cashew gravy', 'status' => 'Active'],
            ['name' => 'Butter Chicken with 2 Butter Naan', 'code' => 'RS-03', 'category' => 'Room Dining', 'price' => 520.00, 'unit' => 'Per Order', 'description' => 'Clay oven roasted tandoori chicken pulled into silky makhani gravy', 'status' => 'Active'],
            ['name' => 'Hyderabadi Dum Biryani with Raita & Mirchi Salan', 'code' => 'RS-04', 'category' => 'Room Dining', 'price' => 490.00, 'unit' => 'Per Order', 'description' => 'Aromatic basmati rice cooked on slow dum with royal spices and saffron', 'status' => 'Active'],
            ['name' => 'Gourmet Arabica Hot Cappuccino & Cookies', 'code' => 'RS-05', 'category' => 'Room Dining', 'price' => 180.00, 'unit' => 'Per Cup', 'description' => 'Freshly ground espresso with micro-foam and artisanal cookies', 'status' => 'Active'],
            ['name' => 'Continental Breakfast Tray to Room', 'code' => 'RS-06', 'category' => 'Room Dining', 'price' => 450.00, 'unit' => 'Per Person', 'description' => 'Croissants, toast, butter & marmalade, fresh juice, eggs to order & coffee', 'status' => 'Active'],
            ['name' => 'Fresh Fruit Platter & Exotic Berries', 'code' => 'RS-07', 'category' => 'Room Dining', 'price' => 260.00, 'unit' => 'Per Platter', 'description' => 'Seasonal sliced fruits, imported berries and organic wild honey', 'status' => 'Active'],

            // Guest Services
            ['name' => 'Express Laundry & Steam Press Service', 'code' => 'SV-01', 'category' => 'Laundry & Dry Clean', 'price' => 350.00, 'unit' => 'Per Pair', 'description' => 'Same-day 4-hour express washing and crisp steam finishing', 'status' => 'Active'],
            ['name' => 'Full Suit / Blazer Dry Cleaning', 'code' => 'SV-02', 'category' => 'Laundry & Dry Clean', 'price' => 450.00, 'unit' => 'Per Piece', 'description' => 'Premium gentle solvent cleaning and garment bag hanger', 'status' => 'Active'],
            ['name' => 'Luxury Airport Pick-up / Drop (Innova Crysta)', 'code' => 'SV-03', 'category' => 'Concierge & Transport', 'price' => 1800.00, 'unit' => 'Per Trip', 'description' => 'Chauffeur driven private airport transfer with bottled water and Wi-Fi', 'status' => 'Active'],
            ['name' => 'Extra Rollaway Luxury Bed & Linen Set', 'code' => 'SV-04', 'category' => 'Room Addon', 'price' => 1200.00, 'unit' => 'Per Night', 'description' => 'Full size foldaway memory foam bed placed in guest room', 'status' => 'Active'],
            ['name' => 'Ayurvedic Rejuvenation Room Massage (60 min)', 'code' => 'SV-05', 'category' => 'Spa & Wellness', 'price' => 2400.00, 'unit' => 'Per Session', 'description' => 'Full body aromatherapy deep tissue massage by certified therapist', 'status' => 'Active'],
        ];

        foreach ($services as $srv) {
            HotelService::updateOrCreate(['name' => $srv['name']], $srv);
        }

        // 3. Sample Banquet Bookings
        $crystalBallroom = $createdHalls['Sagar Sonnet Grand Crystal Ballroom'] ?? null;
        if ($crystalBallroom && BanquetBooking::where('banquet_hall_id', $crystalBallroom->id)->count() === 0) {
            BanquetBooking::create([
                'booking_reference' => 'BNQ-2026-001',
                'banquet_hall_id' => $crystalBallroom->id,
                'client_name' => 'Nexus Technologies Corp',
                'company_name' => 'Nexus Technologies',
                'phone' => '+91 98300 44556',
                'email' => 'events@nexustech.com',
                'event_name' => 'Annual Corporate Gala & Awards',
                'event_date' => now()->addDays(2),
                'time_slot' => 'Evening (6:30 PM - 11:30 PM)',
                'guaranteed_pax' => 300,
                'hall_rental' => 150000.00,
                'catering_per_plate' => 1200.00,
                'total_amount' => 510000.00,
                'advance_paid' => 200000.00,
                'balance_due' => 310000.00,
                'status' => 'Confirmed',
                'function_sheet_notes' => 'Theme: Gold & Black. Audio setup for keynote speaker at 7:30 PM followed by cocktail dinner and live jazz band.',
            ]);
        }

        $lawn = $createdHalls['Royal Sagar Palm Terrace Lawn'] ?? null;
        if ($lawn && BanquetBooking::where('banquet_hall_id', $lawn->id)->count() === 0) {
            BanquetBooking::create([
                'booking_reference' => 'BNQ-2026-002',
                'banquet_hall_id' => $lawn->id,
                'client_name' => 'Singhania Family',
                'company_name' => 'Singhania Group',
                'phone' => '+91 98211 77889',
                'email' => 'singhania.events@gmail.com',
                'event_name' => 'Destination Wedding Reception',
                'event_date' => now()->addDays(5),
                'time_slot' => 'Evening (7:00 PM - 12:00 AM)',
                'guaranteed_pax' => 450,
                'hall_rental' => 220000.00,
                'catering_per_plate' => 1500.00,
                'total_amount' => 895000.00,
                'advance_paid' => 400000.00,
                'balance_due' => 495000.00,
                'status' => 'Confirmed',
                'function_sheet_notes' => 'Full lawn fairylight mandap, 12 live food stations, VIP lounge setup with floral centerpieces.',
            ]);
        }

        // 4. Sample Room Service Orders attached to existing occupied rooms
        $activeBooking = Booking::with('room', 'guest')->where('status', '!=', 'Checked Out')->latest()->first();
        if ($activeBooking && $activeBooking->room) {
            $sandwich = HotelService::where('code', 'RS-01')->first();
            $biryani = HotelService::where('code', 'RS-04')->first();
            $coffee = HotelService::where('code', 'RS-05')->first();

            $order = RoomServiceOrder::updateOrCreate(
                ['order_number' => 'ORD-2026-001'],
                [
                    'room_id' => $activeBooking->room_id,
                    'booking_id' => $activeBooking->id,
                    'guest_name' => $activeBooking->guest?->guest_name ?? 'In-House Guest',
                    'room_number' => $activeBooking->room?->room_number ?? '101',
                    'subtotal' => 1050.00,
                    'tax_amount' => 52.50,
                    'total_amount' => 1102.50,
                    'payment_status' => 'Charged to Room Folio',
                    'order_status' => 'Delivered',
                    'delivery_time' => '30 mins',
                    'special_instructions' => 'Extra spicy biryani, deliver with fresh mint raita.',
                    'served_by' => 'Staff Suresh',
                ]
            );

            if ($sandwich) {
                RoomServiceOrderItem::updateOrCreate(
                    ['room_service_order_id' => $order->id, 'item_name' => $sandwich->name],
                    ['hotel_service_id' => $sandwich->id, 'unit_price' => $sandwich->price, 'quantity' => 1, 'line_total' => $sandwich->price]
                );
            }
            if ($biryani) {
                RoomServiceOrderItem::updateOrCreate(
                    ['room_service_order_id' => $order->id, 'item_name' => $biryani->name],
                    ['hotel_service_id' => $biryani->id, 'unit_price' => $biryani->price, 'quantity' => 1, 'line_total' => $biryani->price]
                );
            }
            if ($coffee) {
                RoomServiceOrderItem::updateOrCreate(
                    ['room_service_order_id' => $order->id, 'item_name' => $coffee->name],
                    ['hotel_service_id' => $coffee->id, 'unit_price' => $coffee->price, 'quantity' => 1, 'line_total' => $coffee->price]
                );
            }
        }
    }
}
