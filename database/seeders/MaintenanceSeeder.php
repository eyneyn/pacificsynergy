<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Maintenance;

class MaintenanceSeeder extends Seeder
{
    public function run(): void
    {
        $maintenances = [
            // OPL
            ['name' => 'Change Over', 'type' => 'OPL'],
            ['name' => 'Full Warehouse / Manual Palletizing', 'type' => 'OPL'],
            ['name' => 'Reload Film or Label', 'type' => 'OPL'],
            ['name' => 'Material Quality', 'type' => 'OPL'],
            ['name' => 'Start Up SOP', 'type' => 'OPL'],
            ['name' => 'Fine Tuning', 'type' => 'OPL'],
            ['name' => 'Line Clearance / Sanitation', 'type' => 'OPL'],
            ['name' => 'Pack Mats/ Bottle Jam', 'type' => 'OPL'],
            ['name' => 'QA Testing', 'type' => 'OPL'],
            ['name' => 'Shutdown SOP', 'type' => 'OPL'],
            ['name' => 'Power Interruption', 'type' => 'OPL'],
            ['name' => 'Stand Up Meeeting', 'type' => 'OPL'],
            ['name' => 'Forklift Delay', 'type' => 'OPL'],
            ['name' => 'CIP', 'type' => 'OPL'],

            // EPL
            ['name' => 'Blow Mold', 'type' => 'EPL'],
            ['name' => 'Auxilliary', 'type' => 'EPL'],
            ['name' => 'Case Conveyor', 'type' => 'EPL'],
            ['name' => 'Filler', 'type' => 'EPL'],
            ['name' => 'Shrink Packer', 'type' => 'EPL'],
            ['name' => 'OPP Labeller', 'type' => 'EPL'],
            ['name' => 'Water Treatment', 'type' => 'EPL'],
            ['name' => 'Case Coder', 'type' => 'EPL'],
            ['name' => 'Laser Coder', 'type' => 'EPL'],
            ['name' => 'Palletizer', 'type' => 'EPL'],
            ['name' => 'Cap Coder', 'type' => 'EPL'],
            ['name' => 'Bottle Conveyor', 'type' => 'EPL'],
        ];

        foreach ($maintenances as $maintenance) {
            Maintenance::create($maintenance);
        }
    }
}
