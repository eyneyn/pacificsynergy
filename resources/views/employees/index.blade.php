@extends('layouts.app')
@section('title', content: 'User')
@section('content')

    <div class="mb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <h2 class="text-xl font-bold text-[#23527c]">Users</h2>
    </div>

@php
    $currentSort = request('sort', 'created_at');
    $currentDirection = request('direction', 'desc');
@endphp
{{-- Search Bar and Add Button --}}
<div class="flex flex-col md:flex-row md:items-center mt-5 mb-6 gap-4">
    <a href="{{ route('employees.create') }}"
        class="inline-flex items-center justify-center gap-1 p-2 bg-[#323B76] hover:bg-[#444d90] border border-[#323B76] text-white text-sm font-medium">
        <x-icons-plus-circle class="w-4 h-4 text-white" />
        <span class="text-sm">Add User</span>
    </a>
    <form method="GET" action="" class="w-full max-w-xl">
        <div class="w-full px-4 border border-[#d9d9d9] shadow-md focus-within:border-blue-500 focus-within:shadow-lg">
            <div class="flex items-center flex-grow">
                <x-icons-search class="w-4 h-4 shrink-0"/>
                <input type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search employee"
                    class="w-full border-none text-sm text-gray-700 placeholder-gray-400"/>
                {{-- keep the role filter while searching --}}
                @if(request('role'))
                    <input type="hidden" name="role" value="{{ request('role') }}">
                @endif
            </div>
        </div>
    </form>

        @if(request('role'))
            <a href="{{ route('employees.index') }}"
               class="text-sm text-blue-600 hover:underline">Clear role filter</a>
        @endif

</div>


{{-- Users Table --}}
<table class="w-full mt-4 text-sm text-left rtl:text-right border border-[#E5E7EB] border-collapse">
    <thead class="text-xs text-white uppercase bg-[#35408e]">
<tr>
            {{-- Full Name (sort by last_name, then first_name) --}}
            <th class="px-6 py-2 border border-[#d9d9d9] text-center">
                <x-table-sort-link
                    field="full_name"
                    label="Full Name"
                    :currentSort="$currentSort"
                    :currentDirection="$currentDirection"
                    route="employees.index"
                />
            </th>

            {{-- Email --}}
            <th class="px-6 py-2 border border-[#d9d9d9] text-center">
                <x-table-sort-link
                    field="email"
                    label="Email"
                    :currentSort="$currentSort"
                    :currentDirection="$currentDirection"
                    route="employees.index"
                />
            </th>

            {{-- Position (first role name) --}}
            <th class="px-6 py-2 border border-[#d9d9d9] text-center">
                <x-table-sort-link
                    field="position"
                    label="Position"
                    :currentSort="$currentSort"
                    :currentDirection="$currentDirection"
                    route="employees.index"
                />
            </th>

            {{-- Department --}}
            <th class="px-6 py-2 border border-[#d9d9d9] text-center">
                <x-table-sort-link
                    field="department"
                    label="Department"
                    :currentSort="$currentSort"
                    :currentDirection="$currentDirection"
                    route="employees.index"
                />
            </th>

            {{-- Joined Date --}}
            <th class="px-6 py-2 border border-[#d9d9d9] text-center">
                <x-table-sort-link
                    field="created_at"
                    label="Joined Date"
                    :currentSort="$currentSort"
                    :currentDirection="$currentDirection"
                    route="employees.index"
                />
            </th>
        </tr>
    </thead>
    </thead>
    <tbody class="text-center text-sm text-gray-800">
        @forelse ($users as $user)
            <tr class="border-t border-gray-200 hover:bg-gray-100 transition cursor-pointer"
                onclick="window.location='{{ route('employees.view', $user->id) }}'">
                <td class="p-2 ml-8 flex items-center gap-6">
<img src="{{ asset('storage/' . $user->photo) }}" alt="Profile"
     class="w-10 h-10 p-1 rounded-full object-cover border border-[#23527c]">
                         <span class="text-sm font-medium text-[#23527c]">{{ $user->first_name }} {{ $user->last_name }}</span>
                </td>
                <td class="p-2 border border-[#d9d9d9] text-gray-600 text-center">{{ $user->email }}</td>
                <td class="p-2 border border-[#d9d9d9] text-gray-600 text-center">
                    @foreach($user->roles as $role)
                        {{ $role->name }}
                    @endforeach
                </td>
                <td class="p-2 border border-[#d9d9d9] text-gray-600 text-center">{{ $user->department }}</td>
                <td class="p-2 border border-[#d9d9d9] text-gray-600 text-center">{{ $user->created_at->format('F j, Y') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center py-6 text-gray-600 italic">No users found.</td>
            </tr>
        @endforelse
    </tbody>
</table>

@endsection
