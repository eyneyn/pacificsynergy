{{-- ===========================
  OPL TAB
  - Chart + category table
  ============================ --}}
<div x-show="activeTab === 'opl'"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-100 transform scale-100">

  <div class="w-full bg-white rounded-sm border border-gray-200 p-6 shadow-md hover:shadow-xl hover:border-[#E5E7EB]">
    <div class="flex items-center justify-between">
      <h2 class="text-lg font-semibold mb-4 text-[#23527c]">OPL Downtimes in Minutes</h2>
    </div>

    {{-- Chart --}}
    <div class="w-full mb-6 flex justify-center">
      <canvas id="oplChart" style="height: 350px !important;" class="w-full !pl-0 !ml-0 !translate-x-0"></canvas>
    </div>

    {{-- Table --}}
    <div class="w-full overflow-x-auto">
      <table class="min-w-[1500px] text-xs text-center border border-collapse border-gray-300 whitespace-nowrap">
        <thead class="text-white">
          <tr>
            <th class="p-2 border border-[#F2F2F2] text-center w-[120px] whitespace-nowrap bg-[#0070C0]">Production Date</th>
            @foreach($oplCategories as $label)
              <th class="p-2 border border-[#F2F2F2] text-center w-[120px] whitespace-nowrap bg-[#0070C0]">{{ $label }}</th>
            @endforeach
          </tr>
        </thead>
        <tbody class="text-gray-700">
          @foreach($oplData as $row)
            <tr>
              <td class="border border-[#F2F2F2] bg-[#DBE5F1] p-2 text-center">{{ $row['date'] }}</td>
              @foreach($oplCategories as $cat)
                <td class="border border-[#F2F2F2] bg-[#DBE5F1] p-2 text-center">
                  {{ number_format($row['categories'][$cat] ?? 0) }}
                </td>
              @endforeach
            </tr>
          @endforeach
          {{-- Divider rows --}}
<tr><th class="p-2 bg-[#595959]" colspan="{{ 1 + count($oplCategories) }}"></th></tr>
<tr><th class="p-2 bg-[#F2F2F2]" colspan="{{ 1 + count($oplCategories) }}"></th></tr>

{{-- Weekly OPL totals per category --}}
@foreach($weeklyOplByCategory as $week => $cats)
  <tr>
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">
      Week {{ $week }}
    </td>
    @foreach($oplCategories as $cat)
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
  @foreach($oplCategories as $cat)
    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4] font-semibold">
      {{ number_format($oplTotalsByCategory[$cat] ?? 0) }}
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
  const oplCtx = document.getElementById('oplChart')?.getContext('2d');
  if (oplCtx) {
    const oplLabels = @json($oplCategories);
    const oplTotals = @json(
      collect($oplCategories)->map(function($cat) use ($oplData) {
        return collect($oplData)->sum(fn($row) => $row['categories'][$cat] ?? 0);
      })
    );

    // ✅ Find max and round up to nearest 500, then add +500 for spacing
    const maxValue = Math.max(...oplTotals, 0);
    const yMax = maxValue > 0
      ? (Math.ceil(maxValue / 500) * 500) + 500
      : 500; // default if all 0

    new Chart(oplCtx, {
      type: 'bar',
      data: {
        labels: oplLabels,
        datasets: [{
          label: 'OPL Downtime (min)',
          data: oplTotals,
          backgroundColor: '#B3A2C7',
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
            anchor: 'end',       // place at top of bar
            align: 'end',
            offset: -2,          // slight lift above bar
            color: '#000',       // black text
            font: {
              weight: 'bold',
              size: 11
            },
            formatter: v => v.toLocaleString()
          }
        },
        scales: {
          x: {
            ticks: {
              callback: function(value) {
                const label = this.getLabelForValue(value);
                const maxChars = 10; // wrap long labels
                if (label.length > maxChars) {
                  const words = label.split(' ');
                  let line = '', lines = [];
                  for (const w of words) {
                    if ((line + w).length <= maxChars) line += w + ' ';
                    else { lines.push(line.trim()); line = w + ' '; }
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
            max: yMax,
            ticks: {
              stepSize: 500,
              callback: v => v.toLocaleString()
            }
          }
        }
      },
      plugins: [ChartDataLabels] // ✅ enable the datalabels plugin
    });
  }
</script>

