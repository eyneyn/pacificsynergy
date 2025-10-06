<?php

namespace App\Observers;

use App\Models\Status;
use App\Models\MaterialUtilizationAnalytics;
use App\Models\Notification;

class StatusObserver
{
    /**
     * Prevent duplicate statuses before saving
     */
    public function creating(Status $status): void
    {
        $exists = Status::where('production_report_id', $status->production_report_id)
            ->where('status', $status->status)
            ->exists();

        if ($exists) {
            // Stop duplicate insertion
            throw new \Exception("Duplicate status '{$status->status}' already exists for this report.");
        }
    }
    /**
    * After a Status has been created → trigger notifications + analytics
    */
    public function created(Status $status): void
    {
        $sku      = $status->productionReport?->standard?->description ?? 'No Report';
        $line     = $status->productionReport?->line ?? 'Unknown';
        $userName = trim(($status->user?->first_name ?? '') . ' ' . ($status->user?->last_name ?? ''));

        $skuTag   = "<span style=\"color:#23527c;font-weight:bold;\">{$sku}</span>";
        $lineTag  = "<span style=\"color:#23527c;font-weight:bold;\">Line {$line}</span>";
        $userTag  = "<span style=\"color:#23527c;font-weight:bold;\">{$userName}</span>";

        if ($status->status === 'Submitted') {
            $message = "{$skuTag} | {$lineTag} was submitted by {$userTag}.";
        } elseif ($status->status === 'Validated') {
            $message = "{$skuTag} | {$lineTag} was validated by {$userTag}.";
        } else {
            $message = "{$skuTag} | {$lineTag} has status <strong>{$status->status}</strong> by {$userTag}.";
        }
        // Broadcast Submitted + Validated
        if ($status->status === 'Submitted') {
            Notification::create([
                'user_id'              => null,
                'type'                 => 'report_submitted', // 👈 match type
                'production_report_id' => $status->production_report_id,
                'message'              => $message,
                'required_permission'  => 'report.index',
                'is_read'              => false,
            ]);
        } elseif ($status->status === 'Validated') {
            Notification::create([
                'user_id'              => null,
                'type'                 => 'report_validate', // 👈 match type
                'production_report_id' => $status->production_report_id,
                'message'              => $message,
                'required_permission'  => 'report.index',
                'is_read'              => false,
            ]);
        } else {
            // User-specific
            Notification::create([
                'user_id'              => $status->user_id,
                'type'                 => 'status',
                'production_report_id' => $status->production_report_id,
                'message'              => $message,
                'is_read'              => false,
            ]);
        }
    }
    /**
     * After a Status has been saved → trigger analytics
     */
    public function saved(Status $status): void
    {
        // only if new status = Validated
        if ($status->status !== 'Validated') {
            return;
        }

        $report = $status->productionReport;
        if (!$report) {
            return;
        }

        $sku   = $report->standard->description ?? 'No Run';
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

        // ✅ Insert/Update analytics row
        MaterialUtilizationAnalytics::updateOrCreate(
            ['production_report_id' => $report->id],
            [
                'production_report_id' => $report->id,
                'line'            => $report->line,
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
    }
}
