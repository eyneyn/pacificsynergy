@extends('layouts.app')
@section('title', content: 'Roles')
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
        'user.dashboard' => 'Admin Dashboard',
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
        'analytics.index' => 'Analytics & Reports',
        'configuration.index' => 'Configuration',
    ];
@endphp

    <div class="mb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <h2 class="text-xl font-bold text-[#23527c]">Roles</h2>
    </div>

<div class="flex flex-col md:flex-row md:items-center mt-5 mb-6 gap-4">
    <a href="{{ route('roles.create') }}"
        class="inline-flex items-center justify-center gap-1 p-2 bg-[#323B76] hover:bg-[#444d90] border border-[#323B76] text-white text-sm font-medium">
        <x-icons-plus-circle class="w-2 h-2 text-white" />
        <span class="text-sm">New Roles</span>
    </a>
    <form method="GET" action="" class="w-full max-w-xl"> {{-- ~36rem --}}
        <div class="w-full px-4 border border-[#d9d9d9] shadow-md focus-within:border-blue-500 focus-within:shadow-lg">
            <div class="flex items-center gap-2">
                <x-icons-search class="w-4 h-4 shrink-0"/>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search roles"
                    class="flex-1 min-w-0 border-none bg-transparent text-sm text-gray-700 placeholder-gray-400"
                />
            </div>
        </div>
    </form>
</div>

<!-- Role Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @forelse ($roles as $role)
        <div class="bg-white border border-gray-200 shadow-[-1px_6px_5px_rgba(0,0,0.1,0.1)] hover:shadow-lg transition-all duration-200 p-5 flex flex-col justify-between h-full">
            <div class="mb-4">
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-lg font-semibold text-[#23527c]">{{ $role->name }}</h3>
                    <span class="inline-block px-3 py-2 text-xs font-medium text-[#23527c]">
                        {{ $role->users_count ?? 0 }} Employee{{ ($role->users_count ?? 0) != 1 ? 's' : '' }}
                    </span>
                </div>

                <p class="text-sm text-[#23527c] mr-4 mb-5">
                    {{ $role->description ?? 'Manages access rights, permissions, and operational functions based on the designated role.' }}
                </p>
            </div>

            <div class="flex justify-between items-center mt-auto pt-3 border-t border-gray-200">
                <a href="{{ route('employees.index', ['role' => $role->name]) }}"
                class="text-sm text-[#23527c] font-medium hover:underline">
                    View Users
                </a>
                <a href="{{ route('roles.edit', $role->id) }}"
                    class="text-sm px-4 py-1.5 bg-[#323B76] hover:bg-[#444d90] border border-[#323B76] text-white font-medium">
                    Manage
                </a>
            </div>
        </div>
    @empty
        <p class="text-gray-400 text-center w-full col-span-2 mt-10">
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
