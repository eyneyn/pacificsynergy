@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <!-- Header -->
    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-xl font-bold text-[#2d326b]">Edit Product Standard</h1>
    </div>
    <!-- Product Standard Form Card -->
    <div class="border-t border-b border-gray-200 mx-auto">
        <!-- Info Message -->
        <div class="bg-blue-50 border border-blue-200 p-4 mt-4">
            <div class="text-blue-800">
                <div class="font-medium mb-1">Editing Product Standard: {{ $standard->description }}</div>
                <div class="text-sm">Please modify the fields you want to update for this product standard.</div>
            </div>
        </div>
        <div class="px-8 py-6">
            <form action="{{ route('configuration.standard.update', $standard->id) }}" method="POST">
                @csrf
                @method('PUT')
                <!-- Form Fields -->
                <div class="space-y-4 mx-auto">
                    <!-- Row 1: Description (readonly) & Size -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="flex items-center">
                            <label class="text-[#2d326b] font-bold w-40 text-right mr-8" for="description">Description:</label>
                            <div class="flex-1">
                                <input type="text" name="description" id="description"
                                    value="{{ old('description', $standard->description) }}" readonly
                                    class="w-full border border-gray-300 px-3 py-2 text-[#2d326b] bg-gray-100 cursor-not-allowed">
                                @error('description')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="flex items-center">
                            <label class="text-[#2d326b] font-bold w-32 text-right mr-8" for="size">Size:</label>
                            <div class="flex-1">
                                <input type="text" name="size" id="size"
                                    value="{{ old('size', $standard->size) }}" required
                                    class="w-full border border-gray-300 px-3 py-2 text-[#2d326b] focus:border-blue-500 focus:shadow-lg focus:outline-none outline-none">
                                @error('size')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <!-- Row 2: Bottles per Case & Group -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="flex items-center">
                            <label class="text-[#2d326b] font-bold w-40 text-right mr-8" for="bottles_per_case">Bottles per Case:</label>
                            <div class="flex-1">
                                <input type="number" name="bottles_per_case" id="bottles_per_case"
                                    value="{{ old('bottles_per_case', $standard->bottles_per_case) }}" required
                                    class="w-full border border-gray-300 px-3 py-2 text-[#2d326b] focus:border-blue-500 focus:shadow-lg focus:outline-none outline-none">
                                @error('bottles_per_case')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="flex items-center">
                            <label class="text-[#2d326b] font-bold w-32 text-right mr-8" for="group">Group:</label>
                            <div class="flex-1">
                                <x-select-dropdown name="group" id="group"
                                    :value="old('group', $standard->group)" required
                                    :options="['Water' => 'Water']" />
                                @error('group')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <!-- Row 3: Material No. & Preform Weight -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="flex items-center">
                            <label class="text-[#2d326b] font-bold w-40 text-right mr-8" for="mat_no">Material No.:</label>
                            <div class="flex-1">
                                <input type="text" name="mat_no" id="mat_no"
                                    value="{{ old('mat_no', $standard->mat_no) }}" required
                                    class="w-full border border-gray-300 px-3 py-2 text-[#2d326b] focus:border-blue-500 focus:shadow-lg focus:outline-none outline-none">
                                @error('mat_no')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="flex items-center">
                            <label class="text-[#2d326b] font-bold w-32 text-right mr-8" for="preform_weight">Preform Weight:</label>
                            <div class="flex-1">
                                <input type="text" name="preform_weight" id="preform_weight"
                                    value="{{ old('preform_weight', $standard->preform_weight) }}" required
                                    class="w-full border border-gray-300 px-3 py-2 text-[#2d326b] focus:border-blue-500 focus:shadow-lg focus:outline-none outline-none">
                                @error('preform_weight')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <!-- Row 4: LDPE Size & Cases per Roll -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="flex items-center">
                            <label class="text-[#2d326b] font-bold w-40 text-right mr-8" for="ldpe_size">LDPE Size:</label>
                            <div class="flex-1">
                                <input type="text" name="ldpe_size" id="ldpe_size"
                                    value="{{ old('ldpe_size', $standard->ldpe_size) }}" required
                                    class="w-full border border-gray-300 px-3 py-2 text-[#2d326b] focus:border-blue-500 focus:shadow-lg focus:outline-none outline-none">
                                @error('ldpe_size')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="flex items-center">
                            <label class="text-[#2d326b] font-bold w-32 text-right mr-8" for="cases_per_roll">Cases per Roll:</label>
                            <div class="flex-1">
                                <input type="number" name="cases_per_roll" id="cases_per_roll"
                                    value="{{ old('cases_per_roll', $standard->cases_per_roll) }}" required
                                    class="w-full border border-gray-300 px-3 py-2 text-[#2d326b] focus:border-blue-500 focus:shadow-lg focus:outline-none outline-none">
                                @error('cases_per_roll')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <!-- Row 5: Caps & OPP Label -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="flex items-center">
                            <label class="text-[#2d326b] font-bold w-40 text-right mr-8" for="caps">Caps:</label>
                            <div class="flex-1">
                                <x-select-dropdown name="caps" id="caps"
                                    :value="old('caps', $standard->caps)" required
                                    :options="[
                                        'CAPS Manly White' => 'CAPS Manly White',
                                        'CAPS Manly Blue' => 'CAPS Manly Blue',
                                        'CAPS White Blue' => 'CAPS White Blue'
                                    ]" />
                                @error('caps')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="flex items-center">
                            <label class="text-[#2d326b] font-bold w-32 text-right mr-8" for="opp_label">OPP Label:</label>
                            <div class="flex-1">
                                <x-select-dropdown name="opp_label" id="opp_label"
                                    :value="old('opp_label', $standard->opp_label)" required
                                    :options="[
                                        'OPP Label China' => 'OPP Label China',
                                        'Sticker Label' => 'Sticker Label',
                                        'OPP Label Shrinkpack' => 'OPP Label Shrinkpack'
                                    ]" />
                                @error('opp_label')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <!-- Row 6: Barcode Sticker & Alt Preform for 350ml -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="flex items-center">
                            <label class="text-[#2d326b] font-bold w-40 text-right mr-8" for="barcode_sticker">Barcode Sticker:</label>
                            <div class="flex-1">
                                <x-select-dropdown name="barcode_sticker" id="barcode_sticker"
                                    :value="old('barcode_sticker', $standard->barcode_sticker)" required
                                    :options="[
                                        'BC Sticker Double' => 'BC Sticker Double',
                                        'None' => 'None'
                                    ]" />
                                @error('barcode_sticker')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="flex items-center">
                            <label class="text-[#2d326b] font-bold w-32 text-right mr-8" for="alt_preform_for_350ml">Alt Preform 350ml:</label>
                            <div class="flex-1">
                                <input type="number" step="0.001" name="alt_preform_for_350ml" id="alt_preform_for_350ml"
                                    value="{{ old('alt_preform_for_350ml', $standard->alt_preform_for_350ml) }}" required
                                    class="w-full border border-gray-300 px-3 py-2 text-[#2d326b] focus:border-blue-500 focus:shadow-lg focus:outline-none outline-none">
                                @error('alt_preform_for_350ml')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <!-- Row 7: Preform Weight 2 -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="flex items-center">
                            <label class="text-[#2d326b] font-bold w-40 text-right mr-8" for="preform_weight2">Preform Weight 2:</label>
                            <div class="flex-1">
                                <input type="number" step="0.001" name="preform_weight2" id="preform_weight2"
                                    value="{{ old('preform_weight2', $standard->preform_weight2) }}" required
                                    class="w-full border border-gray-300 px-3 py-2 text-[#2d326b] focus:border-blue-500 focus:shadow-lg focus:outline-none outline-none">
                                @error('preform_weight2')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div></div>
                    </div>
                </div>
                <!-- Submit & Back Buttons -->
                <div class="flex justify-center items-center gap-4 mt-6">
                    <a href="{{ route('configuration.standard.view', $standard->id) }}"
                        class="inline-flex items-center px-3 py-2 bg-[#5a9fd4] hover:bg-[#4a8bc2] border border-[#5a9fd4] hover:border-[#4a8bc2] text-white text-sm font-medium transition-colors duration-200">
                        <x-icons-back class="w-2 h-2 text-white" />
                        Back
                    </a>
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-1 bg-[#5bb75b] border border-[#43a143] text-white px-3 py-2 hover:bg-[#42a542] text-sm">
                        <x-icons-save class="w-2 h-2 text-white" />
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
