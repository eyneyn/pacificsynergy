@extends('layouts.app')

@section('content')

{{-- Page Title --}}
<h2 class="text-xl mb-2 font-bold text-[#23527c]">Line Efficiency Report</h2>


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

    <form action="{{ route('analytics.export_excel_line_summary') }}" method="GET" class="inline-block">
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
    <div class="w-full xl:w-3/4 bg-white rounded-sm border border-gray-200 p-6 shadow-md space-y-5 transition-all duration-300 hover:shadow-xl hover:border-[#E5E7EB] flex flex-col">
    <h3 class="text-lg font-semibold mb-4 text-[#23527c]">Line Efficiency</h3>
    <canvas id="leChart" height="300" class="w-full !pl-0 !ml-0 !translate-x-0"></canvas>
        <div class="overflow-x-auto mt-4">
        <table class="min-w-full text-[10px] border border-gray-300 text-gray-700 table-auto">
        <thead class="bg-[#f1f5f9]  text-center">
            <tr>
                <th class="border border-gray-300 px-2 py-1 text-left w-[200px] whitespace-nowrap">
                    Indicator
                </th>            @foreach ($grouped as $month => $data)
                <th class="border border-gray-300 px-2 py-1 text-right whitespace-nowrap">
                    P{{ $month }}
                </th>
            @endforeach
        </tr>
    </thead>
    <tbody class="text-[9px]">
        <!-- OPL -->
        <tr>
                <td class="border border-gray-300 px-2 py-1  text-left whitespace-nowrap">
                  <span class="inline-flex items-center mr-2">
                      <span class="inline-block w-12 h-2 bg-[#8064A2]"></span>
                  </span>  
                  OPL, %
                </td>
            @foreach ($grouped as $month => $data)
                <td class="border border-gray-300 px-2 py-1 text-right">
                    {{ $data['plantTotal']['opl'] ?? 0 }}%
                </td>
            @endforeach
        </tr>
        <!-- EPL -->
        <tr>
                <td class="border border-gray-300 px-2 py-1  text-left whitespace-nowrap">
                  <span class="inline-flex items-center mr-2">
                      <span class="inline-block w-12 h-2 bg-[#4BACC6]"></span>
                  </span> 
                  EPL, %
                </td>
            @foreach ($grouped as $month => $data)
                <td class="border border-gray-300 px-2 py-1 text-right">
                    {{ $data['plantTotal']['epl'] ?? 0 }}%
                </td>
            @endforeach
        </tr>
        <!-- Target LE -->
        <tr>
                <td class="border border-gray-300 px-2 py-1  text-left whitespace-nowrap">
                    <span class="inline-flex items-center mr-2">
                        <span class="h-[2px] w-4 bg-[#D9D9D9]"></span>
                        <span class="inline-block w-2 h-2 rounded-full bg-[#98B954] mx-1"></span>
                        <span class="h-[2px] w-4 bg-[#D9D9D9]"></span>
                    </span>
                    Target LE, %</td>
            @foreach ($grouped as $month => $data)
                <td class="border border-gray-300 px-2 py-1 text-right">
                    {{ $data['targetLE'] }}%
                </td>
            @endforeach
        </tr>

        <!-- LE -->
        <tr>
                <td class="border border-gray-300 px-2 py-1  text-left whitespace-nowrap">
                    <span class="inline-flex items-center mr-2">
                        <span class="h-[2px] w-4 bg-[#4F81BD]"></span>
                        <span class="inline-block w-2 h-2 rounded-full bg-[#7D60A0] mx-1"></span>
                        <span class="h-[2px] w-4 bg-[#4F81BD]"></span>
                    </span>
                    LE, %
                </td>
            @foreach ($grouped as $month => $data)
                <td class="border border-gray-300 px-2 py-1 text-right">
                    {{ $data['plantTotal']['le'] ?? 0 }}%
                </td>
            @endforeach
        </tr>

    </tbody>
</table>

    </div>
    </div>

