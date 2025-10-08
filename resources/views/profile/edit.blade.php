@extends('layouts.app')
@section('title', content: 'Profile')
@section('content')
<div class="container mx-auto px-4">

    <!-- Header -->
    <div class="mb-4">
        <h1 class="text-xl font-bold text-[#23527c]">My Profile</h1>
    </div>

    {{-- 🔔 Alerts --}}
    <x-alert-message />

    <div class="border-t border-b border-gray-200 mb-4 mx-auto"> 
        <div 
            x-data="{ tab: '{{ session('tab', 'info') }}' }"
            class="grid grid-cols-1 lg:grid-cols-4 gap-6"
        >

            {{-- Sidebar --}}
            <div class="col-span-1 p-8 text-center">

                <!-- Profile Photo -->
                <div class="relative w-40 h-40 mx-auto mb-4">
                    <img id="photoPreview"
                        src="{{ $user->photo ? asset('storage/' . $user->photo) : asset('img/default.jpg') }}"
                        onerror="this.onerror=null;this.src='{{ asset('img/default.jpg') }}';"
                        alt="User Photo"
                        class="w-full h-full object-cover border border-gray-300 p-1 rounded"
                    />
                </div>

                {{-- Sidebar Navigation --}}
                <nav class="mt-6 space-y-2 text-left">
                    <button @click="tab = 'info'" type="button"
                        class="w-full text-left px-4 py-2 text-sm rounded font-medium transition"
                        :class="tab === 'info' ? 'bg-[#23527c]/10 text-[#23527c]' : 'text-[#23527c] hover:bg-gray-100'">
                        Personal Information
                    </button>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-4 py-2 text-sm text-[#23527c] hover:bg-gray-100 rounded font-medium transition">
                            Log Out
                        </button>
                    </form>
                </nav>
            </div>

            {{-- Right Panel --}}
            <div class="col-span-3 p-8">
                
                <h3 class="text-lg font-bold text-[#23527c] mb-6">User Information</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- First Name --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium text-[#23527c]">First Name</label>
                        <p class="w-full border border-gray-300 px-3 py-2 text-sm shadow-sm bg-gray-100 text-gray-700">
                            {{ $user->first_name }}
                        </p>
                    </div>

                    {{-- Last Name --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium text-[#23527c]">Last Name</label>
                        <p class="w-full border border-gray-300 px-3 py-2 text-sm shadow-sm bg-gray-100 text-gray-700">
                            {{ $user->last_name }}
                        </p>
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium text-[#23527c]">Phone Number</label>
                        <p class="w-full border border-gray-300 px-3 py-2 text-sm shadow-sm bg-gray-100 text-gray-700">
                            {{ $user->phone_number }}
                        </p>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium text-[#23527c]">Email</label>
                        <p class="w-full border border-gray-300 px-3 py-2 text-sm shadow-sm bg-gray-100 text-gray-700">
                            {{ $user->email }}
                        </p>
                    </div>

                    {{-- Position --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium text-[#23527c]">Position</label>
                        <p class="w-full border border-gray-300 px-3 py-2 text-sm shadow-sm bg-gray-100 text-gray-700">
                            {{ $user->getRoleNames()->first() ?? 'N/A' }}
                        </p>
                    </div>

                    {{-- Department --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium text-[#23527c]">Department</label>
                        <p class="w-full border border-gray-300 px-3 py-2 text-sm shadow-sm bg-gray-100 text-gray-700">
                            {{ $user->department }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
