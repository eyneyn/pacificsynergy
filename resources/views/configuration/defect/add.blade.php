@extends('layouts.app')
@section('title', content: 'Defect')
@section('content')
<div class="container mx-auto px-4">
    <!-- Header with Icon and Title -->
    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-xl font-bold text-[#23527c]">Add New Defect</h1>
    </div>

    {{-- 🔔 Modal Alerts (Success, Error, Validation) --}}
    <x-alert-message />

    <!-- Defect Form Card -->
    <div class="border-t border-b border-gray-200 px-20 py-10 mb-6">
        <!-- Defect Form -->
        <div>
            <form action="{{ route('configuration.defect.store') }}" method="POST">
                @csrf

                <!-- Defect Name Field -->
                <div class="flex items-center mb-2">
                    <label class="text-[#23527c] font-bold w-40 text-right mr-8" for="defect_name">
                        Defect Name: <span style="color: red;">*</span>
                    </label>
                    <div class="flex-1">
                        <input type="text"
                               name="defect_name"
                               id="defect_name"
                               value="{{ old('defect_name') }}"
                               required
                               class="w-full text-sm border border-gray-300 bg-white px-3 py-1 pr-8 focus:border-[#2d326b] focus:ring focus:ring-[#2d326b] focus:ring-0 transition focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400">
                        @error('defect_name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Category Field -->
                <div class="flex items-center mb-2">
                    <label class="text-[#23527c] font-bold w-40 text-right mr-8" for="category">
                        Defect Category: <span style="color: red;">*</span>
                    </label>
                    <div class="flex-1">
                        <!-- Custom select dropdown component -->
                        <x-select-dropdown name="category"
                                           id="category"
                                           required
                                           :options="[
                                               'Caps' => 'Caps',
                                               'Bottle' => 'Bottle',
                                               'Label' => 'Label',
                                               'LDPE Shrinkfilm' => 'LDPE Shrinkfilm',
                                           ]" />
                    </div>
                </div>

                <!-- Description Field -->
                <div class="flex items-start">
                    <label class="text-[#23527c] font-bold w-40 text-right mr-8" for="description">
                        Description: <span style="color: red;">*</span>
                    </label>
                    <div class="flex-1">
                        <textarea name="description"
                                  id="description"
                                  rows="3"
                                  class="w-full text-sm border border-gray-300 bg-white px-3 py-1 pr-8 focus:border-[#2d326b] focus:ring focus:ring-[#2d326b] focus:ring-0 transition focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
        </div>
    </div>

            <!-- Information Message -->
        <div class="bg-[#5a9fd4] text-sm border border-[#4590ca] p-4 mt-4 text-white">
            <div class="font-bold">
                New Defect
            </div>
            <div>
                Please fill in all required fields to add a new defect to the system.
            </div>
        </div>

                        <!-- Submit Button Section -->
                <div class="flex gap-4 mt-4">
                    <!-- Back Button -->
                    <a href="{{ route('configuration.defect.index') }}"
                       class="inline-flex items-center px-3 py-2 bg-[#5a9fd4] hover:bg-[#4a8bc2] text-white text-sm font-medium transition-colors duration-200 border border-[#5a9fd4] hover:border-[#4a8bc2]">
                        <x-icons-back class="w-4 h-4 text-white" />
                        Back
                    </a>

                    <!-- Save Button -->
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-1 bg-[#5bb75b] border border-[#43a143] text-white px-3 py-2 hover:bg-[#42a542] text-sm">
                        <x-icons-save class="w-4 h-4 text-white" />
                        Save Defect
                    </button>
                </div>
            </form>

</div>

@endsection
