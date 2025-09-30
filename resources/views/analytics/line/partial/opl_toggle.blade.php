<div x-data="{ showOPL: false }" class="w-full mb-4 bg-white rounded-sm border border-gray-200 p-6 shadow-md hover:shadow-xl hover:border-[#E5E7EB]">
  <div class="flex items-center justify-between cursor-pointer" @click="showOPL = !showOPL">
    <h2 class="text-lg font-semibold mb-4 text-[#23527c]">OPL Downtimes in Minutes</h2>
    <svg :class="showOPL ? 'rotate-180' : ''" class="w-5 h-5 transition-transform text-[#2d326b]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
    </svg>
  </div>

  <div x-show="showOPL" x-transition class="mt-6">
    {{-- Chart: YTD totals per OPL category --}}
    <div class="w-full mb-6 flex justify-center">
      <canvas id="oplChart"style="height: 350px !important;" class="w-full !pl-0 !ml-0 !translate-x-0"></canvas>
    </div>

    {{-- Table: Production Date (P1..P12) × OPL categories --}}
    <div class="w-full overflow-x-auto">
      <table class="w-full text-xs text-center border border-collapse border-gray-300 whitespace-nowrap">
        <thead class="text-white">
          <tr>
            <th class="p-2 border border-[#F2F2F2] text-center  whitespace-nowrap bg-[#0070C0]">Production Date</th>
            @foreach(($oplLabels ?? []) as $label)
              <th class="p-2 border border-[#F2F2F2] text-center  whitespace-nowrap bg-[#0070C0]">{{ $label }}</th>
            @endforeach
          </tr>
        </thead>
        <tbody class="text-gray-700">
          @foreach(($oplMonthlyRows ?? []) as $row)
            <tr>
              <td class="border border-[#ffffff] bg-[#F2F2F2] p-2 text-center">{{ $row['period'] }}</td>
              @foreach($row['values'] as $val)
                <td class="border border-[#ffffff] bg-[#F2F2F2] p-2 text-center">{{ number_format((int)$val) }}</td>
              @endforeach
            </tr>
          @endforeach

          {{-- Divider rows --}}
          <tr>
            <th class="p-2 bg-[#595959]" colspan="{{ 1 + count($oplLabels) }}"></th>
          </tr>

          @if(!empty($oplTotals))
            <tr class="font-bold">
              <td class="border border-[#ffffff] bg-[#FCD5B4] p-2 text-center">Total</td>
              @foreach($oplTotals as $total)
                <td class="border border-[#ffffff] bg-[#FCD5B4] p-2 text-center">{{ number_format((int)$total) }}</td>
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
(function () {
  const el = document.getElementById('oplChart');
  if (!el) return;

  // 1) Take your labels and wrap long ones to multiple lines
  const rawLabels  = @json($oplChartLabels ?? []);
  const minutes    = @json($oplChartMinutes ?? []);

  // Wrap by " / " first; if not present, soft-wrap every ~14 chars
  const wrap = (s) => {
    if (s.includes(' / ')) return s.split(' / ');
    const words = s.split(' ');
    const lines = [];
    let line = '';
    for (const w of words) {
      const tryLine = line ? line + ' ' + w : w;
      if (tryLine.length > 14) { lines.push(line); line = w; }
      else line = tryLine;
    }
    if (line) lines.push(line);
    return lines;
  };
  const labels = rawLabels.map(wrap);

  // ✅ Dynamic step size + buffer
  const maxVal = Math.max(...minutes, 0);
  const stepSize = (() => {
    if (maxVal <= 2000) return 500;
    if (maxVal <= 5000) return 1000;
    return 2000;
  })();
  const suggestedMax = maxVal > 0
    ? (Math.ceil(maxVal / stepSize) * stepSize) + stepSize
    : stepSize * 2;

  const ctx = el.getContext('2d');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'OPL Downtime (min, YTD)',
        data: minutes,
        backgroundColor: '#B3A2C7',
        borderSkipped: false
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: { label: c => `${(c.raw ?? 0).toLocaleString()} minutes` }
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
        x: {
          ticks: {
            maxRotation: 0,
            minRotation: 0,
            autoSkip: false,
            font: { size: 10 },
            padding: 6
          },
          grid: { display: true, drawBorder: true } // ✅ show X gridlines like EPL
        },
        y: {
          beginAtZero: true,
          ticks: {
            stepSize,
            callback: v => Number(v).toLocaleString()
          },
          suggestedMax,
          grid: { display: true, drawBorder: true } // ✅ show Y gridlines like EPL
        }
      }
    },
    plugins: [ChartDataLabels]
  });
})();
</script>


