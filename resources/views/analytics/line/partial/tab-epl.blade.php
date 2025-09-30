{{-- ===========================
  EPL TAB
  - Chart + category table
  ============================ --}}
<div x-show="activeTab === 'epl'"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-100 transform scale-100">

  <div class="w-full bg-white rounded-sm border border-gray-200 p-6 shadow-md hover:shadow-xl hover:border-[#E5E7EB]">
    <div class="flex items-center justify-between">
      <h2 class="text-lg font-semibold mb-4 text-[#23527c]">EPL Downtimes in Minutes</h2>
    </div>

    {{-- Chart --}}
    <div class="w-full mb-6 flex justify-center">
      <canvas id="eplChart" style="height: 350px !important;" class="w-full !pl-0 !ml-0 !translate-x-0"></canvas>
    </div>

    {{-- Table --}}
    <div class="w-full overflow-x-auto">
      <table class="min-w-[1500px] text-xs text-center border border-collapse border-gray-300 whitespace-nowrap">
        <thead class="text-white">
          <tr>
            <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#0070C0]">Production Date</th>
            @foreach($eplCategories as $label)
              <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#0070C0]">{{ $label }}</th>
            @endforeach
          </tr>
        </thead>
        <tbody class="text-gray-700">
          @foreach($eplData as $row)
            <tr>
              <td class="border border-[#F2F2F2] bg-[#DBE5F1] p-2 text-center">{{ $row['date'] }}</td>
              @foreach($eplCategories as $cat)
                <td class="border border-[#F2F2F2] bg-[#DBE5F1] p-2 text-center">
                  {{ number_format($row['categories'][$cat] ?? 0) }}
                </td>
              @endforeach
            </tr>
          @endforeach
          {{-- Divider rows (optional) --}}
          <tr><th class="p-2 bg-[#595959]" colspan="{{ 1 + count($eplCategories) }}"></th></tr>
          <tr><th class="p-2 bg-[#F2F2F2]" colspan="{{ 1 + count($eplCategories) }}"></th></tr>

          {{-- Weekly EPL totals per category --}}
          @foreach($weeklyEplByCategory as $week => $cats)
            <tr>
              <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">
                Week {{ $week }}
              </td>
              @foreach($eplCategories as $cat)
                <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">
                  {{ number_format($cats[$cat] ?? 0) }}
                </td>
              @endforeach
            </tr>
          @endforeach

          {{-- PTD row per category --}}
          <tr>
            <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4] font-semibold">
              PTD
            </td>
            @foreach($eplCategories as $cat)
              <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4] font-semibold">
                {{ number_format($eplTotalsByCategory[$cat] ?? 0) }}
              </td>
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
  const eplCtx = document.getElementById('eplChart')?.getContext('2d');
  if (eplCtx) {
    const eplLabels = @json($eplCategories);
    const eplTotals = @json(collect($eplCategories)->map(function($cat) use ($eplData) {
      return collect($eplData)->sum(fn($row) => $row['categories'][$cat] ?? 0);
    }));

    // Find the max value and round up to the nearest 500, then add +500 for spacing
    const maxValue = Math.max(...eplTotals, 0);
    const yMax = maxValue > 0 
      ? (Math.ceil(maxValue / 500) * 500) + 500 
      : 500; // default if all 0

new Chart(eplCtx, {
  type: 'bar',
  data: {
    labels: eplLabels,
    datasets: [{
      label: 'EPL Downtime (min)',
      data: eplTotals,
      backgroundColor: '#93CDDD',
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
        anchor: 'end',       // position relative to bar
        align: 'end',        // top of the bar
        offset: -2,          // move a little higher
        color: '#000',       // black text
        font: {
          weight: 'bold',
          size: 11
        },
        formatter: v => v.toLocaleString()
      }
    },
    scales: {
      y: {
        beginAtZero: true,
        min: 0,
        max: yMax, // rounded +500
        ticks: {
          stepSize: 500,
          callback: v => v.toLocaleString()
        }
      }
    }
  },
  plugins: [ChartDataLabels] // ✅ activate datalabels plugin
});
}
</script>
