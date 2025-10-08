@extends('layouts.app')
@section('title', content: 'Production Report')
@section('content')

<div class="container mx-auto px-4">

    {{-- 🔔 Modal Alerts (Success, Error, Validation) --}}
    <x-alert-message />

    <form action="{{ route('report.store') }}" method="POST">
        @csrf

        <!-- Title -->
        <div class="flex-1 text-center">
            <h2 class="text-2xl m-4 font-bold text-[#23527c]">Daily Production Report</h2>
        </div>

        <div class="space-y-5">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <a href="{{ url('report/index') }}" class="inline-flex items-center px-3 py-2 bg-[#5a9fd4] hover:bg-[#4a8bc2] text-white text-sm font-medium transition-colors duration-200 border border-[#4590ca] hover:border-[#4a8bc2]">
                        <x-icons-back class="w-2 h-2 text-white" />
                        Back
                    </a>

                    <div class="flex items-center gap-2">
                        <button
                            type="submit"
                            name="mode"
                            value="no_report"
                            formnovalidate
                            id="btnNoReport"
                            class="inline-flex items-center justify-center gap-1 bg-gray-500 border border-gray-600 text-white px-3 py-2 hover:bg-gray-600 text-sm"
                            title="No Run entry for the selected date & line"
                        >
                            No Run
                        </button>

                        <button
                            type="submit"
                            name="mode"
                            value="save"
                            class="inline-flex items-center justify-center gap-1 bg-[#5bb75b] border border-[#43a143] text-white px-3 py-2 hover:bg-[#42a542] text-sm"
                        >
                            <x-icons-save class="w-2 h-2 text-white" />
                            Save
                        </button>
                    </div>
                </div>

                {{-- Description requirements --}}
                <div class="mt-10">
                    <ul class="space-y-1 text-red-500 text-sm">
                        <li class="text-[#42a542]">Fill out all required fields — details marked with an asterisk (*) are mandatory.</li>
                        <li>If there is <span class="font-bold">“No Run</span>, please specify the production line and date first.</li>
                    </ul>
                </div>
            </div>

            <!-- Basic Production Form Table -->
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
                        <td class="font-medium text-[#23527c] px-4 py-2">Running SKU <span style="color: red;">*</span></td>
                        <td class="px-4 py-2">
                        <x-select-dropdown 
                            name="sku_id" 
                            :options="$skus->pluck('description', 'id')->toArray()" 
                            :selected="old('sku_id')" 
                            placeholder="Select SKU" 
                            required 
                        />                        </td>
                        <td class="font-medium text-[#23527c] px-4 py-2">Production Date <span style="color: red;">*</span></td>
                        <td class="px-4 py-2">
                            <input type="date" name="production_date" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm" value="{{ old('production_date') }}" required>                       
                        </td>
                    </tr>
                    <tr>
                        <td class="font-medium text-[#23527c] px-4 py-2">Shift <span style="color: red;">*</span></td>
                        <td class="px-4 py-2">
                            <input 
                                type="text" 
                                name="shift" 
                                class="text-sm  px-2 py-1 w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400" 
                                placeholder="Format must be like 00:00H/00:00H"
                                value="{{ old('shift') }}"
                                pattern="\d{2}:\d{2}H/\d{2}:\d{2}H"
                                title="Format must be like 00:00H/00:00H"
                            />
                        </td>
                        <td class="font-medium text-[#23527c] px-4 py-2">AC Temperatures <span style="color: red;">*</span></td>
                        <td class="px-4 py-2">
                            <div class="grid grid-cols-4 gap-1">
                                @for ($i = 1; $i <= 4; $i++)
                                    <input type="text" name="ac{{ $i }}" placeholder="AC {{ $i }}" value="{{ old('ac' . $i) }}" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm ">
                                @endfor
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="font-medium text-[#23527c] px-4 py-2">Line # <span style="color: red;">*</span></td>
                        <td class="px-4 py-2">
                            <x-select-dropdown name="line" :options="$lineOptions->toArray()" :selected="old('line')" placeholder="Select Line" required />
                        </td>
                        <td class="font-medium text-[#23527c] px-4 py-2">Total Output (Cases) <span style="color: red;">*</span></td>
                        <td class="px-4 py-2">
                            <input type="text" name="total_outputCase" placeholder="cases" value="{{ old('total_outputCase') }}" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm ">
                        </td>
                    </tr>
                    <tr>
                        <td class="font-medium text-[#23527c] px-4 py-2">FBO/FCO <span style="color: red;">*</span></td>
                        <td class="px-4 py-2">
                            <input 
                                type="text" 
                                name="fbo_fco" 
                                class="text-sm  px-2 py-1 w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400" 
                                placeholder="Format must be like 00:00H/00:00H"
                                value="{{ old('fbo_fco') }}"
                                pattern="\d{2}:\d{2}H/\d{2}:\d{2}H"
                                title="Format must be like 00:00H/00:00H"
                            />
                        </td>
                        <td class="font-medium text-[#23527c] px-4 py-2">LBO/LCO <span style="color: red;">*</span></td>
                        <td class="px-4 py-2">
                            <input 
                                type="text" 
                                name="lbo_lco" 
                                class="text-sm px-2 py-1 w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400" 
                                placeholder="Format must be like 00:00H/00:00H"
                                value="{{ old('lbo_lco') }}"
                                pattern="\d{2}:\d{2}H/\d{2}:\d{2}H"
                                title="Format must be like 00:00H/00:00H"
                            /> 
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Filling Line and Blow Molding Section -->
            <table class="min-w-full text-sm border border-[#E5E7EB] shadow-sm">
                <thead class="uppercase text-[#23527c] bg-[#e2f2ff]">
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
                        <td class="font-medium text-[#23527c] px-4 py-2 text-xs">Speed (Bottles per Hour) <br><span class="text-sm">Filler Speed <span style="color: red;">*</span></span></td>
                        <td class="px-4 py-2">
                            <input type="text" name="filler_speed" placeholder="bph" value="{{ old('filler_speed') }}" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm ">
                        </td>
                        <td class="font-medium text-[#23527c] px-4 py-2 text-xs">RM Rejects <br><span class="text-sm">Opp/Labels <span style="color: red;">*</span></span></td>
                        <td class="px-4 py-2">
                            <input type="text" name="opp_labels" placeholder="pcs" value="{{ old('opp_labels') }}" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm ">
                        </td>
                        <td class="font-medium text-[#23527c] px-4 py-2">Caps <span style="color: red;">*</span></td>
                        <td class="px-4 py-2">
                            <input type="text" name="caps_filling" placeholder="pcs" value="{{ old('caps_filling') }}" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm ">
                        </td>
                    </tr>
                    <tr>
                        <td class="font-medium text-[#23527c] px-4 py-2">OPP/Labeler Speed <span style="color: red;">*</span></td>
                        <td class="px-4 py-2">
                            <input type="text" name="opp_labeler_speed" placeholder="0" value="{{ old('opp_labeler_speed') }}" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm ">
                        </td>
                        <td class="font-medium text-[#23527c] px-4 py-2">Shrinkfilm <span style="color: red;">*</span></td>
                        <td class="px-4 py-2">
                            <input type="text" name="shrinkfilm" placeholder="pcs" value="{{ old('shrinkfilm') }}" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm ">
                        </td>                        
                        <td class="font-medium text-[#23527c] px-4 py-2">Bottle <span style="color: red;">*</span></td>
                        <td class="px-4 py-2">
                            <input type="text" name="bottle_filling" placeholder="pcs" value="{{ old('bottle_filling') }}" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm ">
                        </td>
                    </tr>
                </tbody>
                <!-- Blow Molding Section -->
                <thead class="uppercase text-[#23527c] bg-[#e2f2ff]">
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
                        <td class="font-medium text-[#23527c] px-4 py-2">Blow Molding Output <span style="color: red;">*</span></td>
                        <td class="px-4 py-2">
                            <input type="text" name="blow_molding_output" placeholder="pcs" value="{{ old('blow_molding_output') }}" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm ">
                        </td>
                        <td class="font-medium text-[#23527c] px-4 py-2 text-xs">Blow Molding Rejects <br><span class="text-sm">Preform <span style="color: red;">*</span></span></td>
                        <td class="px-4 py-2">
                            <input type="text" name="preform_blow_molding" placeholder="pcs" value="{{ old('preform_blow_molding') }}" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm ">
                        </td>
                        <td class="font-medium text-[#23527c] px-4 py-2">Bottles <span style="color: red;">*</span></td>
                        <td class="px-4 py-2">
                            <input type="text" name="bottles_blow_molding" placeholder="pcs" value="{{ old('bottles_blow_molding') }}" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm ">
                        </td>
                    </tr>
                    <tr>
                        <td class="font-medium text-[#23527c] px-4 py-2">Speed (Bottles/Hour) <span style="color: red;">*</span></td>
                        <td class="px-4 py-2">
                            <input type="text" name="speed_blow_molding" placeholder="bph" value="{{ old('speed_blow_molding') }}" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm ">
                        </td>
                        <td colspan="4"></td>
                    </tr>
                </tbody>
            </table>

            <!-- Issues/Down Time/Remarks Section -->
            <div x-data="issueTable()" class="space-y-4">
                <table class="min-w-full text-sm border border-[#E5E7EB] shadow-sm">
                    <thead class="uppercase text-[#23527c] bg-[#e2f2ff]">
                        <tr>
                            <th class="text-left px-4 py-3 text-center w-1/4">Machine / Others</th>
                            <th class="text-left px-4 py-3 text-center">Description</th>
                            <th class="text-left px-4 py-3 text-center w-1/12">Minutes</th>
                            <th class="text-left px-4 py-3 text-center w-1/12">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Dynamic Issue Rows -->
                        <template x-for="issue in issues" :key="issue._uid">
                            <tr>
                                <td class="px-4 py-2">
                                    <x-select-dropdown
                                        :name="'materials[]'"
                                        :options="$materialsOptions->toArray()"
                                        placeholder="Select machine"
                                        :selected="'issue.material'"
                                        @change="issue.material = $event.target.value"
                                        required
                                    />
                                </td>
                                <td class="px-2 py-2">
                                    <input type="text" :name="`description[]`" x-model="issue.description" placeholder="Describe the issue or remark"
                                        class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm ">
                                </td>
                                <td class="px-2 py-2 ">
                                    <input type="text" :name="`minutes[]`" x-model="issue.minutes" placeholder="mins"
                                        class="w-20 border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm ">
                                </td>
                                <td class="px-2 py-2 ">
                                    <button type="button" @click="removeIssue(issue._uid)"
                                        class="bg-red-600 hover:bg-red-700 border border-red-700 text-white py-1 px-2 text-sm font-medium">
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
                                    class="inline-flex items-center gap-2 bg-[#323B76] hover:bg-[#444d90] text-white p-2 text-xs  shadow">
                                    Add Issue
                                </button>
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>

                <!-- QA Remarks and QC Rejects Section -->
                <table class="min-w-full text-sm border border-[#E5E7EB] shadow-sm">
                    <thead class="uppercase text-[#23527c] bg-[#e2f2ff]">
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
                            <td class="font-medium text-[#23527c] px-4 py-2">Ozone <span style="color: red;">*</span></td>
                            <td class="px-4 py-2">
                                <x-select-dropdown name="qa_remarks" :options="['Passed' => 'Passed']" />
                            </td>
                            <td class="font-medium text-[#23527c] px-4 py-2">With Label <span style="color: red;">*</span></td>
                            <td class="px-4 py-2">
                                <input type="text" name="with_label" placeholder="pcs" value="{{ old('with_label') }}" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm ">
                            </td>
                            <td class="font-medium text-[#23527c] px-4 py-2">Without Label <span style="color: red;">*</span></td>
                            <td class="px-4 py-2">
                                <input type="text" name="without_label" placeholder="pcs" value="{{ old('without_label') }}" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm ">
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Line QC Rejects Section -->
                <table class="min-w-full text-sm border border-[#E5E7EB] shadow-sm mt-6">
                    <thead class="uppercase text-[#23527c] bg-[#e2f2ff]">
                        <tr>
                            <th colspan="3" class="text-left px-4 py-3">Line QC Rejects</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- QC Rejects -->
                        <template x-for="reject in qcRejects" :key="reject._uid">
                            <tr>
                                <td class="px-4 py-2 w-full">
                                    <x-select-defects
                                        class="w-full"
                                        :name="'qc_defect[]'"
                                        :options="$defects->pluck('defect_name', 'id')->toArray()"
                                        x-init="$watch('reject.defect', value => $el.querySelector('select').value = value)"
                                        @change="reject.defect = $event.target.value"
                                    />
                                </td>

                                <td class="px-4 py-2  w-28">
                                    <input type="text" :name="`qc_qty[]`" x-model="reject.qty" placeholder="pcs"
                                        class="w-20 border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm ">
                                </td>

                                <td class="px-4 py-2  w-32">
                                    <button type="button" @click="removeQcReject(reject._uid)"
                                        class="bg-red-600 hover:bg-red-700 border border-red-700 text-white py-1 px-3 text-sm font-medium">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <!-- Add Reject Button -->
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-center">
                                <button type="button" @click="addQcReject()"
                                    class="inline-flex items-center gap-2 bg-[#323B76] hover:bg-[#444d90] text-white p-2 text-xs shadow">
                                    Add Reject
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>

