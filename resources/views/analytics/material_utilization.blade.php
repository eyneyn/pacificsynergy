@extends('layouts.app')

@section('content')

<h2 class="text-xl mt-6 mb-6 font-semibold text-[#2d326b] tracking-wider mb-4> text-center">MATERIAL UTILIZATION REPORT</h2>

<!-- Heading -->
<h2 class="text-lg font-semibold text-[#2d326b] tracking-wider mb-4">
    Production Year {{ request('date', now()->year) }}
</h2>

<div class="flex flex-col md:flex-row md:items-center justify-between mb-4">
    <!-- Year Selection -->
    <div>
        <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2 text-[#2d326b]">
            <label for="date" class="whitespace-nowrap text-sm">Production Year:</label>
            <x-select-year 
                name="date" 
                placeholder="Select Year" 
                :options="collect($availableYears)->mapWithKeys(fn($v) => [$v => $v])->toArray()" 
                :selected="$year"
                onchange="this.form.submit()" />
            @if(request('line'))
                <input type="hidden" name="line" value="{{ request('line') }}">
            @endif
        </form>
    </div>

    <!-- Export Button -->
    <div class="flex flex-col md:flex-row items-start md:items-center gap-3">
        <form method="POST" action="" target="_blank">
            @csrf
            <input type="hidden" name="report_id" value="">
            <button type="submit"
                class="text-center px-3 py-2 bg-[#323B76] border border-[#444d90] hover:bg-[#444d90] text-white text-xs font-medium rounded-md shadow-sm transition duration-200">
                Export to Excel
            </button>
        </form>
    </div>
</div>

<!-- Divider -->
<div class="w-full flex items-center justify-center my-6">
    <div class="w-full border-t border-[#E5E7EB]"></div>
</div>


<!-- Chart + Cards Layout -->
<div class="w-full flex flex-col xl:flex-row gap-4 mb-8">
    <!-- Chart + Table Section -->
    <div class="w-full xl:w-3/4 bg-white rounded-sm border border-gray-200 p-6 shadow-md space-y-5 transition-all duration-300 hover:shadow-xl hover:border-[#E5E7EB] flex flex-col">
        <h2 class="text-lg font-semibold mb-4 text-[#2d326b]">Material Efficiency</h2>
        <canvas id="efficiencyChart" height="300" class="w-full !pl-0 !ml-0 !translate-x-0"></canvas>
        <!-- Table -->
        <div class="overflow-x-auto mt-4">
            <table class="min-w-full text-[10px] border border-gray-300 text-gray-700 table-auto">
                <thead class="bg-[#f1f5f9] font-semibold text-gray-800 text-center">
                    <tr>
                        <th class="border border-gray-300 px-2 py-1 text-left w-[130px]">Indicator</th>
                        @foreach (range(1,12) as $month)
                            <th class="border border-gray-300 px-2 py-1 text-right">{{ $month }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="text-[9px]">
                    <tr>
                        <td class="border border-gray-300 px-2 py-1 font-semibold text-left text-red-600">Weekly Trend</td>
                        @foreach (array_fill(0, 12, '1.00%') as $item)
                            <td class="border border-gray-300 px-2 py-1 text-right">{{ $item }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="border border-gray-300 px-2 py-1 font-semibold text-left text-green-600">Target MAT Efficiency, %</td>
                        @foreach (array_fill(0, 12, '1.00%') as $item)
                            <td class="border border-gray-300 px-2 py-1 text-right">{{ $item }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="border border-gray-300 px-2 py-1 font-semibold text-left text-[#0f766e]">Production <br><span class="text-[8px]">(Output, Cs)</span></td>
                        @foreach ($monthlyProduction as $item)
                            <td class="border border-gray-300 px-2 py-1 text-right">{{ number_format($item) }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="border border-gray-300 px-2 py-1 font-semibold text-left text-[#1e3a8a]">PREFORMS</td>
                        @foreach ($preformRejectRates as $item)
                            <td class="border border-gray-300 px-2 py-1 text-right">{{ $item }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="border border-gray-300 px-2 py-1 font-semibold text-left text-[#334155]">CAPS</td>
                        @foreach ($capsRejectRates as $item)
                            <td class="border border-gray-300 px-2 py-1 text-right">{{ $item }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="border border-gray-300 px-2 py-1 font-semibold text-left text-[#0f172a]">OPP LABELS</td>
                        @foreach ($oppRejectRates as $item)
                            <td class="border border-gray-300 px-2 py-1 text-right">{{ $item }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="border border-gray-300 px-2 py-1 font-semibold text-left text-[#b45309]">LDPE SHRINK FILM</td>
                        @foreach ($ldpeRejectRates as $item)
                            <td class="border border-gray-300 px-2 py-1 text-right">{{ $item }}</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right-side Cards -->
    <div class="w-full xl:w-1/4 flex flex-col justify-between self-stretch">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 h-full">
            <!-- Total Material Efficiency -->
            <div class="col-span-1 sm:col-span-2 bg-white hover:bg-[#e5f4ff] rounded-xl shadow border border-gray-200 p-3 h-28 flex items-center justify-center">
                <div class="text-center">
                    <h3 class="text-sm text-[#2d326b] mb-1">Material Efficiency</h3>
                    <p class="text-lg font-semibold text-[#4b5563]">{{ $materialEfficiencyRate }}</p>
                </div>
            </div>
            @php
                $cards = [
                    ['title' => 'Preform', 'value' => $preformTotalRate ?? '0.00%', 'color' => '#4b5563'],
                    ['title' => 'Caps', 'value' => $capsTotalRate ?? '0.00%', 'color' => '#1e3a8a'],
                    ['title' => 'OPP', 'value' => $oppTotalRate ?? '0.00%', 'color' => '#166534'],
                    ['title' => 'LDPE', 'value' => $ldpeTotalRate ?? '0.00%', 'color' => '#7c2d12'],
                ];
            @endphp
            @foreach ($cards as $card)
                <div class="bg-white hover:bg-[#e5f4ff] rounded-xl shadow border border-gray-200 p-3 h-28 flex items-center justify-center">
                    <div class="text-center">
                        <h3 class="text-sm text-[#2d326b] mb-1">{{ $card['title'] }}</h3>
                        <p class="text-lg font-semibold" style="color: {{ $card['color'] }}">{{ $card['value'] }}</p>
                    </div>
                </div>
            @endforeach

<!-- Line Buttons -->
<div class="col-span-1 sm:col-span-2 bg-white rounded-xl shadow border border-gray-200 p-4">
    <h3 class="text-sm text-[#2d326b] text-center mb-3 font-semibold">Select Production Line</h3>
    <div class="grid grid-cols-3 gap-2">
        @foreach ($lines as $lineOption)
            <a href="{{ route('analytics.material.index', [
                'line' => $lineOption,
                'date' => $year // This ensures the selected year is preserved
            ]) }}"
            class="text-xs text-[#2d326b] text-center border border-gray-300 rounded-md py-1 px-2 hover:bg-[#2d326b] hover:text-white transition duration-150">
                Line {{ $lineOption }}
            </a>
        @endforeach
    </div>
</div>


        </div>
    </div>
</div>

<!-- Quarter-To-Date Report Section -->
<div x-data="{ showQuarter: false }" class="w-full mb-4">
    <!-- Toggle Button -->
    <div class="flex items-center justify-between cursor-pointer bg-white rounded-sm border border-gray-200 p-4 shadow-md hover:shadow-xl hover:border-[#E5E7EB]" @click="showQuarter = !showQuarter">
        <p class="text-lg text-[#2d326b] font-semibold">Quarter-To-Date Report</p>
        <svg :class="showQuarter ? 'rotate-180' : ''" class="w-5 h-5 transition-transform text-[#2d326b]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </div>
    <!-- Content to show/hide -->
    <div x-show="showQuarter" x-transition>
        <div class="bg-white rounded-sm border border-gray-200 p-6 shadow-md space-y-4 mt-2">
            <!-- Production Output Table -->
            <table class="w-full text-xs text-left border border-[#E5E7EB] border-collapse">
                <thead class="text-xs text-white uppercase bg-[#35408e]">
                    <tr>
                        <th class="p-2 border border-[#d9d9d9]"></th>
                        <th class="p-2 border border-[#d9d9d9] text-center">Q1</th>
                        <th class="p-2 border border-[#d9d9d9] text-center">Q2</th>
                        <th class="p-2 border border-[#d9d9d9] text-center">Q3</th>
                        <th class="p-2 border border-[#d9d9d9] text-center">Q4</th>
                        <th class="p-2 border border-[#d9d9d9] text-center">YTD</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-white hover:bg-[#e5f4ff] cursor-pointer">
                        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">Production Output</td>
                        <td class="p-2 border border-[#d9d9d9] text-center">{{ number_format($quarterlyProduction['Q1']) }}</td>
                        <td class="p-2 border border-[#d9d9d9] text-center">{{ number_format($quarterlyProduction['Q2']) }}</td>
                        <td class="p-2 border border-[#d9d9d9] text-center">{{ number_format($quarterlyProduction['Q3']) }}</td>
                        <td class="p-2 border border-[#d9d9d9] text-center">{{ number_format($quarterlyProduction['Q4']) }}</td>
                        <td class="p-2 border border-[#d9d9d9] text-center font-semibold text-[#1e3a8a]">{{ number_format($totalAnnualProduction) }}</td>
                    </tr>
                </tbody>
            </table>
            <!-- Preforms Quarterly Summary Table -->
            @foreach (['Preforms' => [
                'FgUsage' => $preformQuarterFgUsage,
                'Rejects' => $preformQuarterRejects,
                'QaSamples' => $preformQuarterQaSamples,
                'RejectRates' => $preformQuarterRejectRates,
                'TotalFg' => $preformTotalFg,
                'TotalRej' => $preformTotalRej,
                'TotalQa' => $preformTotalQa,
                'TotalRate' => $preformTotalRate
            ], 'Caps' => [
                'FgUsage' => $capsQuarterFgUsage,
                'Rejects' => $capsQuarterRejects,
                'QaSamples' => $capsQuarterQaSamples,
                'RejectRates' => $capsQuarterRejectRates,
                'TotalFg' => $capsTotalFg,
                'TotalRej' => $capsTotalRej,
                'TotalQa' => $capsTotalQa,
                'TotalRate' => $capsTotalRate
            ], 'OPP Labels' => [
                'FgUsage' => $oppQuarterFgUsage,
                'Rejects' => $oppQuarterRejects,
                'QaSamples' => $oppQuarterQaSamples,
                'RejectRates' => $oppQuarterRejectRates,
                'TotalFg' => $oppTotalFg,
                'TotalRej' => $oppTotalRej,
                'TotalQa' => $oppTotalQa,
                'TotalRate' => $oppTotalRate
            ], 'LDPE Shrinkfilm' => [
                'FgUsage' => $ldpeQuarterFgUsage,
                'Rejects' => $ldpeQuarterRejects,
                'QaSamples' => $ldpeQuarterQaSamples,
                'RejectRates' => $ldpeQuarterRejectRates,
                'TotalFg' => $ldpeTotalFg,
                'TotalRej' => $ldpeTotalRej,
                'TotalQa' => $ldpeTotalQa,
                'TotalRate' => $ldpeTotalRate
            ]] as $label => $data)
            <div class="mt-6">
                <table class="w-full text-xs text-left border border-[#E5E7EB] border-collapse">
                    <thead class="uppercase bg-[#35408e] text-white">
                        <tr>
                            <th class="p-2 border border-[#d9d9d9]">{{ $label }}</th>
                            <th class="p-2 border border-[#d9d9d9] text-center">Q1</th>
                            <th class="p-2 border border-[#d9d9d9] text-center">Q2</th>
                            <th class="p-2 border border-[#d9d9d9] text-center">Q3</th>
                            <th class="p-2 border border-[#d9d9d9] text-center">Q4</th>
                            <th class="p-2 border border-[#d9d9d9] text-center">YTD</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="bg-white hover:bg-[#f1f5f9] transition">
                            <td class="p-2 border text-[#2d326b]">FG Usage</td>
                            <td class="p-2 border text-center">{{ number_format($data['FgUsage']['Q1']) }}</td>
                            <td class="p-2 border text-center">{{ number_format($data['FgUsage']['Q2']) }}</td>
                            <td class="p-2 border text-center">{{ number_format($data['FgUsage']['Q3']) }}</td>
                            <td class="p-2 border text-center">{{ number_format($data['FgUsage']['Q4']) }}</td>
                            <td class="p-2 border text-center font-semibold text-[#1e3a8a]">{{ number_format($data['TotalFg']) }}</td>
                        </tr>
                        <tr class="bg-white hover:bg-[#f1f5f9] transition">
                            <td class="p-2 border text-[#2d326b]">Rejects</td>
                            <td class="p-2 border text-center">{{ number_format($data['Rejects']['Q1']) }}</td>
                            <td class="p-2 border text-center">{{ number_format($data['Rejects']['Q2']) }}</td>
                            <td class="p-2 border text-center">{{ number_format($data['Rejects']['Q3']) }}</td>
                            <td class="p-2 border text-center">{{ number_format($data['Rejects']['Q4']) }}</td>
                            <td class="p-2 border text-center font-semibold text-[#1e3a8a]">{{ number_format($data['TotalRej']) }}</td>
                        </tr>
                        <tr class="bg-white hover:bg-[#f1f5f9] transition">
                            <td class="p-2 border text-[#2d326b]">QA Samples</td>
                            <td class="p-2 border text-center">{{ number_format($data['QaSamples']['Q1']) }}</td>
                            <td class="p-2 border text-center">{{ number_format($data['QaSamples']['Q2']) }}</td>
                            <td class="p-2 border text-center">{{ number_format($data['QaSamples']['Q3']) }}</td>
                            <td class="p-2 border text-center">{{ number_format($data['QaSamples']['Q4']) }}</td>
                            <td class="p-2 border text-center font-semibold text-[#1e3a8a]">{{ number_format($data['TotalQa']) }}</td>
                        </tr>
                        <tr class="bg-white hover:bg-[#f1f5f9] transition">
                            <td class="p-2 border text-[#2d326b]">% Rejects</td>
                            <td class="p-2 border text-center">{{ $data['RejectRates']['Q1'] }}</td>
                            <td class="p-2 border text-center">{{ $data['RejectRates']['Q2'] }}</td>
                            <td class="p-2 border text-center">{{ $data['RejectRates']['Q3'] }}</td>
                            <td class="p-2 border text-center">{{ $data['RejectRates']['Q4'] }}</td>
                            <td class="p-2 border text-center font-semibold text-[#1e3a8a]">{{ $data['TotalRate'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Month-To-Date Report Section -->
<div x-data="{ showMonth: false }" class="w-full mb-4">
    <!-- Toggle Button -->
    <div class="flex items-center justify-between cursor-pointer bg-white rounded-sm border border-gray-200 p-4 shadow-md hover:shadow-xl hover:border-[#E5E7EB]" @click="showMonth = !showMonth">
        <p class="text-lg text-[#2d326b] font-semibold">Month-To-Date Report</p>
        <svg :class="showMonth ? 'rotate-180' : ''" class="w-5 h-5 transition-transform text-[#2d326b]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </div>
    <!-- Content to show/hide -->
    <div x-show="showMonth" x-transition>
        <div class="bg-white rounded-sm border border-gray-200 p-6 shadow-md space-y-4 mt-2">
            <!-- Production Output Table -->
            <table class="w-full text-xs text-left border border-[#E5E7EB] border-collapse">
                <thead class="text-xs text-white uppercase bg-[#35408e]">
                    <tr>
                        <th class="p-2 border border-[#d9d9d9]">Production output</th>
                        @for ($i = 1; $i <= 12; $i++)
                            <th class="p-2 border border-[#d9d9d9] text-center">{{ $i }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-white hover:bg-[#f1f5f9] cursor-pointer">
                        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">Target Mat'l Efficiency, %</td>
                        @for ($i = 1; $i <= 12; $i++)
                            <td class="p-2 border border-[#d9d9d9] text-center">1.00%</td>
                        @endfor
                    </tr>
                    <tr class="bg-white hover:bg-[#f1f5f9] cursor-pointer">
                        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">Production Output, Cs</td>
                        @for ($i = 1; $i <= 12; $i++)
                            <td class="p-2 border border-[#d9d9d9] text-center">
                                {{ number_format($monthlyProduction[$i]) }}
                            </td>
                        @endfor
                    </tr>
                </tbody>
            </table>
            <!-- Preforms Table -->
            @foreach ([
                ['label' => 'Preforms', 'FgUsage' => $monthlyFgUsage, 'Rejects' => $monthlyRejects, 'QaSamples' => $monthlyQaSamples, 'RejectRates' => $monthlyRejectRates],
                ['label' => 'Caps', 'FgUsage' => $capsFgUsage, 'Rejects' => $capsRejects, 'QaSamples' => $capsQaSamples, 'RejectRates' => $capsRejectRates],
                ['label' => 'OPP Labels', 'FgUsage' => $oppFgUsage, 'Rejects' => $oppRejects, 'QaSamples' => $oppQaSamples, 'RejectRates' => $oppRejectRates],
                ['label' => 'LDPE Shrinkfilm', 'FgUsage' => $ldpeFgUsage, 'Rejects' => $ldpeRejects, 'QaSamples' => $ldpeQaSamples, 'RejectRates' => $ldpeRejectRates],
            ] as $mat)
            <table class="w-full mt-4 text-[11px] text-left border border-[#E5E7EB] border-collapse">
                <thead class="uppercase bg-[#35408e] text-white">
                    <tr>
                        <th class="p-2 border border-[#d9d9d9] text-center">{{ $mat['label'] }}</th>
                        @for ($i = 1; $i <= 12; $i++)
                            <th class="p-2 border border-[#d9d9d9] text-center">{{ $i }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-white hover:bg-[#f1f5f9] transition">
                        <td class="p-2 border text-[#2d326b]">FG Usage</td>
                        @foreach ($mat['FgUsage'] as $value)
                            <td class="p-2 border text-center">{{ number_format($value) }}</td>
                        @endforeach
                    </tr>
                    <tr class="bg-white hover:bg-[#f1f5f9] transition">
                        <td class="p-2 border text-[#2d326b]">Rejects</td>
                        @foreach ($mat['Rejects'] as $value)
                            <td class="p-2 border text-center">{{ number_format($value) }}</td>
                        @endforeach
                    </tr>
                    <tr class="bg-white hover:bg-[#f1f5f9] transition">
                        <td class="p-2 border text-[#2d326b]">QA Samples</td>
                        @foreach ($mat['QaSamples'] as $value)
                            <td class="p-2 border text-center">{{ number_format($value) }}</td>
                        @endforeach
                    </tr>
                    <tr class="bg-white hover:bg-[#f1f5f9] transition">
                        <td class="p-2 border text-[#2d326b]">% Rejects</td>
                        @foreach ($mat['RejectRates'] as $value)
                            <td class="p-2 border text-center">{{ $value }}</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
            @endforeach
        </div>
    </div>
</div>

<!-- Chart.js Data Preparation -->
@php
    $jsPreforms = json_encode(array_map(fn($v) => (float) str_replace('%', '', $v) / 100, $preformRejectRates));
    $jsCaps = json_encode(array_map(fn($v) => (float) str_replace('%', '', $v) / 100, $capsRejectRates));
    $jsOpp = json_encode(array_map(fn($v) => (float) str_replace('%', '', $v) / 100, $oppRejectRates));
    $jsLdpe = json_encode(array_map(fn($v) => (float) str_replace('%', '', $v) / 100, $ldpeRejectRates));
@endphp

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('efficiencyChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['1','2','3','4','5','6','7','8','9','10','11','12'],
        datasets: [
            {
                label: 'Target MAT Efficiency, %',
                data: Array(12).fill(0.01),
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
                data: {!! $jsPreforms !!},
                borderColor: '#1e3a8a',
                tension: 0.3,
                fill: false,
                borderWidth: 2,
                pointRadius: 3,
                pointHoverRadius: 5
            },
            {
                label: 'CAPS',
                data: {!! $jsCaps !!},
                borderColor: '#334155',
                tension: 0.3,
                fill: false,
                borderWidth: 2,
                pointRadius: 3,
                pointHoverRadius: 5
            },
            {
                label: 'OPP LABELS',
                data: {!! $jsOpp !!},
                borderColor: '#0f172a',
                tension: 0.3,
                fill: false,
                borderWidth: 2,
                pointRadius: 3,
                pointHoverRadius: 5
            },
            {
                label: 'LDPE SHRINK FILM',
                data: {!! $jsLdpe !!},
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
                    label: function(context) {
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
                    callback: function(value) {
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
                offset: false,
                title: {
                    display: true,
                    text: 'Month',
                    font: { size: 10 }
                },
                grid: {
                    drawTicks: true,
                    drawBorder: true
                },
                ticks: {
                    align: 'start'
                }
            }
        }
    }
});
</script>
@endsection