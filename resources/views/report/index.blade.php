@extends('layouts.app')

@section('content')

    <h2 class="text-2xl font-bold text-[#2d326b]">Production Report</h2>

    <div class="flex flex-col md:flex-row md:items-center justify-between mt-5 mb-6 gap-4">
            <!-- Search bar with filter -->
            <form method="GET" action="{{ route('report.index') }}">
                <div class="w-full max-w-xs px-4 py-1 border border-[#d9d9d9] rounded-md shadow-md transition-all duration-200 hover:shadow-lg hover:border-[#2d326b]">
                    <!-- Search Input with Icon -->
                    <div class="flex items-center flex-grow">
                        <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-4.35-4.35M16.65 16.65A7.5 7.5 0 1 0 3 10a7.5 7.5 0 0 0 13.65 6.65z" />
                        </svg>
                        <input type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search production report"
                            class="w-full border-none text-sm text-gray-700 placeholder-gray-400"/>
                    </div>
                </div>
            </form>
            <a href="{{url('report/add')}}"
            class="inline-flex items-center gap-2 p-3 py-2 bg-[#323B76] hover:bg-[#444d90] border border-[#323B76] text-white text-sm font-medium rounded-md">
                <x-icons-plus-circle class="w-4 h-4 text-white" />
                <span class="text-sm">Production Report</span>
            </a>
    </div>

<!-- Report Table -->
<table class="w-full mt-4 text-sm text-left rtl:text-right border border-[#E5E7EB] border-collapse">
    <thead class="text-xs text-white uppercase bg-[#35408e]">
        <tr>
            <th class="px-6 py-2 border border-[#d9d9d9]">Production Date</th>
            <th class="px-6 py-2 border border-[#d9d9d9] text-center">SKU</th>
            <th class="px-6 py-2 border border-[#d9d9d9] text-center">Line</th>
            <th class="px-6 py-2 border border-[#d9d9d9] text-center">Total Output Case</th>
            <th class="px-6 py-2 border border-[#d9d9d9] text-center">Submitted Date and Time</th>
            <th class="px-6 py-2 border border-[#d9d9d9] text-center">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($reports as $report)
        <tr 
            onclick="window.location='{{ route('report.view', $report->id) }}'"
            class="bg-white hover:bg-gray-50 cursor-pointer"
        >
            <td class="px-6 py-2 border border-[#d9d9d9] text-[#2d326b]">{{ $report->production_date }}</td>
            <td class="px-6 py-2 border border-[#d9d9d9] text-center">{{ $report->sku }}</td>
            <td class="px-6 py-2 border border-[#d9d9d9] text-center">{{ $report->line }}</td>
            <td class="px-6 py-2 border border-[#d9d9d9] text-center">{{ $report->total_outputCase }}</td>
            <td class="px-6 py-2 border border-[#d9d9d9] text-center">{{ $report->created_at }}</td>
            <td class="px-6 py-2 border border-[#d9d9d9] text-center"></td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="px-6 py-2 border border-[#E5E7EB] text-center text-[#35408e]">No report entries found.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<!-- Pagination -->
<div class="mt-6">
    {{ $reports->links('pagination::tailwind') }}
</div>


@endsection