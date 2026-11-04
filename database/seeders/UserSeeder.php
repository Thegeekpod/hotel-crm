<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds for all users & staff permissions.
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('12345678');

        $users = [
            [
                'name'              => 'Debashis Roy',
                'phone'             => '9876543210',
                'email'             => 'admin@hotelsagarsonnet.com',
                'password'          => $defaultPassword,
                'role'              => 'admin',
                'assigned_sections' => ['frontoffice', 'pos', 'stores', 'banquet', 'accounts', 'admin'],
                'status'            => 'Active',
            ],
            [
                'name'              => 'Front Desk Executive',
                'phone'             => '9876543211',
                'email'             => 'frontdesk@hotelsagarsonnet.com',
                'password'          => $defaultPassword,
                'role'              => 'staff',
                'assigned_sections' => ['frontoffice'],
                'status'            => 'Active',
            ],
            [
                'name'              => 'Housekeeping Supervisor',
                'phone'             => '9876543212',
                'email'             => 'housekeeping@hotelsagarsonnet.com',
                'password'          => $defaultPassword,
                'role'              => 'staff',
                'assigned_sections' => ['frontoffice'],
                'status'            => 'Active',
            ],
            [
                'name'              => 'Amit Sen (POS Staff)',
                'phone'             => '9876543299',
                'email'             => 'pos@hotelsagarsonnet.com',
                'password'          => $defaultPassword,
                'role'              => 'staff',
                'assigned_sections' => ['pos'],
                'status'            => 'Active',
            ],
            [
                'name'              => 'Tanmoy Das (Banquet Manager)',
                'phone'             => '9876543213',
                'email'             => 'banquet@hotelsagarsonnet.com',
                'password'          => $defaultPassword,
                'role'              => 'staff',
                'assigned_sections' => ['banquet'],
                'status'            => 'Active',
            ],
            [
                'name'              => 'Subir Ghosh (Store In-Charge)',
                'phone'             => '9876543214',
                'email'             => 'stores@hotelsagarsonnet.com',
                'password'          => $defaultPassword,
                'role'              => 'staff',
                'assigned_sections' => ['stores'],
                'status'            => 'Active',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['phone' => $userData['phone']],
                $userData
            );
        }

        $this->command->info('User management seeder executed successfully. All passwords set to 12345678.');
    }
}

