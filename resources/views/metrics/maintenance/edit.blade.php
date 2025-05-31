@extends('layouts.app')

@section('content')

<!-- Back Button -->
<a href="{{ route('metrics.maintenance.view', $maintenance->id) }}" class="flex items-center text-sm text-gray-500 hover:text-[#2d326b] mb-4">
    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
    </svg>
    Maintenance
</a>

<form action="{{ route('metrics.maintenance.update', $maintenance->id) }}" method="POST">
    @csrf
    @method('PUT')

    <!-- Save Button -->
    <div class="mb-6 flex items-center justify-between">
        <div class="flex-1 text-center">
            <h2 class="text-xl font-bold text-[#2d326b]">{{ $maintenance->name }}</h2>
        </div>

        <button type="submit"
            class="inline-flex items-center gap-2 p-3 py-2 bg-[#323B76] hover:bg-[#444d90] border border-[#323B76] text-white text-sm font-medium rounded-md">
            Save
        </button>
    </div>

    <div class="bg-white rounded-sm border border-gray-200 p-6 shadow-md space-y-5 transition-all duration-300 hover:shadow-xl hover:border-[#E5E7EB]">

        <table class="min-w-full text-sm border border-gray-200 shadow-sm">
            <thead class="bg-gray-100 text-[#2d326b]">
                <tr>
                    <th class="text-left px-4 py-3 w-1/4">Machine Field</th>
                    <th class="text-left px-4 py-3 w-1/2 max-w-xs">Type</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 divide-y divide-gray-200">
                <tr>
                    <td class="font-medium text-[#2d326b] px-4 py-2">Machine / Others</td>
                    <td class="px-4 py-2">
                        <input type="text" name="name" value="{{ old('name', $maintenance->name) }}"
                            class="w-1/2 max-w-xs border border-gray-300 rounded px-3 py-1 text-sm">
                        @error('name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <td class="font-medium text-[#2d326b] px-4 py-2">Type</td>
                    <td class="px-4 py-2">
                        <div class="w-1/2 max-w-xs">
                            <x-select-dropdown name="type" id="type" value="{{ old('name', $maintenance->type) }}"
                                :options="[
                                    'EPL' => 'EPL',
                                    'OPL' => 'OPL',
                                ]" />
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</form>

@endsection
