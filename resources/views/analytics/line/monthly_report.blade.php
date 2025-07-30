@extends('layouts.app')

@section('content')

<!-- Back Button -->
<a href="{{ route('analytics.line.index', ['line' => request('line'), 'date' => request('date', now()->year)]) }}" class="flex items-center text-sm text-gray-500 hover:text-[#2d326b] mb-4">
    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
    </svg>
    Analytics and Report
</a>

<!-- Main Title -->
<h2 class="text-xl mt-6 mb-6 font-semibold text-[#2d326b] tracking-wider mb-4 text-center">
    PRODUCTION DOWNTIME ANALYSIS
</h2>

<!-- Header Section: Line Info & Export -->
<div class="flex flex-col md:flex-row md:items-center justify-between mb-4">
    <div class="mb-4 md:mb-0">
        <h2 class="text-lg font-semibold text-[#2d326b] tracking-wider">
            Line {{ $line ? 'Line ' . $line : 'Line Not Selected' }} Downtime Analysis
        </h2>
        <p class="text-sm text-[#2d326b] mt-1">
            Covering period: {{ $month }} {{ $year }}
        </p>
    </div>
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

<!-- Navigation Tabs -->
<div x-data="{ activeTab: 'overview' }" class="w-full mb-6">
    <!-- Tab Navigation -->
    <div class="flex flex-wrap bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
        <!-- Overview Tab -->
        <button @click="activeTab = 'overview'" 
                :class="activeTab === 'overview' ? 'bg-[#3B82F6] text-white' : 'bg-gray-50 text-gray-700 hover:bg-gray-100'"
                class="flex items-center px-4 py-3 text-sm font-medium transition-colors duration-200 border-r border-gray-200 min-w-0 flex-1 md:flex-initial">
            <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <span class="truncate">Overview</span>
        </button>
        
        <!-- OPL Analysis Tab -->
        <button @click="activeTab = 'opl'" 
                :class="activeTab === 'opl' ? 'bg-[#F97316] text-white' : 'bg-gray-50 text-gray-700 hover:bg-gray-100'"
                class="flex items-center px-4 py-3 text-sm font-medium transition-colors duration-200 border-r border-gray-200 min-w-0 flex-1 md:flex-initial">
            <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <span class="truncate">OPL Analysis</span>
        </button>
        
        <!-- EPL Analysis Tab -->
        <button @click="activeTab = 'epl'" 
                :class="activeTab === 'epl' ? 'bg-[#6B7280] text-white' : 'bg-gray-50 text-gray-700 hover:bg-gray-100'"
                class="flex items-center px-4 py-3 text-sm font-medium transition-colors duration-200 border-r border-gray-200 min-w-0 flex-1 md:flex-initial">
            <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span class="truncate">EPL Analysis</span>
        </button>
        
        <!-- Comparison Tab -->
        <button @click="activeTab = 'comparison'" 
                :class="activeTab === 'comparison' ? 'bg-[#8B5CF6] text-white' : 'bg-gray-50 text-gray-700 hover:bg-gray-100'"
                class="flex items-center px-4 py-3 text-sm font-medium transition-colors duration-200 min-w-0 flex-1 md:flex-initial">
            <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <span class="truncate">Comparison</span>
        </button>
    </div>
    
    <!-- Tab Content -->
    <div class="mt-6">
        <!-- Overview Content -->
        <div x-show="activeTab === 'overview'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
            <!-- Your existing content goes here for Overview -->
            <div class="text-gray-600">
                <!-- This is where your current Line Efficiency Chart & Table and other overview content would go -->
            </div>
        </div>
        
        <!-- OPL Analysis Content -->
        <div x-show="activeTab === 'opl'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
            <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-md">
                <h3 class="text-lg font-semibold text-[#F97316] mb-4">OPL Analysis Content</h3>
                <p class="text-gray-600">OPL (Operational Process Loss) analysis content will be displayed here.</p>
            </div>
        </div>
        
        <!-- EPL Analysis Content -->
        <div x-show="activeTab === 'epl'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
            <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-md">
                <h3 class="text-lg font-semibold text-[#6B7280] mb-4">EPL Analysis Content</h3>
                <p class="text-gray-600">EPL (Equipment Process Loss) analysis content will be displayed here.</p>
            </div>
        </div>
        
        <!-- Comparison Content -->
        <div x-show="activeTab === 'comparison'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
            <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-md">
                <h3 class="text-lg font-semibold text-[#8B5CF6] mb-4">Comparison Analysis</h3>
                <p class="text-gray-600">Comparison analysis content will be displayed here.</p>
            </div>
        </div>
    </div>