<!-- Right-side Cards -->
<div class="w-full xl:w-1/4 flex flex-col gap-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        @php
            $latestMonth = $grouped->keys()->max(); 
        @endphp

        {{-- Line cards --}}
        @foreach ($activeLines as $line)
            <div class="bg-white hover:bg-[#e5f4ff] rounded-xl shadow border border-gray-200 p-3 flex items-center justify-center">
                <div class="text-center">
                    <h3 class="text-sm text-[#23527c] mb-1">Line {{ $line->line_number }}</h3>
                    <p class="text-lg font-semibold text-[#23527c]">
                        {{ $yearSummary['lines'][$line->line_number]['le'] ?? 0 }}%
                    </p>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Line buttons --}}
    <div class="bg-white rounded-xl shadow border border-gray-200 p-4 mt-2">
        <h3 class="text-sm text-[#23527c] text-center mb-3 font-semibold">
            Select Production Line
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
            @foreach ($lines as $lineOption)
                <a href="{{ route('analytics.material.index', ['line' => $lineOption, 'date' => $year]) }}"
                   class="text-xs text-[#23527c] text-center border border-gray-300 rounded-md py-1 px-2 hover:bg-[#23527c] hover:text-white transition duration-150">
                    Line {{ $lineOption }}
                </a>
            @endforeach
        </div>
    </div>
</div>

</div>



<!-- Volume Reference Report Section -->
<div x-data="{ showVolume: true }" class="w-full mb-4">
    <!-- Toggle Button -->
    <div class="flex items-center justify-between cursor-pointer bg-white rounded-sm border border-gray-200 p-4 shadow-md hover:shadow-xl hover:border-[#E5E7EB]" @click="showVolume = !showVolume">
        <p class="text-lg text-[#23527c] font-semibold">Volume Reference</p>
        <svg :class="showVolume ? 'rotate-180' : ''" class="w-5 h-5 transition-transform text-[#23527c]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </div>

    <!-- Content to show/hide -->
    <div x-show="showVolume" x-transition>
        <div class="bg-white rounded-sm border border-gray-200 p-6 shadow-md mt-2 overflow-x-auto">

