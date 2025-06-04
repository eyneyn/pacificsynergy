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
    <form action="{{ route('report.store') }}" method="POST">
        @csrf

        <!-- Title -->
        <div class="flex-1 text-center">
            <h2 class="text-2xl m-4 font-bold text-[#2d326b]">Daily Production Report</h2>
        </div>

        <div class="space-y-5">
            <!-- Header -->
            <div class="mb-6 flex items-center justify-between">
                <h4 class="text-lg font-semibold text-[#2d326b]">Basic Production Details</h4>
                <button type="submit"
                    class="inline-flex items-center gap-2 p-3 py-2 bg-[#323B76] hover:bg-[#444d90] border border-[#323B76] text-white text-sm font-medium rounded-md">
                    Save Report
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
                            <x-select-dropdown name="sku" :options="$skus->pluck('description', 'description')->toArray()" placeholder="Select SKU" required />
                        </td>
                        <td class="font-medium text-[#2d326b] px-4 py-2">Production Date</td>
                        <td class="px-4 py-2">
                            <input type="date" name="production_date" class="w-full border border-gray-300 rounded px-3 py-1 text-sm" required>
                        </td>
                    </tr>
                    <tr>
                        <td class="font-medium text-[#2d326b] px-4 py-2">Shift</td>
                        <td class="px-4 py-2">
                            <x-select-dropdown name="shift" :options="['00:00H - 24:00H' => '00:00H - 24:00H']" required />
                        </td>
                        <td class="font-medium text-[#2d326b] px-4 py-2">AC Temperatures</td>
                        <td class="px-4 py-2">
                            <div class="grid grid-cols-4 gap-1">
                                @for ($i = 1; $i <= 4; $i++)
                                    <input type="text" name="ac{{ $i }}" placeholder="AC {{ $i }}" class="w-full border border-gray-300 rounded placeholder-gray-400 px-2 py-1 text-sm text-center">
                                @endfor
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="font-medium text-[#2d326b] px-4 py-2">Line #</td>
                        <td class="px-4 py-2">
                            <x-select-dropdown name="line" :options="$lineOptions->toArray()" placeholder="Select Line" required />
                        </td>
                        <td class="font-medium text-[#2d326b] px-4 py-2">Total Output (Cases)</td>
                        <td class="px-4 py-2">
                            <input type="text" name="total_outputCase" placeholder="cases" class="w-full border border-gray-300 placeholder-gray-400 rounded px-3 py-1 text-sm text-center">
                        </td>
                    </tr>
                    <tr>
                        <td class="font-medium text-[#2d326b] px-4 py-2">FBO/FCO</td>
                        <td class="px-4 py-2">
                            <x-select-dropdown name="fbo_fco" :options="['00:00H - 00:00H' => '00:00H - 00:00H']" />
                        </td>
                        <td class="font-medium text-[#2d326b] px-4 py-2">LBO/LCO</td>
                        <td class="px-4 py-2">
                            <x-select-dropdown name="lbo_lco" :options="['24:00H - 24:00H' => '24:00H - 24:00H']" />
                        </td>
                    </tr>
                    <tr>
                        <td class="font-medium text-[#2d326b] px-4 py-2">Manpower Present</td>
                        <td class="px-4 py-2">
                            <input type="text" name="manpower_present" class="w-full border border-gray-300 rounded px-3 py-1 text-sm text-center">
                        </td>
                        <td class="font-medium text-[#2d326b] px-4 py-2">Manpower Absent</td>
                        <td class="px-4 py-2">
                            <input type="text" name="manpower_absent" class="w-full border border-gray-300 rounded px-3 py-1 text-sm text-center">
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
                            <input type="text" name="filler_speed" placeholder="bph" class="w-full border border-gray-300 placeholder-gray-400 rounded px-2 py-1 text-sm text-center">
                        </td>
                        <td class="font-medium text-[#2d326b] px-4 py-2 text-xs">RM Rejects <br><span class="text-sm">Opp/Labels</span></td>
                        <td class="px-4 py-2">
                            <input type="text" name="opp_labels" placeholder="pcs" class="w-full border border-gray-300 placeholder-gray-400 rounded px-2 py-1 text-sm text-center">
                        </td>
                        <td class="font-medium text-[#2d326b] px-4 py-2">Bottle</td>
                        <td class="px-4 py-2">
                            <input type="text" name="bottle_filling" placeholder="pcs" class="w-full border border-gray-300 placeholder-gray-400 rounded px-2 py-1 text-sm text-center">
                        </td>
                    </tr>
                    <tr>
                        <td class="font-medium text-[#2d326b] px-4 py-2">OPP/Labeler Speed</td>
                        <td class="px-4 py-2">
                            <input type="text" name="opp_labeler_speed" placeholder="0" class="w-full border border-gray-300 placeholder-gray-400 rounded px-2 py-1 text-sm text-center">
                        </td>
                        <td class="font-medium text-[#2d326b] px-4 py-2">Shrinkfilm</td>
                        <td class="px-4 py-2">
                            <input type="text" name="shrinkfilm" placeholder="pcs" class="w-full border border-gray-300 placeholder-gray-400 rounded px-2 py-1 text-sm text-center">
                        </td>
                        <td class="font-medium text-[#2d326b] px-4 py-2">Caps</td>
                        <td class="px-4 py-2">
                            <input type="text" name="caps_filling" placeholder="pcs" class="w-full border border-gray-300 placeholder-gray-400 rounded px-2 py-1 text-sm text-center">
                        </td>
                    </tr>
                </tbody>
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
                <tbody>
                    <tr>
                        <td class="font-medium text-[#2d326b] px-4 py-2">Blow Molding Output</td>
                        <td class="px-4 py-2">
                            <input type="text" name="blow_molding_output" placeholder="pcs" class="w-full border border-gray-300 placeholder-gray-400 rounded px-2 py-1 text-sm text-center">
                        </td>
                        <td class="font-medium text-[#2d326b] px-4 py-2 text-xs">Blow Molding Rejects <span class="text-sm">Preform</span></td>
                        <td class="px-4 py-2">
                            <input type="text" name="speed_blow_molding" placeholder="pcs" class="w-full border border-gray-300 placeholder-gray-400 rounded px-2 py-1 text-sm text-center">
                        </td>
                        <td class="font-medium text-[#2d326b] px-4 py-2">Bottles</td>
                        <td class="px-4 py-2">
                            <input type="text" name="preform_blow_molding" placeholder="pcs" class="w-full border border-gray-300 placeholder-gray-400 rounded px-2 py-1 text-sm text-center">
                        </td>
                    </tr>
                    <tr>
                        <td class="font-medium text-[#2d326b] px-4 py-2">Speed (Bottles/Hour)</td>
                        <td class="px-4 py-2">
                            <input type="text" name="bottles_blow_molding" placeholder="bph" class="w-full border border-gray-300 placeholder-gray-400 rounded px-2 py-1 text-sm text-center">
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
                                    <x-select-dropdown 
                                        :name="'materials[]'" 
                                        :options="$materialsOptions->toArray()" 
                                        placeholder="Select machine" 
                                        required 
                                    />
                                </td>
                                <td class="px-2 py-2">
                                    <input type="text" :name="`description[]`" placeholder="Describe the issue or remark"
                                        class="w-full border border-gray-300 placeholder-gray-400 rounded px-2 py-1 text-sm text-center">
                                </td>
                                <td class="px-2 py-2 text-center">
                                    <input type="text" :name="`minutes[]`" placeholder="mins"
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
                                <x-select-dropdown name="qa_remarks" :options="['Passed' => 'Passed']" />
                            </td>
                            <td class="font-medium text-[#2d326b] px-4 py-2">With Label</td>
                            <td class="px-4 py-2">
                                <input type="text" name="with_label" placeholder="pcs" class="w-full border border-gray-300 placeholder-gray-400 rounded px-2 py-1 text-sm text-center">
                            </td>
                            <td class="font-medium text-[#2d326b] px-4 py-2">Without Label</td>
                            <td class="px-4 py-2">
                                <input type="text" name="without_label" placeholder="pcs" class="w-full border border-gray-300 placeholder-gray-400 rounded px-2 py-1 text-sm text-center">
                            </td>
                        </tr>
                    </tbody>
                    <!-- Line QC Rejects Section -->
                    <thead class="bg-gray-100 text-[#2d326b]">
                        <tr>
                            <th colspan="6" class="text-left px-4 py-3">Line QC Rejects</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="6">
                                <div class="grid md:grid-cols-4 gap-2">
                                    @foreach (['Caps', 'Bottle', 'Label', 'Carton'] as $category)
                                        <div class="pl-3 pr-3 p-3 border-l border-r border-gray-200 flex flex-col gap-2">
                                            <!-- Category Header with Add Button -->
                                            <div class="flex items-center justify-between">
                                                <h5 class="text-sm font-bold text-[#2d326b]">{{ $category }}</h5>
                                                <button type="button"
                                                    class="text-xs px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700"
                                                    @click="addQcReject('{{ $category }}')">
                                                    Add
                                                </button>
                                            </div>
                                            <!-- Dynamic QC Reject Items -->
                                            <template x-for="(item, index) in form.qcRejects['{{ $category }}']" :key="index">
                                                <div class="flex items-center gap-2">
                                                    <!-- Defect Select -->
                                                    <x-select-defects
                                                        class="w-[160px]"
                                                        :name="'qc_' . strtolower($category) . '_defect[]'"
                                                        :options="$defects->where('category', $category)->pluck('defect_name', 'defect_name')->toArray()"
                                                        x-init="$watch('item.defect', value => $el.querySelector('select').value = value)"
                                                        @change="item.defect = $event.target.value"
                                                    />
                                                    <!-- Quantity Input -->
                                                    <input type="text" x-model="item.qty" name="qc_{{ strtolower($category) }}_qty[]"
                                                        placeholder="pcs"
                                                        class="w-[50px] text-sm text-center rounded-md border border-gray-300 placeholder-gray-400 shadow-sm focus:ring-[#323B76] focus:border-[#323B76]">
                                                    <!-- Delete Button -->
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
            </div>
        </div>
    </form>
</div>

<!-- Alpine.js Data for Dynamic Tables -->
<script>
    // Alpine.js component for handling dynamic issue and QC reject rows
    function issueTable() {
        return {
            issues: [{}], // Initial issue row
            form: {
                qcRejects: {
                    'Caps': [{ defect: '', qty: '' }],
                    'Bottle': [{ defect: '', qty: '' }],
                    'Label': [{ defect: '', qty: '' }],
                    'Carton': [{ defect: '', qty: '' }]
                }
            },
            addIssue() {
                this.issues.push({});
            },
            removeIssue(index) {
                this.issues.splice(index, 1);
            },
            addQcReject(category) {
                this.form.qcRejects[category].push({ defect: '', qty: '' });
            },
            removeQcReject(category, index) {
                if (this.form.qcRejects[category].length > 1) {
                    this.form.qcRejects[category].splice(index, 1);
                }
            }
        };
    }
</script>
@endsection
