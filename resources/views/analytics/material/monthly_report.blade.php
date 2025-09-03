@extends('layouts.app')

@section('content')


{{-- Page Title --}}
<h2 class="text-xl mb-2 font-bold text-[#23527c]">Monthly Report</h2>

<a href="{{ route('analytics.material.index', ['line' => request('line'), 'date' => request('date', now()->year)]) }}"
   class="text-xs text-gray-500 hover:text-[#23527c] mb-4 inline-flex items-center">
    <x-icons-back-confi/>
    Analytics and Report
</a>

<div class="mx-16 mt-4">
<!-- Heading -->
<h2 class="flex items-center text-xl text-[#23527c] mb-2 font-bold">
    <svg class="w-6 h-6 mr-2 flex-shrink-0" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 15.315 15.315" xml:space="preserve" fill="#23527c"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <g> <path style="fill:#23527c;" d="M3.669,3.71h0.696c0.256,0,0.464-0.165,0.464-0.367V0.367C4.829,0.164,4.621,0,4.365,0H3.669 C3.414,0,3.206,0.164,3.206,0.367v2.976C3.205,3.545,3.413,3.71,3.669,3.71z"></path> <path style="fill:#23527c;" d="M10.95,3.71h0.696c0.256,0,0.464-0.165,0.464-0.367V0.367C12.11,0.164,11.902,0,11.646,0H10.95 c-0.256,0-0.463,0.164-0.463,0.367v2.976C10.487,3.545,10.694,3.71,10.95,3.71z"></path> <path style="fill:#23527c;" d="M14.512,1.42h-1.846v2.278c0,0.509-0.458,0.923-1.021,0.923h-0.696 c-0.563,0-1.021-0.414-1.021-0.923V1.42H5.384v2.278c0,0.509-0.458,0.923-1.021,0.923H3.669c-0.562,0-1.02-0.414-1.02-0.923V1.42 H0.803c-0.307,0-0.557,0.25-0.557,0.557V14.76c0,0.307,0.25,0.555,0.557,0.555h13.709c0.308,0,0.557-0.248,0.557-0.555V1.977 C15.069,1.67,14.82,1.42,14.512,1.42z M14.316,9.49v4.349c0,0.096-0.078,0.176-0.175,0.176H7.457H1.174 c-0.097,0-0.175-0.08-0.175-0.176V10.31V5.961c0-0.096,0.078-0.176,0.175-0.176h6.683h6.284l0,0c0.097,0,0.175,0.08,0.175,0.176 V9.49z"></path> <rect x="2.327" y="8.93" style="fill:#23527c;" width="1.735" height="1.736"></rect> <rect x="5.28" y="8.93" style="fill:#23527c;" width="1.735" height="1.736"></rect> <rect x="8.204" y="8.93" style="fill:#23527c;" width="1.734" height="1.736"></rect> <rect x="11.156" y="8.93" style="fill:#23527c;" width="1.736" height="1.736"></rect> <rect x="2.363" y="11.432" style="fill:#23527c;" width="1.736" height="1.736"></rect> <rect x="5.317" y="11.432" style="fill:#23527c;" width="1.735" height="1.736"></rect> <rect x="8.241" y="11.432" style="fill:#23527c;" width="1.734" height="1.736"></rect> <rect x="11.194" y="11.432" style="fill:#23527c;" width="1.735" height="1.736"></rect> <rect x="8.215" y="6.47" style="fill:#23527c;" width="1.735" height="1.735"></rect> <rect x="11.17" y="6.47" style="fill:#23527c;" width="1.734" height="1.735"></rect> </g> </g> </g></svg>
    {{ $monthName }} {{ $year }} (Line {{ $line }})
</h2>


<!-- Divider -->
<div class="w-full flex items-center justify-center mb-6">
    <div class="w-full border-t border-[#E5E7EB]"></div>
</div>


<div class="flex flex-col md:flex-row gap-2 mb-4">

    <a href="{{ route('analytics.index') }}"
        class="inline-flex items-center px-3 py-2 bg-[#5a9fd4] hover:bg-[#4a8bc2] text-white text-sm font-medium transition-colors duration-200 border border-[#5a9fd4] hover:border-[#4a8bc2]">
        <x-icons-back class="w-4 h-4 text-white" />
        Back
    </a>


