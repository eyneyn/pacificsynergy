{{-- Overview content --}}
<div x-show="activeTab === 'overview'"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-100 transform scale-100"
     class="mt-6">
    <div class="w-full bg-white rounded-sm border border-gray-200 p-6 mb-8 shadow-md space-y-5 transition-all duration-300 hover:shadow-xl hover:border-[#E5E7EB] flex flex-col">
        <h2 class="text-lg font-semibold mb-4 text-[#23527c]">Material Efficiency</h2>

        @php
            // --- Build chart series from $reports (per-row, not per-day) ---
            $chartLabels       = [];   // 1..N row numbers
            $preformSeries     = [];
            $capsSeries        = [];
            $labelsSeries      = [];
            $ldpeSeries        = [];
            $targetEfficiency  = [];   // constant 1% as 0.01

            // --- Build overview table values per row (aligned with chart) ---
            $rowTargetEfficiency = []; // '1.00%' per row
            $rowProductionCases  = []; // cases (number or blank when No Run)
            $rowPreformsPct      = []; // 'X.XX%' per row (blank when No Run)
            $rowCapsPct          = [];
            $rowLabelsPct        = [];
            $rowLdpePct          = [];
            $rowIsNoRun          = []; // boolean per row (for blanks if needed)

            $pctFloat = function ($rej, $fg, $qa) {
                $den = $fg + $rej + $qa;
                return $den > 0 ? round($rej / $den, 4) : null; // null skips point in Chart.js
            };

            $pctText = function ($rej, $fg, $qa, $isNoRun) {
                if ($isNoRun) return ''; // blank if No Run
                $den = $fg + $rej + $qa;
                return $den > 0 ? number_format(($rej / $den) * 100, 2) . '%' : '0.00%';
            };

            $rowIndex = 1;
            foreach ($analytics as $row) {
                // Instead of calculating again, just use the precomputed values
                $chartLabels[]       = $rowIndex;
                $targetEfficiency[]  = $row->targetMaterialEfficiency ?? 0.01;

                // Chart series (as ratios for Chart.js)
                $preformSeries[] = $row->preform_pct / 100;
                $capsSeries[]    = $row->caps_pct / 100;
                $labelsSeries[]  = $row->label_pct / 100;
                $ldpeSeries[]    = $row->ldpe_pct / 100;

                // Table values (percent text)
                $rowTargetEfficiency[$rowIndex] = number_format(($row->targetMaterialEfficiency ?? 0.01) * 100, 2) . '%';
                $rowProductionCases[$rowIndex]  = $row->total_output ?? '';
                $rowPreformsPct[$rowIndex]      = number_format($row->preform_pct, 2) . '%';
                $rowCapsPct[$rowIndex]          = number_format($row->caps_pct, 2) . '%';
                $rowLabelsPct[$rowIndex]        = number_format($row->label_pct, 2) . '%';
                $rowLdpePct[$rowIndex]          = number_format($row->ldpe_pct, 2) . '%';

                $rowIndex++;
            }
        @endphp

        {{-- Chart canvas (single instance) --}}
        <canvas id="efficiencyChart" style="height: 400px !important;" class="w-full !pl-0 !ml-0 !translate-x-0"></canvas>


