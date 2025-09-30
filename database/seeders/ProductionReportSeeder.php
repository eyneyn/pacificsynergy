<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductionReport;
use App\Models\Status;
use App\Models\ProductionIssues;
use App\Models\LineQcReject;

class ProductionReportSeeder extends Seeder
{
    public function run(): void
    {
        $report1 = ProductionReport::create([
            'production_date' => '2025-02-04', //Year-Mon
            'shift' => '00:00H - 24:00H',
            'line' => 1,
            'ac1' => 17,
            'ac2' => 17,
            'ac3' => 17,
            'ac4' => 17,
            'sku_id' => 2,
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
            'user_id' => $report1->user_id,
            'production_report_id' => $report1->id,
            'status' => 'Submitted',
        ]);

                // Issues
        $issues1 = [
            ['maintenances_id' => 1, 'remarks' => 'Underrated speed of filler to 12,000 bph due to schedule run of line 2,1 liter size of bottle and low supply', 'minutes' => 173],
            ['maintenances_id' => 2, 'remarks' => 'Change empty core of label', 'minutes' => 10],
            ['maintenances_id' => 3, 'remarks' => 'Change empty core of film', 'minutes' => 16],
            ['maintenances_id' => 4, 'remarks' => 'Bottle fall down during run at utfeed curve', 'minutes' => 8],
            ['maintenances_id' => 5, 'remarks' => 'Scratches on lower portion of the bottle...', 'minutes' => 20],
            ['maintenances_id' => 6, 'remarks' => 'Upper guide issue with label vacuum drum', 'minutes' => 11],
            ['maintenances_id' => 7, 'remarks' => 'Film jammed issue during run...', 'minutes' => 13],
            ['maintenances_id' => 8, 'remarks' => 'Lack of water in RWT, BT, CT and elevated tank', 'minutes' => 77],
        ];

        foreach ($issues1 as $i) {
            ProductionIssues::create([
                'production_reports_id' => $report1->id,
                'maintenances_id' => $i['maintenances_id'],
                'remarks' => $i['remarks'],
                'minutes' => $i['minutes'],
            ]);
        }

                // QC Rejects
        $rejects1 = [
            ['defects_id' => 1, 'quantity' => 63],
            ['defects_id' => 2, 'quantity' => 85],
            ['defects_id' => 3, 'quantity' => 36],
            ['defects_id' => 4, 'quantity' => 22],
            ['defects_id' => 34, 'quantity' => 15],
            ['defects_id' => 20, 'quantity' => 11]
        ];

        foreach ($rejects1 as $r) {
            LineQcReject::create([
                'production_reports_id' => $report1->id,
                'defects_id' => $r['defects_id'],
                'quantity' => $r['quantity'],
            ]);
        }

                $report2 = ProductionReport::create([
            'production_date' => '2025-03-04',
            'shift' => '00:00H - 24:00H',
            'line' => 1,
            'ac1' => 17,
            'ac2' => 17,
            'ac3' => 17,
            'ac4' => 17,
            'sku_id' => 1,
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
            'user_id' => $report2->user_id,
            'production_report_id' => $report2->id,
            'status' => 'Submitted',
        ]);

                // Issues
        $issues2 = [
            ['maintenances_id' => 1, 'remarks' => 'Underrated speed of filler to 12,000 bph due to schedule run of line 2,1 liter size of bottle and low supply', 'minutes' => 173],
            ['maintenances_id' => 2, 'remarks' => 'Change empty core of label', 'minutes' => 10],
            ['maintenances_id' => 3, 'remarks' => 'Change empty core of film', 'minutes' => 16],
            ['maintenances_id' => 4, 'remarks' => 'Bottle fall down during run at utfeed curve', 'minutes' => 8],
            ['maintenances_id' => 5, 'remarks' => 'Scratches on lower portion of the bottle...', 'minutes' => 20],
            ['maintenances_id' => 6, 'remarks' => 'Upper guide issue with label vacuum drum', 'minutes' => 11],
            ['maintenances_id' => 7, 'remarks' => 'Film jammed issue during run...', 'minutes' => 13],
            ['maintenances_id' => 8, 'remarks' => 'Lack of water in RWT, BT, CT and elevated tank', 'minutes' => 77],
        ];

        foreach ($issues2 as $i) {
            ProductionIssues::create([
                'production_reports_id' => $report2->id,
                'maintenances_id' => $i['maintenances_id'],
                'remarks' => $i['remarks'],
                'minutes' => $i['minutes'],
            ]);
        }

                // QC Rejects
        $rejects2 = [
            ['defects_id' => 1, 'quantity' => 63],
            ['defects_id' => 2, 'quantity' => 85],
            ['defects_id' => 3, 'quantity' => 36],
            ['defects_id' => 4, 'quantity' => 22],
            ['defects_id' => 34, 'quantity' => 15],
            ['defects_id' => 20, 'quantity' => 11]
        ];

        foreach ($rejects2 as $r) {
            LineQcReject::create([
                'production_reports_id' => $report2->id,
                'defects_id' => $r['defects_id'],
                'quantity' => $r['quantity'],
            ]);
        }
    }
}
