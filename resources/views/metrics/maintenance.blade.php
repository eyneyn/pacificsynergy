@extends('layouts.app')

@section('content')

    <!-- Back Button -->
    <a href="{{url('metrics/configuration')}}" class="flex items-center text-sm text-gray-500 hover:text-[#2d326b] mb-4">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        Configuration
    </a>
    <h2 class="text-2xl font-bold text-[#2d326b]">Maintenance</h2>

        <div class="flex flex-col md:flex-row md:items-center justify-between mt-5 mb-6 gap-4">
              <!-- Search bar with filter -->
      <div class="flex items-center justify-between w-full max-w-lg px-4 py-1 border rounded-md shadow-sm bg-white">
            <!-- Search Input with Icon -->
            <div class="flex items-center flex-grow">
                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M16.65 16.65A7.5 7.5 0 1 0 3 10a7.5 7.5 0 0 0 13.65 6.65z"></path>
                </svg>
                <input type="text"
                    placeholder="Search machine or others by name"
                    class="w-full border-none text-sm text-gray-700 placeholder-gray-400"
                />
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center space-x-2 ml-4">
                <!-- Filter Button -->
                <button class="flex items-center px-3 py-1.5 border rounded-md text-sm text-gray-600 hover:bg-gray-100">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v2a1 1 0 0 1-.293.707L15 13.414V19a1 1 0 0 1-.553.894l-4 2A1 1 0 0 1 9 21v-7.586L3.293 6.707A1 1 0 0 1 3 6V4z" />
                    </svg>
                    Filter
                </button>

                <!-- View Toggle Buttons -->
                <button class="p-2 rounded-md hover:bg-gray-100">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                </button>
                <button class="p-2 rounded-md bg-gray-100">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h4v4H4V6zm6 0h4v4h-4V6zm6 0h4v4h-4V6zM4 12h4v4H4v-4zm6 0h4v4h-4v-4zm6 0h4v4h-4v-4zM4 18h4v4H4v-4zm6 0h4v4h-4v-4zm6 0h4v4h-4v-4z" />
                    </svg>
                </button>
            </div>
      </div>

            <button data-modal-target="new-machine-modal" data-modal-toggle="new-machine-modal" 
                class="inline-flex items-center gap-2 p-3 py-2 bg-[#323B76] hover:bg-[#444d90] border border-[#323B76] text-white text-sm font-medium rounded-md">
                <x-icons-plus-circle class="w-4 h-4 text-white" />
                <span>New Machine / Others</span>
           </button>
    </div>

        <!-- Modal -->
<div id="new-machine-modal" tabindex="-1" aria-hidden="true"
     class="hidden fixed inset-0 z-50 flex justify-center items-center w-full p-4 overflow-x-hidden overflow-y-auto backdrop-blur-sm">
  <div class="relative w-full max-w-lg max-h-[90vh]">
    <!-- Modal content -->
    <div class="relative bg-white rounded-2xl shadow-xl border border-gray-200">
      
      <!-- Modal header -->
      <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 rounded-t">
        <h3 class="text-xl font-semibold text-[#323B76]">New Machine / Others</h3>
        <button type="button" class="text-gray-400 hover:text-[#323B76] rounded-lg text-sm w-8 h-8 flex items-center justify-center"
                data-modal-hide="new-machine-modal">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <!-- Modal body -->
      <form action="{{ route('maintenance.store') }}" method="POST" class="px-6 py-4 space-y-5 overflow-y-auto max-h-[70vh]">
        @csrf

        <!-- Machine Name -->
        <div>
          <label for="name" class="block text-sm font-medium text-[#323B76]">Name</label>
          <input type="text" id="name" name="name"
                 class="mt-1 block w-full text-sm rounded-md border-gray-300 shadow-sm focus:ring-[#323B76] focus:border-[#323B76]">
        </div>

        <!-- Types -->
        <div>
          <label for="type" class="block text-sm font-medium text-[#323B76]">Type</label>
          <select name="type" id="type"
                  class="mt-1 block w-full text-sm rounded-md border-gray-300 shadow-sm focus:ring-[#323B76] focus:border-[#323B76]">
            <option value=""></option>
            <option value="EPL">EPL</option>
            <option value="OPL">OPL</option>
          </select>
        </div>

      <!-- Modal footer -->
      <div class="flex justify-end items-center gap-3 px-6 py-4 border-t border-gray-200 rounded-b">
        <button type="button" data-modal-hide="new-machine-modal"
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

        <!-- Maintenance Table -->
        <table class="w-full mt-4 text-sm text-left rtl:text-right border border-[#35408e]">
            <thead class="text-xs text-white uppercase bg-[#35408e]">
                <tr>
                    <th class="px-6 py-3">Machine / Others</th>
                    <th class="px-6 py-3">Type</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($maintenances as $maintenance)
                    <tr 
                        class="bg-white border-b border-[#35408e] hover:bg-[#e5f4ff] cursor-pointer"
                        data-modal-target="edit-maintenance-modal" 
                        data-modal-toggle="edit-maintenance-modal"
                        data-id="{{ $maintenance->id }}"
                        data-description="{{ $maintenance->name }}"
                        data-size="{{ $maintenance->type }}"
                    >
                        <td class="px-6 py-4 text-[#35408e]">{{ $maintenance->name }}</td>
                        <td class="px-6 py-4">{{ $maintenance->type }}</td>
                    </tr>
                @empty
                  <tr>
                      <td colspan="2" class="text-center px-6 py-4 text-[#35408e]">No maintenance entries found.</td>
                  </tr>
              @endforelse
            </tbody>
        </table>

