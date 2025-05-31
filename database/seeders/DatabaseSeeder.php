<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create the Admin role
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);

        // 2. Create the admin user with full details
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'employee_number' => 'EMP-0001',
                'first_name'      => 'System',
                'last_name'       => 'Administrator',
                'department'      => 'IT Department',
                'photo'           => null,
                'phone_number'    => '123-456-7890',
                'email'           => 'admin@example.com',
                'password'        => Hash::make('admin1234'), // ⚠️ Change in production
            ]
        );

        // 3. Assign the Admin role
        if (!$adminUser->hasRole('Admin')) {
            $adminUser->assignRole($adminRole);
        }
    }
}
