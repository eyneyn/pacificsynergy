@extends('layouts.app')
@section('title', content: 'User')
@section('content')
<div class="container mx-auto px-4">
    <!-- Header with Icon and Title -->
    <div class="mb-4">
        <h1 class="text-xl font-bold text-[#23527c]">
           User Profile
        </h1>
    </div>

    <div class="bg-white border-t border-b border-gray-200 shadow-sm mb-4 mx-auto">
        <!-- Form Body -->
        <div class="lg:flex lg:gap-8 py-6 items-center">
            <!-- Left: Profile Image and Basic Info -->
            <div class="w-full lg:max-w-xs flex flex-col items-center text-center">
                <div class="relative w-52 h-52">
                    <img 
                        src="{{ asset('storage/' . $user->photo) }}" 
                        class="w-full h-full object-cover p-1 border border-gray-300 rounded" 
                        alt="Profile Photo"
                        onerror="this.onerror=null;this.src='{{ asset('img/default.jpg') }}';"
                    >
                </div>
            </div>

            <!-- Right: User Info -->
            <div class="flex-1 self-center py-6">
                <div class="space-y-3">
                    {{-- Name (Last, First [Middle]) --}}
                    <h3 class="text-2xl font-bold text-[#23527c]">
                        {{ $user->last_name }}, {{ $user->first_name }}
                        @if(!empty($user->middle_name))
                            {{ $user->middle_name }}
                        @endif
                    </h3>

                    {{-- Role--}}
                    <p class="text-gray-700">
                        <span class="font-medium">Role:</span>
                            <span class="uppercase">
                                {{ $user->roles->pluck('name')->join(', ') ?: 'NO ROLE' }}
                            </span>
                    </p>

                    {{-- Status--}}
                    <p class="text-gray-700">
                        <span class="font-medium">Status:</span>
                        <span class="uppercase {{ $user->status === 'Active' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $user->status ?? '—' }}
                        </span>
                    </p>

                    {{-- Info list --}}
                    <div class="mt-2 space-y-3">
                        {{-- ID Number --}}
                        <div class="flex items-start sm:items-center gap-3">
                            <span class="inline-flex items-center justify-center p-1.5 rounded text-[#2a7a48] bg-green-50">
                                {{-- ID card icon --}}
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path d="M3 5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v2H3V5zm0 4h20v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9zm5 3a2 2 0 1 0 0 4h2a2 2 0 1 0 0-4H8zm6 1h5v2h-5v-2zm-9 5h16v2H5v-2z"/>
                                </svg>
                            </span>
                            <span class="text-sm font-medium text-green-700 min-w-[160px]">ID Number</span>
                            <span class="text-sm text-gray-800">
                                {{ $user->employee_number ?? '—' }}
                            </span>
                        </div>

                        {{-- Department --}}
                        <div class="flex items-start sm:items-center gap-3">
                            <span class="inline-flex items-center justify-center p-1.5 rounded text-[#2a7a48] bg-green-50">
                                {{-- Building icon --}}
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path d="M4 20V6a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v14h2v2H2v-2h2zm8 0V6H6v14h6zm8-10h-3v12h3a1 1 0 0 0 1-1V11a1 1 0 0 0-1-1zM8 8h2v2H8V8zm0 4h2v2H8v-2zm0 4h2v2H8v-2z"/>
                                </svg>
                            </span>
                            <span class="text-sm font-medium text-green-700 min-w-[160px]">Department</span>
                            <span class="text-sm text-gray-800">
                                {{ $user->department ?? '—' }}
                            </span>
                        </div>

                        {{-- Contact Number --}}
                        <div class="flex items-start sm:items-center gap-3">
                            <span class="inline-flex items-center justify-center p-1.5 rounded text-[#2a7a48] bg-green-50">
                                <svg class="w-5 h-5" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                                <path d="M3.654 1.328a.678.678 0 0 1 1.015-.063l2.29 2.29c.329.33.445.81.29 1.243l-.805 2.252a.678.678 0 0 0 .144.68l2.457 2.457a.678.678 0 0 0 .68.144l2.252-.805a1.745 1.745 0 0 1 1.243.29l2.29 2.29a.678.678 0 0 1-.063 1.015l-1.387 1.387c-.668.668-1.736.745-2.522.225a19.86 19.86 0 0 1-8.63-8.63c-.52-.786-.443-1.854.225-2.522L3.654 1.328z"/>
                                </svg>
                            </span>
                            <span class="text-sm font-medium text-green-700 min-w-[160px]">
                            Phone Number
                            </span>
                            <span class="text-sm text-gray-800 break-all">
                                {{ $user->phone_number }}
                            </span>
                        </div>

                        {{-- Official Email Address --}}
                        <div class="flex items-start sm:items-center gap-3">
                            <span class="inline-flex items-center justify-center p-1.5 rounded text-[#2a7a48] bg-green-50">
                                {{-- Envelope icon --}}
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path d="M20 4H4a2 2 0 0 0-2 2v.4l10 6.25L22 6.4V6a2 2 0 0 0-2-2zm2 5.1-9.36 5.85a2 2 0 0 1-2.28 0L1 9.1V18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9.1z"/>
                                </svg>
                            </span>
                            <span class="text-sm font-medium text-green-700 min-w-[160px]">Official Email Address</span>
                            <span class="text-sm text-gray-800 break-all">
                                {{ $user->email }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


    @if (session('login_link_sent'))
        <div class="bg-[#5a9fd4] text-sm border border-[#4590ca] p-4 mt-4 text-white">
            <div class="font-bold">The login link was successfully sent to the user's email.</div>
            <div>{{ session('login_link_sent') }}</div>
        </div>
    @endif

    @if (session('two_fa_reset'))
        <div class="bg-[#5a9fd4] text-sm border border-[#4590ca] p-4 mt-4 text-white">
            <div class="font-bold">The 2FA reset link was successfully sent to the user's email.</div>
            <div>{{ session('two_fa_reset') }}</div>
        </div>
    @endif

    @if (session('user_updated'))
        <div class="bg-[#43ac6a] text-sm border border-[#2f9655] p-4 mt-4 text-white">
            <div class="font-bold">Good job! This user profile has been successfully recorded in the system.</div>
            <div>{{ session('user_updated') }}</div>
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="flex items-center gap-2 mt-6">
        <!-- Back Button -->
        <a href="{{ route('employees.index') }}"
           class="inline-flex items-center px-3 py-2 bg-[#5a9fd4] hover:bg-[#4a8bc2] text-white text-sm font-medium transition-colors duration-200 border border-[#5a9fd4] hover:border-[#4a8bc2]">
            <x-icons-back class="w-2 h-2 text-white" />
            Back
        </a>
        <form action="{{ route('employees.sendLoginLink', $user->id) }}" method="POST">
            @csrf
            <button type="submit" 
                class="px-3 py-2 text-sm font-medium text-white bg-[#5bb75b] border border-[#43a143] hover:bg-[#42a542]">
                Send Login Link
            </button>
        </form>
        <!-- Reset 2FA -->
        <x-reset2-f-a-modal :userId="$user->id" />
        <!-- Edit Button -->
        <a href="{{ route('employees.edit', $user->id) }}"
           class="inline-flex items-center gap-2 px-3 py-2 border border-[#323B76] bg-[#323B76] hover:bg-[#444d90] text-white text-sm font-medium transition-colors duration-200">
            <x-icons-edit class="w-4 h-4" />
            Edit
        </a>
    </div>


@endsection
