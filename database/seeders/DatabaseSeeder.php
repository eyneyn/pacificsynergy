<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            StandardSeeder::class,
            DefectSeeder::class,
            MaintenanceSeeder::class,
            LineSeeder::class,
            ProductionReportSeeder::class,
            ProductionIssueSeeder::class,
            LineQcRejectSeeder::class
            
        ]);
    }
}
