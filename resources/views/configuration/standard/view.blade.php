@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <!-- Header with Icon and Title -->
    <div class="mb-4">
        <h1 class="text-xl font-bold text-[#2d326b]">
            Product Standard Details
        </h1>
    </div>

    <!-- Product Standard Card -->
    <div class="bg-white border-t border-b border-gray-200 shadow-sm mb-4 mx-auto">

        <!-- Product Standard Details -->
        <div class="px-8 py-6">
            <div class="space-y-4 max-w-4xl mx-auto">
                <!-- Description & Size -->
                <div class="grid grid-cols-2 gap-6">
                    <div class="flex items-center">
                        <span class="text-[#2d326b] font-bold w-48 text-right mr-6">Description:</span>
                        <span class="text-[#2d326b]">{{ $standard->description }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-[#2d326b] font-bold w-48 text-right mr-6">Size:</span>
                        <span class="text-[#2d326b]">{{ $standard->size }}</span>
                    </div>
                </div>
                <!-- Bottles per Case & Group -->
                <div class="grid grid-cols-2 gap-6">
                    <div class="flex items-center">
                        <span class="text-[#2d326b] font-bold w-48 text-right mr-6">Bottles per Case:</span>
                        <span class="text-[#2d326b]">{{ $standard->bottles_per_case }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-[#2d326b] font-bold w-48 text-right mr-6">Group:</span>
                        <span class="text-[#2d326b]">{{ $standard->group }}</span>
                    </div>
                </div>
                <!-- Material No. & Preform Weight -->
                <div class="grid grid-cols-2 gap-6">
                    <div class="flex items-center">
                        <span class="text-[#2d326b] font-bold w-48 text-right mr-6">Material No.:</span>
                        <span class="text-[#2d326b]">{{ $standard->mat_no }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-[#2d326b] font-bold w-48 text-right mr-6">Preform Weight:</span>
                        <span class="text-[#2d326b]">{{ $standard->preform_weight }}</span>
                    </div>
                </div>
                <!-- LDPE Size & Cases per Roll -->
                <div class="grid grid-cols-2 gap-6">
                    <div class="flex items-center">
                        <span class="text-[#2d326b] font-bold w-48 text-right mr-6">LDPE Size:</span>
                        <span class="text-[#2d326b]">{{ $standard->ldpe_size }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-[#2d326b] font-bold w-48 text-right mr-6">Cases per Roll:</span>
                        <span class="text-[#2d326b]">{{ $standard->cases_per_roll }}</span>
                    </div>
                </div>
                <!-- Caps & OPP Label -->
                <div class="grid grid-cols-2 gap-6">
                    <div class="flex items-center">
                        <span class="text-[#2d326b] font-bold w-48 text-right mr-6">Caps:</span>
                        <span class="text-[#2d326b]">{{ $standard->caps }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-[#2d326b] font-bold w-48 text-right mr-6">OPP Label:</span>
                        <span class="text-[#2d326b]">{{ $standard->opp_label }}</span>
                    </div>
                </div>
                <!-- Barcode Sticker & Alt Preform (350ml) -->
                <div class="grid grid-cols-2 gap-6">
                    <div class="flex items-center">
                        <span class="text-[#2d326b] font-bold w-48 text-right mr-6">Barcode Sticker:</span>
                        <span class="text-[#2d326b]">{{ $standard->barcode_sticker }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-[#2d326b] font-bold w-48 text-right mr-6">Alt Preform (350ml):</span>
                        <span class="text-[#2d326b]">{{ $standard->alt_preform_for_350ml }}</span>
                    </div>
                </div>
                <!-- Preform Weight 2 -->
                <div class="flex items-center">
                    <span class="text-[#2d326b] font-bold w-48 text-right mr-6">Preform Weight 2:</span>
                    <span class="text-[#2d326b]">{{ $standard->preform_weight2 }}</span>
                </div>
            </div>
        </div>
    </div>

            <!-- Status Message -->
        @if ($errors->has('standard_delete'))
            <div class="bg-red-50 border border-red-200 p-4 mt-4">
                <div class="text-red-800">
                    <strong class="font-medium">Delete Failed:</strong>
                    <span>{{ $errors->first('standard_delete') }}</span>
                </div>
            </div>
        @else
            <div class="bg-[#43ac6a] text-sm border border-[#2f9655] p-4 mt-4 text-white">
                <div class="font-bold">Good job! This product standard has been successfully recorded in the system.</div>
                <div>You can edit or delete this record using the controls below.</div>
            </div>
        @endif


    <!-- Action Buttons -->
    <div class="flex items-center gap-2 mt-6">
        <!-- Back Button -->
        <a href="{{ route('configuration.standard.index') }}" 
           class="inline-flex items-center px-3 py-2 bg-[#5a9fd4] hover:bg-[#4a8bc2] text-white text-sm font-medium transition-colors duration-200 border border-[#5a9fd4] hover:border-[#4a8bc2]">
            <x-icons-back class="w-2 h-2 text-white" />
            Back
        </a>
        <!-- Edit Button -->
        <a href="{{ route('configuration.standard.edit', $standard->id) }}"
           class="inline-flex items-center gap-2 px-3 py-2 bg-[#323B76] hover:bg-[#444d90] text-white text-sm font-medium transition-colors duration-200">
            <x-icons-edit class="w-4 h-4" />
            Edit
        </a>
        <!-- Delete Form -->
        <form id="icon-delete-standard-form-{{ $standard->id }}"
              data-delete-type="standard"
              data-base-action="{{ route('configuration.standard.destroy', $standard->id) }}"
              class="delete-standard-form"
              method="POST">
            @csrf
            @method('DELETE')
            <input type="hidden" id="edit_standard_description" value="{{ $standard->description }}">
            <input type="hidden" id="edit_standard_name" value="{{ $standard->standard_name }}">
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
