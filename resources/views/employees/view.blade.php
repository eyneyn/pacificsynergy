@extends('layouts.app')

@section('content')
<!-- Back Button -->
<a href="{{ route('employees.index') }}" class="flex items-center text-sm text-gray-500 hover:text-[#2d326b] mb-4">
    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
    </svg>
    Employees
</a>

<div class="bg-white border border-gray-200 rounded-sm shadow-lg p-8 space-y-10 hover:shadow-xl transition duration-300">
    <!-- Header -->
    <div class="-mx-8 px-8 pb-4 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-semibold text-[#2d326b]">User Profile</h2>
            <p class="text-sm text-gray-500 mt-1">Details of the selected employee.</p>
        </div>
        <!-- Edit Button -->
        <a href="{{ route('employees.edit', $user->id) }}"
           class="inline-flex items-center gap-2 p-3 py-2 bg-[#323B76] hover:bg-[#444d90] border border-[#323B76] text-white text-sm font-medium rounded-md">
            <x-icons-edit class="w-4 h-4" />
            <span class="text-sm">Edit</span>
        </a>
    </div>

    <!-- Form Body -->
    <div class="lg:flex lg:gap-8 pb-4 items-center">
        <!-- Left: Profile Image and Basic Info -->
        <div class="w-full lg:max-w-xs flex flex-col items-center text-center">
            <div class="relative w-40 h-40 mb-5">
                <img 
                    src="{{ asset('storage/' . $user->photo) }}"
                    class="w-full h-full object-cover rounded-full p-1 border border-gray-300"
                    alt="Profile Photo"
                >
            </div>
            <h2 class="text-lg font-semibold text-[#2d326b]">{{ $user->first_name }} {{ $user->last_name }}</h2>
            <p class="text-md mt-1 text-gray-600">{{ $user->getRoleNames()->first() ?? 'No Role' }}</p>
            <p class="text-sm text-gray-500">{{ $user->email }}</p>
        </div>

        <!-- Right: User Info -->
        <div class="flex-1 space-y-10 pt-1 self-center">
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-[#2d326b]">User Information</h3>
                <!-- User Details Grid 1 -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm text-[#2d326b] font-medium">Last Name</label>
                        <p class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm text-gray-700">{{ $user->last_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-[#2d326b] font-medium">First Name</label>
                        <p class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm text-gray-700">{{ $user->first_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-[#2d326b] font-medium">Phone Number</label>
                        <p class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm text-gray-700">{{ $user->phone_number ?? '+63' }}</p>
                    </div>
                </div>
                <!-- User Details Grid 2 -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm text-[#2d326b] font-medium">Employee Number</label>
                        <p class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm text-gray-700">{{ $user->employee_number }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-[#2d326b] font-medium">Role</label>
                        <p class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm text-gray-700">
                            {{ $user->getRoleNames()->first() ?? 'No Role' }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm text-[#2d326b] font-medium">Department</label>
                        <p class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm text-gray-700">{{ $user->department ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