<script>
function issueTable() {
    return {
        issues: [],
        qcRejects: [],

        // --- Helpers ---
        uid() {
            return Math.random().toString(36).substr(2, 9);
        },
        makeIssue(partial = {}) {
            return {
                _uid: this.uid(),
                material: partial.material ?? '',
                description: partial.description ?? '',
                minutes: partial.minutes ?? ''
            };
        },
        makeQcReject(partial = {}) {
            return {
                _uid: this.uid(),
                defect: partial.defect ?? '',
                qty: partial.qty ?? ''
            };
        },

        // --- Init ---
        init() {
            // Initialize with 1 blank row if none exist
            if (!Array.isArray(this.issues) || this.issues.length === 0) {
                this.issues = [this.makeIssue()];
            } else {
                this.issues = this.issues.map(i => this.makeIssue(i));
            }

            if (!Array.isArray(this.qcRejects) || this.qcRejects.length === 0) {
                this.qcRejects = [this.makeQcReject()];
            } else {
                this.qcRejects = this.qcRejects.map(i => this.makeQcReject(i));
            }
        },

        // --- Actions ---
        addIssue() {
            this.issues.push(this.makeIssue());
        },
        removeIssue(uid) {
            this.issues = this.issues.filter(i => i._uid !== uid);
        },
        addQcReject() {
            this.qcRejects.push(this.makeQcReject());
        },
        removeQcReject(uid) {
            this.qcRejects = this.qcRejects.filter(i => i._uid !== uid);
        }
    };
}
</script>


@endsection