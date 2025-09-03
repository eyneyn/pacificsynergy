@extends('layouts.app')

@section('content')

{{-- Page Title --}}
<h2 class="text-xl mb-2 font-bold text-[#23527c]">Line Efficiency Report</h2>

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

<h2 class="text-xl font-bold text-[#23527c] mt-4">
    Production Line {{ $selectedLine }} - Year {{ $year }}
</h2>

<!-- Divider -->
<div class="w-full flex items-center justify-center my-6">
    <div class="w-full border-t border-[#E5E7EB]"></div>
</div>


<!-- Chart + Cards Layout -->
<div class="w-full flex flex-col xl:flex-row gap-4 mb-8">

<!-- Left Side Chart With Inline Table -->
<div class="w-full xl:w-3/4 bg-white rounded-sm border border-gray-200 p-6 shadow-md space-y-5 transition-all duration-300 hover:shadow-xl hover:border-[#E5E7EB] flex flex-col">
    <h3 class="text-lg font-semibold mb-4 text-[#2d326b]">Line Efficiency</h3>
    <canvas id="leChart" height="300" class="w-full !pl-0 !ml-0 !translate-x-0"></canvas>

    <!-- Inline Table -->
    <div class="overflow-x-auto mt-4">
            <table class="min-w-full text-[10px] border border-gray-300 text-gray-700 table-auto">
                <thead class="bg-[#f1f5f9] font-semibold text-gray-800 text-center">
                <tr>
                    <th class="border border-gray-300 px-2 py-1 text-left w-[130px]"></th>
                    @foreach(['P1','P2','P3','P4','P5','P6','P7','P8','P9','P10','P11','P12','2023','2024'] as $period)
                        <th class="border border-gray-300 px-2 py-1 text-right">{{ $period }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="text-[9px]">
                <tr>
                    <td class="border border-gray-300 px-2 py-1 font-semibold text-left text-[#22D3EE]">EPL, %</td>
                    @foreach([12.0,11.6,8.3,9.8,7.3,13.7,16.0,10.6,8.7,10.0,6.8,14.4,'',''] as $val)
                        <td class="border border-gray-300 px-2 py-1 text-right">{{ $val !== '' ? number_format($val, 1).'%' : '' }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td class="border border-gray-300 px-2 py-1 font-semibold text-left text-[#6D28D9]">OPL, %</td>
                    @foreach([5.9,9.5,4.0,7.1,12.3,11.8,4.6,6.0,6.4,6.6,4.3,4.0,'',''] as $val)
                        <td class="border border-gray-300 px-2 py-1 text-right">{{ $val !== '' ? number_format($val, 1).'%' : '' }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td class="border border-gray-300 px-2 py-1 font-semibold text-left text-[#DC2626]">Yearly Trend</td>
                    @foreach([null,null,null,null,null,null,null,null,null,null,null,null,69.1,82.3] as $val)
                        <td class="border border-gray-300 px-2 py-1 text-right">{{ $val !== null ? number_format($val, 1).'%' : '' }}</td>
                    @endforeach
                </tr>
<tr>
    <td class="border border-gray-300 px-2 py-1 font-semibold text-left text-[#D1D5DB]">Target LE, %</td>
    @foreach(array_merge(array_fill(0, 12, 80), [null, null]) as $index => $val)
        @php
            $isYear = $index === 12 || $index === 13;
        @endphp
        <td class="border border-gray-300 px-2 py-1 text-right {{ $isYear ? 'border border-gray-300 px-2 py-1 text-right' : '' }}">
            {{ $val !== null ? $val.'%' : '' }}
        </td>
    @endforeach
</tr>
                <tr>
                    <td class="border border-gray-300 px-2 py-1 font-semibold text-left text-[#2563EB]">LE, %</td>
                    @foreach([82.1,78.9,87.6,83.1,80.4,74.5,79.4,83.3,84.9,83.4,88.9,81.6,null,null] as $val)
                        <td class="border border-gray-300 px-2 py-1 text-right">{{ $val !== null ? number_format($val, 1).'%' : '' }}</td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
</div>


    <!-- Right Side Table -->
    <div class="w-full xl:w-1/4 flex flex-col space-y-4">

        <!-- Efficiency Summary -->
        <div class="col-span-1 sm:col-span-2 bg-white hover:bg-[#e5f4ff] rounded-xl shadow border border-gray-200 p-3 h-28 flex items-center justify-center">
                <div class="text-center">
                        <h3 class="text-sm text-[#2d326b] mb-1">YTD Line Efficiency:</h3>
                <p class="text-lg font-semibold text-[#4b5563]">82.34%</p>
            </div>
        </div>

            <!-- Month Buttons -->
            <div class="col-span-1 sm:col-span-2 bg-white rounded-xl shadow border border-gray-200 p-4">
                <h3 class="text-sm text-[#2d326b] text-center mb-3 font-semibold">Select Month</h3>
                <div class="grid grid-cols-3 gap-2">
                    @foreach ([
                        'January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'
                    ] as $monthName)
                        <a href="{{ route('analytics.line.monthly_report', [
                            'month' => $monthName,
                            'line' => request('line'),
                            'date' => request('date')
                        ]) }}"
                        class="text-xs text-[#2d326b] text-center border border-gray-300 rounded-md py-1 px-2 hover:bg-[#2d326b] hover:text-white transition duration-150">
                            {{ $monthName }}
                        </a>
                    @endforeach
                </div>
            </div>
    </div>
