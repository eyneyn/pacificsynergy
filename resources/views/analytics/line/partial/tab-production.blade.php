{{-- ===========================
  PRODUCTION TAB
  - PTD chart
  - Daily & Weekly tables
  ============================ --}}
<div x-show="activeTab === 'production'"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-100 transform scale-100">

  {{-- Constant Target Line Efficiency (display-only) --}}
  @php($TARGET_LE = 80)

  {{-- ===========================
      PTD Downtime mini chart
      Shows PTD OPL vs EPL minutes
      ============================ --}}
  <div class="w-full mb-8 bg-white rounded-sm border border-gray-200 p-6 shadow-md hover:shadow-xl hover:border-[#E5E7EB]">
    <h2 class="text-lg font-semibold mb-4 text-[#23527c]">OPL &amp; EPL Downtime in Minutes (PTD)</h2>
    <div class="w-full flex items-center justify-center">
      <canvas id="ptdChart" style="height: 350px !important;" class="w-full !pl-0 !ml-0 !translate-x-0"></canvas>
    </div>
  </div>

  {{-- ===========================
      Daily + Weekly Tables
      ============================ --}}
  <div class="w-full bg-white rounded-sm border border-gray-200 p-6 shadow-md hover:shadow-xl hover:border-[#E5E7EB]">
    <div class="overflow-x-auto">
      <div class="min-w-[1800px]">
        <div class="flex flex-col">

          {{-- ========== DAILY TABLES (Left: % by day, Right: minutes & % impact) ========== --}}
          <div class="flex gap-4">

            {{-- Daily – Left: Target/LE/OPL/EPL (%) --}}
            <div class="w-full">
              <table class="w-full table-fixed text-xs text-center border border-collapse border-gray-300 whitespace-nowrap">
                <thead class="text-white">
                  <tr>
                    <th colspan="7" class="p-4 border border-[#F2F2F2] bg-[#0070C0]"></th>
                  </tr>
                  <tr>
                    <th class="p-2 border border-[#F2F2F2] text-center w-[120px] whitespace-nowrap bg-[#0070C0]">Production Date</th>
                    <th class="p-2 border border-[#F2F2F2] text-center w-[120px] whitespace-nowrap bg-[#0070C0]">S K U</th>
                    <th class="p-2 border border-[#F2F2F2] text-center w-[120px] whitespace-nowrap bg-[#0070C0]">Size</th>
                    <th class="p-2 border border-[#F2F2F2] text-center w-[120px] whitespace-nowrap bg-[#0070C0]">Target LE, %</th>
                    <th class="p-2 border border-[#F2F2F2] text-center w-[120px] whitespace-nowrap bg-[#0070C0]">LE, %</th>
                    <th class="p-2 border border-[#F2F2F2] text-center w-[120px] whitespace-nowrap bg-[#0070C0]">OPL, %</th>
                    <th class="p-2 border border-[#F2F2F2] text-center w-[120px] whitespace-nowrap bg-[#0070C0]">EPL, %</th>
                  </tr>
                </thead>
                <tbody class="text-gray-700">
                  @foreach($dailyRows as $row)
                    <tr >
                      <td class="border border-[#F2F2F2] bg-[#DBE5F1] p-2 text-center">{{ $row['date'] }}</td>
                      <td class="border border-[#F2F2F2] bg-[#DBE5F1] p-2 text-center">{{ $row['sku'] }}</td>
                      <td class="border border-[#F2F2F2] bg-[#DBE5F1] p-2 text-center">{{ $row['size'] }}</td>

                      {{-- Target LE is fixed to 80% for display --}}
                      <td class="border border-[#F2F2F2] bg-[#F2F2F2] p-2 text-center">{{ $TARGET_LE }}%</td>

                      {{-- Note: controller already rounds; view adds consistent formatting --}}
                      <td class="border border-[#F2F2F2] bg-[#DBE5F1] p-2 text-center">{{ number_format($row['le'], 2) }}%</td>
                      <td class="border border-[#F2F2F2] bg-[#F2F2F2] p-2 text-center">{{ number_format($row['opl_percent'], 2) }}%</td>
                      <td class="border border-[#F2F2F2] bg-[#F2F2F2] p-2 text-center">{{ number_format($row['epl_percent'], 2) }}%</td>
                    </tr>
                  @endforeach
                  
                </tbody>
              </table>
            </div>

            {{-- Daily – Right: Minutes & Percent Impact (DT/OPL/EPL) --}}
            <div class="w-1/4">
              <table class="w-full table-fixed text-xs text-center border border-collapse border-gray-300 whitespace-nowrap">
                <thead class="text-white">
                  <tr>
                    <th colspan="3" class="p-2 border border-[#F2F2F2] bg-[#0070C0]">Minutes</th>
                  </tr>
                  <tr>
                    <th class="p-2 border border-[#F2F2F2] bg-[#0070C0]">Total DT</th>
                    <th class="p-2 border border-[#F2F2F2] bg-[#0070C0]">OPL</th>
                    <th class="p-2 border border-[#F2F2F2] bg-[#0070C0]">EPL</th>
                  </tr>
                </thead>
                <tbody class="text-gray-700">
                  @foreach($dailyRows as $row)
                    <tr >
                      <td class="border border-[#F2F2F2] bg-[#F2F2F2] p-2 text-center">{{ number_format($row['total_mins']) }}</td>
                      <td class="border border-[#F2F2F2] bg-[#F2F2F2] p-2 text-center">{{ number_format($row['opl_mins']) }}</td>
                      <td class="border border-[#F2F2F2] bg-[#F2F2F2] p-2 text-center">{{ number_format($row['epl_mins']) }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            <div class="w-1/4">
              <table class="w-full table-fixed text-xs text-center border border-collapse border-gray-300 whitespace-nowrap">
                <thead class="text-white">
                  <tr>
                    <th colspan="3" class="p-2 border border-[#F2F2F2] bg-[#0070C0]">Minutes</th>
                  </tr>
                  <tr>
                    <th class="p-2 border border-[#F2F2F2] bg-[#0070C0]">Total DT %</th>
                    <th class="p-2 border border-[#F2F2F2] bg-[#0070C0]">OPL %</th>
                    <th class="p-2 border border-[#F2F2F2] bg-[#0070C0]">EPL %</th>
                  </tr>
                </thead>
                <tbody class="text-gray-700">
                  @foreach($dailyRows as $row)
                    <tr >
                      <td class="border border-[#F2F2F2] bg-[#F2F2F2] p-2 text-center">{{ number_format($row['dt'], 2) }}%</td>
                      <td class="border border-[#F2F2F2] bg-[#F2F2F2] p-2 text-center">{{ number_format($row['opl_percent'], 2) }}%</td>
                      <td class="border border-[#F2F2F2] bg-[#F2F2F2] p-2 text-center">{{ number_format($row['epl_percent'], 2) }}%</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>

          {{-- ========== WEEKLY TABLES (Left: % by week, Right: minutes + % by week) ========== --}}
          <div class="flex gap-4">

            {{-- Weekly – Left: LE/OPL/EPL (%) per week + PTD row --}}
            <div class="w-full">
              <table class="w-full table-fixed text-xs text-center border border-collapse border-gray-300 whitespace-nowrap">
                <tbody class="text-gray-700">
                    <tr>
                      <th colspan="7"
                          class="p-2 bg-[#595959]">
                      </th>
                    </tr>  
                    <tr>
                        <th colspan="7"
                            class="p-2 bg-[#F2F2F2]">
                        </th>
                    </tr>  

                  @foreach($finalRows as $weekRow)
                    <tr >
                      <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">Week {{ $weekRow[0] }}</td>
                      <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]"></td>
                      <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]"></td>
                      <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]"></td>

                      {{-- weekly LE, OPL%, EPL% (already computed in controller) --}}
                      <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">{{ number_format($weekRow[1], 2) }}%</td>
                      <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">{{ number_format($weekRow[2], 2) }}%</td>
                      <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">{{ number_format($weekRow[3], 2) }}%</td>
                    </tr>
                  @endforeach

                  {{-- PTD summary row (uses controller scalars) --}}
                  <tr class="font-bold">
                    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">PTD</td>
                    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]"></td>
                    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]"></td>
                    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]"></td>
                    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">{{ number_format($ptdLE, 2) }}%</td>
                    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">{{ number_format($ptdOPL, 2) }}%</td>
                    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">{{ number_format($ptdEPL, 2) }}%</td>
                  </tr>
                </tbody>
              </table>
            </div>

            {{-- Weekly – Right: Minutes & % impact per week + PTD totals --}}
            <div class="w-1/4">
              <table class="w-full table-fixed text-xs text-center border border-collapse border-gray-300 whitespace-nowrap">
                <tbody class="text-gray-700">
                    <tr>
                      <th colspan="3"
                          class="p-2 bg-[#595959]">
                      </th>
                    </tr>  
                    <tr>
                        <th colspan="3"
                            class="p-2 bg-[#F2F2F2]">
                        </th>
                    </tr>  
                  @foreach($weeklyRows as $row)
                    <tr >
                      <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">{{ number_format($row['dt'], 0) }}</td>
                      <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">{{ number_format($row['opl'], 0) }}</td>
                      <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">{{ number_format($row['epl'], 0) }}</td>
                    </tr>
                  @endforeach

                  {{-- PTD totals row (minutes + PTD %) --}}
                  <tr class="font-bold">
                    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">{{ number_format($totalOpl + $totalEpl) }}</td>
                    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">{{ number_format($totalOpl) }}</td>
                    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">{{ number_format($totalEpl) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="w-1/4">
              <table class="w-full table-fixed text-xs text-center border border-collapse border-gray-300 whitespace-nowrap">
                <caption class="sr-only">Weekly downtime minutes and percent impact</caption>
                <tbody class="text-gray-700">
                    <tr>
                      <th colspan="3"
                          class="p-2 bg-[#595959]">
                      </th>
                    </tr>  
                    <tr>
                        <th colspan="3"
                            class="p-2 bg-[#F2F2F2]">
                        </th>
                    </tr>  
                  @foreach($weeklyRows as $row)
                    <tr >
                      <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">{{ number_format($row['dt_percent'], 2) }}%</td>
                      <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">{{ number_format($row['opl_percent'], 2) }}%</td>
                      <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">{{ number_format($row['epl_percent'], 2) }}%</td>
                    </tr>
                  @endforeach
                    {{-- PTD DT% = 100 − PTD LE --}}
                    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">{{ number_format(max(0, min(100, 100 - $ptdLE)), 2) }}%</td>
                    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">{{ number_format($ptdOPL, 2) }}%</td>
                    <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">{{ number_format($ptdEPL, 2) }}%</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
  <script>
    // ------- PTD bar chart -------
    (function () {
      const el = document.getElementById('ptdChart');
      if (!el) return;
      const ctx = el.getContext('2d');

      const totalOpl = {{ (int) $totalOpl }};
      const totalEpl = {{ (int) $totalEpl }};

      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: ['OPL, Minutes', 'EPL, Minutes'],
          datasets: [{
            label: 'Downtime (min)',
            data: [totalOpl, totalEpl],
            backgroundColor: ['#B3A2C7', '#93CDDD'],
            borderSkipped: false
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: c => `${(c.raw ?? 0).toLocaleString()} minutes` } },
            datalabels: {
              color: '#111827',
              anchor: 'end', align: 'end',
              font: { weight: 'bold', size: 12 },
              formatter: v => (v ?? 0).toLocaleString()
            }
          },
          scales: {
            x: { grid: { display: false, drawBorder: false } },
            y: {
              beginAtZero: true,
              min: 0,
              // Auto-scale to next 1000 above the largest value + extra 1000 for spacing
              max: Math.max(totalOpl, totalEpl) 
                ? (Math.ceil(Math.max(totalOpl, totalEpl) / 1000) * 1000) + 1000
                : 5000,
              ticks: {
                stepSize: 1000,   // ✅ always interval of 1000
                callback: v => v.toLocaleString()
              },
              grid: { drawBorder: false }
            }
          }
        },
        plugins: [ChartDataLabels]
      });
    })();
  </script>
