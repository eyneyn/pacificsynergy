@extends('layouts.app')

@section('content')

<div class="mb-5 gap-4 md:gap-0 "> 
    <h2 class="text-2xl font-bold text-[#2d326b]">Production Configuration</h2>
</div>

    <!-- Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-5">
        
        <!-- Defect Monitoring -->
        <div class="bg-white rounded-xl shadow border border-gray-200 p-5 hover:shadow-md transition">
            <h3 class="text-lg font-semibold text-[#323B76] mb-2">Defect</h3>
            <p class="text-sm text-gray-600 mb-4">Manage defect types and severity configurations.</p>
            <div class="flex justify-between items-center">
                <a href="{{ route('metrics.defect.index')}}" class="text-sm text-white bg-[#323B76] hover:bg-[#2d326b] px-4 py-2 rounded-md font-medium">
                    Manage
                </a>
            </div>
        </div>

        <!-- Maintenance Department -->
        <div class="bg-white rounded-xl shadow border border-gray-200 p-5 hover:shadow-md transition">
            <h3 class="text-lg font-semibold text-[#323B76] mb-2">Maintenance</h3>
            <p class="text-sm text-gray-600 mb-4">Set department codes and responsibilities.</p>
            <div class="flex justify-between items-center">
                <a href="{{ route('metrics.maintenance.index')}}" class="text-sm text-white bg-[#323B76] hover:bg-[#2d326b] px-4 py-2 rounded-md font-medium">
                    Manage
                </a>
            </div>
        </div>

        <!-- Formula -->
        <div class="bg-white rounded-xl shadow border border-gray-200 p-5 hover:shadow-md transition">
            <h3 class="text-lg font-semibold text-[#323B76] mb-2">Formula</h3>
            <p class="text-sm text-gray-600 mb-4">Maintain product formulas and references.</p>
            <div class="flex justify-between items-center">
                <a href="{{ route('metrics.formula')}}" class="text-sm text-white bg-[#323B76] hover:bg-[#2d326b] px-4 py-2 rounded-md font-medium">
                    Manage
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    <!-- Column 1: Defect Monitoring -->
    <div class="bg-white rounded-xl border border-gray-200 shadow p-6">
        <h3 class="text-lg font-semibold text-[#323B76] mb-2">Defect</h3>
        <p class="text-sm text-gray-500 mb-4">Recent issues identified during inspection.</p>

        <div class="divide-y">
            @forelse ($defects as $defect)
                <div class="flex flex-col gap-1 py-3 hover:bg-gray-50 transition">
                    <div class="flex justify-between items-center">
                        <h4 class="font-medium text-gray-800">{{ $defect->defect_name }}</h4>
                        
                        @php
                            $badgeColor = match(strtolower($defect->category)) {
                                'caps' => 'bg-blue-600',
                                'bottle' => 'bg-green-600',
                                'label' => 'bg-purple-600',
                                'carton' => 'bg-yellow-500 text-black',
                                default => 'bg-gray-500'
                            };
                        @endphp

                        <span class="text-xs font-semibold text-white px-2 py-0.5 rounded-md {{ $badgeColor }}">
                            {{ ucfirst($defect->category) }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500">{{ $defect->description ?? 'No description' }}</p>
                </div>
            @empty
                <p class="text-sm text-gray-500">No defects found.</p>
            @endforelse
        </div>
    </div>

    <!-- Column 2: Maintenance Department -->
    <div class="bg-white rounded-xl border border-gray-200 shadow p-6">
        <h3 class="text-lg font-semibold text-[#323B76] mb-2">Maintenance</h3>
        <p class="text-sm text-gray-500 mb-4">Teams assigned to specific tasks.</p>

        <div class="divide-y">
        @forelse ($maintenances as $maintenance)
            <div class="flex flex-col gap-1 py-3 hover:bg-gray-50 transition">
                <div class="flex justify-between items-center">
                    <h4 class="font-medium text-gray-800">{{ $maintenance->name }}</h4>
                    
                    @php
                        $badgeColor = match(strtolower($maintenance->type)) {
                            'epl' => 'bg-purple-600',
                            'opl' => 'bg-yellow-500 text-black',
                            default => 'bg-gray-500'
                        };
                    @endphp

                    <span class="text-xs font-semibold text-white px-2 py-0.5 rounded-md {{ $badgeColor }}">
                        {{ ucfirst($maintenance->type) }}
                    </span>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-500">No machines / others found.</p>
        @endforelse
    </div>
    </div>

    <!-- Column 3: Active Lines -->
    <div class="space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-[#323B76]">Active Lines</h3>
                    <p class="text-xs text-gray-500">Currently running production lines.</p>
                </div>
                <!-- New Line Button -->
                <button data-modal-target="new-line-modal" data-modal-toggle="new-line-modal" 
                    class="inline-flex items-center gap-2 p-2 bg-[#323B76] hover:bg-[#444d90] border border-[#323B76] text-white text-xs font-medium rounded-md">
                    <x-icons-plus-circle class="w-4 h-4 text-white" />
                    <span>Line</span>
                </button>
            </div>

    <ul class="text-sm space-y-2">
        @forelse ($lines as $line)
    <li class="flex items-center justify-between cursor-pointer hover:bg-gray-50 p-2 rounded"
                                data-modal-target="edit-line-modal" 
                            data-modal-toggle="edit-line-modal"
        data-id="{{ $line->id }}"
        data-line_number="{{ $line->line_number }}"
        data-status="{{ $line->status }}">
        <span>Line {{ $line->line_number }}</span>
        <span class="text-xs font-semibold px-2 py-0.5 rounded
            {{ $line->status === 'Active' ? 'text-green-700 bg-green-100' : 'text-red-700 bg-red-100' }}">
            {{ $line->status }}
        </span>
    </li>
        @empty
            <li class="text-gray-500 text-sm">No lines available.</li>
        @endforelse
    </ul>
        </div>
    </div>

        <!-- New Line Modal -->
<div id="new-line-modal" tabindex="-1" aria-hidden="true"
     class="hidden fixed inset-0 z-50 justify-center items-center w-full p-4 overflow-x-hidden overflow-y-auto backdrop-blur-sm bg-black/20">        
     <div class="relative w-full max-w-xs max-h-[90vh]">
            <!-- Modal content -->
            <div class="relative bg-white rounded-2xl shadow-xl border border-gray-200">
            
            <!-- Modal header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 rounded-t">
                <h3 class="text-xl font-semibold text-[#323B76]">New Line</h3>
                <button type="button" class="text-gray-400 hover:text-[#323B76] rounded-lg text-sm w-8 h-8 flex items-center justify-center"
                        data-modal-hide="new-line-modal">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"/>
                </svg>
                </button>
            </div>

            <!-- Modal body -->
            <form action="{{ route('configuration.store') }}" method="POST" class="px-6 py-4 space-y-5 overflow-y-auto max-h-[70vh]">
                @csrf

                <!-- Line Number -->
                <div>
                    <label for="line_number" class="block text-sm font-medium text-[#323B76]">Line Number</label>
<input type="number" id="line_number" name="line_number"
       class="mt-1 block w-full max-w-xs border border-gray-300 rounded px-3 py-1 text-sm">
                               <x-input-error :messages="$errors->get('line_number')" class="mt-2 text-sm text-[#FF2C2C]" />
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-[#323B76]">Status</label>
                    <div class="mt-1 max-w-xs">
                        <x-select-dropdown name="status" id="status" required
                            :options="[
                                'Active' => 'Active',
                                'Inactive' => 'Inactive'
                            ]"
                        />
                    </div>                
                </div>
                

            <!-- Modal footer -->
            <div class="flex justify-end items-center gap-3 px-6 py-4 border-t border-gray-200 rounded-b">
                <button type="button" data-modal-hide="new-line-modal"
                        class="text-[#323B76] bg-white border border-[#323B76] hover:bg-gray-50 focus:ring-4 focus:ring-[#323B76]/30 font-medium rounded-lg text-sm px-5 py-2.5">
                Cancel
                </button>
                <button type="submit"
                        class="text-white bg-[#323B76] hover:bg-[#4450a0] focus:ring-4 focus:ring-[#1f2b6d] font-medium rounded-lg text-sm px-5 py-2.5">
                Save
                </button>
                </form>
            </div>
            </div>
        </div>
        </div>

<!-- Edit Line Modal -->
<div id="edit-line-modal" tabindex="-1" aria-hidden="true"
     class="hidden fixed inset-0 z-50 flex justify-center items-center w-full p-4 overflow-x-auto backdrop-blur-sm bg-black/20">
    
    <div class="relative w-full max-w-xs bg-white rounded-xl shadow-xl border border-gray-200">
        <!-- Modal header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 rounded-t">
            <h3 class="text-xl font-semibold text-[#323B76]">Edit Line</h3>
            <div class="flex items-center gap-2">
                <!-- Delete Icon -->
                <form id="icon-delete-line-form"
                      data-delete-type="line"
                      data-base-action="{{ route('configuration.destroy', ':id') }}"
                      method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="id" id="icon_delete_line_id">
                    <button type="submit"
                            class="text-red-600 hover:text-red-800 rounded-lg text-sm w-8 h-8 flex items-center justify-center"
                            title="Delete Line">
                        <x-icons-delete class="w-5 h-5" />
                    </button>
                </form>

                <button type="button" data-modal-hide="edit-line-modal" class="text-gray-400 hover:text-[#323B76]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Modal Form -->
        <form id="edit-line-form" method="POST" class="px-6 py-4 space-y-4">
            @csrf
            @method('PATCH')
            <input type="hidden" id="edit_line_id" name="id">

            <!-- Line Number -->
            <div>
                <label for="edit_line_number" class="block text-sm font-medium text-[#323B76]">Line Number</label>
                <input type="number" id="edit_line_number" name="line_number" disabled
                       class="mt-1 block w-full max-w-xs rounded-md border-gray-300 px-3 py-1 text-sm shadow-sm focus:ring-[#323B76] focus:border-[#323B76]">
            </div>

            <!-- Status Dropdown -->
            <div>
                <label for="edit_status" class="block text-sm font-medium text-[#323B76]">Status</label>
                <div class="mt-1 max-w-xs">
                    <x-select-dropdown name="status" id="edit_status" required
                        :options="[
                            'Active' => 'Active',
                            'Inactive' => 'Inactive'
                        ]"
                    />
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end gap-2 pt-4 border-t">
                <button type="button" data-modal-hide="edit-line-modal"
                        class="text-[#323B76] bg-white border border-[#323B76] rounded px-4 py-2 text-sm">
                    Cancel
                </button>
                <button type="submit"
                        class="text-white bg-[#323B76] hover:bg-[#4450a0] rounded px-4 py-2 text-sm">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>




</div>

<x-delete-modal />

<!-- ✅ Auto-show modal script if there are errors -->
@if ($errors->any() && session('show_modal') === 'new-line')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('new-line-modal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex'); // Ensure modal background overlay activates
            }
        });
    </script>
@endif



@endsection
