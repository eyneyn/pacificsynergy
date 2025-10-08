@extends('layouts.app')
@section('title', content: 'Standard')
@section('content')
<div class="container mx-auto px-4">
    <!-- Header -->
    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-xl font-bold text-[#23527c]">Add New Product Standard</h1>
    </div>

    {{-- 🔔 Modal Alerts (Success, Error, Validation) --}}
    <x-alert-message />

    <!-- Card -->
    <div class="border-t border-b border-gray-200 px-20 py-10 mb-6">
        <div>
            <form action="{{ route('configuration.standard.store') }}" method="POST">
                @csrf
                <!-- Form Fields -->
                <div class="space-y-4 mx-auto">
                    <!-- Row 1: Description and Size -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Description -->
                        <div class="flex items-center">
                            <label class="text-[#23527c] font-bold w-40 text-right mr-8" for="description">Description: <span style="color: red;">*</span></label>
                            <div class="flex-1">
                                <input type="text" name="description" id="description" value="{{ old('description') }}" required class="text-sm w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none">
                                @error('description')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <!-- Size -->
                        <div class="flex items-center">
                            <label class="text-[#23527c] font-bold w-40 text-right mr-8" for="size">Size: <span style="color: red;">*</span></label>
                            <div class="flex-1">
                                <input type="text" name="size" id="size" value="{{ old('size') }}" required class="text-sm w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none">
                                @error('size')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <!-- Row 2: Bottles per Case and Group -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Bottles per Case -->
                        <div class="flex items-center">
                            <label class="text-[#2d326b] font-bold w-40 text-right mr-8" for="bottles_per_case">Bottles per Case: <span style="color: red;">*</span></label>
                            <div class="flex-1">
                                <input type="number" name="bottles_per_case" id="bottles_per_case" value="{{ old('bottles_per_case') }}" required class="text-sm w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none">
                                @error('bottles_per_case')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <!-- Group -->
                        <div class="flex items-center">
                            <label class="text-[#23527c] font-bold w-40 text-right mr-8" for="group">Group: <span style="color: red;">*</span></label>
                            <div class="flex-1">
                                <x-select-dropdown name="group" id="group" required :options="['-' => 'Select an option','Water' => 'Water']" />
                                @error('group')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <!-- Row 3: Material No. and Preform Weight -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Material No. -->
                        <div class="flex items-center">
                            <label class="text-[#2d326b] font-bold w-40 text-right mr-8" for="mat_no">Material No.: <span style="color: red;">*</span></label>
                            <div class="flex-1">
                                <input type="text" name="mat_no" id="mat_no" value="{{ old('mat_no', 'n/a') }}" required class="text-sm w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none">
                                @error('mat_no')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <!-- Preform Weight -->
                        <div class="flex items-center">
                            <label class="text-[#23527c] font-bold w-40 text-right mr-8" for="preform_weight">Preform Weight: <span style="color: red;">*</span></label>
                            <div class="flex-1">
                                <input type="text" name="preform_weight" id="preform_weight" value="{{ old('preform_weight') }}" required class="text-sm w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none">
                                @error('preform_weight')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <!-- Row 4: LDPE Size and Cases per Roll -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- LDPE Size -->
                        <div class="flex items-center">
                            <label class="text-[#2d326b] font-bold w-40 text-right mr-8" for="ldpe_size">LDPE Size: <span style="color: red;">*</span></label>
                            <div class="flex-1">
                                <input type="text" name="ldpe_size" id="ldpe_size" value="{{ old('ldpe_size') }}" required class="text-sm w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none">
                                @error('ldpe_size')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <!-- Cases per Roll -->
                        <div class="flex items-center">
                            <label class="text-[#23527c] font-bold w-40 text-right mr-8" for="cases_per_roll">Cases per Roll: <span style="color: red;">*</span></label>
                            <div class="flex-1">
                                <input type="number" name="cases_per_roll" id="cases_per_roll" value="{{ old('cases_per_roll') }}" required class="text-sm w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none">
                                @error('cases_per_roll')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <!-- Row 5: Caps and OPP Label -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Caps -->
                        <div class="flex items-center">
                            <label class="text-[#2d326b] font-bold w-40 text-right mr-8" for="caps">Caps: <span style="color: red;">*</span></label>
                            <div class="flex-1">
                                <x-select-dropdown name="caps" id="caps" required :options="[
                                    '-' => 'Select an option',
                                    'CAPS Manly White' => 'CAPS Manly White',
                                    'CAPS Manly Blue' => 'CAPS Manly Blue',
                                    'CAPS White Blue' => 'CAPS White Blue'
                                ]" />
                                @error('caps')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <!-- OPP Label -->
                        <div class="flex items-center">
                            <label class="text-[#23527c] font-bold w-40 text-right mr-8" for="opp_label">OPP Label: <span style="color: red;">*</span></label>
                            <div class="flex-1">
                                <x-select-dropdown name="opp_label" id="opp_label" required :options="[
                                    '-' => 'Select an option',
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
                    <!-- Row 6: Barcode Sticker and Alt Preform for 350ml -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Barcode Sticker -->
                        <div class="flex items-center">
                            <label class="text-[#2d326b] font-bold w-40 text-right mr-8" for="barcode_sticker">Barcode Sticker: <span style="color: red;">*</span></label>
                            <div class="flex-1">
                                <x-select-dropdown name="barcode_sticker" id="barcode_sticker" required :options="[
                                    'BC Sticker Double' => 'BC Sticker Double',
                                    'None' => 'None'
                                ]" />
                                @error('barcode_sticker')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <!-- Alt Preform for 350ml -->
                        <div class="flex items-center">
                            <label class="text-[#23527c] font-bold w-40 text-right mr-8" for="alt_preform_for_350ml">Alt Preform 350ml: <span style="color: red;">*</span></label>
                            <div class="flex-1">
                                <input type="number" step="0.001" name="alt_preform_for_350ml" id="alt_preform_for_350ml" value="{{ old('alt_preform_for_350ml') }}" required class="text-sm w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none">
                                @error('alt_preform_for_350ml')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <!-- Row 7: Preform Weight 2 -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Preform Weight 2 -->
                        <div class="flex items-center">
                            <label class="text-[#2d326b] font-bold w-40 text-right mr-8" for="preform_weight2">Preform Weight 2: <span style="color: red;">*</span></label>
                            <div class="flex-1">
                                <input type="number" step="0.001" name="preform_weight2" id="preform_weight2" value="{{ old('preform_weight2') }}" required class="text-sm w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none">
                                @error('preform_weight2')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div></div>
                    </div>
                </div>
        </div>
    </div>

            <!-- Info Message -->
        <div class="bg-[#5a9fd4] text-sm border border-[#4590ca] p-4 mt-4 text-white">
                <div class="font-bold">New Product Standard</div>
                <div>Please fill in all required fields to add a new product standard to the system.</div>
        </div>

                        <!-- Submit Buttons -->
                <div class="flex gap-4 mt-4">
                    <!-- Back Button -->
                    <a href="{{ url('configuration/standard/index') }}" class="inline-flex items-center px-3 py-2 bg-[#5a9fd4] hover:bg-[#4a8bc2] border border-[#5a9fd4] hover:border-[#4a8bc2] text-white text-sm font-medium transition-colors duration-200">
                        <x-icons-back class="w-2 h-2 text-white" /> Back
                    </a>
                    <!-- Save Button -->
                    <button type="submit" class="inline-flex items-center justify-center gap-1 bg-[#5bb75b] border border-[#43a143] text-white px-3 py-2 hover:bg-[#42a542] text-sm">
                        <x-icons-save class="w-2 h-2 text-white" />
                         Save
                    </button>
                </div>
            </form>
</div>
@endsection
