<?php

namespace App\Http\Controllers;

use App\Exports\MTDLineSummaryExport;
use Illuminate\Http\Request;
use App\Models\ProductionReport;
use App\Models\LineQcReject;
use App\Models\Defect;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Maintenance;
use App\Models\ProductionIssues;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelWriter;
use App\Exports\YTDLineSummaryExport;
use App\Exports\LineOverallExport;
use App\Models\LineEfficiencyAnalytics;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;



class LineController extends Controller
{
    /**
     * Display the main analytics index for a line and year.
     */
    public function index(Request $request)
    {
        $line = $request->query('line');
        $year = (int) $request->query('date', now()->year);

        // ------------------------------
        // Available filters
        // ------------------------------
        $availableYears = LineEfficiencyAnalytics::selectRaw('YEAR(production_date) as year')
            ->distinct()->orderByDesc('year')->pluck('year')->toArray();

        $activeLines = LineEfficiencyAnalytics::select('line as line_number')
            ->distinct()->orderBy('line')->get();

        // ------------------------------
        // Category labels
        // ------------------------------
        $eplLabels = Maintenance::where('type','EPL')->orderBy('name')->pluck('name')->toArray();
        $oplLabels = Maintenance::where('type','OPL')->orderBy('name')->pluck('name')->toArray();

        $eplMonthlyRows = $oplMonthlyRows = [];
        $eplTotals = array_fill_keys($eplLabels, 0);
        $oplTotals = array_fill_keys($oplLabels, 0);

        $ptdMonthlyRows = [];
        $ptdTotalsRow   = ['dt'=>0,'opl'=>0,'epl'=>0,'le'=>0,'dt_percent'=>0,'opl_percent'=>0,'epl_percent'=>0];

        // ------------------------------
        // If filters are provided
        // ------------------------------
        if ($year && $line) {
            $yearAnalytics = LineEfficiencyAnalytics::query()
                ->whereYear('production_date', $year)
                ->when($line, fn($q) => $q->where('line', $line))
                ->get();

            // --------------------------
            // Monthly aggregates
            // --------------------------
            foreach (range(1,12) as $m) {
                $monthAnalytics = $yearAnalytics->filter(
                    fn($a) => (int) Carbon::parse($a->production_date)->format('n') === $m
                );

                // init category buckets
                $eplByCat = array_fill_keys($eplLabels, 0);
                $oplByCat = array_fill_keys($oplLabels, 0);

                if ($monthAnalytics->isEmpty()) {
                    $eplMonthlyRows[] = ['period' => 'P'.$m, 'values' => array_values($eplByCat)];
                    $oplMonthlyRows[] = ['period' => 'P'.$m, 'values' => array_values($oplByCat)];

                    $ptdMonthlyRows[] = [
                        'period' => 'P'.$m,
                        'dt' => 0, 'opl' => 0, 'epl' => 0, 'le' => 0,
                        'dt_percent' => 0,'opl_percent'=>0,'epl_percent'=>0,
                    ];
                    continue;
                }

                // === OPL & EPL category minutes
                foreach ($monthAnalytics as $row) {
                    if ($row->downtime_type === 'OPL' && in_array($row->category, $oplLabels)) {
                        $oplByCat[$row->category] += (int) $row->minutes;
                    }
                    if ($row->downtime_type === 'EPL' && in_array($row->category, $eplLabels)) {
                        $eplByCat[$row->category] += (int) $row->minutes;
                    }
                }

                // Update monthly rows
                $eplMonthlyRows[] = ['period'=>'P'.$m, 'values'=>array_values($eplByCat)];
                $oplMonthlyRows[] = ['period'=>'P'.$m, 'values'=>array_values($oplByCat)];

                // Update totals
                foreach ($eplByCat as $c=>$v) $eplTotals[$c] += $v;
                foreach ($oplByCat as $c=>$v) $oplTotals[$c] += $v;

                // === LE & PTD row
                $dailyGroups = $monthAnalytics->groupBy(fn($a) => Carbon::parse($a->production_date)->format('Y-m-d'));
                $dailyLEs = $dailyGroups->map(fn($rows) => (float) $rows->first()->line_efficiency)->values();

                $avgLE = round($dailyLEs->avg() ?? 0, 2);

                $monthOpl = array_sum($oplByCat);
                $monthEpl = array_sum($eplByCat);
                $totalMins = $monthOpl + $monthEpl;

                $dtPercent = max(0, min(100, 100 - $avgLE));

                $ptdMonthlyRows[] = [
                    'period'      => 'P'.$m,
                    'dt'          => $totalMins,
                    'opl'         => $monthOpl,
                    'epl'         => $monthEpl,
                    'le'          => $avgLE,
                    'dt_percent'  => $dtPercent,
                    'opl_percent' => $totalMins ? round(($monthOpl/$totalMins)*$dtPercent, 2) : 0,
                    'epl_percent' => $totalMins ? round(($monthEpl/$totalMins)*$dtPercent, 2) : 0,
                ];
            }

            // --------------------------
            // YTD totals row
            // --------------------------
            $totalOplYtd = array_sum(array_column($ptdMonthlyRows, 'opl'));
            $totalEplYtd = array_sum(array_column($ptdMonthlyRows, 'epl'));
            $totalDtYtd  = $totalOplYtd + $totalEplYtd;

            $nonZeroLEs = collect($ptdMonthlyRows)->pluck('le')->filter(fn($v)=>$v!=0);
            $avgLEYtd   = $nonZeroLEs->isNotEmpty() ? round($nonZeroLEs->avg(), 2) : 0;

            $dtPercentYtd = max(0, min(100, 100 - $avgLEYtd));

            $ptdTotalsRow = [
                'dt'          => $totalDtYtd,
                'opl'         => $totalOplYtd,
                'epl'         => $totalEplYtd,
                'le'          => $avgLEYtd,
                'dt_percent'  => $dtPercentYtd,
                'opl_percent' => $totalDtYtd ? round(($totalOplYtd/$totalDtYtd)*$dtPercentYtd, 2) : 0,
                'epl_percent' => $totalDtYtd ? round(($totalEplYtd/$totalDtYtd)*$dtPercentYtd, 2) : 0,
            ];
        }

        // ------------------------------
        // Return
        // ------------------------------
        return view('analytics.line.index', [
            'selectedLine'   => $line,
            'year'           => $year,
            'availableYears' => $availableYears,
            'activeLines'    => $activeLines,

            'eplLabels'       => $eplLabels,
            'eplMonthlyRows'  => $eplMonthlyRows,
            'eplTotals'       => $eplTotals,
            'eplChartLabels'  => $eplLabels,
            'eplChartMinutes' => array_values($eplTotals),

            'oplLabels'       => $oplLabels,
            'oplMonthlyRows'  => $oplMonthlyRows,
            'oplTotals'       => $oplTotals,
            'oplChartLabels'  => $oplLabels,
            'oplChartMinutes' => array_values($oplTotals),

            'ptdMonthlyRows'  => $ptdMonthlyRows,
            'ptdTotalsRow'    => $ptdTotalsRow,
        ]);
    }
    public function monthly_report(Request $request)
    {
        // ------------------------------
        // 1) Parse inputs (month / year / line)
        // ------------------------------
        $monthInput = $request->query('month', now()->month);
        if (is_numeric($monthInput)) {
            $monthNumber = (int) $monthInput;
            $monthName   = Carbon::create()->month($monthNumber)->format('F');
        } else {
            $monthName   = (string) $monthInput;
            $monthNumber = Carbon::parse("1 {$monthInput}")->month;
        }

        $line = $request->query('line');
        $year = (int) $request->query('date', now()->year);

        // ------------------------------
        // 2) Fetch analytics data
        // ------------------------------
        $analytics = \App\Models\LineEfficiencyAnalytics::query()
            ->whereMonth('production_date', $monthNumber)
            ->whereYear('production_date', $year)
            ->when($line, fn($q) => $q->where('line', $line))
            ->get();

        $labelsDays = $analytics
            ->pluck('production_date')
            ->map(fn ($d) => Carbon::parse($d)->day)
            ->unique()
            ->sort()
            ->values()
            ->all();

        // ------------------------------
        // 3) Category dictionaries
        // ------------------------------
        $oplCategories = Maintenance::where('type', 'OPL')->orderBy('name')->pluck('name')->toArray();
        $eplCategories = Maintenance::where('type', 'EPL')->orderBy('name')->pluck('name')->toArray();

        // ------------------------------
        // 4) Group analytics by date (Y-m-d)
        // ------------------------------
        $analyticsByDate = $analytics->groupBy(fn($a) => Carbon::parse($a->production_date)->format('Y-m-d'));

        // ------------------------------
        // 5) Build daily rows
        // ------------------------------
        $dailyRows = [];
        foreach ($analyticsByDate as $dateKey => $entries) {
            $sample    = $entries->first();
            $le        = (float) $sample->line_efficiency;
            $oplMins   = (int) $entries->where('downtime_type', 'OPL')->sum('minutes');
            $eplMins   = (int) $entries->where('downtime_type', 'EPL')->sum('minutes');
            $totalMins = $oplMins + $eplMins;

            $dtPercent = max(0, min(100, 100 - $le));
            $oplPct    = $totalMins > 0 ? ($oplMins / $totalMins) * $dtPercent : 0;
            $eplPct    = $totalMins > 0 ? ($eplMins / $totalMins) * $dtPercent : 0;

            $dailyRows[] = [
                'date'        => Carbon::parse($dateKey)->format('F j, Y'),
                'date_key'    => $dateKey, // raw date for grouping
                'sku'         => $sample->sku,
                'size'        => $sample->bottlesPerCase,
                'target_le'   => 0,
                'le'          => round($le, 2),
                'opl_percent' => round($oplPct, 2),
                'epl_percent' => round($eplPct, 2),
                'total_mins'  => $totalMins,
                'opl_mins'    => $oplMins,
                'epl_mins'    => $eplMins,
                'dt'          => round($dtPercent, 2),
            ];
        }

        // ------------------------------
        // 6) Weekly aggregates
        // ------------------------------
        $weeklyRows = [];
        $finalRows  = [];

        $weekGroups = collect($dailyRows)->groupBy(fn ($row) => Carbon::parse($row['date_key'])->weekOfMonth);

        foreach ($weekGroups as $week => $rows) {
            $totalDtMins   = (int) $rows->sum('total_mins');
            $totalOpl      = (int) $rows->sum('opl_mins');
            $totalEpl      = (int) $rows->sum('epl_mins');
            $avgLE         = (float) $rows->avg('le');
            $weekDtPercent = max(0, min(100, 100 - $avgLE));

            $weeklyRows[] = [
                'dt'          => $totalDtMins,
                'opl'         => $totalOpl,
                'epl'         => $totalEpl,
                'dt_percent'  => round($weekDtPercent, 2),
                'opl_percent' => $totalDtMins ? round(($totalOpl / $totalDtMins) * $weekDtPercent, 2) : 0,
                'epl_percent' => $totalDtMins ? round(($totalEpl / $totalDtMins) * $weekDtPercent, 2) : 0,
            ];

            $weeklyAvgOplPct = (float) $rows->avg('opl_percent');
            $weeklyAvgEplPct = (float) $rows->avg('epl_percent');

            $finalRows[] = [$week, round($avgLE, 2), round($weeklyAvgOplPct, 2), round($weeklyAvgEplPct, 2)];
        }

        // ------------------------------
        // 6b) Weekly OPL by category
        // ------------------------------
        $weeklyOplByCategory = [];
        foreach ($weekGroups as $week => $rows) {
            $cats = [];
            foreach ($oplCategories as $cat) {
                $sumCat = 0;
                foreach ($rows as $row) {
                    $sumCat += $analyticsByDate[$row['date_key']]
                        ->where('downtime_type', 'OPL')
                        ->where('category', $cat)
                        ->sum('minutes');
                }
                $cats[$cat] = $sumCat;
            }
            $weeklyOplByCategory[$week] = $cats;
        }

        // ------------------------------
        // 6c) Weekly EPL by category
        // ------------------------------
        $weeklyEplByCategory = [];
        foreach ($weekGroups as $week => $rows) {
            $cats = [];
            foreach ($eplCategories as $cat) {
                $sumCat = 0;
                foreach ($rows as $row) {
                    $sumCat += $analyticsByDate[$row['date_key']]
                        ->where('downtime_type', 'EPL')
                        ->where('category', $cat)
                        ->sum('minutes');
                }
                $cats[$cat] = $sumCat;
            }
            $weeklyEplByCategory[$week] = $cats;
        }

        // ------------------------------
        // 7) PTD totals
        // ------------------------------
        $ptdLE        = round(collect($dailyRows)->avg('le') ?? 0, 2);
        $totalOpl     = (int) collect($dailyRows)->sum('opl_mins');
        $totalEpl     = (int) collect($dailyRows)->sum('epl_mins');
        $totalDtMins  = $totalOpl + $totalEpl;

        $ptdDtPercent = max(0, min(100, 100 - $ptdLE));
        $ptdOPL       = $totalDtMins ? round(($totalOpl / $totalDtMins) * $ptdDtPercent, 2) : 0;
        $ptdEPL       = $totalDtMins ? round(($totalEpl / $totalDtMins) * $ptdDtPercent, 2) : 0;

        $totalDt = $totalDtMins;

        // ------------------------------
        // 8) Totals per category
        // ------------------------------
        $oplTotalsByCategory = $analytics->where('downtime_type','OPL')
            ->groupBy('category')->map->sum('minutes')->toArray();
        $eplTotalsByCategory = $analytics->where('downtime_type','EPL')
            ->groupBy('category')->map->sum('minutes')->toArray();

        // ------------------------------
        // 9) Daily OPL/EPL data for charts
        // ------------------------------
        $oplData = [];
        $eplData = [];

        $datesInAnalytics = $analytics
            ->pluck('production_date')
            ->unique()
            ->sort()
            ->values();

        foreach ($datesInAnalytics as $dbDate) {
            $display = Carbon::parse($dbDate)->format('F j, Y');

            $catsOpl = $analytics
                ->where('downtime_type', 'OPL')
                ->where('production_date', $dbDate)
                ->groupBy('category')
                ->map->sum('minutes')
                ->toArray();

            $oplData[] = [
                'date'       => $display,
                'categories' => collect($oplCategories)->mapWithKeys(
                    fn ($c) => [$c => $catsOpl[$c] ?? 0]
                )->toArray(),
            ];

            $catsEpl = $analytics
                ->where('downtime_type', 'EPL')
                ->where('production_date', $dbDate)
                ->groupBy('category')
                ->map->sum('minutes')
                ->toArray();

            $eplData[] = [
                'date'       => $display,
                'categories' => collect($eplCategories)->mapWithKeys(
                    fn ($c) => [$c => $catsEpl[$c] ?? 0]
                )->toArray(),
            ];
        }

        // ------------------------------
        // 10) Return view
        // ------------------------------
        return view('analytics.line.monthly_report', compact(
            'oplCategories', 'eplCategories',
            'oplTotalsByCategory','eplTotalsByCategory',
            'dailyRows','weeklyRows','finalRows',
            'ptdLE','ptdOPL','ptdEPL','totalOpl','totalEpl','totalDt',
            'line','year','monthName','monthNumber',
            'labelsDays',
            'oplData','eplData',
            'weeklyOplByCategory','weeklyEplByCategory',
            'analytics'
        ));
    }
    public function exportExcel(Request $request)
    {
        // Accept both month number (1–12) and month name ("January")
        $monthInput = $request->query('month', now()->month);

        if (is_numeric($monthInput)) {
            $monthNumber = (int) $monthInput;
            $monthName   = Carbon::create()->month($monthNumber)->format('F');
        } else {
            $monthName   = $monthInput;
            $monthNumber = Carbon::parse("1 $monthInput")->month;
        }

        $line = $request->query('line');
        $year = $request->query('date', now()->year);

            AuditLog::create([
                'user_id'    => Auth::id(),
                'event'      => 'line_monthly_export',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'context'    => [
                    'line' => "{$monthName} {$year} LE MONITORING Graph Line {$line}",
                ],
            ]);

        return Excel::download(
            new MTDLineSummaryExport($line, $monthNumber, $year, $monthName),
            "{$monthName} {$year} LE MONITORING Graph Line {$line}.xlsx",
            ExcelWriter::XLSX,
            [
                'withCharts' => true, // <-- REQUIRED
            ]
        );
    }
    public function exportExcelAnnual(Request $request)
    {
        $line = $request->query('line');
        $year = $request->query('date', now()->year);

            AuditLog::create([
                'user_id'    => Auth::id(),
                'event'      => 'line_annual_export',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'context'    => [
                    'line' => "{$year} LE MONITORING Summary Line {$line}",
                ],
            ]);

        return Excel::download(
            new YTDLineSummaryExport($line, $year),
            "{$year} LINE  MONITORING Line {$line}.xlsx",
            ExcelWriter::XLSX,
            ['withCharts' => true]
        );
    }

