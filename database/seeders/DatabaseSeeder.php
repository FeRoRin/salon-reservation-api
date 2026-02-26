<?php

namespace Database\Seeders;

use App\Models\BusinessSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        User::firstOrCreate(
            ['email' => 'superadmin@salon.com'],
            [
                'name'     => 'Super Admin',
                'phone'    => '+1234567890',
                'password' => Hash::make('SuperAdmin@123'),
                'role'     => 'super_admin',
            ]
        );

        // Default Business Settings
        BusinessSetting::firstOrCreate([], [
            'open_time'    => '09:00:00',
            'close_time'   => '18:00:00',
            'working_days' => [1, 2, 3, 4, 5], // Monday to Friday
        ]);

        $this->command->info('Super Admin created: superadmin@salon.com / SuperAdmin@123');
        $this->command->info('Business settings initialized.');
    }
}
