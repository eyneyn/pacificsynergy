@extends('layouts.app')

@section('content')

<!-- Back Button -->
<a href="{{url('analytics/index')}}" class="flex items-center text-sm text-gray-500 hover:text-[#2d326b] mb-4">
<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
</svg>
Analytics and Report
</a>

<!-- Heading -->
<h2 class="text-lg font-semibold text-[#2d326b] tracking-wider mb-4">
    Production Line 1 (Year 2024)
</h2>

<div class="flex flex-col md:flex-row md:items-center justify-between mb-4">

{{-- <!-- Left-side filters styled like tab nav -->
<div class="flex space-x-4 text-sm text-[#2d326b] mb-4 md:mb-0">
    <a href="?filter=all" class="px-4 py-2 border-b-2 border-[#2d326b] font-semibold text-[#2d326b] hover:text-[#6B7280] transition duration-200">
        All
    </a>
    <a href="?filter=qtd" class="px-4 py-2 border-b-2 border-transparent font-semibold text-gray-500 hover:text-[#2d326b] hover:border-[#2d326b] transition duration-200">
        Quarter-To-Date
    </a>
    <a href="?filter=mtd" class="px-4 py-2 border-b-2 border-transparent font-semibold text-gray-500 hover:text-[#2d326b] hover:border-[#2d326b] transition duration-200">
        Month-To-Date
    </a>
</div> --}}

    <div class="w-[30]">
        <!-- Year Selection -->
        <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2 text-[#2d326b]">
            <label for="date" class="whitespace-nowrap text-sm">Production Year:</label>
            <x-select-dropdown name="date" placeholder="Year" :options="['2024' => '2024']" />
        </form>
    </div>


<!-- Right-side controls: Year dropdown + Export -->
<div class="flex flex-col md:flex-row items-start md:items-center gap-3">

    <!-- Export Button -->
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

<!-- Elegant Divider -->
<div class="w-full flex items-center justify-center my-6">
<div class="w-full border-t border-[#E5E7EB]"></div>
</div>

<!-- 🧭 Chart + Cards Layout -->
<div class="w-full flex flex-col xl:flex-row gap-4 mb-8">

    <!-- 📈 Chart + Table Section (wider) -->
    <div class="w-full xl:w-3/4 bg-white rounded-sm border border-gray-200 p-6 shadow-md space-y-5 transition-all duration-300 hover:shadow-xl hover:border-[#E5E7EB] flex flex-col">
        <h2 class="text-lg font-semibold mb-4 text-[#2d326b]">Material Efficiency</h2>

        <canvas id="efficiencyChart" height="300" class="w-full !pl-0 !ml-0 !translate-x-0"></canvas>

        <!-- 📋 Table -->
        <div class="overflow-x-auto mt-4">
            <table class="min-w-full text-[10px] border border-gray-300 text-gray-700 table-auto">
                <thead class="bg-[#f1f5f9] font-semibold text-gray-800 text-center">
                    <tr>
                        <th class="border border-gray-300 px-2 py-1 text-left w-[130px]">Indicator</th>
                        @foreach (['1','2','3','4','5','6','7','8','9','10','11','12'] as $month)
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
                        @foreach ([38950,172277,237982,193767,308612,138176,254969,263036,282720,274888,279835,224552] as $item)
                            <td class="border border-gray-300 px-2 py-1 text-right">{{ number_format($item) }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="border border-gray-300 px-2 py-1 font-semibold text-left text-[#1e3a8a]">PREFORMS</td>
                        @foreach (['0.16%','0.24%','0.18%','0.21%','0.19%','0.42%','0.50%','0.33%','0.39%','0.17%','0.32%','0.47%'] as $item)
                            <td class="border border-gray-300 px-2 py-1 text-right">{{ $item }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="border border-gray-300 px-2 py-1 font-semibold text-left text-[#334155]">CAPS</td>
                        @foreach (['0.14%','0.19%','0.11%','0.13%','0.18%','0.21%','0.26%','0.26%','0.19%','0.14%','0.14%','0.17%'] as $item)
                            <td class="border border-gray-300 px-2 py-1 text-right">{{ $item }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="border border-gray-300 px-2 py-1 font-semibold text-left text-[#0f172a]">OPP LABELS</td>
                        @foreach (['0.02%','0.03%','0.02%','0.07%','0.03%','0.06%','0.08%','0.08%','0.06%','0.06%','0.04%','0.06%'] as $item)
                            <td class="border border-gray-300 px-2 py-1 text-right">{{ $item }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="border border-gray-300 px-2 py-1 font-semibold text-left text-[#b45309]">LD PE SHRINK FILM</td>
                        @foreach (['0.13%','0.27%','0.15%','0.17%','0.11%','0.26%','0.33%','0.59%','0.31%','0.27%','0.18%','0.23%'] as $item)
                            <td class="border border-gray-300 px-2 py-1 text-right">{{ $item }}</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 📦 Right-side Cards (narrower) -->
    <div class="w-full xl:w-1/4 flex flex-col justify-between self-stretch">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 h-full">
         <!-- Full Width Output Card -->
<div class="col-span-1 sm:col-span-2 bg-white hover:bg-[#e5f4ff] rounded-xl shadow border border-gray-200 p-3 h-28 flex items-center justify-center">
    <div class="text-center">
        <h3 class="text-sm text-[#2d326b] mb-1">Production Output</h3>
        <p class="text-lg font-semibold text-[#4b5563]">2,827,724</p>
    </div>
</div>

<!-- Individual Cards -->
@php
    $cards = [
        ['title' => 'Preform', 'value' => '1.17%', 'color' => '#4b5563'],
        ['title' => 'Caps', 'value' => '0.18%', 'color' => '#1e3a8a'],
        ['title' => 'OPP', 'value' => '0.06%', 'color' => '#166534'],
        ['title' => 'Shrinkfilm', 'value' => '0.25%', 'color' => '#7c2d12'],
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

<!-- Month Buttons -->
<div class="col-span-1 sm:col-span-2 bg-white rounded-xl shadow border border-gray-200 p-4">
    <h3 class="text-sm text-[#2d326b] text-center mb-3 font-semibold">Select Month</h3>
    <div class="grid grid-cols-3 gap-2">
        @foreach ([
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ] as $month)
            <button type="button"
                class="text-xs text-[#2d326b] border border-gray-300 rounded-md py-1 px-2 hover:bg-[#2d326b] hover:text-white transition duration-150">
                {{ $month }}
            </button>
        @endforeach
    </div>
</div>

        </div>
    </div>

</div>



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

     <div>
<p class="text-sm text-[#2d326b] font-medium mb-2">Production output</p>

<!-- Production Output Table -->
<table class="w-full text-xs text-left border border-[#E5E7EB] border-collapse">
    <thead class="text-xs text-white uppercase bg-[#35408e]">
    <tr>
        <th class="p-2 border border-[#d9d9d9]"></th>
        <th class="p-2 border border-[#d9d9d9] text-center">Q1</th>
        <th class="p-2 border border-[#d9d9d9] text-center">Q2</th>
        <th class="p-2 border border-[#d9d9d9] text-center">Q3</th>
        <th class="p-2 border border-[#d9d9d9] text-center">Q4</th>
        <th class="p-2 border border-[#d9d9d9] text-center">TOTAL</th>
    </tr>
</thead>
<tbody>
<tr class="bg-white hover:bg-[#e5f4ff] cursor-pointer">
<td class="p-2 border border-[#d9d9d9] text-[#2d326b]">Production Output</td>
<td class="p-2 border border-[#d9d9d9] text-center">609169</td>
<td class="p-2 border border-[#d9d9d9] text-center">638555</td>
<td class="p-2 border border-[#d9d9d9] text-center">800725</td>
<td class="p-2 border border-[#d9d9d9] text-center">779275</td>
<td class="p-2 border border-[#d9d9d9] text-center">2827724</td>
</tr>
</tbody>
</table>    
</div>

<div>
<p class="text-sm text-[#2d326b] font-medium mb-2">Preforms</p>

<!-- Preforms Table -->
<table class="w-full text-xs text-left border border-[#E5E7EB] border-collapse">
<thead class="text-xs text-white uppercase bg-[#35408e]">
    <tr>
        <th class="p-2 border border-[#d9d9d9]"></th>
        <th class="p-2 border border-[#d9d9d9] text-center">Q1</th>
        <th class="p-2 border border-[#d9d9d9] text-center">Q2</th>
        <th class="p-2 border border-[#d9d9d9] text-center">Q3</th>
        <th class="p-2 border border-[#d9d9d9] text-center">Q4</th>
        <th class="p-2 border border-[#d9d9d9] text-center">TOTAL</th>
    </tr>
</thead>
<tbody>
    <tr class="bg-white hover:bg-[#e5f4ff] cursor-pointer">
        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">FG Usage</td>
        <td class="p-2 border border-[#d9d9d9] text-center">17,544,296</td>
        <td class="p-2 border border-[#d9d9d9] text-center">13,903,713</td>
        <td class="p-2 border border-[#d9d9d9] text-center">22,042,925</td>
        <td class="p-2 border border-[#d9d9d9] text-center">21,757,740</td>
        <td class="p-2 border border-[#d9d9d9] text-center">75,248,674</td>
    </tr>
    <tr class="bg-white hover:bg-[#e5f4ff] cursor-pointer">
        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">Rejects</td>
        <td class="p-2 border border-[#d9d9d9] text-center">33,974</td>
        <td class="p-2 border border-[#d9d9d9] text-center">35,357</td>
        <td class="p-2 border border-[#d9d9d9] text-center">89,627</td>
        <td class="p-2 border border-[#d9d9d9] text-center">62,851</td>
        <td class="p-2 border border-[#d9d9d9] text-center">221,809</td>
    </tr>
    <tr class="bg-white hover:bg-[#e5f4ff] cursor-pointer">
        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">QA Samples</td>
        <td class="p-2 border border-[#d9d9d9] text-center">12,869</td>
        <td class="p-2 border border-[#d9d9d9] text-center">12,611</td>
        <td class="p-2 border border-[#d9d9d9] text-center">17,299</td>
        <td class="p-2 border border-[#d9d9d9] text-center">16,486</td>
        <td class="p-2 border border-[#d9d9d9] text-center">59,265</td>
    </tr>
    <tr class="bg-white hover:bg-[#e5f4ff] cursor-pointer">
        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">% Rejects</td>
        <td class="p-2 border border-[#d9d9d9] text-center">0.19%</td>
        <td class="p-2 border border-[#d9d9d9] text-center">0.25%</td>
        <td class="p-2 border border-[#d9d9d9] text-center">0.40%</td>
        <td class="p-2 border border-[#d9d9d9] text-center">0.29%</td>
        <td class="p-2 border border-[#d9d9d9] text-center">0.29%</td>
    </tr>
</tbody>
</table>
</div>

<div>
<p class="text-sm text-[#2d326b] font-medium mb-2">Caps</p>

<!-- Caps Table -->
<table class="w-full text-xs text-left border border-[#E5E7EB] border-collapse">
<thead class="text-xs text-white uppercase bg-[#35408e]">
    <tr>
        <th class="p-2 border border-[#d9d9d9]"></th>
        <th class="p-2 border border-[#d9d9d9] text-center">Q1</th>
        <th class="p-2 border border-[#d9d9d9] text-center">Q2</th>
        <th class="p-2 border border-[#d9d9d9] text-center">Q3</th>
        <th class="p-2 border border-[#d9d9d9] text-center">Q4</th>
        <th class="p-2 border border-[#d9d9d9] text-center">TOTAL</th>
    </tr>
</thead>
<tbody>
    <tr class="bg-white hover:bg-[#e5f4ff] cursor-pointer">
        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">FG Usage</td>
        <td class="p-2 border border-[#d9d9d9] text-center">17,544,296</td>
        <td class="p-2 border border-[#d9d9d9] text-center">13,903,713</td>
        <td class="p-2 border border-[#d9d9d9] text-center">22,042,925</td>
        <td class="p-2 border border-[#d9d9d9] text-center">21,757,740</td>
        <td class="p-2 border border-[#d9d9d9] text-center">75,248,674</td>
    </tr>
    <tr class="bg-white hover:bg-[#e5f4ff] cursor-pointer">
        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">Rejects</td>
        <td class="p-2 border border-[#d9d9d9] text-center">33,974</td>
        <td class="p-2 border border-[#d9d9d9] text-center">35,357</td>
        <td class="p-2 border border-[#d9d9d9] text-center">89,627</td>
        <td class="p-2 border border-[#d9d9d9] text-center">62,851</td>
        <td class="p-2 border border-[#d9d9d9] text-center">221,809</td>
    </tr>
    <tr class="bg-white hover:bg-[#e5f4ff] cursor-pointer">
        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">QA Samples</td>
        <td class="p-2 border border-[#d9d9d9] text-center">12,869</td>
        <td class="p-2 border border-[#d9d9d9] text-center">12,611</td>
        <td class="p-2 border border-[#d9d9d9] text-center">17,299</td>
        <td class="p-2 border border-[#d9d9d9] text-center">16,486</td>
        <td class="p-2 border border-[#d9d9d9] text-center">59,265</td>
    </tr>
    <tr class="bg-white hover:bg-[#e5f4ff] cursor-pointer">
        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">% Rejects</td>
        <td class="p-2 border border-[#d9d9d9] text-center">0.19%</td>
        <td class="p-2 border border-[#d9d9d9] text-center">0.25%</td>
        <td class="p-2 border border-[#d9d9d9] text-center">0.40%</td>
        <td class="p-2 border border-[#d9d9d9] text-center">0.29%</td>
        <td class="p-2 border border-[#d9d9d9] text-center">0.29%</td>
    </tr>
</tbody>
</table>
</div>

<div>
<p class="text-sm text-[#2d326b] font-medium mb-2">OPP Labels</p>

<!-- OPP Labels Table -->
<table class="w-full text-xs text-left border border-[#E5E7EB] border-collapse">
<thead class="text-xs text-white uppercase bg-[#35408e]">
    <tr>
        <th class="p-2 border border-[#d9d9d9]"></th>
        <th class="p-2 border border-[#d9d9d9] text-center">Q1</th>
        <th class="p-2 border border-[#d9d9d9] text-center">Q2</th>
        <th class="p-2 border border-[#d9d9d9] text-center">Q3</th>
        <th class="p-2 border border-[#d9d9d9] text-center">Q4</th>
        <th class="p-2 border border-[#d9d9d9] text-center">TOTAL</th>
    </tr>
</thead>
<tbody>
    <tr class="bg-white hover:bg-[#e5f4ff] cursor-pointer">
        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">FG Usage</td>
        <td class="p-2 border border-[#d9d9d9] text-center">17,544,296</td>
        <td class="p-2 border border-[#d9d9d9] text-center">13,903,713</td>
        <td class="p-2 border border-[#d9d9d9] text-center">22,042,925</td>
        <td class="p-2 border border-[#d9d9d9] text-center">21,757,740</td>
        <td class="p-2 border border-[#d9d9d9] text-center">75,248,674</td>
    </tr>
    <tr class="bg-white hover:bg-[#e5f4ff] cursor-pointer">
        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">Rejects</td>
        <td class="p-2 border border-[#d9d9d9] text-center">33,974</td>
        <td class="p-2 border border-[#d9d9d9] text-center">35,357</td>
        <td class="p-2 border border-[#d9d9d9] text-center">89,627</td>
        <td class="p-2 border border-[#d9d9d9] text-center">62,851</td>
        <td class="p-2 border border-[#d9d9d9] text-center">221,809</td>
    </tr>
    <tr class="bg-white hover:bg-[#e5f4ff] cursor-pointer">
        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">QA Samples</td>
        <td class="p-2 border border-[#d9d9d9] text-center">12,869</td>
        <td class="p-2 border border-[#d9d9d9] text-center">12,611</td>
        <td class="p-2 border border-[#d9d9d9] text-center">17,299</td>
        <td class="p-2 border border-[#d9d9d9] text-center">16,486</td>
        <td class="p-2 border border-[#d9d9d9] text-center">59,265</td>
    </tr>
    <tr class="bg-white hover:bg-[#e5f4ff] cursor-pointer">
        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">% Rejects</td>
        <td class="p-2 border border-[#d9d9d9] text-center">0.19%</td>
        <td class="p-2 border border-[#d9d9d9] text-center">0.25%</td>
        <td class="p-2 border border-[#d9d9d9] text-center">0.40%</td>
        <td class="p-2 border border-[#d9d9d9] text-center">0.29%</td>
        <td class="p-2 border border-[#d9d9d9] text-center">0.29%</td>
    </tr>
</tbody>
</table>
</div>


<div>
<p class="text-sm text-[#2d326b] font-medium mb-2">LDPE Shrinkfilm </p>

<!-- LDPE Shrinkfilm Table -->
<table class="w-full text-xs text-left border border-[#E5E7EB] border-collapse">
<thead class="text-xs text-white uppercase bg-[#35408e]">
    <tr>
        <th class="p-2 border border-[#d9d9d9]"></th>
        <th class="p-2 border border-[#d9d9d9] text-center">Q1</th>
        <th class="p-2 border border-[#d9d9d9] text-center">Q2</th>
        <th class="p-2 border border-[#d9d9d9] text-center">Q3</th>
        <th class="p-2 border border-[#d9d9d9] text-center">Q4</th>
        <th class="p-2 border border-[#d9d9d9] text-center">TOTAL</th>
    </tr>
</thead>
<tbody>
    <tr class="bg-white hover:bg-[#e5f4ff] cursor-pointer">
        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">FG Usage</td>
        <td class="p-2 border border-[#d9d9d9] text-center">17,544,296</td>
        <td class="p-2 border border-[#d9d9d9] text-center">13,903,713</td>
        <td class="p-2 border border-[#d9d9d9] text-center">22,042,925</td>
        <td class="p-2 border border-[#d9d9d9] text-center">21,757,740</td>
        <td class="p-2 border border-[#d9d9d9] text-center">75,248,674</td>
    </tr>
    <tr class="bg-white hover:bg-[#e5f4ff] cursor-pointer">
        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">Rejects</td>
        <td class="p-2 border border-[#d9d9d9] text-center">33,974</td>
        <td class="p-2 border border-[#d9d9d9] text-center">35,357</td>
        <td class="p-2 border border-[#d9d9d9] text-center">89,627</td>
        <td class="p-2 border border-[#d9d9d9] text-center">62,851</td>
        <td class="p-2 border border-[#d9d9d9] text-center">221,809</td>
    </tr>
    <tr class="bg-white hover:bg-[#e5f4ff] cursor-pointer">
        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">QA Samples</td>
        <td class="p-2 border border-[#d9d9d9] text-center">12,869</td>
        <td class="p-2 border border-[#d9d9d9] text-center">12,611</td>
        <td class="p-2 border border-[#d9d9d9] text-center">17,299</td>
        <td class="p-2 border border-[#d9d9d9] text-center">16,486</td>
        <td class="p-2 border border-[#d9d9d9] text-center">59,265</td>
    </tr>
    <tr class="bg-white hover:bg-[#e5f4ff] cursor-pointer">
        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">% Rejects</td>
        <td class="p-2 border border-[#d9d9d9] text-center">0.19%</td>
        <td class="p-2 border border-[#d9d9d9] text-center">0.25%</td>
        <td class="p-2 border border-[#d9d9d9] text-center">0.40%</td>
        <td class="p-2 border border-[#d9d9d9] text-center">0.29%</td>
        <td class="p-2 border border-[#d9d9d9] text-center">0.29%</td>
    </tr>
</tbody>
</table>
</div>


<div>
<p class="text-sm text-[#2d326b] font-medium mb-2">Preforms</p>

<!-- Preforms Table -->
<table class="w-full text-xs text-left border border-[#E5E7EB] border-collapse">
<thead class="text-xs text-white uppercase bg-[#35408e]">
    <tr>
        <th class="p-2 border border-[#d9d9d9]"></th>
        <th class="p-2 border border-[#d9d9d9] text-center">Q1</th>
        <th class="p-2 border border-[#d9d9d9] text-center">Q2</th>
        <th class="p-2 border border-[#d9d9d9] text-center">Q3</th>
        <th class="p-2 border border-[#d9d9d9] text-center">Q4</th>
        <th class="p-2 border border-[#d9d9d9] text-center">TOTAL</th>
    </tr>
</thead>
<tbody>
    <tr class="bg-white hover:bg-[#e5f4ff] cursor-pointer">
        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">FG Usage</td>
        <td class="p-2 border border-[#d9d9d9] text-center">17,544,296</td>
        <td class="p-2 border border-[#d9d9d9] text-center">13,903,713</td>
        <td class="p-2 border border-[#d9d9d9] text-center">22,042,925</td>
        <td class="p-2 border border-[#d9d9d9] text-center">21,757,740</td>
        <td class="p-2 border border-[#d9d9d9] text-center">75,248,674</td>
    </tr>
    <tr class="bg-white hover:bg-[#e5f4ff] cursor-pointer">
        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">Rejects</td>
        <td class="p-2 border border-[#d9d9d9] text-center">33,974</td>
        <td class="p-2 border border-[#d9d9d9] text-center">35,357</td>
        <td class="p-2 border border-[#d9d9d9] text-center">89,627</td>
        <td class="p-2 border border-[#d9d9d9] text-center">62,851</td>
        <td class="p-2 border border-[#d9d9d9] text-center">221,809</td>
    </tr>
    <tr class="bg-white hover:bg-[#e5f4ff] cursor-pointer">
        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">QA Samples</td>
        <td class="p-2 border border-[#d9d9d9] text-center">12,869</td>
        <td class="p-2 border border-[#d9d9d9] text-center">12,611</td>
        <td class="p-2 border border-[#d9d9d9] text-center">17,299</td>
        <td class="p-2 border border-[#d9d9d9] text-center">16,486</td>
        <td class="p-2 border border-[#d9d9d9] text-center">59,265</td>
    </tr>
    <tr class="bg-white hover:bg-[#e5f4ff] cursor-pointer">
        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">% Rejects</td>
        <td class="p-2 border border-[#d9d9d9] text-center">0.19%</td>
        <td class="p-2 border border-[#d9d9d9] text-center">0.25%</td>
        <td class="p-2 border border-[#d9d9d9] text-center">0.40%</td>
        <td class="p-2 border border-[#d9d9d9] text-center">0.29%</td>
        <td class="p-2 border border-[#d9d9d9] text-center">0.29%</td>
    </tr>
</tbody>
</table>
</div>
    </div>
</div>

</div>


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

<div>
<p class="text-sm text-[#2d326b] font-medium mb-2">Production output</p>

<!-- Production Output Table -->
<table class="w-full text-xs text-left border border-[#E5E7EB] border-collapse">
    <thead class="text-xs text-white uppercase bg-[#35408e]">
    <tr>
        <th class="p-2 border border-[#d9d9d9]"></th>
        <th class="p-2 border border-[#d9d9d9] text-center">1</th>
        <th class="p-2 border border-[#d9d9d9] text-center">2</th>
        <th class="p-2 border border-[#d9d9d9] text-center">3</th>
        <th class="p-2 border border-[#d9d9d9] text-center">4</th>
        <th class="p-2 border border-[#d9d9d9] text-center">5</th>
        <th class="p-2 border border-[#d9d9d9] text-center">6</th>
        <th class="p-2 border border-[#d9d9d9] text-center">7</th>
        <th class="p-2 border border-[#d9d9d9] text-center">8</th>
        <th class="p-2 border border-[#d9d9d9] text-center">9</th>
        <th class="p-2 border border-[#d9d9d9] text-center">10</th>
        <th class="p-2 border border-[#d9d9d9] text-center">11</th>
        <th class="p-2 border border-[#d9d9d9] text-center">12</th>
    </tr>
</thead>
<tbody>
<tr class="bg-white hover:bg-[#e5f4ff] cursor-pointer">
<td class="p-2 border border-[#d9d9d9] text-[#2d326b]">Target Mat'l Efficiency, %</td>
<td class="p-2 border border-[#d9d9d9] text-center">1.00%</td>
<td class="p-2 border border-[#d9d9d9] text-center">1.00%</td>
<td class="p-2 border border-[#d9d9d9] text-center">1.00%</td>
<td class="p-2 border border-[#d9d9d9] text-center">1.00%</td>
<td class="p-2 border border-[#d9d9d9] text-center">1.00%</td>
<td class="p-2 border border-[#d9d9d9] text-center">1.00%</td>
<td class="p-2 border border-[#d9d9d9] text-center">1.00%</td>
<td class="p-2 border border-[#d9d9d9] text-center">1.00%</td>
<td class="p-2 border border-[#d9d9d9] text-center">1.00%</td>
<td class="p-2 border border-[#d9d9d9] text-center">1.00%</td>
<td class="p-2 border border-[#d9d9d9] text-center">1.00%</td>
<td class="p-2 border border-[#d9d9d9] text-center">1.00%</td>
</tr>

<tr class="bg-white hover:bg-[#e5f4ff] cursor-pointer">
<td class="p-2 border border-[#d9d9d9] text-[#2d326b]">Production Output, Cs</td>
<td class="p-2 border border-[#d9d9d9] text-center">198960</td>
<td class="p-2 border border-[#d9d9d9] text-center">172227</td>
<td class="p-2 border border-[#d9d9d9] text-center">237982</td>
<td class="p-2 border border-[#d9d9d9] text-center">193767</td>
<td class="p-2 border border-[#d9d9d9] text-center">308612</td>
<td class="p-2 border border-[#d9d9d9] text-center">136176</td>
<td class="p-2 border border-[#d9d9d9] text-center">254969</td>
<td class="p-2 border border-[#d9d9d9] text-center">263036</td>
<td class="p-2 border border-[#d9d9d9] text-center">282720</td>
<td class="p-2 border border-[#d9d9d9] text-center">274888</td>
<td class="p-2 border border-[#d9d9d9] text-center">279835</td>
<td class="p-2 border border-[#d9d9d9] text-center">224552</td>
</tr>
</tbody>
</table>    
</div>

<div>
<p class="text-sm text-[#2d326b] font-medium mb-2">Preforms</p>

<!-- Preforms Table -->
<table class="w-full mt-4 text-[11px] text-left border border-[#E5E7EB] border-collapse">
<thead class="uppercase bg-[#35408e] text-white">
    <tr>
        <th class="p-1 border border-[#d9d9d9] text-center"></th>
        @for ($i = 1; $i <= 12; $i++)
            <th class="p-2 border border-[#d9d9d9] text-center">{{ $i }}</th>
        @endfor
    </tr>
</thead>
<tbody>
    @php
        $fgUsage =    [5897953, 5037725, 6608618, 5445576, 5326861, 3131276, 7007506, 7322268, 7713151, 7607893, 7930055, 6219792];
        $rejects =    [9571,    12191,   12212,   11624,   10370,   13363,   35071,   24241,   30315,   15097,   18251,   29503];
        $qaSamples =  [4013,    3887,    4969,    4139,    5672,    2800,    5623,    5759,    5917,    5666,    5863,    4957];
        $rejectRates = ['0.16%', '0.24%', '0.18%', '0.21%', '0.19%', '0.42%', '0.50%', '0.33%', '0.39%', '0.20%', '0.23%', '0.47%'];
    @endphp

    {{-- FG Usage Row --}}
    <tr class="bg-white hover:bg-[#f1f5f9] transition">
        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">FG Usage</td>
        @foreach ($fgUsage as $value)
            <td class="p-2 border border-[#d9d9d9] text-center">{{ number_format($value) }}</td>
        @endforeach
    </tr>

    {{-- Rejects Row --}}
    <tr class="bg-white hover:bg-[#f1f5f9] transition">
        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">Rejects</td>
        @foreach ($rejects as $value)
            <td class="p-2 border border-[#d9d9d9] text-center">{{ number_format($value) }}</td>
        @endforeach
    </tr>

    {{-- QA Samples Row --}}
    <tr class="bg-white hover:bg-[#f1f5f9] transition">
        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">QA Samples</td>
        @foreach ($qaSamples as $value)
            <td class="p-2 border border-[#d9d9d9] text-center">{{ number_format($value) }}</td>
        @endforeach
    </tr>

    {{-- % Rejects Row --}}
    <tr class="bg-white hover:bg-[#f1f5f9] transition">
        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">% Rejects</td>
        @foreach ($rejectRates as $value)
            <td class="p-2 border border-[#d9d9d9] text-center">{{ $value }}</td>
        @endforeach
    </tr>
</tbody>
</table>
    
</div>

<div>
<p class="text-sm text-[#2d326b] font-medium mb-2">Caps</p>

<!-- Caps Table -->
<table class="w-full mt-4 text-[11px] text-left border border-[#E5E7EB] border-collapse">
<thead class="uppercase bg-[#35408e] text-white">
    <tr>
        <th class="p-1 border border-[#d9d9d9] text-center"></th>
        @for ($i = 1; $i <= 12; $i++)
            <th class="p-2 border border-[#d9d9d9] text-center">{{ $i }}</th>
        @endfor
    </tr>
</thead>
<tbody>
    @php
        $fgUsage =    [5897953, 5037725, 6608618, 5445576, 5326861, 3131276, 7007506, 7322268, 7713151, 7607893, 7930055, 6219792];
        $rejects =    [9571,    12191,   12212,   11624,   10370,   13363,   35071,   24241,   30315,   15097,   18251,   29503];
        $qaSamples =  [4013,    3887,    4969,    4139,    5672,    2800,    5623,    5759,    5917,    5666,    5863,    4957];
        $rejectRates = ['0.16%', '0.24%', '0.18%', '0.21%', '0.19%', '0.42%', '0.50%', '0.33%', '0.39%', '0.20%', '0.23%', '0.47%'];
    @endphp

    {{-- FG Usage Row --}}
    <tr class="bg-white hover:bg-[#f1f5f9] transition">
        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">FG Usage</td>
        @foreach ($fgUsage as $value)
            <td class="p-2 border border-[#d9d9d9] text-center">{{ number_format($value) }}</td>
        @endforeach
    </tr>

    {{-- Rejects Row --}}
    <tr class="bg-white hover:bg-[#f1f5f9] transition">
        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">Rejects</td>
        @foreach ($rejects as $value)
            <td class="p-2 border border-[#d9d9d9] text-center">{{ number_format($value) }}</td>
        @endforeach
    </tr>

    {{-- QA Samples Row --}}
    <tr class="bg-white hover:bg-[#f1f5f9] transition">
        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">QA Samples</td>
        @foreach ($qaSamples as $value)
            <td class="p-2 border border-[#d9d9d9] text-center">{{ number_format($value) }}</td>
        @endforeach
    </tr>

    {{-- % Rejects Row --}}
    <tr class="bg-white hover:bg-[#f1f5f9] transition">
        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">% Rejects</td>
        @foreach ($rejectRates as $value)
            <td class="p-2 border border-[#d9d9d9] text-center">{{ $value }}</td>
        @endforeach
    </tr>
</tbody>
</table>
    
</div>

<div>
<p class="text-sm text-[#2d326b] font-medium mb-2">OPP Labels</p>

<!-- Caps Table -->
<table class="w-full mt-4 text-[11px] text-left border border-[#E5E7EB] border-collapse">
<thead class="uppercase bg-[#35408e] text-white">
    <tr>
        <th class="p-1 border border-[#d9d9d9] text-center"></th>
        @for ($i = 1; $i <= 12; $i++)
            <th class="p-2 border border-[#d9d9d9] text-center">{{ $i }}</th>
        @endfor
    </tr>
</thead>
<tbody>
    @php
        $fgUsage =    [5897953, 5037725, 6608618, 5445576, 5326861, 3131276, 7007506, 7322268, 7713151, 7607893, 7930055, 6219792];
        $rejects =    [9571,    12191,   12212,   11624,   10370,   13363,   35071,   24241,   30315,   15097,   18251,   29503];
        $qaSamples =  [4013,    3887,    4969,    4139,    5672,    2800,    5623,    5759,    5917,    5666,    5863,    4957];
        $rejectRates = ['0.16%', '0.24%', '0.18%', '0.21%', '0.19%', '0.42%', '0.50%', '0.33%', '0.39%', '0.20%', '0.23%', '0.47%'];
    @endphp

    {{-- FG Usage Row --}}
    <tr class="bg-white hover:bg-[#f1f5f9] transition">
        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">FG Usage</td>
        @foreach ($fgUsage as $value)
            <td class="p-2 border border-[#d9d9d9] text-center">{{ number_format($value) }}</td>
        @endforeach
    </tr>

    {{-- Rejects Row --}}
    <tr class="bg-white hover:bg-[#f1f5f9] transition">
        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">Rejects</td>
        @foreach ($rejects as $value)
            <td class="p-2 border border-[#d9d9d9] text-center">{{ number_format($value) }}</td>
        @endforeach
    </tr>

    {{-- QA Samples Row --}}
    <tr class="bg-white hover:bg-[#f1f5f9] transition">
        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">QA Samples</td>
        @foreach ($qaSamples as $value)
            <td class="p-2 border border-[#d9d9d9] text-center">{{ number_format($value) }}</td>
        @endforeach
    </tr>

    {{-- % Rejects Row --}}
    <tr class="bg-white hover:bg-[#f1f5f9] transition">
        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">% Rejects</td>
        @foreach ($rejectRates as $value)
            <td class="p-2 border border-[#d9d9d9] text-center">{{ $value }}</td>
        @endforeach
    </tr>
</tbody>
</table>
    
</div>

<div>
<p class="text-sm text-[#2d326b] font-medium mb-2">LDPE Shrinkfilm</p>

<!-- Caps Table -->
<table class="w-full mt-4 text-[11px] text-left border border-[#E5E7EB] border-collapse">
<thead class="uppercase bg-[#35408e] text-white">
    <tr>
        <th class="p-1 border border-[#d9d9d9] text-center"></th>
        @for ($i = 1; $i <= 12; $i++)
            <th class="p-2 border border-[#d9d9d9] text-center">{{ $i }}</th>
        @endfor
    </tr>
</thead>
<tbody>
    @php
        $fgUsage =    [5897953, 5037725, 6608618, 5445576, 5326861, 3131276, 7007506, 7322268, 7713151, 7607893, 7930055, 6219792];
        $rejects =    [9571,    12191,   12212,   11624,   10370,   13363,   35071,   24241,   30315,   15097,   18251,   29503];
        $qaSamples =  [4013,    3887,    4969,    4139,    5672,    2800,    5623,    5759,    5917,    5666,    5863,    4957];
        $rejectRates = ['0.16%', '0.24%', '0.18%', '0.21%', '0.19%', '0.42%', '0.50%', '0.33%', '0.39%', '0.20%', '0.23%', '0.47%'];
    @endphp

    {{-- FG Usage Row --}}
    <tr class="bg-white hover:bg-[#f1f5f9] transition">
        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">FG Usage</td>
        @foreach ($fgUsage as $value)
            <td class="p-2 border border-[#d9d9d9] text-center">{{ number_format($value) }}</td>
        @endforeach
    </tr>

    {{-- Rejects Row --}}
    <tr class="bg-white hover:bg-[#f1f5f9] transition">
        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">Rejects</td>
        @foreach ($rejects as $value)
            <td class="p-2 border border-[#d9d9d9] text-center">{{ number_format($value) }}</td>
        @endforeach
    </tr>

    {{-- QA Samples Row --}}
    <tr class="bg-white hover:bg-[#f1f5f9] transition">
        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">QA Samples</td>
        @foreach ($qaSamples as $value)
            <td class="p-2 border border-[#d9d9d9] text-center">{{ number_format($value) }}</td>
        @endforeach
    </tr>

    {{-- % Rejects Row --}}
    <tr class="bg-white hover:bg-[#f1f5f9] transition">
        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">% Rejects</td>
        @foreach ($rejectRates as $value)
            <td class="p-2 border border-[#d9d9d9] text-center">{{ $value }}</td>
        @endforeach
    </tr>
</tbody>
</table>
</div>
        </div>
    
</div>
</div>


<!-- 📈 Chart.js Script -->
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
                data: [0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01],
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
                data: [0.0016, 0.0024, 0.0018, 0.0021, 0.0019, 0.0042, 0.0050, 0.0033, 0.0039, 0.0017, 0.0032, 0.0047],
                borderColor: '#1e3a8a',
                tension: 0.3,
                fill: false,
                borderWidth: 2,
                pointRadius: 3,
                pointHoverRadius: 5
            },
            {
                label: 'CAPS',
                data: [0.0014, 0.0019, 0.0011, 0.0013, 0.0018, 0.0021, 0.0026, 0.0026, 0.0019, 0.0014, 0.0014, 0.0017],
                borderColor: '#334155',
                tension: 0.3,
                fill: false,
                borderWidth: 2,
                pointRadius: 3,
                pointHoverRadius: 5
            },
            {
                label: 'OPP LABELS',
                data: [0.0002, 0.0003, 0.0002, 0.0007, 0.0003, 0.0006, 0.0008, 0.0008, 0.0006, 0.0006, 0.0004, 0.0006],
                borderColor: '#0f172a',
                tension: 0.3,
                fill: false,
                borderWidth: 2,
                pointRadius: 3,
                pointHoverRadius: 5
            },
            {
                label: 'LD PE SHRINK FILM',
                data: [0.0013, 0.0027, 0.0015, 0.0017, 0.0011, 0.0026, 0.0033, 0.0059, 0.0031, 0.0027, 0.0018, 0.0023],
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





{{-- <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">

    <div>

    
<p class="text-sm text-[#2d326b] font-medium mb-2">Production Output</p>

<table class="w-full mt-4 text-xs text-left border border-[#E5E7EB] border-collapse">
<thead class="text-white uppercase bg-[#35408e]">
    <tr>
        <th class="p-1 text-[11px] border border-[#d9d9d9]">Period</th>
        <th class="p-1 text-[11px] border border-[#d9d9d9] text-center">Month</th>
        <th class="p-1 text-[11px] border border-[#d9d9d9] text-center">Target Mat'l Efficiency, %</th>
        <th class="p-1 text-[11px] border border-[#d9d9d9] text-center">Production Output, Cs</th>
    </tr>
</thead>
<tbody>
    @php
        $data = [
            ['P1', 'JANUARY',     198960],
            ['P2', 'FEBRUARY',    172227],
            ['P3', 'MARCH',       237982],
            ['P4', 'APRIL',       193767],
            ['P5', 'MAY',         308612],
            ['P6', 'JUNE',        136176],
            ['P7', 'JULY',        254969],
            ['P8', 'AUGUST',      263036],
            ['P9', 'SEPTEMBER',   282720],
            ['P10', 'OCTOBER',    274888],
            ['P11', 'NOVEMBER',   279835],
            ['P12', 'DECEMBER',   224552],
        ];
    @endphp

    @foreach ($data as [$period, $month, $output])
        <tr class="bg-white hover:bg-[#e5f4ff] cursor-pointer">
            <td class="p-1 border border-[#d9d9d9] text-[#2d326b]">{{ $period }}</td>
            <td class="p-1 border border-[#d9d9d9] text-center">{{ $month }}</td>
            <td class="p-1 border border-[#d9d9d9] text-center">1.00%</td>
            <td class="p-1 border border-[#d9d9d9] text-center">{{ number_format($output) }}</td>
        </tr>
    @endforeach
</tbody>
</table>

<table class="w-full mt-4 text-xs text-left border border-[#E5E7EB] border-collapse">
<thead class="text-white uppercase bg-[#35408e]">
    <tr>
        <th class="p-1 text-[11px] border border-[#d9d9d9]" colspan="2">Quarter</th>
        <th class="p-1 text-[11px] border border-[#d9d9d9] text-center">Production Output, Cs</th>
    </tr>
</thead>
<tbody class="text-[#2d326b] text-[11px]">
    <tr class="bg-white hover:bg-[#e5f4ff] cursor-pointer">
        <td colspan="2" class="p-1 border border-[#d9d9d9] text-center">Q1</td>
        <td class="p-1 border border-[#d9d9d9] text-center">{{ number_format(198960 + 172227 + 237982) }}</td>
    </tr>
    <tr class="bg-white hover:bg-[#e5f4ff] cursor-pointer">
        <td colspan="2" class="p-1 border border-[#d9d9d9] text-center">Q2</td>
        <td class="p-1 border border-[#d9d9d9] text-center">{{ number_format(193767 + 308612 + 136176) }}</td>
    </tr>
    <tr class="bg-white hover:bg-[#e5f4ff] cursor-pointer">
        <td colspan="2" class="p-1 border border-[#d9d9d9] text-center">Q3</td>
        <td class="p-1 border border-[#d9d9d9] text-center">{{ number_format(254969 + 263036 + 282720) }}</td>
    </tr>
    <tr class="bg-white hover:bg-[#e5f4ff] cursor-pointer">
        <td colspan="2" class="p-1 border border-[#d9d9d9] text-center">Q4</td>
        <td class="p-1 border border-[#d9d9d9] text-center">{{ number_format(274888 + 279835 + 224552) }}</td>
    </tr>
    <tr class="bg-white font-bold">
        <td colspan="2" class="p-1 border border-[#d9d9d9] text-center">YTD</td>
        <td class="p-1 border border-[#d9d9d9] text-center text-[13px] text-black">{{ number_format(2827724) }}</td>
    </tr>
</tbody>
</table>


    </div>
<div>

<p class="text-sm text-[#2d326b] font-medium mb-2">Preforms</p>

<table class="w-full mt-4 text-xs text-left border border-[#E5E7EB] border-collapse">
<thead class="text-white uppercase bg-[#35408e]">
    <tr>
        <th class="p-1 text-[11px] border border-[#d9d9d9]">FG Usage</th>
        <th class="p-1 text-[11px] border border-[#d9d9d9] text-center">Rejects</th>
        <th class="p-1 text-[11px] border border-[#d9d9d9] text-center">QA Samples</th>
        <th class="p-1 text-[11px] border border-[#d9d9d9] text-center">% Rejects</th>
    </tr>
</thead>
<tbody class="text-[#2d326b]">
    @php
        $data = [
            [5897953,  9571,  4013, '0.16%'],
            [5037725, 12191, 3887, '0.24%'],
            [6608618, 12212, 4969, '0.18%'],
            [5445576, 11624, 4139, '0.21%'],
            [5326861, 10370, 5672, '0.19%'],
            [3131276, 13363, 2800, '0.42%'],
            [7007506, 35071, 5623, '0.50%'],
            [7322268, 24241, 5759, '0.33%'],
            [7713151, 30315, 5917, '0.39%'],
            [7607893, 15097, 5666, '0.20%'],
            [7930055, 18251, 5863, '0.23%'],
            [6219792, 29503, 4957, '0.47%'],
        ];
    @endphp

    @foreach ($data as [$usage, $rejects, $samples, $percent])
        <tr class="bg-white hover:bg-[#e5f4ff] cursor-pointer">
            <td class="p-1 border border-[#d9d9d9] text-center">{{ number_format($usage) }}</td>
            <td class="p-1 border border-[#d9d9d9] text-center">{{ number_format($rejects) }}</td>
            <td class="p-1 border border-[#d9d9d9] text-center">{{ number_format($samples) }}</td>
            <td class="p-1 border border-[#d9d9d9] text-center">{{ $percent }}</td>
        </tr>
    @endforeach
</tbody>
</table>

</div>
</div> --}}