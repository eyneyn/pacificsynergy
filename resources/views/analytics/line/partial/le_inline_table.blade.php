<div class="overflow-x-auto mt-4">
  <table class="min-w-full text-[10px] border border-gray-300 text-gray-700 table-auto">
    <thead class="bg-[#f1f5f9] font-semibold text-gray-800 text-center">
      <tr>
        <th class="border border-gray-300 px-2 py-1 text-left w-[200px] whitespace-nowrap">
            Indicator
        </th>
        @foreach(range(1, 12) as $period)
          <th class="border border-gray-300 px-2 py-1 text-right whitespace-nowrap">P{{ $period }}</th>
        @endforeach
      </tr>
    </thead>
    <tbody class="text-[9px]">
            <!-- OPL -->
      <tr>
                        <td class="border border-gray-300 px-2 py-1  text-left whitespace-nowrap">
                  <span class="inline-flex items-center mr-2">
                      <span class="inline-block w-12 h-2 bg-[#8064A2]"></span>
                  </span>  
                  OPL, %
                </td>
        @foreach($ptdMonthlyRows as $row)
          <td class="border border-gray-300 px-2 py-1 text-right">{{ number_format($row['opl_percent'], 2) }}%</td>
        @endforeach
      </tr>

            <!-- EPL -->
      <tr>
<td class="border border-gray-300 px-2 py-1  text-left whitespace-nowrap">
                  <span class="inline-flex items-center mr-2">
                      <span class="inline-block w-12 h-2 bg-[#4BACC6]"></span>
                  </span> 
                  EPL, %
                </td>        @foreach($ptdMonthlyRows as $row)
          <td class="border border-gray-300 px-2 py-1 text-right">{{ number_format($row['epl_percent'], 2) }}%</td>
        @endforeach
      </tr>

      <!-- Target LE -->
      <tr>
                <td class="border border-gray-300 px-2 py-1  text-left whitespace-nowrap">
                    <span class="inline-flex items-center mr-2">
                        <span class="h-[2px] w-4 bg-[#D9D9D9]"></span>
                        <span class="inline-block w-2 h-2 rounded-full bg-[#98B954] mx-1"></span>
                        <span class="h-[2px] w-4 bg-[#D9D9D9]"></span>
                    </span>
                    Target LE, %</td>
        @foreach(range(1, 12) as $i)
          <td class="border border-gray-300 px-2 py-1 text-right">80%</td>
        @endforeach
      </tr>

      <!-- LE -->
      <tr>
                <td class="border border-gray-300 px-2 py-1  text-left whitespace-nowrap">
                    <span class="inline-flex items-center mr-2">
                        <span class="h-[2px] w-4 bg-[#4F81BD]"></span>
                        <span class="inline-block w-2 h-2 rounded-full bg-[#7D60A0] mx-1"></span>
                        <span class="h-[2px] w-4 bg-[#4F81BD]"></span>
                    </span>
                    LE, %
                </td>        @foreach($ptdMonthlyRows as $row)
          <td class="border border-gray-300 px-2 py-1 text-right">{{ number_format($row['le'], 2) }}%</td>
        @endforeach
      </tr>
    </tbody>
  </table>
</div>
