@extends('layouts.app')

@section('content')
<a href="{{ url('report/index') }}" class="flex items-center text-sm text-gray-500 hover:text-[#2d326b] mb-4">
    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
    </svg>
    Back to Reports
</a>

<div class="bg-white rounded-sm border border-gray-200 p-6 shadow-md space-y-5">

    <h2 class="text-xl font-bold text-[#2d326b]">Preview Version Changes</h2>

    @php
        $old = $history->old_data['fields'] ?? [];
        $new = $history->new_data['fields'] ?? [];

        function diffField($key, $old, $new) {
            if (!array_key_exists($key, $old) && !array_key_exists($key, $new)) return '-';
            $oldVal = $old[$key] ?? '-';
            $newVal = $new[$key] ?? '-';
            return $oldVal != $newVal
                ? "<span class='line-through text-red-500'>$oldVal</span> <span class='text-gray-400'>→</span> <span class='text-green-600 font-semibold'>$newVal</span>"
                : "<span class='text-gray-700'>$newVal</span>";
        }
    @endphp

    <!-- Preview Table Replicating Main View -->
    <table class="min-w-full text-sm border border-gray-200 shadow-sm">
        <thead class="bg-gray-100 text-[#2d326b]">
            <tr>
                <th class="text-left px-4 py-3 w-1/4">Field</th>
                <th class="text-left px-4 py-3 w-1/4">Value</th>
                <th class="text-left px-4 py-3 w-1/4">Field</th>
                <th class="text-left px-4 py-3 w-1/4">Value</th>
            </tr>
        </thead>
        <tbody class="text-gray-700 divide-y divide-gray-200">
            <tr>
                <td class="font-medium text-[#2d326b] px-4 py-2">Running SKU</td>
                <td class="px-4 py-2">{!! diffField('sku', $old, $new) !!}</td>

                <td class="font-medium text-[#2d326b] px-4 py-2">Production Date</td>
                <td class="px-4 py-2">{!! diffField('production_date', $old, $new) !!}</td>
            </tr>
            <tr>
                <td class="font-medium text-[#2d326b] px-4 py-2">Shift</td>
                <td class="px-4 py-2">{!! diffField('shift', $old, $new) !!}</td>

                <td class="font-medium text-[#2d326b] px-4 py-2">Line</td>
                <td class="px-4 py-2">{!! diffField('line', $old, $new) !!}</td>
            </tr>
            <tr>
                <td class="font-medium text-[#2d326b] px-4 py-2">Total Output (Cases)</td>
                <td class="px-4 py-2">{!! diffField('total_outputCase', $old, $new) !!}</td>

                <td class="font-medium text-[#2d326b] px-4 py-2">FBO/FCO</td>
                <td class="px-4 py-2">{!! diffField('fbo_fco', $old, $new) !!}</td>
            </tr>
            <tr>
                <td class="font-medium text-[#2d326b] px-4 py-2">LBO/LCO</td>
                <td class="px-4 py-2">{!! diffField('lbo_lco', $old, $new) !!}</td>
            </tr>
            <tr>

                <td class="font-medium text-[#2d326b] px-4 py-2">Filler Speed</td>
                <td class="px-4 py-2">{!! diffField('filler_speed', $old, $new) !!} <span class="text-xs">bph</span></td>
            </tr>
        </tbody>
    </table>

    <!-- Issues Section -->
    @if (!empty($history->old_data['issues']))
        <h4 class="text-lg font-semibold text-[#2d326b]">Issues / Down Time</h4>
        <table class="min-w-full text-sm border border-gray-200 shadow-sm">
            <thead class="bg-gray-100 text-[#2d326b]">
                <tr>
                    <th class="text-left px-4 py-3 w-1/2">Old</th>
                    <th class="text-left px-4 py-3 w-1/2">New</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-sm">
                @foreach ($history->old_data['issues'] as $i => $oldIssue)
                    @php
                        $newIssue = $history->new_data['issues'][$i] ?? [];
                    @endphp
                    @if ($oldIssue !== $newIssue)
                        <tr>
                            <td class="px-4 py-2 text-red-500 line-through">{{ json_encode($oldIssue) }}</td>
                            <td class="px-4 py-2 text-green-600">{{ json_encode($newIssue) }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- QC Rejects Section -->
    @if (!empty($history->old_data['qc_rejects']))
        <h4 class="text-lg font-semibold text-[#2d326b]">Line QC Rejects</h4>
        <table class="min-w-full text-sm border border-gray-200 shadow-sm">
            <thead class="bg-gray-100 text-[#2d326b]">
                <tr>
                    <th class="text-left px-4 py-3 w-1/2">Old</th>
                    <th class="text-left px-4 py-3 w-1/2">New</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-sm">
                @foreach ($history->old_data['qc_rejects'] as $i => $oldReject)
                    @php
                        $newReject = $history->new_data['qc_rejects'][$i] ?? [];
                    @endphp
                    @if ($oldReject !== $newReject)
                        <tr>
                            <td class="px-4 py-2 text-red-500 line-through">{{ json_encode($oldReject) }}</td>
                            <td class="px-4 py-2 text-green-600">{{ json_encode($newReject) }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    @endif

</div>
@endsection
