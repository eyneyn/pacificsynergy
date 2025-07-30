<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductionReport;
use App\Models\Defect;
use App\Models\LineQcReject;

class LineQcRejectSeeder extends Seeder
{
    public function run(): void
    {
        $report = ProductionReport::first();

        $rejects = [
            ['defect_name' => 'No Caps', 'quantity' => 11],
            ['defect_name' => 'Tampered band damage', 'quantity' => 3],
            ['defect_name' => 'Low Fill', 'quantity' => 19],
            ['defect_name' => 'Scratched on Bottle', 'quantity' => 232],
            ['defect_name' => 'Bottle with Pin Hole', 'quantity' => 11],
            ['defect_name' => 'Visible Glue', 'quantity' => 69],
            ['defect_name' => 'Sticky/Messy Bottle', 'quantity' => 284],
            ['defect_name' => 'Out of square', 'quantity' => 15],
            ['defect_name' => 'Wrong print LDPE Shrinkfilm', 'quantity' => 15],
        ];

        foreach ($rejects as $reject) {
            $defect = Defect::where('defect_name', $reject['defect_name'])->first();
            if ($defect) {
                LineQcReject::create([
                    'production_reports_id' => $report->id,
                    'defects_id' => $defect->id,
                    'quantity' => $reject['quantity']
                ]);
            }
        }
    }
}
