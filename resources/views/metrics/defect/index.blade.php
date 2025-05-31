@extends('layouts.app')

@section('content')

    <!-- Back Button -->
    <a href="{{url('metrics/configuration')}}" class="flex items-center text-sm text-gray-500 hover:text-[#2d326b] mb-4">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        Configuration
    </a>
    <h2 class="text-2xl font-bold text-[#2d326b]">Types of Defect</h2>

    <div class="flex flex-col md:flex-row md:items-center justify-between mt-5 mb-6 gap-4">
            <!-- Search bar with filter -->
            <form method="GET" action="{{ route('metrics.defect.index') }}" class="">
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
                            placeholder="Search products"
                            class="w-full border-none text-sm text-gray-700 placeholder-gray-400"/>
                    </div>
                </div>
            </form>

            <a href="{{url('metrics/defect/add')}}"
              class="inline-flex items-center gap-2 p-3 py-2 bg-[#323B76] hover:bg-[#444d90] border border-[#323B76] text-white text-sm font-medium rounded-md">
                  <x-icons-plus-circle class="w-4 h-4 text-white" />
                  <span class="text-sm">Defect</span>
            </a>
    </div>

        <!-- Defect Table -->
        <table class="w-full mt-4 text-sm text-left rtl:text-right border border-[#E5E7EB] border-collapse">
            <thead class="text-xs text-white uppercase bg-[#35408e]">
                <tr>
                    <th class="px-6 py-2 border border-[#d9d9d9]">Defect Name</th>
                    <th class="px-6 py-2 border border-[#d9d9d9] text-center">Category</th>
                    <th class="px-6 py-2 border border-[#d9d9d9] text-center">Description</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($defects as $defect)
                  <tr 
                      onclick="window.location='{{ route('metrics.defect.view', $defect) }}'" 
                      class="bg-white border-b border-[#35408e] hover:bg-[#e5f4ff] cursor-pointer"
                  >
                    <td class="px-6 py-2 border border-[#d9d9d9] text-[#2d326b]">{{ $defect->defect_name }}</td>
                    <td class="px-6 py-2 border border-[#d9d9d9] text-center">{{ $defect->category }}</td>
                    <td class="px-6 py-2 border border-[#d9d9d9] text-center">{{ $defect->description }}</td>
                  </tr>
                @empty
                  <tr>
                      <td colspan="3" class="px-6 py-4 border border-[#E5E7EB] text-center text-[#35408e]">No defect entries found.</td>
                  </tr>
              @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $defects->appends(request()->query())->links('pagination::tailwind') }}
        </div>

<x-delete-modal />

@endsection