<form action="{{ route('analytics.material.export_excel') }}" method="GET" class="inline-block">
    <input type="hidden" name="line" value="{{ $line }}">
    <input type="hidden" name="month" value="{{ $monthNumber }}"> {{-- ✅ numeric month --}}
    <input type="hidden" name="date" value="{{ $year }}">

    <button type="submit"
        class="inline-flex items-center justify-center gap-1 p-2 bg-[#5bb75b] hover:bg-[#42a542] border border-[#42a542] text-white text-sm font-medium">
        <x-icons-pdf class="w-4 h-4" />
        Excel
    </button>
</form>

</div>


    <!-- 📈 Chart + Table Section (wider) -->
    <div class="w-full bg-white rounded-sm border border-gray-200 p-6 mb-8 shadow-md space-y-5 transition-all duration-300 hover:shadow-xl hover:border-[#E5E7EB] flex flex-col">
        <h2 class="text-lg font-semibold mb-4 text-[#2d326b]">Material Efficiency</h2>

        <canvas id="efficiencyChart" height="300" class="w-full !pl-0 !ml-0 !translate-x-0"></canvas>

@php
    // Initialize daily buckets for each indicator
    $dailyProduction = array_fill(1, 31, 0);
    $dailyTargetEfficiency = array_fill(1, 31, '1.00%'); // Assuming static
    $dailyPreforms = array_fill(1, 31, '0.00%');
    $dailyCaps = array_fill(1, 31, '0.00%');
    $dailyLabels = array_fill(1, 31, '0.00%');
    $dailyLdpe = array_fill(1, 31, '0.00%');

    foreach ($reports as $report) {
        $day = \Carbon\Carbon::parse($report->production_date)->day;

        $output = $report->total_outputCase ?? 0;
        $bottlesPerCase = $report->standard->bottles_per_case ?? 0;
        $fgUsage = $output * $bottlesPerCase;

        $groupedRejects = $report->lineQcRejects->groupBy(fn($r) => $r->defect->category);
        $qaSamples = 0; // Adjust if dynamic

        $preformRejects = ($groupedRejects['Bottle'] ?? collect())->sum('quantity');
        $capsRejects = ($groupedRejects['Caps'] ?? collect())->sum('quantity');
        $labelRejects = ($groupedRejects['Label'] ?? collect())->sum('quantity');
        $ldpeRejects = ($groupedRejects['LDPE Shrinkfilm'] ?? collect())->sum('quantity');

        $calculatePercentage = function($rejects) use ($fgUsage, $qaSamples) {
            return ($fgUsage + $rejects + $qaSamples) > 0
                ? number_format(($rejects / ($fgUsage + $rejects + $qaSamples)) * 100, 2) . '%'
                : '0.00%';
        };

        $dailyProduction[$day] += $output;
        $dailyPreforms[$day] = $calculatePercentage($preformRejects);
        $dailyCaps[$day] = $calculatePercentage($capsRejects);
        $dailyLabels[$day] = $calculatePercentage($labelRejects);
        $dailyLdpe[$day] = $calculatePercentage($ldpeRejects);
    }
@endphp

@php
    $monthlyOutput = array_fill(1, 12, 0);
    $monthlyPreforms = array_fill(1, 12, '0.00%');
    $monthlyCaps = array_fill(1, 12, '0.00%');
    $monthlyLabels = array_fill(1, 12, '0.00%');
    $monthlyLdpe = array_fill(1, 12, '0.00%');

    foreach ($reports as $report) {
        $month = \Carbon\Carbon::parse($report->production_date)->month;

        $output = $report->total_outputCase ?? 0;
        $bottles = $report->standard->bottles_per_case ?? 0;
        $fgPreform = $output * $bottles;
        $fgLdpe = $output; // LDPE only uses output
        $qa = 0;

        $grouped = $report->lineQcRejects->groupBy(fn($r) => $r->defect->category);
        $rejPreform = ($grouped['Bottle'] ?? collect())->sum('quantity');
        $rejCaps = ($grouped['Caps'] ?? collect())->sum('quantity');
        $rejLabels = ($grouped['Label'] ?? collect())->sum('quantity');
        $rejLdpe = ($grouped['LDPE Shrinkfilm'] ?? collect())->sum('quantity');

        $totalPreform = $fgPreform + $qa + $rejPreform;
        $totalCaps = $fgPreform + $qa + $rejCaps;
        $totalLabels = $fgPreform + $qa + $rejLabels;
        $totalLdpe = $fgLdpe + $qa + $rejLdpe;

        // Aggregate values
        $monthlyOutput[$month] += $output;

        if ($totalPreform > 0) {
            $monthlyPreforms[$month] = number_format(($rejPreform / $totalPreform) * 100, 2) . '%';
        }
        if ($totalCaps > 0) {
            $monthlyCaps[$month] = number_format(($rejCaps / $totalCaps) * 100, 2) . '%';
        }
        if ($totalLabels > 0) {
            $monthlyLabels[$month] = number_format(($rejLabels / $totalLabels) * 100, 2) . '%';
        }
        if ($totalLdpe > 0) {
            $monthlyLdpe[$month] = number_format(($rejLdpe / $totalLdpe) * 100, 2) . '%';
        }
    }
