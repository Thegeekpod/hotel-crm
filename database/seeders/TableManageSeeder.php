<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TableManage;

class TableManageSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['table_number' => 'Table 1', 'table_name' => 'Sea Breeze Deck', 'status' => 'available'],
            ['table_number' => 'Table 2', 'table_name' => 'Lawn View', 'status' => 'available'],
            ['table_number' => 'Table 3', 'table_name' => 'Poolside', 'status' => 'available'],
            ['table_number' => 'Table 4', 'table_name' => 'Indoor AC', 'status' => 'available'],
            ['table_number' => 'Table 5', 'table_name' => 'Family Cluster', 'status' => 'available'],
            ['table_number' => 'VIP Booth 01', 'table_name' => 'VIP Booth 01', 'status' => 'available'],
        ];

        foreach ($defaults as $d) {
            TableManage::firstOrCreate(['table_number' => $d['table_number']], $d);
        }
    }
}
