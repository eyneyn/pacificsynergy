@extends('layouts.app')

@section('content')

{{-- Page Title --}}
<h2 class="text-xl mb-2 font-bold text-[#23527c]">Material Utilization Report</h2>

{{-- Back to Configuration Link --}}
<a href="{{ url('analytics/index') }}" class="text-xs text-gray-500 hover:text-[#23527c] mb-4 inline-flex items-center">
    <x-icons-back-confi/>
    Analytics and Report
</a>

<div class="mx-16 mt-4">
<!-- Heading -->
<h2 class="flex items-center text-xl text-[#23527c] mb-2">
    <svg class="w-6 h-6 mr-2 flex-shrink-0" 
         viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg" fill="#23527c">
        <g id="SVGRepo_iconCarrier">
            <title>filter-horizontal</title>
            <g id="Layer_2" data-name="Layer 2">
                <g id="invisible_box" data-name="invisible box">
                    <rect width="48" height="48" fill="none"></rect>
                </g>
                <g id="icons_Q2" data-name="icons Q2">
                    <path d="M41.8,8H21.7A6.2,6.2,0,0,0,16,4a6,6,0,0,0-5.6,4H6.2A2.1,2.1,0,0,0,4,10a2.1,2.1,0,0,0,2.2,2h4.2A6,6,0,0,0,16,16a6.2,6.2,0,0,0,5.7-4H41.8A2.1,2.1,0,0,0,44,10,2.1,2.1,0,0,0,41.8,8ZM16,12a2,2,0,1,1,2-2A2,2,0,0,1,16,12Z"></path>
                    <path d="M41.8,22H37.7A6.2,6.2,0,0,0,32,18a6,6,0,0,0-5.6,4H6.2a2,2,0,1,0,0,4H26.4A6,6,0,0,0,32,30a6.2,6.2,0,0,0,5.7-4h4.1a2,2,0,1,0,0-4ZM32,26a2,2,0,1,1,2-2A2,2,0,0,1,32,26Z"></path>
                    <path d="M41.8,36H24.7A6.2,6.2,0,0,0,19,32a6,6,0,0,0-5.6,4H6.2a2,2,0,1,0,0,4h7.2A6,6,0,0,0,19,44a6.2,6.2,0,0,0,5.7-4H41.8a2,2,0,1,0,0-4ZM19,40a2,2,0,1,1,2-2A2,2,0,0,1,19,40Z"></path>
                </g>
            </g>
        </g>
    </svg>
    Select analytic report
</h2>


<!-- Divider -->
<div class="w-full flex items-center justify-center mb-6">
    <div class="w-full border-t border-[#E5E7EB]"></div>
</div>


