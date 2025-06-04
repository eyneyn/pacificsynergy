@extends('layouts.app')

@section('content')

<!-- Tab Navigation -->
<div class="border-b border-gray-200 mb-4">
    <nav class="flex space-x-8" aria-label="Tabs">
        <a href="{{ url('material.monitoring') }}"
           class="{{ request()->routeIs('material.monitoring') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-[#2d326b] hover:border-[#2d326b]' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
            Material Monitoring
        </a>
        <a href="{{ url('line.efficiency.monitoring') }}"
           class="{{ request()->routeIs('line.efficiency.monitoring') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-[#2d326b] hover:border-[#2d326b]' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
            Line Efficiency Monitoring
        </a>
    </nav>
</div>

<div class="bg-white rounded-sm border border-gray-200 p-6 shadow-md transition-all duration-300 hover:shadow-xl hover:border-[#E5E7EB]">

    <!-- 📆 Year Selection -->
    <div class="text-sm text-[#2d326b] mb-6">
        <div class="flex flex-col md:flex-row md:items-center gap-4">
            <label for="date" class="font-semibold">Production Year</label>
            <div>
                <x-select-dropdown name="date" :options="['2024' => '2024']" />
            </div>
        </div>
    </div>

    <!-- Layout: Chart Left, Cards Right -->
    <div class="flex flex-col md:flex-row gap-6">
        <!-- 📊 Chart Area -->
        <div class="w-full">
            <h2 class="text-lg font-semibold mb-4 text-blue-800">Efficiency Trends</h2>
            <canvas id="efficiencyChart" height="120"></canvas>
        </div>

        <!-- 🔹 Cards Area (Stacked Vertically) -->
        <div class="md:w-1/6 space-y-4">
            <div class="bg-white shadow rounded-lg p-4">
                <h3 class="text-sm text-blue-800">Preform</h3>
                <p class="text-xl font-semibold text-blue-600">1.17%</p>
            </div>
            <div class="bg-white shadow rounded-lg p-4">
                <h3 class="text-sm text-blue-800">Caps</h3>
                <p class="text-xl font-semibold text-purple-600">0.24%</p>
            </div>
            <div class="bg-white shadow rounded-lg p-4">
                <h3 class="text-sm text-blue-800">OPP</h3>
                <p class="text-xl font-semibold text-yellow-600">0.11%</p>
            </div>
            <div class="bg-white shadow rounded-lg p-4">
                <h3 class="text-sm text-blue-800">Shrinkfilm</h3>
                <p class="text-xl font-semibold text-red-600">0.23%</p>
            </div>
            <div class="bg-white shadow rounded-lg p-4">
                <h3 class="text-sm text-blue-800">Material Effy PTD</h3>
                <p class="text-xl font-semibold text-green-600">0.44%</p>
            </div>
        </div>
    </div>
</div>

<!-- 📈 Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('efficiencyChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['P1','P2','P3','P4','P5','P6','P7','P8','P9','P10','P11','P12'],
            datasets: [
                {
                    label: 'Actual Effy',
                    data: [0.57, 1.52, 1.32, 1.25, 1.16, 1.58, 1.26, 1.04, 0.98, 0.82, 0.96, 1.11],
                    borderColor: '#6366F1',
                    tension: 0.3,
                    fill: false
                },
                {
                    label: 'Target',
                    data: [2,2,2,2,2,2,2,2,2,2,2,2],
                    borderColor: '#10B981',
                    borderDash: [5, 5],
                    tension: 0.3,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            }
        }
    });
</script>

@endsection
