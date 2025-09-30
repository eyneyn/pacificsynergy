<div class="w-full mb-8 cursor-pointer bg-white rounded-sm border border-gray-200 p-6 shadow-md hover:shadow-xl hover:border-[#E5E7EB]">
  <div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold mb-4 text-[#23527c]">Month-To-Date Report</h2>
  </div>

  <div class="space-y-4 mt-2">
    <table class="w-full text-xs text-left border border-[#E5E7EB] border-collapse">
      <thead class="text-xs text-white uppercase bg-[#35408e]">
        <tr>
          <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#0070C0]"></th>
          @foreach(range(1, 12) as $i)
            <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#0070C0]">P{{ $i }}</th>
          @endforeach
          <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#0070C0]">YTD</th>
        </tr>
      </thead>
      <tbody>
        <!-- Target LE -->
        <tr >
          <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#0070C0] text-white">Target LE, %</td>
          @foreach(range(1, 12) as $i)
            <td class="border border-[#ffffff] bg-[#F2F2F2] p-2 text-center">80%</td>
          @endforeach
          <td class="border border-[#ffffff] bg-[#F2F2F2] p-2 text-center"></td>
        </tr>

        <!-- Actual LE -->
        <tr>
          <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#0070C0] text-white">LE, %</td>
          @foreach($ptdMonthlyRows as $row)
            <td class="border border-[#ffffff] bg-[#F2F2F2] p-2 text-center">{{ number_format($row['le'], 2) }}%</td>
          @endforeach
          <td class="border border-[#ffffff] bg-[#F2F2F2] p-2 text-center">{{ number_format($ptdTotalsRow['le'], 2) }}%</td>
        </tr>

        <!-- OPL % -->
        <tr >
          <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#0070C0] text-white">OPL, %</td>
          @foreach($ptdMonthlyRows as $row)
            <td class="border border-[#ffffff] bg-[#F2F2F2] p-2 text-center">{{ number_format($row['opl_percent'], 2) }}%</td>
          @endforeach
          <td class="border border-[#ffffff] bg-[#F2F2F2] p-2 text-center">{{ number_format($ptdTotalsRow['opl_percent'], 2) }}%</td>
        </tr>

        <!-- EPL % -->
        <tr >
          <td class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#0070C0] text-white">EPL, %</td>
          @foreach($ptdMonthlyRows as $row)
            <td class="border border-[#ffffff] bg-[#F2F2F2] p-2 text-center">{{ number_format($row['epl_percent'], 2) }}%</td>
          @endforeach
          <td class="border border-[#ffffff] bg-[#F2F2F2] p-2 text-center">{{ number_format($ptdTotalsRow['epl_percent'], 2) }}%</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
