@extends('layouts.app')
@section('content')

{{-- Page Title --}}
<h2 class="text-xl mb-2 font-bold text-[#23527c]">Maintenance</h2>

{{-- Back to Configuration Link --}}
<a href="{{ url('configuration/index') }}" class="text-xs text-gray-500 hover:text-[#23527c] mb-4 inline-flex items-center">
    <x-icons-back-confi/>
    Configuration
</a>

<div class="flex flex-col md:flex-row gap-8">
    {{-- Add Maintenance Form --}}
    <div class="w-full md:w-[320px] bg-white p-6 shadow-md border border-gray-200 self-start">
        <h3 class="text-lg font-semibold mb-4 text-[#23527c]">Add Maintenance</h3>
        <form action="{{ route('configuration.maintenance.store') }}" method="POST">
            @csrf
            <input type="hidden" name="_context" value="add">
            {{-- Name Input --}}
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Machine / Others</label>
                <input type="text" name="name" id="name"
                    value="{{ session('error_source') === 'add' && old('_context') === 'add' ? old('name') : '' }}"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 shadow-sm text-sm text-gray-700 focus:border-blue-500 focus:shadow-lg focus:outline-none"
                    required>
                @if ($errors->addForm->has('name') && old('_context') === 'add')
                    <p class="text-sm text-red-600 mt-1">{{ $errors->addForm->first('name') }}</p>
                @endif
            </div>
            {{-- Type Dropdown --}}
            <div class="mb-4">
                <label for="type" class="mb-1 block text-sm font-medium text-gray-700">Type</label>
                <x-select-dropdown
                    name="type"
                    id="type"
                    value="{{ session('error_source') === 'add' && old('_context') === 'add' ? old('type') : '' }}"
                    :options="['EPL' => 'EPL', 'OPL' => 'OPL']"
                    required
                />
                @if ($errors->addForm->has('type') && old('_context') === 'add')
                    <p class="text-sm text-red-600 mt-1">{{ $errors->addForm->first('type') }}</p>
                @endif
            </div>
            <button type="submit"
                class="w-full bg-[#5bb75b] border border-[#43a143] text-white px-3 py-1 hover:bg-[#42a542] text-sm">
                Add Maintenance
            </button>
        </form>
    </div>

    {{-- Maintenance Table Section --}}
    <div class="flex-1">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-6">
            {{-- Search Bar --}}
            <form method="GET" action="{{ route('configuration.maintenance.index') }}">
                <div class="px-4 border border-[#d9d9d9] shadow-md focus-within:border-blue-500 focus-within:shadow-lg focus-within:outline-none">
                    <div class="flex items-center">
                        <x-icons-search/>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search maintenance"
                            class="w-full border-none text-sm text-gray-700 placeholder-gray-400 focus:outline-none">
                    </div>
                </div>
            </form>
            {{-- Pagination --}}
            <div>
                {{ $maintenances->appends(request()->query())->links('pagination::tailwind') }}
            </div>
        </div>

        @php
            // Sorting logic
            $currentSort = request('sort', 'created_at');
            $currentDirection = request('direction', 'desc');
            $toggleDirection = $currentDirection === 'asc' ? 'desc' : 'asc';
        @endphp

        {{-- Success Message --}}
        @if (session('success'))
            <div class="mb-4 p-2 text-sm bg-green-100 text-green-800 border border-green-300">
                {{ session('success') }}
            </div>
        @endif

        {{-- Delete Error Message --}}
        @if ($errors->has('maintenance_delete'))
            <div class="bg-red-100 border border-red-400 text-red-700 p-2 relative mb-4 text-sm">
                {{ $errors->first('maintenance_delete') }}
            </div>
        @endif

        {{-- Maintenance Table --}}
        <table class="w-full text-sm text-left border border-[#E5E7EB] border-collapse">
            <thead>
                <tr class="text-xs text-white uppercase bg-[#35408e]">
                    {{-- Table Headers with Sort Links --}}
                    @foreach (['name' => 'Machine / Others', 'type' => 'Type'] as $field => $label)
                        <th class="p-2 border border-[#d9d9d9] text-center">
                            <x-table-sort-link 
                                :field="$field" 
                                :label="$label" 
                                :currentSort="$currentSort" 
                                :currentDirection="$currentDirection"
                                route="configuration.maintenance.index"
                            />
                        </th>
                    @endforeach
                    <th class="p-2 border border-[#d9d9d9] text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                {{-- Maintenance Rows --}}
                @forelse ($maintenances as $maintenance)
                    @php
                        $isCurrentRow = old('_id') == $maintenance->id && old('_context') === 'edit';
                    @endphp
                    <tr class="bg-white border-b border-[#35408e] hover:bg-[#f8faff]">
                        {{-- Edit Maintenance Row Form --}}
                        <form action="{{ route('configuration.maintenance.update', $maintenance->id) }}" method="POST" class="contents">
                            @csrf
                            @method('PUT')
                            <td class="p-1 border border-[#d9d9d9]">
                                <input type="text" name="name"
                                    value="{{ $isCurrentRow ? old('name') : $maintenance->name }}"
                                    class="w-full text-sm p-2 border border-gray-300 text-[#23527c] focus:border-blue-500 focus:shadow-lg focus:outline-none">
                                <input type="hidden" name="_id" value="{{ $maintenance->id }}">
                                <input type="hidden" name="_context" value="edit">
                                @if (session('error_source') === 'edit' && $errors->has('name') && $isCurrentRow)
                                    <p class="text-sm text-red-600">{{ $errors->first('name') }}</p>
                                @endif
                            </td>
                            <td class="p-1 border border-[#d9d9d9] text-center">
                                <x-select-dropdown
                                    name="type"
                                    id="type"
                                    value="{{ $isCurrentRow && session('error_source') === 'edit' ? old('type') : $maintenance->type }}"
                                    :options="['EPL' => 'EPL', 'OPL' => 'OPL']"
                                />
                            </td>
                            <td class="p-1 border border-[#d9d9d9] text-center">
                                <div class="flex justify-center items-center gap-3">
                                    <button type="submit"
                                        class="bg-[#5bb75b] border border-[#43a143] text-white px-3 py-1 hover:bg-[#42a542] text-sm">
                                        Save
                                    </button>
                        </form>
                                    {{-- Delete Maintenance Form --}}
                                    <form id="icon-delete-maintenance-form-{{ $maintenance->id }}"
                                        method="POST"
                                        action="{{ route('configuration.maintenance.destroy', $maintenance->id) }}"
                                        class="delete-maintenance-form"
                                        data-delete-type="maintenance"
                                        data-base-action="{{ route('configuration.maintenance.destroy', ':id') }}">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" id="edit_maintenance_id" value="{{ $maintenance->id }}">
                                        <input type="hidden" id="edit_maintenance_name" value="{{ $maintenance->name }}">
                                        <button type="submit"
                                            class="inline-flex items-center gap-1 px-3 py-1 bg-red-600 hover:bg-red-700 border border-red-700 text-white text-sm font-medium transition-colors duration-200"
                                            title="Delete Standard">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-2 border border-[#d9d9d9] text-gray-600 text-center">No matching records found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Delete Modal Component --}}
<x-delete-modal />

@endsection
