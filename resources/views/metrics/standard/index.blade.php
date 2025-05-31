@extends('layouts.app')

@section('content')

    <div class="mb-5 gap-4 md:gap-0 "> 
        <h2 class="text-2xl font-bold text-[#2d326b]">Product Standard</h2>
    </div>

    <div class="flex flex-col md:flex-row md:items-center justify-between">
          <!-- Search bar with filter -->
          <form method="GET" action="{{ route('metrics.standard.index') }}">
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
          <a href="{{url('metrics/standard/add')}}"
            class="inline-flex items-center gap-2 p-3 py-2 bg-[#323B76] hover:bg-[#444d90] border border-[#323B76] text-white text-sm font-medium rounded-md">
                <x-icons-plus-circle class="w-4 h-4 text-white" />
                <span class="text-sm">Product Standard</span>
          </a>
    </div>

      <!-- Standards Table -->
        <table class="w-full mt-4 text-sm text-left rtl:text-right border border-[#E5E7EB] border-collapse">
            <thead class="text-xs text-white uppercase bg-[#35408e]">
              <tr>
                  <th class="px-6 py-2 border border-[#d9d9d9]">Description</th>
                  <th class="px-6 py-2 border border-[#d9d9d9] text-center">Size</th>
                  <th class="px-6 py-2 border border-[#d9d9d9] text-center">Bottles per Case</th>
                  <th class="px-6 py-2 border border-[#d9d9d9] text-center">Mat No</th>
                  <th class="px-6 py-2 border border-[#d9d9d9] text-center">Group</th>
                  <th class="px-6 py-2 border border-[#d9d9d9] text-center">Preform Weight</th>
                  <th class="px-6 py-2 border border-[#d9d9d9] text-center">LDPE Size</th>
              </tr>
          </thead>
          <tbody>
              @forelse ($standards as $standard)
                  <tr 
                      onclick="window.location='{{ route('metrics.standard.view', $standard) }}'" 
                      class="bg-white border-b border-[#35408e] hover:bg-[#e5f4ff] cursor-pointer"
                  >
                      <td class="px-6 py-2 border border-[#d9d9d9] text-[#2d326b]">{{ $standard->description }}</td>
                      <td class="px-6 py-2 border border-[#d9d9d9] text-center">{{ $standard->size }}</td>
                      <td class="px-6 py-2 border border-[#d9d9d9] text-center">{{ $standard->bottles_per_case }}</td>
                      <td class="px-6 py-2 border border-[#d9d9d9] text-center">{{ $standard->mat_no }}</td>
                      <td class="px-6 py-2 border border-[#d9d9d9] text-center">{{ ucfirst($standard->group) }}</td>
                      <td class="px-6 py-2 border border-[#d9d9d9] text-center">{{ $standard->preform_weight }}</td>
                      <td class="px-6 py-2 border border-[#d9d9d9] text-center">{{ $standard->ldpe_size }}</td>
                  </tr>
              @empty
                  <tr>
                      <td colspan="7" class="px-6 py-4 border border-[#E5E7EB] text-center text-[#35408e]">No standard entries found.</td>
                  </tr>
              @endforelse
          </tbody>
      </table>

      <!-- Pagination -->
      <div class="mt-6">
          {{ $standards->appends(request()->query())->links('pagination::tailwind') }}
      </div>

<x-delete-modal />
</div>

    @endsection