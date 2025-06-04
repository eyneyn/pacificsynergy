@extends('layouts.app')

@section('content')
    {{-- Page Title --}}
    <h2 class="text-2xl font-bold text-[#2d326b]">Production Report</h2>

    {{-- Search Bar and Add Button --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mt-5 mb-6 gap-4">
        <form method="GET" action="{{ route('report.index') }}">
            <div class="w-full max-w-xs px-4 border border-[#d9d9d9] rounded-md shadow-md transition-all duration-200 hover:shadow-lg hover:border-[#2d326b]">
                <div class="flex items-center flex-grow">
                    {{-- Search Icon --}}
                    <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M16.65 16.65A7.5 7.5 0 1 0 3 10a7.5 7.5 0 0 0 13.65 6.65z" />
                    </svg>
                    {{-- Search Input --}}
                    <input type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search production report"
                        class="w-full border-none text-sm text-gray-700 placeholder-gray-400"/>
                </div>
            </div>
        </form>
        {{-- Add Report Button --}}
        <a href="{{ url('report/add') }}"
            class="inline-flex items-center gap-2 p-3 py-2 bg-[#323B76] hover:bg-[#444d90] border border-[#323B76] text-white text-sm font-medium rounded-md">
            <x-icons-plus-circle class="w-4 h-4 text-white" />
            <span class="text-sm">Production Report</span>
        </a>
    </div>

    @php
        $currentSort = request('sort');
        $currentDirection = request('direction') === 'asc' ? 'desc' : 'asc';
    @endphp

    {{-- Report Table --}}
    <table class="w-full mt-4 text-sm text-left rtl:text-right border border-[#E5E7EB] border-collapse">
        <thead class="text-xs text-white uppercase bg-[#35408e]">
            <tr>
                {{-- Sortable Table Headers --}}
                <th class="px-6 py-2 border border-[#d9d9d9]">
                    <a href="{{ route('report.index', ['sort' => 'production_date', 'direction' => ($currentSort === 'production_date' ? $currentDirection : 'asc')]) }}"
                        class="flex items-center gap-1 text-white no-underline">
                        Machine / Others
                        <svg class="w-4 h-4 {{ $currentSort === 'name' ? 'opacity-100' : 'opacity-50' }}" fill="currentColor" viewBox="0 0 512 512">
                            <path d="M304 96h48v320h48l-72 80-72-80h48V96zM64 192h128v32H64v-32zm32 64h96v32H96v-32zm32 64h64v32h-64v-32zm32 64h32v32h-32v-32z"/>
                        </svg>
                    </a>
                </th>
                <th class="px-6 py-2 border border-[#d9d9d9] text-center">
                    <a href="{{ route('report.index', ['sort' => 'sku', 'direction' => ($currentSort === 'sku' ? $currentDirection : 'asc')]) }}"
                        class="flex justify-center items-center gap-1 text-white no-underline">
                        SKU
                        <svg class="w-4 h-4 {{ $currentSort === 'name' ? 'opacity-100' : 'opacity-50' }}" fill="currentColor" viewBox="0 0 512 512">
                            <path d="M304 96h48v320h48l-72 80-72-80h48V96zM64 192h128v32H64v-32zm32 64h96v32H96v-32zm32 64h64v32h-64v-32zm32 64h32v32h-32v-32z"/>
                        </svg>
                    </a>
                </th>
                <th class="px-6 py-2 border border-[#d9d9d9] text-center">
                    <a href="{{ route('report.index', ['sort' => 'line', 'direction' => ($currentSort === 'line' ? $currentDirection : 'asc')]) }}"
                        class="flex justify-center items-center gap-1 text-white no-underline">
                        Line
                        <svg class="w-4 h-4 {{ $currentSort === 'name' ? 'opacity-100' : 'opacity-50' }}" fill="currentColor" viewBox="0 0 512 512">
                            <path d="M304 96h48v320h48l-72 80-72-80h48V96zM64 192h128v32H64v-32zm32 64h96v32H96v-32zm32 64h64v32h-64v-32zm32 64h32v32h-32v-32z"/>
                        </svg>
                    </a>
                </th>
                <th class="px-6 py-2 border border-[#d9d9d9] text-center">
                    <a href="{{ route('report.index', ['sort' => 'total_outputCase', 'direction' => ($currentSort === 'total_outputCase' ? $currentDirection : 'asc')]) }}"
                        class="flex justify-center items-center gap-1 text-white no-underline">
                        Total Output Case
                        <svg class="w-4 h-4 {{ $currentSort === 'name' ? 'opacity-100' : 'opacity-50' }}" fill="currentColor" viewBox="0 0 512 512">
                            <path d="M304 96h48v320h48l-72 80-72-80h48V96zM64 192h128v32H64v-32zm32 64h96v32H96v-32zm32 64h64v32h-64v-32zm32 64h32v32h-32v-32z"/>
                        </svg>
                    </a>
                </th>
                <th class="px-6 py-2 border border-[#d9d9d9] text-center">
                    <a href="{{ route('report.index', ['sort' => 'created_at', 'direction' => ($currentSort === 'created_at' ? $currentDirection : 'asc')]) }}"
                        class="flex justify-center items-center gap-1 text-white no-underline">
                        Submitted Date and Time
                        <svg class="w-4 h-4 {{ $currentSort === 'name' ? 'opacity-100' : 'opacity-50' }}" fill="currentColor" viewBox="0 0 512 512">
                            <path d="M304 96h48v320h48l-72 80-72-80h48V96zM64 192h128v32H64v-32zm32 64h96v32H96v-32zm32 64h64v32h-64v-32zm32 64h32v32h-32v-32z"/>
                        </svg>
                    </a>
                </th>
                <th class="px-6 py-2 border border-[#d9d9d9] text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            {{-- Table Rows --}}
            @forelse ($reports as $report)
                <tr onclick="window.location='{{ route('report.view', $report->id) }}'" class="bg-white hover:bg-gray-50 cursor-pointer">
                    <td class="px-6 py-2 border border-[#d9d9d9] text-[#2d326b]">{{ $report->production_date }}</td>
                    <td class="px-6 py-2 border border-[#d9d9d9] text-gray-600 text-center">{{ $report->sku }}</td>
                    <td class="px-6 py-2 border border-[#d9d9d9] text-gray-600 text-center">{{ $report->line }}</td>
                    <td class="px-6 py-2 border border-[#d9d9d9] text-gray-600 text-center">{{ $report->total_outputCase }}</td>
                    <td class="px-6 py-2 border border-[#d9d9d9] text-gray-600 text-center">{{ $report->created_at }}</td>
                    <td class="px-6 py-2 border border-[#d9d9d9] text-gray-600 text-center"></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-2 border border-[#E5E7EB] text-center text-[#35408e]">No report entries found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $reports->links('pagination::tailwind') }}
    </div>
@endsection
