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
use App\Exports\LineEfficiencyExport;
use Maatwebsite\Excel\Facades\Excel;

class LineController extends Controller
{
    /**
     * Display the main analytics index for a line and year.
     */
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



    return view('analytics.line.index', array_merge([
        'selectedLine' => $line,
        'year' => $year,
        'availableYears' => $availableYears,
        'activeLines' => $activeLines,
    ]));
    }

    /**
     * Display the monthly report for a line, month, and year.
     */
public function monthly_report(Request $request)
{
    $month = $request->query('month', now()->format('F'));
    $monthNumber = Carbon::parse($month)->month;
    $line = $request->query('line');
    $year = $request->query('date', now()->year);

    // ✅ Get reports
    $reports = ProductionReport::with(['standard', 'productionIssues.maintenance', 'statuses'])
        ->whereMonth('production_date', $monthNumber)
        ->whereYear('production_date', $year)
        ->when($line, fn($q) => $q->where('line', $line))
        ->whereHas('statuses', fn($q) => $q->where('status', 'Validated'))
        ->orderBy('production_date')
        ->get();

    $reportIds = $reports->pluck('id');

    // ✅ Categories
    $oplCategories = Maintenance::where('type', 'OPL')->orderBy('name')->pluck('name')->toArray();
    $eplCategories = Maintenance::where('type', 'EPL')->orderBy('name')->pluck('name')->toArray();

    // ✅ Issues
    $oplIssues = ProductionIssues::with(['maintenance', 'productionReport'])
        ->whereIn('production_reports_id', $reportIds)
        ->whereHas('maintenance', fn($q) => $q->where('type', 'OPL'))
        ->get();

    $eplIssues = ProductionIssues::with(['maintenance', 'productionReport'])
        ->whereIn('production_reports_id', $reportIds)
        ->whereHas('maintenance', fn($q) => $q->where('type', 'EPL'))
        ->get();

    // ✅ Group by date
    $oplDowntimes = [];
    foreach ($oplIssues as $issue) {
        $date = $issue->productionReport->production_date->format('Y-m-d');
        $cat = $issue->maintenance->name;
        $oplDowntimes[$date][$cat] = ($oplDowntimes[$date][$cat] ?? 0) + $issue->minutes;
    }

    $eplDowntimes = [];
    foreach ($eplIssues as $issue) {
        $date = $issue->productionReport->production_date->format('Y-m-d');
        $cat = $issue->maintenance->name;
        $eplDowntimes[$date][$cat] = ($eplDowntimes[$date][$cat] ?? 0) + $issue->minutes;
    }

    // ✅ Normalize to view format
    $oplData = [];
    foreach ($oplDowntimes as $date => $categories) {
        $oplData[] = [
            'date' => $date,
            'categories' => collect($oplCategories)->mapWithKeys(fn($cat) => [$cat => $categories[$cat] ?? 0])->toArray()
        ];
    }

    $eplData = [];
    foreach ($eplDowntimes as $date => $categories) {
        $eplData[] = [
            'date' => $date,
            'categories' => collect($eplCategories)->mapWithKeys(fn($cat) => [$cat => $categories[$cat] ?? 0])->toArray()
        ];
    }

    // ✅ Build Daily Rows (for left + right table)
    $dailyRows = [];
    foreach ($reports as $r) {
        $oplMins = $oplIssues->where('production_reports_id', $r->id)->sum('minutes');
        $eplMins = $eplIssues->where('production_reports_id', $r->id)->sum('minutes');
        $totalMins = $oplMins + $eplMins;

        $dailyRows[] = [
            'date' => $r->production_date->format('Y-m-d'),
            'sku' => $r->standard->sku ?? '',
            'size' => $r->standard->size ?? '',
            'target_le' => $r->standard->target_le ?? 0,
            'le' => $r->line_efficiency ?? 0,
            'opl_percent' => $totalMins > 0 ? round(($oplMins / $totalMins) * 100, 2) : 0,
            'epl_percent' => $totalMins > 0 ? round(($eplMins / $totalMins) * 100, 2) : 0,
            'total_mins' => $totalMins,
            'opl_mins' => $oplMins,
            'epl_mins' => $eplMins,
            'dt' => $totalMins > 0 ? 100 : 0,
        ];
    }

    // ✅ Weekly aggregation
    $weeklyRows = [];
    $finalRows = [];
    $weekGroups = $dailyRows ? collect($dailyRows)->groupBy(fn($r) => Carbon::parse($r['date'])->weekOfMonth) : collect();

    foreach ($weekGroups as $week => $rows) {
        $totalDt = $rows->sum('total_mins');
        $totalOpl = $rows->sum('opl_mins');
        $totalEpl = $rows->sum('epl_mins');
        $weeklyRows[] = [
            'dt' => $totalDt,
            'opl' => $totalOpl,
            'epl' => $totalEpl,
            'dt_percent' => $totalDt ? round(($totalDt / ($totalDt)) * 100, 2) : 0,
            'opl_percent' => $totalDt ? round(($totalOpl / $totalDt) * 100, 2) : 0,
            'epl_percent' => $totalDt ? round(($totalEpl / $totalDt) * 100, 2) : 0,
        ];
        $finalRows[] = [$week, $rows->avg('le'), $rows->avg('opl_percent'), $rows->avg('epl_percent')];
    }

    // ✅ PTD values
    $ptdLE = $reports->avg('line_efficiency') ?? 0;
    $ptdOPL = collect($dailyRows)->avg('opl_percent') ?? 0;
    $ptdEPL = collect($dailyRows)->avg('epl_percent') ?? 0;
    $totalOpl = collect($dailyRows)->sum('opl_mins');
    $totalEpl = collect($dailyRows)->sum('epl_mins');
    $totalDt = $totalOpl + $totalEpl;

    return view('analytics.line.monthly_report', compact(
        'reports', 'oplCategories', 'oplData', 'eplCategories', 'eplData',
        'dailyRows', 'weeklyRows', 'finalRows',
        'ptdLE', 'ptdOPL', 'ptdEPL',
        'totalOpl', 'totalEpl', 'totalDt','line' ,'month','year'
    ));
}


    public function exportCSV(Request $request)
{
    $month = $request->query('month', now()->format('F'));
    $monthNumber = Carbon::parse($month)->month;
    $line = $request->query('line');
    $year = $request->query('date', now()->year);

    // --- get the same data as monthly_report ---
    $reports = ProductionReport::with(['standard', 'lineQcRejects.defect', 'statuses'])
        ->whereMonth('production_date', $monthNumber)
        ->whereYear('production_date', $year)
        ->when($line, fn($q) => $q->where('line', $line))
        ->whereHas('statuses', fn($q) => $q->where('status', 'Validated'))
        ->orderBy('production_date')
        ->get();

    $data = []; // build the table rows
    foreach ($reports as $report) {
        $data[] = [
            Carbon::parse($report->production_date)->format('n/j/y'),
            $report->standard->description ?? 'N/A',
            $report->standard->size ?? '',
            number_format($report->line_efficiency, 2) . '%',
        ];
    }

    // headers for table
    $tableHeaders = ['Date', 'SKU', 'Size', 'Line Efficiency'];

    // return Excel download
    return Excel::download(
        new LineEfficiencyExport($line, $month, $year, $data, $tableHeaders),
        "LineEfficiency_{$line}_{$month}_{$year}.xlsx"
    );
}
}
