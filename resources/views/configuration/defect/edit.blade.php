@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <!-- Page Header -->
    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-xl font-bold text-[#23527c]">Edit Defect</h1>
    </div>

    <!-- Defect Edit Form -->
    <div class="border-t border-b border-gray-200 px-20 py-10 mb-6">

        <!-- Defect Form -->
        <div>
            <form action="{{ route('configuration.defect.update', $defect->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Defect Name Field -->
                <div class="flex items-center mb-2">
                    <label class="text-[#23527c] font-bold w-40 text-right mr-8" for="defect_name">Defect Name:</label>
                    <div class="flex-1">
                        <input type="text" 
                            name="defect_name" 
                            id="defect_name"
                            value="{{ old('defect_name', $defect->defect_name) }}"
                            required 
                            class="text-sm w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none">
                        @error('defect_name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Defect Category Field -->
                <div class="flex items-center mb-2">
                    <label class="text-[#23527c] font-bold w-40 text-right mr-8" for="category">Defect Category:</label>
                    <div class="flex-1">
                        <x-select-dropdown name="category" 
                            id="category" 
                            value="{{ old('category', $defect->category) }}"
                            required
                            :options="[
                                'Caps' => 'Caps',
                                'Bottle' => 'Bottle',
                                'Label' => 'Label',
                                'LDPE Shrinkfilm' => 'LDPE Shrinkfilm',
                            ]" />
                        @error('category')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Description Field -->
                <div class="flex items-start">
                    <label class="text-[#23527c] font-bold w-40 text-right mr-8" for="description">Description:</label>
                    <div class="flex-1">
                        <textarea name="description" 
                            id="description" 
                            rows="3" 
                            class="text-sm w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none">{{ old('description', $defect->description) }}</textarea>
                        @error('description')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
        </div>
    </div>

            <!-- Info Message -->
        <div class="bg-[#5a9fd4] text-sm border border-[#4590ca] p-4 mt-4 text-white">
                <div> <span class="font-bold">Editing Defect:</span> {{ $defect->defect_name }}</div>
                <div>Please modify the fields below to update the defect information.</div>
        </div>

                        <!-- Form Actions -->
                <div class="flex gap-4 mt-4">
                    <!-- Back Button -->
                    <a href="{{ route('configuration.defect.view', $defect->id) }}" 
                        class="inline-flex items-center px-3 py-2 bg-[#5a9fd4] hover:bg-[#4a8bc2] border border-[#5a9fd4] hover:border-[#4a8bc2] text-white text-sm font-medium transition-colors duration-200">
                        <x-icons-back class="w-2 h-2 text-white" />
                        Back
                    </a>
                    <!-- Update Button -->
                    <button type="submit" 
                        class="inline-flex items-center gap-1 bg-[#5bb75b] border border-[#43a143] text-white px-3 py-2 hover:bg-[#42a542] text-sm">
                        <x-icons-save class="w-2 h-2 text-white" />
                        Update Defect
                    </button>
                </div>
            </form>
</div>
@endsection