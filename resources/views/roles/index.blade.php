@extends('layouts.app')

@section('content')

@php
    function badgeColor($name) {
        return match(true) {
            str_contains($name, 'dashboard') => 'bg-blue-50 text-blue-700 border-blue-200',
            str_contains($name, 'report') => 'bg-yellow-50 text-yellow-700 border-yellow-200',
            str_contains($name, 'analytics') => 'bg-purple-50 text-purple-700 border-purple-200',
            str_contains($name, 'employee') => 'bg-pink-50 text-pink-700 border-pink-200',
            str_contains($name, 'role') || str_contains($name, 'permission') => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            default => 'bg-gray-100 text-gray-700 border-gray-300',
        };
    }
    $permissionLabels = [
        'dashboard' => 'Admin Dashboard',
        'roles.permission' => 'Role Permission',
        'employees.index' => 'Employee Management',
        'employees.create' => 'Add New Employee',
        'employees.edit' => 'Edit Employee Information',
        'employees.delete' => 'Delete Employee',

        // Production Reports
        'analytics.dashboard' => 'Production Dashboard',
        'report.index' => 'Production Report',
        'report.view' => 'View Production Report',
        'report.add' => 'Add Report',
        'report.edit' => 'Edit Report',
        'report.validate' => 'Validate Report',
        'report.delete' => 'Delete Report',
        'analytics.index' => 'Analytics & Reports',
        'configuration.index' => 'Configuration',
    ];
@endphp


<h2 class="text-2xl font-bold text-[#2d326b]">Roles</h2>

<div class="flex flex-col md:flex-row md:items-center justify-between mt-5 mb-6 gap-4">
    <form method="GET" action="">
        <div class="w-full max-w-xs px-4 border border-[#d9d9d9] rounded-md shadow-md transition-all duration-200 hover:shadow-lg hover:border-[#2d326b]">
            <div class="flex items-center flex-grow">
                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M16.65 16.65A7.5 7.5 0 1 0 3 10a7.5 7.5 0 0 0 13.65 6.65z" />
                </svg>
                <input type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search roles"
                    class="w-full border-none text-sm text-gray-700 placeholder-gray-400"/>
            </div>
        </div>
    </form>

    <a href="{{ route('roles.create') }}"
        class="inline-flex items-center gap-2 p-3 py-2 bg-[#323B76] hover:bg-[#444d90] border border-[#323B76] text-white text-sm font-medium rounded-md">
        <x-icons-plus-circle class="w-4 h-4 text-white" />
        <span class="text-sm">New Roles</span>
    </a>
</div>

<!-- Role Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @forelse ($roles as $role)
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-5 flex flex-col justify-between h-full">
            <div class="mb-4">
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-lg font-semibold text-[#2d326b]">{{ $role->name }}</h3>
                    <span class="inline-block px-3 py-0.5 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">
                        {{ $role->users_count ?? 0 }} Manager{{ ($role->users_count ?? 0) != 1 ? 's' : '' }}
                    </span>
                </div>

                <p class="text-sm text-gray-600 mr-4 mb-5">
                    {{ $role->description ?? 'Manages access rights, permissions, and operational functions based on the designated role.' }}
                </p>

                <div class="flex flex-wrap gap-2">
                    @forelse($role->permissions as $permission)
                        <span class="inline-block px-3 py-1 text-xs font-medium border rounded-full {{ badgeColor($permission->name) }}">
                            {{ $permissionLabels[$permission->name] ?? $permission->name }}
                        </span>
                    @empty
                        <span class="text-xs text-gray-400 italic">No permissions</span>
                    @endforelse
                </div>
            </div>

            <div class="flex justify-between items-center mt-auto pt-3 border-t border-gray-100">
                <a href="#"
                    class="text-sm text-[#323B76] font-medium hover:underline">
                    View Users
                </a>
                <a href="{{ route('roles.edit', $role->id) }}"
                    class="text-sm px-4 py-1.5 border border-[#323B76] text-[#323B76] rounded-md hover:bg-[#f1f2fa] font-medium">
                    Manage {{ $role->name }}
                </a>
            </div>
        </div>
    @empty
        <p class="text-gray-500 italic text-center w-full col-span-2 mt-10">
            No roles found{{ request('search') ? ' for "' . request('search') . '"' : '' }}.
        </p>
    @endforelse
</div>

@if(request('search'))
    <div class="text-center mt-10">
        <p class="text-sm text-gray-500">
            Showing results for <span class="font-medium text-[#2d326b]">"{{ request('search') }}"</span>
        </p>
        <a href="{{ route('roles.index') }}" class="text-xs text-blue-600 hover:underline">Clear search</a>
    </div>
@endif

@endsection