<!-- Edit Maintenance Modal -->
<div id="edit-maintenance-modal" tabindex="-1" aria-hidden="true"
     class="hidden fixed inset-0 z-50 flex justify-center items-center w-full p-4 overflow-x-hidden overflow-y-auto backdrop-blur-sm">
  <div class="relative w-full max-w-lg max-h-[90vh]">
    <!-- Modal content -->
    <div class="relative bg-white rounded-2xl shadow-xl border border-gray-200">
      
      <!-- Modal header -->
<!-- Modal header -->
<div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 rounded-t">
  <h3 class="text-xl font-semibold text-[#323B76]">Edit Machine / Others</h3>

  <div class="flex items-center gap-2">
    <!-- Delete Icon -->
<form id="icon-delete-maintenance-form"
      data-delete-type="maintenance"
      action="{{ route('maintenance.destroy', 0) }}"
      method="POST">
  @csrf
      @method('DELETE')
      <input type="hidden" name="id" id="icon_delete_maintenance_id">
      <button type="submit"
              class="text-red-600 hover:text-red-800 rounded-lg text-sm w-8 h-8 flex items-center justify-center"
              title="Delete">
<x-icons-delete class="w-5 h-5" />
      </button>
    </form>

    <!-- Close Icon -->
    <button type="button"
            class="text-gray-400 hover:text-[#323B76] rounded-lg text-sm w-8 h-8 flex items-center justify-center"
            data-modal-hide="edit-maintenance-modal" title="Close">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M6 18L18 6M6 6l12 12"/>
      </svg>
    </button>
  </div>
</div>


      <!-- Modal body -->
      <form id="edit-maintenance-form" method="POST" class="px-6 py-4 space-y-5 overflow-y-auto max-h-[70vh]">
        @csrf
        @method('PATCH')

        <input type="hidden" name="id" id="edit_maintenance_id">

        <!-- Machine Name -->
        <div>
          <label for="edit_name" class="block text-sm font-medium text-[#323B76]">Name</label>
          <input type="text" id="edit_name" name="name"
                 class="mt-1 block w-full text-sm rounded-md border-gray-300 shadow-sm focus:ring-[#323B76] focus:border-[#323B76]">
        </div>

        <!-- Category -->
        <div>
          <label for="edit_type" class="block text-sm font-medium text-[#323B76]">Type</label>
          <select name="type" id="edit_type"
                  class="mt-1 block w-full text-sm rounded-md border-gray-300 shadow-sm focus:ring-[#323B76] focus:border-[#323B76]">
            <option value="EPL">EPL</option>
            <option value="OPL">OPL</option>
          </select>
        </div>

        <!-- Modal footer -->
      <div class="flex justify-end items-center gap-3 px-6 py-4 border-t border-gray-200 rounded-b">
          <button type="button" data-modal-hide="edit-maintenance-modal"
                  class="text-[#323B76] bg-white border border-[#323B76] hover:bg-gray-50 focus:ring-4 focus:ring-[#323B76]/30 font-medium rounded-lg text-sm px-5 py-2.5">
            Cancel
          </button>
          <button type="submit"
                  class="text-white bg-[#323B76] hover:bg-[#4450a0] focus:ring-4 focus:ring-[#1f2b6d] font-medium rounded-lg text-sm px-5 py-2.5">
            Update
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<x-delete-modal />

@endsection