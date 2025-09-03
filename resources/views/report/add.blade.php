@extends('layouts.app')

@section('content')

<div class="container mx-auto px-4">

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
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-1 bg-[#5bb75b] border border-[#43a143] text-white px-3 py-2 hover:bg-[#42a542] text-sm">
                        <x-icons-save class="w-2 h-2 text-white" />
                        Save
                    </button>
                </div>

                {{-- Centered Duplicate Error --}}
                @if ($errors->has('duplicate'))
                    <div class="text-red-500 text-sm text-center">
                        {{ $errors->first('duplicate') }}
                    </div>
                @else
                    <div></div> {{-- Maintains alignment when no error --}}
                @endif
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
                        <td class="font-medium text-[#23527c] px-4 py-2">Running SKU</td>
                        <td class="px-4 py-2">
                            <x-select-dropdown name="sku" :options="$skus->pluck('description', 'description')->toArray()" :selected="old('sku')" placeholder="Select SKU" required />
                        </td>
                        <td class="font-medium text-[#23527c] px-4 py-2">Production Date</td>
                        <td class="px-4 py-2">
                            <input type="date" name="production_date" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm" value="{{ old('production_date') }}" required>                       
                        </td>
                    </tr>
                    <tr>
                        <td class="font-medium text-[#23527c] px-4 py-2">Shift</td>
                        <td class="px-4 py-2">
                            <x-select-dropdown name="shift" :options="['00:00H - 24:00H' => '00:00H - 24:00H']" :selected="old('shift')" required />
                        </td>
                        <td class="font-medium text-[#23527c] px-4 py-2">AC Temperatures</td>
                        <td class="px-4 py-2">
                            <div class="grid grid-cols-4 gap-1">
                                @for ($i = 1; $i <= 4; $i++)
                                    <input type="text" name="ac{{ $i }}" placeholder="AC {{ $i }}" value="{{ old('ac' . $i) }}" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm text-center">
                                @endfor
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="font-medium text-[#23527c] px-4 py-2">Line #</td>
                        <td class="px-4 py-2">
                            <x-select-dropdown name="line" :options="$lineOptions->toArray()" :selected="old('line')" placeholder="Select Line" required />
                        </td>
                        <td class="font-medium text-[#23527c] px-4 py-2">Total Output (Cases)</td>
                        <td class="px-4 py-2">
                            <input type="text" name="total_outputCase" placeholder="cases" value="{{ old('total_outputCase') }}" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm text-center">
                        </td>
                    </tr>
                    <tr>
                        <td class="font-medium text-[#23527c] px-4 py-2">FBO/FCO</td>
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
                        <td class="font-medium text-[#23527c] px-4 py-2">LBO/LCO</td>
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
                        <td class="font-medium text-[#23527c] px-4 py-2 text-xs">Speed (Bottles per Hour) <span class="text-sm">Filler Speed</span></td>
                        <td class="px-4 py-2">
                            <input type="text" name="filler_speed" placeholder="bph" value="{{ old('filler_speed') }}" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm text-center">
                        </td>
                        <td class="font-medium text-[#23527c] px-4 py-2 text-xs">RM Rejects <br><span class="text-sm">Opp/Labels</span></td>
                        <td class="px-4 py-2">
                            <input type="text" name="opp_labels" placeholder="pcs" value="{{ old('opp_labels') }}" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm text-center">
                        </td>
                        <td class="font-medium text-[#23527c] px-4 py-2">Bottle</td>
                        <td class="px-4 py-2">
                            <input type="text" name="bottle_filling" placeholder="pcs" value="{{ old('bottle_filling') }}" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm text-center">
                        </td>
                    </tr>
                    <tr>
                        <td class="font-medium text-[#23527c] px-4 py-2">OPP/Labeler Speed</td>
                        <td class="px-4 py-2">
                            <input type="text" name="opp_labeler_speed" placeholder="0" value="{{ old('opp_labeler_speed') }}" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm text-center">
                        </td>
                        <td class="font-medium text-[#23527c] px-4 py-2">Shrinkfilm</td>
                        <td class="px-4 py-2">
                            <input type="text" name="shrinkfilm" placeholder="pcs" value="{{ old('shrinkfilm') }}" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm text-center">
                        </td>
                        <td class="font-medium text-[#23527c] px-4 py-2">Caps</td>
                        <td class="px-4 py-2">
                            <input type="text" name="caps_filling" placeholder="pcs" value="{{ old('caps_filling') }}" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm text-center">
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
                        <td class="font-medium text-[#23527c] px-4 py-2">Blow Molding Output</td>
                        <td class="px-4 py-2">
                            <input type="text" name="blow_molding_output" placeholder="pcs" value="{{ old('blow_molding_output') }}" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm text-center">
                        </td>
                        <td class="font-medium text-[#23527c] px-4 py-2 text-xs">Blow Molding Rejects <span class="text-sm">Preform</span></td>
                        <td class="px-4 py-2">
                            <input type="text" name="speed_blow_molding" placeholder="pcs" value="{{ old('speed_blow_molding') }}" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm text-center">
                        </td>
                        <td class="font-medium text-[#23527c] px-4 py-2">Bottles</td>
                        <td class="px-4 py-2">
                            <input type="text" name="preform_blow_molding" placeholder="pcs" value="{{ old('preform_blow_molding') }}" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm text-center">
                        </td>
                    </tr>
                    <tr>
                        <td class="font-medium text-[#23527c] px-4 py-2">Speed (Bottles/Hour)</td>
                        <td class="px-4 py-2">
                            <input type="text" name="bottles_blow_molding" placeholder="bph" value="{{ old('bottles_blow_molding') }}" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm text-center">
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
                class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm text-center">
        </td>
        <td class="px-2 py-2 text-center">
            <input type="text" :name="`minutes[]`" x-model="issue.minutes" placeholder="mins"
                class="w-20 border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm text-center">
        </td>
        <td class="px-2 py-2 text-center">
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
                            <td class="font-medium text-[#23527c] px-4 py-2">Ozone</td>
                            <td class="px-4 py-2">
                                <x-select-dropdown name="qa_remarks" :options="['Passed' => 'Passed']" />
                            </td>
                            <td class="font-medium text-[#23527c] px-4 py-2">With Label</td>
                            <td class="px-4 py-2">
                                <input type="text" name="with_label" placeholder="pcs" value="{{ old('with_label') }}" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm text-center">
                            </td>
                            <td class="font-medium text-[#23527c] px-4 py-2">Without Label</td>
                            <td class="px-4 py-2">
                                <input type="text" name="without_label" placeholder="pcs" value="{{ old('without_label') }}" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm text-center">
                            </td>
                        </tr>
                    </tbody>
                    <!-- Line QC Rejects Section -->
                    <thead class="uppercase text-[#23527c] bg-[#e2f2ff]">
                        <tr>
                            <th colspan="6" class="text-left px-4 py-3">Line QC Rejects</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="6">
                                <div class="grid md:grid-cols-2 gap-4">
                                    @foreach (['Caps', 'Bottle', 'Label', 'LDPE Shrinkfilm'] as $category)
                                        <div class="p-4 border border-gray-200 flex flex-col gap-4">
                                            <!-- Category Header with Add Button -->
                                            <div class="flex items-center justify-between">
                                                <h5 class="text-sm font-bold text-[#23527c]">{{ $category }}</h5>
                                                <button type="button"
                                                    class="text-xs px-2 py-1 bg-[#323B76] hover:bg-[#444d90] text-white"
                                                    @click="addQcReject('{{ $category }}')">
                                                    Add
                                                </button>
                                            </div>
                                            <!-- Dynamic QC Reject Items -->
<template x-for="(item, index) in form.qcRejects['{{ $category }}']" :key="item._uid">
    <div class="flex items-center gap-1">
        <!-- Defect Select -->
        <x-select-defects
            class="w-[140px] h-[32px]"
            :name="'qc_' . strtolower($category) . '_defect[]'"
            :options="$defects->where('category', $category)->pluck('defect_name', 'defect_name')->toArray()"
            x-init="$watch('item.defect', value => $el.querySelector('select').value = value)"
            @change="item.defect = $event.target.value"
        />
        <!-- Quantity Input -->
        <input type="text" x-model="item.qty" name="qc_{{ strtolower($category) }}_qty[]"
            placeholder="pcs"
            class="w-[60px] h-[30px] text-sm text-center border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400">
        <!-- Delete Button -->
        <button type="button"
            @click="removeQcReject('{{ $category }}', item._uid)"
            :class="form.qcRejects['{{ $category }}'].length > 1 ? 'visible' : 'invisible'"
            class="w-[32px] h-[30px] bg-red-600 hover:bg-red-700 border border-red-700 text-white text-sm flex items-center justify-center">
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
function issueTable() {
    return {
        // Raw issues from old() (may be [] on first load)
        issues: {!! json_encode(
            collect(old('description', []))->map(function ($desc, $index) {
                return [
                    'description' => $desc,
                    'minutes'     => old('minutes')[$index] ?? '',
                    'material'    => old('materials')[$index] ?? ''
                ];
            })
        ) !!} || [],

        form: {
            qcRejects: {
                'Caps': [{ defect: '', qty: '' }],
                'Bottle': [{ defect: '', qty: '' }],
                'Label': [{ defect: '', qty: '' }],
                'LDPE Shrinkfilm': [{ defect: '', qty: '' }]
            }
        },

        // --- helpers ---
        uid() {
            return Math.random().toString(36).slice(2) + Date.now().toString(36);
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

        // --- lifecycle ---
        init() {
            // Ensure each issue item has stable _uid and structure
            if (!Array.isArray(this.issues) || this.issues.length === 0) {
                this.issues = [this.makeIssue()];
            } else {
                this.issues = this.issues.map(i => this.makeIssue(i));
            }

            // Ensure each QC reject array has items with _uid
            Object.keys(this.form.qcRejects).forEach(category => {
                if (!Array.isArray(this.form.qcRejects[category]) || this.form.qcRejects[category].length === 0) {
                    this.form.qcRejects[category] = [this.makeQcReject()];
                } else {
                    this.form.qcRejects[category] = this.form.qcRejects[category].map(i => this.makeQcReject(i));
                }
            });
        },

        // --- actions ---
        addIssue() {
            this.issues.push(this.makeIssue());
        },
        removeIssue(uid) {
            this.issues = this.issues.filter(i => i._uid !== uid);
        },

        addQcReject(category) {
            this.form.qcRejects[category].push(this.makeQcReject());
        },
        removeQcReject(category, uid) {
            // keep at least 1 item (same behaviour as before). Remove this check if you want empty lists allowed.
            if (this.form.qcRejects[category].length > 1) {
                this.form.qcRejects[category] = this.form.qcRejects[category].filter(i => i._uid !== uid);
            }
        }
    };
}
</script>

@endsection