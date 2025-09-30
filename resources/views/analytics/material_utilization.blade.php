@extends('layouts.app')

@section('content')

{{-- Page Title --}}
<h2 class="text-xl mb-2 font-bold text-[#23527c]">Material Utilization Report</h2>


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

        <!-- Submit Button Section -->
        <div class="flex gap-4 mt-4">
            <button type="submit"
                    class="inline-flex items-center justify-center gap-1 bg-[#5bb75b] border border-[#43a143] text-white px-3 py-2 hover:bg-[#42a542] text-sm">
                <x-icons-submit class="w-4 h-4 text-white" />
                Submit
            </button>
        </div>
    </form>
</div>


<div>
    @if($year)
    <div class="flex justify-between items-center mt-4">
        <h2 class="text-xl font-bold text-[#23527c]">
            Year {{ $year }}
        </h2>

<form action="{{ route('analytics.export_excel_material_summary') }}" method="GET" class="inline-block">
    <input type="hidden" name="date" value="{{ $year }}">
    <button type="submit"
        class="inline-flex items-center justify-center gap-1 p-2 bg-[#5bb75b] hover:bg-[#42a542] border border-[#42a542] text-white text-sm font-medium">
        <x-icons-pdf class="w-4 h-4" />
        Excel
    </button>
</form>
</div>

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
            <table class="min-w-full text-[10px] border border-gray-300 table-auto">
                <thead class="bg-[#f1f5f9]  text-center">
                    <tr>
                        <th class="border border-gray-300 px-2 py-1 text-left w-[200px] whitespace-nowrap">Indicator</th>
                        @for ($i = 1; $i <= 12; $i++)
                            <th class="border border-gray-300 px-2 py-1 text-right whitespace-nowrap">{{ $i }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody class="text-[9px]">
                    <!-- Target MAT Efficiency -->
                    <tr>
                <td class="border border-gray-300 px-2 py-1  text-left whitespace-nowrap">
                        <span class="inline-flex items-center mr-2">
                            <span class="h-[2px] w-4 bg-[#00B050]"></span>
                            <span class="inline-block w-2 h-2 rounded-full bg-[#4A7EBB] mx-1"></span>
                            <span class="h-[2px] w-4 bg-[#00B050]"></span>
                        </span>
                    Target MAT Efficiency, %
                </td>                        @for ($i = 1; $i <= 12; $i++)
                            <td class="border border-gray-300 px-2 py-1 text-right">95%</td>
                        @endfor
                    </tr>

                    <!-- Production Output -->
                    <tr>
                <td class="border px-2 py-1 text-left  whitespace-nowrap">
                            <span class="inline-flex items-center mr-2">
            <span class="h-[2px] w-4 bg-[#376faa]"></span>
            <span class="inline-block w-2 h-2 rounded-full bg-[#376faa] mx-1"></span>
            <span class="h-[2px] w-4 bg-[#376faa]"></span>
        </span>
                    Production Output (Cases)
                </td>             
                        </td>
                        @for ($i = 1; $i <= 12; $i++)
                            <td class="border border-gray-300 px-2 py-1 text-right">
                                {{ number_format($monthlyProduction[$i] ?? 0) }}
                            </td>
                        @endfor
                    </tr>

                    <!-- Preforms Reject Rates -->
                    <tr>
                <td class="border px-2 py-1 text-left  whitespace-nowrap">
                            <span class="inline-flex items-center mr-2">
            <span class="h-[2px] w-4 bg-[#7F7F7F]"></span>
            <span class="inline-block w-2 h-2 rounded-full bg-[#BE4B48] mx-1"></span>
            <span class="h-[2px] w-4 bg-[#7F7F7F]"></span>
        </span>
                    PREFORMS % Rejects
                </td>                        @for ($i = 1; $i <= 12; $i++)
                            <td class="border border-gray-300 px-2 py-1 text-right">
                                {{ $preformRejectRates[$i] ?? '0.00%' }}
                            </td>
                        @endfor
                    </tr>

                    <!-- Caps Reject Rates -->
                    <tr>
                <td class="border px-2 py-1 text-left  whitespace-nowrap">
                            <span class="inline-flex items-center mr-2">
            <span class="h-[2px] w-4 bg-[#254061]"></span>
            <span class="inline-block w-2 h-2 rounded-full bg-[#9BBB59] mx-1"></span>
            <span class="h-[2px] w-4 bg-[#254061]"></span>
        </span>
                    CAPS % Rejects
                </td>                          @for ($i = 1; $i <= 12; $i++)
                            <td class="border border-gray-300 px-2 py-1 text-right">
                                {{ $capsRejectRates[$i] ?? '0.00%' }}
                            </td>
                        @endfor
                    </tr>

                    <!-- OPP Labels Reject Rates -->
                    <tr>
                <td class="border px-2 py-1 text-left  whitespace-nowrap">
                            <span class="inline-flex items-center mr-2">
            <span class="h-[2px] w-4 bg-[#77933C]"></span>
            <span class="inline-block w-2 h-2 rounded-full bg-[#8064A2] mx-1"></span>
            <span class="h-[2px] w-4 bg-[#77933C]"></span>
        </span>
                    OPP LABELS % Rejects
                </td>                         @for ($i = 1; $i <= 12; $i++)
                            <td class="border border-gray-300 px-2 py-1 text-right">
                                {{ $oppRejectRates[$i] ?? '0.00%' }}
                            </td>
                        @endfor
                    </tr>

                    <!-- LDPE Reject Rates -->
                    <tr>
                <td class="border px-2 py-1 text-left  whitespace-nowrap">
                                                <span class="inline-flex items-center mr-2">
<span class="h-[2px] w-4 bg-[#984807]/70"></span>
<span class="inline-block w-2 h-2 rounded-full bg-[#4BACC6] mx-1"></span>
<span class="h-[2px] w-4 bg-[#984807]/70"></span>
        </span>
                    LDPE Shrinkfilm % Rejects
                </td>                        @for ($i = 1; $i <= 12; $i++)
                            <td class="border border-gray-300 px-2 py-1 text-right">
                                {{ $ldpeRejectRates[$i] ?? '0.00%' }}
                            </td>
                        @endfor
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

<!-- Right-side Cards -->
<div class="w-full xl:w-1/4 flex flex-col justify-between gap-2">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 h-full">

@php
    $cards = [
        ['label' => 'Material Efficiency', 'value' => $materialEfficiency * 100, 'color' => '#23527c', 'span' => 'col-span-1 sm:col-span-2'],

        ['label' => 'Preforms', 'value' => $preformTotalRate, 'color' => '#808080'],
        ['label' => 'Caps', 'value' => $capsTotalRate, 'color' => '#16365C'],
        ['label' => 'OPP', 'value' => $oppTotalRate, 'color' => '#4F6228'],
        ['label' => 'LDPE', 'value' => $ldpeTotalRate, 'color' => '#963634'],
    ];
@endphp

@foreach ($cards as $card)
    <div class="{{ $card['span'] ?? '' }} bg-white hover:bg-[#e5f4ff] rounded-xl shadow border border-gray-200 p-3 h-28 flex items-center justify-center">
        <div class="text-center">
            <h3 class="text-sm text-[#2d326b] mb-1">{{ $card['label'] }}</h3>
            <p class="text-lg font-semibold" style="color: {{ $card['color'] }}">
                {{ number_format($card['value'], 2) }}%
            </p>
        </div>
    </div>
@endforeach

        <!-- Line Buttons -->
        <div class="col-span-1 sm:col-span-2 bg-white rounded-xl shadow border border-gray-200 p-4">
            <h3 class="text-sm text-[#2d326b] text-center mb-3 font-semibold">Select Production Line</h3>
            <div class="grid grid-cols-3 gap-2">
                @foreach ($lines as $lineOption)
                    <a href="{{ route('analytics.material.index', ['line' => $lineOption, 'date' => $year]) }}"
                       class="text-xs text-[#2d326b] text-center border border-gray-300 rounded-md py-1 px-2 hover:bg-[#2d326b] hover:text-white transition duration-150">
                        Line {{ $lineOption }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>

</div>



<!-- Month-To-Date Report Section -->
<div x-data="{ showMonth: true }" class="w-full mb-4">
    <!-- Toggle Button -->
    <div class="flex items-center justify-between cursor-pointer bg-white rounded-sm border border-gray-200 p-4 shadow-md hover:shadow-xl hover:border-[#fffffb]" 
         @click="showMonth = !showMonth">
        <p class="text-lg text-[#23527c] font-semibold">Month-To-Date Report</p>
        <svg :class="showMonth ? 'rotate-180' : ''" 
             class="w-5 h-5 transition-transform text-[#23527c]" 
             fill="none" stroke="currentColor" stroke-width="2" 
             viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </div>

    <!-- Content -->
    <div x-show="showMonth" x-transition>
        <div class="bg-white rounded-sm border border-gray-200 p-6 shadow-md mt-2 overflow-x-auto">

            @php
            function rejectBgClass($percent) {
                $value = floatval(str_replace('%', '', $percent));
                return $value <= 1.00 ? 'bg-[#92D050]' : 'bg-[#FF0000]';
            }
            @endphp

            <table class="w-full text-xs text-left border border-[#fffffb] border-collapse">
                <thead class="text-[8px] text-white uppercase bg-[#0070C0] whitespace-nowrap">
                    {{-- Title row --}}
                    <tr>
                        <th colspan="4" class="p-2 border border-[#fffffb] text-center"></th>
                        <th colspan="5" class="p-2 border border-[#fffffb] bg-[#808080] text-center">Preforms</th>
                        <th colspan="5" class="p-2 border border-[#fffffb] bg-[#16365C] text-center">Caps</th>
                        <th colspan="5" class="p-2 border border-[#fffffb] bg-[#4F6228] text-center">OPP Label</th>
                        <th colspan="5" class="p-2 border border-[#fffffb] bg-[#974706] text-center">LDPE Shrinkfilm</th>
                    </tr>

                    {{-- Header row --}}
                    <tr>
                        <th class="p-2 border border-[#fffffb] text-center whitespace-nowrap">Period</th>
                        <th class="p-2 border border-[#fffffb] text-center whitespace-nowrap">Month</th>
                        <th class="p-2 border border-[#fffffb] text-center whitespace-nowrap">Target Mat'l Efficiency, %</th>
                        <th class="p-2 border border-[#fffffb] text-center whitespace-nowrap">Production Output</th>

                        {{-- Preforms --}}
                        <th class="p-2 border border-[#fffffb] text-center bg-[#808080]">Description</th>
                        <th class="p-2 border border-[#fffffb] text-center bg-[#808080]">FG Usage</th>
                        <th class="p-2 border border-[#fffffb] text-center bg-[#808080]">Rejects</th>
                        <th class="p-2 border border-[#fffffb] text-center bg-[#808080]">QA Samples</th>
                        <th class="p-2 border border-[#fffffb] text-center bg-[#808080]">% Rejects</th>

                        {{-- Caps --}}
                        <th class="p-2 border border-[#fffffb] text-center bg-[#16365C]">Description</th>
                        <th class="p-2 border border-[#fffffb] text-center bg-[#16365C]">FG Usage</th>
                        <th class="p-2 border border-[#fffffb] text-center bg-[#16365C]">Rejects</th>
                        <th class="p-2 border border-[#fffffb] text-center bg-[#16365C]">QA Samples</th>
                        <th class="p-2 border border-[#fffffb] text-center bg-[#16365C]">% Rejects</th>

                        {{-- OPP Label --}}
                        <th class="p-2 border border-[#fffffb] text-center bg-[#4F6228]">Description</th>
                        <th class="p-2 border border-[#fffffb] text-center bg-[#4F6228]">FG Usage</th>
                        <th class="p-2 border border-[#fffffb] text-center bg-[#4F6228]">Rejects</th>
                        <th class="p-2 border border-[#fffffb] text-center bg-[#4F6228]">QA Samples</th>
                        <th class="p-2 border border-[#fffffb] text-center bg-[#4F6228]">% Rejects</th>

                        {{-- LDPE --}}
                        <th class="p-2 border border-[#fffffb] text-center bg-[#974706]">Description</th>
                        <th class="p-2 border border-[#fffffb] text-center bg-[#974706]">FG Usage</th>
                        <th class="p-2 border border-[#fffffb] text-center bg-[#974706]">Rejects</th>
                        <th class="p-2 border border-[#fffffb] text-center bg-[#974706]">QA Samples</th>
                        <th class="p-2 border border-[#fffffb] text-center bg-[#974706]">% Rejects</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($months as $i => $month)
                    @php $m = $i + 1; @endphp
                    <tr class="group">
                        <td class="p-2 border border-[#fffffb] bg-[#DBE5F1] text-center">P{{ $m }}</td>
                        <td class="p-2 border border-[#fffffb] bg-[#DBE5F1] text-center">{{ $month }}</td>
                        <td class="p-2 border border-[#fffffb] bg-[#F2F2F2] text-center">1.00%</td>
                        <td class="p-2 border border-[#fffffb] bg-[#F2F2F2] text-center">{{ number_format($monthlyProduction[$m] ?? 0) }}</td>


                        {{-- Preforms --}}
                        <td class="p-2 border border-[#fffffb] bg-[#F2F2F2] text-center">Preforms</td>
                        <td class="p-2 border border-[#fffffb] bg-[#F2F2F2] text-center">{{ number_format($preformFgUsage[$m] ?? 0) }}</td>
                        <td class="p-2 border border-[#fffffb] bg-[#F2F2F2] text-center">{{ number_format($preformRejects[$m] ?? 0) }}</td>
                        <td class="p-2 border border-[#fffffb] bg-[#F2F2F2] text-center">{{ number_format($preformQaSamples[$m] ?? 0) }}</td>
                        <td class="p-2 border border-[#fffffb] text-center {{ rejectBgClass($preformRejectRates[$m] ?? '0.00%') }}">
                            {{ $preformRejectRates[$m] ?? '0.00%' }}
                        </td>

                        {{-- Caps --}}
                        <td class="p-2 border border-[#fffffb] bg-[#F2F2F2] text-center">Caps</td>
                        <td class="p-2 border border-[#fffffb] bg-[#F2F2F2] text-center">{{ number_format($capsFgUsage[$m] ?? 0) }}</td>
                        <td class="p-2 border border-[#fffffb] bg-[#F2F2F2] text-center">{{ number_format($capsRejects[$m] ?? 0) }}</td>
                        <td class="p-2 border border-[#fffffb] bg-[#F2F2F2] text-center">{{ number_format($capsQaSamples[$m] ?? 0) }}</td>
                        <td class="p-2 border border-[#fffffb] text-center {{ rejectBgClass($capsRejectRates[$m] ?? '0.00%') }}">
                            {{ $capsRejectRates[$m] ?? '0.00%' }}
                        </td>

                        {{-- OPP --}}
                        <td class="p-2 border border-[#fffffb] bg-[#F2F2F2] text-center">OPP</td>
                        <td class="p-2 border border-[#fffffb] bg-[#F2F2F2] text-center">{{ number_format($oppFgUsage[$m] ?? 0) }}</td>
                        <td class="p-2 border border-[#fffffb] bg-[#F2F2F2] text-center">{{ number_format($oppRejects[$m] ?? 0) }}</td>
                        <td class="p-2 border border-[#fffffb] bg-[#F2F2F2] text-center">{{ number_format($oppQaSamples[$m] ?? 0) }}</td>
                        <td class="p-2 border border-[#fffffb] text-center {{ rejectBgClass($oppRejectRates[$m] ?? '0.00%') }}">
                            {{ $oppRejectRates[$m] ?? '0.00%' }}
                        </td>

                        {{-- LDPE --}}
                        <td class="p-2 border border-[#fffffb] bg-[#F2F2F2] text-center">LDPE</td>
                        <td class="p-2 border border-[#fffffb] bg-[#F2F2F2] text-center">{{ number_format($ldpeFgUsage[$m] ?? 0) }}</td>
                        <td class="p-2 border border-[#fffffb] bg-[#F2F2F2] text-center">{{ number_format($ldpeRejects[$m] ?? 0) }}</td>
                        <td class="p-2 border border-[#fffffb] bg-[#F2F2F2] text-center">{{ number_format($ldpeQaSamples[$m] ?? 0) }}</td>
                        <td class="p-2 border border-[#fffffb] text-center {{ rejectBgClass($ldpeRejectRates[$m] ?? '0.00%') }}">
                            {{ $ldpeRejectRates[$m] ?? '0.00%' }}
                        </td>
                    </tr>
                    @endforeach
                        <tr>
        <th colspan="26"
            class="p-2 text-center whitespace-nowrap bg-[#595959]">
        </th>
    </tr>  
        <tr>
        <th colspan="26"
            class="p-2 text-center whitespace-nowrap bg-[#F2F2F2]">
        </th>
    </tr>  

{{-- ===== QUARTERLY SUMMARY (based on specific months per quarter) ===== --}}
<thead class="text-[10px]">
  <tr>
    {{-- Title (spans 6 rows) --}}
    <th colspan="2" rowspan="6"
        class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4] text-black">
      MTD RM SUMMARY REPORT
    </th>
  </tr>

  {{-- Q1 --}}
  <tr>
    <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">Q1</th>
    {{-- Production Output (cases) --}}
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">
      {{ number_format($quarterlyProduction['Q1'] ?? 0) }}
    </td>

    {{-- PREFORMS title (rowspan) --}}
    <th rowspan="6"
        class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">
      PREFORMS
    </th>
    {{-- Preforms FG / Rejects --}}
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">
      {{ number_format($preformQuarterFgUsage['Q1'] ?? 0) }}
    </td>
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">
      {{ number_format($preformQuarterRejects['Q1'] ?? 0) }}
    </td>
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">
      {{ number_format($preformQuarterQaSamples['Q1'] ?? 0) }}
    </td>
                    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">
      {{( $preformQuarterRejectRates['Q1'] ?? 0) }}
    </td>

    {{-- CAPS title (rowspan) --}}
    <th rowspan="6"
        class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">
      CAPS
    </th>
    {{-- Caps FG / Rejects --}}
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">
      {{ number_format($capsQuarterFgUsage['Q1'] ?? 0) }}
    </td>
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">
      {{ number_format($capsQuarterRejects['Q1'] ?? 0) }}
    </td>
        <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">
      {{ number_format($capsQuarterQaSamples['Q1'] ?? 0) }}
    </td>
                    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">
      {{( $capsQuarterRejectRates['Q1'] ?? 0) }}
    </td>

    {{-- OPP LABELS title (rowspan) --}}
    <th rowspan="6"
        class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">
      OPP LABELS
    </th>
    {{-- OPP FG / Rejects --}}
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">
      {{ number_format($oppQuarterFgUsage['Q1'] ?? 0) }}
    </td>
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">
      {{ number_format($oppQuarterRejects['Q1'] ?? 0) }}
    </td>
            <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">
      {{ number_format($oppQuarterQaSamples['Q1'] ?? 0) }}
    </td>
                    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">
      {{( $oppQuarterRejectRates['Q1'] ?? 0) }}
    </td>


    {{-- LDPE title (rowspan) --}}
    <th rowspan="6"
        class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">
      LDPE SHRINKFILM
    </th>
    {{-- LDPE FG / Rejects --}}
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">
      {{ number_format($ldpeQuarterFgUsage['Q1'] ?? 0) }}
    </td>
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">
      {{ number_format($ldpeQuarterRejects['Q1'] ?? 0) }}
    </td>
                <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">
      {{ number_format($ldpeQuarterQaSamples['Q1'] ?? 0) }}
    </td>
                    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">
      {{( $ldpeQuarterRejectRates['Q1'] ?? 0) }}
    </td>

  </tr>

  {{-- Q2 --}}
  <tr>
    <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">Q2</th>
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">
      {{ number_format($quarterlyProduction['Q2'] ?? 0) }}
    </td>

    {{-- Preforms FG / Rejects --}}
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">
      {{ number_format($preformQuarterFgUsage['Q2'] ?? 0) }}
    </td>
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">
      {{ number_format($preformQuarterRejects['Q2'] ?? 0) }}
    </td>
        <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">
      {{ number_format($preformQuarterQaSamples['Q2'] ?? 0) }}
    </td>
                    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">
      {{( $preformQuarterRejectRates['Q2'] ?? 0) }}
    </td>

    {{-- Caps FG / Rejects --}}
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">
      {{ number_format($capsQuarterFgUsage['Q2'] ?? 0) }}
    </td>
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">
      {{ number_format($capsQuarterRejects['Q2'] ?? 0) }}
    </td>
        <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">
      {{ number_format($capsQuarterQaSamples['Q2'] ?? 0) }}
    </td>
                    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">
      {{( $capsQuarterRejectRates['Q2'] ?? 0) }}
    </td>


{{-- OPP FG / Rejects --}}
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">
      {{ number_format($oppQuarterFgUsage['Q2'] ?? 0) }}
    </td>
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">
      {{ number_format($oppQuarterRejects['Q2'] ?? 0) }}
    </td>
            <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">
      {{ number_format($oppQuarterQaSamples['Q2'] ?? 0) }}
    </td>
                    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">
      {{( $oppQuarterRejectsRates['Q2'] ?? 0) }}
    </td>

    {{-- LDPE FG / Rejects --}}
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">
      {{ number_format($ldpeQuarterFgUsage['Q2'] ?? 0) }}
    </td>
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">
      {{ number_format($ldpeQuarterRejects['Q2'] ?? 0) }}
    </td>
                <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">
      {{ number_format($ldpeQuarterQaSamples['Q2'] ?? 0) }}
    </td>
                    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">
      {{( $ldpeQuarterRejectsRates['Q2'] ?? 0) }}
  </tr>

  {{-- Q3 --}}
  <tr>
    <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">Q3</th>
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">
      {{ number_format($quarterlyProduction['Q3'] ?? 0) }}
    </td>

    {{-- Preforms FG / Rejects --}}
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">
      {{ number_format($preformQuarterFgUsage['Q3'] ?? 0) }}
    </td>
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">
      {{ number_format($preformQuarterRejects['Q3'] ?? 0) }}
    </td>
        <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">
      {{ number_format($preformQuarterQaSamples['Q3'] ?? 0) }}
    </td>
                    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">
      {{( $preformQuarterRejectRates['Q3'] ?? 0) }}
    </td>

    {{-- Caps FG / Rejects --}}
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">
      {{ number_format($capsQuarterFgUsage['Q3'] ?? 0) }}
    </td>
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">
      {{ number_format($capsQuarterRejects['Q3'] ?? 0) }}
    </td>
            <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">
      {{ number_format($capsQuarterQaSamples['Q3'] ?? 0) }}
    </td>
                    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">
      {{( $capsQuarterRejectRates['Q3'] ?? 0) }}
    </td>


{{-- OPP FG / Rejects --}}
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">
      {{ number_format($oppQuarterFgUsage['Q3'] ?? 0) }}
    </td>
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">
      {{ number_format($oppQuarterRejects['Q3'] ?? 0) }}
    </td>
            <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">
      {{ number_format($oppQuarterQaSamples['Q3'] ?? 0) }}
    </td>
    </td>
                    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">
      {{( $oppQuarterRejectRates['Q3'] ?? 0) }}
    </td>


    {{-- LDPE FG / Rejects --}}
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">
      {{ number_format($ldpeQuarterFgUsage['Q3'] ?? 0) }}
    </td>
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">
      {{ number_format($ldpeQuarterRejects['Q3'] ?? 0) }}
    </td>
                <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">
      {{ number_format($ldpeQuarterQaSamples['Q3'] ?? 0) }}
    </td>
                    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">
      {{( $ldpeQuarterRejectRates['Q3'] ?? 0) }}
  </tr>
  </tr>

  {{-- Q4 --}}
  <tr>
    <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">Q4</th>
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">
      {{ number_format($quarterlyProduction['Q4'] ?? 0) }}
    </td>

    {{-- Preforms FG / Rejects --}}
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">
      {{ number_format($preformQuarterFgUsage['Q4'] ?? 0) }}
    </td>
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">
      {{ number_format($preformQuarterRejects['Q4'] ?? 0) }}
    </td>
        <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">
      {{ number_format($preformQuarterQaSamples['Q4'] ?? 0) }}
    </td>
                <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">
      {{( $preformQuarterRejectRates['Q4'] ?? 0) }}
    </td>

    {{-- Caps FG / Rejects --}}
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">
      {{ number_format($capsQuarterFgUsage['Q4'] ?? 0) }}
    </td>
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">
      {{ ($capsQuarterRejects['Q4'] ?? 0) }}
    </td>
            <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">
      {{ number_format($capsQuarterQaSamples['Q4'] ?? 0) }}
    </td>
                    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">
      {{( $capsQuarterRejectRates['Q4'] ?? 0) }}
    </td>


{{-- OPP FG / Rejects --}}
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">
      {{ number_format($oppQuarterFgUsage['Q4'] ?? 0) }}
    </td>
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">
      {{ number_format($oppQuarterRejects['Q4'] ?? 0) }}
    </td>
            <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">
      {{ number_format($oppQuarterRejects['Q4'] ?? 0) }}
    </td>
                    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">
      {{( $oppQuarterRejectRates['Q4'] ?? 0) }}
    </td>

    {{-- LDPE FG / Rejects --}}
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">
      {{ number_format($ldpeQuarterFgUsage['Q4'] ?? 0) }}
    </td>
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">
      {{ number_format($ldpeQuarterRejects['Q4'] ?? 0) }}
    </td>
                <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">
      {{ number_format($ldpeQuarterQaSamples['Q4'] ?? 0) }}
    </td>
                    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">
      {{( $ldpeQuarterRejectRates['Q4'] ?? 0) }}
  </tr>
  </tr>

    {{-- PTD (Annual Totals) --}}
  <tr>
    <th class="bg-[#FCD5B4] p-2 border border-[#F2F2F2] text-center font-bold">PTD</th>

    {{-- Total Annual Production (cases) --}}
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">
      {{ number_format($totalAnnualProduction ?? 0) }}
    </td>

   {{-- ===== PTD TOTALS ===== --}}

{{-- PREFORMS --}}
@php
  $preformTotalFg = ($preformQuarterFgUsage['Q1'] ?? 0) +
                    ($preformQuarterFgUsage['Q2'] ?? 0) +
                    ($preformQuarterFgUsage['Q3'] ?? 0) +
                    ($preformQuarterFgUsage['Q4'] ?? 0);

  $preformTotalRej = ($preformQuarterRejects['Q1'] ?? 0) +
                     ($preformQuarterRejects['Q2'] ?? 0) +
                     ($preformQuarterRejects['Q3'] ?? 0) +
                     ($preformQuarterRejects['Q4'] ?? 0);

  $preformTotalQa = ($preformQuarterQaSamples['Q1'] ?? 0) +
                    ($preformQuarterQaSamples['Q2'] ?? 0) +
                    ($preformQuarterQaSamples['Q3'] ?? 0) +
                    ($preformQuarterQaSamples['Q4'] ?? 0);

  $preformTotalAll = $preformTotalFg + $preformTotalRej + $preformTotalQa;
  $preformRejectPercent = $preformTotalAll > 0 ? ($preformTotalRej / $preformTotalAll) * 100 : 0;
@endphp

<td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">{{ number_format($preformTotalFg) }}</td>
<td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">{{ number_format($preformTotalRej) }}</td>
<td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">{{ number_format($preformTotalQa) }}</td>
<td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">{{ number_format($preformRejectPercent, 2) }}%</td>


{{-- CAPS --}}
@php
  $capsTotalFg = ($capsQuarterFgUsage['Q1'] ?? 0) +
                 ($capsQuarterFgUsage['Q2'] ?? 0) +
                 ($capsQuarterFgUsage['Q3'] ?? 0) +
                 ($capsQuarterFgUsage['Q4'] ?? 0);

  $capsTotalRej = ($capsQuarterRejects['Q1'] ?? 0) +
                  ($capsQuarterRejects['Q2'] ?? 0) +
                  ($capsQuarterRejects['Q3'] ?? 0) +
                  ($capsQuarterRejects['Q4'] ?? 0);

  $capsTotalQa = ($capsQuarterQaSamples['Q1'] ?? 0) +
                 ($capsQuarterQaSamples['Q2'] ?? 0) +
                 ($capsQuarterQaSamples['Q3'] ?? 0) +
                 ($capsQuarterQaSamples['Q4'] ?? 0);

  $capsTotalAll = $capsTotalFg + $capsTotalRej + $capsTotalQa;
  $capsRejectPercent = $capsTotalAll > 0 ? ($capsTotalRej / $capsTotalAll) * 100 : 0;
@endphp

<td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">{{ number_format($capsTotalFg) }}</td>
<td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">{{ number_format($capsTotalRej) }}</td>
<td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">{{ number_format($capsTotalQa) }}</td>
<td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">{{ number_format($capsRejectPercent, 2) }}%</td>


{{-- OPP LABELS --}}
@php
  $oppTotalFg = ($oppQuarterFgUsage['Q1'] ?? 0) +
                ($oppQuarterFgUsage['Q2'] ?? 0) +
                ($oppQuarterFgUsage['Q3'] ?? 0) +
                ($oppQuarterFgUsage['Q4'] ?? 0);

  $oppTotalRej = ($oppQuarterRejects['Q1'] ?? 0) +
                 ($oppQuarterRejects['Q2'] ?? 0) +
                 ($oppQuarterRejects['Q3'] ?? 0) +
                 ($oppQuarterRejects['Q4'] ?? 0);

  $oppTotalQa = ($oppQuarterQaSamples['Q1'] ?? 0) +
                ($oppQuarterQaSamples['Q2'] ?? 0) +
                ($oppQuarterQaSamples['Q3'] ?? 0) +
                ($oppQuarterQaSamples['Q4'] ?? 0);

  $oppTotalAll = $oppTotalFg + $oppTotalRej + $oppTotalQa;
  $oppRejectPercent = $oppTotalAll > 0 ? ($oppTotalRej / $oppTotalAll) * 100 : 0;
@endphp

<td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">{{ number_format($oppTotalFg) }}</td>
<td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">{{ number_format($oppTotalRej) }}</td>
<td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">{{ number_format($oppTotalQa) }}</td>
<td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">{{ number_format($oppRejectPercent, 2) }}%</td>


{{-- LDPE SHRINKFILM --}}
@php
  $ldpeTotalFg = ($ldpeQuarterFgUsage['Q1'] ?? 0) +
                 ($ldpeQuarterFgUsage['Q2'] ?? 0) +
                 ($ldpeQuarterFgUsage['Q3'] ?? 0) +
                 ($ldpeQuarterFgUsage['Q4'] ?? 0);

  $ldpeTotalRej = ($ldpeQuarterRejects['Q1'] ?? 0) +
                  ($ldpeQuarterRejects['Q2'] ?? 0) +
                  ($ldpeQuarterRejects['Q3'] ?? 0) +
                  ($ldpeQuarterRejects['Q4'] ?? 0);

  $ldpeTotalQa = ($ldpeQuarterQaSamples['Q1'] ?? 0) +
                 ($ldpeQuarterQaSamples['Q2'] ?? 0) +
                 ($ldpeQuarterQaSamples['Q3'] ?? 0) +
                 ($ldpeQuarterQaSamples['Q4'] ?? 0);

  $ldpeTotalAll = $ldpeTotalFg + $ldpeTotalRej + $ldpeTotalQa;
  $ldpeRejectPercent = $ldpeTotalAll > 0 ? ($ldpeTotalRej / $ldpeTotalAll) * 100 : 0;
@endphp

<td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">{{ number_format($ldpeTotalFg) }}</td>
<td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">{{ number_format($ldpeTotalRej) }}</td>
<td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">{{ number_format($ldpeTotalQa) }}</td>
<td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">{{ number_format($ldpeRejectPercent, 2) }}%</td>


  </tr>
</thead>

    </tbody>
          </table>

        </div>
    </div>
</div>

@else
    <div class="w-full inline-flex items-center gap-1 bg-[#5a9fd4] text-sm border border-[#4590ca] p-4 mt-4 text-white">
        <x-icons-warning />
        Please select a year, then click <b>Submit</b> to view analytics.
    </div>
@endif
<!-- Chart.js Data Preparation -->
@php
    $jsPreforms = json_encode($preformRejectRatesData);
    $jsCaps = json_encode($capsRejectRatesData);
    $jsOpp = json_encode($oppRejectRatesData);
    $jsLdpe = json_encode($ldpeRejectRatesData);
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
                borderColor: '#00B050',
                borderDash: [4, 4],
                tension: 0.3,
                fill: false,
                borderWidth: 2,
                pointRadius: 3,
                pointBackgroundColor: '#4A7EBB',
                pointBorderWidth: 0,
                spanGaps: true
            },
            {
                label: 'PREFORMS',
                data: {!! $jsPreforms !!},
                borderColor: '#7F7F7F',
                tension: 0.3,
                fill: false,
                borderWidth: 2,
                pointRadius: 3,
                pointBackgroundColor: '#BE4B48',
                pointBorderWidth: 0,
                spanGaps: true 
            },
            {
                label: 'CAPS',
                data: {!! $jsCaps !!},
                borderColor: '#254061',
                tension: 0.3,
                fill: false,
                borderWidth: 2,
                pointRadius: 3,
                pointBackgroundColor: '#98B954',
                pointBorderWidth: 0,
                spanGaps: true 
            },
            {
                label: 'OPP LABELS',
                data: {!! $jsOpp !!},
                borderColor: '#77933C',
                tension: 0.3,
                fill: false,
                borderWidth: 2,
                pointRadius: 3,
                pointBackgroundColor: '#7D60A0',
                pointBorderWidth: 0,
                spanGaps: true 
            },
            {
                label: 'LDPE SHRINK FILM',
                data: {!! $jsLdpe !!},
                borderColor: '#984807',
                tension: 0.3,
                fill: false,
                borderWidth: 2,
                pointRadius: 3,
                pointBackgroundColor: '#46AAC5',
                pointBorderWidth: 0,
                spanGaps: true 
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
            legend: { display: false },
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
                min: 0,
                max: 0.012,
                ticks: {
                    stepSize: 0.002,
                    callback: value => (value * 100).toFixed(2) + '%'
                },
                grid: {
                    color: 'rgba(0,0,0,0.08)',
                    lineWidth: 0.5
                },
                title: {
                    display: true,
                    text: ' ',
                    font: { size: 10 }
                }
            },
            x: {
                offset: false,
                title: {
                    display: true,
                    text: ' ',
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