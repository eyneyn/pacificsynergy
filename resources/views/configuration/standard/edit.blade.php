@extends('layouts.app')

@section('content')

<!-- Back Button -->
<a href="{{ route('configuration.standard.view', $standard->id) }}" class="flex items-center text-sm text-gray-500 hover:text-[#2d326b] mb-4">
    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
    </svg>
    Product Standard
</a>

<div class="bg-white rounded-sm border border-gray-200 p-6 shadow-md space-y-5 transition-all duration-300 hover:shadow-xl hover:border-[#E5E7EB]">
    <!-- Edit Form -->
    <form action="{{ route('configuration.standard.update', $standard->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Heading with Save Button -->
        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-xl font-bold text-[#2d326b]">{{ $standard->description }}</h2>
            <button type="submit"
                class="inline-flex items-center gap-2 p-3 py-2 bg-[#323B76] hover:bg-[#444d90] border border-[#323B76] text-white text-sm font-medium rounded-md">
                Save
            </button>
        </div>

        <!-- Product Standard Edit Table -->
        <table class="min-w-full text-sm border border-gray-200 shadow-sm">
            <thead class="bg-gray-100 text-[#2d326b]">
                <tr>
                    <th class="text-left px-4 py-3 w-1/4">Product Standard Field</th>
                    <th class="text-left px-4 py-3 w-1/4">Value</th>
                    <th class="text-left px-4 py-3 w-1/4">Product Standard Field</th>
                    <th class="text-left px-4 py-3 w-1/4">Value</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 divide-y divide-gray-200">
                <!-- Description & Size -->
                <tr>
                    <td class="font-medium text-[#2d326b] px-4 py-2">Description</td>
                    <td class="px-4 py-2">
                        {{ $standard->description }}
                    </td>
                    <td class="font-medium text-[#2d326b] px-4 py-2">Size</td>
                    <td class="px-4 py-2">
                        <input type="text" name="size" value="{{ old('size', $standard->size) }}"
                            class="w-full border border-gray-300 rounded px-3 py-1 text-sm">
                        @error('size') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                    </td>
                </tr>
                <!-- Bottles per Case & Group -->
                <tr>
                    <td class="font-medium text-[#2d326b] px-4 py-2">Bottles per Case</td>
                    <td class="px-4 py-2">
                        <input type="number" name="bottles_per_case" value="{{ old('bottles_per_case', $standard->bottles_per_case) }}"
                            class="w-full border border-gray-300 rounded px-3 py-1 text-sm">
                        @error('bottles_per_case') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                    </td>
                    <td class="font-medium text-[#2d326b] px-4 py-2">Group</td>
                    <td class="px-4 py-2">
                        <x-select-dropdown name="group" id="group" :value="old('group', $standard->group)"
                            :options="['Water' => 'Water']" />
                    </td>
                </tr>
                <!-- Material No. & Preform Weight -->
                <tr>
                    <td class="font-medium text-[#2d326b] px-4 py-2">Material No.</td>
                    <td class="px-4 py-2">
                        <input type="text" name="mat_no" value="{{ old('mat_no', $standard->mat_no) }}"
                            class="w-full border border-gray-300 rounded px-3 py-1 text-sm">
                        @error('mat_no') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                    </td>
                    <td class="font-medium text-[#2d326b] px-4 py-2">Preform Weight</td>
                    <td class="px-4 py-2">
                        <input type="text" name="preform_weight" value="{{ old('preform_weight', $standard->preform_weight) }}"
                            class="w-full border border-gray-300 rounded px-3 py-1 text-sm">
                        @error('preform_weight') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                    </td>
                </tr>
                <!-- LDPE Size & Cases per Roll -->
                <tr>
                    <td class="font-medium text-[#2d326b] px-4 py-2">LDPE Size</td>
                    <td class="px-4 py-2">
                        <input type="text" name="ldpe_size" value="{{ old('ldpe_size', $standard->ldpe_size) }}"
                            class="w-full border border-gray-300 rounded px-3 py-1 text-sm">
                        @error('ldpe_size') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                    </td>
                    <td class="font-medium text-[#2d326b] px-4 py-2">Cases per Roll</td>
                    <td class="px-4 py-2">
                        <input type="number" name="cases_per_roll" value="{{ old('cases_per_roll', $standard->cases_per_roll) }}"
                            class="w-full border border-gray-300 rounded px-3 py-1 text-sm">
                        @error('cases_per_roll') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                    </td>
                </tr>
                <!-- Caps & OPP Label -->
                <tr>
                    <td class="font-medium text-[#2d326b] px-4 py-2">Caps</td>
                    <td class="px-4 py-2">
                        <x-select-dropdown name="caps" id="caps" :value="old('caps', $standard->caps)"
                            :options="[
                                'CAPS Manly White' => 'CAPS Manly White',
                                'CAPS Manly Blue' => 'CAPS Manly Blue',
                                'CAPS White Blue' => 'CAPS White Blue'
                            ]" />
                    </td>
                    <td class="font-medium text-[#2d326b] px-4 py-2">OPP Label</td>
                    <td class="px-4 py-2">
                        <x-select-dropdown name="opp_label" id="opp_label" :value="old('opp_label', $standard->opp_label)"
                            :options="[
                                'OPP Label China' => 'OPP Label China',
                                'Sticker Label' => 'Sticker Label',
                                'OPP Label Shrinkpack' => 'OPP Label Shrinkpack'
                            ]" />
                    </td>
                </tr>
                <!-- Barcode Sticker & Alt Preform for 350ml -->
                <tr>
                    <td class="font-medium text-[#2d326b] px-4 py-2">Barcode Sticker</td>
                    <td class="px-4 py-2">
                        <x-select-dropdown name="barcode_sticker" id="barcode_sticker" :value="old('barcode_sticker', $standard->barcode_sticker)"
                            :options="[
                                'BC Sticker Double' => 'BC Sticker Double',
                                'None' => 'None'
                            ]" />
                    </td>
                    <td class="font-medium text-[#2d326b] px-4 py-2">Alt Preform for 350ml</td>
                    <td class="px-4 py-2">
                        <input type="number" step="0.001" name="alt_preform_for_350ml"
                            value="{{ old('alt_preform_for_350ml', $standard->alt_preform_for_350ml) }}"
                            class="w-full border border-gray-300 rounded px-3 py-1 text-sm">
                        @error('alt_preform_for_350ml') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                    </td>
                </tr>
                <!-- Preform Weight 2 -->
                <tr>
                    <td class="font-medium text-[#2d326b] px-4 py-2">Preform Weight 2</td>
                    <td class="px-4 py-2">
                        <input type="number" step="0.001" name="preform_weight2"
                            value="{{ old('preform_weight2', $standard->preform_weight2) }}"
                            class="w-full border border-gray-300 rounded px-3 py-1 text-sm">
                        @error('preform_weight2') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                    </td>
                    <td class="font-medium text-[#2d326b] px-4 py-2"></td>
                    <td class="px-4 py-2"></td>
                </tr>
            </tbody>
        </table>
        <!-- End Product Standard Edit Table -->
    </form>
    <!-- End Edit Form -->
</div>

@endsection
