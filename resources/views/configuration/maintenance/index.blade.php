@extends('layouts.app')

@section('content')
<!-- Back Button -->
<a href="{{ url('configuration/index') }}" class="flex items-center text-sm text-gray-500 hover:text-[#2d326b] mb-4">
    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
    </svg>
    Configuration
</a>

<h2 class="text-2xl mb-5 font-bold text-[#2d326b]">Maintenance</h2>

<div class="flex flex-col md:flex-row gap-8 mt-4">
    <!-- ADD MAINTENANCE FORM -->
    <div class="w-full md:w-[320px] bg-white p-6 rounded-md shadow-md border border-gray-200 self-start">
        <h3 class="text-lg font-semibold mb-4 text-[#2d326b]">Add Maintenance</h3>
        <form action="{{ route('configuration.maintenance.store') }}" method="POST">
            @csrf
            <input type="hidden" name="_context" value="add">
            <!-- Name Input -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Machine / Others</label>
                <input type="text" name="name" id="name"
                    value="{{ session('error_source') === 'add' && old('_context') === 'add' ? old('name') : '' }}"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm text-gray-700"
                    required>
                @if ($errors->addForm->has('name') && old('_context') === 'add')
                    <p class="text-sm text-red-600 mt-1">{{ $errors->addForm->first('name') }}</p>
                @endif
            </div>
            <!-- Type Dropdown -->
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
            <!-- Submit Button -->
            <button type="submit"
                class="bg-[#2d326b] text-white px-4 py-2 rounded-md hover:bg-[#1f234d] text-sm w-full">
                Add Maintenance
            </button>
        </form>
    </div>

    <!-- MAINTENANCE TABLE -->
    <div class="flex-1">
        <!-- Search bar with filter -->
        <form method="GET" action="{{ route('configuration.maintenance.index') }}">
            <div class="w-full max-w-xs mb-4 px-4 border border-[#d9d9d9] rounded-md shadow-md hover:shadow-lg hover:border-[#2d326b]">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-4.35-4.35M16.65 16.65A7.5 7.5 0 1 0 3 10a7.5 7.5 0 0 0 13.65 6.65z" />
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search maintenance"
                        class="w-full border-none text-sm text-gray-700 placeholder-gray-400">
                </div>
            </div>
        </form>

        @php
            $currentSort = request('sort');
            $currentDirection = request('direction') === 'asc' ? 'desc' : 'asc';
        @endphp

        <!-- Success Message -->
        @if (session('success'))
            <div class="mb-4 p-2 text-sm rounded bg-green-100 text-green-800 border border-green-300">
                {{ session('success') }}
            </div>
        @endif

        <!-- Delete Error Message -->
        @if ($errors->has('maintenance_delete'))
            <div class="bg-red-100 border border-red-400 text-red-700 p-2 rounded relative mb-4 text-sm">
                {{ $errors->first('maintenance_delete') }}
            </div>
        @endif

        <!-- Maintenance Table -->
        <table class="w-full text-sm text-left border border-[#E5E7EB] border-collapse">
            <thead class="text-xs text-white uppercase bg-[#35408e]">
                <tr>
                    <!-- Sortable Name Column -->
                    <th class="px-6 py-2 border border-[#d9d9d9]">
                        <a href="{{ route('configuration.maintenance.index', ['sort' => 'name', 'direction' => ($currentSort === 'name' ? $currentDirection : 'asc')]) }}"
                            class="flex items-center gap-1 text-white no-underline">
                            Machine / Others
                            <svg class="w-4 h-4 {{ $currentSort === 'name' ? 'opacity-100' : 'opacity-50' }}" fill="currentColor" viewBox="0 0 512 512">
                                <path d="M304 96h48v320h48l-72 80-72-80h48V96zM64 192h128v32H64v-32zm32 64h96v32H96v-32zm32 64h64v32h-64v-32zm32 64h32v32h-32v-32z"/>
                            </svg>
                        </a>
                    </th>
                    <!-- Sortable Type Column -->
                    <th class="px-6 py-2 border border-[#d9d9d9] text-center">
                        <a href="{{ route('configuration.maintenance.index', ['sort' => 'type', 'direction' => ($currentSort === 'type' ? $currentDirection : 'asc')]) }}"
                            class="flex justify-center items-center gap-1 text-white no-underline">
                            Type
                            <svg class="w-4 h-4 {{ $currentSort === 'type' ? 'opacity-100' : 'opacity-50' }}" fill="currentColor" viewBox="0 0 512 512">
                                <path d="M304 96h48v320h48l-72 80-72-80h48V96zM64 192h128v32H64v-32zm32 64h96v32H96v-32zm32 64h64v32h-64v-32zm32 64h32v32h-32v-32z"/>
                            </svg>
                        </a>
                    </th>
                    <!-- Action Column -->
                    <th class="px-6 py-2 border border-[#d9d9d9] text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($maintenances as $maintenance)
                    @php
                        $isCurrentRow = old('_id') == $maintenance->id && old('_context') === 'edit';
                    @endphp
                    <tr class="bg-white border-b border-[#35408e] hover:bg-[#f8faff]">
                        <!-- Edit Maintenance Row Form -->
                        <form action="{{ route('configuration.maintenance.update', $maintenance->id) }}" method="POST" class="contents">
                            @csrf
                            @method('PUT')
                            <td class="px-4 py-2 border border-[#d9d9d9]">
                                <input type="text" name="name"
                                    value="{{ $isCurrentRow ? old('name') : $maintenance->name }}"
                                    class="w-full text-sm p-2 border border-gray-300 rounded-sm text-[#2d326b]">
                                <input type="hidden" name="_id" value="{{ $maintenance->id }}">
                                <input type="hidden" name="_context" value="edit">
                                @if (session('error_source') === 'edit' && $errors->has('name') && $isCurrentRow)
                                    <p class="text-sm text-red-600 mt-1">{{ $errors->first('name') }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-2 border border-[#d9d9d9] text-center">
                                <x-select-dropdown
                                    name="type"
                                    id="type"
                                    value="{{ $isCurrentRow && session('error_source') === 'edit' ? old('type') : $maintenance->type }}"
                                    :options="['EPL' => 'EPL', 'OPL' => 'OPL']"
                                />
                            </td>
                            <td class="px-4 py-2 border border-[#d9d9d9] text-center">
                                <div class="flex justify-center items-center gap-3">
                                    <button type="submit"
                                        class="bg-[#2d326b] text-white px-4 py-2 rounded-md hover:bg-[#1f234d] text-sm">
                                        Save
                                    </button>
                        </form>
                                    <!-- Delete Maintenance Form -->
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
                                            class="text-red-600 hover:text-red-800 rounded-lg w-10 h-10 flex items-center justify-center"
                                            title="Delete Maintenance">
                                            <x-icons-delete class="w-5 h-5" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 border border-[#E5E7EB] text-center text-[#35408e]">
                            No maintenance entries found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $maintenances->appends(request()->query())->links('pagination::tailwind') }}
        </div>
    </div>
</div>

<!-- Delete Modal Component -->
<x-delete-modal />
@endsection
