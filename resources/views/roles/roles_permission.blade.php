@extends('layouts.app')

@section('content')

    <div class="flex flex-col md:flex-row md:items-center justify-between mb-5 gap-4 md:gap-0 ">
      
      <h2 class="text-2xl font-bold text-[#2d326b]">Roles</h2>

      <!-- Add User -->
      <div class="flex flex-col sm:flex-row items-center gap-3">
              <a href=""
                  class="inline-flex items-center gap-2 p-3 py-2 bg-transparent border border-[#3c49a3] hover:bg-[#f9fafb] text-[#2d326b] text-sm font-medium rounded-md">
                  <x-icons-plus-circle class="w-4 h-4 text-[#3c49a3]" />
                  <span>Assign</span>
              </a>
              <a href="{{ route('roles.create') }}"
                  class="inline-flex items-center gap-2 p-3 py-2 bg-[#323B76] hover:bg-[#444d90] border border-[#323B76] text-white text-sm font-medium rounded-md">
                  <x-icons-plus-circle class="w-4 h-4 text-white" />
                  <span>New Roles</span>
              </a>
      </div>
    </div>

    <!-- Toggle Nav for All Roles / Permission -->
        <div class="flex space-x-4 border-b border-[#E5E7EB] mb-4">
            <a href="#roles" class="cursor-pointer px-4 py-2 text-sm  text-[#2d326b] border-b-2 border-[#2d326b] font-semibold hover:text-[#6B7280] transition duration-200">
                All Roles
            </a>
            <a href="#permissions" class="cursor-pointer px-4 py-2 text-sm  text-gray-500 border-b-2 border-transparent font-semibold hover:text-[#2d326b] hover:border-[#2d326b] transition duration-200">
                Permission
            </a>
        </div>

        <!-- Search bar with filter -->
<div class="flex items-center justify-between w-full px-4 py-2 border rounded-md shadow-sm bg-white">
    <!-- Search Input with Icon -->
    <div class="flex items-center flex-grow">
        <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" stroke-width="2"
            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M16.65 16.65A7.5 7.5 0 1 0 3 10a7.5 7.5 0 0 0 13.65 6.65z"></path>
        </svg>
        <input type="text"
            placeholder="Search Roles by Title, Teams or any related keywords"
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


@endsection