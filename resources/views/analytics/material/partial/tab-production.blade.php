{{-- Production content --}}
<div x-show="activeTab === 'production'"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-100 transform scale-100"
     class="mt-6">
    <div class="w-full mb-4 bg-white rounded-sm border border-gray-300 p-6 shadow-xl">
        <p class="text-lg text-[#23527c] font-semibold">Production Report</p>

        {{-- Wide table wrapper --}}
        <div class="mt-5 overflow-x-auto">
            <table class="min-w-[1600px] text-xs text-left border-collapse">
                <thead class="text-[8px] text-white uppercase">
                    {{-- Title row --}}
                    <tr>
                        <th colspan="6" class="p-2 border border-[#F2F2F2] text-center bg-[#0070C0]"></th>
                        <th colspan="5" class="p-2 border border-[#F2F2F2] bg-[#808080] text-center">Preforms</th>
                        <th colspan="5" class="p-2 border border-[#F2F2F2] bg-[#16365C] text-center">Caps</th>
                        <th colspan="5" class="p-2 border border-[#F2F2F2] bg-[#4F6228] text-center">OPP Label</th>
                        <th colspan="5" class="p-2 border border-[#F2F2F2] bg-[#974706] text-center">LDPE Shrinkfilm</th>
                    </tr>

                    {{-- Subtitle row --}}
                    <tr>
                        <th class="p-2 border border-[#F2F2F2] text-center w-[120px] whitespace-nowrap bg-[#0070C0]">No.</th>
                        <th class="p-2 border border-[#F2F2F2] text-center w-[120px] whitespace-nowrap bg-[#0070C0]">Production Date</th>
                        <th class="p-2 border border-[#F2F2F2] text-center w-[180px] whitespace-nowrap bg-[#0070C0]">SKU</th>
                        <th class="p-2 border border-[#F2F2F2] text-center w-[100px] whitespace-nowrap bg-[#0070C0]">Bottle per Case</th>
                        <th class="p-2 border border-[#F2F2F2] text-center w-[160px] whitespace-nowrap bg-[#0070C0]">Target Mat'l Efficiency, %</th>
                        <th class="p-2 border border-[#F2F2F2] text-center w-[130px] whitespace-nowrap bg-[#0070C0]">Production Output</th>

                        {{-- Preforms --}}
                        <th class="p-2 border border-[#F2F2F2] text-center w-[120px] whitespace-nowrap bg-[#808080]">Description</th>
                        <th class="p-2 border border-[#F2F2F2] text-center w-[120px] whitespace-nowrap bg-[#808080]">FG Usage</th>
                        <th class="p-2 border border-[#F2F2F2] text-center w-[100px] whitespace-nowrap bg-[#808080]">Rejects</th>
                        <th class="p-2 border border-[#F2F2F2] text-center w-[110px] whitespace-nowrap bg-[#808080]">QA Samples</th>
                        <th class="p-2 border border-[#F2F2F2] text-center w-[100px] whitespace-nowrap bg-[#808080]">% Rejects</th>

                        {{-- Caps --}}
                        <th class="p-2 border border-[#F2F2F2] text-center w-[120px] whitespace-nowrap bg-[#16365C]">Description</th>
                        <th class="p-2 border border-[#F2F2F2] text-center w-[120px] whitespace-nowrap bg-[#16365C]">FG Usage</th>
                        <th class="p-2 border border-[#F2F2F2] text-center w-[100px] whitespace-nowrap bg-[#16365C]">Rejects</th>
                        <th class="p-2 border border-[#F2F2F2] text-center w-[110px] whitespace-nowrap bg-[#16365C]">QA Samples</th>
                        <th class="p-2 border border-[#F2F2F2] text-center w-[100px] whitespace-nowrap bg-[#16365C]">% Rejects</th>

                        {{-- OPP Label --}}
                        <th class="p-2 border border-[#F2F2F2] text-center w-[120px] whitespace-nowrap bg-[#4F6228]">Description</th>
                        <th class="p-2 border border-[#F2F2F2] text-center w-[120px] whitespace-nowrap bg-[#4F6228]">FG Usage</th>
                        <th class="p-2 border border-[#F2F2F2] text-center w-[100px] whitespace-nowrap bg-[#4F6228]">Rejects</th>
                        <th class="p-2 border border-[#F2F2F2] text-center w-[110px] whitespace-nowrap bg-[#4F6228]">QA Samples</th>
                        <th class="p-2 border border-[#F2F2F2] text-center w-[100px] whitespace-nowrap bg-[#4F6228]">% Rejects</th>

                        {{-- LDPE --}}
                        <th class="p-2 border border-[#F2F2F2] text-center w-[120px] whitespace-nowrap bg-[#974706]">Description</th>
                        <th class="p-2 border border-[#F2F2F2] text-center w-[120px] whitespace-nowrap bg-[#974706]">FG Usage</th>
                        <th class="p-2 border border-[#F2F2F2] text-center w-[100px] whitespace-nowrap bg-[#974706]">Rejects</th>
                        <th class="p-2 border border-[#F2F2F2] text-center w-[110px] whitespace-nowrap bg-[#974706]">QA Samples</th>
                        <th class="p-2 border border-[#F2F2F2] text-center w-[100px] whitespace-nowrap bg-[#974706]">% Rejects</th>
                    </tr>
                </thead>
                <tbody>
                    @php $rowNumber = 1; @endphp
