@extends('layouts.app')

@section('content')

{{-- Page Title --}}
<h2 class="text-xl mb-2 font-bold text-[#23527c]">Line Efficiency Report</h2>

{{-- Back to Configuration Link --}}
<a href="{{ url('analytics/index') }}" class="text-xs text-gray-500 hover:text-[#23527c] mb-4 inline-flex items-center">
    <x-icons-back-confi/>
    Analytics and Report
</a>

<div class="mx-16 mt-4">
<!-- Heading -->
<h2 class="flex items-center text-xl text-[#23527c] mb-2">
    <svg class="w-6 h-6 mr-2 flex-shrink-0" 
         viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg" fill="#23527c">
        <g id="SVGRepo_iconCarrier">
            <title>filter-horizontal</title>
            <g id="Layer_2" data-name="Layer 2">
                <g id="invisible_box" data-name="invisible box">
                    <rect width="48" height="48" fill="none"></rect>
                </g>
                <g id="icons_Q2" data-name="icons Q2">
                    <path d="M41.8,8H21.7A6.2,6.2,0,0,0,16,4a6,6,0,0,0-5.6,4H6.2A2.1,2.1,0,0,0,4,10a2.1,2.1,0,0,0,2.2,2h4.2A6,6,0,0,0,16,16a6.2,6.2,0,0,0,5.7-4H41.8A2.1,2.1,0,0,0,44,10,2.1,2.1,0,0,0,41.8,8ZM16,12a2,2,0,1,1,2-2A2,2,0,0,1,16,12Z"></path>
                    <path d="M41.8,22H37.7A6.2,6.2,0,0,0,32,18a6,6,0,0,0-5.6,4H6.2a2,2,0,1,0,0,4H26.4A6,6,0,0,0,32,30a6.2,6.2,0,0,0,5.7-4h4.1a2,2,0,1,0,0-4ZM32,26a2,2,0,1,1,2-2A2,2,0,0,1,32,26Z"></path>
                    <path d="M41.8,36H24.7A6.2,6.2,0,0,0,19,32a6,6,0,0,0-5.6,4H6.2a2,2,0,1,0,0,4h7.2A6,6,0,0,0,19,44a6.2,6.2,0,0,0,5.7-4H41.8a2,2,0,1,0,0-4ZM19,40a2,2,0,1,1,2-2A2,2,0,0,1,19,40Z"></path>
                </g>
            </g>
        </g>
    </svg>
    Select analytic report
</h2>

<!-- Divider -->
<div class="w-full flex items-center justify-center mb-6">
    <div class="w-full border-t border-[#E5E7EB]"></div>
</div>

<div class="flex flex-col mb-4 ml-10">
    <form method="GET" action="{{ url()->current() }}" class="flex flex-col gap-4 text-[#23527c] mb-4">
        <!-- Year Selection -->
        <div class="flex items-center gap-6 w-36">
            <label for="date" class="whitespace-nowrap text-sm font-bold">
                Production Year:<span class="text-red-500">*</span>
            </label>
            <x-select-year 
                name="date" 
                :options="collect($availableYears)->mapWithKeys(fn($v) => [$v => $v])->toArray()" 
                :selected="$year"
                class="w-28" />
        </div>

        <!-- Line Selection -->
        <div class="flex items-center gap-6 text-[#23527c]">
            <label class="whitespace-nowrap text-sm font-bold">
                Select Line:<span class="text-red-500">*</span>
            </label>
            <div class="flex gap-6 ml-8">
                @foreach($activeLines as $ln)
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" 
                               name="line" 
                               value="{{ $ln->line_number }}" 
                               {{ (string)$selectedLine === (string)$ln->line_number ? 'checked' : '' }}
                               class="w-4 h-4 rounded-full bg-gray-300 text-blue-600">
                        <span class="text-sm">Line {{ $ln->line_number }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Submit Button Section -->
        <div class="flex gap-4 mt-4">
            <a href="{{ route('analytics.index') }}"
               class="inline-flex items-center px-3 py-2 bg-[#5a9fd4] hover:bg-[#4a8bc2] text-white text-sm font-medium transition-colors duration-200 border border-[#5a9fd4] hover:border-[#4a8bc2]">
                <x-icons-back class="w-4 h-4 text-white" />
                Back
            </a>

            <button type="submit"
                    class="inline-flex items-center justify-center gap-1 bg-[#5bb75b] border border-[#43a143] text-white px-3 py-2 hover:bg-[#42a542] text-sm">
                <x-icons-submit class="w-4 h-4 text-white" />
                Submit
            </button>
        </div>
    </form>
</div>

@if($year && $selectedLine)
<div class="flex justify-between items-center mt-4">
    <h2 class="text-xl font-bold text-[#23527c]">
        Production Line {{ $selectedLine }} - Year {{ $year }}
    </h2>

<form action="{{ route('analytics.line.export_excel_annual') }}" method="GET" class="inline-block">
    <input type="hidden" name="line" value="{{ $selectedLine }}">
    <input type="hidden" name="date" value="{{ $year }}">
    <button type="submit"
        class="inline-flex items-center justify-center gap-1 p-2 bg-[#5bb75b] hover:bg-[#42a542] border border-[#42a542] text-white text-sm font-medium">
        <x-icons-pdf class="w-4 h-4" />
        Excel
    </button>
</form>


</div>

<!-- Divider -->
<div class="w-full flex items-center justify-center mt-2 mb-8">
    <div class="w-full border-t border-[#E5E7EB]"></div>
</div>


 {{-- Chart + Cards Layout --}}
      <div class="w-full flex flex-col xl:flex-row gap-4 mb-8">
        @include('analytics.line.partial.le_card')
        @include('analytics.line.partial.right_cards')
      </div>

      @include('analytics.line.partial.mtd_table')
      @include('analytics.line.partial.ptd_card')
      @include('analytics.line.partial.opl_toggle')
      @include('analytics.line.partial.epl_toggle')

    @else
      <div class="w-full inline-flex items-center gap-1 bg-[#5a9fd4] text-sm border border-[#4590ca] p-4 mt-4 text-white">
        <x-icons-warning />
        Please select a year and a production line, then click <b>Submit</b> to view analytics.
      </div>
    @endif
@endsection