<div>
    <table class="w-full text-xs border border-gray-300 border-collapse">
        <thead class="bg-[#f1f5f9] text-center">
            <tr>
                <th class="border border-gray-300 px-2 py-1 w-[200px] whitespace-nowrap"> </th>
                <th class="border border-gray-300 px-2 py-1 w-[200px] whitespace-nowrap">Preform</th>
                <th class="border border-gray-300 px-2 py-1 w-[200px] whitespace-nowrap">Caps</th>
                <th class="border border-gray-300 px-2 py-1 w-[200px] whitespace-nowrap">OPP</th>
                <th class="border border-gray-300 px-2 py-1 w-[200px] whitespace-nowrap">Shrinkfilm</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="p-1 border border-gray-300 text-center">
                    PTD Line Eff:
                </td>
                <td class="p-1 border border-gray-300 text-center">
                    {{ $totalPreformPct }}
                </td>
                <td class="p-1 border border-gray-300 text-center">
                    {{ $totalCapsPct }}
                </td>
                <td class="p-1 border border-gray-300 text-center">
                   {{ $totalLabelPct }}
                </td>
                <td class="p-1 border border-gray-300 text-center">
                    {{ $totalLdpePct }}
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{-- Daily Material Utilization Summary Table --}}
<div class="overflow-x-auto mt-4">
    <table class="min-w-full text-[10px] border border-gray-300 table-auto">
        <thead class="bg-[#f1f5f9]  text-center">
            <tr>
                <th class="border border-gray-300 px-2 py-1 text-left w-[200px] whitespace-nowrap">
                    Indicator
                </th>
                @foreach ($chartLabels as $colNo)
                    <th class="border border-gray-300 px-2 py-1 text-right whitespace-nowrap">
                        {{ $colNo }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="text-[9px]">
            {{-- Target MAT Efficiency --}}
            <tr>
                <td class="border border-gray-300 px-2 py-1  text-left whitespace-nowrap">
                        <span class="inline-flex items-center mr-2">
                            <span class="h-[2px] w-4 bg-[#00B050]"></span>
                            <span class="inline-block w-2 h-2 rounded-full bg-[#4A7EBB] mx-1"></span>
                            <span class="h-[2px] w-4 bg-[#00B050]"></span>
                        </span>
                    Target MAT Efficiency, %
                </td>
                @foreach ($chartLabels as $colNo)
                    <td class="border border-gray-300 px-2 py-1 text-right">
                        {{ $rowTargetEfficiency[$colNo] ?? '1.00%' }}
                    </td>
                @endforeach
            </tr>

            {{-- Production Output (cases) --}}
            <tr>
                <td class="border px-2 py-1 text-left  whitespace-nowrap">
                            <span class="inline-flex items-center mr-2">
            <span class="h-[2px] w-4 bg-[#376faa]"></span>
            <span class="inline-block w-2 h-2 rounded-full bg-[#376faa] mx-1"></span>
            <span class="h-[2px] w-4 bg-[#376faa]"></span>
        </span>
                    Production Output (Cases)
                </td>
                @foreach ($chartLabels as $colNo)
                    <td class="border px-2 py-1 text-right">
                        {{ $rowProductionCases[$colNo] ?? '' }}
                    </td>
                @endforeach
            </tr>

            {{-- Preforms --}}
            <tr>
                <td class="border px-2 py-1 text-left  whitespace-nowrap">
                    <span class="inline-flex items-center mr-2">
                        <span class="h-[2px] w-4 bg-[#7F7F7F]"></span>
                        <span class="inline-block w-2 h-2 rounded-full bg-[#BE4B48] mx-1"></span>
                        <span class="h-[2px] w-4 bg-[#7F7F7F]"></span>
                    </span>
                    PREFORMS % Rejects
                </td>
                @foreach ($chartLabels as $colNo)
                    <td class="border px-2 py-1 text-right">{{ $rowPreformsPct[$colNo] ?? '' }}</td>
                @endforeach
            </tr>

            {{-- Caps --}}
            <tr>
                <td class="border px-2 py-1 text-left  whitespace-nowrap">
                            <span class="inline-flex items-center mr-2">
            <span class="h-[2px] w-4 bg-[#254061]"></span>
            <span class="inline-block w-2 h-2 rounded-full bg-[#9BBB59] mx-1"></span>
            <span class="h-[2px] w-4 bg-[#254061]"></span>
        </span>
                    CAPS % Rejects
                </td>
                @foreach ($chartLabels as $colNo)
                    <td class="border px-2 py-1 text-right">{{ $rowCapsPct[$colNo] ?? '' }}</td>
                @endforeach
            </tr>

            {{-- OPP Labels --}}
            <tr>
                <td class="border px-2 py-1 text-left  whitespace-nowrap">
                            <span class="inline-flex items-center mr-2">
            <span class="h-[2px] w-4 bg-[#77933C]"></span>
            <span class="inline-block w-2 h-2 rounded-full bg-[#8064A2] mx-1"></span>
            <span class="h-[2px] w-4 bg-[#77933C]"></span>
        </span>
                    OPP LABELS % Rejects
                </td>
                @foreach ($chartLabels as $colNo)
                    <td class="border px-2 py-1 text-right">{{ $rowLabelsPct[$colNo] ?? '' }}</td>
                @endforeach
            </tr>

            {{-- LDPE Shrinkfilm --}}
            <tr>
                <td class="border px-2 py-1 text-left  whitespace-nowrap">
                                                <span class="inline-flex items-center mr-2">
<span class="h-[2px] w-4 bg-[#984807]/70"></span>
<span class="inline-block w-2 h-2 rounded-full bg-[#4BACC6] mx-1"></span>
<span class="h-[2px] w-4 bg-[#984807]/70"></span>
        </span>
                    LDPE Shrinkfilm % Rejects
                </td>
                @foreach ($chartLabels as $colNo)
                    <td class="border px-2 py-1 text-right">{{ $rowLdpePct[$colNo] ?? '' }}</td>
                @endforeach
            </tr>
        </tbody>
    </table>
</div>



        {{-- Chart.js (scoped to this tab to avoid duplicate canvases) --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const efficiencyLabels     = @json($chartLabels);
    const targetEfficiencyData = @json($targetEfficiency);
    const preformData          = @json($preformSeries);
    const capsData             = @json($capsSeries);
    const labelData            = @json($labelsSeries);
    const ldpeData             = @json($ldpeSeries);

    const ctx = document.getElementById('efficiencyChart').getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: efficiencyLabels,
            datasets: [

{
    label: 'Target MAT Efficiency, %',
    data: targetEfficiencyData,
    borderColor: '#00B050',       // ✅ line color
    borderDash: [4, 4],
    tension: 0.3,
    fill: false,
    borderWidth: 2,
    pointRadius: 3,
    pointBackgroundColor: '#4A7EBB', // ✅ marker color same as line
    pointBorderWidth: 0,             // ✅ removes border stroke
    spanGaps: true
},
                                {
                    label: 'PREFORMS',
                    data: preformData,
borderColor: '#7F7F7F',          // ✅ line color
    tension: 0.3,
    fill: false,
    borderWidth: 2,
    pointRadius: 3,
    pointBackgroundColor: '#BE4B48', // ✅ marker color same as line
    pointBorderWidth: 0,             // ✅ no marker border
    spanGaps: true  
                },
                {
                    label: 'CAPS',
                    data: capsData,
borderColor: '#254061',          // ✅ line color
    tension: 0.3,
    fill: false,
    borderWidth: 2,
    pointRadius: 3,
    pointBackgroundColor: '#98B954', // ✅ marker color same as line
    pointBorderWidth: 0,             // ✅ no marker border
    spanGaps: true 
                },
                {
                    label: 'OPP LABELS',
                    data: labelData,
borderColor: '#77933C',          // ✅ line color
    tension: 0.3,
    fill: false,
    borderWidth: 2,
    pointRadius: 3,
    pointBackgroundColor: '#7D60A0', // ✅ marker color same as line
    pointBorderWidth: 0,             // ✅ no marker border
    spanGaps: true 
                },
                {
                    label: 'LDPE SHRINK FILM',
                    data: ldpeData,
borderColor: '#984807',          // ✅ line color
    tension: 0.3,
    fill: false,
    borderWidth: 2,
    pointRadius: 3,
    pointBackgroundColor: '#46AAC5', // ✅ marker color same as line
    pointBorderWidth: 0,             // ✅ no marker border
    spanGaps: true 
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false
            },
            layout: {
                padding: {
                    top: 10,
                    bottom: 10,
                    left: 10,
                    right: 10
                }
            },
plugins: {
    legend: {
        display: false   // ✅ hides the legend entirely
    },
                tooltip: {
                    mode: 'nearest',
                    intersect: false,
                    position: 'average',
                    backgroundColor: '#fff',
                    titleColor: '#000',
                    bodyColor: '#000',
                    borderColor: '#ccc',
                    borderWidth: 1,
                    cornerRadius: 6,
                    padding: 10,
                    boxPadding: 6,
                    caretPadding: 8,
                    displayColors: true,
                    usePointStyle: true,
                    multiKeyBackground: '#f3f4f6',
                    callbacks: {
                        label: ctx => {
                            const val = ctx.parsed.y;
                            return `${ctx.dataset.label}: ${val == null ? '—' : (val * 100).toFixed(2) + '%'}`;
                        }
                    }
                }
            },
scales: {
    y: {
        min: 0,        // ✅ fixed minimum
        max: 0.012,     // ✅ fixed maximum
        ticks: {
            stepSize: 0.002, // ✅ interval of 0.02 (2%)
            callback: value => (value * 100).toFixed(2) + '%'
        },
        grid: {
            color: 'rgba(0,0,0,0.08)',
            lineWidth: 0.5
        },
        title: {
            display: true,
            text: 'Rejects (%)',
            font: { size: 10 }
        }
    },
    x: {
        title: {
            display: true,
            text: '',
            font: { size: 10 }
        },
        ticks: {
            autoSkip: false
        },
        grid: {
            display: false
        }
    }
}


        },
plugins: [
    {
        id: 'minorGuidelines',
        afterDraw(chart) {
            const { ctx, chartArea, scales: { y } } = chart;
            ctx.save();
            ctx.strokeStyle = '#F2F2F2';
            ctx.lineWidth = 0.05;

            // Draw extra minor lines every 0.02
            for (let v = 0; v <= 0.012; v += 0.002) {
                const yPos = y.getPixelForValue(v);
                ctx.beginPath();
                ctx.moveTo(chartArea.left, yPos);
                ctx.lineTo(chartArea.right, yPos);
                ctx.stroke();
            }

            ctx.restore();
        }
    }
]

    });
</script>

    </div>
</div>
