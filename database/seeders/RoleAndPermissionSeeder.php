<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Admin
            'user.dashboard',
            'roles.permission',
            'employees.index',
            'employees.create',
            'employees.edit',
            'employees.delete',

            // Production Reports
            'analytics.dashboard',
            'report.index',
            'report.view',
            'report.add',
            'report.edit',
            'report.validate',
            'analytics.index',
            'configuration.index',
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create the Admin role
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);

        // Sync all permissions to Admin
        $adminRole->syncPermissions(Permission::all());

        // Create the admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'eynbatoy@gmail.com'],
            [
                'employee_number' => 'EMP-0001',
                'first_name'      => 'Grazel Angel',
                'last_name'       => 'Batoy',
                'department'      => 'Production Department',
                'photo'           => 'img/default.jpg',
                'phone_number'    => '09123456789',
                'password'        => Hash::make('admin1234'), // ⚠️ Change this in production
            ]
        );

        // Assign role if not yet assigned
        if (!$adminUser->hasRole('Admin')) {
            $adminUser->assignRole('Admin');
        }
    }
}