    public function line_efficiency(Request $request)
    {
        $line = $request->query('line');
        $year = $request->query('date');

        $lines = \App\Models\Line::pluck('line_number')->toArray();

        $availableYears = ProductionReport::selectRaw('YEAR(production_date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->toArray();

        $activeLines = ProductionReport::select('line as line_number')
            ->distinct()
            ->orderBy('line')
            ->get();

        $reports = ProductionReport::with(['issues.maintenance', 'statuses'])
            ->whereYear('production_date', $year)
            ->when($line, fn ($q) => $q->where('line', $line))
            ->whereHas('statuses', fn ($q) => $q->where('status', 'Validated'))
            ->orderBy('production_date')
            ->get();

        // Group by month (1–12)
        $reportsByMonth = $reports->groupBy(function ($report) {
            return (int) \Carbon\Carbon::parse($report->production_date)->format('n');
        });

        // Always build 12 months
        $grouped = collect(range(1, 12))->mapWithKeys(function ($m) use ($reportsByMonth, $activeLines) {
            $monthReports = $reportsByMonth->get($m, collect());

            $linesData   = [];
            $totalVolume = 0;

            // Initialize all lines with zeros
            foreach ($activeLines as $lineObj) {
                $lineNum = $lineObj->line_number;
                $linesData[$lineNum] = [
                    'volume'       => 0,
                    'le'           => 0,
                    'opl'          => 0,
                    'epl'          => 0,
                    'contribution' => 0,
                ];
            }

            if ($monthReports->isNotEmpty()) {
                foreach ($monthReports->groupBy('line') as $lineNumber => $lineReports) {
                    $lineVolume   = $lineReports->sum('total_outputCase');
                    $totalVolume += $lineVolume;

                    // === Compute LE (daily highest avg)
                    $dailyGroups = $lineReports->groupBy(fn($r) =>
                        \Carbon\Carbon::parse($r->production_date)->format('Y-m-d')
                    );

                    $dailyLEs = [];
                    foreach ($dailyGroups as $dayReports) {
                        $top = $dayReports->sortByDesc('total_outputCase')->first();
                        if ($top) {
                            $dailyLEs[] = (float) $top->line_efficiency;
                        }
                    }
                    $avgLE = $dailyLEs ? round(collect($dailyLEs)->avg(), 2) : 0;

                    // === OPL/EPL Minutes → %
                    $repIds   = $lineReports->pluck('id');
                    $oplMins  = \App\Models\ProductionIssues::whereIn('production_reports_id', $repIds)
                        ->whereHas('maintenance', fn($q) => $q->where('type', 'OPL'))
                        ->sum('minutes');

                    $eplMins  = \App\Models\ProductionIssues::whereIn('production_reports_id', $repIds)
                        ->whereHas('maintenance', fn($q) => $q->where('type', 'EPL'))
                        ->sum('minutes');

                    $totalMins = $oplMins + $eplMins;
                    $dtPercent = $avgLE > 0 ? (100 - $avgLE) : 0;

                    $oplPercent = $totalMins ? round(($oplMins / $totalMins) * $dtPercent, 2) : 0;
                    $eplPercent = $totalMins ? round(($eplMins / $totalMins) * $dtPercent, 2) : 0;

                    $linesData[$lineNumber] = [
                        'volume' => $lineVolume,
                        'le'     => $avgLE,
                        'opl'    => $oplPercent,
                        'epl'    => $eplPercent,
                    ];
                }

                // Contributions
                foreach ($linesData as $lineNumber => &$ld) {
                    $ld['contribution'] = $totalVolume > 0
                        ? round(($ld['volume'] / $totalVolume) * 100, 2)
                        : 0;
                }
                unset($ld);
            }

            // Plant Total (weighted)
            $plantLE = $plantOPL = $plantEPL = 0;
            foreach ($linesData as $ld) {
                if ($totalVolume > 0) {
                    $weight   = $ld['volume'] / $totalVolume;
                    $plantLE  += $ld['le']  * $weight;
                    $plantOPL += $ld['opl'] * $weight;
                    $plantEPL += $ld['epl'] * $weight;
                }
            }
            

            return [
                $m => [
                    'lines'       => $linesData,
                    'totalVolume' => $totalVolume,
                    'plantTotal'  => [
                        'le'  => round($plantLE, 2),
                        'opl' => round($plantOPL, 2),
                        'epl' => round($plantEPL, 2),
                    ],
                    'targetLE'    => 80,
                ],
            ];
        });


        // === Yearly (YTD) Summary ===
        $yearSummary = [
            'totalVolume' => $grouped->sum('totalVolume'),
            'plantTotal'  => [
                'le'  => round($grouped->pluck('plantTotal.le')->filter(fn($v) => $v > 0)->avg(), 2),
                'opl' => round($grouped->pluck('plantTotal.opl')->filter(fn($v) => $v > 0)->avg(), 2),
                'epl' => round($grouped->pluck('plantTotal.epl')->filter(fn($v) => $v > 0)->avg(), 2),
            ],
            'lines' => [],
        ];

        foreach ($activeLines as $lineObj) {
            $lineNum = $lineObj->line_number;
            $yearSummary['lines'][$lineNum] = [
                'volume'       => $grouped->sum(fn($m) => $m['lines'][$lineNum]['volume']),
                'le'           => round($grouped->pluck("lines.$lineNum.le")->filter(fn($v) => $v > 0)->avg(), 2),
                'opl'          => round($grouped->pluck("lines.$lineNum.opl")->filter(fn($v) => $v > 0)->avg(), 2),
                'epl'          => round($grouped->pluck("lines.$lineNum.epl")->filter(fn($v) => $v > 0)->avg(), 2),
                'contribution' => round($grouped->pluck("lines.$lineNum.contribution")->filter(fn($v) => $v > 0)->avg(), 2),
            ];
        }



        return view('analytics.line_efficiency', [
            'selectedLine'   => $line,
            'lines'          => $lines,
            'year'           => $year,
            'availableYears' => $availableYears,
            'activeLines'    => $activeLines,
            'yearSummary' => $yearSummary,
            'grouped'        => $grouped,
            'plantTotal'     => $grouped->reduce(function ($carry, $month) {
                $carry['le']  += $month['plantTotal']['le'];
                $carry['opl'] += $month['plantTotal']['opl'];
                $carry['epl'] += $month['plantTotal']['epl'];
                return $carry;
            }, ['le' => 0, 'opl' => 0, 'epl' => 0]),
        ]);
    }

    public function exportExcelLineSummary(Request $request)
    {
        $year = $request->query('date', now()->year);

            AuditLog::create([
                'user_id'    => Auth::id(),
                'event'      => 'line_summary_export',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'context'    => [
                    'line' => "{$year} Line Summary",
                ],
            ]);

        return Excel::download(
            new LineOverallExport($year),
            "{$year} LINE MONITORING.xlsx",
            ExcelWriter::XLSX,
            ['withCharts' => true]
        );
    }

}
