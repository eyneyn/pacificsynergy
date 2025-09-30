<div class="w-full xl:w-3/4 bg-white rounded-sm border border-gray-200 p-6 shadow-md space-y-5 transition-all duration-300 hover:shadow-xl hover:border-[#E5E7EB] flex flex-col">
  <h3 class="text-lg font-semibold mb-4 text-[#2d326b]">Line Efficiency</h3>
  <canvas id="leChart" height="300" class="w-full !pl-0 !ml-0 !translate-x-0"></canvas>

  {{-- Inline Table (unchanged content from your original) --}}
  @include('analytics.line.partial.le_inline_table')
</div>

<script>
  const labels = {!! json_encode(array_column($ptdMonthlyRows, 'period')) !!};
  const dataEPL = {!! json_encode(array_map(fn($row) => round($row['epl_percent'], 2) / 100, $ptdMonthlyRows)) !!};
  const dataOPL = {!! json_encode(array_map(fn($row) => round($row['opl_percent'], 2) / 100, $ptdMonthlyRows)) !!};
  const dataLE  = {!! json_encode(array_map(fn($row) => round($row['le'], 2) / 100, $ptdMonthlyRows)) !!};
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
          label: 'EPL %',
          data: dataEPL,
          backgroundColor: '#4BACC6',
          type: 'bar',
          stack: 'stack0',
          yAxisID: 'y',
          order: 2
        },
        {
          label: 'OPL %',
          data: dataOPL,
          backgroundColor: '#8064A2',
          type: 'bar',
          stack: 'stack0',
          yAxisID: 'y',
          order: 1
        },
        {
          label: 'LE %',
          data: dataLE,
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
            color: '#2563EB',
            align: 'top',
            anchor: 'end',
            formatter: val => (val * 100).toFixed(2) + '%',
            font: { weight: 'bold', size: 10 }
          }
        },
        {
          label: 'Target LE %',
          data: Array(labels.length).fill(0.80),
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
        }
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