@foreach($analytics as $row)
    @php
        $productionDate = \Carbon\Carbon::parse($row->production_date)->format('F j, Y');
        $sku            = $row->sku ?? 'No Run';
        $isNoRun        = strcasecmp(trim($sku), 'No Run') === 0;

        $bottlesPerCase = $isNoRun ? 0 : (int) ($row->bottlePerCase ?? 0);
        $output         = $isNoRun ? 0 : (int) ($row->total_output ?? 0);

        $efficiency     = number_format(($row->targetMaterialEfficiency ?? 0.01) * 100, 2) . '%';

        // Denominators
        $fgBottles = (!$isNoRun && $output && $bottlesPerCase) ? ($output * $bottlesPerCase) : 0;
        $fgCases   = (!$isNoRun) ? $output : 0;

        // Material values already stored in analytics
        $preformDesc = $row->preformDesc;
        $capsDesc    = $row->capsDesc;
        $labelDesc   = $row->labelDesc;
        $ldpeDesc    = $row->ldpeDesc;

        $preformRejects = $row->preform_rej ?? 0;
        $capsRejects    = $row->caps_rej ?? 0;
        $labelRejects   = $row->label_rej ?? 0;
        $ldpeRejects    = $row->ldpe_rej ?? 0;

        $qaPreform = $row->preform_qa ?? 0;
        $qaCaps    = $row->caps_qa ?? 0;
        $qaLabel   = $row->label_qa ?? 0;
        $qaLdpe    = $row->ldpe_qa ?? 0;

        $preformPercent = number_format($row->preform_pct, 2) . '%';
        $capsPercent    = number_format($row->caps_pct, 2) . '%';
        $labelPercent   = number_format($row->label_pct, 2) . '%';
        $ldpePercent    = number_format($row->ldpe_pct, 2) . '%';

        $bgClass = fn($percent) => ($percent === '' ? 'bg-[#DBE5F1]' : ((float)$percent >= 1.01 ? 'bg-[#FF0000]' : 'bg-[#92D050]'));
    @endphp

                        <tr class="group text-[10px] text-gray-700 whitespace-nowrap">
                            <td class="border border-[#F2F2F2] bg-[#DBE5F1] p-2 text-center">{{ $rowNumber++ }}</td>
                            <td class="border border-[#F2F2F2] bg-[#DBE5F1] p-2 text-center">{{ $productionDate }}</td>
                            <td class="border border-[#F2F2F2] bg-[#DBE5F1] p-2 text-center">{{ $sku }}</td>

                            {{-- Blank values when No Run --}}
                            <td class="border border-[#F2F2F2] bg-[#F2F2F2] p-2 text-center">{{ $isNoRun ? '' : $bottlesPerCase }}</td>
                            <td class="border border-[#F2F2F2] bg-[#F2F2F2] p-2 text-center">{{ $isNoRun ? '' : $efficiency }}</td>
                            <td class="border border-[#F2F2F2] bg-[#DBE5F1] p-2 text-center">{{ $isNoRun ? '' : $output }}</td>

                            {{-- PREFORMS --}}
                            <td class="border border-[#F2F2F2] bg-[#F2F2F2] p-2 text-center">{{ $isNoRun ? '' : $preformDesc }}</td>
                            <td class="border border-[#F2F2F2] bg-[#F2F2F2] p-2 text-center">{{ $isNoRun ? '' : $fgBottles }}</td>
                            <td class="border border-[#F2F2F2] bg-[#DBE5F1] p-2 text-center">{{ $isNoRun ? '' : $preformRejects }}</td>
                            <td class="border border-[#F2F2F2] bg-[#DBE5F1] p-2 text-center">{{ $isNoRun ? '' : $qaPreform }}</td>
                            <td class="border border-[#F2F2F2] p-2 text-center {{ $bgClass($preformPercent) }}">{{ $preformPercent }}</td>

                            {{-- CAPS --}}
                            <td class="border border-[#F2F2F2] bg-[#F2F2F2] p-2 text-center">{{ $isNoRun ? '' : $capsDesc }}</td>
                            <td class="border border-[#F2F2F2] bg-[#F2F2F2] p-2 text-center">{{ $isNoRun ? '' : $fgBottles }}</td>
                            <td class="border border-[#F2F2F2] bg-[#DBE5F1] p-2 text-center">{{ $isNoRun ? '' : $capsRejects }}</td>
                            <td class="border border-[#F2F2F2] bg-[#DBE5F1] p-2 text-center">{{ $isNoRun ? '' : $qaCaps }}</td>
                            <td class="border border-[#F2F2F2] p-2 text-center {{ $bgClass($capsPercent) }}">{{ $capsPercent }}</td>

                            {{-- OPP LABEL --}}
                            <td class="border border-[#F2F2F2] bg-[#F2F2F2] p-2 text-center">{{ $isNoRun ? '' : $labelDesc }}</td>
                            <td class="border border-[#F2F2F2] bg-[#F2F2F2] p-2 text-center">{{ $isNoRun ? '' : $fgBottles }}</td>
                            <td class="border border-[#F2F2F2] bg-[#DBE5F1] p-2 text-center">{{ $isNoRun ? '' : $labelRejects }}</td>
                            <td class="border border-[#F2F2F2] bg-[#DBE5F1] p-2 text-center">{{ $isNoRun ? '' : $qaLabel }}</td>
                            <td class="border border-[#F2F2F2] p-2 text-center {{ $bgClass($labelPercent) }}">{{ $labelPercent }}</td>

                            {{-- LDPE SHRINKFILM (case-based) --}}
                            <td class="border border-[#F2F2F2] bg-[#F2F2F2] p-2 text-center">{{ $isNoRun ? '' : $ldpeDesc }}</td>
                            <td class="border border-[#F2F2F2] bg-[#F2F2F2] p-2 text-center">{{ $isNoRun ? '' : $fgCases }}</td>
                            <td class="border border-[#F2F2F2] bg-[#DBE5F1] p-2 text-center">{{ $isNoRun ? '' : $ldpeRejects }}</td>
                            <td class="border border-[#F2F2F2] bg-[#DBE5F1] p-2 text-center">{{ $isNoRun ? '' : $qaLdpe }}</td>
                            <td class="border border-[#F2F2F2] p-2 text-center {{ $bgClass($ldpePercent) }}">{{ $ldpePercent }}</td>
                        </tr>
                    @endforeach

                        <tr>
                            <th colspan="26"
                                class="p-2 text-center whitespace-nowrap bg-[#595959]">
                            </th>
                        </tr>  
                        <tr>
                            <th colspan="26"
                                class="p-2 text-center whitespace-nowrap bg-[#F2F2F2]">
                            </th>
                        </tr>  
                        <thead class="text-[10px]">
                            {{-- === MTD RM SUMMARY REPORT + PREFORMS side by side === --}}
                            <tr>
                                {{-- Title (spans down 7 rows) --}}
                                <th colspan="4" rowspan="7"
                                    class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4] text-black">
                                    MTD RM SUMMARY REPORT
                                </th>
                            </tr>

                            <tr>        
                                {{-- First row values (W1) --}}
                                <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">W1</th>
                                <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">
                                    {{ number_format($weeklyData[0]['output'] ?? 0) }}
                                </th>
                                {{-- Title (spans down 7 rows) --}}
                                <th rowspan="7"
                                    class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">
                                    PREFORMS
                                </th>
                                <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">
                                    {{ number_format($weeklyData[0]['preform']['fg']) ?? 0 }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">
                                    {{ number_format($weeklyData[0]['preform']['rej']) ?? 0 }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">
                                {{ $weeklyData[0]['preform']['qa'] }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">
                                {{ $weeklyData[0]['preform']['percent'] }}
                                </th>
                                {{-- Title (spans down 7 rows) --}}
                                <th rowspan="7"
                                    class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">
                                    CAPS
                                </th>
                                <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">
                                    {{ number_format($weeklyData[0]['caps']['fg']) ?? 0 }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">
                                    {{ number_format($weeklyData[0]['caps']['rej']) ?? 0 }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">
                                {{ $weeklyData[0]['caps']['qa'] }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">
                                {{ $weeklyData[0]['caps']['percent'] }}
                                </th>

                                        {{-- Title (spans down 7 rows) --}}
                                <th rowspan="7"
                                    class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">
                                    OPP LABELS
                                </th>
                                <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">
                                    {{ number_format($weeklyData[0]['label']['fg']) ?? 0 }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">
                                    {{ number_format($weeklyData[0]['label']['rej']) ?? 0 }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">
                                {{ $weeklyData[0]['label']['qa'] }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">
                                {{ $weeklyData[0]['label']['percent'] }}
                                </th>

                                                {{-- Title (spans down 7 rows) --}}
                                <th rowspan="7"
                                    class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">
                                    LDPE SHRINKFILM
                                </th>
                                <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">
                                    {{ number_format($weeklyData[0]['ldpe']['fg']) ?? 0 }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">
                                    {{ number_format($weeklyData[0]['ldpe']['rej']) ?? 0 }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">
                                {{ $weeklyData[0]['ldpe']['qa'] }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">
                                {{ $weeklyData[0]['ldpe']['percent'] }}
                                </th>
                            </tr>

                            {{-- W2 --}}
                            <tr>
                                <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">W2</th>
                                <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">
                                    {{ number_format($weeklyData[1]['output'] ?? 0) }}
                                </th>
                                <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">
                                    {{ number_format($weeklyData[1]['preform']['fg']) ?? 0 }}
                                </th>
                                                <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">
                                    {{ number_format($weeklyData[1]['preform']['rej']) ?? 0 }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">
                                {{ $weeklyData[1]['preform']['qa'] }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">
                                {{ $weeklyData[1]['preform']['percent'] }}
                                </th>
                                
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">
                                    {{ number_format($weeklyData[1]['caps']['fg']) ?? 0 }}
                                </th>
                                                <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">
                                    {{ number_format($weeklyData[1]['caps']['rej']) ?? 0 }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">
                                {{ $weeklyData[1]['caps']['qa'] }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">
                                {{ $weeklyData[1]['caps']['percent'] }}
                                </th>

                                                <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">
                                    {{ number_format($weeklyData[1]['label']['fg']) ?? 0 }}
                                </th>
                                                <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">
                                    {{ number_format($weeklyData[1]['label']['rej']) ?? 0 }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">
                                {{ $weeklyData[1]['label']['qa'] }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">
                                {{ $weeklyData[1]['label']['percent'] }}
                                </th>

                                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">
                                    {{ number_format($weeklyData[1]['ldpe']['fg']) ?? 0 }}
                                </th>
                                                <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">
                                    {{ number_format($weeklyData[1]['ldpe']['rej']) ?? 0 }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">
                                {{ $weeklyData[1]['ldpe']['qa'] }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">
                                {{ $weeklyData[1]['ldpe']['percent'] }}
                                </th>
                            </tr>

                            {{-- W3 --}}
                            <tr>
                                <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">W3</th>
                                <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">
                                    {{ number_format($weeklyData[2]['output'] ?? 0) }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">
                                    {{ number_format($weeklyData[2]['preform']['fg']) ?? 0 }}
                                </th>
                                                <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">
                                    {{ number_format($weeklyData[2]['preform']['rej']) ?? 0 }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">
                                {{ $weeklyData[2]['preform']['qa'] }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">
                                {{ $weeklyData[2]['preform']['percent'] }}
                                </th>

                                                <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">
                                    {{ number_format($weeklyData[2]['caps']['fg']) ?? 0 }}
                                </th>
                                                <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">
                                    {{ number_format($weeklyData[2]['caps']['rej']) ?? 0 }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">
                                {{ $weeklyData[2]['caps']['qa'] }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">
                                {{ $weeklyData[2]['caps']['percent'] }}
                                </th>

                                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">
                                    {{ number_format($weeklyData[2]['label']['fg']) ?? 0 }}
                                </th>
                                                <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">
                                    {{ number_format($weeklyData[2]['label']['rej']) ?? 0 }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">
                                {{ $weeklyData[2]['label']['qa'] }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">
                                {{ $weeklyData[2]['label']['percent'] }}
                                </th>

                                                                <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">
                                    {{ number_format($weeklyData[2]['ldpe']['fg']) ?? 0 }}
                                </th>
                                                <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">
                                    {{ number_format($weeklyData[2]['ldpe']['rej']) ?? 0 }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">
                                {{ $weeklyData[2]['ldpe']['qa'] }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">
                                {{ $weeklyData[2]['ldpe']['percent'] }}
                                </th>
                            </tr>

                            {{-- W4 --}}
                            <tr>
                                <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">W4</th>
                                <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">
                                    {{ number_format($weeklyData[3]['output'] ?? 0) }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">
                                    {{ number_format($weeklyData[3]['preform']['fg']) ?? 0 }}
                                </th>
                                                <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">
                                    {{ number_format($weeklyData[3]['preform']['rej']) ?? 0 }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">
                                {{ $weeklyData[3]['preform']['qa'] }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">
                                {{ $weeklyData[3]['preform']['percent'] }}
                                </th>

                                                <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">
                                    {{ number_format($weeklyData[3]['caps']['fg']) ?? 0 }}
                                </th>
                                                <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">
                                    {{ number_format($weeklyData[3]['caps']['rej']) ?? 0 }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">
                                {{ $weeklyData[3]['caps']['qa'] }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">
                                {{ $weeklyData[3]['caps']['percent'] }}
                                </th>

                                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">
                                    {{ number_format($weeklyData[3]['label']['fg']) ?? 0 }}
                                </th>
                                                <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">
                                    {{ number_format($weeklyData[3]['label']['rej']) ?? 0 }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">
                                {{ $weeklyData[3]['label']['qa'] }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">
                                {{ $weeklyData[3]['label']['percent'] }}
                                </th>

                                                                <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">
                                    {{ number_format($weeklyData[3]['ldpe']['fg']) ?? 0 }}
                                </th>
                                                <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">
                                    {{ number_format($weeklyData[3]['ldpe']['rej']) ?? 0 }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">
                                {{ $weeklyData[3]['ldpe']['qa'] }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">
                                {{ $weeklyData[3]['ldpe']['percent'] }}
                                </th>
                            </tr>

                            {{-- W5 --}}
                            <tr>
                                <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">W5</th>
                                <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#FCD5B4]">
                                    {{ number_format($weeklyData[4]['output'] ?? 0) }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">
                                    {{ number_format($weeklyData[4]['preform']['fg']) ?? 0 }}
                                </th>
                                                <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">
                                    {{ number_format($weeklyData[4]['preform']['rej']) ?? 0 }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">
                                {{ $weeklyData[4]['preform']['qa'] }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#808080] text-white">
                                {{ $weeklyData[4]['preform']['percent'] }}
                                </th>

                                                <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">
                                    {{ number_format($weeklyData[4]['caps']['fg']) ?? 0 }}
                                </th>
                                                <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">
                                    {{ number_format($weeklyData[4]['caps']['rej']) ?? 0 }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">
                                {{ $weeklyData[4]['caps']['qa'] }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#16365C] text-white">
                                {{ $weeklyData[4]['caps']['percent'] }}
                                </th>

                                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">
                                    {{ number_format($weeklyData[4]['label']['fg']) ?? 0 }}
                                </th>
                                                <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">
                                    {{ number_format($weeklyData[4]['label']['rej']) ?? 0 }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">
                                {{ $weeklyData[4]['label']['qa'] }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#4F6228] text-white">
                                {{ $weeklyData[4]['label']['percent'] }}
                                </th>

                                                                <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">
                                    {{ number_format($weeklyData[4]['ldpe']['fg']) ?? 0 }}
                                </th>
                                                <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">
                                    {{ number_format($weeklyData[4]['ldpe']['rej']) ?? 0 }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">
                                {{ $weeklyData[4]['ldpe']['qa'] }}
                                </th>
                                        <th class="p-2 border border-[#F2F2F2] text-center whitespace-nowrap bg-[#974706] text-white">
                                {{ $weeklyData[4]['ldpe']['percent'] }}
                                </th>
                            </tr>

                            {{-- PTD ROW --}}
                            <tr>
                                <th class="bg-[#FCD5B4] p-2 border border-[#F2F2F2] text-center font-bold">PTD</th>
                                <th class="bg-[#FCD5B4] p-2 border border-[#F2F2F2] text-center font-bold">
                                    {{ number_format(collect($weeklyData)->sum('output')) }}
                                </th>

                                {{-- Preforms --}}
                                <th class="bg-[#808080] text-white p-2 border border-[#F2F2F2] text-center font-bold">{{ number_format($totalPreformFg) }}</th>
                                <th class="bg-[#808080] text-white p-2 border border-[#F2F2F2] text-center font-bold">{{ number_format($totalPreformRej) }}</th>
                                <th class="bg-[#808080] text-white p-2 border border-[#F2F2F2] text-center font-bold">{{ number_format($totalPreformQa) }}</th>
                                <th class="bg-[#808080] text-white p-2 border border-[#F2F2F2] text-center font-bold">{{ $totalPreformPct }}</th>

                                {{-- Caps --}}
                                <th class="bg-[#16365C] text-white p-2 border border-[#F2F2F2] text-center font-bold">{{ number_format($totalCapsFg) }}</th>
                                <th class="bg-[#16365C] text-white p-2 border border-[#F2F2F2] text-center font-bold">{{ number_format($totalCapsRej) }}</th>
                                <th class="bg-[#16365C] text-white p-2 border border-[#F2F2F2] text-center font-bold">{{ number_format($totalCapsQa) }}</th>
                                <th class="bg-[#16365C] text-white p-2 border border-[#F2F2F2] text-center font-bold">{{ $totalCapsPct }}</th>

                                {{-- Labels --}}
                                <th class="bg-[#4F6228] text-white p-2 border border-[#F2F2F2] text-center font-bold">{{ number_format($totalLabelFg) }}</th>
                                <th class="bg-[#4F6228] text-white p-2 border border-[#F2F2F2] text-center font-bold">{{ number_format($totalLabelRej) }}</th>
                                <th class="bg-[#4F6228] text-white p-2 border border-[#F2F2F2] text-center font-bold">{{ number_format($totalLabelQa) }}</th>
                                <th class="bg-[#4F6228] text-white p-2 border border-[#F2F2F2] text-center font-bold">{{ $totalLabelPct }}</th>

                                {{-- LDPE --}}
                                <th class="bg-[#974706] text-white p-2 border border-[#F2F2F2] text-center font-bold">{{ number_format($totalLdpeFg) }}</th>
                                <th class="bg-[#974706] text-white p-2 border border-[#F2F2F2] text-center font-bold">{{ number_format($totalLdpeRej) }}</th>
                                <th class="bg-[#974706] text-white p-2 border border-[#F2F2F2] text-center font-bold">{{ number_format($totalLdpeQa) }}</th>
                                <th class="bg-[#974706] text-white p-2 border border-[#F2F2F2] text-center font-bold">{{ $totalLdpePct }}</th>
                            </tr>
                        </thead>
                </tbody>
            </table>
        </div>
    </div>
</div>