<!-- Volume Reference Table -->
<table class="w-full text-xs text-left border border-[#fffffb] border-collapse">
    <colgroup>
        <col class="w-16"> <!-- Production Date -->
        <col class="w-28"> <!-- Total Vol -->

        {{-- Volumes per line --}}
        @foreach($activeLines as $line)
            <col class="w-28">
        @endforeach

        {{-- Contributions per line --}}
        @foreach($activeLines as $line)
            <col class="w-28">
        @endforeach

        <col class="w-20"> <!-- Target LE -->

        {{-- Per-line LE, OPL, EPL --}}
        @foreach($activeLines as $line)
            <col class="w-20"> <!-- LE -->
            <col class="w-20"> <!-- OPL -->
            <col class="w-20"> <!-- EPL -->
        @endforeach

        {{-- Plant totals --}}
        <col class="w-20"> <!-- Plant LE -->
        <col class="w-20"> <!-- Plant OPL -->
        <col class="w-20"> <!-- Plant EPL -->
    </colgroup>

    <thead class="text-[10px] text-white uppercase bg-[#0070C0] whitespace-nowrap">
        {{-- Top Header --}}
        <tr class="bg-[#FFFF00] text-black font-bold text-center">
            <th colspan="{{ 2 + ($activeLines->count() * 2) + 1 }}" class="p-2 border border-[#000]">
                VOLUME REFERENCE
            </th>

            {{-- Per-line efficiency block --}}
            @foreach($activeLines as $line)
                <th colspan="3" class="p-2 border border-[#000]">
                    Line {{ $line->line_number }}
                </th>
            @endforeach

            {{-- Plant total block --}}
            <th colspan="3" class="p-2 border border-[#000]">PLANT TOTAL</th>
        </tr>

        {{-- Second row (sub-headers) --}}
        <tr class="text-white">
            <th class="p-2 border border-[#fffffb] text-center whitespace-nowrap">Production Date</th>
            <th class="p-2 border border-[#fffffb] text-center whitespace-nowrap">Total Vol., in cases</th>

            {{-- Volumes --}}
            @foreach($activeLines as $line)
                <th class="p-2 border border-[#fffffb] text-center whitespace-nowrap">Vol. Line {{ $line->line_number }}, cases</th>
            @endforeach

            {{-- Contributions --}}
            @foreach($activeLines as $line)
                <th class="p-2 border border-[#fffffb] text-center whitespace-nowrap">% Vol. Contribution Line {{ $line->line_number }}</th>
            @endforeach

            <th class="p-2 border border-[#fffffb] text-center whitespace-nowrap">Target LE, %</th>

            {{-- Line sub-headers --}}
            @foreach($activeLines as $line)
                <th class="p-2 border border-[#fffffb] text-center whitespace-nowrap">LE, %</th>
                <th class="p-2 border border-[#fffffb] text-center whitespace-nowrap">OPL, %</th>
                <th class="p-2 border border-[#fffffb] text-center whitespace-nowrap">EPL, %</th>
            @endforeach

            {{-- Plant total sub-headers --}}
            <th class="p-2 border border-[#fffffb] text-center whitespace-nowrap">LE, %</th>
            <th class="p-2 border border-[#fffffb] text-center whitespace-nowrap">OPL, %</th>
            <th class="p-2 border border-[#fffffb] text-center whitespace-nowrap">EPL, %</th>
        </tr>
    </thead>

    <tbody>
        {{-- Monthly Rows --}}
        @foreach($grouped as $month => $data)
            <tr class="text-center">
                <td class="p-2 border border-[#fffffb] bg-[#F2F2F2] text-center">P{{ $month }}</td>
                <td class="p-2 border border-[#fffffb] bg-[#F2F2F2] text-center">{{ number_format($data['totalVolume']) }}</td>

                {{-- Volumes --}}
                @foreach($activeLines as $line)
                    <td class="p-2 border border-[#fffffb] bg-[#DBE5F1] text-center">{{ number_format($data['lines'][$line->line_number]['volume'] ?? 0) }}</td>
                @endforeach

                {{-- Contributions --}}
                @foreach($activeLines as $line)
                    <td class="p-2 border border-[#fffffb] bg-[#F2F2F2] text-center">{{ $data['lines'][$line->line_number]['contribution'] ?? 0 }}%</td>
                @endforeach

                <td class="p-2 border border-[#fffffb] bg-[#F2F2F2] text-center">{{ $data['targetLE'] }}%</td>

                {{-- Per-line LE, OPL, EPL --}}
                @foreach($activeLines as $line)
                    <td class="p-2 border border-[#fffffb] bg-[#F2F2F2] text-center">{{ $data['lines'][$line->line_number]['le'] ?? '0' }}%</td>
                    <td class="p-2 border border-[#fffffb] bg-[#F2F2F2] text-center">{{ $data['lines'][$line->line_number]['opl'] ?? '0' }}%</td>
                    <td class="p-2 border border-[#fffffb] bg-[#F2F2F2] text-center">{{ $data['lines'][$line->line_number]['epl'] ?? '0' }}%</td>
                @endforeach

                {{-- Plant totals --}}
                <td class="p-2 border border-[#fffffb] bg-[#F2F2F2] text-center">{{ $data['plantTotal']['le'] ?? '0' }}%</td>
                <td class="p-2 border border-[#fffffb] bg-[#F2F2F2] text-center">{{ $data['plantTotal']['opl'] ?? '0' }}%</td>
                <td class="p-2 border border-[#fffffb] bg-[#F2F2F2] text-center">{{ $data['plantTotal']['epl'] ?? '0' }}%</td>
            </tr>
        @endforeach

        {{-- PTD Summary --}}
        <tr class="font-bold">
            <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">Year {{ $year }} PTD</td>
            <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">{{ number_format($yearSummary['totalVolume']) }}</td>

            {{-- Line totals --}}
            @foreach($activeLines as $line)
                <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">{{ number_format($yearSummary['lines'][$line->line_number]['volume'] ?? 0) }}</td>
            @endforeach

            {{-- Contributions --}}
            @foreach($activeLines as $line)
                <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">{{ $yearSummary['lines'][$line->line_number]['contribution'] ?? 0 }}%</td>
            @endforeach

            <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">80%</td>

            {{-- Line performance --}}
            @foreach($activeLines as $line)
                <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">{{ $yearSummary['lines'][$line->line_number]['le'] ?? 0 }}%</td>
                <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">{{ $yearSummary['lines'][$line->line_number]['opl'] ?? 0 }}%</td>
                <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">{{ $yearSummary['lines'][$line->line_number]['epl'] ?? 0 }}%</td>
            @endforeach

            {{-- Plant total --}}
            <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">{{ $yearSummary['plantTotal']['le'] ?? 0 }}%</td>
            <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">{{ $yearSummary['plantTotal']['opl'] ?? 0 }}%</td>
            <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">{{ $yearSummary['plantTotal']['epl'] ?? 0 }}%</td>
        </tr>
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


