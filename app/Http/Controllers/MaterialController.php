<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductionReport;
use App\Models\LineQcReject;
use App\Models\Line;
use App\Models\Defect;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Exports\MTDMaterialSummaryExport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelWriter;
use App\Exports\YTDMaterialSummaryExport;
use App\Exports\MaterialOverallExport;
use App\Models\MaterialUtilizationAnalytics;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $line = $request->query('line');
        $year = $request->query('date');

    $availableYears = MaterialUtilizationAnalytics::selectRaw('YEAR(production_date) as year')
        ->distinct()
        ->orderByDesc('year')
        ->pluck('year')
        ->toArray();

    $analytics = MaterialUtilizationAnalytics::query()
        ->when($year, fn($q) => $q->whereYear('production_date', $year))
        ->when($line, fn($q) => $q->where('line', $line))   // ✅ fixed
        ->get();

    // Get active lines directly from analytics
    $activeLines = MaterialUtilizationAnalytics::select('line as line_number')  // ✅ fixed
        ->distinct()
        ->orderBy('line')
        ->get();


        // ✅ Compute monthly, quarterly, and PTD totals from analytics
        $monthlyData = $this->computeAnalyticsData($analytics);

        return view('analytics.material.index', array_merge([
            'selectedLine'   => $line,
            'year'           => $year,
            'availableYears' => $availableYears,
            'activeLines'    => $activeLines,
            'analytics'      => $analytics,
        ], $monthlyData));
    }

    private function computeAnalyticsData($analytics)
    {
        // Init arrays
        $monthlyProduction    = array_fill(1, 12, 0);
        $preformFgUsage       = array_fill(1, 12, 0);
        $preformRejects       = array_fill(1, 12, 0);
        $preformQaSamples     = array_fill(1, 12, 0);
        $preformRejectRates   = array_fill(1, 12, '0.00%');

        $capsFgUsage          = array_fill(1, 12, 0);
        $capsRejects          = array_fill(1, 12, 0);
        $capsQaSamples        = array_fill(1, 12, 0);
        $capsRejectRates      = array_fill(1, 12, '0.00%');

        $oppFgUsage           = array_fill(1, 12, 0);
        $oppRejects           = array_fill(1, 12, 0);
        $oppQaSamples         = array_fill(1, 12, 0);
        $oppRejectRates       = array_fill(1, 12, '0.00%');

        $ldpeFgUsage          = array_fill(1, 12, 0);
        $ldpeRejects          = array_fill(1, 12, 0);
        $ldpeQaSamples        = array_fill(1, 12, 0);
        $ldpeRejectRates      = array_fill(1, 12, '0.00%');

        // === Loop analytics rows ===
        foreach ($analytics as $row) {
            $m = (int) \Carbon\Carbon::parse($row->production_date)->format('n'); // 1–12

            $monthlyProduction[$m] += $row->total_output;

            // Preforms
            $preformFgUsage[$m]   += $row->preform_fg;
            $preformRejects[$m]   += $row->preform_rej;
            $preformQaSamples[$m] += $row->preform_qa;

            // Caps
            $capsFgUsage[$m]   += $row->caps_fg;
            $capsRejects[$m]   += $row->caps_rej;
            $capsQaSamples[$m] += $row->caps_qa;

            // OPP
            $oppFgUsage[$m]   += $row->label_fg;
            $oppRejects[$m]   += $row->label_rej;
            $oppQaSamples[$m] += $row->label_qa;

            // LDPE
            $ldpeFgUsage[$m]   += $row->ldpe_fg;
            $ldpeRejects[$m]   += $row->ldpe_rej;
            $ldpeQaSamples[$m] += $row->ldpe_qa;
        }

        // === Calculate monthly reject rates ===
        foreach (range(1, 12) as $m) {
            $preformTotal = $preformFgUsage[$m] + $preformRejects[$m] + $preformQaSamples[$m];
            $preformRejectRates[$m] = $preformTotal > 0 ? number_format(($preformRejects[$m] / $preformTotal) * 100, 2) . '%' : '0.00%';

            $capsTotal = $capsFgUsage[$m] + $capsRejects[$m] + $capsQaSamples[$m];
            $capsRejectRates[$m] = $capsTotal > 0 ? number_format(($capsRejects[$m] / $capsTotal) * 100, 2) . '%' : '0.00%';

            $oppTotal = $oppFgUsage[$m] + $oppRejects[$m] + $oppQaSamples[$m];
            $oppRejectRates[$m] = $oppTotal > 0 ? number_format(($oppRejects[$m] / $oppTotal) * 100, 2) . '%' : '0.00%';

            $ldpeTotal = $ldpeFgUsage[$m] + $ldpeRejects[$m] + $ldpeQaSamples[$m];
            $ldpeRejectRates[$m] = $ldpeTotal > 0 ? number_format(($ldpeRejects[$m] / $ldpeTotal) * 100, 2) . '%' : '0.00%';
        }

    // === Quarterly material breakdown ===
    $quarters = [
        'Q1' => [1,2,3],
        'Q2' => [4,5,6],
        'Q3' => [7,8,9],
        'Q4' => [10,11,12],
    ];

    $preformQuarterFgUsage = $preformQuarterRejects = $preformQuarterQaSamples = $preformQuarterRejectRates = [];
    $capsQuarterFgUsage    = $capsQuarterRejects    = $capsQuarterQaSamples    = $capsQuarterRejectRates = [];
    $oppQuarterFgUsage     = $oppQuarterRejects     = $oppQuarterQaSamples     = $oppQuarterRejectRates = [];
    $ldpeQuarterFgUsage    = $ldpeQuarterRejects    = $ldpeQuarterQaSamples    = $ldpeQuarterRejectRates = [];

    foreach ($quarters as $q => $months) {
        // Preforms
        $fg = array_sum(array_intersect_key($preformFgUsage, array_flip($months)));
        $rej = array_sum(array_intersect_key($preformRejects, array_flip($months)));
        $qa  = array_sum(array_intersect_key($preformQaSamples, array_flip($months)));
        $preformQuarterFgUsage[$q] = $fg;
        $preformQuarterRejects[$q] = $rej;
        $preformQuarterQaSamples[$q] = $qa;
        $preformQuarterRejectRates[$q] = ($fg+$rej+$qa) > 0 ? number_format(($rej/($fg+$rej+$qa))*100, 2).'%' : '0.00%';

        // Caps
        $fg = array_sum(array_intersect_key($capsFgUsage, array_flip($months)));
        $rej = array_sum(array_intersect_key($capsRejects, array_flip($months)));
        $qa  = array_sum(array_intersect_key($capsQaSamples, array_flip($months)));
        $capsQuarterFgUsage[$q] = $fg;
        $capsQuarterRejects[$q] = $rej;
        $capsQuarterQaSamples[$q] = $qa;
        $capsQuarterRejectRates[$q] = ($fg+$rej+$qa) > 0 ? number_format(($rej/($fg+$rej+$qa))*100, 2).'%' : '0.00%';

        // OPP
        $fg = array_sum(array_intersect_key($oppFgUsage, array_flip($months)));
        $rej = array_sum(array_intersect_key($oppRejects, array_flip($months)));
        $qa  = array_sum(array_intersect_key($oppQaSamples, array_flip($months)));
        $oppQuarterFgUsage[$q] = $fg;
        $oppQuarterRejects[$q] = $rej;
        $oppQuarterQaSamples[$q] = $qa;
        $oppQuarterRejectRates[$q] = ($fg+$rej+$qa) > 0 ? number_format(($rej/($fg+$rej+$qa))*100, 2).'%' : '0.00%';

        // LDPE
        $fg = array_sum(array_intersect_key($ldpeFgUsage, array_flip($months)));
        $rej = array_sum(array_intersect_key($ldpeRejects, array_flip($months)));
        $qa  = array_sum(array_intersect_key($ldpeQaSamples, array_flip($months)));
        $ldpeQuarterFgUsage[$q] = $fg;
        $ldpeQuarterRejects[$q] = $rej;
        $ldpeQuarterQaSamples[$q] = $qa;
        $ldpeQuarterRejectRates[$q] = ($fg+$rej+$qa) > 0 ? number_format(($rej/($fg+$rej+$qa))*100, 2).'%' : '0.00%';
    }

    // === Quarterly Production (cases) ===
    $quarterlyProduction = [];
    foreach ($quarters as $q => $months) {
        $quarterlyProduction[$q] = array_sum(array_intersect_key($monthlyProduction, array_flip($months)));
    }

    // === PTD Annual Production (cases) ===
    $totalAnnualProduction = array_sum($monthlyProduction);


        // === Overall Efficiency (average of 4 categories) ===
        $preformTotalRate = $this->calcTotalRate($preformFgUsage, $preformRejects, $preformQaSamples);
        $capsTotalRate    = $this->calcTotalRate($capsFgUsage, $capsRejects, $capsQaSamples);
        $oppTotalRate     = $this->calcTotalRate($oppFgUsage, $oppRejects, $oppQaSamples);
        $ldpeTotalRate    = $this->calcTotalRate($ldpeFgUsage, $ldpeRejects, $ldpeQaSamples);

        $materialEfficiencyRate = number_format(
            ($preformTotalRate + $capsTotalRate + $oppTotalRate + $ldpeTotalRate) / 4,
            2
        ) . '%';

        
    return compact(
        'monthlyProduction',
        'preformFgUsage','preformRejects','preformQaSamples','preformRejectRates',
        'capsFgUsage','capsRejects','capsQaSamples','capsRejectRates',
        'oppFgUsage','oppRejects','oppQaSamples','oppRejectRates',
        'ldpeFgUsage','ldpeRejects','ldpeQaSamples','ldpeRejectRates',
        'quarterlyProduction',
        'totalAnnualProduction',
        'preformTotalRate','capsTotalRate','oppTotalRate','ldpeTotalRate',
        'materialEfficiencyRate',
        'preformQuarterFgUsage','preformQuarterRejects','preformQuarterQaSamples','preformQuarterRejectRates',
        'capsQuarterFgUsage','capsQuarterRejects','capsQuarterQaSamples','capsQuarterRejectRates',
        'oppQuarterFgUsage','oppQuarterRejects','oppQuarterQaSamples','oppQuarterRejectRates',
        'ldpeQuarterFgUsage','ldpeQuarterRejects','ldpeQuarterQaSamples','ldpeQuarterRejectRates'
    );
    }

    private function calcTotalRate($fgUsage, $rejects, $qaSamples)
    {
        $fg  = array_sum($fgUsage);
        $rej = array_sum($rejects);
        $qa  = array_sum($qaSamples);
        $total = $fg + $rej + $qa;

        return $total > 0 ? round(($rej / $total) * 100, 2) : 0;
    }

    public function monthly_report(Request $request) 
    {
        // === Step 1: filters ===
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

        // === Step 2: query analytics ===
        $analytics = MaterialUtilizationAnalytics::query()
            ->whereMonth('production_date', $monthNumber)
            ->whereYear('production_date', $year)
            ->when($line, fn($q) => $q->where('line', $line))
            ->orderBy('production_date')
            ->get();

        // === Step 3: weekly summary ===
        $weeklyData = collect(range(1, 5))->map(function($week) use ($analytics) {
            $weekRows = $analytics->filter(fn($row) => Carbon::parse($row->production_date)->weekOfMonth === $week);

            $sum = fn($col) => $weekRows->sum($col);

            return [
                'output' => $sum('total_output'),
                'preform' => [
                    'fg'      => $sum('preform_fg'),
                    'rej'     => $sum('preform_rej'),
                    'qa'      => $sum('preform_qa'),
                    'percent' => ($sum('preform_fg') + $sum('preform_rej') + $sum('preform_qa')) > 0
                        ? number_format(($sum('preform_rej') / ($sum('preform_fg') + $sum('preform_rej') + $sum('preform_qa'))) * 100, 2) . '%'
                        : '0.00%',
                ],
                'caps' => [
                    'fg'      => $sum('caps_fg'),
                    'rej'     => $sum('caps_rej'),
                    'qa'      => $sum('caps_qa'),
                    'percent' => ($sum('caps_fg') + $sum('caps_rej') + $sum('caps_qa')) > 0
                        ? number_format(($sum('caps_rej') / ($sum('caps_fg') + $sum('caps_rej') + $sum('caps_qa'))) * 100, 2) . '%'
                        : '0.00%',
                ],
                'label' => [
                    'fg'      => $sum('label_fg'),
                    'rej'     => $sum('label_rej'),
                    'qa'      => $sum('label_qa'),
                    'percent' => ($sum('label_fg') + $sum('label_rej') + $sum('label_qa')) > 0
                        ? number_format(($sum('label_rej') / ($sum('label_fg') + $sum('label_rej') + $sum('label_qa'))) * 100, 2) . '%'
                        : '0.00%',
                ],
                'ldpe' => [
                    'fg'      => $sum('ldpe_fg'),
                    'rej'     => $sum('ldpe_rej'),
                    'qa'      => $sum('ldpe_qa'),
                    'percent' => ($sum('ldpe_fg') + $sum('ldpe_rej') + $sum('ldpe_qa')) > 0
                        ? number_format(($sum('ldpe_rej') / ($sum('ldpe_fg') + $sum('ldpe_rej') + $sum('ldpe_qa'))) * 100, 2) . '%'
                        : '0.00%',
                ],
            ];
        })->toArray();

        // === Step 4: PTD totals ===
        $totalPreformFg  = $analytics->sum('preform_fg');
        $totalPreformRej = $analytics->sum('preform_rej');
        $totalPreformQa  = $analytics->sum('preform_qa');
        $totalPreformPct = $totalPreformFg + $totalPreformRej + $totalPreformQa > 0
            ? number_format(($totalPreformRej / ($totalPreformFg + $totalPreformRej + $totalPreformQa)) * 100, 2) . '%'
            : '0.00%';

        $totalCapsFg  = $analytics->sum('caps_fg');
        $totalCapsRej = $analytics->sum('caps_rej');
        $totalCapsQa  = $analytics->sum('caps_qa');
        $totalCapsPct = $totalCapsFg + $totalCapsRej + $totalCapsQa > 0
            ? number_format(($totalCapsRej / ($totalCapsFg + $totalCapsRej + $totalCapsQa)) * 100, 2) . '%'
            : '0.00%';

        $totalLabelFg  = $analytics->sum('label_fg');
        $totalLabelRej = $analytics->sum('label_rej');
        $totalLabelQa  = $analytics->sum('label_qa');
        $totalLabelPct = $totalLabelFg + $totalLabelRej + $totalLabelQa > 0
            ? number_format(($totalLabelRej / ($totalLabelFg + $totalLabelRej + $totalLabelQa)) * 100, 2) . '%'
            : '0.00%';

        $totalLdpeFg  = $analytics->sum('ldpe_fg');
        $totalLdpeRej = $analytics->sum('ldpe_rej');
        $totalLdpeQa  = $analytics->sum('ldpe_qa');
        $totalLdpePct = $totalLdpeFg + $totalLdpeRej + $totalLdpeQa > 0
            ? number_format(($totalLdpeRej / ($totalLdpeFg + $totalLdpeRej + $totalLdpeQa)) * 100, 2) . '%'
            : '0.00%';

        $activeTab = $request->query('tab', 'overview'); // default is overview

        return view('analytics.material.monthly_report', compact(
            'analytics',
            'weeklyData',
            'totalPreformFg','totalPreformRej','totalPreformQa','totalPreformPct',
            'totalCapsFg','totalCapsRej','totalCapsQa','totalCapsPct',
            'totalLabelFg','totalLabelRej','totalLabelQa','totalLabelPct',
            'totalLdpeFg','totalLdpeRej','totalLdpeQa','totalLdpePct',
            'monthName','monthNumber','line','year',
            'activeTab' // 👈 pass to Blade
        ));
    }

    public function material_utilization(Request $request)
    {
        $line = $request->query('line');
        $year = $request->query('date');

        // === Active Lines (from analytics)
        $lines = \App\Models\Line::pluck('line_number')->toArray();

        $availableYears = MaterialUtilizationAnalytics::selectRaw('YEAR(production_date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->toArray();

        $activeLines = MaterialUtilizationAnalytics::select('line as line_number')
            ->distinct()
            ->orderBy('line')
            ->get();

        // === Get analytics filtered by year/line
        $analytics = MaterialUtilizationAnalytics::query()
            ->when($year, fn($q) => $q->whereYear('production_date', $year))
            ->when($line, fn($q) => $q->where('line', $line))
            ->get();

        // === Compute monthly/quarterly/PTD from analytics
        $monthlyData = $this->computeAnalyticsData($analytics);

        // Extract reject rates
        $preformRejectRates = $monthlyData['preformRejectRates'] ?? [];
        $capsRejectRates    = $monthlyData['capsRejectRates'] ?? [];
        $oppRejectRates     = $monthlyData['oppRejectRates'] ?? [];
        $ldpeRejectRates    = $monthlyData['ldpeRejectRates'] ?? [];

        // Convert reject rates into numeric arrays for chart
        $convertToDecimal = fn($arr) => array_map(function ($v) {
            return (float) str_replace('%', '', $v) / 100;
        }, $arr);

        $preformRejectRatesData = $convertToDecimal($preformRejectRates);
        $capsRejectRatesData    = $convertToDecimal($capsRejectRates);
        $oppRejectRatesData     = $convertToDecimal($oppRejectRates);
        $ldpeRejectRatesData    = $convertToDecimal($ldpeRejectRates);

        // Calculate averages (ignore empty values)
        $avg = function ($arr) {
            $filtered = array_filter($arr, fn($v) => $v !== null && $v !== 0);
            return count($filtered) ? array_sum($filtered) / count($filtered) : 0;
        };

        $preformAvg = $avg($preformRejectRatesData);
        $capsAvg    = $avg($capsRejectRatesData);
        $oppAvg     = $avg($oppRejectRatesData);
        $ldpeAvg    = $avg($ldpeRejectRatesData);

        // Overall material efficiency = average of all categories
        $materialEfficiency = ($preformAvg + $capsAvg + $oppAvg + $ldpeAvg) / 4;

        // Months for Blade loop
        $months = [
            'January', 'February', 'March', 'April',
            'May', 'June', 'July', 'August',
            'September', 'October', 'November', 'December'
        ];

        return view('analytics.material_utilization', array_merge([
            'selectedLine' => $line,
            'lines'        => $lines,
            'year'         => $year,
            'analytics'    => $analytics,
            'availableYears' => $availableYears,
            'activeLines'    => $activeLines,

            // Reject rates (string format with %)
            'preformRejectRates' => $preformRejectRates,
            'capsRejectRates'    => $capsRejectRates,
            'oppRejectRates'     => $oppRejectRates,
            'ldpeRejectRates'    => $ldpeRejectRates,

            // Reject rates (decimal values for charts)
            'preformRejectRatesData' => $preformRejectRatesData,
            'capsRejectRatesData'    => $capsRejectRatesData,
            'oppRejectRatesData'     => $oppRejectRatesData,
            'ldpeRejectRatesData'    => $ldpeRejectRatesData,

            // Averages
            'preformAvg' => $preformAvg,
            'capsAvg'    => $capsAvg,
            'oppAvg'     => $oppAvg,
            'ldpeAvg'    => $ldpeAvg,
            'materialEfficiency' => $materialEfficiency,

            // Months
            'months' => $months,
        ], $monthlyData));
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
                'event'      => 'material_monthly_export',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'context'    => [
                    'material' => "{$monthName} {$year} MATERIAL MONITORING Line {$line}"
                ],
            ]);

        return Excel::download(
            new MTDMaterialSummaryExport($line, $monthNumber, $year, $monthName),
            "{$monthName} {$year} MATERIAL MONITORING Line {$line}.xlsx",
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
                'event'      => 'material_annual_export',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'context'    => [
                    'material' => "{$year} MATERIAL MONITORING Line {$line}",
                ],
            ]);

        return Excel::download(
            new YTDMaterialSummaryExport($line, $year),
            "{$year} MATERIAL MONITORING Line {$line}.xlsx",
            ExcelWriter::XLSX,
            ['withCharts' => true]
        );
    }

    public function exportExcelMaterialSummary(Request $request)
    {
        $year = $request->query('date', now()->year);

            AuditLog::create([
                'user_id'    => Auth::id(),
                'event'      => 'material_summary_export',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'context'    => [
                    'material' => "{$year} MATERIAL MONITORING Line",
                ],
            ]);

        return Excel::download(
            new MaterialOverallExport($year),
            "{$year} MATERIAL MONITORING.xlsx",
            ExcelWriter::XLSX,
            ['withCharts' => true]
        );
    }

}
