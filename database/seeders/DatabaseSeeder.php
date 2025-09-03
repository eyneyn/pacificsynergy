<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            StandardSeeder::class,
            DefectSeeder::class,
            MaintenanceSeeder::class,
            LineSeeder::class,
            RoleAndPermissionSeeder::class,
            ProductionReportSeeder::class
        ]);
    }
}
