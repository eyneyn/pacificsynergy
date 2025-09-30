@extends('layouts.app')
@section('title', content: 'Defect')
@section('content')
<div class="container mx-auto px-4">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="text-xl font-bold text-[#23527c]">Defect Details</h1>
    </div>

    <!-- Defect Details Card -->
    <div class="border-t border-b border-gray-200 px-20 py-10 mb-6">
        <!-- Defect Info Section -->
        <div class="space-y-2">
            <!-- Defect Name -->
            <div class="flex items-center">
                <span class="text-[#23527c] font-bold w-40 text-right mr-8">Defect Name:</span>
                <span class="text-[#23527c]">{{ $defect->defect_name }}</span>
            </div>
            <!-- Defect Category -->
            <div class="flex items-center">
                <span class="text-[#23527c] font-bold w-40 text-right mr-8">Defect Category:</span>
                <span class="text-[#23527c]">{{ $defect->category }}</span>
            </div>
            <!-- Description -->
            <div class="flex items-center">
                <span class="text-[#23527c] font-bold w-40 text-right mr-8">Description:</span>
                <span class="text-[#23527c]">{{ $defect->description }}</span>
            </div>
        </div>
    </div>

        <!-- Message -->
            <div class="bg-[#43ac6a] text-sm border border-[#2f9655] p-4 mt-4 text-white">
                <div>
                    <span class="font-bold">Good job!</span> {{ session('success') }}
                </div>
                <div>
                    You can edit or delete this defect record using the controls.
                </div>
            </div>

    <!-- Action Buttons -->
    <div class="flex items-center gap-2 mt-6">
        <!-- Back Button -->
        <a href="{{ route('configuration.defect.index') }}"
           class="inline-flex items-center px-3 py-2 bg-[#5a9fd4] hover:bg-[#4a8bc2] border border-[#4590ca] text-white text-sm font-medium transition-colors duration-200">
            <x-icons-back class="w-2 h-2 text-white" />
            Back
        </a>
        <!-- Edit Button -->
        <a href="{{ route('configuration.defect.edit', $defect->id) }}"
           class="inline-flex items-center gap-2 p-2 border border-[#323B76] bg-[#323B76] hover:bg-[#444d90] border border-[#323B76] text-white text-sm font-medium transition-colors duration-200">
            <x-icons-edit class="w-4 h-4" />
            Edit
        </a>
        <!-- Delete Form -->
        <form id="icon-delete-defect-form-{{ $defect->id }}"
              data-delete-type="defect"
              data-base-action="{{ route('configuration.defect.destroy', ':id') }}"
              class="delete-defect-form"
              method="POST">
            @csrf
            @method('DELETE')
            <input type="hidden" id="edit_defect_id" value="{{ $defect->id }}">
            <input type="hidden" id="edit_defect_name" value="{{ $defect->defect_name }}">
            <button type="submit"
                    class="inline-flex items-center gap-2 p-2 bg-red-600 hover:bg-red-700 border border-red-700 text-white text-sm font-medium transition-colors duration-200"
                    title="Delete Standard">
                <x-icons-delete class="w-4 h-4"/>
                Delete
            </button>
        </form>
    </div>
</div>

<!-- Delete Modal Component -->
<x-delete-modal/>
@endsection