<script>
  const grouped = {!! json_encode($grouped->toArray()) !!};

  // Labels → P1, P2, … P12
  const labels = Object.keys(grouped).map(m => `P${m}`);

  // Dataset values
  const dataTargetLE = Object.values(grouped).map(r => (r.targetLE ?? 80) / 100); // default 80% if missing
  const dataLE       = Object.values(grouped).map(r => (r.plantTotal?.le   ?? 0) / 100);
  const dataEPL      = Object.values(grouped).map(r => (r.plantTotal?.epl  ?? 0) / 100);
  const dataOPL      = Object.values(grouped).map(r => (r.plantTotal?.opl  ?? 0) / 100);
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script>
(function () {
  const el = document.getElementById('leChart');
  if (!el) return;

  const ctx = el.getContext('2d');

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [
        {
          label: 'LE %',
          data: dataLE,
          type: 'line',
          borderColor: '#4F81BD',
          backgroundColor: '#4F81BD',
          tension: 0.4,
          pointRadius: 4,
          pointBackgroundColor: '#7D60A0', // ✅ marker same style as annual
          pointBorderColor: '#7D60A0',
          yAxisID: 'yLine',
          order: 4,
          datalabels: {
            color: '#2563EB',
            align: 'top',
            anchor: 'end',
            formatter: val => (val * 100).toFixed(2) + '%',
            font: { weight: 'bold', size: 10 }
          }
        },
        {
          label: 'EPL %',
          data: dataEPL,
          backgroundColor: '#4BACC6', // ✅ same color as annual
          type: 'bar',
          stack: 'stack0',
          yAxisID: 'y',
          order: 2
        },
        {
          label: 'OPL %',
          data: dataOPL,
          backgroundColor: '#8064A2', // ✅ same color as annual
          type: 'bar',
          stack: 'stack0',
          yAxisID: 'y',
          order: 1
        },
        {
          label: 'Target LE %',
          data: dataTargetLE,
          type: 'line',
          borderColor: '#D9D9D9',
          backgroundColor: '#D9D9D9',
          tension: 0.4,
          pointRadius: 4,
          pointBackgroundColor: '#98B954', // ✅ match annual marker
          pointBorderColor: '#98B954',
          yAxisID: 'yLine',
          datalabels: { display: false },
          order: 3
        }
      ]
    },
    options: {
      responsive: true,
      interaction: { mode: 'index', intersect: false },
      plugins: {
        legend: {
          display: false   // ✅ hide legend (same as annual)
        },
        tooltip: {
          callbacks: {
            label: (ctx) =>
              ctx.raw != null ? `${ctx.dataset.label}: ${(ctx.raw * 100).toFixed(2)}%` : null
          }
        },
        datalabels: {
          display: false // globally off except for LE %
        }
      },
      scales: {
        x: {
          stacked: true,
          ticks: { autoSkip: false },
          grid: { display: false },
          barPercentage: 0.6,
          categoryPercentage: 0.6
        },
        y: {
          stacked: true,
          min: 0,
          max: 1.2,
          ticks: {
            stepSize: 0.2,
            callback: v => `${(v * 100).toFixed(0)}%`
          },
          title: {
            display: true,
            text: ' ',
            font: { size: 10 }
          }
        },
        yLine: {
          display: true,
          stacked: false,
          position: 'right',
          min: 0,
          max: 1.2,
          grid: { drawOnChartArea: false },
          ticks: {
            display: false,
            stepSize: 0.2
          },
          title: { display: false }
        }
      }
    },
    plugins: [ChartDataLabels]
  });
})();
</script>



@endsection