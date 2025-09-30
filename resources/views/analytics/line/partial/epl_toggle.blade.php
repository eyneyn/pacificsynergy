<div x-data="{ showEPL: false }" class="w-full bg-white rounded-sm border border-gray-200 p-6 shadow-md hover:shadow-xl hover:border-[#E5E7EB]">
  <div class="flex items-center justify-between cursor-pointer" @click="showEPL = !showEPL">
    <h2 class="text-lg font-semibold mb-4 text-[#23527c]">EPL Downtimes in Minutes</h2>
    <svg :class="showEPL ? 'rotate-180' : ''" class="w-5 h-5 transition-transform text-[#2d326b]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
    </svg>
  </div>

  <div x-show="showEPL" x-transition class="mt-6">
    {{-- Chart (YTD totals per category) --}}
    <div class="w-full mb-6 flex justify-center">
      <canvas id="eplChart"style="height: 350px !important;" class="w-full !pl-0 !ml-0 !translate-x-0"></canvas>
    </div>

    {{-- Table: Production Date (P1..P12) × Category minutes --}}
    <div class="w-full overflow-x-auto">
      <table class="w-full text-xs text-center border border-collapse border-gray-300 whitespace-nowrap">
        <thead class="text-white">
          <tr>
            <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#0070C0]">Production Date</th>
            @foreach(($eplLabels ?? []) as $label)
              <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#0070C0]">{{ $label }}</th>
            @endforeach
          </tr>
        </thead>
        <tbody class="text-gray-700">
          @foreach(($eplMonthlyRows ?? []) as $row)
            <tr>
              <td class="border border-[#ffffff] bg-[#F2F2F2] p-2 text-center">{{ $row['period'] }}</td>
              @foreach($row['values'] as $val)
                <td class="border border-[#ffffff] bg-[#F2F2F2] p-2 text-center">{{ number_format((int) $val) }}</td>
              @endforeach
            </tr>
          @endforeach

          <tr>
            <th class="p-2 bg-[#595959]" colspan="{{ 1 + count($eplLabels) }}"></th>
          </tr>

          @if(!empty($eplTotals))
            <tr class="font-bold">
              <td class="border border-[#ffffff] bg-[#FCD5B4] p-2 text-center">Total</td>
              @foreach($eplTotals as $total)
                <td class="border border-[#ffffff] bg-[#FCD5B4] p-2 text-center">{{ number_format((int) $total) }}</td>
              @endforeach
            </tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

<script>
(function() {
  const el = document.getElementById('eplChart');
  if (!el) return;

  const labels  = @json($eplChartLabels ?? []);
  const minutes = @json($eplChartMinutes ?? []);

  // ✅ Find the largest bar value
  const maxVal = Math.max(...minutes, 0);

  // ✅ Choose a clean step size (nearest 500, 1000, or 2000 depending on max)
  const stepSize = (() => {
    if (maxVal <= 2000) return 500;
    if (maxVal <= 5000) return 1000;
    return 2000;
  })();

  // ✅ Round max value up to step and add one more step as buffer
  const suggestedMax = maxVal > 0 
    ? (Math.ceil(maxVal / stepSize) * stepSize) + stepSize 
    : stepSize * 2; // default if all zero

  const ctx = el.getContext('2d');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'EPL Downtime (min, YTD)',
        data: minutes,
        backgroundColor: '#67e8f9',
        borderSkipped: false
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
        tooltip: { 
          callbacks: { 
            label: c => `${(c.raw ?? 0).toLocaleString()} minutes` 
          } 
        },
        datalabels: {
          color: '#111827',
          anchor: 'end',
          align: 'end',
          formatter: v => (v ?? 0).toLocaleString(),
          font: { weight: 'bold', size: 10 }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: { 
            stepSize,
            callback: v => Number(v).toLocaleString() 
          },
          suggestedMax
        }
      }
    },
    plugins: [ChartDataLabels]
  });
})();
</script>

