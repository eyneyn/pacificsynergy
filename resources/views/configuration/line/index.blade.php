@extends('layouts.app')

@section('content')
<div class="mx-32">
    <h2 class="text-xl mb-2 font-bold text-[#23527c]">Line Production</h2>

    {{-- Back to Configuration Link --}}
    <a href="{{ url('configuration/index') }}" class="text-xs text-gray-500 hover:text-[#23527c] mb-4 inline-flex items-center">    
        <x-icons-back-confi/>
        Configuration
    </a>

    <!-- Main Layout: Form + Table -->
    <div class="flex flex-col md:flex-row gap-8 items-start">
        <!-- Left Column: Add Line Form -->
        <div class="w-full md:w-auto md:min-w-[300px] space-y-4">
            <div class="bg-white border border-gray-200 p-6 shadow-md space-y-5 transition-all duration-300 hover:shadow-xl hover:border-[#E5E7EB]">
                <h3 class="text-lg font-semibold mb-4 text-[#23527c]">Add Line</h3>
                <form action="{{ route('configuration.line.store') }}" method="POST">
                    @csrf
                    <!-- Line Number Input -->
                    <div class="mb-4">
                        <label for="line_number" class="block text-sm font-medium text-gray-700">Line Number</label>
                        <input type="number" name="line_number" id="line_number" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:shadow-lg focus:outline-none">
                        @error('line_number')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- Status Dropdown -->
                    <div class="mb-4">
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <x-select-dropdown name="status" id="status" required
                            :options="['Active' => 'Active', 'Inactive' => 'Inactive']" />
                    </div>
                    <button type="submit"
                        class="w-full bg-[#5bb75b] border border-[#43a143] text-white px-3 py-1 hover:bg-[#42a542] text-sm">
                        Add Line
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Right Column: Lines Table -->
        <div class="bg-white border border-gray-200 p-6 shadow-md space-y-5 transition-all duration-300 hover:shadow-xl hover:border-[#E5E7EB] flex-1">
            <h3 class="text-lg font-semibold mb-4 text-[#23527c]">All Lines</h3>
            <x-alert-message />
            <table class="w-full text-sm text-left border border-[#E5E7EB] border-collapse shadow-sm">
                <thead>
                    <tr class="text-xs text-white uppercase bg-[#35408e]">
                        <th class="p-2 border border-[#d9d9d9] text-center">Line Number</th>
                        <th class="p-2 border border-[#d9d9d9] text-center">Status</th>
                        <th class="p-2 border border-[#d9d9d9] text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lines as $line)
                    <tr class="bg-white border-b border-gray-200 hover:bg-[#e5f4ff] transition-colors duration-200">
                        <td class="p-1 border border-[#d9d9d9] text-[#23527c] font-bold text-center">
                            Line {{ $line->line_number }}
                        </td>
                        <td class="p-1 border border-[#d9d9d9] text-[#23527c]">
                            <!-- Inline Edit Status Form -->
                            <form action="{{ route('configuration.line.update', $line->line_number) }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <x-select-dropdown name="status" id="status" value="{{ old('status', $line->status) }}"
                                    :options="['Active' => 'Active', 'Inactive' => 'Inactive']" />
                        </td>
                        <td class="p-1 border border-[#d9d9d9] text-gray-600 text-center">
                            <div class="flex justify-center items-center gap-3">
                                <!-- Save Button for Status Update -->
                                <button type="submit"
                                    class="bg-[#5bb75b] border border-[#43a143] text-white px-3 py-1 hover:bg-[#42a542] text-sm">
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
                                        class="inline-flex items-center gap-1 px-3 py-1 bg-red-600 hover:bg-red-700 border border-red-700 text-white text-sm font-medium transition-colors duration-200"
                                        title="Delete Standard">
                                    Delete
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
</div>
@endsection
