@extends('layouts.app')

@section('content')

{{-- Page Title --}}
<h2 class="text-2xl font-bold text-[#2d326b]">Employees</h2>

{{-- Search Bar and Add Button --}}
<div class="flex flex-col md:flex-row md:items-center justify-between mt-5 mb-6 gap-4">
    <form method="GET" action="">
        <div class="w-full max-w-xs px-4 border border-[#d9d9d9] rounded-md shadow-md transition-all duration-200 hover:shadow-lg hover:border-[#2d326b]">
            <div class="flex items-center flex-grow">
                {{-- Search Icon --}}
                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M16.65 16.65A7.5 7.5 0 1 0 3 10a7.5 7.5 0 0 0 13.65 6.65z" />
                </svg>
                <input type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search employee"
                    class="w-full border-none text-sm text-gray-700 placeholder-gray-400"/>
            </div>
        </div>
    </form>

    <a href="{{ route('employees.create') }}"
        class="inline-flex items-center gap-2 p-3 py-2 bg-[#323B76] hover:bg-[#444d90] border border-[#323B76] text-white text-sm font-medium rounded-md">
        <x-icons-plus-circle class="w-4 h-4 text-white" />
        <span class="text-sm">Add User</span>
    </a>
</div>

{{-- Users Table --}}
<table class="w-full mt-4 text-sm text-left rtl:text-right border border-[#E5E7EB] border-collapse">
    <thead class="text-xs text-white uppercase bg-[#35408e]">
        <tr>
            {{-- Full Name --}}
            <th class="px-6 py-2 border border-[#d9d9d9] text-center">
                <a href="" class="flex justify-center items-center gap-1 text-white no-underline">
                    Full Name
                    {{-- Sort Icon --}}
                    <svg class="w-4 h-4" viewBox="0 0 512 512" fill="currentColor">
                        <path d="M304 96h48v320h48l-72 80-72-80h48V96zM64 192h128v32H64v-32zm32 64h96v32H96v-32zm32 64h64v32h-64v-32zm32 64h32v32h-32v-32z"/>
                    </svg>
                </a>
            </th>
            {{-- Email --}}
            <th class="px-6 py-2 border border-[#d9d9d9] text-center">
                <a href="" class="flex justify-center items-center gap-1 text-white no-underline">
                    <svg class="w-4 h-4" viewBox="0 0 512 512" fill="currentColor">
                        <path d="M304 96h48v320h48l-72 80-72-80h48V96zM64 192h128v32H64v-32zm32 64h96v32H96v-32zm32 64h64v32h-64v-32zm32 64h32v32h-32v-32z"/>
                    </svg>
                    Email
                </a>
            </th>
            {{-- Position --}}
            <th class="px-6 py-2 border border-[#d9d9d9] text-center">
                <a href="" class="flex justify-center items-center gap-1 text-white no-underline">
                    Position
                    <svg class="w-4 h-4" viewBox="0 0 512 512" fill="currentColor">
                        <path d="M304 96h48v320h48l-72 80-72-80h48V96zM64 192h128v32H64v-32zm32 64h96v32H96v-32zm32 64h64v32h-64v-32zm32 64h32v32h-32v-32z"/>
                    </svg>
                </a>
            </th>
            {{-- Department --}}
            <th class="px-6 py-2 border border-[#d9d9d9] text-center">
                <a href="" class="flex justify-center items-center gap-1 text-white no-underline">
                    Department
                    <svg class="w-4 h-4" viewBox="0 0 512 512" fill="currentColor">
                        <path d="M304 96h48v320h48l-72 80-72-80h48V96zM64 192h128v32H64v-32zm32 64h96v32H96v-32zm32 64h64v32h-64v-32zm32 64h32v32h-32v-32z"/>
                    </svg>
                </a>
            </th>
            {{-- Joined Date --}}
            <th class="px-6 py-2 border border-[#d9d9d9] text-center">
                <a href="" class="flex justify-center items-center gap-1 text-white no-underline">
                    Joined Date
                    <svg class="w-4 h-4" viewBox="0 0 512 512" fill="currentColor">
                        <path d="M304 96h48v320h48l-72 80-72-80h48V96zM64 192h128v32H64v-32zm32 64h96v32H96v-32zm32 64h64v32h-64v-32zm32 64h32v32h-32v-32z"/>
                    </svg>
                </a>
            </th>
        </tr>
    </thead>
    <tbody class="text-center text-sm text-gray-800">
        @forelse ($users as $user)
            <tr class="border-t border-gray-200 hover:bg-gray-100 transition cursor-pointer"
                onclick="window.location='{{ route('employees.view', $user->id) }}'">
                <td class="px-6 py-3 flex items-center gap-3">
                    <img src="{{ asset('storage/' . $user->photo) }}" alt="Profile" class="w-9 h-9 p-1 rounded-full object-cover border border-gray-300">
                    <span class="text-sm font-medium text-[#2d326b]">{{ $user->first_name }} {{ $user->last_name }}</span>
                </td>
                <td class="px-6 py-3 text-gray-600">{{ $user->email }}</td>
                <td class="px-6 py-3 text-gray-600">
                    @foreach($user->roles as $role)
                        {{ $role->name }}
                    @endforeach
                </td>
                <td class="px-6 py-3 text-gray-600">{{ $user->department }}</td>
                <td class="px-6 py-3 text-gray-600">{{ $user->created_at->format('F j, Y') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center py-6 text-gray-500 italic">No users found.</td>
            </tr>
        @endforelse
    </tbody>
</table>

@endsection
