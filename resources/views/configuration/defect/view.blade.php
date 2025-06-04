@extends('layouts.app')

@section('content')

<!-- Back Button -->
<a href="{{ route('configuration.defect.index') }}" class="flex items-center text-sm text-gray-500 hover:text-[#2d326b] mb-4">
    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
    </svg>
    Defect
</a>

<div class="w-full max-w-2xl bg-white rounded-sm border border-gray-200 p-6 shadow-md space-y-5 transition-all duration-300 hover:shadow-xl hover:border-[#E5E7EB]">


<!-- Heading -->
<div class="flex items-center justify-between mb-6">
    <h2 class="text-xl font-bold text-[#2d326b]">{{ $defect->defect_name }}</h2>

    <div class="flex items-center gap-2">
        <!-- Delete Button -->
        <form id="icon-delete-defect-form"
            data-delete-type="defect"
            action="{{ route('configuration.defect.destroy', $defect->id) }}"
            method="POST">
            @csrf
            @method('DELETE')

            <!-- Add this hidden input -->
            <input type="hidden" id="edit_defect_name" value="{{ $defect->defect_name }}">
            
            <button type="submit"
                    class="text-red-600 hover:text-red-800 rounded-lg text-md w-10 h-10 flex items-center justify-center"
                    title="Delete Standard">
                <x-icons-delete class="w-5 h-5" />
            </button>
        </form>
        <!-- Edit Button -->
        <a href="{{ route('configuration.defect.edit', $defect->id) }}"
           class="inline-flex items-center gap-2 p-3 py-2 bg-[#323B76] hover:bg-[#444d90] border border-[#323B76] text-white text-sm font-medium rounded-md">
            <x-icons-edit class="w-4 h-4" />
            <span class="text-sm">Edit</span>
        </a>
    </div>
</div>
        @if ($errors->has('defect_delete'))
            <div class="bg-red-100 border border-red-400 text-red-700 p-2 rounded relative mb-4 text-sm" role="alert">
                <strong class="font-bold">Delete Failed:</strong>
                <span class="block sm:inline">{{ $errors->first('defect_delete') }}</span>
            </div>
        @endif

    <!-- Table Layout -->
        <table class="w-full max-w-2xl text-sm border border-gray-200 shadow-sm">
            <thead class="bg-gray-100 text-[#2d326b]">
            <tr>
                <th class="text-left px-4 py-3 w-1/3">Product Defect Field</th>
                <th class="text-left px-4 py-3 w-1/4">Value</th>
            </tr>
        </thead>
        <tbody class="text-gray-700 divide-y divide-gray-200">
            <tr>
                <td class="font-medium text-[#2d326b] px-4 py-2">Defect Name</td>
                <td class="px-4 py-2 w-full">{{ $defect->defect_name }}</td>
            </tr>
            <tr>
                <td class="font-medium text-[#2d326b] px-4 py-2">Category</td>
                <td class="px-4 py-2 w-full">{{ $defect->category }}</td>
            </tr>
            <tr>
                <td class="font-medium text-[#2d326b] px-4 py-2">Description</td>
                <td class="px-4 py-2 w-full">{{ $defect->description }}</td>
            </tr>
        </tbody>
    </table>
</div>

<x-delete-modal/>
@endsection