</div>


<!-- Month-To-Date Report Section -->
<div class="w-full mb-8 cursor-pointer bg-white rounded-sm border border-gray-200 p-6 shadow-md hover:shadow-xl hover:border-[#E5E7EB]">
    <div class="flex items-center justify-between">
        <p class="text-lg text-[#2d326b] font-semibold">Month-To-Date Report</p>
    </div>

    <!-- Expandable Content -->
    <div>
        <div class="space-y-4 mt-2">
            <table class="w-full text-xs text-left border border-[#E5E7EB] border-collapse">
                <thead class="text-xs text-white uppercase bg-[#35408e]">
                    <tr>
                        <th class="p-2 border border-[#d9d9d9] text-center"></th>
                        @foreach(['P1','P2','P3','P4','P5','P6','P7','P8','P9','P10','P11','P12','YTD'] as $period)
                            <th class="p-2 border border-[#d9d9d9] text-center">{{ $period }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-white hover:bg-[#f1f5f9] cursor-pointer">
                        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">Target LE, %</td>
                        @foreach(array_fill(0, 12, '80%') as $val)
                            <td class="p-2 border border-[#d9d9d9] text-center">{{ $val }}</td>
                        @endforeach
                        <td class="p-2 border border-[#d9d9d9] text-center"></td>
                    </tr>
                    <tr class="bg-white hover:bg-[#f1f5f9] cursor-pointer">
                        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">LE, %</td>
                        @foreach([82.1, 78.9, 87.6, 83.1, 80.4, 74.5, 79.4, 83.3, 84.9, 83.4, 88.9, 81.6] as $val)
                            <td class="p-2 border border-[#d9d9d9] text-center">{{ number_format($val, 1).'%' }}</td>
                        @endforeach
                        <td class="p-2 border text-center font-semibold text-[#1e3a8a]">82.3%</td>
                    </tr>
                    <tr class="bg-white hover:bg-[#f1f5f9] cursor-pointer">
                        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">OPL, %</td>
                        @foreach([5.9, 9.5, 4.0, 7.1, 12.3, 11.8, 4.6, 6.0, 6.4, 6.6, 4.3, 4.0] as $val)
                            <td class="p-2 border border-[#d9d9d9] text-center">{{ number_format($val, 1).'%' }}</td>
                        @endforeach
                        <td class="p-2 border text-center font-semibold text-[#1e3a8a]">6.9%</td>
                    </tr>
                    <tr class="bg-white hover:bg-[#f1f5f9] cursor-pointer">
                        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">EPL, %</td>
                        @foreach([12.0, 11.6, 8.3, 9.8, 7.3, 13.7, 16.0, 10.6, 8.7, 10.0, 6.8, 14.4] as $val)
                            <td class="p-2 border border-[#d9d9d9] text-center">{{ number_format($val, 1).'%' }}</td>
                        @endforeach
                        <td class="p-2 border text-center font-semibold text-[#1e3a8a]">10.8%</td>
                    </tr>
                    <tr class="bg-white hover:bg-[#f1f5f9] cursor-pointer">
                        <td class="p-2 border border-[#d9d9d9] text-[#2d326b]">Top DT</td>
                        @for($i = 0; $i < 12; $i++)
                            <td class="p-2 border border-[#d9d9d9] text-center"></td>
                        @endfor
                        <td class="p-2 border border-[#d9d9d9] text-center"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- EPL & OPL Breakdown Chart + Table (Side-by-Side) -->
<div class="w-full mb-8 cursor-pointer bg-white rounded-sm border border-gray-200 p-6 shadow-md hover:shadow-xl hover:border-[#E5E7EB]">
    <h3 class="text-md text-[#2d326b] font-semibold mb-4">OPL & EPL Downtime in Minutes PTD</h3>

    <div class="flex flex-col lg:flex-row gap-6 justify-between">
        <!-- Chart (Left Side, Center Aligned) -->
        <div class="w-full lg:w-1/2 flex items-center justify-center">
            <canvas id="ptdChart" height="170"></canvas>
        </div>

        <!-- Table (Right Side) -->
        <div class="w-full lg:w-1/2 overflow-x-auto">
            <table class="w-full text-xs text-center border border-collapse border-gray-300">
                <thead>
                    <tr class="bg-[#1e3a8a] text-white">
                        <th colspan="4" class="p-2 border border-gray-300">Minutes</th>
                        <th colspan="3" class="p-2 border border-gray-300">Percent Impact</th>
                    </tr>
                    <tr class="bg-[#1e3a8a] text-white">
                        <th class="p-2 border border-gray-300">Total DT</th>
                        <th class="p-2 border border-gray-300">OPL</th>
                        <th class="p-2 border border-gray-300">EPL</th>
                        <th class="p-2 border border-gray-300"></th>
                        <th class="p-2 border border-gray-300">Total DT</th>
                        <th class="p-2 border border-gray-300">OPL</th>
                        <th class="p-2 border border-gray-300">EPL</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @php
                        $rows = [
                            [6347, 2109, 4238, '17.9%', '5.9%', '12.0%'],
                            [7183, 3236, 3947, '22.4%', '9.5%', '11.6%'],
                            [6257, 2042, 4215, '18.0%', '4.0%', '8.3%'],
                            [6712, 2530, 4182, '19.1%', '7.1%', '9.8%'],
                            [9863, 6051, 3812, '25.5%', '12.3%', '7.3%'],
                            [9088, 4058, 5030, '28.4%', '11.8%', '13.7%'],
                            [7553, 1207, 6346, '20.6%', '4.6%', '16.0%'],
                            [7730, 1685, 6045, '21.1%', '6.0%', '10.6%'],
                            [8226, 2332, 5894, '22.4%', '6.4%', '8.7%'],
                            [7513, 2486, 5027, '19.8%', '6.6%', '10.0%'],
                            [6450, 1382, 5068, '17.5%', '4.3%', '6.8%'],
                            [7939, 3201, 4738, '21.4%', '4.0%', '14.4%'],
                        ];
                    @endphp

                    @foreach($rows as $r)
                        <tr class="hover:bg-[#f1f5f9]">
                            <td class="border p-1">{{ number_format($r[0]) }}</td>
                            <td class="border p-1">{{ number_format($r[1]) }}</td>
                            <td class="border p-1">{{ number_format($r[2]) }}</td>
                            <td class="border p-1"></td>
                            <td class="border p-1">{{ $r[3] }}</td>
                            <td class="border p-1">{{ $r[4] }}</td>
                            <td class="border p-1">{{ $r[5] }}</td>
                        </tr>
                    @endforeach

                    <tr class="bg-[#1e3a8a] text-white font-semibold">
                        <td class="p-1 border">{{ number_format(81520) }}</td>
                        <td class="p-1 border">{{ number_format(31699) }}</td>
                        <td class="p-1 border">{{ number_format(49821) }}</td>
                        <td class="p-1 border"></td>
                        <td class="p-1 border">17.7%</td>
                        <td class="p-1 border">6.9%</td>
                        <td class="p-1 border">10.8%</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- OPL Downtime Breakdown - TOGGLE VERSION -->
<div x-data="{ showOPL: false }" class="w-full mb-4 bg-white rounded-sm border border-gray-200 p-6 shadow-md hover:shadow-xl hover:border-[#E5E7EB]">
    <!-- Toggle Header -->
    <div class="flex items-center justify-between cursor-pointer" @click="showOPL = !showOPL">
        <h3 class="text-lg text-[#2d326b] font-semibold">OPL Downtimes in Minutes</h3>
        <svg :class="showOPL ? 'rotate-180' : ''" class="w-5 h-5 transition-transform text-[#2d326b]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </div>

    <!-- Chart + Table Content -->
    <div x-show="showOPL" x-transition class="mt-6">
        <!-- Chart -->
        <div class="w-full mb-6 flex justify-center">
            <canvas id="oplChart" height="300" class="w-full !pl-0 !ml-0 !translate-x-0"></canvas>
        </div>

        <!-- Table Below Chart -->
        <div class="w-full overflow-x-auto">
            <table class="text-[10px] border border-collapse border-gray-300 w-full text-center">
                <thead class="bg-[#1e3a8a] text-white font-semibold">
                    <tr>
                        @foreach([
                            'Warehouse Full', 'Reload Film or Label', 'Change Over', 'Start Up SOP',
                            'Fine Tuning', 'Pack Mats/ Bottle Jam', 'Line Clearance / Sanitation',
                            'Material Quality', 'Power Interruption', 'CIP', 'QA Testing',
                            'Shutdown SOP', 'Stand Up Meeting', 'Forklift Delay', 'Others'
                        ] as $label)
                            <th class="p-1 border border-gray-300 whitespace-nowrap">{{ $label }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @php
                        $oplTableData = [
                            [298, 187, 201, 60, 121, 298, 86, 3, 44, 900, 36, 15, 0, 0, 210],
                            [78, 343, 379, 106, 168, 268, 6, 10, 100, 1620, 36, 15, 0, 0, 16],
                            [351, 787, 94, 90, 99, 298, 268, 3, 556, 156, 15, '', '', '', 71],
                            [353, 806, 80, 101, 243, 86, '', '', 480, 18, '', '', '', '', 2396],
                            [130, 717, 799, 181, 290, 25, '', '', 10, 10, '', '', '', '', ''],
                            [38, 672, 473, 66, 44, 25, '', '', 240, 95, '', '', '', '', 180],
                            [1060, 1340, 906, 59, 63, 178, '', '', 22, 720, '', '', '', '', ''],
                            [13, 45, 38, 51, 15, '', '', '', 181, 60, '', '', '', '', ''],
                        ];
                        $totals = [1131, 10831, 5400, 779, 2156, 566, 182, 293, 377, 6401, 450, 260, 0, 0, 2873];
                    @endphp

                    @foreach($oplTableData as $row)
                        <tr class="hover:bg-[#f1f5f9]">
                            @foreach($row as $value)
                                <td class="border p-1">{{ is_numeric($value) ? number_format($value) : '' }}</td>
                            @endforeach
                        </tr>
                    @endforeach

                    <tr class="bg-[#1e3a8a] text-white font-bold">
                        @foreach($totals as $val)
                            <td class="p-1 border">{{ number_format($val) }}</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- EPL Downtime Breakdown (Toggle Section) -->
<div x-data="{ showEPL: false }" class="w-full bg-white rounded-sm border border-gray-200 p-6 shadow-md hover:shadow-xl hover:border-[#E5E7EB]">

    <!-- Toggle Button -->
    <div class="flex items-center justify-between cursor-pointer" @click="showEPL = !showEPL">
        <h3 class="text-lg text-[#2d326b] font-semibold">EPL Downtimes in Minutes</h3>
        <svg :class="showEPL ? 'rotate-180' : ''" class="w-5 h-5 transition-transform text-[#2d326b]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </div>

    <!-- Content -->
    <div x-show="showEPL" x-transition class="mt-6">
        <!-- Chart -->
        <div class="w-full mb-6 flex justify-center">
            <canvas id="eplChart" height="300" class="w-full !pl-0 !ml-0 !translate-x-0"></canvas>
        </div>

        <!-- Table -->
        <div class="w-full overflow-x-auto">
            <table class="text-[10px] border border-collapse border-gray-300 w-full text-center">
                <thead class="bg-[#1e3a8a] text-white font-semibold">
                    <tr>
                        @foreach([
                            'Blow Mold', 'Shrink Packer', 'Auxillary', 'OPP Labeller', 'Filler',
                            'Water Treatment', 'Laser Coder', 'Case Conveyor', 'Cap Coder',
                            'Case Coder', 'Palletizer', 'Bottle Conveyor', 'Others'
                        ] as $label)
                            <th class="p-1 border border-gray-300 whitespace-nowrap">{{ $label }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @php
                        $eplTableData = [
                            [1051, 256, 1162, 272, 143, 857, 350, 21, 126, '', '', '', ''],
                            [427, 244, 1174, 183, 302, '', '', '', '', '', '', '', ''],
                            [2197, 1442, 249, 175, 40, '', '', '', '', '', 60, '', ''],
                            [894, 394, 400, 349, 307, '', '', '', '', '', 4, '', ''],
                            [1197, 611, 716, 639, 394, '', '', 20, '', '', 2, '', ''],
                            [1238, 381, 140, 394, 145, '', '', '', '', '', '', '', ''],
                            [2553, 993, 254, 402, 363, 493, '', 28, '', '', '', '', 131],
                            [1060, 1323, 363, 370, 83, 444, 11, 16, '', '', 6, '', ''],
                            [2152, 123, 2281, 222, 41, 360, '', '', '', '', 9, '', ''],
                            [1233, 384, 53, 122, 204, '', 200, '', '', '', 60, '', ''],
                        ];
                        $totals = [16542, 8780, 5798, 5437, 7910, 3517, 488, 115, 146, 28, 18, 896, 146];
                    @endphp

                    @foreach($eplTableData as $row)
                        <tr class="hover:bg-[#f1f5f9]">
                            @foreach($row as $val)
                                <td class="p-1 border">{{ is_numeric($val) ? number_format($val) : '' }}</td>
                            @endforeach
                        </tr>
                    @endforeach

                    <tr class="bg-[#1e3a8a] text-white font-bold">
                        @foreach($totals as $val)
                            <td class="p-1 border">{{ number_format($val) }}</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>







<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Include Data Labels Plugin -->
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script>
    const ctx = document.getElementById('leChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['P1', 'P2', 'P3', 'P4', 'P5', 'P6', 'P7', 'P8', 'P9', 'P10', 'P11', 'P12', '2023', '2024'],
            datasets: [
                {
                    label: 'EPL %',
                    data: [12.0, 11.6, 8.3, 9.8, 7.3, 13.7, 10.6, 10.6, 9.0, 8.6, 6.0, 14.4, 0, 0],
                    backgroundColor: '#22D3EE',
                    stack: 'Stack 0',
                    type: 'bar'
                },
                                {
                    label: 'OPL %',
                    data: [5.9, 9.5, 4.0, 7.1, 12.3, 11.8, 4.6, 6.0, 6.6, 6.6, 4.0, 4.0, 0, 0],
                    backgroundColor: '#6D28D9',
                    stack: 'Stack 0',
                    type: 'bar'
                },
                {
                    label: 'Yearly Trend',
                    data: [null, null, null, null, null, null, null, null, null, null, null, null, 69.1, null],
                    backgroundColor: '#DC2626', // Red
                    stack: null,
                    type: 'bar'
                },
                                {
                    label: 'Current Trend',
                    data: [null, null, null, null, null, null, null, null, null, null, null, null, null, 82.3],
                    backgroundColor: '#16A34A', // Green
                    stack: null,
                    type: 'bar'
                },
                {
                    label: 'Target LE %',
                    data: Array(12).fill(80).concat([null, null]),
                    type: 'line',
                    borderColor: '#D1D5DB', // light gray
                    borderDash: [],
                    backgroundColor: 'transparent',
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#D1D5DB', // round dot
                    pointBorderColor: '#D1D5DB',
                    yAxisID: 'y'
                },
                {
                    label: 'LE %',
                    data: [82.1, 78.9, 87.6, 83.1, 80.4, 74.5, 79.4, 83.3, 84.9, 83.4, 88.9, 81.6, null, null],
                    type: 'line',
                    borderColor: '#2563EB',
                    backgroundColor: '#2563EB',
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#2563EB',
                    yAxisID: 'y'
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false
            },
            stacked: false,
            plugins: {
                legend: {
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            if (context.raw === null) return null;
                            return `${context.dataset.label}: ${context.parsed.y}%`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: value => value + '%'
                    },
                    title: {
                        display: true,
                        text: 'Efficiency (%)',
                        font: {
                            size: 10,
                        }
                    }
                }
            }
        }
    });

const ctxPTD = document.getElementById('ptdChart').getContext('2d');
new Chart(ctxPTD, {
    type: 'bar',
    data: {
        labels: ['OPL, Minutes', 'EPL, Minutes'],
        datasets: [{
            label: 'Downtime (min)',
            data: [31699, 49821],
            backgroundColor: ['#a78bfa', '#67e8f9'],
            borderRadius: 5,
            borderSkipped: false
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => `${ctx.raw.toLocaleString()} minutes`
                }
            },
            datalabels: {
                color: '#111827',
                anchor: 'end',
                align: 'end',
                font: {
                    weight: 'bold',
                    size: 12
                },
                formatter: value => value.toLocaleString()
            }
        },
        scales: {
            x: {
                grid: {
                    display: false,
                    drawBorder: false
                }
            },
            y: {
                beginAtZero: true,
                min: 1000,
                max: 61000,
                ticks: {
                    stepSize: 10000,
                    callback: value => value.toLocaleString()
                },
                grid: {
                    drawBorder: false
                }
            }
        }
    },
    plugins: [ChartDataLabels]
});

const ctxOPL = document.getElementById('oplChart').getContext('2d');
new Chart(ctxOPL, {
    type: 'bar',
    data: {
        labels: [
            'Warehouse Full', 'Reload Film or Label', 'Change Over', 'Start Up SOP',
            'Fine Tuning', 'Pack Mats/ Bottle Jam', 'Line Clearance / Sanitation',
            'Material Quality', 'Power Interruption', 'CIP', 'QA Testing',
            'Shutdown SOP', 'Stand Up Meeting', 'Forklift Delay', 'Others'
        ],
        datasets: [{
            data: [1131, 10831, 5400, 779, 2156, 566, 182, 293, 377, 6401, 450, 260, 0, 0, 2873],
            backgroundColor: '#c4b5fd',
            borderRadius: 4,
            borderSkipped: false
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => `${ctx.raw.toLocaleString()} minutes`
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 2000,
                    callback: value => value.toLocaleString()
                }
            }
        }
    }
});

const ctxEPL = document.getElementById('eplChart').getContext('2d');

new Chart(ctxEPL, {
    type: 'bar',
    data: {
        labels: [
            'Blow Mold', 'Shrink Packer', 'Auxillary', 'OPP Labeller', 'Filler',
            'Water Treatment', 'Laser Coder', 'Case Conveyor', 'Cap Coder',
            'Case Coder', 'Palletizer', 'Bottle Conveyor', 'Others'
        ],
        datasets: [{
            label: 'EPL Downtime (min)',
            data: [16542, 8780, 5798, 5437, 7910, 3517, 488, 115, 146, 28, 18, 896, 146],
            backgroundColor: '#67e8f9',
            borderRadius: 5,
            borderSkipped: false
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => `${ctx.raw.toLocaleString()} minutes`
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                min: 0,
                max: 18000,
                ticks: {
                    stepSize: 2000,
                    callback: value => value.toLocaleString()
                }
            }
        }
    }
});

</script>



@endsection
