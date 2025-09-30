<?php

namespace App\Observers;

use App\Models\ProductionReport;
use App\Models\MaterialUtilizationAnalytics;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class ProductionReportObserver
{
    /**
     * Handle the ProductionReport "updated" event.
     */
    public function updated(ProductionReport $report): void
    {
        $latestStatus = $report->statuses()->latest()->first();
        if (!$latestStatus || $latestStatus->status !== 'Validated') {
            return; // skip if not validated
        }

        $sku   = $report->standard->description ?? 'No Run';
        $line  = $report->line;
        $isNoRun = strcasecmp(trim($sku), 'No Run') === 0;

        $bottlesPerCase = $isNoRun ? 0 : (int) ($report->standard->bottles_per_case ?? 0);
        $output         = $isNoRun ? 0 : (int) ($report->total_outputCase ?? 0);

        $fgBottles = (!$isNoRun && $output && $bottlesPerCase) ? ($output * $bottlesPerCase) : 0;
        $fgCases   = (!$isNoRun) ? $output : 0;

        $preformRejects = (int) ($report->preform_blow_molding ?? 0);
        $capsRejects    = (int) ($report->caps_filling ?? 0);
        $labelRejects   = (int) ($report->opp_labels ?? 0);
        $ldpeRejects    = (int) ($report->shrinkfilm ?? 0);

        $qaPreform = (int) ($report->total_sample ?? 0);
        $qaCaps    = (int) ($report->total_sample ?? 0);
        $qaLabel   = (int) ($report->with_label ?? 0);
        $qaLdpe    = 0;

        $calcPercent = function ($rej, $fg, $qa, $isNoRun) {
            if ($isNoRun) return 0;
            $den = $fg + $rej + $qa;
            return $den > 0 ? round(($rej / $den) * 100, 2) : 0;
        };

        $preformPercent = $calcPercent($preformRejects, $fgBottles, $qaPreform, $isNoRun);
        $capsPercent    = $calcPercent($capsRejects,    $fgBottles, $qaCaps,    $isNoRun);
        $labelPercent   = $calcPercent($labelRejects,   $fgBottles, $qaLabel,   $isNoRun);
        $ldpePercent    = $calcPercent($ldpeRejects,    $fgCases,   $qaLdpe,    $isNoRun);

        // 🔄 Sync with analytics table
        $analytics = MaterialUtilizationAnalytics::updateOrCreate(
            ['production_report_id' => $report->id],
            [
                'production_report_id' => $report->id,
                'line'            => $line,
                'production_date' => $report->production_date,
                'sku'             => $sku,
                'bottlePerCase'   => $bottlesPerCase,
                'targetMaterialEfficiency' => 1.00,
                'total_output'    => $output,

                'preformDesc'  => $report->standard->preform_weight ?? 0,
                'preform_fg'   => $fgBottles,
                'preform_rej'  => $preformRejects,
                'preform_qa'   => $qaPreform,
                'preform_pct'  => $preformPercent,

                'capsDesc'     => $report->standard->caps ?? 0,
                'caps_fg'      => $fgBottles,
                'caps_rej'     => $capsRejects,
                'caps_qa'      => $qaCaps,
                'caps_pct'     => $capsPercent,

                'labelDesc'    => $report->standard->opp_label ?? 0,
                'label_fg'     => $fgBottles,
                'label_rej'    => $labelRejects,
                'label_qa'     => $qaLabel,
                'label_pct'    => $labelPercent,

                'ldpeDesc'     => $report->standard->ldpe_size ?? 0,
                'ldpe_fg'      => $fgCases,
                'ldpe_rej'     => $ldpeRejects,
                'ldpe_qa'      => $qaLdpe,
                'ldpe_pct'     => $ldpePercent,
            ]
        );
        
        $categories = [
            'Preform' => $preformPercent,
            'Caps'    => $capsPercent,
            'Label'   => $labelPercent,
            'LDPE'    => $ldpePercent,
        ];

        $thresholds = [
            'Preform' => 1.00,
            'Caps'    => 1.00,
            'Label'   => 1.00,
            'LDPE'    => 0.50, // adjust as needed
        ];

        foreach ($categories as $cat => $pct) {
            $limit = $thresholds[$cat] ?? 1.00;

            if ($pct > $limit) {
                Notification::create([
                    'user_id'              => null,
                    'type'                 => 'analytics_warning',
                    'production_report_id' => $report->id,
                    'message'              => "WARNING: {$sku} | Line {$line} → {$cat} % Rejects exceeded limit ({$pct}%).",
                    'is_read'              => false,
                    'required_permission'  => 'analytics.index',
                ]);
            }
        }
    }

    public function deleted(ProductionReport $report): void
    {
        // cleanup analytics if report deleted
        MaterialUtilizationAnalytics::where('production_report_id', $report->id)->delete();
    }
}
