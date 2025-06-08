@extends('layouts.app')

@section('content')

<!-- Back Button -->
<a href="{{ url('report/index') }}" class="flex items-center text-sm text-gray-500 hover:text-[#2d326b] mb-4">
    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
    </svg>
    Production Report
</a>

<!-- Card Container -->
<div class="bg-white rounded-sm border border-gray-200 p-6 shadow-md space-y-5 transition-all duration-300 hover:shadow-xl hover:border-[#E5E7EB]">

    <!-- Edit Production Report Form -->
    <form id="update-report-form" action="{{ route('report.update', $report->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Title -->
        <div class="flex-1 text-center">
            <h2 class="text-2xl m-4 font-bold text-[#2d326b]">Edit Production Report</h2>
        </div>

        <div class="space-y-5">

            <!-- Basic Production Details Header -->
            <div class="mb-6 flex items-center justify-between">
                <h4 class="text-lg font-semibold text-[#2d326b]">Basic Production Details</h4>
                <button type="button"
                    id="open-update-modal-btn"
                    class="inline-flex items-center gap-2 p-3 py-2 bg-[#323B76] hover:bg-[#444d90] border border-[#323B76] text-white text-sm font-medium rounded-md">
                    Update Report
                </button>
            </div>

            <!-- Basic Production Form Table -->
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
                        <td class="px-4 py-2">
                            <x-select-dropdown name="sku" value="{{ old('sku', $report->sku) }}" :options="$skus->pluck('description', 'description')->toArray()" placeholder="Select SKU" required />
                        </td>
                        <td class="font-medium text-[#2d326b] px-4 py-2">Production Date</td>
                        <td class="px-4 py-2">
                            <input type="date" name="production_date" value="{{ old('production_date', $report->production_date) }}" class="w-full border border-gray-300 rounded px-3 py-1 text-sm" required>
                        </td>
                    </tr>
                    <tr>
                        <td class="font-medium text-[#2d326b] px-4 py-2">Shift</td>
                        <td class="px-4 py-2">
                            <x-select-dropdown name="shift" value="{{ old('shift', $report->shift) }}" :options="['00:00H - 24:00H' => '00:00H - 24:00H']" required />
                        </td>
                        <td class="font-medium text-[#2d326b] px-4 py-2">AC Temperatures</td>
                        <td class="px-4 py-2">
                            <div class="grid grid-cols-4 gap-1">
                                @for ($i = 1; $i <= 4; $i++)
                                    <input 
                                        type="text" 
                                        name="ac{{ $i }}" 
                                        placeholder="AC {{ $i }}" 
                                        value="{{ old('ac' . $i, $report->{'ac' . $i}) }}" 
                                        class="w-full border border-gray-300 rounded placeholder-gray-400 px-2 py-1 text-sm text-center"
                                    >
                                @endfor
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="font-medium text-[#2d326b] px-4 py-2">Line #</td>
                        <td class="px-4 py-2">
                            <x-select-dropdown name="line" value="{{ old('line', $report->line) }}" :options="$lineOptions->toArray()" required />
                        </td>
                        <td class="font-medium text-[#2d326b] px-4 py-2">Total Output (Cases)</td>
                        <td class="px-4 py-2">
                            <input type="text" name="total_outputCase" value="{{ old('total_outputCase', $report->total_outputCase) }}" class="w-full border border-gray-300 placeholder-gray-400 rounded px-3 py-1 text-sm text-center">
                        </td>
                    </tr>
                    <tr>
                        <td class="font-medium text-[#2d326b] px-4 py-2">FBO/FCO</td>
                        <td class="px-4 py-2">
                            <x-select-dropdown name="fbo_fco" value="{{ old('fbo_fco', $report->fbo_fco) }}"  :options="['00:00H - 00:00H' => '00:00H - 00:00H']" />
                        </td>
                        <td class="font-medium text-[#2d326b] px-4 py-2">LBO/LCO</td>
                        <td class="px-4 py-2">
                            <x-select-dropdown name="lbo_lco" value="{{ old('lbo_lco', $report->lbo_lco) }}" :options="['24:00H - 24:00H' => '24:00H - 24:00H']" />
                        </td>
                    </tr>
                    <tr>
                        <td class="font-medium text-[#2d326b] px-4 py-2">Manpower Present</td>
                        <td class="px-4 py-2">
                            <input type="text" name="manpower_present" value="{{ old('manpower_present', $report->manpower_present) }}" class="w-full border border-gray-300 rounded px-3 py-1 text-sm text-center">
                        </td>
                        <td class="font-medium text-[#2d326b] px-4 py-2">Manpower Absent</td>
                        <td class="px-4 py-2">
                            <input type="text" name="manpower_absent" value="{{ old('manpower_absent', $report->manpower_absent) }}" class="w-full border border-gray-300 rounded px-3 py-1 text-sm text-center">
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Filling Line and Blow Molding Section -->
            <h4 class="text-lg font-semibold text-[#2d326b]">Filling Line and Blow Molding</h4>
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
                            <input type="text" name="filler_speed" value="{{ old('filler_speed', $report->filler_speed) }}" class="w-full border border-gray-300 placeholder-gray-400 rounded px-2 py-1 text-sm text-center">
                        </td>
                        <td class="font-medium text-[#2d326b] px-4 py-2 text-xs">RM Rejects <br><span class="text-sm">Opp/Labels</span></td>
                        <td class="px-4 py-2">
                            <input type="text" name="opp_labels" value="{{ old('opp_labels', $report->opp_labels) }}" class="w-full border border-gray-300 placeholder-gray-400 rounded px-2 py-1 text-sm text-center">
                        </td>
                        <td class="font-medium text-[#2d326b] px-4 py-2">Bottle</td>
                        <td class="px-4 py-2">
                            <input type="text" name="bottle_filling" value="{{ old('bottle_filling', $report->bottle_filling) }}" class="w-full border border-gray-300 placeholder-gray-400 rounded px-2 py-1 text-sm text-center">
                        </td>
                    </tr>
                    <tr>
                        <td class="font-medium text-[#2d326b] px-4 py-2">OPP/Labeler Speed</td>
                        <td class="px-4 py-2">
                            <input type="text" name="opp_labeler_speed" value="{{ old('opp_labeler_speed', $report->opp_labeler_speed) }}" class="w-full border border-gray-300 placeholder-gray-400 rounded px-2 py-1 text-sm text-center">
                        </td>
                        <td class="font-medium text-[#2d326b] px-4 py-2">Shrinkfilm</td>
                        <td class="px-4 py-2">
                            <input type="text" name="shrinkfilm" value="{{ old('shrinkfilm', $report->shrinkfilm) }}" class="w-full border border-gray-300 placeholder-gray-400 rounded px-2 py-1 text-sm text-center">
                        </td>
                        <td class="font-medium text-[#2d326b] px-4 py-2">Caps</td>
                        <td class="px-4 py-2">
                            <input type="text" name="caps_filling" value="{{ old('caps_filling', $report->caps_filling) }}" class="w-full border border-gray-300 placeholder-gray-400 rounded px-2 py-1 text-sm text-center">
                        </td>
                    </tr>
                    <!-- Blow Molding Section Header -->
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
                            <input type="text" name="blow_molding_output" value="{{ old('blow_molding_output', $report->blow_molding_output) }}"  class="w-full border border-gray-300 placeholder-gray-400 rounded px-2 py-1 text-sm text-center">
                        </td>
                        <td class="font-medium text-[#2d326b] px-4 py-2 text-xs">Blow Molding Rejects <span class="text-sm">Preform</span></td>
                        <td class="px-4 py-2">
                            <input type="text" name="preform_blow_molding" value="{{ old('preform_blow_molding', $report->preform_blow_molding) }}"  class="w-full border border-gray-300 placeholder-gray-400 rounded px-2 py-1 text-sm text-center">
                        </td>
                        <td class="font-medium text-[#2d326b] px-4 py-2">Bottles</td>
                        <td class="px-4 py-2">
                            <input type="text" name="bottles_blow_molding" value="{{ old('bottles_blow_molding', $report->bottles_blow_molding) }}"  class="w-full border border-gray-300 placeholder-gray-400 rounded px-2 py-1 text-sm text-center">
                        </td>
                    </tr>
                    <tr>
                        <td class="font-medium text-[#2d326b] px-4 py-2">Speed (Bottles/Hour)</td>
                        <td class="px-4 py-2">
                            <input type="text" name="speed_blow_molding" value="{{ old('speed_blow_molding', $report->speed_blow_molding) }}"  class="w-full border border-gray-300 placeholder-gray-400 rounded px-2 py-1 text-sm text-center">
                        </td>
                        <td colspan="4"></td>
                    </tr>
                </tbody>
            </table>

            <!-- Issues/Down Time/Remarks Section -->
            <div x-data="issueTable()" class="space-y-4">
                <h4 class="text-lg font-semibold text-[#2d326b]">Issues/ Down Time / Remarks</h4>
                <table class="min-w-full text-sm border border-gray-200 shadow-sm">
                    <thead class="bg-gray-100 text-[#2d326b]">
                        <tr>
                            <th class="text-left px-4 py-3 text-center w-1/4">Machine / Others</th>
                            <th class="text-left px-4 py-3 text-center">Description</th>
                            <th class="text-left px-4 py-3 text-center w-1/12">Minutes</th>
                            <th class="text-left px-4 py-3 text-center w-1/12">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Dynamic Issue Rows -->
                        <template x-for="(issue, index) in issues" :key="index">
                            <tr>
                                <td class="px-4 py-2">
                                    <x-select-material name="materials[]" :options="$materialsOptions" />
                                </td>
                                <td class="px-2 py-2">
                                    <input type="text" :name="`description[]`" x-model="issue.description"
                                        class="w-full border border-gray-300 placeholder-gray-400 rounded px-2 py-1 text-sm text-center">
                                </td>
                                <td class="px-2 py-2 text-center">
                                    <input type="text" :name="`minutes[]`" x-model="issue.minutes" placeholder="mins"
                                        class="w-20 border border-gray-300 placeholder-gray-400 rounded  px-2 py-1 text-sm text-center">
                                </td>
                                <td class="px-2 py-2 text-center">
                                    <button type="button" @click="removeIssue(index)"
                                        class="text-red-600 border border-red-600 py-1 px-2 rounded hover:text-red-800 text-sm font-medium">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <!-- Add Issue Button -->
                        <tr>
                            <td></td>
                            <td class="px-4 py-3 text-center">
                                <button type="button" @click="addIssue()"
                                    class="inline-flex items-center gap-2 bg-[#323B76] hover:bg-[#2d326b] text-white p-2 text-xs rounded shadow">
                                    Add Issue
                                </button>
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>

                <!-- QA Remarks and QC Rejects Section -->
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
                                <x-select-dropdown name="qa_remarks" 
                                    value="{{ old('qa_remarks', $report->qa_remarks) }}"
                                    :options="[ 'Passed' => 'Passed']" />
                            </td>
                            <td class="font-medium text-[#2d326b] px-4 py-2">With Label</td>
                            <td class="px-4 py-2">
                                <input type="text" name="with_label" value="{{ old('with_label', $report->with_label) }}" placeholder="pcs" class="w-full border border-gray-300 placeholder-gray-400 rounded px-2 py-1 text-sm text-center">
                            </td>
                            <td class="font-medium text-[#2d326b] px-4 py-2">Without Label</td>
                            <td class="px-4 py-2">
                                <input type="text" name="without_label" value="{{ old('without_label', $report->without_label) }}" placeholder="pcs" class="w-full border border-gray-300 placeholder-gray-400 rounded px-2 py-1 text-sm text-center">
                            </td>
                        </tr>
                <!-- Line QC Rejects Section Header -->
                <thead class="bg-gray-100 text-[#2d326b]">
                    <tr>
                        <th colspan="6" class="text-left px-4 py-3">Line QC Rejects</th>
                    </tr>
                </thead>
                <tr>
                    <td colspan="6">
                        <div class="grid md:grid-cols-2 gap-2">
                            @foreach (['Caps', 'Bottle', 'Label', 'Carton'] as $category)
                                <div class="pl-3 pr-3 p-3 border-l border-r border-gray-200 flex flex-col gap-2">
                                    <!-- QC Rejects Category Header and Add Button -->
                                    <div class="flex items-center justify-between">
                                        <h5 class="text-sm font-bold text-[#2d326b]">{{ $category }}</h5>
                                        <button type="button"
                                            class="text-xs px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700"
                                            @click="addQcReject('{{ $category }}')">
                                            Add
                                        </button>
                                    </div>

                                    <!-- Dynamic QC Rejects Items -->
                                    <template x-for="(item, index) in form.qcRejects['{{ $category }}']" :key="index">
                                        <div class="flex items-center gap-2">
                                            <x-select-defect
                                                class="w-[160px]"
                                                :name="'qc_' . strtolower($category) . '_defect[]'"
                                                :options="$defects->where('category', $category)->pluck('defect_name', 'defect_name')->toArray()"
                                                x-init="$watch('item.defect', value => $el.querySelector('select').value = value)"
                                                @change="item.defect = $event.target.value"
                                            />
                                            <input type="text" x-model="item.qty" name="qc_{{ strtolower($category) }}_qty[]"
                                                placeholder="pcs"
                                                class="w-[50px] text-sm text-center rounded-md border border-gray-300 placeholder-gray-400 shadow-sm focus:ring-[#323B76] focus:border-[#323B76]">
                                            <button type="button"
                                                @click="removeQcReject('{{ $category }}', index)"
                                                :class="form.qcRejects['{{ $category }}'].length > 1 ? 'visible' : 'invisible'"
                                                class="w-[32px] h-[20px] text-white bg-red-600 rounded hover:bg-red-700 text-sm flex items-center justify-center">
                                                ×
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            @endforeach
                        </div>
                    </td>
                </tr>
                    </tbody>
                </table>

                <!-- Alpine.js Data and Methods for Dynamic Sections -->
                <script>
                    function issueTable() {
                        return {
                            // Issues array for dynamic issue rows
                            issues: @json($issues),
                            // QC Rejects data structure for each category
                            form: {
                                qcRejects: {
                                    'Caps': @json($qcRejects['Caps']),
                                    'Bottle': @json($qcRejects['Bottle']),
                                    'Label': @json($qcRejects['Label']),
                                    'Carton': @json($qcRejects['Carton']),
                                }
                            },
                            // Add new issue row
                            addIssue() {
                                this.issues.push({ material: '', description: '', minutes: '' });
                            },
                            // Remove issue row
                            removeIssue(index) {
                                this.issues.splice(index, 1);
                            },
                            // Add new QC reject row for a category
                            addQcReject(category) {
                                this.form.qcRejects[category].push({ defect: '', qty: '' });
                            },
                            // Remove QC reject row for a category
                            removeQcReject(category, index) {
                                if (this.form.qcRejects[category].length > 1) {
                                    this.form.qcRejects[category].splice(index, 1);
                                }
                            }
                        };
                    }
                </script>
            </div>
        </div>
    </form>
</div>

<!-- Update Modal Component -->
<x-update-modal/>

@endsection
