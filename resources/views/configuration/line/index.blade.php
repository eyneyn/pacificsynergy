@extends('layouts.app')

@section('content')

<!-- Back Button -->
<a href="{{ url('configuration/index') }}" class="flex items-center text-sm text-gray-500 hover:text-[#2d326b] mb-4">
    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
    </svg>
    Configuration
</a>

<h2 class="text-2xl mb-5 font-bold text-[#2d326b]">Line Production</h2>

<!-- Main Layout: Form + Table -->
<div class="flex flex-col md:flex-row gap-8 items-start">

    <!-- Add Line Form -->
    <div class="bg-white rounded-sm border border-gray-200 p-6 shadow-md space-y-5 transition-all duration-300 hover:shadow-xl hover:border-[#E5E7EB] w-full md:w-auto md:min-w-[300px]">
        <h3 class="text-lg font-semibold mb-4 text-[#2d326b]">Add Line</h3>
        <form action="{{ route('configuration.line.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="line_number" class="block text-sm font-medium text-gray-700">Line Number</label>
                <input type="number" name="line_number" id="line_number" required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm">
                @error('line_number')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <x-select-dropdown name="status" id="status" required
                    :options="['Active' => 'Active', 'Inactive' => 'Inactive']" />
            </div>
            <button type="submit"
                class="bg-[#2d326b] text-white px-4 py-2 rounded-md hover:bg-[#1f234d] text-sm">
                Add Line
            </button>
        </form>
    </div>

    <!-- Lines Table -->
    <div class="bg-white rounded-sm border border-gray-200 p-6 shadow-md space-y-5 transition-all duration-300 hover:shadow-xl hover:border-[#E5E7EB] flex-1">
        <h3 class="text-lg font-semibold mb-4 text-[#2d326b]">All Lines</h3>
        @if ($errors->has('line_delete'))
            <div class="bg-red-100 border border-red-400 text-red-700 p-2 rounded relative mb-4 text-sm" role="alert">
                <strong class="font-bold">Delete Failed:</strong>
                <span class="block sm:inline">{{ $errors->first('line_delete') }}</span>
            </div>
        @endif
        <table class="min-w-full table-auto text-sm border border-gray-200 rounded-md">
            <thead class="bg-gray-100 text-gray-700 text-left">
                <tr>
                    <th class="px-4 py-3 border-b">Line Number</th>
                    <th class="px-4 py-3 border-b text-center">Status</th>
                    <th class="px-4 py-3 border-b text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lines as $line)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 border-b text-[#2d326b]">Line {{ $line->line_number }}</td>
                    <td class="px-4 py-2 border-b">
                        <!-- Inline Edit Status Form -->
                        <form action="{{ route('configuration.line.update', $line->line_number) }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            @method('PUT')
                            <x-select-dropdown name="status" id="status" value="{{ old('status', $line->status) }}"
                                :options="['Active' => 'Active', 'Inactive' => 'Inactive']" />
                    </td>
                    <td class="px-4 py-2 border-b text-center">
                        <div class="flex justify-center items-center gap-3">
                            <button type="submit"
                                class="bg-[#2d326b] text-white px-4 py-2 rounded-md hover:bg-[#1f234d] text-sm">
                                Save
                            </button>
                        </form>
                        <!-- Delete Line Form -->
                        <form id="icon-delete-line-form-{{ $line->line_number }}"
                              data-delete-type="line"
                              data-base-action="{{ route('configuration.line.destroy', ':id') }}"
                              class="delete-line-form"
                              method="POST">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" id="edit_line_id" value="{{ $line->line_number }}">
                            <input type="hidden" id="edit_line_number" value="{{ $line->line_number }}">
                            <button type="submit"
                                class="text-red-600 hover:text-red-800 rounded-lg text-md w-10 h-10 flex items-center justify-center"
                                title="Delete Line">
                                <x-icons-delete class="w-5 h-5" />
                            </button>
                        </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Delete Modal Component -->
<x-delete-modal/>

@endsection
