<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StoreCategory;

class MenuCategorySeeder extends Seeder
{
    /**
     * Run the database seeds for Menu Categories.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Breakfast',
                'code' => 'BKF',
                'description' => 'Morning breakfast specials, South Indian platters, Continental, pancakes, eggs & toasts',
                'status' => 'Active',
            ],
            [
                'name' => 'Starters',
                'code' => 'STR',
                'description' => 'Crispy appetisers, tandoori kebabs, paneer tikka, chicken tikka, fish finger & starters',
                'status' => 'Active',
            ],
            [
                'name' => 'Main Course',
                'code' => 'MNC',
                'description' => 'Royal gravies, butter chicken, paneer butter masala, mutton rogan josh, dal makhani & curries',
                'status' => 'Active',
            ],
            [
                'name' => 'Biryani & Rice',
                'code' => 'BRY',
                'description' => 'Awadhi dum biryani, Kolkata chicken/mutton biryani, jeera rice, pulao & steamed basmati rice',
                'status' => 'Active',
            ],
            [
                'name' => 'Tandoori & Breads',
                'code' => 'BRD',
                'description' => 'Butter naan, garlic naan, tandoori roti, lachha paratha, roomali roti & stuffed kulchas',
                'status' => 'Active',
            ],
            [
                'name' => 'Chinese & Asian',
                'code' => 'CHN',
                'description' => 'Wok-tossed noodles, fried rice, chilli chicken, manchurian, crispy chilli babycorn & momos',
                'status' => 'Active',
            ],
            [
                'name' => 'Continental & Fast Food',
                'code' => 'CNT',
                'description' => 'Gourmet burgers, club sandwiches, creamy white/red sauce pasta, pizzas & French fries',
                'status' => 'Active',
            ],
            [
                'name' => 'Soups & Salads',
                'code' => 'SOP',
                'description' => 'Hot & sour soup, sweet corn soup, tomato shorba, fresh green salad, caesar salad & raita',
                'status' => 'Active',
            ],
            [
                'name' => 'Desserts',
                'code' => 'DST',
                'description' => 'Gulab jamun with rabri, kesar kulfi falooda, sizzling brownies with vanilla ice cream & sweets',
                'status' => 'Active',
            ],
            [
                'name' => 'Beverages & Mocktails',
                'code' => 'BEV',
                'description' => 'Virgin mojito, fresh lime soda, blue lagoon, fruit milkshakes, lassi & cold drinks',
                'status' => 'Active',
            ],
            [
                'name' => 'Tea & Coffee',
                'code' => 'HOT',
                'description' => 'Special masala chai, Darjeeling tea, South Indian filter coffee, cappuccino, latte & espresso',
                'status' => 'Active',
            ],
            [
                'name' => 'Snacks & Finger Food',
                'code' => 'SNK',
                'description' => 'Evening tea-time snacks, crispy pakodas, samosas, cutlets and quick savory bites',
                'status' => 'Active',
            ],
        ];

        foreach ($categories as $cat) {
            StoreCategory::updateOrCreate(
                ['name' => $cat['name']],
                [
                    'code' => $cat['code'],
                    'description' => $cat['description'],
                    'status' => $cat['status'],
                ]
            );
        }
    }
}

