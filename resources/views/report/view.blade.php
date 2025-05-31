@extends('layouts.app')

@section('content')

<!-- Back Button -->
<a href="{{url('report/index')}}" class="flex items-center text-sm text-gray-500 hover:text-[#2d326b] mb-4">
    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
    </svg>
    Production Report
</a>

    {{-- <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div class="flex-1">
            <h2 class="text-sm font-bold text-[#2d326b]">Production Report: {{ $reports->sku ?? '-' }}</h2>
        </div>
        <button type="submit"
            class="inline-flex items-center gap-2 p-3 py-2 bg-[#323B76] hover:bg-[#444d90] border border-[#323B76] text-white text-sm font-medium rounded-md">
            Save Report 
            
            {{ route('report.edit', $reports->id) }}
        </button>
    </div> --}}

    <!-- Card Container -->
    <div class="bg-white rounded-sm border border-gray-200 p-6 shadow-md space-y-5 transition-all duration-300 hover:shadow-xl hover:border-[#E5E7EB]">

        <div class="flex items-center justify-between mb-6">
            <h4 class="text-lg font-semibold text-[#2d326b]">Basic Production Details</h4>
                <div class="flex items-center gap-2">
        <!-- Delete Button -->
        <form id="icon-delete-report-form"
            data-delete-type="report"
            action="{{ route('report.destroy', $reports->id) }}"
            method="POST">
            @csrf
            @method('DELETE')

            <input type="hidden" id="edit_report_date" value="{{ $reports->production_date }}">

            <button type="submit"
                class="text-red-600 hover:text-red-800 rounded-lg text-md w-10 h-10 flex items-center justify-center"
                title="Delete Production Report">
                <x-icons-delete class="w-5 h-5" />
            </button>
        </form>

        <!-- Edit Button -->
        <a href="{{ route('report.edit', $reports->id) }}"
           class="inline-flex items-center gap-2 p-3 py-2 bg-[#323B76] hover:bg-[#444d90] border border-[#323B76] text-white text-sm font-medium rounded-md">
            <x-icons-edit class="w-4 h-4" />
            <span class="text-sm">Edit</span>
        </a>
        <!-- Export -->
            <div>
                <form method="POST" action="{{ url('download-pdf') }}" target="_blank">
                    @csrf
                    <input type="hidden" name="report_id" value="{{ $reports->id }}">
                    <button type="submit"
                        class="flex items-center gap-2 px-4 py-2 bg-[#323B76] border border-[#444d90] hover:bg-[#444d90] text-white text-sm font-medium rounded-md shadow-sm transition duration-200">
                        Export to PDF
                    </button>
                </form>
            </div>
    </div>
        </div>

        <!-- Basic Production Form -->
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
                    <td class="font-medium text-[#2d326b] px-4 py-2">RUnning SKU</td>
                    <td class="px-4 py-2">
                        <p class="w-full border border-gray-300 rounded px-3 py-1 text-sm">{{ $reports->sku ?? '-' }}</p>
                    </td> 
                    <td class="font-medium text-[#2d326b] px-4 py-2">Production Date</td>
                    <td class="px-4 py-2">
                        <p class="w-full border border-gray-300 rounded px-3 py-1 text-sm">{{ $reports->production_date ?? '-' }}</p>
                    </td>
                </tr>
                <tr>
                    <td class="font-medium text-[#2d326b] px-4 py-2">Shift</td>
                    <td class="px-4 py-2">
                        <p class="w-full border border-gray-300 rounded px-3 py-1 text-sm">{{ $reports->shift ?? '-' }}</p>
                    </td>
                    <td class="font-medium text-[#2d326b] px-4 py-2">AC Temperatures</td>
                    <td class="px-4 py-2">
                        <div class="grid grid-cols-4 gap-1">
                            @for ($i = 1; $i <= 4; $i++)
                                <p class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                                    {{ $reports->{'ac' . $i} ?? '-' }}
                                </p>
                            @endfor
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="font-medium text-[#2d326b] px-4 py-2">Line #</td>
                    <td class="px-4 py-2">
                        <p class="w-full border border-gray-300 rounded px-3 py-1 text-sm">{{ $reports->line ?? '-' }}</p>
                    </td>
                    <td class="font-medium text-[#2d326b] px-4 py-2">Total Output (Cases)</td>
                    <td class="px-4 py-2">
                        <p class="w-full border border-gray-300 rounded px-3 py-1 text-sm">
                            {{ $reports->total_outputCase ?? '-' }} <span>cases</span>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td class="font-medium text-[#2d326b] px-4 py-2">FBO/FCO</td>
                    <td class="px-4 py-2">
                        <p class="w-full border border-gray-300 rounded px-3 py-1 text-sm">{{ $reports->fbo_fco ?? '-' }}</p>                       
                    </td>
                    <td class="font-medium text-[#2d326b] px-4 py-2">LBO/LCO</td>
                    <td class="px-4 py-2">
                        <p class="w-full border border-gray-300 rounded px-3 py-1 text-sm">{{ $reports->lbo_lco ?? '-' }}</p>    
                    </td>
                </tr>
                <tr>
                    <td class="font-medium text-[#2d326b] px-4 py-2">Manpower Present</td>
                    <td class="px-4 py-2">
                        <p class="w-full border border-gray-300 rounded px-3 py-1 text-sm">{{ $reports->manpower_present ?? '-' }}</p>  
                    </td>
                    <td class="font-medium text-[#2d326b] px-4 py-2">Manpower Absent</td>
                    <td class="px-4 py-2">
                        <p class="w-full border border-gray-300 rounded px-3 py-1 text-sm">{{ $reports->manpower_absent ?? '-' }}</p>  
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="flex items-center justify-between">
            <h4 class="text-lg font-semibold text-[#2d326b]">Filling Line and Blow Molding</h4>
            <div class="text-sm font-semibold text-[#2d326b] ml-2">
                Bottle Code:
                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">
                    {{ $reports->bottle_code ?? '-' }}
                </span>
            </div>
        </div>

        <table class="min-w-full text-sm border border-gray-200 shadow-sm">
            <thead class="bg-gray-100 text-[#2d326b]">
                <tr>
                    <th class="text-left px-4 py-3 w-1/6">Filling Line</th>
                    <th class="text-left px-4 py-3 w-1/6"></th>
                    <th class="text-left px-4 py-3 w-1/6"></th>
                    <th class="text-left px-4 py-3 w-1/6"></th>
                    <th class="text-left px-4 py-3 w-1/6"></th>
                    <th class="text-left px-4 py-3 w-1/6"></th>
                </tr>
            </thead>
            <tbody class="text-gray-700 divide-y divide-gray-200">
                <tr>
                    <td class="font-medium text-[#2d326b] px-4 py-2 text-xs">Speed (Bottles per Hour) <span class="text-sm">Filler Speed</span></td>
                    <td class="px-4 py-2">
                        <p class="w-full border border-gray-300 rounded px-3 py-1 text-sm">{{ $reports->filler_speed ?? '-' }} <span>bph</span></p>  
                    </td>
                    <td class="font-medium text-[#2d326b] px-4 py-2 text-xs">RM Rejects <br><span class="text-sm">Opp/Labels</td>
                    <td class="px-4 py-2">
                        <p class="w-full border border-gray-300 rounded px-3 py-1 text-sm">{{ $reports->opp_labels ?? '-' }}</p>  
                    </td>
                    <td class="font-medium text-[#2d326b] px-4 py-2">Bottle (pcs)</td>
                    <td class="px-4 py-2">
                        <p class="w-full border border-gray-300 rounded px-3 py-1 text-sm">{{ $reports->bottle_filling ?? '-' }} <span>pcs</span></p>  
                    </td>
                </tr>
                <tr>
                    <td class="font-medium text-[#2d326b] px-4 py-2">OPP/Labeler Speed</td>
                    <td class="px-4 py-2">
                        <p class="w-full border border-gray-300 rounded px-3 py-1 text-sm">{{ $reports->opp_labeler_speed ?? '-' }} <span>pcs</span></p>  
                    </td>
                    <td class="font-medium text-[#2d326b] px-4 py-2">Shrinkfilm</td>
                    <td class="px-4 py-2">
                        <p class="w-full border border-gray-300 rounded px-3 py-1 text-sm">{{ $reports->shrinkfilm ?? '-' }} <span>pcs</span></p>  
                    </td>
                    <td class="font-medium text-[#2d326b] px-4 py-2">Caps (pcs)</td>
                    <td class="px-4 py-2">
                        <p class="w-full border border-gray-300 rounded px-3 py-1 text-sm">{{ $reports->caps_filling ?? '-' }} <span>pcs</span></p> 
                    </td>
                </tr>

                <!-- Blow Molding Section -->
                <thead class="bg-gray-100 text-[#2d326b]">
                    <tr>
                        <th class="text-left px-4 py-3 w-1/6">Blow Molding</th>
                        <th class="text-left px-4 py-3 w-1/6"></th>
                        <th class="text-left px-4 py-3 w-1/6"></th>
                        <th class="text-left px-4 py-3 w-1/6"></th>
                        <th class="text-left px-4 py-3 w-1/6"></th>
                        <th class="text-left px-4 py-3 w-1/6"></th>
                    </tr>
                </thead>
                <tr>
                    <td class="font-medium text-[#2d326b] px-4 py-2">Blow Molding Output</td>
                    <td class="px-4 py-2">
                        <p class="w-full border border-gray-300 rounded px-3 py-1 text-sm">{{ $reports->blow_molding_output ?? '-' }} <span>pcs</span></p> 
                    </td>
                    <td class="font-medium text-[#2d326b] px-4 py-2 text-xs">Blow Molding Rejects <span class="text-sm">Preform</span></td>
                    <td class="px-4 py-2">
                        <p class="w-full border border-gray-300 rounded px-3 py-1 text-sm">{{ $reports->speed_blow_molding ?? '-' }} <span>bph</span></p> 
                    </td>
                    <td class="font-medium text-[#2d326b] px-4 py-2">Bottles</td>
                    <td class="px-4 py-2">
                        <p class="w-full border border-gray-300 rounded px-3 py-1 text-sm">{{ $reports->preform_blow_molding ?? '-' }} <span>pcs</span></p> 
                    </td>
                </tr>
                <tr>
                    <td class="font-medium text-[#2d326b] px-4 py-2">Speed (Bottles/Hour)</td>
                    <td class="px-4 py-2">
                        <p class="w-full border border-gray-300 rounded px-3 py-1 text-sm">{{ $reports->bottles_blow_molding ?? '-' }} <span>pcs</span></p> 
                    </td>
                    <td colspan="4"></td>
                </tr>
            </tbody>
        </table>

            <div class="flex items-center justify-between">
                <h4 class="text-lg font-semibold text-[#2d326b]">Issues/ Down Time / Remarks</h4>
                <div class="text-sm font-semibold text-[#2d326b]">
                    Total downtime:
                    <span class="ml-2 bg-red-100 text-red-700 px-2 py-1 rounded">
                        {{ $reports->issues->sum('minutes') ?? 0 }} mins
                    </span>
                </div>
            </div>
            

            <table class="min-w-full text-sm border border-gray-200 shadow-sm">
                <thead class="bg-gray-100 text-[#2d326b]">
                    <tr>
                        <th class="text-left px-4 py-3 w-1/4">Machine / Others</th>
                        <th class="text-left px-4 py-3 text-center">Description</th>
                        <th class="text-left px-4 py-3 w-1/12">Minutes</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 divide-y divide-gray-200">
                    @forelse($reports->issues as $issue)
                        <tr>
                            <td class="px-4 py-2">{{ $issue->maintenance->name ?? '-' }}</td>
                            <td class="px-2 py-2 text-center">{{ $issue->remarks ?? '-' }}</td>
                            <td class="px-2 py-2 text-center">{{ $issue->minutes ?? '0' }} <span>mins</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-3 italic text-gray-400">No issues reported.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            
            <table class="min-w-full text-sm border border-gray-200 shadow-sm">
                <thead class="bg-gray-100 text-[#2d326b]">
                    <tr>
                        <th class="text-left px-4 py-3 w-1/6">QA Remarks</th>
                        <th class="text-left px-4 py-3 w-1/6"></th>
                        <th class="text-left px-4 py-3 w-1/6">QA Sample</th>
                        <th class="text-left px-4 py-3 w-1/6"></th>
                        <th class="text-left px-4 py-3 w-1/6"></th>
                        <th class="text-left px-4 py-3 w-1/6"></th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 divide-y divide-gray-200">
                    <tr>
                        <td class="font-medium text-[#2d326b] px-4 py-2">Ozone</td>
                        <td class="px-4 py-2">
                            <p class="w-full rounded px-3 py-1 text-sm
                                @if($reports->qa_remarks === 'Passed') bg-green-100 text-green-800 font-semibold 
                                @else border border-gray-300 
                                @endif">
                                {{ $reports->qa_remarks ?? '-' }}
                            </p>
                        </td>
                        <td class="font-medium text-[#2d326b] px-4 py-2">With Label</td>
                        <td class="px-4 py-2">
                            <p class="w-full border border-gray-300 rounded px-3 py-1 text-sm">{{ $reports->with_label ?? '-' }} <span>pcs</span></p> 
                        </td>
                        <td class="font-medium text-[#2d326b] px-4 py-2">Without Label</td>
                        <td class="px-4 py-2">
                            <p class="w-full border border-gray-300 rounded px-3 py-1 text-sm">{{ $reports->without_label ?? '-' }} <span>pcs</span></p> 
                        </td>
                    </tr>
                @php
                    $groupedRejects = $reports->lineQcRejects ? $reports->lineQcRejects->groupBy(fn($item) => $item->defect->category ?? 'Uncategorized') : collect();
                @endphp

                <!-- Line QC Rejects Section -->
                <thead class="bg-gray-100 text-[#2d326b]">
                    <tr>
                        <th colspan="6" class="text-left px-4 py-3">Line QC Rejects</th>
                    </tr>
                </thead>
                <tr>
                    <td colspan="6">
                        <div class="grid md:grid-cols-4 gap-2">
                            @foreach (['Caps', 'Bottle', 'Label', 'Carton'] as $category)
                                <div class="p-3 border border-gray-200 rounded flex flex-col gap-2 bg-white">
                                    <!-- Category Title -->
                                    <h5 class="text-sm font-bold text-[#2d326b]">{{ $category }}</h5>

                                    @if ($groupedRejects->has($category))
                                        @foreach ($groupedRejects[$category] as $reject)
                                            <div class="flex items-center justify-between gap-2 border border-gray-200 px-2 py-1 rounded font-medium text-[#2d326b] text-sm">
                                                <span>{{ $reject->defect->defect_name ?? '-' }}</span>
                                                <span class="font-medium text-[#2d326b]">{{ $reject->quantity }} pcs</span>
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

<x-delete-modal/>
@endsection
