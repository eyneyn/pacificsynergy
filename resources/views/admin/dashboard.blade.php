@extends('layouts.app')
@section('title', content: 'Dashboard')
@section('content')
<div class="space-y-6">

<!-- Summary Widgets -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Total Users -->
    <div class="bg-white border border-gray-200 hover:border-[#c9e8fe] hover:bg-[#e2f2ff] shadow-[-1px_6px_5px_rgba(0,0,0.1,0.1)] hover:shadow-lg transition-all duration-200 p-6 cursor-pointer">
                <h2 class="text-sm font-semibold text-[#23527c] uppercase tracking-wide text-center">Total Employees</h2>
                <p class="text-3xl font-bold text-[#23527c] mt-2 text-center">{{ $totalUsers }}</p>
    </div>

    <!-- Total Roles -->
    <div class="bg-white border border-gray-200 hover:border-[#c9e8fe] hover:bg-[#e2f2ff] shadow-[-1px_6px_5px_rgba(0,0,0.1,0.1)] hover:shadow-lg transition-all duration-200 p-6 cursor-pointer">
                <h2 class="text-sm font-semibold text-[#23527c] uppercase tracking-wide text-center">Total Roles</h2>
                <p class="text-3xl font-bold text-[#23527c] mt-2 text-center">{{ $totalRoles }}</p>
    </div>

<!-- Employees without a role -->
<div class="bg-white border border-gray-200 hover:border-[#c9e8fe] hover:bg-[#e2f2ff] shadow-[-1px_6px_5px_rgba(0,0,0.1,0.1)] hover:shadow-lg transition-all duration-200 p-6 cursor-pointer">
        <h2 class="text-sm font-semibold text-[#23527c] uppercase tracking-wide text-center">
            Employees without a role
        </h2>
        <p class="text-3xl font-bold text-[#23527c] mt-2 text-center">
            {{ number_format($usersWithoutRole) }}
        </p>
</div>

<!-- Roles without users -->
<div class="bg-white border border-gray-200 hover:border-[#c9e8fe] hover:bg-[#e2f2ff] shadow-[-1px_6px_5px_rgba(0,0,0.1,0.1)] hover:shadow-lg transition-all duration-200 p-6 cursor-pointer">
        <h2 class="text-sm font-semibold text-[#23527c] uppercase tracking-wide text-center">
            Roles without users
        </h2>
        <p class="text-3xl font-bold text-[#23527c] mt-2 text-center">
            {{ number_format($rolesWithoutUsers) }}
        </p>
</div>

</div>



<div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">

    <!-- Recent Users Table (2/3 width) -->
    <div class="lg:col-span-3 bg-white border border-gray-200  shadow-md p-6 shadow-[-1px_6px_5px_rgba(0,0,0.1,0.1)] hover:shadow-lg transition-all duration-200">
        <h2 class="text-lg font-semibold text-[#23527c] mb-4">Recent Users</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-gray-700 border border-gray-200">
                <thead class="bg-[#35408e] text-white font-medium">
                    <tr>
                        <th class="px-4 py-2 text-left border border-[#d9d9d9]">Name</th>
                        <th class="px-4 py-2 text-left border border-[#d9d9d9]">Email</th>
                        <th class="px-4 py-2 text-left border border-[#d9d9d9]">Role</th>
                        <th class="px-4 py-2 text-left border border-[#d9d9d9]">Joined</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentUsers as $user)
                        <tr class="border-t">
                            <td class="px-4 py-2 border border-[#d9d9d9]">{{ $user->first_name }} {{ $user->last_name }}</td>
                            <td class="px-4 py-2 border border-[#d9d9d9]">{{ $user->email }}</td>
                            <td class="px-4 py-2 border border-[#d9d9d9]">
                                {{ $user->roles->pluck('name')->join(', ') }}
                            </td>
                            <td class="px-4 py-2 border border-[#d9d9d9]">{{ $user->created_at->format('F d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-2 text-center text-gray-500">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Actions Panel (1/3 width) -->
    <div class="bg-white border border-gray-200  shadow-md p-6 shadow-[-1px_6px_5px_rgba(0,0,0.1,0.1)] hover:shadow-lg transition-all duration-200">
        <h2 class="text-lg font-semibold text-[#23527c] mb-4">Quick Actions</h2>
        <div class="flex flex-col gap-3 text-center">
            <a href="{{ route('employees.create') }}"
               class="px-4 py-2 bg-[#323B76] hover:bg-[#444d90] border border-[#323B76] text-white text-sm font-medium ">
               Add New User
            </a>
            <a href="{{ route('roles.index') }}"
               class="px-4 py-2 bg-[#323B76] hover:bg-[#444d90] border border-[#323B76] text-white text-sm font-medium">
               Manage Roles
            </a>
            <a href="{{ route('audit-logs.index') }}"
            class="px-4 py-2 bg-[#323B76] hover:bg-[#444d90] border border-[#323B76] text-white text-sm font-medium">
            Activity Logs
            </a>
            <a href="{{ route('setting.index') }}"
               class="px-4 py-2 bg-[#323B76] hover:bg-[#444d90] border border-[#323B76] text-white text-sm font-medium">
               Setting
            </a>
        </div>
    </div>

</div>



@endsection