@endphp

<!-- 📋 Daily Material Utilization Summary Table -->
<div class="overflow-x-auto mt-4">
    <table class="min-w-full text-[10px] border border-gray-300 text-gray-700 table-auto">
        <thead class="bg-[#f1f5f9] font-semibold text-gray-800 text-center">
            <tr>
                <th class="border border-gray-300 px-2 py-1 text-left w-[130px]">Indicator</th>
                @foreach (range(1, 31) as $day)
                    <th class="border border-gray-300 px-2 py-1 text-right">{{ $day }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody class="text-[9px]">
            <!-- Weekly Trend (Fixed) -->
            <tr>
                <td class="border border-gray-300 px-2 py-1 font-semibold text-left text-red-600">Weekly Trend</td>
                @foreach (range(1, 31) as $day)
                    <td class="border border-gray-300 px-2 py-1 text-right">1.00%</td>
                @endforeach
            </tr>

            <!-- Target Efficiency (Fixed) -->
            <tr>
                <td class="border border-gray-300 px-2 py-1 font-semibold text-left text-green-600">Target MAT Efficiency, %</td>
                @foreach (range(1, 31) as $day)
                    <td class="border border-gray-300 px-2 py-1 text-right">{{ $dailyTargetEfficiency[$day] }}</td>
                @endforeach
            </tr>

            <!-- Production Output -->
            <tr>
                <td class="border px-2 py-1 text-left font-semibold text-[#0f766e]">
                    Production <br><span class="text-[8px]">(Output, Cs)</span>
                </td>
                @foreach ($dailyProduction as $value)
                    <td class="border px-2 py-1 text-right">{{ number_format($value) }}</td>
                @endforeach
            </tr>

            <!-- PREFORMS -->
            <tr>
                <td class="border px-2 py-1 text-left font-semibold text-[#1e3a8a]">PREFORMS</td>
                @foreach ($dailyPreforms as $value)
                    <td class="border px-2 py-1 text-right">{{ $value }}</td>
                @endforeach
            </tr>

            <!-- CAPS -->
            <tr>
                <td class="border px-2 py-1 text-left font-semibold text-[#334155]">CAPS</td>
                @foreach ($dailyCaps as $value)
                    <td class="border px-2 py-1 text-right">{{ $value }}</td>
                @endforeach
            </tr>

            <!-- LABELS -->
            <tr>
                <td class="border px-2 py-1 text-left font-semibold text-[#0f172a]">OPP LABELS</td>
                @foreach ($dailyLabels as $value)
                    <td class="border px-2 py-1 text-right">{{ $value }}</td>
                @endforeach
            </tr>

            <!-- LDPE -->
            <tr>
                <td class="border px-2 py-1 text-left font-semibold text-[#b45309]">LDPE Shrinkfilm</td>
                @foreach ($dailyLdpe as $value)
                    <td class="border px-2 py-1 text-right">{{ $value }}</td>
                @endforeach
            </tr>
        </tbody>
    </table>
</div>

    </div>

    <!-- Weekly-To-Date Report Section -->
<div x-data="{ showWeekly: false }" class="w-full mb-4">
    <!-- Toggle Button -->
    <div class="flex items-center justify-between cursor-pointer bg-white rounded-sm border border-gray-200 p-4 shadow-md hover:shadow-xl hover:border-[#E5E7EB]" @click="showWeekly = !showWeekly">
        <p class="text-lg text-[#23527c] font-semibold">Weekly Report</p>
        <svg :class="showWeekly ? 'rotate-180' : ''" class="w-5 h-5 transition-transform text-[#23527c]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </div>
    <!-- Content to show/hide -->
    <div x-show="showWeekly" x-transition>
        <div class="bg-white rounded-sm border border-gray-200 p-6 shadow-md space-y-4 mt-2">
                <table class="w-full text-xs text-left border border-[#E5E7EB] border-collapse">
                    <thead class="text-[8px] text-white uppercase bg-[#35408e]">
                        <tr>
                            <th class="p-2 border"></th>
                            <th class="p-2 border text-center"></th>
                            <th colspan="4" class="p-2 border bg-[#6c6c6c] text-center">Preforms</th>
                            <th colspan="4" class="p-2 border bg-[#1f3661] text-center">Caps</th>
                            <th colspan="4" class="p-2 border bg-[#4d642d] text-center">OPP Label</th>
                            <th colspan="4" class="p-2 border bg-[#8a2e2e] text-center">LDPE Shrinkfilm</th>
                        </tr>
                        <tr>
                            <th class="p-2 border text-center">Week</th>
                            
                            <th class="p-2 border text-center">Production Output</th>

                            <th class="p-2 border text-center">FG Usage</th>
                            <th class="p-2 border text-center">Rejects</th>
                            <th class="p-2 border text-center">QA Samples</th>
                            <th class="p-2 border text-center">% Rejects</th>

                            <th class="p-2 border text-center">FG Usage</th>
                            <th class="p-2 border text-center">Rejects</th>
                            <th class="p-2 border text-center">QA Samples</th>
                            <th class="p-2 border text-center">% Rejects</th>

                            <th class="p-2 border text-center">FG Usage</th>
                            <th class="p-2 border text-center">Rejects</th>
                            <th class="p-2 border text-center">QA Samples</th>
                            <th class="p-2 border text-center">% Rejects</th>

                            <th class="p-2 border text-center">FG Usage</th>
                            <th class="p-2 border text-center">Rejects</th>
                            <th class="p-2 border text-center">QA Samples</th>
                            <th class="p-2 border text-center">% Rejects</th>
                        </tr>
                    </thead>
        <tbody>
        @foreach ($weeklyData as $data)
            <tr class="text-[10px] text-gray-700 whitespace-nowrap bg-white hover:bg-[#e5f4ff]">
                <td class="p-2 border text-center font-semibold text-[#2d326b]">{{ $data['week'] }}</td>
                <td class="p-2 border text-center">{{ number_format($data['output']) }}</td>

                {{-- Preforms --}}
                <td class="p-2 border text-center">{{ number_format($data['preform']['fg']) }}</td>
                <td class="p-2 border text-center">{{ number_format($data['preform']['rej']) }}</td>
                <td class="p-2 border text-center">{{ $data['preform']['qa'] ?? 0 }}</td>
                <td class="p-2 border text-center">{{ $data['preform']['percent'] }}</td>

                {{-- Caps --}}
                <td class="p-2 border text-center">{{ number_format($data['caps']['fg']) }}</td>
                <td class="p-2 border text-center">{{ number_format($data['caps']['rej']) }}</td>
                <td class="p-2 border text-center">{{ $data['caps']['qa'] ?? 0 }}</td>
                <td class="p-2 border text-center">{{ $data['caps']['percent'] }}</td>

                {{-- Labels --}}
                <td class="p-2 border text-center">{{ number_format($data['label']['fg']) }}</td>
                <td class="p-2 border text-center">{{ number_format($data['label']['rej']) }}</td>
                <td class="p-2 border text-center">{{ $data['label']['qa'] ?? 0 }}</td>
                <td class="p-2 border text-center">{{ $data['label']['percent'] }}</td>

                {{-- LDPE Shrinkfilm --}}
                <td class="p-2 border text-center">{{ number_format($data['ldpe']['fg']) }}</td>
                <td class="p-2 border text-center">{{ number_format($data['ldpe']['rej']) }}</td>
                <td class="p-2 border text-center">0</td>
                <td class="p-2 border text-center">{{ $data['ldpe']['percent'] }}</td>
            </tr>
        @endforeach
        </tbody>

                </table>
            </div>
        </div>
</div>

<div class="w-full mb-4 bg-white rounded-sm border border-gray-300 p-6 shadow-xl">
    <!-- Header -->
    <div>
        <p class="text-lg text-[#23527c] font-semibold">Production Report</p>
    </div>

<!-- Scrollable Container -->
<div class="mt-5 overflow-x-auto">
    <table class="min-w-[1600px] text-xs text-left border border-[#E5E7EB] border-collapse">
        <thead class="text-[8px] text-white uppercase bg-[#35408e]">
            <tr>
                <th class="p-2 border w-[120px] whitespace-nowrap"></th>
                <th colspan="4" class="p-2 border text-center"></th>
                <th colspan="5" class="p-2 border bg-[#6c6c6c] text-center">Preforms</th>
                <th colspan="5" class="p-2 border bg-[#1f3661] text-center">Caps</th>
                <th colspan="5" class="p-2 border bg-[#4d642d] text-center">OPP Label</th>
                <th colspan="5" class="p-2 border bg-[#8a2e2e] text-center">LDPE Shrinkfilm</th>
            </tr>
            <tr>
                <th class="p-2 border text-center w-[120px] whitespace-nowrap">Production Date</th>
                <th class="p-2 border text-center w-[180px] whitespace-nowrap">SKU</th>
                <th class="p-2 border text-center w-[100px] whitespace-nowrap">Bottle per Case</th>
                <th class="p-2 border text-center w-[160px] whitespace-nowrap">Target Mat'l Efficiency, %</th>
                <th class="p-2 border text-center w-[130px] whitespace-nowrap">Production Output</th>

                @for ($i = 0; $i < 4; $i++)
                    <th class="p-2 border text-center w-[120px] whitespace-nowrap">Description</th>
                    <th class="p-2 border text-center w-[120px] whitespace-nowrap">FG Usage</th>
                    <th class="p-2 border text-center w-[100px] whitespace-nowrap">Rejects</th>
                    <th class="p-2 border text-center w-[110px] whitespace-nowrap">QA Samples</th>
                    <th class="p-2 border text-center w-[100px] whitespace-nowrap">% Rejects</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $report)
                @php
                    $productionDate = \Carbon\Carbon::parse($report->production_date)->format('n/j/y');
                    $sku = $report->standard->description ?? 'No Run';
                    $bottlesPerCase = $report->standard->bottles_per_case ?? '';
                    $efficiency = '1.00%';
                    $output = $report->total_outputCase ?? '';

                    $fgUsage = $output && $bottlesPerCase ? $output * $bottlesPerCase : 0;
                    $qaSamples = 0;

                    $groupedRejects = $report->lineQcRejects->groupBy(fn($r) => $r->defect->category);
                    $preformRejects = ($groupedRejects['Bottle'] ?? collect())->sum('quantity');
                    $capsRejects = ($groupedRejects['Caps'] ?? collect())->sum('quantity');
                    $labelRejects = ($groupedRejects['Label'] ?? collect())->sum('quantity');
                    $ldpeRejects = ($groupedRejects['LDPE Shrinkfilm'] ?? collect())->sum('quantity');

                    $calcPercent = fn($rejects, $fgUsage, $qa) =>
                        ($rejects + $fgUsage + $qa) > 0 ? number_format(($rejects / ($fgUsage + $rejects + $qa)) * 100, 2) . '%' : '0.00%';

                    // Descriptions from the standard table
                    $preformDesc = $report->standard->preform_weight ?? '';
                    $capsDesc = $report->standard->caps ?? '';
                    $labelDesc = $report->standard->opp_label ?? '';
                    $ldpeDesc = $report->standard->ldpe_size ?? '';
                
                @endphp

                <tr class="text-[10px] text-gray-700 whitespace-nowrap bg-white hover:bg-[#e5f4ff]">
                    <td class="border p-2 text-center">{{ $productionDate }}</td>
                    <td class="border p-2 text-center">{{ $sku }}</td>
                    <td class="border p-2 text-center">{{ $bottlesPerCase }}</td>
                    <td class="border p-2 text-center">{{ $efficiency }}</td>
                    <td class="border p-2 text-center">{{ $output }}</td>

                    {{-- PREFORMS --}}
                    <td class="border p-2 text-center">{{ $preformDesc }}</td>
                    <td class="border p-2 text-center">{{ $fgUsage }}</td>
                    <td class="border p-2 text-center">{{ $preformRejects }}</td>
                    <td class="border p-2 text-center">{{ $report->total_sample ?? 0 }}</td>
                    <td class="border p-2 text-center">{{ $calcPercent($preformRejects, $fgUsage, $qaSamples) }}</td>

                    {{-- CAPS --}}
                    <td class="border p-2 text-center">{{ $capsDesc }}</td>
                    <td class="border p-2 text-center">{{ $fgUsage }}</td>
                    <td class="border p-2 text-center">{{ $capsRejects }}</td>
                    <td class="border p-2 text-center">{{ $report->total_sample ?? 0 }}</td>
                    <td class="border p-2 text-center">{{ $calcPercent($capsRejects, $fgUsage, $qaSamples) }}</td>

                    {{-- OPP LABEL --}}
                    <td class="border p-2 text-center">{{ $labelDesc }}</td>
                    <td class="border p-2 text-center">{{ $fgUsage }}</td>
                    <td class="border p-2 text-center">{{ $labelRejects }}</td>
                    <td class="border p-2 text-center">{{ $report->with_label ?? 0 }}</td>
                    <td class="border p-2 text-center">{{ $calcPercent($labelRejects, $fgUsage, $qaSamples) }}</td>

                    {{-- LDPE SHRINKFILM --}}
                    <td class="border p-2 text-center">{{ $ldpeDesc }}</td>
                    <td class="border p-2 text-center">{{ $fgUsage }}</td>
                    <td class="border p-2 text-center">{{ $ldpeRejects }}</td>
                    <td class="border p-2 text-center">0</td>
                    <td class="border p-2 text-center">{{ $calcPercent($ldpeRejects, $fgUsage, $qaSamples) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

</div>

@php
$labels = range(1, 31);
$targetData = array_fill(1, 31, 0.01); // 1.00%
$preformData = array_fill(1, 31, 0);
$capsData = array_fill(1, 31, 0);
$labelsData = array_fill(1, 31, 0);
$ldpeData = array_fill(1, 31, 0);

foreach ($reports as $report) {
    $day = \Carbon\Carbon::parse($report->production_date)->day;
    $output = $report->total_outputCase ?? 0;
    $bottles = $report->standard->bottles_per_case ?? 0;
    $fgUsage = $output * $bottles;
    $qa = 0;

    $grouped = $report->lineQcRejects->groupBy(fn($r) => $r->defect->category);

    $calc = function ($rej, $fg, $qa) {
        return ($rej + $fg + $qa) > 0 ? round($rej / ($rej + $fg + $qa), 4) : 0;
    };

    $preformData[$day] = $calc(($grouped['Bottle'] ?? collect())->sum('quantity'), $fgUsage, $qa);
    $capsData[$day] = $calc(($grouped['Caps'] ?? collect())->sum('quantity'), $fgUsage, $qa);
    $labelsData[$day] = $calc(($grouped['Label'] ?? collect())->sum('quantity'), $fgUsage, $qa);
    $ldpeData[$day] = $calc(($grouped['LDPE Shrinkfilm'] ?? collect())->sum('quantity'), $output, $qa);
}
@endphp

<!-- 📈 Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const efficiencyLabels = @json(range(1, 31));
const targetEfficiencyData = @json(array_values($targetData));
const preformData = @json(array_values($preformData));
const capsData = @json(array_values($capsData));
const oppLabelData = @json(array_values($labelsData));
const ldpeData = @json(array_values($ldpeData));

    const ctx = document.getElementById('efficiencyChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: efficiencyLabels,
            datasets: [
                {
                    label: 'Target MAT Efficiency, %',
                    data: targetEfficiencyData,
                    borderColor: '#10B981',
                    borderDash: [4, 4],
                    tension: 0.3,
                    fill: false,
                    borderWidth: 1.5,
                    pointRadius: 3,
                    pointHoverRadius: 5
                },
                {
                    label: 'PREFORMS',
                    data: preformData,
                    borderColor: '#1e3a8a',
                    tension: 0.3,
                    fill: false,
                    borderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 5
                },
                {
                    label: 'CAPS',
                    data: capsData,
                    borderColor: '#334155',
                    tension: 0.3,
                    fill: false,
                    borderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 5
                },
                {
                    label: 'OPP LABELS',
                    data: oppLabelData,
                    borderColor: '#0f172a',
                    tension: 0.3,
                    fill: false,
                    borderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 5
                },
                {
                    label: 'LDPE SHRINK FILM',
                    data: ldpeData,
                    borderColor: '#b45309',
                    tension: 0.3,
                    fill: false,
                    borderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 5
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        boxWidth: 12,
                        font: { size: 10 }
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function (context) {
                            return context.dataset.label + ': ' + (context.parsed.y * 100).toFixed(2) + '%';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    suggestedMax: 0.012,
                    ticks: {
                        callback: function (value) {
                            return (value * 100).toFixed(2) + '%';
                        }
                    },
                    title: {
                        display: true,
                        text: 'Efficiency (%)',
                        font: { size: 10 }
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Day of Month',
                        font: { size: 10 }
                    },
                    ticks: {
                        align: 'start'
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>


@endsection
