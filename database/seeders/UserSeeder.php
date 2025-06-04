<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create the Admin role if it doesn't exist
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);

        // Create the admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'employee_number' => 'EMP-0001',
                'first_name'      => 'System',
                'last_name'       => 'Administrator',
                'department'      => 'IT Department',
                'photo'           => null,
                'phone_number'    => '123-456-7890',
                'password'        => Hash::make('admin1234'), // ⚠️ Change in production
            ]
        );

        // Assign the Admin role
        if (!$adminUser->hasRole('Admin')) {
            $adminUser->assignRole($adminRole);
        }
    }
}
