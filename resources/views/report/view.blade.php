@extends('layouts.app')
@section('title', content: 'Production Report')
@section('content')

<div x-data="{ showHistory: false, showVoid: false, showValidate: false }" class="container mx-auto px-4">

    <!-- Header with Icon and Title -->
    <div class="flex-1 text-center">
        <h2 class="text-2xl m-4 font-bold text-[#23527c]">
            Basic Production Details
        </h2>
    </div>

    {{-- 🔔 Modal Alerts (Success, Error, Validation) --}}
    <x-alert-message />

    <div class=" border-t border-[#E5E7EB]">

        {{-- Top Controls: Back, Add Defect, Show Entries --}}
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 mt-6 mb-10">
            <div class="flex flex-col md:flex-row gap-2">
                {{-- Back Button --}}
                <a href="{{ url('report/index') }}" class="inline-flex items-center px-3 py-2 bg-[#5a9fd4] hover:bg-[#4a8bc2] text-white text-sm font-medium transition-colors duration-200 border border-[#4590ca] hover:border-[#4a8bc2]">
                    <x-icons-back class="w-2 h-2 text-white" />
                    Back
                </a>
                @can('report.edit')
                <!-- Edit Button -->
                <a href="{{ route('report.edit', $report->id) }}"
                    class="inline-flex items-center justify-center gap-1 p-2 border border-[#323B76] bg-[#323B76] hover:bg-[#444d90] border border-[#323B76] text-white text-sm font-medium">
                    <x-icons-edit class="w-2 h-2" />
                    <span class="text-sm">Edit</span>
                 </a>
                @endcan

                <!-- Change History Button -->
                <button 
                    type="button"
                    @click="showHistory = true"
                    class="inline-flex items-center justify-center gap-1 p-2 bg-[#5bb75b] hover:bg-[#42a542] border border-[#42a542] text-white text-sm font-medium"
                >
                    <x-icons-history/>
                    <span class="text-sm">History</span>
                </button>
                </div>

                <div class="flex items-center gap-2">

                    {{-- Void Button --}}
                    @if (! $isValidated && $report->latestStatus?->status !== 'Voided')
                        <button 
                            type="button"
                            @click="showVoid = true"
                            class="inline-flex items-center justify-center gap-1 p-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium"
                        >
                            <x-icons-void class="w-4 h-4" />
                            Void
                        </button>
                    @endif

                    {{-- Validate Button --}}
                    @can('report.validate')
                        @if (! $isValidated && $report->latestStatus?->status !== 'Voided')
                            <button type="button"
                                @click="showValidate = true"
                                class="inline-flex items-center justify-center gap-1 p-2 bg-[#5bb75b] hover:bg-[#42a542] border border-[#42a542] text-white text-sm font-medium">
                                <x-icons-validate class="w-2 h-2" />
                                Validate
                            </button>
                        @endif
                    @endcan

                    <!-- Export PDF Button -->
                    <a href="{{ route('report.pdf', $report->id) }}" target="_blank"
                        class="inline-flex items-center justify-center gap-1 p-2 bg-red-600 hover:bg-red-700 border border-red-700 text-white text-sm font-medium">
                        <x-icons-pdf class="w-4 h-4" />
                        View PDF
                    </a>
                </div>
        </div>

        <div class="mx-auto">

        <h1 class="text-xl font-bold text-[#23527c]">
            {{ $report->standard?->description ?? '-' }}
        </h1>

    <!-- Status (One Row) -->
    <div class="flex items-center mt-4 mb-4">
        <span class="text-[#23527c] font-bold w-48 mr-6">Status: </span>
        @php
            $status = $report->statuses->sortByDesc('created_at')->first()->status ?? null;
        @endphp

        <span class="w-[160px] px-3 py-1 text-sm text-center font-bold
            @if($status === 'Submitted') bg-yellow-100 text-yellow-800
            @elseif($status === 'Reviewed') border border-blue-200 bg-blue-100 text-blue-800
            @elseif($status === 'Validated') border border-green-200 bg-green-100 text-green-800
            @elseif($status === 'Voided') border border-red-200 bg-red-100 text-red-800
            @else bg-gray-100 text-gray-600 @endif">
            {{ $status ?? 'N/A' }}
        </span>
    </div>

    <!-- Line Efficiency Input (One Row) -->
    <div class="flex items-center mt-4 mb-4">
        <span class="text-[#23527c] font-bold w-48 mr-6">Line Efficiency (%): </span>
        <span class="w-[160px] border border-gray-300  px-3 py-1 text-sm text-center">{{ old('line_efficiency', $report->line_efficiency) }}</span>
    </div>


    <!-- Basic Production Details Table -->
    <table class="min-w-full text-sm border border-[#E5E7EB] shadow-sm">
        <thead class="uppercase text-[#23527c] bg-[#e2f2ff]">
            <tr>
                <th class="text-left p-3 w-1/4 font-bold">Production Details</th>
                <th class="p-3 w-1/4"></th>
                <th class="p-3 w-1/4"></th>
                <th class="p-3 w-1/4"></th>
            </tr>
        </thead>
        <tbody class="text-gray-700 divide-y divide-gray-200">
            <tr>
                <td class="font-medium text-[#2d326b] px-3 py-1">Running SKU</td>
                <td class="p-2">
                    <p class="w-full border border-gray-300 px-3 py-1 text-sm">
                        {{ $report->standard?->description ?? '-' }}
                    </p>
                </td>
                <td class="font-medium text-[#23527c] p-2">Production Date</td>
                <td class="p-2">
                    <p class="w-full border border-gray-300  px-3 py-1 text-sm">{{ $report->production_date ? \Carbon\Carbon::parse($report->production_date)->format('F j, Y') : '-' }}</p>
                </td>
            </tr>
            <tr>
                <td class="font-medium text-[#23527c] p-2">Shift</td>
                <td class="p-2">
                    <p class="w-full border border-gray-300  px-3 py-1 text-sm">{{ $report->shift ?? '-' }}</p>
                </td>
                <td class="font-medium text-[#23527c] p-2">AC Temperatures</td>
                <td class="p-2">
                    <div class="grid grid-cols-4 gap-1">
                        @for ($i = 1; $i <= 4; $i++)
                            <p class="w-full border border-gray-300  p-2 text-sm">
                                {{ $report->{'ac' . $i} ?? '-' }} °C
                            </p>
                        @endfor
                    </div>
                </td>
            </tr>
            <tr>
                <td class="font-medium text-[#23527c] p-2">Line #</td>
                <td class="p-2">
                    <p class="w-full border border-gray-300  px-3 py-1 text-sm">{{ $report->line ?? '-' }}</p>
                </td>
                <td class="font-medium text-[#23527c] p-2">Total Output (Cases)</td>
                <td class="p-2">
                    <p class="w-full border border-gray-300  px-3 py-1 text-sm">
                        {{ $report->total_outputCase ?? '-' }} <span>cases</span>
                    </p>
                </td>
            </tr>
            <tr>
                <td class="font-medium text-[#23527c] p-2">FBO/FCO</td>
                <td class="p-2">
                    <p class="w-full border border-gray-300  px-3 py-1 text-sm">{{ $report->fbo_fco ?? '-' }}</p>
                </td>
                <td class="font-medium text-[#23527c] p-2">LBO/LCO</td>
                <td class="p-2">
                    <p class="w-full border border-gray-300  px-3 py-1 text-sm">{{ $report->lbo_lco ?? '-' }}</p>
                </td>
            </tr>
        </tbody>
    </table>


    <!-- Filling Line Table -->
    <table class="min-w-full text-sm border border-[#E5E7EB] shadow-sm mt-6">
        <thead class="uppercase text-[#23527c] bg-[#e2f2ff]">
            <tr>
                <th class="text-left p-3 w-1/6 font-bold">Filling Line</th>
                <th class="text-left p-3 w-1/6"></th>
                <th class="text-left p-3 w-1/6"></th>
                <th class="text-left p-3 w-1/8"></th>
                <th class="text-right text-xs p-3 w-1/6 font-bold">Bottle Code:</th>
                <th class="text-left text-xs p-3 w-1/4 font-bold">{{ $report->bottle_code ?? '-' }}</th>
            </tr>
        </thead>
        <tbody class="text-gray-700 divide-y divide-gray-200">
            <tr>
                <td class="font-medium text-[#23527c] p-2 text-xs">
                    Speed (Bottles per Hour) <span class="text-sm">Filler Speed</span>
                </td>
                <td class="p-2">
                    <p class="w-full border border-gray-300  px-3 py-1 text-sm">{{ $report->filler_speed ?? '-' }} <span>bph</span></p>
                </td>
                <td class="font-medium text-[#23527c] p-2 text-xs">
                    RM Rejects <br><span class="text-sm">Opp/Labels</span>
                </td>
                <td class="p-2">
                    <p class="w-full border border-gray-300  px-3 py-1 text-sm">{{ $report->opp_labels ?? '-' }} <span>pcs</span></p>
                </td>
                <td class="font-medium text-[#23527c] p-2">Caps (pcs)</td>
                <td class="p-2">
                    <p class="w-full border border-gray-300  px-3 py-1 text-sm">{{ $report->caps_filling ?? '-' }} <span>pcs</span></p>
                </td>
            </tr>
            <tr>
                <td class="font-medium text-[#23527c] p-2">OPP/Labeler Speed</td>
                <td class="p-2">
                    <p class="w-full border border-gray-300  px-3 py-1 text-sm">{{ $report->opp_labeler_speed ?? '-' }} <span>pcs</span></p>
                </td>
                <td class="font-medium text-[#23527c] p-2">Shrinkfilm</td>
                <td class="p-2">
                    <p class="w-full border border-gray-300  px-3 py-1 text-sm">{{ $report->shrinkfilm ?? '-' }} <span>pcs</span></p>
                </td>
                <td class="font-medium text-[#23527c] p-2">Bottle (pcs)</td>
                <td class="p-2">
                    <p class="w-full border border-gray-300  px-3 py-1 text-sm">{{ $report->bottle_filling ?? '-' }} <span>pcs</span></p>
                </td>
            </tr>
            <!-- Blow Molding Section Header -->
            <tr>
                <td colspan="2" class="uppercase text-[#23527c] bg-[#e2f2ff] font-bold p-3">Blow Molding</td>
                <td colspan="4" class="uppercase text-[#23527c] bg-[#e2f2ff] font-bold p-3">Blow Molding Rejects</td>
            </tr>
            <tr>
                <td class="font-medium text-[#23527c] p-2">Blow Molding Output</td>
                <td class="p-2">
                    <p class="w-full border border-gray-300  px-3 py-1 text-sm">{{ $report->blow_molding_output ?? '-' }} <span>pcs</span></p>
                </td>
                <td class="font-medium text-[#23527c] p-2">
                   Preform
                </td>
                <td class="p-2">
                    <p class="w-full border border-gray-300  px-3 py-1 text-sm">{{ $report->preform_blow_molding ?? '-' }} <span>pcs</span></p>
                </td>
                <td class="font-medium text-[#23527c] p-2">Bottles</td>
                <td class="p-2">
                    <p class="w-full border border-gray-300  px-3 py-1 text-sm">{{ $report->bottles_blow_molding ?? '-' }} <span>pcs</span></p>
                </td>
            </tr>
            <tr>
                <td class="font-medium text-[#23527c] p-2">Speed (Bottles/Hour)</td>
                <td class="p-2">
                    <p class="w-full border border-gray-300  px-3 py-1 text-sm">{{ $report->speed_blow_molding ?? '-' }} <span>bph</span></p>
                </td>
                <td colspan="4"></td>
            </tr>
        </tbody>
    </table>

    <!-- Issues Table -->
    <table class="min-w-full text-sm border border-[#E5E7EB] shadow-sm mt-6">
        <thead class="uppercase text-[#23527c] bg-[#e2f2ff]">
            <tr>
                <th class="text-left p-2 w-1/5">Machine / Others</th>
                <th class="text-left p-2 text-center">Description</th>
                <th class="text-left p-2 w-1/12">Minutes</th>
            </tr>
        </thead>
        <tbody class="text-gray-700 divide-y divide-gray-200">
            @forelse($report->issues as $issue)
                <tr>
                    <td class="p-2">{{ $issue->maintenance->name ?? '-' }}</td>
                    <td class="p-2 text-center">{{ $issue->remarks ?? '-' }}</td>
                    <td class="p-2 text-center">{{ $issue->minutes ?? '0' }} <span>mins</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center py-2 italic text-gray-400">No issues reported.</td>
                </tr>
            @endforelse

            {{-- Total Row --}}
            @if($report->issues->count() > 0)
                <tr class="bg-[#f8fafc] font-semibold text-[#23527c]">
                    <td colspan="2" class="p-2 text-right">Total Downtime:</td>
                    <td class="p-2 text-center">
                        {{ $report->issues->sum('minutes') ?? 0 }} mins
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- QA Remarks and Sample Table -->
    <table class="min-w-full text-sm border border-[#E5E7EB] shadow-sm  mt-6">
        <thead class="uppercase text-[#23527c] bg-[#e2f2ff]">
            <tr>
                <th class="text-left p-2 w-1/6">QA Remarks</th>
                <th class="text-left p-2 w-1/6"></th>
                <th class="text-left p-2 w-1/6">QA Sample</th>
                <th class="text-left p-2 w-1/6"></th>
                <th class="text-left p-2 w-1/6"></th>
                <th class="text-right p-2 w-1/6">Total: {{ $report->total_sample ?? '-' }} pcs</th>
            </tr>
        </thead>
        <tbody class="text-gray-700 divide-y divide-gray-200">
            <tr>
                <td class="font-medium text-[#23527c] p-2">Ozone</td>
                <td class="p-2">
                    <p class="w-full  px-3 py-1 text-sm
                        @if($report->qa_remarks === 'Passed') bg-green-100 text-green-800 font-semibold
                        @else border border-gray-300
                        @endif">
                        {{ $report->qa_remarks ?? '-' }}
                    </p>
                </td>
                <td class="font-medium text-[#23527c] p-2">With Label</td>
                <td class="p-2">
                    <p class="w-full border border-gray-300  px-3 py-1 text-sm">{{ $report->with_label ?? '-' }} <span>pcs</span></p>
                </td>
                <td class="font-medium text-[#23527c] p-2">Without Label</td>
                <td class="p-2">
                    <p class="w-full border border-gray-300  px-3 py-1 text-sm">{{ $report->without_label ?? '-' }} <span>pcs</span></p>
                </td>
            </tr>
                    </tbody>
                </table>

                <!-- Line QC Rejects Section -->
                <table class="min-w-full text-sm border border-[#E5E7EB] shadow-sm mt-6">
                    <thead class="uppercase text-[#23527c] bg-[#e2f2ff]">
                        <tr>
                            <th colspan="6" class="text-left px-4 py-3">Line QC Rejects</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 divide-y divide-gray-200">
                        @forelse ($report->lineQcRejects as $reject)
                            <tr>
                                <td colspan="5" class="p-2">
                                    {{ $reject->defect->defect_name ?? '-' }}
                                </td>
                                <td colspan="1" class="p-2 text-center w-1/12">
                                    {{ $reject->quantity ?? 0 }} pcs
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-2 italic text-gray-400">No QC rejects recorded.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
    </div>
</div>


<x-history-modal :report="$report" />
<x-validate-modal :report="$report"/>
<x-void-modal :report="$report"/>
</div>
@endsection
