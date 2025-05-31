@extends('layouts.app')

@section('content')

<!-- Back Button -->
<a href="{{ route('metrics.maintenance.index') }}" class="flex items-center text-sm text-gray-500 hover:text-[#2d326b] mb-4">
    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
    </svg>
    Maintenance
</a>

<!-- Heading -->
<div class="flex items-center justify-between mb-6">
    <div class="flex-1 text-center">
        <h2 class="text-xl font-bold text-[#2d326b]">{{ $maintenance->name }}</h2>
    </div>

    <div class="flex items-center gap-2">
        <!-- Delete Button -->
        <form id="icon-delete-maintenance-form"
            data-delete-type="maintenance"
            action="{{ route('metrics.maintenance.destroy', $maintenance->id) }}"
            method="POST">
            @csrf
            @method('DELETE')

            <!-- Add this hidden input -->
            <input type="hidden" id="edit_maintenance_name" value="{{ $maintenance->name }}">
            
            <button type="submit"
                    class="text-red-600 hover:text-red-800 rounded-lg text-md w-10 h-10 flex items-center justify-center"
                    title="Delete Standard">
                <x-icons-delete class="w-5 h-5" />
            </button>
        </form>
        <!-- Edit Button -->
        <a href="{{ route('metrics.maintenance.edit', $maintenance->id) }}"
           class="inline-flex items-center gap-2 p-3 py-2 bg-[#323B76] hover:bg-[#444d90] border border-[#323B76] text-white text-sm font-medium rounded-md">
            <x-icons-edit class="w-4 h-4" />
            <span class="text-sm">Edit</span>
        </a>
    </div>
</div>

<!-- Table Layout -->
    <div class="bg-white rounded-sm border border-gray-200 p-6 shadow-md space-y-5 transition-all duration-300 hover:shadow-xl hover:border-[#E5E7EB]">
        <table class="min-w-full text-sm border border-gray-200 shadow-sm">
            <thead class="bg-gray-100 text-[#2d326b]">
            <tr>
                <th class="text-left px-4 py-3 w-1/4">Maintenance Field</th>
                <th class="text-left px-4 py-3 w-1/4">Value</th>
            </tr>
        </thead>
        <tbody class="text-gray-700 divide-y divide-gray-200">
            <tr>
                <td class="font-medium text-[#2d326b] px-4 py-2">Machine / Others</td>
                <td class="px-4 py-2 w-full">{{ $maintenance->name }}</td>
            </tr>
            <tr>
                <td class="font-medium text-[#2d326b] px-4 py-2">Type</td>
                <td class="px-4 py-2 w-full">{{ $maintenance->type }}</td>
            </tr>
        </tbody>
    </table>
</div>

<x-delete-modal/>
@endsection