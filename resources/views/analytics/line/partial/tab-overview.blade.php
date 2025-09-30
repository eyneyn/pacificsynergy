{{-- ===========================
  OVERVIEW TAB
  - Chart + compact table
  - Export button moved here so it’s shown alongside overview
  ============================ --}}
<div x-show="activeTab === 'overview'"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-100 transform scale-100">

  @php
    // Build arrays from daily rows so both table and chart are in sync
    $dayNumbers   = collect($dailyRows)
                      ->map(fn($r) => \Carbon\Carbon::parse($r['date'])->day)
                      ->values()->all();                                // e.g. [1,2,3,...]
    $leSeries     = collect($dailyRows)->pluck('le')
                      ->map(fn($v) => (float) $v)->values()->all();     // LE % per day
    $oplSeries    = collect($dailyRows)->pluck('opl_percent')
                      ->map(fn($v) => (float) $v)->values()->all();     // OPL % per day (already scaled by DT%)
    $eplSeries    = collect($dailyRows)->pluck('epl_percent')
                      ->map(fn($v) => (float) $v)->values()->all();     // EPL % per day (already scaled by DT%)
    $targetSeries = array_fill(0, count($dayNumbers), 80);              // Fixed target LE%
  @endphp

  {{-- Card: Line Efficiency --}}
  <div class="w-full bg-white rounded-sm border border-gray-200 p-6 mb-8 shadow-md space-y-5 transition-all duration-300 hover:shadow-xl hover:border-[#E5E7EB]">
    <h2 class="text-lg font-semibold mb-4 text-[#23527c]">Line Efficiency</h2>

    {{-- Chart canvas (Chart.js configured in _charts_js) --}}
    <canvas id="leChart" style="height: 350px !important;" class="w-full !pl-0 !ml-0 !translate-x-0"></canvas>

    {{-- Inline data table aligned with chart labels --}}
        <div class="overflow-x-auto mt-4">
        <table class="min-w-full text-[10px] border border-gray-300 text-gray-700 table-auto">
        <thead class="bg-[#f1f5f9]  text-center">
            <tr>
                <th class="border border-gray-300 px-2 py-1 text-left w-[200px] whitespace-nowrap">
                    Indicator
                </th>
                @foreach($dayNumbers as $d)
                  <th class="border border-gray-300 px-2 py-1 text-right whitespace-nowrap">{{ $d }}</th>
                @endforeach
          </tr>
        </thead>
            <tbody class="text-[9px]">
            {{-- OPL % from ProductionReport --}}
            <tr>
                <td class="border border-gray-300 px-2 py-1  text-left whitespace-nowrap">
                  <span class="inline-flex items-center mr-2">
                      <span class="inline-block w-12 h-2 bg-[#8064A2]"></span>
                  </span>  
                  OPL, %
                </td>
                @foreach($oplSeries as $val)
                <td class="border border-gray-300 px-2 py-1 text-right">{{ number_format($val, 2) }}%</td>
                @endforeach
            </tr>

            {{-- EPL % from ProductionReport --}}
            <tr>
                <td class="border border-gray-300 px-2 py-1  text-left whitespace-nowrap">
                  <span class="inline-flex items-center mr-2">
                      <span class="inline-block w-12 h-2 bg-[#4BACC6]"></span>
                  </span> 
                  EPL, %
                </td>
                @foreach($eplSeries as $val)
                <td class="border border-gray-300 px-2 py-1 text-right">{{ number_format($val, 2) }}%</td>
                @endforeach
            </tr>

            {{-- Target LE (fixed 80%) --}}
            <tr>
                <td class="border border-gray-300 px-2 py-1  text-left whitespace-nowrap">
                    <span class="inline-flex items-center mr-2">
                        <span class="h-[2px] w-4 bg-[#D9D9D9]"></span>
                        <span class="inline-block w-2 h-2 rounded-full bg-[#98B954] mx-1"></span>
                        <span class="h-[2px] w-4 bg-[#D9D9D9]"></span>
                    </span>
                    Target LE, %</td>
                @foreach($targetSeries as $val)
                <td class="border border-gray-300 px-2 py-1 text-right">{{ $val }}%</td>
                @endforeach
            </tr>

            {{-- Actual LE % --}}
            <tr>
                <td class="border border-gray-300 px-2 py-1  text-left whitespace-nowrap">
                    <span class="inline-flex items-center mr-2">
                        <span class="h-[2px] w-4 bg-[#4F81BD]"></span>
                        <span class="inline-block w-2 h-2 rounded-full bg-[#7D60A0] mx-1"></span>
                        <span class="h-[2px] w-4 bg-[#4F81BD]"></span>
                    </span>
                    LE, %
                </td>
                @foreach($leSeries as $val)
                <td class="border border-gray-300 px-2 py-1 text-right">{{ number_format($val, 2) }}%</td>
                @endforeach
            </tr>
            </tbody>
        </table>
        </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

<script>
  (function () {
    const el = document.getElementById('leChart');
    if (!el) return;
    const ctx = el.getContext('2d');

    const dayNumbers = @json($dayNumbers);
    const labels = dayNumbers.map(d => `${d}`);

    const eplSeries = @json($eplSeries).map(val => val / 100);
    const oplSeries = @json($oplSeries).map(val => val / 100);
    const leSeries = @json($leSeries).map(val => val / 100);
    const targetSeries = @json($targetSeries).map(val => val / 100);

    new Chart(ctx, {
        type: 'bar',
      data: {
        labels,
        datasets: [
          // Line datasets (drawn after bars)
          {
            label: 'Target LE',
            data: targetSeries,
            type: 'line',
            borderColor: '#D9D9D9',
            backgroundColor: '#D9D9D9',
            tension: 0.4,
            pointRadius: 4,
            pointBackgroundColor: '#98B954',
            pointBorderColor: '#98B954',
            yAxisID: 'yLine',
            datalabels: { display: false },
            order: 3
          },
          {
            label: 'Line Efficiency',
            data: leSeries,
            type: 'line',
            borderColor: '#4F81BD',
            backgroundColor: '#4F81BD',
            tension: 0.4,
            pointRadius: 4,
            pointBackgroundColor: '#7D60A0',
            pointBorderColor: '#7D60A0',
            yAxisID: 'yLine',
            order: 4,
            datalabels: {
              color: '#7D60A0',
              align: 'top',
              anchor: 'end',
              formatter: val => (val * 100).toFixed(2) + '%',
              font: { weight: 'bold', size: 10 }
            }          
          },
          {
            label: 'OPL',
            data: oplSeries,
            backgroundColor: '#8064A2',
            type: 'bar',
            stack: 'dt',
            yAxisID: 'y',
            order: 1
          },
          {
            label: 'EPL',
            data: eplSeries,
            backgroundColor: '#4BACC6',
            type: 'bar',
            stack: 'dt',
            yAxisID: 'y',
            order: 2
          },
        ]
      },
      options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: {
              legend: {
        display: false   // ✅ hides the legend entirely
    },
          tooltip: {
            callbacks: {
              title: items => items?.[0]?.label ?? '',
              label: ({ dataset, raw }) =>
                raw != null ? `${dataset.label}: ${(raw * 100).toFixed(2)}%` : null
            }
          },
         datalabels: { display: false }
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
              callback: v => v.toFixed(2)
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
              stepSize: 0.2,
              callback: v => v.toFixed(2)
            },
            title: { display: false }
          }
        }
      },
      plugins: [ChartDataLabels]
    });
  })();
</script>

