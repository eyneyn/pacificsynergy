@extends('layouts.app')

@section('content')

<div class="mb-5 gap-4 md:gap-0 "> 
    <h2 class="text-2xl font-bold text-[#2d326b]">Analytics and Report</h2>
</div>
    <!-- Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-5">
        
<!-- Material Monitoring -->
<div class="bg-white rounded-xl shadow border border-gray-200 p-8 hover:bg-[#e5f4ff] hover:shadow-xl transition duration-300">
    <h3 class="text-lg font-semibold text-[#323B76] mb-2">Material Monitoring</h3>
    <p class="text-sm text-gray-600 mb-6">Monitors the material utilization of Production Department.</p>
    <div class="flex justify-between items-center">
        <a href="{{ route('analytics.materials.index') }}" class="text-sm text-white bg-[#323B76] hover:bg-[#2d326b] px-4 py-2 rounded-md font-medium">
            Manage
        </a>
    </div>
</div>

<!-- Line Efficiency Monitoring -->
<div class="bg-white rounded-xl shadow border border-gray-200 p-8 hover:bg-[#e5f4ff] hover:shadow-xl transition duration-300">
    <h3 class="text-lg font-semibold text-[#323B76] mb-2">Line Efficiency Monitoring</h3>
    <p class="text-sm text-gray-600 mb-6">Track production line performance, downtime, output, and overall efficiency rates.</p>
    <div class="flex justify-between items-center">
        <a href="{{ route('metrics.maintenance.index')}}" class="text-sm text-white bg-[#323B76] hover:bg-[#2d326b] px-4 py-2 rounded-md font-medium">
            Manage
        </a>
    </div>
</div>


    </div>

@endsection