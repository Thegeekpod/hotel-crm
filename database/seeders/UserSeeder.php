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
                'email'             => 'admin@mail.com',
                'password'          => $defaultPassword,
                'role'              => 'admin',
                'assigned_sections' => ['frontoffice', 'pos', 'stores', 'banquet', 'accounts', 'admin'],
                'status'            => 'Active',
            ],
            [
                'name'              => 'Front Desk Executive',
                'email'             => 'frontdesk@mail.com',
                'password'          => $defaultPassword,
                'role'              => 'staff',
                'assigned_sections' => ['frontoffice'],
                'status'            => 'Active',
            ],
            [
                'name'              => 'Housekeeping Supervisor',
                'email'             => 'housekeeping@mail.com',
                'password'          => $defaultPassword,
                'role'              => 'staff',
                'assigned_sections' => ['frontoffice'],
                'status'            => 'Active',
            ],
            [
                'name'              => 'Amit Sen (POS Staff)',
                'email'             => 'pos@mail.com',
                'password'          => $defaultPassword,
                'role'              => 'staff',
                'assigned_sections' => ['pos'],
                'status'            => 'Active',
            ],
            [
                'name'              => 'Tanmoy Das (Banquet Manager)',
                'email'             => 'banquet@mail.com',
                'password'          => $defaultPassword,
                'role'              => 'staff',
                'assigned_sections' => ['banquet'],
                'status'            => 'Active',
            ],
            [
                'name'              => 'Subir Ghosh (Store In-Charge)',
                'email'             => 'stores@mail.com',
                'password'          => $defaultPassword,
                'role'              => 'staff',
                'assigned_sections' => ['stores'],
                'status'            => 'Active',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('User management seeder executed successfully. All passwords set to 12345678.');
    }
}

