@extends('layouts.app')
@section('title', content: 'Standard')
@section('content')

{{-- Page Title --}}
<h2 class="text-xl mb-2 font-bold text-[#23527c]">List of Product Standard</h2>

{{-- Back to Configuration Link --}}
<a href="{{ url('configuration/index') }}" class="text-xs text-gray-500 hover:text-[#23527c] mb-4 inline-flex items-center">
    <x-icons-back-confi/>
    Configuration
</a>

{{-- Action Buttons --}}
<div class="flex flex-col md:flex-row gap-2 mb-4">
    <!-- Back Button -->
    <a href="{{ url('configuration/index') }}" 
       class="inline-flex items-center px-3 py-2 bg-[#5a9fd4] hover:bg-[#4a8bc2] text-white text-sm font-medium transition-colors duration-200 border border-[#4590ca] hover:border-[#4a8bc2]">
        <x-icons-back class="w-2 h-2 text-white" />
        Back
    </a>
    <!-- Add Product Standard Button -->
    <a href="{{ url('configuration/standard/add') }}"
       class="inline-flex items-center justify-center gap-1 p-2 bg-[#323B76] hover:bg-[#444d90] border border-[#323B76] text-white text-sm font-medium">
        <x-icons-plus-circle class="w-2 h-2 text-white" />
        <span class="text-sm">Standard</span>
    </a>
</div>

{{-- Search Bar and Pagination --}}
<div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-6">
    <!-- Search bar with filter -->
    <form method="GET" action="{{ route('configuration.standard.index') }}" class="flex-1 md:max-w-md lg:max-w-lg">
        <div class="px-4 border border-[#d9d9d9] shadow-md focus-within:border-blue-500 focus-within:shadow-lg focus-within:outline-none">
            <div class="flex items-center flex-grow">
                <x-icons-search/>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search products"
                    class="w-full border-none text-sm text-gray-700 placeholder-gray-400"
                />
            </div>
        </div>
    </form>
    <!-- Pagination -->
    <div class="flex-shrink-0">
        {{ $standards->appends(request()->query())->links('pagination::tailwind') }}
    </div>
</div>

{{-- Standards Table --}}
<form id="column-filter-form" method="GET" action="{{ route('configuration.standard.index') }}">
    {{-- Preserve existing sort parameters --}}
    @if(request('sort'))
        <input type="hidden" name="sort" value="{{ request('sort') }}">
    @endif
    @if(request('direction'))
        <input type="hidden" name="direction" value="{{ request('direction') }}">
    @endif

    <table class="w-full text-sm text-left border border-[#E5E7EB] border-collapse shadow-sm">
        <thead>
            {{-- Table Header --}}
            <tr class="text-xs text-white uppercase bg-[#35408e]">
                <th class="p-2 border border-[#d9d9d9] text-center w-[20%]">
                    <x-table-sort-link 
                        field="description" 
                        label="Description" 
                        :currentSort="$currentSort ?? null" 
                        :currentDirection="$currentDirection ?? null"
                        route="configuration.standard.index"
                    />
                </th>
                <th class="p-2 border border-[#d9d9d9] text-center w-[10%]">
                    <x-table-sort-link 
                        field="size" 
                        label="Size" 
                        :currentSort="$currentSort ?? null" 
                        :currentDirection="$currentDirection ?? null"
                        route="configuration.standard.index"
                    />
                </th>
                <th class="p-2 border border-[#d9d9d9] text-center w-[20%]">
                    <x-table-sort-link 
                        field="bottles_per_case" 
                        label="Bottles per Case" 
                        :currentSort="$currentSort ?? null" 
                        :currentDirection="$currentDirection ?? null"
                        route="configuration.standard.index"
                    />
                </th>
                <th class="p-2 border border-[#d9d9d9] text-center w-[10%]">Mat No</th>
                <th class="p-2 border border-[#d9d9d9] text-center w-[10%]">Group</th>
                <th class="p-2 border border-[#d9d9d9] text-center w-[20%]">Preform Weight</th>
                <th class="p-2 border border-[#d9d9d9] text-center w-[10%]">LDPE Size</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($standards as $standard)
                <tr onclick="window.location='{{ route('configuration.standard.view', $standard) }}'"
                    class="bg-white border-b border-[#35408e] hover:bg-[#e5f4ff] cursor-pointer">
                    <td class="p-2 border border-[#d9d9d9] text-[#23527c] text-center">{{ $standard->description }}</td>
                    <td class="p-2 border border-[#d9d9d9] text-gray-600 text-center">{{ $standard->size }}</td>
                    <td class="p-2 border border-[#d9d9d9] text-gray-600 text-center">{{ $standard->bottles_per_case }}</td>
                    <td class="p-2 border border-[#d9d9d9] text-gray-600 text-center">{{ $standard->mat_no }}</td>
                    <td class="p-2 border border-[#d9d9d9] text-gray-600 text-center">{{ ucfirst($standard->group) }}</td>
                    <td class="p-2 border border-[#d9d9d9] text-gray-600 text-center">{{ $standard->preform_weight }}</td>
                    <td class="p-2 border border-[#d9d9d9] text-gray-600 text-center">{{ $standard->ldpe_size }}</td>
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
        @if($standards->total() > 0)
            Showing {{ $standards->firstItem() }} to {{ $standards->lastItem() }} of {{ $standards->total() }} entries
            @if($standards->total() < $totalStandards)
                (filtered from {{ $totalStandards }} total entries)
            @endif
        @else
            Showing 0 to 0 of 0 entries (filtered from {{ $totalStandards }} total entries)
        @endif
    </div>

    {{-- Pagination --}}
    <div>
        {{ $standards->appends(request()->query())->links('pagination::tailwind') }}
    </div>
</div>
{{-- Delete Modal Component --}}
<x-delete-modal />

@endsection