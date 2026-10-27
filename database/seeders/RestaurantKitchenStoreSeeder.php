<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PosItem;
use App\Models\StoreCategory;
use App\Models\StoreSupplier;
use App\Models\StoreItem;
use App\Models\StoreMovement;
use App\Models\StoreRequisition;
use App\Models\PosItemIngredient;

class RestaurantKitchenStoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Restaurant Kitchen Store Categories
        $categories = [
            ['name' => 'Meat, Poultry & Eggs', 'code' => 'MEAT', 'description' => 'Fresh broiler chicken, mutton, seafood, eggs for daily tandoor & curry prep'],
            ['name' => 'Dairy & Paneer', 'code' => 'DAIRY', 'description' => 'Fresh malai paneer, milk, cooking cream, butter, curd and pure ghee'],
            ['name' => 'Grains, Rice & Flour', 'code' => 'GRAINS', 'description' => 'Aged Basmati rice, wheat atta, maida, black urad dal, lentils and batters'],
            ['name' => 'Beverages, Mocktails & Bar', 'code' => 'BEV', 'description' => 'Mocktail syrups, Arabica coffee beans, mineral water, imported sparkling waters, tea'],
            ['name' => 'Spices, Oils & Dry Pantry', 'code' => 'SPICE', 'description' => 'Tandoori masalas, cooking oil, mustard oil, saffron, whole spices and dry provisions'],
            ['name' => 'Desserts & Sweet Provisions', 'code' => 'DESSERT', 'description' => 'Khoya/mawa, kulfi bricks, falooda sev, rose water, cardamom and dessert mixes'],
        ];

        $catMap = [];
        foreach ($categories as $cat) {
            $catMap[$cat['name']] = StoreCategory::updateOrCreate(['name' => $cat['name']], $cat);
        }

        // 2. Restaurant Kitchen Suppliers
        $suppliers = [
            [
                'name' => 'Bengal Fresh Poultry & Organic Farms',
                'contact_person' => 'Subhashish Ghosh',
                'phone' => '+91 98302 11223',
                'email' => 'orders@bengalfreshfarms.com',
                'gst_number' => '19AAACB1122D1Z5',
                'address' => 'Barasat Agricultural Zone, North 24 Parganas, WB',
            ],
            [
                'name' => 'Mother Dairy & Creamery Logistics Ltd',
                'contact_person' => 'Amitabh Sen',
                'phone' => '+91 98310 44556',
                'email' => 'sales@motherdairywb.in',
                'gst_number' => '19AAACM3344F1Z8',
                'address' => 'Dankuni Dairy Hub, Hooghly, WB',
            ],
            [
                'name' => 'Kolkata Wholesale Grains & Spices Syndicate',
                'contact_person' => 'Ramesh Agarwal',
                'phone' => '+91 98201 55667',
                'email' => 'supply@kolkataspices.com',
                'gst_number' => '19AAACK9988H1Z1',
                'address' => 'Posta Wholesale Bazaar, Burrabazar, Kolkata',
            ],
            [
                'name' => 'Blue Mountain Coffee Roasters & Syrups',
                'contact_person' => 'Pooja Hegde',
                'phone' => '+91 98451 77332',
                'email' => 'orders@bluemountainroasters.com',
                'gst_number' => '29AAACB9988H1Z4',
                'address' => 'Chikmagalur Plantation Hub, Karnataka',
            ],
        ];

        $supMap = [];
        foreach ($suppliers as $sup) {
            $supMap[$sup['name']] = StoreSupplier::updateOrCreate(['name' => $sup['name']], $sup);
        }

        // 3. Restaurant Kitchen Store Items (With "Kota Plate Hobe" plates_per_unit yield)
        $items = [
            [
                'sku' => 'KIT-01',
                'name' => 'Fresh Chicken (Curry / Tandoori Cut)',
                'store_category_id' => $catMap['Meat, Poultry & Eggs']->id,
                'default_supplier_id' => $supMap['Bengal Fresh Poultry & Organic Farms']->id,
                'unit' => 'Kg',
                'plates_per_unit' => 4.00, // 1 kg = 4 plates of Butter Chicken / Tandoori Murgh
                'unit_cost' => 240.00,
                'current_stock' => 25, // 25 Kg = 100 plates!
                'min_threshold' => 10, // Min 40 plates
                'location' => 'Kitchen Deep Chiller C-1',
                'description' => 'Fresh tender broiler chicken cut and cleaned daily for tandoor and gravies (250g meat per portion)',
                'status' => 'Optimal',
            ],
            [
                'sku' => 'KIT-02',
                'name' => 'Fresh Malai Paneer (Dairy Fresh)',
                'store_category_id' => $catMap['Dairy & Paneer']->id,
                'default_supplier_id' => $supMap['Mother Dairy & Creamery Logistics Ltd']->id,
                'unit' => 'Kg',
                'plates_per_unit' => 5.00, // 1 kg = 5 plates of Paneer Tikka / Lababdar
                'unit_cost' => 380.00,
                'current_stock' => 20, // 20 Kg = 100 plates!
                'min_threshold' => 6,
                'location' => 'Dairy Walk-in Fridge',
                'description' => 'Pure cow milk soft malai cottage cheese blocks (200g per portion/plate)',
                'status' => 'Optimal',
            ],
            [
                'sku' => 'KIT-03',
                'name' => 'Aged Royal Basmati Rice (Daawat Gold)',
                'store_category_id' => $catMap['Grains, Rice & Flour']->id,
                'default_supplier_id' => $supMap['Kolkata Wholesale Grains & Spices Syndicate']->id,
                'unit' => 'Kg',
                'plates_per_unit' => 5.00, // 1 kg raw = 5 plates cooked biryani / jeera rice
                'unit_cost' => 135.00,
                'current_stock' => 60, // 60 Kg = 300 plates!
                'min_threshold' => 20,
                'location' => 'Dry Grains Store Shelf R-1',
                'description' => '2-year aged long grain aromatic basmati rice for dum biryani and jeera rice',
                'status' => 'Optimal',
            ],
            [
                'sku' => 'KIT-04',
                'name' => 'Tender Baby Mutton (Dum Biryani Cut)',
                'store_category_id' => $catMap['Meat, Poultry & Eggs']->id,
                'default_supplier_id' => $supMap['Bengal Fresh Poultry & Organic Farms']->id,
                'unit' => 'Kg',
                'plates_per_unit' => 4.00, // 1 kg = 4 plates Awadhi Mutton Biryani
                'unit_cost' => 780.00,
                'current_stock' => 14, // 14 Kg = 56 plates!
                'min_threshold' => 8,
                'location' => 'Meat Deep Freezer F-2',
                'description' => 'Fresh prime tender baby goat meat cut for royal Awadhi dum slow cooking',
                'status' => 'Optimal',
            ],
            [
                'sku' => 'KIT-05',
                'name' => 'Farm Fresh Table Eggs (Tray Pack)',
                'store_category_id' => $catMap['Meat, Poultry & Eggs']->id,
                'default_supplier_id' => $supMap['Bengal Fresh Poultry & Organic Farms']->id,
                'unit' => 'Pcs',
                'plates_per_unit' => 0.50, // 2 eggs per breakfast platter
                'unit_cost' => 7.00,
                'current_stock' => 160, // 160 eggs = 80 breakfast platters!
                'min_threshold' => 40,
                'location' => 'Egg Storage Racks',
                'description' => 'Grade A sanitized farm fresh eggs for breakfast cooking and room orders',
                'status' => 'Optimal',
            ],
            [
                'sku' => 'KIT-06',
                'name' => 'South Indian Idli & Dosa Fermented Batter',
                'store_category_id' => $catMap['Grains, Rice & Flour']->id,
                'default_supplier_id' => $supMap['Kolkata Wholesale Grains & Spices Syndicate']->id,
                'unit' => 'Kg',
                'plates_per_unit' => 4.00, // 1 kg = 4 South Indian platters
                'unit_cost' => 65.00,
                'current_stock' => 18, // 18 Kg = 72 platters!
                'min_threshold' => 6,
                'location' => 'Prep Chiller Shelf B-1',
                'description' => 'Naturally fermented rice & urad dal stone ground batter for crispy dosas and fluffy idlis',
                'status' => 'Optimal',
            ],
            [
                'sku' => 'KIT-07',
                'name' => 'Black Urad Dal (Dal Makhani Special)',
                'store_category_id' => $catMap['Grains, Rice & Flour']->id,
                'default_supplier_id' => $supMap['Kolkata Wholesale Grains & Spices Syndicate']->id,
                'unit' => 'Kg',
                'plates_per_unit' => 6.00, // 1 kg raw dal = 6 plates Dal Makhani
                'unit_cost' => 140.00,
                'current_stock' => 25, // 25 Kg = 150 plates!
                'min_threshold' => 8,
                'location' => 'Dry Grains Store Shelf R-2',
                'description' => 'Whole black lentils slow cooked overnight on charcoal with butter and cream',
                'status' => 'Optimal',
            ],
            [
                'sku' => 'KIT-08',
                'name' => 'Fresh Full Cream Milk & Cooking Cream',
                'store_category_id' => $catMap['Dairy & Paneer']->id,
                'default_supplier_id' => $supMap['Mother Dairy & Creamery Logistics Ltd']->id,
                'unit' => 'Liters',
                'plates_per_unit' => 6.00, // 1 liter = 6 cups cappuccino or gravy base
                'unit_cost' => 68.00,
                'current_stock' => 35, // 35 Liters = 210 servings!
                'min_threshold' => 12,
                'location' => 'Dairy Chiller D-1',
                'description' => 'Pasteurized standardized full cream 6% fat milk for coffee bar and creamy gravies',
                'status' => 'Optimal',
            ],
            [
                'sku' => 'KIT-09',
                'name' => 'Gourmet Arabica Coffee Beans (Medium Roast)',
                'store_category_id' => $catMap['Beverages, Mocktails & Bar']->id,
                'default_supplier_id' => $supMap['Blue Mountain Coffee Roasters & Syrups']->id,
                'unit' => 'Kg',
                'plates_per_unit' => 70.00, // 1 kg = 70 double espresso cups (14g per shot)
                'unit_cost' => 920.00,
                'current_stock' => 8, // 8 Kg = 560 cups of Cappuccino Royale!
                'min_threshold' => 3,
                'location' => 'Barista Dry Store Bin B-1',
                'description' => '100% single origin Arabica coffee beans with chocolate and caramel notes',
                'status' => 'Optimal',
            ],
            [
                'sku' => 'KIT-10',
                'name' => 'Sagar Lagoon Blue Curacao Mocktail Syrup',
                'store_category_id' => $catMap['Beverages, Mocktails & Bar']->id,
                'default_supplier_id' => $supMap['Blue Mountain Coffee Roasters & Syrups']->id,
                'unit' => 'Bottles',
                'plates_per_unit' => 25.00, // 1 bottle 750ml = 25 mocktail glasses (30ml per drink)
                'unit_cost' => 380.00,
                'current_stock' => 6, // 6 Bottles = 150 mocktails!
                'min_threshold' => 2,
                'location' => 'Bar Store Rack M-1',
                'description' => 'Premium cocktail & mocktail citrus infusion syrup for Sea Breeze lagoon drinks',
                'status' => 'Optimal',
            ],
            [
                'sku' => 'KIT-11',
                'name' => 'San Pellegrino Sparkling Mineral Water (330ml)',
                'store_category_id' => $catMap['Beverages, Mocktails & Bar']->id,
                'default_supplier_id' => $supMap['Blue Mountain Coffee Roasters & Syrups']->id,
                'unit' => 'Bottles',
                'plates_per_unit' => 1.00, // 1 bottle = 1 serving
                'unit_cost' => 110.00,
                'current_stock' => 96, // 96 servings
                'min_threshold' => 24,
                'location' => 'Beverage Cold Chiller',
                'description' => 'Imported natural sparkling mineral water in 330ml glass bottles',
                'status' => 'Optimal',
            ],
            [
                'sku' => 'KIT-12',
                'name' => 'Pure Khoya / Mawa (Gulab Jamun Prep)',
                'store_category_id' => $catMap['Desserts & Sweet Provisions']->id,
                'default_supplier_id' => $supMap['Mother Dairy & Creamery Logistics Ltd']->id,
                'unit' => 'Kg',
                'plates_per_unit' => 8.00, // 1 kg = 8 plates (2 large jamuns per plate)
                'unit_cost' => 360.00,
                'current_stock' => 12, // 12 Kg = 96 plates!
                'min_threshold' => 4,
                'location' => 'Pastry Chiller Shelf S-2',
                'description' => 'Fresh slow evaporated unsweetened rich dairy khoya for artisanal hot gulab jamuns',
                'status' => 'Optimal',
            ],
            [
                'sku' => 'KIT-13',
                'name' => 'Royal Saffron Kulfi Bricks (Rabri Base)',
                'store_category_id' => $catMap['Desserts & Sweet Provisions']->id,
                'default_supplier_id' => $supMap['Mother Dairy & Creamery Logistics Ltd']->id,
                'unit' => 'Pcs',
                'plates_per_unit' => 5.00, // 1 brick = 5 falooda plates
                'unit_cost' => 210.00,
                'current_stock' => 16, // 16 Bricks = 80 plates!
                'min_threshold' => 5,
                'location' => 'Dessert Deep Freezer D-3',
                'description' => 'Pista kesar rich malai kulfi bricks prepared with thick rabri for falooda dessert platters',
                'status' => 'Optimal',
            ],
            [
                'sku' => 'KIT-14',
                'name' => 'Refined Sunflower Oil & Desi Cow Ghee',
                'store_category_id' => $catMap['Spices, Oils & Dry Pantry']->id,
                'default_supplier_id' => $supMap['Kolkata Wholesale Grains & Spices Syndicate']->id,
                'unit' => 'Liters',
                'plates_per_unit' => 15.00, // 1 liter covers ~15 plates of cooking / tarka
                'unit_cost' => 160.00,
                'current_stock' => 40, // 40 Liters = 600 plates cooking coverage!
                'min_threshold' => 15,
                'location' => 'Oil & Liquid Store O-1',
                'description' => 'Cooking oil tins and fragrant Bilona cow ghee for gravies, parathas and dum biryani',
                'status' => 'Optimal',
            ],
        ];

        $createdStoreItems = [];
        foreach ($items as $it) {
            $created = StoreItem::updateOrCreate(['sku' => $it['sku']], $it);
            $createdStoreItems[$it['sku']] = $created;

            // Log initial Inward stock movement
            StoreMovement::create([
                'store_item_id' => $created->id,
                'type' => 'IN',
                'quantity' => $created->current_stock,
                'stock_before' => 0,
                'stock_after' => $created->current_stock,
                'reference_no' => 'INWARD-' . $created->sku,
                'issued_to_department' => 'Kitchen & Restaurant Store',
                'remarks' => "Inward fresh kitchen stock received (Yield: {$created->plates_per_unit} plates/{$created->unit} = {$created->total_plates} Plates)",
                'performed_by' => 'Chef / F&B Store Keeper',
            ]);
        }

        // 4. Link POS Dishes to Kitchen Store Ingredients for automatic portion yield tracking!
        $posDishes = PosItem::all()->keyBy('name');

        $recipes = [
            'Mughlai Butter Chicken & Naan' => [
                ['sku' => 'KIT-01', 'qty' => 0.250, 'unit' => 'Kg'], // 250g Chicken
            ],
            'Tandoori Murgh (Full)' => [
                ['sku' => 'KIT-01', 'qty' => 0.750, 'unit' => 'Kg'], // 750g Chicken
            ],
            'Awadhi Dum Biryani (Mutton)' => [
                ['sku' => 'KIT-04', 'qty' => 0.250, 'unit' => 'Kg'], // 250g Mutton
                ['sku' => 'KIT-03', 'qty' => 0.200, 'unit' => 'Kg'], // 200g Basmati Rice
            ],
            'Paneer Tikka Charcoal' => [
                ['sku' => 'KIT-02', 'qty' => 0.200, 'unit' => 'Kg'], // 200g Paneer
            ],
            'Paneer Lababdar with Lachha Paratha' => [
                ['sku' => 'KIT-02', 'qty' => 0.200, 'unit' => 'Kg'], // 200g Paneer
            ],
            'Dal Makhani with Jeera Rice' => [
                ['sku' => 'KIT-07', 'qty' => 0.150, 'unit' => 'Kg'], // 150g Makhani Dal
                ['sku' => 'KIT-03', 'qty' => 0.150, 'unit' => 'Kg'], // 150g Rice
            ],
            'Sagar Sonnet Breakfast Platter' => [
                ['sku' => 'KIT-05', 'qty' => 2.000, 'unit' => 'Pcs'], // 2 Eggs
            ],
            'South Indian Platter' => [
                ['sku' => 'KIT-06', 'qty' => 0.250, 'unit' => 'Kg'], // 250g Batter
            ],
            'Sagar Lagoon Mocktail' => [
                ['sku' => 'KIT-10', 'qty' => 0.040, 'unit' => 'Bottles'], // 1 drink
            ],
            'Cappuccino Royale' => [
                ['sku' => 'KIT-09', 'qty' => 0.015, 'unit' => 'Kg'], // 15g Beans
                ['sku' => 'KIT-08', 'qty' => 0.150, 'unit' => 'Liters'], // 150ml Milk
            ],
            'Gulab Jamun with Rabri' => [
                ['sku' => 'KIT-12', 'qty' => 0.125, 'unit' => 'Kg'], // 125g Khoya
            ],
            'Royal Kesar Kulfi Falooda' => [
                ['sku' => 'KIT-13', 'qty' => 0.200, 'unit' => 'Pcs'], // 1/5 Brick Kulfi
            ],
        ];

        foreach ($recipes as $dishName => $ings) {
            $dish = $posDishes[$dishName] ?? null;
            if (!$dish) continue;

            foreach ($ings as $ingData) {
                $storeItem = $createdStoreItems[$ingData['sku']] ?? null;
                if (!$storeItem) continue;

                PosItemIngredient::updateOrCreate([
                    'pos_item_id' => $dish->id,
                    'store_item_id' => $storeItem->id,
                ], [
                    'quantity_per_plate' => $ingData['qty'],
                    'unit' => $ingData['unit'],
                ]);
            }
        }
    }
}
