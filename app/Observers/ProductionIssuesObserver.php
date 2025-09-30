<?php

namespace App\Observers;

use App\Models\ProductionIssues;
use App\Models\LineEfficiencyAnalytics;

class ProductionIssuesObserver
{
    /**
     * Handle create & update
     */
    public function saved(ProductionIssues $issue): void
    {
        $report = $issue->productionReport;
        if (!$report) {
            \Log::warning("No productionReport for issue {$issue->id}");
            return;
        }

        $latestStatus = $report->statuses()->latest()->first();
        if (!$latestStatus || $latestStatus->status !== 'Validated') {
            return;
        }

        if (!$issue->maintenance) {
            \Log::warning("No maintenance for issue {$issue->id}");
            return;
        }

        $sku   = $report->standard->description ?? 'No Run';
        $bpc   = $report->standard->bottles_per_case ?? 0;
        $le    = is_numeric($report->line_efficiency)
            ? (float) $report->line_efficiency
            : (float) preg_replace('/[^0-9.\-]/', '', (string) $report->line_efficiency);

        LineEfficiencyAnalytics::updateOrCreate(
            [
                'production_report_id' => $report->id,
                'downtime_type'        => $issue->maintenance->type,  // OPL/EPL
                'category'             => $issue->maintenance->name,
            ],
            [
                'line'            => $report->line,
                'production_date' => $report->production_date,
                'sku'             => $sku,
                'bottlesPerCase'  => $bpc,
                'line_efficiency' => $le,
                'minutes'         => $issue->minutes,
            ]
        );
    }
    /**
     * Cleanup if deleted
     */
    public function deleted(ProductionIssues $issue): void
    {
        $reportId = $issue->production_reports_id;
        if (!$reportId || !$issue->maintenance) return;

        LineEfficiencyAnalytics::where([
            'production_report_id' => $reportId,
            'downtime_type'        => $issue->maintenance->type,
            'category'             => $issue->maintenance->name,
        ])->delete();
    }
}
