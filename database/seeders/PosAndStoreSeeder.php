<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PosItem;
use App\Models\StoreCategory;
use App\Models\StoreSupplier;
use App\Models\StoreItem;
use App\Models\StoreMovement;
use App\Models\StoreRequisition;

class PosAndStoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed POS Menu Items
        $posItems = [
            // Breakfast
            [
                'code' => 'BK-01',
                'name' => 'Sagar Sonnet Breakfast Platter',
                'category' => 'Breakfast',
                'price' => 650.00,
                'emoji' => '🍳',
                'description' => 'Two eggs to order, bacon/chicken sausage, grilled tomatoes, hash brown, toasted brioche and fresh juice',
                'status' => 'Active',
            ],
            [
                'code' => 'BK-02',
                'name' => 'South Indian Platter',
                'category' => 'Breakfast',
                'price' => 420.00,
                'emoji' => '🥘',
                'description' => 'Steamed idlis, crispy medu vada, mini masala dosa, coconut chutney and aromatic sambar',
                'status' => 'Active',
            ],
            [
                'code' => 'BK-03',
                'name' => 'Continental Breakfast Tray',
                'category' => 'Breakfast',
                'price' => 450.00,
                'emoji' => '🥐',
                'description' => 'Warm butter croissants, pain au chocolat, fresh seasonal cut fruits, artisanal jam and choice of tea/coffee',
                'status' => 'Active',
            ],

            // Starters
            [
                'code' => 'ST-01',
                'name' => 'Paneer Tikka Charcoal',
                'category' => 'Starters',
                'price' => 450.00,
                'emoji' => '🧀',
                'description' => 'Clay-oven charred cottage cheese cubes marinated in Kashmiri red chili, mustard oil and hung curd',
                'status' => 'Active',
            ],
            [
                'code' => 'ST-02',
                'name' => 'Tandoori Murgh (Full)',
                'category' => 'Starters',
                'price' => 620.00,
                'emoji' => '🍗',
                'description' => 'Whole spring chicken marinated overnight with royal spices, roasted in charcoal tandoor',
                'status' => 'Active',
            ],
            [
                'code' => 'ST-03',
                'name' => 'Crispy Corn Salt & Pepper',
                'category' => 'Starters',
                'price' => 320.00,
                'emoji' => '🌽',
                'description' => 'Wok-tossed golden sweet corn kernels, spring onions, cracked black pepper and cilantro',
                'status' => 'Active',
            ],

            // Main Course
            [
                'code' => 'MC-01',
                'name' => 'Mughlai Butter Chicken & Naan',
                'category' => 'Main Course',
                'price' => 690.00,
                'emoji' => '🍛',
                'description' => 'Smoked tandoori chicken shreds simmered in velvety butter tomato gravy, served with 2 butter naan',
                'status' => 'Active',
            ],
            [
                'code' => 'MC-02',
                'name' => 'Awadhi Dum Biryani (Mutton)',
                'category' => 'Main Course',
                'price' => 750.00,
                'emoji' => '🍲',
                'description' => 'Tender baby goat pieces layered with aged aromatic basmati rice, saffron and ghee, served with burani raita',
                'status' => 'Active',
            ],
            [
                'code' => 'MC-03',
                'name' => 'Dal Makhani with Jeera Rice',
                'category' => 'Main Course',
                'price' => 490.00,
                'emoji' => '🍚',
                'description' => 'Black lentils slow cooked overnight on charcoal embers with fresh cream and butter, paired with cumin rice',
                'status' => 'Active',
            ],
            [
                'code' => 'MC-04',
                'name' => 'Paneer Lababdar with Lachha Paratha',
                'category' => 'Main Course',
                'price' => 520.00,
                'emoji' => '🥘',
                'description' => 'Cottage cheese chunks cooked in rich onion-tomato masala enriched with grated paneer and cashew paste',
                'status' => 'Active',
            ],

            // Beverages
            [
                'code' => 'BV-01',
                'name' => 'Sagar Lagoon Mocktail',
                'category' => 'Beverages',
                'price' => 280.00,
                'emoji' => '🍹',
                'description' => 'Blue curacao, crushed mint leaves, lime juice, sparkling soda and sweet lychee nectar',
                'status' => 'Active',
            ],
            [
                'code' => 'BV-02',
                'name' => 'Cappuccino Royale',
                'category' => 'Beverages',
                'price' => 210.00,
                'emoji' => '☕',
                'description' => 'Freshly extracted double espresso with velvety steamed milk froth and Belgian chocolate dusting',
                'status' => 'Active',
            ],
            [
                'code' => 'BV-03',
                'name' => 'Fresh Lime Soda / Mint Cooler',
                'category' => 'Beverages',
                'price' => 160.00,
                'emoji' => '🥤',
                'description' => 'Refreshing freshly squeezed lemon, Himalayan black salt, sugar syrup and chilled club soda',
                'status' => 'Active',
            ],

            // Desserts
            [
                'code' => 'DS-01',
                'name' => 'Gulab Jamun with Rabri',
                'category' => 'Desserts',
                'price' => 250.00,
                'emoji' => '🍨',
                'description' => 'Warm khoya dumplings soaked in saffron-cardamom syrup, served on chilled laccha rabri with crushed pistachios',
                'status' => 'Active',
            ],
            [
                'code' => 'DS-02',
                'name' => 'Royal Kesar Kulfi Falooda',
                'category' => 'Desserts',
                'price' => 290.00,
                'emoji' => '🍧',
                'description' => 'Traditional saffron-almond kulfi slices served with rose syrup, falooda sev and basil seeds',
                'status' => 'Active',
            ],
        ];

        foreach ($posItems as $item) {
            PosItem::updateOrCreate(['name' => $item['name']], $item);
        }

        // 2. Seed Store Categories
        $categories = [
            ['name' => 'Linen & Bedding', 'code' => 'LINEN', 'description' => 'Bed sheets, duvet covers, pillowcases, bath towels and bathrobes'],
            ['name' => 'Guest Toiletries', 'code' => 'TOIL', 'description' => 'Soaps, shampoos, body washes, dental kits and vanity items'],
            ['name' => 'F&B Stores', 'code' => 'FNB', 'description' => 'Dry provisions, coffee beans, imported waters, beverages and minibar items'],
            ['name' => 'Cleaning Supplies', 'code' => 'CLEAN', 'description' => 'Disinfectants, detergents, room sprays, mops and housekeeping chemicals'],
            ['name' => 'Maintenance', 'code' => 'MAINT', 'description' => 'Bulbs, electrical fittings, plumbing parts and hardware essentials'],
        ];

        $categoryMap = [];
        foreach ($categories as $cat) {
            $categoryMap[$cat['name']] = StoreCategory::updateOrCreate(['name' => $cat['name']], $cat);
        }

        // 3. Seed Store Suppliers
        $suppliers = [
            [
                'name' => 'Apex Hospitality Linen Mills Ltd',
                'contact_person' => 'Rajeev Malhotra',
                'phone' => '+91 98301 22445',
                'email' => 'sales@apexlinen.com',
                'gst_number' => '19AAACA1234F1Z8',
                'address' => 'Plot 44, Industrial Textile Estate, Howrah, WB',
            ],
            [
                'name' => 'Himalayan Organic Toiletries',
                'contact_person' => 'Sunil Verma',
                'phone' => '+91 98200 88991',
                'email' => 'orders@himalayantoiletries.in',
                'gst_number' => '05AAACH5566G2Z1',
                'address' => 'Dehradun Fragrance Park, Uttarakhand',
            ],
            [
                'name' => 'Blue Mountain Coffee Roasters & Provisions',
                'contact_person' => 'Pooja Hegde',
                'phone' => '+91 98451 77332',
                'email' => 'support@bluemountainroasters.com',
                'gst_number' => '29AAACB9988H1Z4',
                'address' => 'Chikmagalur Plantation Hub, Karnataka',
            ],
            [
                'name' => 'Sagar CleanChem Solutions',
                'contact_person' => 'Anand Sen',
                'phone' => '+91 98311 44556',
                'email' => 'contact@sagarcleanchem.com',
                'gst_number' => '19AAACS7744K1ZQ',
                'address' => 'Sector V, Salt Lake, Kolkata, WB',
            ],
        ];

        $supplierMap = [];
        foreach ($suppliers as $sup) {
            $supplierMap[$sup['name']] = StoreSupplier::updateOrCreate(['name' => $sup['name']], $sup);
        }

        // 4. Seed Store Items (matching Static/stores/index.html)
        $storeItems = [
            [
                'sku' => 'ST-01',
                'name' => 'Luxury Cotton Bed Linens (King)',
                'store_category_id' => $categoryMap['Linen & Bedding']->id,
                'default_supplier_id' => $supplierMap['Apex Hospitality Linen Mills Ltd']->id,
                'unit' => 'Pcs',
                'unit_cost' => 850.00,
                'current_stock' => 140,
                'min_threshold' => 50,
                'location' => 'Main Store Shelf L-01',
                'description' => '300-thread count 100% Egyptian cotton king sized crisp white fitted bed sheets',
                'status' => 'Optimal',
            ],
            [
                'sku' => 'ST-02',
                'name' => 'Plush Bath Towels (White)',
                'store_category_id' => $categoryMap['Linen & Bedding']->id,
                'default_supplier_id' => $supplierMap['Apex Hospitality Linen Mills Ltd']->id,
                'unit' => 'Pcs',
                'unit_cost' => 350.00,
                'current_stock' => 220,
                'min_threshold' => 80,
                'location' => 'Main Store Shelf L-04',
                'description' => '650 GSM heavy plush combed cotton white bath towels',
                'status' => 'Optimal',
            ],
            [
                'sku' => 'ST-03',
                'name' => 'Herbal Shower Gel & Shampoo 50ml',
                'store_category_id' => $categoryMap['Guest Toiletries']->id,
                'default_supplier_id' => $supplierMap['Himalayan Organic Toiletries']->id,
                'unit' => 'Bottles',
                'unit_cost' => 45.00,
                'current_stock' => 35,
                'min_threshold' => 100,
                'location' => 'Toiletry Bin T-02',
                'description' => 'Eco-friendly biodegradable 50ml bottles with green tea and aloe vera extracts',
                'status' => 'Low Stock Alert',
            ],
            [
                'sku' => 'ST-04',
                'name' => 'Dental Kits & Shaving Kits',
                'store_category_id' => $categoryMap['Guest Toiletries']->id,
                'default_supplier_id' => $supplierMap['Himalayan Organic Toiletries']->id,
                'unit' => 'Kits',
                'unit_cost' => 28.00,
                'current_stock' => 180,
                'min_threshold' => 60,
                'location' => 'Toiletry Bin T-05',
                'description' => 'Individually boxed bamboo toothbrush with toothpaste and twin-blade razor kit',
                'status' => 'Optimal',
            ],
            [
                'sku' => 'ST-05',
                'name' => 'Gourmet Arabica Coffee Beans',
                'store_category_id' => $categoryMap['F&B Stores']->id,
                'default_supplier_id' => $supplierMap['Blue Mountain Coffee Roasters & Provisions']->id,
                'unit' => 'Kg',
                'unit_cost' => 920.00,
                'current_stock' => 12,
                'min_threshold' => 25,
                'location' => 'Dry Food Pantry F-01',
                'description' => 'Medium roast single origin 100% Arabica espresso beans packed in 1kg sealed valve bags',
                'status' => 'Low Stock Alert',
            ],
            [
                'sku' => 'ST-06',
                'name' => 'Minibar Imported Sparkling Water',
                'store_category_id' => $categoryMap['F&B Stores']->id,
                'default_supplier_id' => $supplierMap['Blue Mountain Coffee Roasters & Provisions']->id,
                'unit' => 'Bottles',
                'unit_cost' => 120.00,
                'current_stock' => 95,
                'min_threshold' => 40,
                'location' => 'Beverage Rack F-03',
                'description' => 'San Pellegrino 330ml glass bottles for in-room minibars and dining',
                'status' => 'Optimal',
            ],
            [
                'sku' => 'ST-07',
                'name' => 'Sanitizing Floor Wash & Cleaner (5L)',
                'store_category_id' => $categoryMap['Cleaning Supplies']->id,
                'default_supplier_id' => $supplierMap['Sagar CleanChem Solutions']->id,
                'unit' => 'Liters',
                'unit_cost' => 180.00,
                'current_stock' => 60,
                'min_threshold' => 30,
                'location' => 'Chemical Store C-01',
                'description' => 'Hospitality grade antimicrobial surface cleaner with fresh pine fragrance',
                'status' => 'Optimal',
            ],
            [
                'sku' => 'ST-08',
                'name' => 'Room Mist Aerosol Air Freshener',
                'store_category_id' => $categoryMap['Cleaning Supplies']->id,
                'default_supplier_id' => $supplierMap['Sagar CleanChem Solutions']->id,
                'unit' => 'Cans',
                'unit_cost' => 210.00,
                'current_stock' => 15,
                'min_threshold' => 20,
                'location' => 'Chemical Store C-03',
                'description' => 'Long-lasting lavender lemongrass dry room air neutralizer spray',
                'status' => 'Low Stock Alert',
            ],
        ];

        foreach ($storeItems as $sItem) {
            $created = StoreItem::updateOrCreate(['sku' => $sItem['sku']], $sItem);

            // Log initial stock movement if none exists
            if ($created->movements()->count() === 0) {
                StoreMovement::create([
                    'store_item_id' => $created->id,
                    'type' => 'IN',
                    'quantity' => $created->current_stock,
                    'stock_before' => 0,
                    'stock_after' => $created->current_stock,
                    'reference_no' => 'INIT-STOCK-' . $created->sku,
                    'issued_to_department' => 'Central Inventory',
                    'remarks' => 'Initial stock inventory audit opening balance',
                    'performed_by' => 'Stores Supervisor',
                ]);
            }
        }

        // 5. Seed a sample requisition for the low stock items
        $lowStockItem = StoreItem::where('sku', 'ST-03')->first();
        if ($lowStockItem && StoreRequisition::where('store_item_id', $lowStockItem->id)->count() === 0) {
            StoreRequisition::create([
                'requisition_no' => 'PR-2026-001',
                'store_item_id' => $lowStockItem->id,
                'store_supplier_id' => $lowStockItem->default_supplier_id,
                'quantity_requested' => 150,
                'estimated_cost' => 150 * 45.00,
                'priority' => 'Urgent',
                'status' => 'Approved',
                'requested_by' => 'Stores Supervisor',
                'notes' => 'Stock is below 35% threshold. Require delivery by weekend for high weekend arrivals.',
                'required_by_date' => now()->addDays(2),
            ]);
        }
    }
}
