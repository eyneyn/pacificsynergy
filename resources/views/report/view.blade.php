@extends('layouts.app')

@section('content')

<div x-data="{ showHistory: false }" class="container mx-auto px-4">

    <!-- Header with Icon and Title -->
    <div class="mb-4">
        <h1 class="text-xl font-bold text-[#23527c]">
            Basic Production Details
        </h1>
    </div>

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
                        <a href="{{ route('report.edit', $reports->id) }}"
                            class="inline-flex items-center justify-center gap-1 p-2 bg-[#323B76] hover:bg-[#444d90] border border-[#323B76] text-white text-sm font-medium">
                            <x-icons-edit class="w-2 h-2" />
                            <span class="text-sm">Edit</span>
                        </a>
                    @endcan

                    
            <!-- Change History Button -->
            <button @click="showHistory = true"
                class="inline-flex items-center justify-center gap-1 p-2 bg-[#5bb75b] hover:bg-[#42a542] border border-[#42a542] text-white text-sm font-medium">
                <x-icons-history/>
                <span class="text-sm">History</span>
            </button>
            </div>

                <div class="flex items-center gap-2">
                    @can('report.validate')
                        @if (! $isValidated)
                            <!-- Validate Report Button -->
                            <button type="button"
                                id="open-validate-modal"
                                class="inline-flex items-center justify-center gap-1 p-2 bg-[#5bb75b] hover:bg-[#42a542] border border-[#42a542] text-white text-sm font-medium">
                                <x-icons-validate class="w-2 h-2" />
                                Validate
                            </button>
                        @endif
                    @endcan

                    <!-- Export PDF Button -->
                    <a href="{{ route('report.pdf', $reports->id) }}" target="_blank"
                        class="inline-flex items-center justify-center gap-1 p-2 bg-red-600 hover:bg-red-700 border border-red-700 text-white text-sm font-medium">
                        <x-icons-pdf class="w-4 h-4" />
                        View PDF
                    </a>
                </div>
        </div>

        <div class="mx-auto">

        <h1 class="text-xl font-bold text-[#23527c]">
            {{ $reports->sku ?? '-' }}
        </h1>


    <!-- Line Efficiency Input (One Row) -->
                        <div class="flex items-center mt-4 mb-4">
                        <span class="text-[#23527c] font-bold w-48 mr-6">Line Efficiency (%): </span>
                        <span class="text-[#23527c] w-[160px] border border-gray-300  px-3 py-1 text-sm text-center">{{ old('line_efficiency', $reports->line_efficiency) }}</span>
                    </div>



    <!-- Success Message -->
    @if (session('success'))
        <div class="mb-4 p-2 text-sm  bg-green-100 text-green-800 border border-green-300 text-center">
            {{ session('success') }}
        </div>
    @endif

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
                    <p class="w-full border border-gray-300  px-3 py-1 text-sm">{{ $reports->sku ?? '-' }}</p>
                </td>
                <td class="font-medium text-[#23527c] p-2">Production Date</td>
                <td class="p-2">
                    <p class="w-full border border-gray-300  px-3 py-1 text-sm">{{ $reports->production_date ? \Carbon\Carbon::parse($reports->production_date)->format('F j, Y') : '-' }}</p>
                </td>
            </tr>
            <tr>
                <td class="font-medium text-[#23527c] p-2">Shift</td>
                <td class="p-2">
                    <p class="w-full border border-gray-300  px-3 py-1 text-sm">{{ $reports->shift ?? '-' }}</p>
                </td>
                <td class="font-medium text-[#23527c] p-2">AC Temperatures</td>
                <td class="p-2">
                    <div class="grid grid-cols-4 gap-1">
                        @for ($i = 1; $i <= 4; $i++)
                            <p class="w-full border border-gray-300  p-2 text-sm">
                                {{ $reports->{'ac' . $i} ?? '-' }} °C
                            </p>
                        @endfor
                    </div>
                </td>
            </tr>
            <tr>
                <td class="font-medium text-[#23527c] p-2">Line #</td>
                <td class="p-2">
                    <p class="w-full border border-gray-300  px-3 py-1 text-sm">{{ $reports->line ?? '-' }}</p>
                </td>
                <td class="font-medium text-[#23527c] p-2">Total Output (Cases)</td>
                <td class="p-2">
                    <p class="w-full border border-gray-300  px-3 py-1 text-sm">
                        {{ $reports->total_outputCase ?? '-' }} <span>cases</span>
                    </p>
                </td>
            </tr>
            <tr>
                <td class="font-medium text-[#23527c] p-2">FBO/FCO</td>
                <td class="p-2">
                    <p class="w-full border border-gray-300  px-3 py-1 text-sm">{{ $reports->fbo_fco ?? '-' }}</p>
                </td>
                <td class="font-medium text-[#23527c] p-2">LBO/LCO</td>
                <td class="p-2">
                    <p class="w-full border border-gray-300  px-3 py-1 text-sm">{{ $reports->lbo_lco ?? '-' }}</p>
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
                <th class="text-left p-3 w-1/6"></th>
                <th class="text-right text-xs p-3 w-1/6 font-bold">Bottle Code:</th>
                <th class="text-left text-xs p-3 w-1/6 font-bold">{{ $reports->bottle_code ?? '-' }}</th>
            </tr>
        </thead>
        <tbody class="text-gray-700 divide-y divide-gray-200">
            <tr>
                <td class="font-medium text-[#23527c] p-2 text-xs">
                    Speed (Bottles per Hour) <span class="text-sm">Filler Speed</span>
                </td>
                <td class="p-2">
                    <p class="w-full border border-gray-300  px-3 py-1 text-sm">{{ $reports->filler_speed ?? '-' }} <span>bph</span></p>
                </td>
                <td class="font-medium text-[#23527c] p-2 text-xs">
                    RM Rejects <br><span class="text-sm">Opp/Labels</span>
                </td>
                <td class="p-2">
                    <p class="w-full border border-gray-300  px-3 py-1 text-sm">{{ $reports->opp_labels ?? '-' }} <span>pcs</span></p>
                </td>
                <td class="font-medium text-[#23527c] p-2">Bottle (pcs)</td>
                <td class="p-2">
                    <p class="w-full border border-gray-300  px-3 py-1 text-sm">{{ $reports->bottle_filling ?? '-' }} <span>pcs</span></p>
                </td>
            </tr>
            <tr>
                <td class="font-medium text-[#23527c] p-2">OPP/Labeler Speed</td>
                <td class="p-2">
                    <p class="w-full border border-gray-300  px-3 py-1 text-sm">{{ $reports->opp_labeler_speed ?? '-' }} <span>pcs</span></p>
                </td>
                <td class="font-medium text-[#23527c] p-2">Shrinkfilm</td>
                <td class="p-2">
                    <p class="w-full border border-gray-300  px-3 py-1 text-sm">{{ $reports->shrinkfilm ?? '-' }} <span>pcs</span></p>
                </td>
                <td class="font-medium text-[#23527c] p-2">Caps (pcs)</td>
                <td class="p-2">
                    <p class="w-full border border-gray-300  px-3 py-1 text-sm">{{ $reports->caps_filling ?? '-' }} <span>pcs</span></p>
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
                    <p class="w-full text-[#23527c] border border-gray-300  px-3 py-1 text-sm">{{ $reports->blow_molding_output ?? '-' }} <span>pcs</span></p>
                </td>
                <td class="font-medium text-[#23527c] p-2">
                   Preform
                </td>
                <td class="p-2">
                    <p class="w-full border border-gray-300  px-3 py-1 text-sm">{{ $reports->preform_blow_molding ?? '-' }} <span>pcs</span></p>
                </td>
                <td class="font-medium text-[#23527c] p-2">Bottles</td>
                <td class="p-2">
                    <p class="w-full border border-gray-300  px-3 py-1 text-sm">{{ $reports->bottles_blow_molding ?? '-' }} <span>pcs</span></p>
                </td>
            </tr>
            <tr>
                <td class="font-medium text-[#23527c] p-2">Speed (Bottles/Hour)</td>
                <td class="p-2">
                    <p class="w-full border border-gray-300  px-3 py-1 text-sm">{{ $reports->speed_blow_molding ?? '-' }} <span>bph</span></p>
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
        @forelse($reports->issues as $issue)
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
        @if($reports->issues->count() > 0)
            <tr class="bg-[#f8fafc] font-semibold text-[#23527c]">
                <td colspan="2" class="p-2 text-right">Total Downtime:</td>
                <td class="p-2 text-center">
                    {{ $reports->issues->sum('minutes') ?? 0 }} mins
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
                <th class="text-right p-2 w-1/6">Total: {{ $reports->total_sample ?? '-' }} pcs</th>
            </tr>
        </thead>
        <tbody class="text-gray-700 divide-y divide-gray-200">
            <tr>
                <td class="font-medium text-[#23527c] p-2">Ozone</td>
                <td class="p-2">
                    <p class="w-full  px-3 py-1 text-sm
                        @if($reports->qa_remarks === 'Passed') bg-green-100 text-green-800 font-semibold
                        @else border border-gray-300
                        @endif">
                        {{ $reports->qa_remarks ?? '-' }}
                    </p>
                </td>
                <td class="font-medium text-[#23527c] p-2">With Label</td>
                <td class="p-2">
                    <p class="w-full border border-gray-300  px-3 py-1 text-sm">{{ $reports->with_label ?? '-' }} <span>pcs</span></p>
                </td>
                <td class="font-medium text-[#23527c] p-2">Without Label</td>
                <td class="p-2">
                    <p class="w-full border border-gray-300  px-3 py-1 text-sm">{{ $reports->without_label ?? '-' }} <span>pcs</span></p>
                </td>
            </tr>
            @php
                // Group line QC rejects by defect category
                $groupedRejects = $reports->lineQcRejects ? $reports->lineQcRejects->groupBy(fn($item) => $item->defect->category ?? 'Uncategorized') : collect();
            @endphp

            <!-- Line QC Rejects Section -->
            <tr>
                <td colspan="6" class="uppercase text-[#23527c] bg-[#e2f2ff] font-bold p-2">Line QC Rejects</td>
            </tr>
            <tr>
                <td colspan="6">
                    <div class="grid md:grid-cols-2 gap-2">
                        @foreach (['Caps', 'Bottle', 'Label', 'LDPE Shrinkfilm'] as $category)
                            <div class="p-3 border border-[#E5E7EB]  flex flex-col gap-2 ">
                                <!-- Category Title -->
                                <h5 class="text-sm font-bold text-[#23527c]">{{ $category }}</h5>
                                @if ($groupedRejects->has($category))
                                    @foreach ($groupedRejects[$category] as $reject)
                                        <div class="flex items-center justify-between gap-2 border border-[#E5E7EB] p-2  font-medium text-[#23527c] text-sm">
                                            <span>{{ $reject->defect->defect_name ?? '-' }}</span>
                                            <span class="font-medium text-[#23527c]">{{ $reject->quantity }} pcs</span>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-xs italic text-gray-400">No defects recorded</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>


<!-- Validate Modal Component -->
<x-validate-modal :reportId="$reports->id" />
        <!-- Include the modal component -->
    <x-history-modal :reports="$reports" />
</div>
@endsection
