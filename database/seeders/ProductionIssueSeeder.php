<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductionReport;
use App\Models\ProductionIssues;

class ProductionIssueSeeder extends Seeder
{
    public function run(): void
    {
        $report = ProductionReport::first();

        $issues = [
            ['maintenances_id' => 1, 'remarks' => 'Underrated speed of filler to 12,000 bph due to schedule run of line 2,1 liter size of bottle and low supply', 'minutes' => 173],
            ['maintenances_id' => 2, 'remarks' => 'Change empty core of label', 'minutes' => 10],
            ['maintenances_id' => 3, 'remarks' => 'Change empty core of film', 'minutes' => 16],
            ['maintenances_id' => 4, 'remarks' => 'Bottle fall down during run at utfeed curve', 'minutes' => 8],
            ['maintenances_id' => 5, 'remarks' => 'Scratches on lower portion of the bottle...', 'minutes' => 20],
            ['maintenances_id' => 6, 'remarks' => 'Upper guide issue with label vacuum drum', 'minutes' => 11],
            ['maintenances_id' => 7, 'remarks' => 'Film jammed issue during run...', 'minutes' => 13],
            ['maintenances_id' => 8, 'remarks' => 'Lack of water in RWT, BT, CT and elevated tank', 'minutes' => 77],
        ];

        foreach ($issues as $issue) {
            ProductionIssues::create(array_merge($issue, ['production_reports_id' => $report->id]));
        }
    }
}
