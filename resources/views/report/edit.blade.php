@extends('layouts.app')

@section('content')

<div class="container mx-auto px-4">


    <!-- Edit Production Report Form -->
    <form id="update-report-form" action="{{ route('report.update', $report->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Title -->
        <div class="flex-1 text-center">
            <h2 class="text-2xl m-4 font-bold text-[#23527c]">Edit Production Report</h2>
        </div>

        <div class="space-y-5">

            <!-- Basic Production Details Header -->
                <div class="mb-6 flex items-center justify-between">
                    <a href="{{ url('report/index') }}" class="inline-flex items-center px-3 py-2 bg-[#5a9fd4] hover:bg-[#4a8bc2] text-white text-sm font-medium transition-colors duration-200 border border-[#4590ca] hover:border-[#4a8bc2]">
                        <x-icons-back class="w-2 h-2 text-white" />
                        Back
                    </a>                
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-1 bg-[#5bb75b] border border-[#43a143] text-white px-3 py-2 hover:bg-[#42a542] text-sm">
                        <x-icons-save class="w-2 h-2 text-white" />
                        Update
                    </button>
                </div>

                <!-- Line Efficiency Input (One Row) -->
                <div class="mb-4 mt-10">
                    <label for="line_efficiency" class="w-full text-sm font-medium text-[#2d326b]">
                        Line Efficiency (%)
                    </label>
                    <input 
                        type="number"
                        name="line_efficiency"
                        id="line_efficiency"
                        step="0.01"
                        min="0"
                        max="100"
                        inputmode="decimal"
                        oninput="
                            const val = parseFloat(this.value);
                            if (val > 100) this.value = 100;
                            if (val < 0) this.value = 0;
                        "
                        class="w-1/6 ml-2 h-[30px] text-sm border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400"
                        value="{{ old('line_efficiency', $report->line_efficiency) }}"
                        placeholder="(e.g. 98.75)"
                    >
                </div>

            <!-- Basic Production Form Table -->
            <table class="min-w-full text-sm border border-[#E5E7EB] shadow-sm">
                <thead class="uppercase text-[#23527c] bg-[#e2f2ff]">
                    <tr>
                        <th class="text-left px-4 py-3 w-1/4">Field</th>
                        <th class="text-left px-4 py-3 w-1/4">Value</th>
                        <th class="text-left px-4 py-3 w-1/4">Field</th>
                        <th class="text-left px-4 py-3 w-1/4">Value</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 divide-y divide-gray-200">
                    <tr>
                        <td class="font-medium text-[#23527c] px-4 py-2">Running SKU</td>
                        <td class="px-4 py-2">
                            <x-select-dropdown name="sku" value="{{ old('sku', $report->sku) }}" :options="$skus->pluck('description', 'description')->toArray()" placeholder="Select SKU" required />
                        </td>
                        <td class="font-medium text-[#23527c] px-4 py-2">Production Date</td>
                        <td class="px-4 py-2">
                            <input type="date" name="production_date" value="{{ old('production_date', $report->production_date) }}" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-3 py-1 text-sm" required>
                        </td>
                    </tr>
                    <tr>
                        <td class="font-medium text-[#23527c] px-4 py-2">Shift</td>
                        <td class="px-4 py-2">
                            <x-select-dropdown name="shift" value="{{ old('shift', $report->shift) }}" :options="['00:00H - 24:00H' => '00:00H - 24:00H']" required />
                        </td>
                        <td class="font-medium text-[#23527c] px-4 py-2">AC Temperatures</td>
                        <td class="px-4 py-2">
                            <div class="grid grid-cols-4 gap-1">
                                @for ($i = 1; $i <= 4; $i++)
                                    <input 
                                        type="text" 
                                        name="ac{{ $i }}" 
                                        placeholder="AC {{ $i }}" 
                                        value="{{ old('ac' . $i, $report->{'ac' . $i}) }}" 
                                        class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm text-center"
                                    >
                                @endfor
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="font-medium text-[#23527c] px-4 py-2">Line #</td>
                        <td class="px-4 py-2">
                            <x-select-dropdown name="line" value="{{ old('line', $report->line) }}" :options="$lineOptions->toArray()" required />
                        </td>
                        <td class="font-medium text-[#23527c] px-4 py-2">Total Output (Cases)</td>
                        <td class="px-4 py-2">
                            <input type="text" name="total_outputCase" value="{{ old('total_outputCase', $report->total_outputCase) }}" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm text-center">
                        </td>
                    </tr>
                    <tr>
                        <td class="font-medium text-[#23527c] px-4 py-2">FBO/FCO</td>
                        <td class="px-4 py-2">
                            <x-select-dropdown name="fbo_fco" value="{{ old('fbo_fco', $report->fbo_fco) }}"  :options="['00:00H - 00:00H' => '00:00H - 00:00H']" />
                        </td>
                        <td class="font-medium text-[#23527c] px-4 py-2">LBO/LCO</td>
                        <td class="px-4 py-2">
                            <x-select-dropdown name="lbo_lco" value="{{ old('lbo_lco', $report->lbo_lco) }}" :options="['24:00H - 24:00H' => '24:00H - 24:00H']" />
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
                            <input type="text" name="filler_speed" value="{{ old('filler_speed', $report->filler_speed) }}" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm text-center">
                        </td>
                        <td class="font-medium text-[#23527c] px-4 py-2 text-xs">RM Rejects <br><span class="text-sm">Opp/Labels</span></td>
                        <td class="px-4 py-2">
                            <input type="text" name="opp_labels" value="{{ old('opp_labels', $report->opp_labels) }}" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm text-center">
                        </td>
                        <td class="font-medium text-[#23527c] px-4 py-2">Bottle</td>
                        <td class="px-4 py-2">
                            <input type="text" name="bottle_filling" value="{{ old('bottle_filling', $report->bottle_filling) }}" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm text-center">
                        </td>
                    </tr>
                    <tr>
                        <td class="font-medium text-[#23527c] px-4 py-2">OPP/Labeler Speed</td>
                        <td class="px-4 py-2">
                            <input type="text" name="opp_labeler_speed" value="{{ old('opp_labeler_speed', $report->opp_labeler_speed) }}" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm text-center">
                        </td>
                        <td class="font-medium text-[#23527c] px-4 py-2">Shrinkfilm</td>
                        <td class="px-4 py-2">
                            <input type="text" name="shrinkfilm" value="{{ old('shrinkfilm', $report->shrinkfilm) }}" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm text-center">
                        </td>
                        <td class="font-medium text-[#23527c] px-4 py-2">Caps</td>
                        <td class="px-4 py-2">
                            <input type="text" name="caps_filling" value="{{ old('caps_filling', $report->caps_filling) }}" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm text-center">
                        </td>
                    </tr>
                    <!-- Blow Molding Section Header -->
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
                    <tr>
                        <td class="font-medium text-[#23527c] px-4 py-2">Blow Molding Output</td>
                        <td class="px-4 py-2">
                            <input type="text" name="blow_molding_output" value="{{ old('blow_molding_output', $report->blow_molding_output) }}"  class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm text-center">
                        </td>
                        <td class="font-medium text-[#23527c] px-4 py-2 text-xs">Blow Molding Rejects <span class="text-sm">Preform</span></td>
                        <td class="px-4 py-2">
                            <input type="text" name="preform_blow_molding" value="{{ old('preform_blow_molding', $report->preform_blow_molding) }}"  class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm text-center">
                        </td>
                        <td class="font-medium text-[#23527c] px-4 py-2">Bottles</td>
                        <td class="px-4 py-2">
                            <input type="text" name="bottles_blow_molding" value="{{ old('bottles_blow_molding', $report->bottles_blow_molding) }}"  class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm text-center">
                        </td>
                    </tr>
                    <tr>
                        <td class="font-medium text-[#23527c] px-4 py-2">Speed (Bottles/Hour)</td>
                        <td class="px-4 py-2">
                            <input type="text" name="speed_blow_molding" value="{{ old('speed_blow_molding', $report->speed_blow_molding) }}"  class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm text-center">
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
                                    <x-select-material name="materials[]" :options="$materialsOptions" />
                                </td>
                                <td class="px-2 py-2">
                                    <input type="text" name="description[]" x-model="issue.description"
                                        placeholder="Describe the issue or remark"
                                        class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm text-center">
                                </td>
                                <td class="px-2 py-2 text-center">
                                    <input type="text" name="minutes[]" x-model="issue.minutes"
                                        placeholder="mins"
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
                                    class="inline-flex items-center gap-2 bg-[#323B76] hover:bg-[#444d90] text-white p-2 text-xs shadow">
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
                                <x-select-dropdown name="qa_remarks" 
                                    value="{{ old('qa_remarks', $report->qa_remarks) }}"
                                    :options="[ 'Passed' => 'Passed']" />
                            </td>
                            <td class="font-medium text-[#23527c] px-4 py-2">With Label</td>
                            <td class="px-4 py-2">
                                <input type="text" name="with_label" value="{{ old('with_label', $report->with_label) }}" placeholder="pcs" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm text-center">
                            </td>
                            <td class="font-medium text-[#23527c] px-4 py-2">Without Label</td>
                            <td class="px-4 py-2">
                                <input type="text" name="without_label" value="{{ old('without_label', $report->without_label) }}" placeholder="pcs" class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm text-center">
                            </td>
                        </tr>
                <!-- Line QC Rejects Section Header -->
                <thead class="uppercase text-[#23527c] bg-[#e2f2ff]">
                    <tr>
                        <th colspan="6" class="text-left px-4 py-3">Line QC Rejects</th>
                    </tr>
                </thead>
                <tr>
                    <td colspan="6">
                        <div class="grid md:grid-cols-2 gap-4">
                            @foreach (['Caps', 'Bottle', 'Label', 'LDPE Shrinkfilm'] as $category)
                                <div class="p-4 border border-gray-200 flex flex-col gap-4">
                                    <!-- QC Rejects Category Header and Add Button -->
                                    <div class="flex items-center justify-between">
                                        <h5 class="text-sm font-bold text-[#23527c]">{{ $category }}</h5>
                                        <button type="button"
                                            class="text-xs px-2 py-1 bg-[#323B76] hover:bg-[#444d90] text-white"
                                            @click="addQcReject('{{ $category }}')">
                                            Add
                                        </button>
                                    </div>

                                    <!-- Dynamic QC Rejects Items -->
                                    <template x-for="item in form.qcRejects['{{ $category }}']" :key="item._uid">
                                        <div class="flex items-center gap-1">
                                            <x-select-defect
                                                class="w-[160px]"
                                                :name="'qc_' . strtolower($category) . '_defect[]'"
                                                :options="$defects->where('category', $category)->pluck('defect_name', 'defect_name')->toArray()"
                                                x-init="$watch('item.defect', value => $el.querySelector('select').value = value)"
                                                @change="item.defect = $event.target.value"
                                            />
                                            <input type="text" x-model="item.qty" name="qc_{{ strtolower($category) }}_qty[]"
                                                placeholder="pcs"
                                                class="w-[60px] h-[30px] text-sm text-center border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400">
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

                <!-- Alpine.js Data and Methods for Dynamic Sections -->
                <script>
                function issueTable() {
                    return {
                        // Inject server-provided arrays, but enrich them with _uid
                        issues: (@json($issues) || []).map(i => ({ ...i, _uid: crypto.randomUUID() })),
                        form: {
                            qcRejects: {
                                'Caps': (@json($qcRejects['Caps']) || []).map(i => ({ ...i, _uid: crypto.randomUUID() })),
                                'Bottle': (@json($qcRejects['Bottle']) || []).map(i => ({ ...i, _uid: crypto.randomUUID() })),
                                'Label': (@json($qcRejects['Label']) || []).map(i => ({ ...i, _uid: crypto.randomUUID() })),
                                'LDPE Shrinkfilm': (@json($qcRejects['LDPE Shrinkfilm']) || []).map(i => ({ ...i, _uid: crypto.randomUUID() })),
                            }
                        },

                        // --- Issue actions ---
                        addIssue() {
                            this.issues.push({ _uid: crypto.randomUUID(), material: '', description: '', minutes: '' });
                        },
                        removeIssue(uid) {
                            this.issues = this.issues.filter(i => i._uid !== uid);
                        },

                        // --- QC Reject actions ---
                        addQcReject(category) {
                            this.form.qcRejects[category].push({ _uid: crypto.randomUUID(), defect: '', qty: '' });
                        },
                        removeQcReject(category, uid) {
                            if (this.form.qcRejects[category].length > 1) {
                                this.form.qcRejects[category] = this.form.qcRejects[category].filter(i => i._uid !== uid);
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