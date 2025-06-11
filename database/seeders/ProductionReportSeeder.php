<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductionReport;
use App\Models\Status;

class ProductionReportSeeder extends Seeder
{
    public function run(): void
    {
        $report = ProductionReport::create([
            'production_date' => '2025-02-04',
            'shift' => '00:00H - 24:00H',
            'line' => 1,
            'ac1' => 17,
            'ac2' => 17,
            'ac3' => 17,
            'ac4' => 17,
            'manpower_present' => 0,
            'manpower_absent' => 0,
            'sku' => 'AQUASPRING 350 x24',
            'fbo_fco' => '00:00H - 00:00H',
            'lbo_lco' => '24:00H - 24:00H',
            'total_outputCase' => 11648,
            'filler_speed' => 12000,
            'opp_labeler_speed' => 15000,
            'opp_labels' => 431,
            'shrinkfilm' => 31,
            'caps_filling' => 361,
            'bottle_filling' => 406,
            'blow_molding_output' => 278257,
            'speed_blow_molding' => 17000,
            'preform_blow_molding' => 18,
            'bottles_blow_molding' => 25,
            'qa_remarks' => 'Passed',
            'with_label' => 24,
            'without_label' => 210,
            'total_sample' => 234,
            'total_downtime' => 328,
            'bottle_code' => 'EXP 02 FEB 25',
            'user_id' => 1,
        ]);

        // ✅ Insert initial status for the report
        Status::create([
            'user_id' => $report->user_id,
            'production_report_id' => $report->id,
            'status' => 'Submitted',
        ]);
    }
}