<div class="flex flex-col mb-4 ml-10">
    <form method="GET" action="{{ url()->current() }}" class="flex flex-col gap-4 text-[#23527c] mb-4">
        <!-- Year Selection -->
        <div class="flex items-center gap-6 w-36">
            <label for="date" class="whitespace-nowrap text-sm font-bold">
                Production Year:<span class="text-red-500">*</span>
            </label>
            <x-select-year 
                name="date" 
                :options="collect($availableYears)->mapWithKeys(fn($v) => [$v => $v])->toArray()" 
                :selected="$year"
                class="w-28" />
        </div>

        <!-- Line Selection -->
        <div class="flex items-center gap-6 text-[#23527c]">
            <label class="whitespace-nowrap text-sm font-bold">
                Select Line:<span class="text-red-500">*</span>
            </label>
            <div class="flex gap-6 ml-8">
                @foreach($activeLines as $ln)
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" 
                               name="line" 
                               value="{{ $ln->line_number }}" 
                               {{ (string)$selectedLine === (string)$ln->line_number ? 'checked' : '' }}
                               class="w-4 h-4 rounded-full bg-gray-300 text-blue-600">
                        <span class="text-sm">Line {{ $ln->line_number }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Submit Button Section -->
        <div class="flex gap-4 mt-4">
            <a href="{{ route('analytics.index') }}"
               class="inline-flex items-center px-3 py-2 bg-[#5a9fd4] hover:bg-[#4a8bc2] text-white text-sm font-medium transition-colors duration-200 border border-[#5a9fd4] hover:border-[#4a8bc2]">
                <x-icons-back class="w-4 h-4 text-white" />
                Back
            </a>

            <button type="submit"
                    class="inline-flex items-center justify-center gap-1 bg-[#5bb75b] border border-[#43a143] text-white px-3 py-2 hover:bg-[#42a542] text-sm">
                <x-icons-submit class="w-4 h-4 text-white" />
                Submit
            </button>
        </div>
    </form>
</div>

{{-- ✅ Require year and line before showing analytics --}}
@if($year && $selectedLine)
<h2 class="text-xl font-bold text-[#23527c] mt-4">
    Production Line {{ $selectedLine }} - Year {{ $year }}
</h2>


<!-- Divider -->
<div class="w-full flex items-center justify-center mt-2 mb-8">
    <div class="w-full border-t border-[#E5E7EB]"></div>
</div>

<!-- Chart + Cards Layout -->
<div class="w-full flex flex-col xl:flex-row gap-4 mb-4">
    <!-- Chart + Table Section -->
    <div class="w-full xl:w-3/4 bg-white rounded-sm border border-gray-200 p-6 shadow-md space-y-5 transition-all duration-300 hover:shadow-xl hover:border-[#E5E7EB] flex flex-col">
        <h2 class="text-lg font-semibold mb-4 text-[#23527c]">Material Efficiency</h2>
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
                    <h3 class="text-sm text-[#23527c] mb-1">Material Efficiency</h3>
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
                        <h3 class="text-sm text-[#23527c] mb-1">{{ $card['title'] }}</h3>
                        <p class="text-lg font-semibold" style="color: {{ $card['color'] }}">{{ $card['value'] }}</p>
                    </div>
                </div>
            @endforeach

            <!-- Month Buttons -->
            <div class="col-span-1 sm:col-span-2 bg-white rounded-xl shadow border border-gray-200 p-4">
                <h3 class="text-sm text-[#23527c] text-center mb-3 font-semibold">Select Month</h3>
                <div class="grid grid-cols-3 gap-2">
                    @foreach ([
                        'January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'
                    ] as $monthName)
                        <a href="{{ route('analytics.material.monthly_report', [
                            'month' => $monthName,
                            'line' => request('line'),
                            'date' => request('date')
                        ]) }}"
                        class="text-xs text-[#23527c] text-center border border-gray-300 rounded-md py-1 px-2 hover:bg-[#23527c] hover:text-white transition duration-150">
                            {{ $monthName }}
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
        <p class="text-lg text-[#23527c] font-semibold">Quarter-To-Date Report</p>
        <svg :class="showQuarter ? 'rotate-180' : ''" class="w-5 h-5 transition-transform text-[#23527c]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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
                        <td class="p-2 border border-[#d9d9d9] text-[#23527c]">Production Output</td>
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
                            <td class="p-2 border text-[#23527c]">FG Usage</td>
                            <td class="p-2 border text-center">{{ number_format($data['FgUsage']['Q1']) }}</td>
                            <td class="p-2 border text-center">{{ number_format($data['FgUsage']['Q2']) }}</td>
                            <td class="p-2 border text-center">{{ number_format($data['FgUsage']['Q3']) }}</td>
                            <td class="p-2 border text-center">{{ number_format($data['FgUsage']['Q4']) }}</td>
                            <td class="p-2 border text-center font-semibold text-[#1e3a8a]">{{ number_format($data['TotalFg']) }}</td>
                        </tr>
                        <tr class="bg-white hover:bg-[#f1f5f9] transition">
                            <td class="p-2 border text-[#23527c]">Rejects</td>
                            <td class="p-2 border text-center">{{ number_format($data['Rejects']['Q1']) }}</td>
                            <td class="p-2 border text-center">{{ number_format($data['Rejects']['Q2']) }}</td>
                            <td class="p-2 border text-center">{{ number_format($data['Rejects']['Q3']) }}</td>
                            <td class="p-2 border text-center">{{ number_format($data['Rejects']['Q4']) }}</td>
                            <td class="p-2 border text-center font-semibold text-[#1e3a8a]">{{ number_format($data['TotalRej']) }}</td>
                        </tr>
                        <tr class="bg-white hover:bg-[#f1f5f9] transition">
                            <td class="p-2 border text-[#23527c]">QA Samples</td>
                            <td class="p-2 border text-center">{{ number_format($data['QaSamples']['Q1']) }}</td>
                            <td class="p-2 border text-center">{{ number_format($data['QaSamples']['Q2']) }}</td>
                            <td class="p-2 border text-center">{{ number_format($data['QaSamples']['Q3']) }}</td>
                            <td class="p-2 border text-center">{{ number_format($data['QaSamples']['Q4']) }}</td>
                            <td class="p-2 border text-center font-semibold text-[#1e3a8a]">{{ number_format($data['TotalQa']) }}</td>
                        </tr>
                        <tr class="bg-white hover:bg-[#f1f5f9] transition">
                            <td class="p-2 border text-[#23527c]">% Rejects</td>
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
        <p class="text-lg text-[#23527c] font-semibold">Month-To-Date Report</p>
        <svg :class="showMonth ? 'rotate-180' : ''" class="w-5 h-5 transition-transform text-[#23527c]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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
                        <td class="p-2 border border-[#d9d9d9] text-[#23527c]">Target Mat'l Efficiency, %</td>
                        @for ($i = 1; $i <= 12; $i++)
                            <td class="p-2 border border-[#d9d9d9] text-center">1.00%</td>
                        @endfor
                    </tr>
                    <tr class="bg-white hover:bg-[#f1f5f9] cursor-pointer">
                        <td class="p-2 border border-[#d9d9d9] text-[#23527c]">Production Output, Cs</td>
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
                        <td class="p-2 border text-[#23527c]">FG Usage</td>
                        @foreach ($mat['FgUsage'] as $value)
                            <td class="p-2 border text-center">{{ number_format($value) }}</td>
                        @endforeach
                    </tr>
                    <tr class="bg-white hover:bg-[#f1f5f9] transition">
                        <td class="p-2 border text-[#23527c]">Rejects</td>
                        @foreach ($mat['Rejects'] as $value)
                            <td class="p-2 border text-center">{{ number_format($value) }}</td>
                        @endforeach
                    </tr>
                    <tr class="bg-white hover:bg-[#f1f5f9] transition">
                        <td class="p-2 border text-[#23527c]">QA Samples</td>
                        @foreach ($mat['QaSamples'] as $value)
                            <td class="p-2 border text-center">{{ number_format($value) }}</td>
                        @endforeach
                    </tr>
                    <tr class="bg-white hover:bg-[#f1f5f9] transition">
                        <td class="p-2 border text-[#23527c]">% Rejects</td>
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
@else
    <div class="w-full inline-flex items-center gap-1 bg-[#5a9fd4] text-sm border border-[#4590ca] p-4 mt-4 text-white">
        <x-icons-warning />
        Please select a year and a production line, then click <b>Submit</b> to view analytics.
    </div>
@endif

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
