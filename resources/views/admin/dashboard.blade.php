@extends('layouts.app')

@section('content')
<div class="space-y-6">

<!-- Summary Widgets -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Total Users -->
    <div class="p-6 bg-blue-100 border border-blue-200 rounded-md shadow-md">
        <div class="flex items-center gap-4">
            <div class="p-2 rounded-full bg-blue-200 text-blue-800">
                <!-- icon -->
            </div>
            <div>
                <h2 class="text-sm font-semibold text-[#2d326b] uppercase tracking-wide">Total Users</h2>
                <p class="text-3xl font-bold text-[#2d326b] mt-2">{{ $totalUsers }}</p>
            </div>
        </div>
    </div>

    <!-- Total Roles -->
    <div class="p-6 bg-green-100 border border-green-200 rounded-md shadow-md">
        <div class="flex items-center gap-4">
            <div class="p-2 rounded-full bg-green-200 text-green-800">
                <!-- icon -->
            </div>
            <div>
                <h2 class="text-sm font-semibold text-[#2d326b] uppercase tracking-wide">Total Roles</h2>
                <p class="text-3xl font-bold text-[#2d326b] mt-2">{{ $totalRoles }}</p>
            </div>
        </div>
    </div>

    <!-- Admins -->
    <div class="p-6 bg-purple-100 border border-purple-200 rounded-md shadow-md">
        <div class="flex items-center gap-4">
            <div class="p-2 rounded-full bg-purple-200 text-purple-800">
                <!-- icon -->
            </div>
            <div>
                <h2 class="text-sm font-semibold text-[#2d326b] uppercase tracking-wide">Admins</h2>
                <p class="text-3xl font-bold text-[#2d326b] mt-2">{{ $adminCount }}</p>
            </div>
        </div>
    </div>
</div>



<div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">

    <!-- Recent Users Table (2/3 width) -->
    <div class="lg:col-span-3 bg-white border border-gray-200 rounded-md shadow-md p-6">
        <h2 class="text-lg font-semibold text-[#2d326b] mb-4">Recent Users</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-gray-700 border border-gray-200">
                <thead class="bg-gray-100 text-[#2d326b] font-medium">
                    <tr>
                        <th class="px-4 py-2 text-left">Name</th>
                        <th class="px-4 py-2 text-left">Email</th>
                        <th class="px-4 py-2 text-left">Role</th>
                        <th class="px-4 py-2 text-left">Joined</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentUsers as $user)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $user->first_name }} {{ $user->last_name }}</td>
                            <td class="px-4 py-2">{{ $user->email }}</td>
                            <td class="px-4 py-2">
                                {{ $user->roles->pluck('name')->join(', ') }}
                            </td>
                            <td class="px-4 py-2">{{ $user->created_at->format('F d, Y') }}</td>
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
    <div class="bg-white border border-gray-200 rounded-md shadow-md p-6">
        <h2 class="text-lg font-semibold text-[#2d326b] mb-4">Quick Actions</h2>
        <div class="flex flex-col gap-3 text-center">
            <a href="{{ route('employees.create') }}"
               class="px-4 py-2 bg-[#2d326b] text-white text-sm font-medium rounded-md hover:bg-[#444d90] transition">
               Add New User
            </a>
            <a href="{{ route('roles.index') }}"
               class="px-4 py-2 bg-[#2d326b] text-white text-sm font-medium rounded-md hover:bg-[#444d90] transition">
               Manage Roles
            </a>
        </div>
    </div>

</div>



@endsection
