@extends('layouts.app')
@section('title', content: 'Defect')
@section('content')

{{-- Page Title --}}
<h2 class="text-xl mb-2 font-bold text-[#23527c]">List of Defects</h2>

{{-- Back to Configuration Link --}}
<a href="{{ url('configuration/index') }}" class="text-xs text-gray-500 hover:text-[#23527c] mb-4 inline-flex items-center">
    <x-icons-back-confi/>
    Configuration
</a>

{{-- 🔔 Modal Alerts (Success, Error, Validation) --}}
<x-alert-message />

{{-- Top Controls: Back, Add Defect, Show Entries --}}
<div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-4">
    <div class="flex flex-col md:flex-row gap-2">
        {{-- Back Button --}}
        <a href="{{ url('configuration/index') }}" class="inline-flex items-center px-3 py-2 bg-[#5a9fd4] hover:bg-[#4a8bc2] text-white text-sm font-medium transition-colors duration-200 border border-[#4590ca] hover:border-[#4a8bc2]">
            <x-icons-back class="w-2 h-2 text-white" />
            Back
        </a>
        {{-- Add Defect Button --}}
        <a href="{{ url('configuration/defect/add') }}" class="inline-flex items-center justify-center gap-1 p-2 bg-[#323B76] hover:bg-[#444d90] border border-[#323B76] text-white text-sm font-medium">
            <x-icons-plus-circle class="w-2 h-2 text-white" />
            <span class="text-sm">Defect</span>
        </a>
        {{-- Show Entries Dropdown --}}
        <div class="inline-flex items-center gap-2 text-sm text-gray-600">
            <span>Show</span>
            <form id="per-page-form" method="GET" action="{{ route('configuration.defect.index') }}">
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
        {{ $defects->appends(request()->query())->links('pagination::tailwind') }}
    </div>
</div>

{{-- Search & Table --}}
<form id="column-search-form" method="GET" action="{{ route('configuration.defect.index') }}">
    {{-- Preserve existing sort parameters --}}
    @if(request('sort'))
        <input type="hidden" name="sort" value="{{ request('sort') }}">
    @endif
    @if(request('direction'))
        <input type="hidden" name="direction" value="{{ request('direction') }}">
    @endif

    {{-- Defect Table --}}
    <table class="w-full table-fixed text-sm text-left border border-[#E5E7EB] border-collapse shadow-sm">
        <thead>
            {{-- Main Header Row --}}
            <tr class="text-xs text-white uppercase bg-[#35408e]">
                <th class="p-2 border border-[#d9d9d9] text-center w-[20%]">
                    <x-table-sort-link 
                        field="defect_name" 
                        label="Defect Name" 
                        :currentSort="$currentSort ?? null" 
                        :currentDirection="$currentDirection ?? null"
                        route="configuration.defect.index"
                    />
                </th>
                <th class="p-2 border border-[#d9d9d9] text-center w-[20%]">
                    <x-table-sort-link 
                        field="category" 
                        label="Category" 
                        :currentSort="$currentSort ?? null" 
                        :currentDirection="$currentDirection ?? null"
                        route="configuration.defect.index"
                    />
                </th>
                <th class="p-2 border border-[#d9d9d9] text-center w-[60%]">
                    Description
                </th>
            </tr>

            {{-- Search Input Row --}}
            <tr>
                <th class="p-2 border border-[#d9d9d9] w-[20%]">
                    <input type="text" name="defect_name_search" value="{{ request('defect_name_search') }}"
                        placeholder="Search defect"
                        class="w-full placeholder:text-gray-400 p-2 text-xs font-medium border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none"
                        autocomplete="off">
                </th>
                <th class="p-2 border border-[#d9d9d9] w-[20%]">
                    <input type="text" name="category_search" value="{{ request('category_search') }}"
                        placeholder="Search category"
                        class="w-full placeholder:text-gray-400 p-2 text-xs font-medium border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none"
                        autocomplete="off">
                </th>
                <th class="p-2 border border-[#d9d9d9] w-[60%]">
                    <input type="text" name="description_search" value="{{ request('description_search') }}"
                        placeholder="Search description"
                        class="w-full placeholder:text-gray-400 p-2 text-xs font-medium border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none"
                        autocomplete="off">
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse ($defects as $defect)
                <tr onclick="window.location='{{ route('configuration.defect.view', $defect) }}'" 
                    class="bg-white border-b border-gray-200 hover:bg-[#e5f4ff] transition-colors duration-200 cursor-pointer">
                    <td class="p-2 border border-[#d9d9d9] text-[#23527c] text-center w-[20%] truncate">
                        {{ $defect->defect_name }}
                    </td>
                    <td class="p-2 border border-[#d9d9d9] text-gray-600 text-center w-[20%] truncate">
                        {{ $defect->category }}
                    </td>
                    <td class="p-2 border border-[#d9d9d9] text-gray-600 text-center w-[60%] truncate">
                        {{ $defect->description }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="p-2 border border-[#d9d9d9] text-gray-600 text-center">
                        No matching records found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</form>
{{-- Entries Info + Pagination --}}
<div class="mt-4 flex flex-col md:flex-row items-center justify-between text-sm text-gray-600 gap-2">
    {{-- Entries Information --}}
    <div>
        @if($defects->total() > 0)
            Showing {{ $defects->firstItem() }} to {{ $defects->lastItem() }} of {{ $defects->total() }} entries
            @if($defects->total() < $totalDefects)
                (filtered from {{ $totalDefects }} total entries)
            @endif
        @else
            Showing 0 to 0 of 0 entries (filtered from {{ $totalDefects }} total entries)
        @endif
    </div>

    {{-- Pagination --}}
    <div>
        {{ $defects->appends(request()->query())->links('pagination::tailwind') }}
    </div>
</div>
{{-- Delete Modal Component --}}
<x-delete-modal />

{{-- JS for search and clear --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Submit search form on Enter key
    const searchInputs = document.querySelectorAll('input[name$="_search"]');
    searchInputs.forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('column-search-form').submit();
            }
        });
    });
});

// Clear search field and submit form
function clearSearch(fieldName) {
    let input = document.querySelector(`input[name="${fieldName}"]`);
    if (input) {
        input.value = "";
        let form = input.closest("form");
        if (form) {
            form.submit();
        }
    }
}
</script>

@endsection
