@extends('layouts.app')

@section('content')


{{-- Page Title --}}
<h2 class="text-xl mb-2 font-bold text-[#23527c]"> Report</h2>

<a href="{{ route('analytics.material.index', ['line' => request('line'), 'date' => request('date', now()->year)]) }}"
   class="text-xs text-gray-500 hover:text-[#23527c] mb-4 inline-flex items-center">
    <x-icons-back-confi/>
    Analytics and Report
</a>

<div class="mx-16 mt-4">
<!-- Heading -->
<h2 class="flex items-center text-xl text-[#23527c] mb-2 font-bold">
    <svg class="w-6 h-6 mr-2 flex-shrink-0" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 15.315 15.315" xml:space="preserve" fill="#23527c"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <g> <path style="fill:#23527c;" d="M3.669,3.71h0.696c0.256,0,0.464-0.165,0.464-0.367V0.367C4.829,0.164,4.621,0,4.365,0H3.669 C3.414,0,3.206,0.164,3.206,0.367v2.976C3.205,3.545,3.413,3.71,3.669,3.71z"></path> <path style="fill:#23527c;" d="M10.95,3.71h0.696c0.256,0,0.464-0.165,0.464-0.367V0.367C12.11,0.164,11.902,0,11.646,0H10.95 c-0.256,0-0.463,0.164-0.463,0.367v2.976C10.487,3.545,10.694,3.71,10.95,3.71z"></path> <path style="fill:#23527c;" d="M14.512,1.42h-1.846v2.278c0,0.509-0.458,0.923-1.021,0.923h-0.696 c-0.563,0-1.021-0.414-1.021-0.923V1.42H5.384v2.278c0,0.509-0.458,0.923-1.021,0.923H3.669c-0.562,0-1.02-0.414-1.02-0.923V1.42 H0.803c-0.307,0-0.557,0.25-0.557,0.557V14.76c0,0.307,0.25,0.555,0.557,0.555h13.709c0.308,0,0.557-0.248,0.557-0.555V1.977 C15.069,1.67,14.82,1.42,14.512,1.42z M14.316,9.49v4.349c0,0.096-0.078,0.176-0.175,0.176H7.457H1.174 c-0.097,0-0.175-0.08-0.175-0.176V10.31V5.961c0-0.096,0.078-0.176,0.175-0.176h6.683h6.284l0,0c0.097,0,0.175,0.08,0.175,0.176 V9.49z"></path> <rect x="2.327" y="8.93" style="fill:#23527c;" width="1.735" height="1.736"></rect> <rect x="5.28" y="8.93" style="fill:#23527c;" width="1.735" height="1.736"></rect> <rect x="8.204" y="8.93" style="fill:#23527c;" width="1.734" height="1.736"></rect> <rect x="11.156" y="8.93" style="fill:#23527c;" width="1.736" height="1.736"></rect> <rect x="2.363" y="11.432" style="fill:#23527c;" width="1.736" height="1.736"></rect> <rect x="5.317" y="11.432" style="fill:#23527c;" width="1.735" height="1.736"></rect> <rect x="8.241" y="11.432" style="fill:#23527c;" width="1.734" height="1.736"></rect> <rect x="11.194" y="11.432" style="fill:#23527c;" width="1.735" height="1.736"></rect> <rect x="8.215" y="6.47" style="fill:#23527c;" width="1.735" height="1.735"></rect> <rect x="11.17" y="6.47" style="fill:#23527c;" width="1.734" height="1.735"></rect> </g> </g> </g></svg>
    {{ $monthName }} {{ $year }} (Line {{ $line }})
</h2>


<!-- Divider -->
<div class="w-full flex items-center justify-center mb-6">
    <div class="w-full border-t border-[#E5E7EB]"></div>
</div>


<div class="flex flex-col md:flex-row gap-2 mb-8">

    <a href="{{ route('analytics.material.index', ['line' => request('line'), 'date' => request('date', now()->year)]) }}"
        class="inline-flex items-center px-3 py-2 bg-[#5a9fd4] hover:bg-[#4a8bc2] text-white text-sm font-medium transition-colors duration-200 border border-[#5a9fd4] hover:border-[#4a8bc2]">
        <x-icons-back class="w-4 h-4 text-white" />
        Back
    </a>


<form action="{{ route('analytics.material.export_excel') }}" method="GET" class="inline-block">
    <input type="hidden" name="line" value="{{ $line }}">
    <input type="hidden" name="month" value="{{ $monthNumber }}"> {{-- ✅ numeric month --}}
    <input type="hidden" name="date" value="{{ $year }}">

    <button type="submit"
        class="inline-flex items-center justify-center gap-1 p-2 bg-[#5bb75b] hover:bg-[#42a542] border border-[#42a542] text-white text-sm font-medium">
        <x-icons-pdf class="w-4 h-4" />
        Excel
    </button>
</form>

</div>


          {{-- Tabs wrapper (Alpine state lives here) --}}
    <div x-data="{ activeTab: '{{ $activeTab }}' }" class="w-full mb-6">
        {{-- Tabs navigation --}}
        @include('analytics.material.partial.tabs-nav')

        {{-- Tab: Overview (Chart + daily table) --}}
        @include('analytics.material.partial.tab-overview', [
            'analytics' => $analytics,
        ])

        {{-- Tab: Production (Detailed production table) --}}
        @include('analytics.material.partial.tab-production', [
            'analytics' => $analytics,
        ])
    </div>



@endsection
