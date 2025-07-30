<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductionReport;
use App\Models\LineQcReject;
use App\Models\Line;
use App\Models\Defect;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MaterialController extends Controller
{
public function index(Request $request)
{
    $line = $request->query('line');
    $year = $request->query('date');

    $availableYears = ProductionReport::selectRaw('YEAR(production_date) as year')
        ->distinct()
        ->orderByDesc('year')
        ->pluck('year')
        ->toArray();

    $activeLines = ProductionReport::select('line as line_number')
        ->distinct()
        ->orderBy('line')
        ->get();

    // 👇 Auto-pick defaults if not provided
    $year = $year ?? now()->year;
    $line = $line ?? ($activeLines->first()->line_number ?? null);

    $reports = ProductionReport::with(['standard', 'lineQcRejects.defect', 'statuses'])
        ->when($year, fn ($q) => $q->whereYear('production_date', $year))
        ->when($line, fn ($q) => $q->where('line', $line))
        ->whereHas('statuses', fn ($q) => $q->where('status', 'Validated'))
        ->get();

    $monthlyData = $this->computeProductionData($reports);

    return view('analytics.material.index', array_merge([
        'selectedLine' => $line,
        'year' => $year,
        'reports' => $reports,
        'availableYears' => $availableYears,
        'activeLines' => $activeLines,
    ], $monthlyData));
}



public function monthly_report(Request $request)
{
    $month = $request->query('month', now()->format('F')); // e.g. January
    $monthNumber = Carbon::parse($month)->month;

    $line = $request->query('line');
    $year = $request->query('date', now()->year); // 👈 accept year from query

    $reports = ProductionReport::with(['standard', 'lineQcRejects.defect', 'statuses'])
        ->whereMonth('production_date', $monthNumber)
        ->whereYear('production_date', $year) // ✅ filter by year
        ->when($line, fn ($q) => $q->where('line', $line)) // ✅ optional line filter
        ->whereHas('statuses', fn ($q) => $q->where('status', 'Validated'))
        ->orderBy('production_date')
        ->get();

// Weekly Grouping
$groupedWeeks = $reports->groupBy(function ($report) {
    return 'Week ' . Carbon::parse($report->production_date)->weekOfMonth;
});

// Weekly Report Setup
$rawWeeklyData = [];
foreach ($groupedWeeks as $weekKey => $group) {
    $outputSum = $group->sum('total_outputCase');
    $bottleSum = $group->sum(fn($r) => ($r->standard?->bottles_per_case ?? 0) * ($r->total_outputCase ?? 0));

    // Sum QA Samples by category
    $qaSampleBottle = $group->sum('total_sample');
    $qaSampleCaps   = $group->sum('total_sample'); // same as bottles
    $qaSampleLabel  = $group->sum('with_label');

    // Rejects grouping
    $rejects = ['Bottle' => 0, 'Caps' => 0, 'Label' => 0, 'LDPE Shrinkfilm' => 0];
    foreach ($group as $report) {
        $qc = $report->lineQcRejects->groupBy(fn($r) => $r->defect->category);
        foreach ($rejects as $cat => $_) {
            $rejects[$cat] += ($qc[$cat] ?? collect())->sum('quantity');
        }
    }

    $calcPercent = fn($rej, $usage, $qa) =>
        ($rej + $usage + $qa) > 0 ? number_format(($rej / ($rej + $usage + $qa)) * 100, 2) . '%' : '0.00%';

    $rawWeeklyData[$weekKey] = [
        'output' => $outputSum,
        'preform' => [
            'fg' => $bottleSum,
            'rej' => $rejects['Bottle'],
            'qa' => $qaSampleBottle,
            'percent' => $calcPercent($rejects['Bottle'], $bottleSum, $qaSampleBottle),
        ],
        'caps' => [
            'fg' => $bottleSum,
            'rej' => $rejects['Caps'],
            'qa' => $qaSampleCaps,
            'percent' => $calcPercent($rejects['Caps'], $bottleSum, $qaSampleCaps),
        ],
        'label' => [
            'fg' => $bottleSum,
            'rej' => $rejects['Label'],
            'qa' => $qaSampleLabel,
            'percent' => $calcPercent($rejects['Label'], $bottleSum, $qaSampleLabel),
        ],
        'ldpe' => [
            'fg' => $outputSum,
            'rej' => $rejects['LDPE Shrinkfilm'],
            'qa' => 0,
            'percent' => $calcPercent($rejects['LDPE Shrinkfilm'], $outputSum, 0),
        ],
    ];
}

// Reformat to fixed Week 1–5 format
$weeklyData = [];
for ($i = 1; $i <= 5; $i++) {
    $key = "Week $i";
    $weeklyData[] = [
        'week' => $key,
        'output' => $rawWeeklyData[$key]['output'] ?? 0,
        'preform' => $rawWeeklyData[$key]['preform'] ?? ['fg' => 0, 'rej' => 0, 'qa' => 0, 'percent' => '0.00%'],
        'caps' => $rawWeeklyData[$key]['caps'] ?? ['fg' => 0, 'rej' => 0, 'qa' => 0, 'percent' => '0.00%'],
        'label' => $rawWeeklyData[$key]['label'] ?? ['fg' => 0, 'rej' => 0, 'qa' => 0, 'percent' => '0.00%'],
        'ldpe' => $rawWeeklyData[$key]['ldpe'] ?? ['fg' => 0, 'rej' => 0, 'qa' => 0, 'percent' => '0.00%'],
    ];
}


    // Daily Chart Data
    $daysInMonth = Carbon::createFromDate(null, $monthNumber, 1)->daysInMonth;
    $dailyLabels = range(1, $daysInMonth);
    $dailyTarget = array_fill(0, $daysInMonth, 0.01); // Constant target efficiency

    $dailyPreform = $dailyCaps = $dailyLabel = $dailyLdpe = [];

    for ($day = 1; $day <= $daysInMonth; $day++) {
        $report = $reports->firstWhere('production_date', function ($date) use ($day, $monthNumber) {
            return Carbon::parse($date)->day === $day;
        });

        if ($report) {
            $output = $report->total_outputCase ?? 0;
            $bottle = ($report->standard?->bottles_per_case ?? 0) * $output;

            $qc = $report->lineQcRejects->groupBy(fn($r) => $r->defect->category);
            $qa = 0;

            $calcRatio = fn($rej, $usage, $qa) =>
                ($rej + $usage + $qa) > 0 ? round($rej / ($rej + $usage + $qa), 4) : 0;

            $dailyPreform[] = $calcRatio(($qc['Bottle'] ?? collect())->sum('quantity'), $bottle, $qa);
            $dailyCaps[] = $calcRatio(($qc['Caps'] ?? collect())->sum('quantity'), $bottle, $qa);
            $dailyLabel[] = $calcRatio(($qc['Label'] ?? collect())->sum('quantity'), $bottle, $qa);
            $dailyLdpe[] = $calcRatio(($qc['LDPE Shrinkfilm'] ?? collect())->sum('quantity'), $output, $qa);
        } else {
            $dailyPreform[] = 0;
            $dailyCaps[] = 0;
            $dailyLabel[] = 0;
            $dailyLdpe[] = 0;
        }
    }

    return view('analytics.material.monthly_report', compact(
        'reports', 'weeklyData', 'month', 'line', 'year',
        'dailyLabels', 'dailyTarget', 'dailyPreform', 'dailyCaps', 'dailyLabel', 'dailyLdpe'
    ));
}
 private function computeProductionData($reports)
    {
        $categories = ['Bottle' => 'preform', 'Caps' => 'caps', 'Label' => 'opp', 'LDPE Shrinkfilm' => 'ldpe'];
        $results = [];

        foreach ($categories as $category => $key) {
            $results["{$key}FgUsage"] = array_fill(1, 12, 0);
            $results["{$key}Rejects"] = array_fill(1, 12, 0);
            $results["{$key}QaSamples"] = array_fill(1, 12, 0);
            $results["{$key}RejectRates"] = array_fill(1, 12, '0.00%');
        }

        $monthlyProduction = array_fill(1, 12, 0);

        foreach ($reports as $report) {
            $month = Carbon::parse($report->production_date)->month;
            $output = $report->total_outputCase ?? 0;
            $bpc = $report->standard->bottles_per_case ?? 0;
            $fg = $output * $bpc;
            $qa = intval($report->with_label ?? 0) + intval($report->without_label ?? 0);
            $grouped = $report->lineQcRejects->groupBy(fn($r) => $r->defect->category);

            $monthlyProduction[$month] += $output;

foreach ($categories as $category => $key) {
    $rej = ($grouped[$category] ?? collect())->sum('quantity');
    $results["{$key}FgUsage"][$month] += $key === 'ldpe' ? $output : $fg;
    $results["{$key}Rejects"][$month] += $rej;

    // Correct QA assignment by category
    if (in_array($key, ['preform', 'caps'])) {
        $results["{$key}QaSamples"][$month] += intval($report->total_sample ?? 0);
    } elseif ($key === 'opp') {
        $results["{$key}QaSamples"][$month] += intval($report->with_label ?? 0);
    } else {
        $results["{$key}QaSamples"][$month] += 0; // LDPE
    }
}

        }

        foreach (range(1, 12) as $month) {
            foreach ($categories as $key => $prefix) {
                $rej = $results["{$prefix}Rejects"][$month];
                $qa = $results["{$prefix}QaSamples"][$month];
                $fg = $results["{$prefix}FgUsage"][$month];
                $total = $rej + $qa + $fg;
                $results["{$prefix}RejectRates"][$month] = $total > 0 ? number_format(($rej / $total) * 100, 2) . '%' : '0.00%';
            }
        }

        // Quarterly breakdowns
        foreach ($categories as $prefix) {
            $results["{$prefix}QuarterFgUsage"] = [
                'Q1' => $results["{$prefix}FgUsage"][1] + $results["{$prefix}FgUsage"][2] + $results["{$prefix}FgUsage"][3],
                'Q2' => $results["{$prefix}FgUsage"][4] + $results["{$prefix}FgUsage"][5] + $results["{$prefix}FgUsage"][6],
                'Q3' => $results["{$prefix}FgUsage"][7] + $results["{$prefix}FgUsage"][8] + $results["{$prefix}FgUsage"][9],
                'Q4' => $results["{$prefix}FgUsage"][10] + $results["{$prefix}FgUsage"][11] + $results["{$prefix}FgUsage"][12],
            ];

            $results["{$prefix}QuarterRejects"] = [
                'Q1' => $results["{$prefix}Rejects"][1] + $results["{$prefix}Rejects"][2] + $results["{$prefix}Rejects"][3],
                'Q2' => $results["{$prefix}Rejects"][4] + $results["{$prefix}Rejects"][5] + $results["{$prefix}Rejects"][6],
                'Q3' => $results["{$prefix}Rejects"][7] + $results["{$prefix}Rejects"][8] + $results["{$prefix}Rejects"][9],
                'Q4' => $results["{$prefix}Rejects"][10] + $results["{$prefix}Rejects"][11] + $results["{$prefix}Rejects"][12],
            ];

            $results["{$prefix}QuarterQaSamples"] = [
                'Q1' => $results["{$prefix}QaSamples"][1] + $results["{$prefix}QaSamples"][2] + $results["{$prefix}QaSamples"][3],
                'Q2' => $results["{$prefix}QaSamples"][4] + $results["{$prefix}QaSamples"][5] + $results["{$prefix}QaSamples"][6],
                'Q3' => $results["{$prefix}QaSamples"][7] + $results["{$prefix}QaSamples"][8] + $results["{$prefix}QaSamples"][9],
                'Q4' => $results["{$prefix}QaSamples"][10] + $results["{$prefix}QaSamples"][11] + $results["{$prefix}QaSamples"][12],
            ];

            $results["{$prefix}QuarterRejectRates"] = [];
            foreach (['Q1', 'Q2', 'Q3', 'Q4'] as $q) {
                $rej = $results["{$prefix}QuarterRejects"][$q];
                $qa = $results["{$prefix}QuarterQaSamples"][$q];
                $fg = $results["{$prefix}QuarterFgUsage"][$q];
                $total = $rej + $qa + $fg;
                $results["{$prefix}QuarterRejectRates"][$q] = $total > 0 ? number_format(($rej / $total) * 100, 2) . '%' : '0.00%';
            }

            $results["{$prefix}TotalFg"] = array_sum($results["{$prefix}QuarterFgUsage"]);
            $results["{$prefix}TotalRej"] = array_sum($results["{$prefix}QuarterRejects"]);
            $results["{$prefix}TotalQa"] = array_sum($results["{$prefix}QuarterQaSamples"]);
            $total = $results["{$prefix}TotalFg"] + $results["{$prefix}TotalRej"] + $results["{$prefix}TotalQa"];
            $results["{$prefix}TotalRate"] = $total > 0 ? number_format(($results["{$prefix}TotalRej"] / $total) * 100, 2) . '%' : '0.00%';
        }

        $rateAvg = (
    floatval($results['preformTotalRate']) +
    floatval($results['capsTotalRate']) +
    floatval($results['oppTotalRate']) +
    floatval($results['ldpeTotalRate'])
) / 4;

$results['materialEfficiencyRate'] = number_format($rateAvg, 2) . '%';

        return array_merge([
            'monthlyProduction' => $monthlyProduction,
'monthlyFgUsage' => $results['preformFgUsage'],
'monthlyRejects' => $results['preformRejects'],
'monthlyQaSamples' => $results['preformQaSamples'],
'monthlyRejectRates' => $results['preformRejectRates'],
            'quarterlyProduction' => [
                'Q1' => $monthlyProduction[1] + $monthlyProduction[2] + $monthlyProduction[3],
                'Q2' => $monthlyProduction[4] + $monthlyProduction[5] + $monthlyProduction[6],
                'Q3' => $monthlyProduction[7] + $monthlyProduction[8] + $monthlyProduction[9],
                'Q4' => $monthlyProduction[10] + $monthlyProduction[11] + $monthlyProduction[12],
            ],
            'totalAnnualProduction' => array_sum($monthlyProduction),
    'preformTotalRate' => $results['preformTotalRate'],
    'capsTotalRate' => $results['capsTotalRate'],
    'oppTotalRate' => $results['oppTotalRate'],
    'ldpeTotalRate' => $results['ldpeTotalRate'],
    'materialEfficiencyRate' => $results['materialEfficiencyRate'],
], $results);
    }
public function material_utilization(Request $request)
{
    $availableYears = ProductionReport::selectRaw('YEAR(production_date) as year')
        ->distinct()
        ->orderByDesc('year')
        ->pluck('year')
        ->toArray();

    $lines = \App\Models\Line::pluck('line_number')->toArray();

    $year = $request->query('date') ?? ($availableYears[0] ?? now()->year);
    $line = $request->query('line') ?? ($lines[0] ?? null);

    $reports = ProductionReport::with(['standard', 'lineQcRejects.defect', 'statuses'])
        ->whereYear('production_date', $year)
        ->whereHas('statuses', fn($q) => $q->where('status', 'Validated'))
        ->get();

    $monthlyData = $this->computeProductionData($reports);

    return view('analytics.material_utilization', array_merge([
        'line' => $line,
        'lines' => $lines,
        'year' => $year,
        'availableYears' => $availableYears,
        'reports' => $reports,
    ], $monthlyData));
}




}