</div>

<!-- Line Efficiency Chart & Table -->
<div class="w-full bg-white rounded-sm border border-gray-200 p-6 mb-8 shadow-md space-y-5 transition-all duration-300 hover:shadow-xl hover:border-[#E5E7EB] flex flex-col">
    <h3 class="text-lg font-semibold mb-4 text-[#2d326b]">Line Efficiency</h3>
    <canvas id="leChart" height="300" class="w-full !pl-0 !ml-0 !translate-x-0"></canvas>
    <!-- Inline Table -->
    <div class="overflow-x-auto mt-4">
        <table class="min-w-full text-[10px] border border-gray-300 text-gray-700 table-auto">
            <thead class="bg-[#f1f5f9] font-semibold text-gray-800 text-center">
                <tr>
                    <th class="border border-gray-300 px-2 py-1 text-left w-[130px]"></th>
                    @foreach([ 'P1', 'P2', 'P3', 'P4', 'P5', 'P6', 'P7', 'P8', 'P9', 'P10', 'P11', 'P12',
  'P13', 'P14', 'P15', 'P16', 'P17', 'P18', 'P19', 'P20', 'P21', 'P22', 'P23', 'P24',
  'P25', 'P26', 'P27', 'P28', 'P29', 'P30', 'P31',
  '2023', '2024'] as $report)
                        <th class="border border-gray-300 px-2 py-1 text-right">{{ $report }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="text-[9px]">

<tr>
    <td class="border border-gray-300 px-2 py-1 font-semibold text-left text-[#22D3EE]">EPL, %</td>
    @foreach($reports as $report)
        <td class="border border-gray-300 px-2 py-1 text-right">
{{ isset($eplPercent) ? round($eplPercent) . '%' : '0.0%' }}        </td>
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
                    @foreach([null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,69.1,82.3] as $val)
                        <td class="border border-gray-300 px-2 py-1 text-right">{{ $val !== null ? number_format($val, 1).'%' : '' }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td class="border border-gray-300 px-2 py-1 font-semibold text-left text-[#D1D5DB]">Target LE, %</td>
                    @foreach(array_merge(array_fill(0, 31, 80), [null, null]) as $index => $val)
                        @php $isYear = $index === 12 || $index === 31; @endphp
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

<!-- EPL & OPL Breakdown Chart + Table -->
<div class="w-full mb-8 cursor-pointer bg-white rounded-sm border border-gray-200 p-6 shadow-md hover:shadow-xl hover:border-[#E5E7EB]">
    <h3 class="text-md text-[#2d326b] font-semibold mb-4">OPL & EPL Downtime in Minutes PTD</h3>
    <div class="flex flex-col gap-6 justify-between">
        <!-- Chart (Left Side) -->
        <div class="w-full flex items-center justify-center">
            <canvas id="ptdChart" height="65"></canvas>
        </div>

    </div>
</div>

<!-- Production Daily Report (Toggle) -->
<div x-data="{ showDaily: true }" class="w-full mb-4 bg-white rounded-sm border border-gray-200 p-6 shadow-md hover:shadow-xl hover:border-[#E5E7EB]">
    <!-- Toggle Header -->
    <div class="flex items-center justify-between cursor-pointer" @click="showDaily = !showDaily">
        <h3 class="text-lg text-[#2d326b] font-semibold">Production Report</h3>
        <svg :class="showDaily ? 'rotate-180' : ''" class="w-5 h-5 transition-transform text-[#2d326b]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </div>
    <!-- Toggle Content -->
    <div x-show="showDaily" x-transition class="overflow-x-auto mt-4">
        <div class="min-w-[1800px]">
            <div class="flex flex-col gap-6">
                <!-- Daily Tables (Left and Right) -->
                <div class="flex gap-4">
                    <!-- Daily Left Table -->
                    <div class="w-full">
                        <table class="w-full table-fixed text-xs text-center border border-collapse border-gray-300 whitespace-nowrap">
                            <thead class="bg-[#1e3a8a] text-white">
                                <tr>
                                    <th colspan="7" class="p-4 border border-gray-300"></th>
                                </tr>
                                <tr>
                                    <th class="p-2 border border-gray-300">Production Date</th>
                                    <th class="p-2 border border-gray-300 w-[14.28%]">S K U</th>
                                    <th class="p-2 border border-gray-300 w-[14.28%]">Size</th>
                                    <th class="p-2 border border-gray-300 w-[14.28%]">Target LE, %</th>
                                    <th class="p-2 border border-gray-300 w-[14.28%]">LE, %</th>
                                    <th class="p-2 border border-gray-300 w-[14.28%]">OPL, %</th>
                                    <th class="p-2 border border-gray-300 w-[14.28%]">EPL, %</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                @foreach($dailyRows as $row)
                                <tr class="hover:bg-[#f1f5f9]">
                                    <td class="border p-1">{{ $row['date'] }}</td>
                                    <td class="border p-1">{{ $row['sku'] }}</td>
                                    <td class="border p-1">{{ $row['size'] }}</td>
                                    <td class="border p-1">{{ $row['target_le'] }}</td>
                                    <td class="border p-1">{{ $row['le'] }}</td>
                                    <td class="border p-1">{{ $row['opl_percent'] }}</td>
                                    <td class="border p-1">{{ $row['epl_percent'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- Daily Right Table -->
                    <div class="w-1/2">
                        <table class="w-full table-fixed text-xs text-center border border-collapse border-gray-300 whitespace-nowrap">
                            <thead class="bg-[#1e3a8a] text-white">
                                <tr>
                                    <th colspan="3" class="p-2 border border-gray-300">Minutes</th>
                                    <th colspan="3" class="p-2 border border-gray-300">Percent Impact</th>
                                </tr>
                                <tr>
                                    <th class="p-2 border border-gray-300 w-[16.66%]">Total DT</th>
                                    <th class="p-2 border border-gray-300 w-[16.66%]">OPL</th>
                                    <th class="p-2 border border-gray-300 w-[16.66%]">EPL</th>
                                    <th class="p-2 border border-gray-300 w-[16.66%]">Total DT</th>
                                    <th class="p-2 border border-gray-300 w-[16.66%]">OPL</th>
                                    <th class="p-2 border border-gray-300 w-[16.66%]">EPL</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                @foreach($dailyRows as $row)
                                <tr class="hover:bg-[#f1f5f9]">
                                    <td class="border p-1">{{ $row['total_mins'] }}</td>
                                    <td class="border p-1">{{ $row['opl_mins'] }}</td>
                                    <td class="border p-1">{{ $row['epl_mins'] }}</td>
                                    <td class="border p-1">{{ $row['dt'] }}</td>
                                    <td class="border p-1">{{ $row['opl_percent'] }}</td>
                                    <td class="border p-1">{{ $row['epl_percent'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Weekly Tables (Left and Right) -->
                <div class="flex gap-4">
                    <!-- Weekly Left Table -->
                    <div class="w-full">
                        <table class="w-full table-fixed text-xs text-center border border-collapse border-gray-300 whitespace-nowrap">
                            <tbody class="text-gray-700">
                                @foreach($finalRows as $weekRow)
                                <tr class="hover:bg-[#f1f5f9]">
                                    <td class="border p-1">{{ $weekRow[0] }}</td>
                                    <td class="border p-1"></td>
                                    <td class="border p-1"></td>
                                    <td class="border p-1"></td>
                                    <td class="border p-1">{{ $weekRow[1] }}</td>
                                    <td class="border p-1">{{ $weekRow[2] }}</td>
                                    <td class="border p-1">{{ $weekRow[3] }}</td>
                                </tr>
                                @endforeach
                                <tr class="hover:bg-[#fef3c7] font-bold text-[#1e3a8a]">
                                    <td class="p-1 border text-center">PTD</td>
                                    <td class="p-1 border text-center"></td>
                                    <td class="p-1 border text-center"></td>
                                    <td class="p-1 border text-center"></td>
                                    <td class="p-1 border text-center">{{ $ptdLE }}</td>
                                    <td class="p-1 border text-center">{{ $ptdOPL }}</td>
                                    <td class="p-1 border text-center">{{ $ptdEPL }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Weekly Right Table -->
                    <div class="w-1/2">
                        <table class="w-full table-fixed text-xs text-center border border-collapse border-gray-300 whitespace-nowrap">
                            <tbody class="text-gray-700">
                                @foreach($weeklyRows as $row)
                                <tr class="hover:bg-[#f1f5f9]">
                                    <td class="border p-1">{{ $row['dt'] }}</td>
                                    <td class="border p-1">{{ $row['opl'] }}</td>
                                    <td class="border p-1">{{ $row['epl'] }}</td>
                                    <td class="border p-1">{{ $row['dt_percent'] }}</td>
                                    <td class="border p-1">{{ $row['opl_percent'] }}</td>
                                    <td class="border p-1">{{ $row['epl_percent'] }}</td>
                                </tr>
                                @endforeach
                                <tr class="hover:bg-[#fef3c7] font-bold text-[#1e3a8a]">
                                    <td class="p-1 border text-center">{{ $totalOpl + $totalEpl }}</td>
                                    <td class="p-1 border text-center">{{ $totalOpl }}</td>
                                    <td class="p-1 border text-center">{{ $totalEpl }}</td>
                                    <td class="p-1 border text-center">{{ round($totalDt / max(count($reports), 1), 2) }}%</td>
                                    <td class="p-1 border text-center">{{ $ptdOPL }}</td>
                                    <td class="p-1 border text-center">{{ $ptdEPL }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- End Weekly Tables -->
            </div>
        </div>
    </div>
</div>
<!-- End Production Daily Report (Toggle) -->


<!-- OPL Downtime Breakdown (Toggle) -->
<div x-data="{ showOPL: false }" class="w-full mb-4 bg-white rounded-sm border border-gray-200 p-6 shadow-md hover:shadow-xl hover:border-[#E5E7EB]">
    <div class="flex items-center justify-between cursor-pointer" @click="showOPL = !showOPL">
        <h3 class="text-lg text-[#2d326b] font-semibold">OPL Downtimes in Minutes</h3>
        <svg :class="showOPL ? 'rotate-180' : ''" class="w-5 h-5 transition-transform text-[#2d326b]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </div>
    <div x-show="showOPL" x-transition class="mt-6">
        <!-- Chart -->
        <div class="w-full mb-6 flex justify-center">
            <canvas id="oplChart" height="300" class="w-full !pl-0 !ml-0 !translate-x-0"></canvas>
        </div>
        <!-- Table -->
        <div class="w-full overflow-x-auto mt-6">
            <table class="min-w-[1500px] text-[10px] border border-collapse border-gray-300 text-center">
                <thead class="bg-[#1e3a8a] text-white font-semibold">
                    <tr>
                        <th class="p-2 border border-gray-300 whitespace-nowrap text-left bg-[#1e3a8a]">Date</th>
                        @foreach($oplCategories as $label)
                            <th class="p-2 border border-gray-300 whitespace-nowrap min-w-[120px]">{{ $label }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @foreach($oplData as $row)
                        <tr class="hover:bg-[#f1f5f9] whitespace-nowrap">
                            <td class="border p-2 text-left min-w-[100px] font-semibold text-[#1e3a8a]">{{ $row['date'] }}</td>
                            @foreach($oplCategories as $cat)
                                <td class="border p-2 text-center min-w-[120px]">
                                    {{ number_format($row['categories'][$cat] ?? 0) }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <script>
    const oplLabels = @json($oplCategories);

    const oplTotals = @json(
        collect($oplCategories)->map(function($cat) use ($oplData) {
            return collect($oplData)->sum(fn($row) => $row['categories'][$cat] ?? 0);
        })
    );
</script>

        </div>
    </div>
</div>

<!-- EPL Downtime Breakdown (Toggle) -->
<div x-data="{ showEPL: false }" class="w-full bg-white rounded-sm border border-gray-200 p-6 shadow-md hover:shadow-xl hover:border-[#E5E7EB]">
    <div class="flex items-center justify-between cursor-pointer" @click="showEPL = !showEPL">
        <h3 class="text-lg text-[#2d326b] font-semibold">EPL Downtimes in Minutes</h3>
        <svg :class="showEPL ? 'rotate-180' : ''" class="w-5 h-5 transition-transform text-[#2d326b]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </div>
    <div x-show="showEPL" x-transition class="mt-6">
        <!-- Chart -->
        <div class="w-full mb-6 flex justify-center">
            <canvas id="eplChart" height="300" class="w-full !pl-0 !ml-0 !translate-x-0"></canvas>
        </div>
        <!-- Table -->
        <div class="w-full overflow-x-auto">
            <table class="min-w-[1500px] text-[10px] border border-collapse border-gray-300 text-center">
                <thead class="bg-[#1e3a8a] text-white font-semibold">
                    <tr>
                        <th class="p-2 border border-gray-300 whitespace-nowrap text-left bg-[#1e3a8a]">Date</th>
                        @foreach($eplCategories as $label)
                            <th class="p-2 border border-gray-300 whitespace-nowrap min-w-[120px]">{{ $label }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @foreach($eplData as $row)
                        <tr class="hover:bg-[#f1f5f9] whitespace-nowrap">
                            <td class="border p-2 text-left min-w-[100px] font-semibold text-[#1e3a8a]">{{ $row['date'] }}</td>
                            @foreach($eplCategories as $cat)
                                <td class="border p-2 text-center min-w-[120px]">
                                    {{ number_format($row['categories'][$cat] ?? 0) }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <script>
                // Prepare EPL chart data
                const eplLabels = @json($eplCategories);
                const eplTotals = @json(
                    collect($eplCategories)->map(function($cat) use ($eplData) {
                        return collect($eplData)->sum(fn($row) => $row['categories'][$cat]);
                    })
                );
            </script>
        </div>
    </div>
</div>

<!-- Chart.js & Chart Data Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script>
    // Line Efficiency Chart
    const ctx = document.getElementById('leChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [
                    'P1', 'P2', 'P3', 'P4', 'P5', 'P6', 'P7', 'P8', 'P9', 'P10', 'P11', 'P12',
                    'P13', 'P14', 'P15', 'P16', 'P17', 'P18', 'P19', 'P20', 'P21', 'P22', 'P23', 'P24',
                    'P25', 'P26', 'P27', 'P28', 'P29', 'P30', 'P31',
                    '2023', '2024'
                    ],
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
                    backgroundColor: '#DC2626',
                    stack: null,
                    type: 'bar'
                },
                {
                    label: 'Current Trend',
                    data: [null, null, null, null, null, null, null, null, null, null, null, null, null, 82.3],
                    backgroundColor: '#16A34A',
                    stack: null,
                    type: 'bar'
                },
                {
                    label: 'Target LE %',
                    data: Array(12).fill(80).concat([null, null]),
                    type: 'line',
                    borderColor: '#D1D5DB',
                    borderDash: [],
                    backgroundColor: 'transparent',
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#D1D5DB',
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
                legend: { position: 'top' },
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
                        font: { size: 10 }
                    }
                }
            }
        }
    });

    // PTD Chart
        const totalOpl = {{ $totalOpl }};
    const totalEpl = {{ $totalEpl }};

    const ctxPTD = document.getElementById('ptdChart').getContext('2d');
    new Chart(ctxPTD, {
        type: 'bar',
        data: {
            labels: ['OPL, Minutes', 'EPL, Minutes'],
            datasets: [{
                label: 'Downtime (min)',
                data: [totalOpl, totalEpl],
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
                    font: { weight: 'bold', size: 12 },
                    formatter: value => value.toLocaleString()
                }
            },
            scales: {
                x: {
                    grid: { display: false, drawBorder: false }
                },
                y: {
                    beginAtZero: true,
                    min: 0,
                    max: 4500,
                    ticks: {
                        stepSize: 500,
                        callback: value => value.toLocaleString()
                    },
                    grid: { drawBorder: false }
                }
            }
        },
        plugins: [ChartDataLabels]
    });

    // OPL Chart
const ctxOPL = document.getElementById('oplChart').getContext('2d');
new Chart(ctxOPL, {
    type: 'bar',
    data: {
        labels: oplLabels,
        datasets: [{
            label: 'OPL Downtime (min)',
            data: oplTotals,
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
            x: {
                ticks: {
                    callback: function(value) {
                        // Wrap labels if they are too long
                        const label = this.getLabelForValue(value);
                        const maxCharsPerLine = 10;
                        if (label.length > maxCharsPerLine) {
                            const words = label.split(' ');
                            let line = '';
                            const lines = [];

                            for (const word of words) {
                                if ((line + word).length <= maxCharsPerLine) {
                                    line += word + ' ';
                                } else {
                                    lines.push(line.trim());
                                    line = word + ' ';
                                }
                            }
                            if (line) lines.push(line.trim());
                            return lines;
                        }
                        return label;
                    },
                    maxRotation: 0,
                    minRotation: 0,
                    autoSkip: false,
                    padding: 10
                }
            },
            y: {
                beginAtZero: true,
                min: 0,
                ticks: {
                    stepSize: 2000,
                    callback: value => value.toLocaleString()
                }
            }
        }
    }
});



    // EPL Chart
    const ctxEPL = document.getElementById('eplChart').getContext('2d');
    new Chart(ctxEPL, {
        type: 'bar',
        data: {
            labels: eplLabels,
            datasets: [{
                label: 'EPL Downtime (min)',
                data: eplTotals,
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
                    suggestedMax: Math.max(...eplTotals) + 2000,
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
