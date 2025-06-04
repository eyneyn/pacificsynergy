@extends('layouts.app')

@section('content')
    <!-- Back Button -->
    <a href="{{ route('configuration.standard.index') }}" class="flex items-center text-sm text-gray-500 hover:text-[#2d326b] mb-4">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        Product Standard
    </a>

    <div class="bg-white rounded-sm border border-gray-200 p-6 shadow-md space-y-5 transition-all duration-300 hover:shadow-xl hover:border-[#E5E7EB]">
        <!-- Heading and Actions -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-[#2d326b]">{{ $standard->description }}</h2>
            <div class="flex items-center gap-2">
                <!-- Delete Button -->
                <form id="icon-delete-standard-form"
                      class="delete-standard-form"
                      data-delete-type="standard"
                      action="{{ route('configuration.standard.destroy', $standard->id) }}"
                      method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" id="edit_standard_description" value="{{ $standard->description }}">
                    <button type="submit"
                            class="text-red-600 hover:text-red-800 rounded-lg text-md w-10 h-10 flex items-center justify-center"
                            title="Delete Standard">
                        <x-icons-delete class="w-5 h-5" />
                    </button>
                </form>
                <!-- Edit Button -->
                <a href="{{ route('configuration.standard.edit', $standard->id) }}"
                   class="inline-flex items-center gap-2 p-3 py-2 bg-[#323B76] hover:bg-[#444d90] border border-[#323B76] text-white text-sm font-medium rounded-md">
                    <x-icons-edit class="w-4 h-4" />
                    <span class="text-sm">Edit</span>
                </a>
            </div>
        </div>

        <!-- Delete Error Message -->
        @if ($errors->has('standard_delete'))
            <div class="bg-red-100 border border-red-400 text-red-700 p-2 rounded relative mb-4 text-sm">
                {{ $errors->first('standard_delete') }}
            </div>
        @endif

        <!-- Product Standard Details Table -->
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
                <tr>
                    <td class="font-medium text-[#2d326b] px-4 py-2">Description</td>
                    <td class="px-4 py-2">{{ $standard->description }}</td>
                    <td class="font-medium text-[#2d326b] px-4 py-2">Size</td>
                    <td class="px-4 py-2">{{ $standard->size }}</td>
                </tr>
                <tr>
                    <td class="font-medium text-[#2d326b] px-4 py-2">Bottles per Case</td>
                    <td class="px-4 py-2">{{ $standard->bottles_per_case }}</td>
                    <td class="font-medium text-[#2d326b] px-4 py-2">Group</td>
                    <td class="px-4 py-2">{{ $standard->group }}</td>
                </tr>
                <tr>
                    <td class="font-medium text-[#2d326b] px-4 py-2">Material No.</td>
                    <td class="px-4 py-2">{{ $standard->mat_no }}</td>
                    <td class="font-medium text-[#2d326b] px-4 py-2">Preform Weight</td>
                    <td class="px-4 py-2">{{ $standard->preform_weight }}</td>
                </tr>
                <tr>
                    <td class="font-medium text-[#2d326b] px-4 py-2">LDPE Size</td>
                    <td class="px-4 py-2">{{ $standard->ldpe_size }}</td>
                    <td class="font-medium text-[#2d326b] px-4 py-2">Cases per Roll</td>
                    <td class="px-4 py-2">{{ $standard->cases_per_roll }}</td>
                </tr>
                <tr>
                    <td class="font-medium text-[#2d326b] px-4 py-2">Caps</td>
                    <td class="px-4 py-2">{{ $standard->caps }}</td>
                    <td class="font-medium text-[#2d326b] px-4 py-2">OPP Label</td>
                    <td class="px-4 py-2">{{ $standard->opp_label }}</td>
                </tr>
                <tr>
                    <td class="font-medium text-[#2d326b] px-4 py-2">Barcode Sticker</td>
                    <td class="px-4 py-2">{{ $standard->barcode_sticker }}</td>
                    <td class="font-medium text-[#2d326b] px-4 py-2">Alt Preform for 350ml</td>
                    <td class="px-4 py-2">{{ $standard->alt_preform_for_350ml }}</td>
                </tr>
                <tr>
                    <td class="font-medium text-[#2d326b] px-4 py-2">Preform Weight 2</td>
                    <td class="px-4 py-2" colspan="3">{{ $standard->preform_weight2 }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Delete Modal Component -->
    <x-delete-modal/>
@endsection
