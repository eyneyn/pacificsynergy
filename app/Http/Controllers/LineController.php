<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductionReport;
use App\Models\LineQcReject;
use App\Models\Defect;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Maintenance;
use App\Models\ProductionIssues;

class LineController extends Controller
{
    /**
     * Display the main analytics index for a line and year.
     */
    public function index(Request $request)
    {
        $line = $request->query('line');
        $year = $request->query('date', now()->year);

        // Get available years from production reports
        $availableYears = ProductionReport::selectRaw('YEAR(production_date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->toArray();

        // Get reports for the selected year and line
        $reports = ProductionReport::with(['standard', 'lineQcRejects.defect', 'statuses'])
            ->whereYear('production_date', $year)
            ->when($line, fn($q) => $q->where('line', $line))
            ->whereHas('statuses', fn($q) => $q->where('status', 'Validated'))
            ->get();

        return view('analytics.line.index', [
            'line' => $line,
            'year' => $year,
            'reports' => $reports,
            'availableYears' => $availableYears
        ]);
    }

    /**
     * Display the monthly report for a line, month, and year.
     */
    public function monthly_report(Request $request)
    {
        $month = $request->query('month', now()->format('F')); // e.g. January
        $monthNumber = Carbon::parse($month)->month;
        $line = $request->query('line');
        $year = $request->query('date', now()->year);

        // Get production reports for the selected month/year/line
        $reports = ProductionReport::with(['standard', 'lineQcRejects.defect', 'statuses'])
            ->whereMonth('production_date', $monthNumber)
            ->whereYear('production_date', $year)
            ->when($line, fn($q) => $q->where('line', $line))
            ->whereHas('statuses', fn($q) => $q->where('status', 'Validated'))
            ->orderBy('production_date')
            ->get();

        $reportIds = $reports->pluck('id');

        // Get all OPL Maintenance categories (fixed columns)
        $oplCategories = Maintenance::where('type', 'OPL')
            ->orderBy('name')
            ->pluck('name')
            ->toArray();

        // Load OPL-type production issues
        $oplIssues = ProductionIssues::with('maintenance', 'productionReport')
            ->whereIn('production_reports_id', $reportIds)
            ->whereHas('maintenance', fn($q) => $q->where('type', 'OPL'))
            ->get();

        // Group OPL issues by date and category
        $oplDowntimes = [];
        foreach ($oplIssues as $issue) {
            $date = Carbon::parse($issue->productionReport->production_date)->format('Y-m-d');
            $category = $issue->maintenance->name;
            $oplDowntimes[$date][$category] = ($oplDowntimes[$date][$category] ?? 0) + $issue->minutes;
        }

        // Normalize OPL data for Blade view
        $oplData = [];
        foreach ($oplDowntimes as $date => $categories) {
            $entry = ['date' => $date, 'categories' => []];
            foreach ($oplCategories as $cat) {
                $entry['categories'][$cat] = $categories[$cat] ?? 0;
            }
            $oplData[] = $entry;
        }

        // Get all EPL Maintenance categories (fixed columns)
        $eplCategories = Maintenance::where('type', 'EPL')
            ->orderBy('name')
            ->pluck('name')
            ->toArray();

        // Load EPL-type production issues
        $eplIssues = ProductionIssues::with('maintenance', 'productionReport')
            ->whereIn('production_reports_id', $reportIds)
            ->whereHas('maintenance', fn($q) => $q->where('type', 'EPL'))
            ->get();

        // Group EPL issues by date and category
        $eplDowntimes = [];
        foreach ($eplIssues as $issue) {
            $date = Carbon::parse($issue->productionReport->production_date)->format('Y-m-d');
            $category = $issue->maintenance->name;
            $eplDowntimes[$date][$category] = ($eplDowntimes[$date][$category] ?? 0) + $issue->minutes;
        }

        // Normalize EPL data for Blade view
        $eplData = [];
        foreach ($eplDowntimes as $date => $categories) {
            $entry = ['date' => $date, 'categories' => []];
            foreach ($eplCategories as $cat) {
                $entry['categories'][$cat] = $categories[$cat] ?? 0;
            }
            $eplData[] = $entry;
        }

// --- Initialize Variables ---
$totalDt = $totalOpl = $totalEpl = $totalMOP = 0;
$ptdOplImpact = $ptdEplImpact = 0;
$ptdImpactCount = 0;
$weeklySummary = [];
$weeklyMinutes = [];
$dailyRows = [];

// Loop through each report
foreach ($reports as $report) {
    $date = Carbon::parse($report->production_date)->format('Y-m-d');
    $le = $report->line_efficiency ?? 0;
    $dt = 100 - $le;

    $oplMins = collect($oplData)->firstWhere('date', $date)['categories'] ?? [];
    $eplMins = collect($eplData)->firstWhere('date', $date)['categories'] ?? [];

    $oplSum = array_sum($oplMins);
    $eplSum = array_sum($eplMins);
    $totalMins = $oplSum + $eplSum;

    $oplPercent = $totalMins > 0 ? ($oplSum / $totalMins) * $dt : 0;
    $eplPercent = $totalMins > 0 ? ($eplSum / $totalMins) * $dt : 0;

    // Store daily row data
    $dailyRows[] = [
        'date' => Carbon::parse($report->production_date)->format('n/j/y'),
        'sku' => $report->standard->description ?? 'N/A',
        'size' => $report->standard->size ?? '',
        'target_le' => '80%',
        'le' => number_format($le, 2) . '%',
        'opl_percent' => number_format($oplPercent, 2) . '%',
        'epl_percent' => number_format($eplPercent, 2) . '%',
        'opl_mins' => round($oplSum),
        'epl_mins' => round($eplSum),
        'total_mins' => round($totalMins),
        'dt' => round($dt) . '%',
    ];

    $totalDt += $dt;
    $totalOpl += $oplSum;
    $totalEpl += $eplSum;
    $totalMOP += ($oplSum + $eplSum);

    if ($totalMins > 0) {
        $ptdOplImpact += $oplPercent;
        $ptdEplImpact += $eplPercent;
        $ptdImpactCount++;
    }

    $week = 'W' . Carbon::parse($report->production_date)->weekOfMonth;

    // Weekly Summary
    $weeklySummary[$week]['le_total'] = ($weeklySummary[$week]['le_total'] ?? 0) + $le;
    $weeklySummary[$week]['opl_total'] = ($weeklySummary[$week]['opl_total'] ?? 0) + $report->opl_percent ?? 0;
    $weeklySummary[$week]['epl_total'] = ($weeklySummary[$week]['epl_total'] ?? 0) + $report->epl_percent ?? 0;
    $weeklySummary[$week]['count'] = ($weeklySummary[$week]['count'] ?? 0) + 1;

    // Weekly Minutes
    $weeklyMinutes[$week]['dt_mins'] = ($weeklyMinutes[$week]['dt_mins'] ?? 0) + $totalMins;
    $weeklyMinutes[$week]['opl_mins'] = ($weeklyMinutes[$week]['opl_mins'] ?? 0) + $oplSum;
    $weeklyMinutes[$week]['epl_mins'] = ($weeklyMinutes[$week]['epl_mins'] ?? 0) + $eplSum;
    $weeklyMinutes[$week]['dt_percent_total'] = ($weeklyMinutes[$week]['dt_percent_total'] ?? 0) + $dt;
    $weeklyMinutes[$week]['opl_impact'] = ($weeklyMinutes[$week]['opl_impact'] ?? 0) + $oplPercent;
    $weeklyMinutes[$week]['epl_impact'] = ($weeklyMinutes[$week]['epl_impact'] ?? 0) + $eplPercent;
    $weeklyMinutes[$week]['count'] = ($weeklyMinutes[$week]['count'] ?? 0) + 1;
}

// PTD Calculations
$ptdLEFloat = $reports->avg('line_efficiency');
$ptdLEP = 100 - $ptdLEFloat;
$ptdLE = $ptdLEFloat ? number_format($ptdLEFloat, 2) . '%' : '0.00%';
$ptdOPL = $totalMOP > 0 ? number_format($totalOpl / $totalMOP * $ptdLEP, 2) . '%' : '0.00%';
$ptdEPL = $totalMOP > 0 ? number_format($totalEpl / $totalMOP * $ptdLEP, 2) . '%' : '0.00%';

// Weekly Rows
$weeklyRows = [];
$finalRows = [];
for ($i = 1; $i <= 5; $i++) {
    $weekKey = 'W' . $i;
    $summary = $weeklySummary[$weekKey] ?? ['count' => 0];
    $minutes = $weeklyMinutes[$weekKey] ?? ['count' => 0];

    // Left table (percent summary)
    $finalRows[] = [
        $weekKey,
        $summary['count'] ? number_format($summary['le_total'] / $summary['count'], 2) . '%' : '0%',
        $summary['count'] ? number_format($summary['opl_total'] / $summary['count'], 2) . '%' : '0%',
        $summary['count'] ? number_format($summary['epl_total'] / $summary['count'], 2) . '%' : '0%',
    ];

    // Right table (minutes summary)
    $weeklyRows[] = [
        'week' => $weekKey,
        'dt' => number_format($minutes['dt_mins'] ?? 0),
        'opl' => number_format($minutes['opl_mins'] ?? 0),
        'epl' => number_format($minutes['epl_mins'] ?? 0),
        'dt_percent' => $minutes['count'] ? round($minutes['dt_percent_total'] / $minutes['count']) . '%' : '0%',
        'opl_percent' => $minutes['count'] ? round($minutes['opl_impact'] / $minutes['count']) . '%' : '0%',
        'epl_percent' => $minutes['count'] ? round($minutes['epl_impact'] / $minutes['count']) . '%' : '0%',
    ];
}


return view('analytics.line.monthly_report', compact(
    'reports', 'month', 'line', 'year',
    'oplCategories', 'oplData',
    'eplCategories', 'eplData',
    'dailyRows', 'finalRows', 'weeklyRows',
    'ptdLE', 'ptdOPL', 'ptdEPL',
    'totalDt', 'totalOpl', 'totalEpl'
));

    }
}
