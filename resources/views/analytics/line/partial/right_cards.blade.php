<div class="w-full xl:w-1/4 flex flex-col space-y-4">
  {{-- Efficiency Summary --}}
  <div class="col-span-1 sm:col-span-2 bg-white hover:bg-[#e5f4ff] rounded-xl shadow border border-gray-200 p-3 h-28 flex items-center justify-center">
    <div class="text-center">
      <h3 class="text-sm text-[#2d326b] mb-1">YTD Line Efficiency:</h3>
      <p class="text-lg font-semibold text-[#4b5563]">{{ number_format($ptdTotalsRow['le'], 2) }}%</p>
    </div>
  </div>

  {{-- Month Buttons --}}
  <div class="col-span-1 sm:col-span-2 bg-white rounded-xl shadow border border-gray-200 p-4">
    <h3 class="text-sm text-[#2d326b] text-center mb-3 font-semibold">Select Month</h3>
    <div class="grid grid-cols-3 gap-2">
      @foreach ([
        'January','February','March','April','May','June',
        'July','August','September','October','November','December'
      ] as $monthName)
        <a href="{{ route('analytics.line.monthly_report', [
              'month' => $monthName,
              'line'  => request('line'),
              'date'  => request('date')
            ]) }}"
           class="text-xs text-[#2d326b] text-center border border-gray-300 rounded-md py-1 px-2 hover:bg-[#2d326b] hover:text-white transition duration-150">
          {{ $monthName }}
        </a>
      @endforeach
    </div>
  </div>
</div>
