@extends('layouts.app')

@section('content')

<div class="mb-5 gap-4 md:gap-0 "> 
    <h2 class="text-2xl font-bold text-[#2d326b]">Analytics and Report</h2>
</div>

<!-- Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-5">
        
<!-- Material Monitoring -->
<div class="bg-white rounded-xl shadow border border-gray-200 p-8 shadow-md hover:shadow-2xl transition duration-300">
    <h3 class="text-xl font-semibold text-[#2d326b] mb-1">Material Monitoring</h3>
    <p class="text-sm text-gray-600 mb-4">Monitors the material utilization of Production Department.</p>

    <div class="overflow-x-auto rounded-sm mt-6">
        <table class="w-full text-sm text-left rtl:text-right border border-[#E5E7EB] border-collapse">
            <thead class="bg-[#35408e] text-xs text-white uppercase tracking-wide text-center">
                <tr>
                    <th class="px-4 py-2 border border-[#d9d9d9]">Production Line</th>
                    <th class="px-4 py-2 border border-[#d9d9d9]">Action</th>
                </tr>
            </thead>
            <tbody class="text-center">
                <tr class="hover:bg-blue-50 transition duration-150">
                    <td class="px-4 py-2 text-[#2d326b] border border-[#d9d9d9]">Line 1</td>
                    <td class="px-4 py-2 border border-[#d9d9d9]">
                        <a href="{{ route('analytics.materials.index')}}" class="inline-flex items-center border border-[#d9d9d9] gap-2 px-3 py-1.5 bg-[#323B76] hover:bg-[#1f275e] text-white text-xs font-medium rounded-md shadow-sm transition">
                            View
                        </a>
                    </td>
                </tr>
                <tr class="hover:bg-blue-50 transition duration-150">
                    <td class="px-4 py-2 text-[#2d326b] border border-[#d9d9d9]">Line 2</td>
                    <td class="px-4 py-2 border border-[#d9d9d9]">
                        <a href="#" class="inline-flex items-center border border-[#d9d9d9] gap-2 px-3 py-1.5 bg-[#323B76] hover:bg-[#1f275e] text-white text-xs font-medium rounded-md shadow-sm transition">
                            View
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Material Monitoring -->
<div class="bg-white rounded-xl shadow border border-gray-200 p-8 shadow-md hover:shadow-2xl transition duration-300">
    <h3 class="text-xl font-semibold text-[#2d326b] mb-1">Line Efficiency Monitoring</h3>
    <p class="text-sm text-gray-600 mb-4">Track production line performance, downtime, output, and overall efficiency rates.</p>

    <div class="overflow-x-auto rounded-sm mt-6">
        <table class="w-full text-sm text-left rtl:text-right border border-[#E5E7EB] border-collapse">
            <thead class="bg-[#35408e] text-xs text-white uppercase tracking-wide text-center">
                <tr>
                    <th class="px-4 py-2 border border-[#d9d9d9]">Production Line</th>
                    <th class="px-4 py-2 border border-[#d9d9d9]">Action</th>
                </tr>
            </thead>
            <tbody class="text-center">
                <tr class="hover:bg-blue-50 transition duration-150">
                    <td class="px-4 py-2 text-[#2d326b] border border-[#d9d9d9]">Line 1</td>
                    <td class="px-4 py-2 border border-[#d9d9d9]">
                        <a href="#" class="inline-flex items-center border border-[#d9d9d9] gap-2 px-3 py-1.5 bg-[#323B76] hover:bg-[#1f275e] text-white text-xs font-medium rounded-md shadow-sm transition">
                            View
                        </a>
                    </td>
                </tr>
                <tr class="hover:bg-blue-50 transition duration-150">
                    <td class="px-4 py-2 text-[#2d326b] border border-[#d9d9d9]">Line 2</td>
                    <td class="px-4 py-2 border border-[#d9d9d9]">
                        <a href="#" class="inline-flex items-center border border-[#d9d9d9] gap-2 px-3 py-1.5 bg-[#323B76] hover:bg-[#1f275e] text-white text-xs font-medium rounded-md shadow-sm transition">
                            View
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

</div>

@endsection