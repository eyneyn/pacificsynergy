@extends('layouts.app')
@section('content')
    {{-- Page Title --}}
    <h2 class="text-xl mb-2 font-bold text-[#23527c] mb-8">Production Report</h2>

    {{-- Top Controls: Back, Add Report, Show Entries --}}
    <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-4">
        <div class="flex flex-col md:flex-row gap-2">
            {{-- Add Report Button --}}
            @can('report.add')
                <a href="{{ url('report/add') }}" class="inline-flex items-center justify-center gap-1 p-2 bg-[#323B76] hover:bg-[#444d90] border border-[#323B76] text-white text-sm font-medium">
                    <x-icons-plus-circle class="w-2 h-2 text-white" />
                    <span class="text-sm">Report</span>
                </a>
            @endcan
            {{-- Show Entries Dropdown --}}
            <div class="inline-flex items-center gap-2 text-sm text-gray-600">
                <span>Show</span>
                <form id="per-page-form" method="GET" action="{{ route('report.index') }}">
                    {{-- Keep existing filters/sort when changing per_page --}}
                    @foreach(request()->except('per_page') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <select name="per_page" class="px-2 py-1 border border-gray-300 text-sm bg-white"
                            onchange="document.getElementById('per-page-form').submit()">
                        <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </form>
                <span>entries</span>
            </div>
        </div>
        {{-- Pagination --}}
        <div>
            {{ $reports->appends(request()->query())->links('pagination::tailwind') }}
        </div>
    </div>

    @php
        // Sorting logic
        $currentSort = request('sort', 'created_at');
        $currentDirection = request('direction', 'desc');
        $toggleDirection = $currentDirection === 'asc' ? 'desc' : 'asc';
    @endphp

    {{-- Search & Table --}}
    <form id="column-search-form" method="GET" action="{{ route('report.index') }}">
        {{-- Preserve existing sort parameters --}}
        @if(request('sort'))
            <input type="hidden" name="sort" value="{{ request('sort') }}">
        @endif
        @if(request('direction'))
            <input type="hidden" name="direction" value="{{ request('direction') }}">
        @endif
        @if(request('per_page'))
            <input type="hidden" name="per_page" value="{{ request('per_page') }}">
        @endif

        {{-- Report Table --}}
        <table class="w-full text-sm text-left border border-[#E5E7EB] border-collapse shadow-sm">
            <thead>
                {{-- Main Header Row --}}
                <tr class="text-xs text-white uppercase bg-[#35408e]">
                    <th class="p-2 border border-[#d9d9d9] text-center">
                        <a href="{{ route('report.index', array_merge(request()->query(), ['sort' => 'production_date', 'direction' => ($currentSort === 'production_date' ? $toggleDirection : 'asc')])) }}"
                            class="flex justify-center items-center gap-1 text-white no-underline">
                            Production Date
                            <svg class="w-4 h-4 {{ $currentSort === 'production_date' ? 'opacity-100' : 'opacity-50' }}" fill="currentColor" viewBox="0 0 512 512">
                                <path d="M304 96h48v320h48l-72 80-72-80h48V96zM64 192h128v32H64v-32zm32 64h96v32H96v-32zm32 64h64v32h-64v-32zm32 64h32v32h-32v-32z"/>
                            </svg>
                        </a>
                    </th>
                    <th class="p-2 border border-[#d9d9d9] text-center">
                        <a href="{{ route('report.index', array_merge(request()->query(), ['sort' => 'sku', 'direction' => ($currentSort === 'sku' ? $toggleDirection : 'asc')])) }}"
                            class="flex justify-center items-center gap-1 text-white no-underline">
                            SKU
                            <svg class="w-4 h-4 {{ $currentSort === 'sku' ? 'opacity-100' : 'opacity-50' }}" fill="currentColor" viewBox="0 0 512 512">
                                <path d="M304 96h48v320h48l-72 80-72-80h48V96zM64 192h128v32H64v-32zm32 64h96v32H96v-32zm32 64h64v32h-64v-32zm32 64h32v32h-32v-32z"/>
                            </svg>
                        </a>
                    </th>
                    <th class="p-2 border border-[#d9d9d9] text-center">
                        <a href="{{ route('report.index', array_merge(request()->query(), ['sort' => 'line', 'direction' => ($currentSort === 'line' ? $toggleDirection : 'asc')])) }}"
                            class="flex justify-center items-center gap-1 text-white no-underline">
                            Line
                            <svg class="w-4 h-4 {{ $currentSort === 'line' ? 'opacity-100' : 'opacity-50' }}" fill="currentColor" viewBox="0 0 512 512">
                                <path d="M304 96h48v320h48l-72 80-72-80h48V96zM64 192h128v32H64v-32zm32 64h96v32H96v-32zm32 64h64v32h-64v-32zm32 64h32v32h-32v-32z"/>
                            </svg>
                        </a>
                    </th>
                    <th class="p-2 border border-[#d9d9d9] text-center">
                        <a href="{{ route('report.index', array_merge(request()->query(), ['sort' => 'total_outputCase', 'direction' => ($currentSort === 'total_outputCase' ? $toggleDirection : 'asc')])) }}"
                            class="flex justify-center items-center gap-1 text-white no-underline">
                            Total Output Case
                            <svg class="w-4 h-4 {{ $currentSort === 'total_outputCase' ? 'opacity-100' : 'opacity-50' }}" fill="currentColor" viewBox="0 0 512 512">
                                <path d="M304 96h48v320h48l-72 80-72-80h48V96zM64 192h128v32H64v-32zm32 64h96v32H96v-32zm32 64h64v32h-64v-32zm32 64h32v32h-32v-32z"/>
                            </svg>
                        </a>
                    </th>
                    <th class="p-2 border border-[#d9d9d9] text-center">
                        <a href="{{ route('report.index', array_merge(request()->query(), ['sort' => 'created_at', 'direction' => ($currentSort === 'created_at' ? $toggleDirection : 'asc')])) }}"
                            class="flex justify-center items-center gap-1 text-white no-underline">
                            Submitted Date and Time
                            <svg class="w-4 h-4 {{ $currentSort === 'created_at' ? 'opacity-100' : 'opacity-50' }}" fill="currentColor" viewBox="0 0 512 512">
                                <path d="M304 96h48v320h48l-72 80-72-80h48V96zM64 192h128v32H64v-32zm32 64h96v32H96v-32zm32 64h64v32h-64v-32zm32 64h32v32h-32v-32z"/>
                            </svg>
                        </a>
                    </th>
                    <th class="p-2 border border-[#d9d9d9] text-center">Status</th>
                </tr>
                {{-- Search Input Row --}}
                <tr>
                    <th class="p-2 border border-[#d9d9d9]">
                        <div class="relative">
                            <input type="text" name="production_date_search" value="{{ request('production_date_search') }}"
                                   placeholder="Search date"
                                   class="w-full placeholder:text-gray-400 p-2 text-xs font-medium border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none"
                                   autocomplete="off">
                        </div>
                    </th>
                    <th class="p-2 border border-[#d9d9d9]">
                        <div class="relative">
                            <input type="text" name="sku_search" value="{{ request('sku_search') }}"
                                   placeholder="Search SKU"
                                   class="w-full placeholder:text-gray-400 p-2 text-xs font-medium border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none"
                                   autocomplete="off">
                        </div>
                    </th>
                    <th class="p-2 border border-[#d9d9d9]">
                        <div class="relative">
                            <input type="text" name="line_search" value="{{ request('line_search') }}"
                                   placeholder="Search line"
                                   class="w-full placeholder:text-gray-400 p-2 text-xs font-medium border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none"
                                   autocomplete="off">
                        </div>
                    </th>
                    <th class="p-2 border border-[#d9d9d9]">
                        <div class="relative">
                            <input type="text" name="output_search" value="{{ request('output_search') }}"
                                   placeholder="Search output"
                                   class="w-full placeholder:text-gray-400 p-2 text-xs font-medium border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none"
                                   autocomplete="off">
                        </div>
                    </th>
                    <th class="p-2 border border-[#d9d9d9]">
                        <div class="relative">
                            <input type="text" name="submitted_date_search" value="{{ request('submitted_date_search') }}"
                                   placeholder="Search submitted"
                                   class="w-full placeholder:text-gray-400 p-2 text-xs font-medium border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none"
                                   autocomplete="off">
                        </div>
                    </th>
                    <th class="p-2 border border-[#d9d9d9]">
                        <div class="relative">
                            <select name="status_search"
                                    class="w-full p-2 text-xs font-medium border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none bg-white"
                                    autocomplete="off">
                                <option value="">All Status</option>
                                <option value="Submitted" {{ request('status_search') === 'Submitted' ? 'selected' : '' }}>Submitted</option>
                                <option value="Reviewed" {{ request('status_search') === 'Reviewed' ? 'selected' : '' }}>Reviewed</option>
                                <option value="Validated" {{ request('status_search') === 'Validated' ? 'selected' : '' }}>Validated</option>
                            </select>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>
                {{-- Table Rows --}}
                @forelse ($reports as $report)
                    <tr onclick="window.location='{{ route('report.view', $report->id) }}'" class="bg-white border-b border-[#35408e] hover:bg-[#e5f4ff] cursor-pointer">
                        <td class="p-2 border border-[#d9d9d9] text-[#23527c] text-center">
                            {{-- Highlight search term for production_date --}}
                            @if(request('production_date_search'))
                                @php
                                    $displayDate = $report->production_date ? \Carbon\Carbon::parse($report->production_date)->format('F d, Y') : '-';
                                @endphp
                                {!! str_ireplace(request('production_date_search'), '<span class="bg-yellow-200">' . request('production_date_search') . '</span>', $displayDate) !!}
                            @else
                                {{ $report->production_date ? \Carbon\Carbon::parse($report->production_date)->format('F d, Y') : '-' }}
                            @endif
                        </td>
                        <td class="p-2 border border-[#d9d9d9] text-gray-600 text-center">
                            {{-- Highlight search term for SKU --}}
                            @if(request('sku_search'))
                                {!! str_ireplace(request('sku_search'), '<span class="bg-yellow-200">' . request('sku_search') . '</span>', $report->sku) !!}
                            @else
                                {{ $report->sku }}
                            @endif
                        </td>
                        <td class="p-2 border border-[#d9d9d9] text-gray-600 text-center">
                            {{-- Highlight search term for line --}}
                            @if(request('line_search'))
                                {!! str_ireplace(request('line_search'), '<span class="bg-yellow-200">' . request('line_search') . '</span>', $report->line) !!}
                            @else
                                {{ $report->line }}
                            @endif
                        </td>
                        <td class="p-2 border border-[#d9d9d9] text-gray-600 text-center">
                            {{-- Highlight search term for output --}}
                            @if(request('output_search'))
                                {!! str_ireplace(request('output_search'), '<span class="bg-yellow-200">' . request('output_search') . '</span>', $report->total_outputCase) !!}
                            @else
                                {{ $report->total_outputCase }}
                            @endif
                        </td>
                        <td class="p-2 border border-[#d9d9d9] text-gray-600 text-center">
                            {{-- Highlight search term for submitted date --}}
                            @if(request('submitted_date_search'))
                                @php
                                    $displaySubmittedDate = $report->created_at ? $report->created_at->format('F j, Y \a\t h:i A') : '-';
                                @endphp
                                {!! str_ireplace(request('submitted_date_search'), '<span class="bg-yellow-200">' . request('submitted_date_search') . '</span>', $displaySubmittedDate) !!}
                            @else
                                {{ $report->created_at ? $report->created_at->format('F j, Y \a\t h:i A') : '-' }}
                            @endif
                        </td>
                        <td class="p-2 border border-[#d9d9d9] text-gray-600 text-center">
                            @php
                                // Get latest status if exists
                                $status = $report->statuses->first()?->status;
                            @endphp
                            @if ($status)
                                <span class="inline-block p-1 text-xs font-medium
                                    @if($status === 'Submitted') bg-yellow-100 text-yellow-800
                                    @elseif($status === 'Reviewed') bg-blue-100 text-blue-800
                                    @elseif($status === 'Validated') bg-green-100 text-green-800
                                    @endif">
                                    {{-- Highlight search term for status --}}
                                    @if(request('status_search'))
                                        {!! str_ireplace(request('status_search'), '<span class="bg-yellow-200">' . request('status_search') . '</span>', $status) !!}
                                    @else
                                        {{ $status }}
                                    @endif
                                </span>
                            @else
                                <span class="text-gray-400 text-xs italic">N/A</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-2 border border-[#d9d9d9] text-gray-600 text-center">No matching records found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </form>

{{-- Entries Info + Pagination --}}
<div class="mt-4 flex flex-col md:flex-row items-center justify-between text-sm text-gray-600 gap-2">
    {{-- Entries Information --}}
    <div>
        @if($reports->total() > 0)
            Showing {{ $reports->firstItem() }} to {{ $reports->lastItem() }} of {{ $reports->total() }} entries
            @if($reports->total() < $totalReports)
                (filtered from {{ $totalReports }} total entries)
            @endif
        @else
            Showing 0 to 0 of 0 entries (filtered from {{ $totalReports }} total entries)
        @endif
    </div>

    {{-- Pagination --}}
    <div>
        {{ $reports->appends(request()->query())->links('pagination::tailwind') }}
    </div>
</div>


    {{-- JS for search --}}
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Submit search form on Enter key
        const searchInputs = document.querySelectorAll('input[name$="_search"]');
        const selectInputs = document.querySelectorAll('select[name$="_search"]');
        
        searchInputs.forEach(input => {
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('column-search-form').submit();
                }
            });
        });

        // Submit form immediately when select changes
        selectInputs.forEach(select => {
            select.addEventListener('change', function() {
                document.getElementById('column-search-form').submit();
            });
        });
    });
    </script>
@endsection