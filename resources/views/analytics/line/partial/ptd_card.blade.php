<div class="w-full mb-8 bg-white rounded-sm border border-gray-200 p-6 shadow-md hover:shadow-xl hover:border-[#E5E7EB]">
  <h2 class="text-lg font-semibold mb-4 text-[#23527c]">OPL & EPL Downtime in Minutes (PTD by Month)</h1>

  <div class="flex flex-col lg:flex-row gap-6 justify-between">
    <div class="w-full lg:w-1/2 flex items-center justify-center">
      <canvas id="ptdChart" style="height: 350px !important;" class="w-full !pl-0 !ml-0 !translate-x-0"></canvas>
    </div>

    <div class="w-full lg:w-1/2 overflow-x-auto">
      <table class="w-full text-xs text-center border border-collapse border-gray-300">
        <thead class="text-white">
          <tr>
            <th colspan="1" class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#0070C0]"></th>
            <th colspan="3" class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#0070C0]">Minutes</th>
            <th colspan="3" class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#0070C0]">Percent Impact</th>
          </tr>
          <tr>
            <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#0070C0]">Production Date</th>
            <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#0070C0]">Total DT</th>
            <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#0070C0]">OPL</th>
            <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#0070C0]">EPL</th>
            <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#0070C0]">Total DT</th>
            <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#0070C0]">OPL</th>
            <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#0070C0]">EPL</th>
          </tr>
        </thead>
        <tbody class="text-gray-700">
          @foreach($ptdMonthlyRows as $row)
            <tr>
              <td class="border border-[#ffffff] bg-[#F2F2F2] p-2 text-center">{{ $row['period'] }}</td>
              <td class="border border-[#ffffff] bg-[#F2F2F2] p-2 text-center">{{ number_format($row['dt']) }}</td>
              <td class="border border-[#ffffff] bg-[#F2F2F2] p-2 text-center">{{ number_format($row['opl']) }}</td>
              <td class="border border-[#ffffff] bg-[#F2F2F2] p-2 text-center">{{ number_format($row['epl']) }}</td>
              <td class="border border-[#ffffff] bg-[#F2F2F2] p-2 text-center">{{ number_format($row['dt_percent'], 2) }}%</td>
              <td class="border border-[#ffffff] bg-[#F2F2F2] p-2 text-center">{{ number_format($row['opl_percent'], 2) }}%</td>
              <td class="border border-[#ffffff] bg-[#F2F2F2] p-2 text-center">{{ number_format($row['epl_percent'], 2) }}%</td>
            </tr>
          @endforeach

          {{-- YTD PTD totals --}}
          <tr class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4] font-semibold">
            <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4] font-semibold">PTD</td>
            <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4] font-semibold">{{ number_format($ptdTotalsRow['dt']) }}</td>
            <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4] font-semibold">{{ number_format($ptdTotalsRow['opl']) }}</td>
            <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4] font-semibold">{{ number_format($ptdTotalsRow['epl']) }}</td>
            <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4] font-semibold">{{ number_format($ptdTotalsRow['dt_percent'], 2) }}%</td>
            <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4] font-semibold">{{ number_format($ptdTotalsRow['opl_percent'], 2) }}%</td>
            <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4] font-semibold">{{ number_format($ptdTotalsRow['epl_percent'], 2) }}%</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script>
(function() {
  const el = document.getElementById('ptdChart');
  if (!el) return;
  const ctxPTD = el.getContext('2d');

  // Use YTD totals for the mini-chart (you can also switch to the latest month if preferred)
  const totalOpl = {{ (int)($ptdTotalsRow['opl'] ?? 0) }};
  const totalEpl = {{ (int)($ptdTotalsRow['epl'] ?? 0) }};

  new Chart(ctxPTD, {
    type: 'bar',
    data: {
      labels: ['OPL, Minutes', 'EPL, Minutes'],
      datasets: [{
        label: 'PTD Downtime (min)',
        data: [totalOpl, totalEpl],
        backgroundColor: ['#8064A2', '#4BACC6'],
        borderSkipped: false // flat bars, no rounded corners
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
        tooltip: { callbacks: { label: ctx => `${ctx.dataset.label}: ${ctx.raw.toLocaleString()} minutes` } },
        datalabels: {
          color: '#111827',
          anchor: 'end',
          align: 'end',
          font: { weight: 'bold', size: 12 },
          formatter: v => v.toLocaleString()
        }
      },
scales: {
  x: { grid: { display: false, drawBorder: false } },
  y: {
    beginAtZero: true,
    ticks: { 
      stepSize: undefined, // auto-calc by Chart.js
      callback: v => Number(v).toLocaleString()
    },
    suggestedMax: (() => {
      const maxVal = Math.max(totalOpl, totalEpl, 0);
      if (maxVal === 0) return 100; // default fallback if no data
      // Round up to nearest 500 and add +500 buffer
      return (Math.ceil(maxVal / 500) * 500) + 500;
    })()
  }
}

    },
    plugins: [ChartDataLabels]
  });
})();
</script>
