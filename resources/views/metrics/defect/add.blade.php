@extends('layouts.app')

@section('content')

<!-- Back Button -->
<a href="{{ url('metrics/defect/index') }}" class="flex items-center text-sm text-gray-500 hover:text-[#2d326b] mb-4">
    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
    </svg>
    Defect
</a>

<form action="{{ route('metrics.defect.store') }}" method="POST">
    @csrf

    <div class="flex justify-center">
        <div class="w-full max-w-2xl bg-white rounded-sm border border-gray-200 p-6 shadow-md space-y-5 transition-all duration-300 hover:shadow-xl hover:border-[#E5E7EB]">

        <div class="mb-6 flex items-center justify-between">
            <div class="flex-1 text-center">
                <h2 class="text-xl font-bold text-[#2d326b]">New Defect</h2>
            </div>
        </div>

        <table class="w-full max-w-2xl text-sm border border-gray-200 shadow-sm">
            <thead class="bg-gray-100 text-[#2d326b]">
                <tr>
                    <th class="text-left px-4 py-3 w-1/4">Defect Field</th>
                    <th class="text-left px-4 py-3 w-3/4">Value</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 divide-y divide-gray-200">
                <tr>
                    <td class="font-medium text-[#2d326b] px-4 py-2">Defect Name</td>
                    <td class="px-4 py-2">
                        <input type="text" name="defect_name" required
                            class="w-full border border-gray-300 rounded px-3 py-1 text-sm">
                        @error('defect_name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <td class="font-medium text-[#2d326b] px-4 py-2">Category</td>
                    <td class="px-4 py-2">
                        <x-select-dropdown name="category" id="category" required
                            :options="[
                                'Caps' => 'Caps',
                                'Bottle' => 'Bottle',
                                'Label' => 'Label',
                                'Carton' => 'Carton',
                            ]" />
                    </td>
                </tr>
                <tr>
                    <td class="font-medium text-[#2d326b] px-4 py-2">Description</td>
                    <td class="px-4 py-2">
                        <textarea name="description" id="description" rows="3"
                            class="w-full border border-gray-300 rounded px-3 py-1 text-sm">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <td colspan="2" class="p-3">
                        <div class="flex justify-end">
                            <button type="submit"
                                class="inline-flex items-center px-3 py-2 bg-[#323B76] hover:bg-[#444d90] border border-[#323B76] text-white text-sm font-medium rounded-md shadow-sm">
                                Save
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    </div>
</form>

@endsection
