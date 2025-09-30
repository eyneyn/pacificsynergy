@extends('layouts.app')
@section('title', content: 'User')
@section('content')
<div class="container mx-auto px-4" x-data="{ step: 1 }">
    <!-- Header with Icon and Title -->
    <div class="mb-4">
        <h1 class="text-xl font-bold text-[#23527c]">
           New Employee
        </h1>
    </div>

    <div class="border-t border-b border-gray-200 mb-4 mx-auto grid grid-cols-1 lg:grid-cols-4 gap-6">
        {{-- Sidebar --}}
        <div class="col-span-1 p-8 text-center">
            {{-- Profile Image Upload --}}
            <div class="relative w-40 h-40 mx-auto mb-4">
                <img id="photoPreview"
                    src="{{ asset('profile/default.jpg') }}"
                    onerror="this.onerror=null;this.src='{{ asset('img/default.jpg') }}';"
                    alt="Profile Photo"
                    class="w-full h-full object-cover border border-gray-300 p-1" />
                <label for="photo"
                    class="absolute bottom-2 right-2 bg-white p-1 rounded-full shadow hover:bg-gray-100 cursor-pointer">
                    {{-- Edit Icon --}}
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.25 2.25 0 113.182 3.182L6.75 20.25H3v-3.75L16.732 3.732z" />
                    </svg>
                </label>
                <input type="file" name="photo" id="photo" accept="image/*" class="hidden">
            </div>
            <p class="text-sm text-blue-600 hover:underline cursor-pointer">Upload Profile Picture</p>
            @error('photo')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror

            {{-- Sidebar Navigation --}}
            <nav class="mt-6 space-y-2 text-left">
                <button type="button"
                    @click="step = 1"
                    class="w-full text-left px-4 py-2 text-sm rounded font-medium transition"
                    :class="step === 1 ? 'bg-[#23527c]/10 text-[#23527c]' : 'text-[#23527c] hover:bg-gray-100'">
                    User Information
                </button>
                <button type="button"
                    @click="step = 2"
                    class="w-full text-left px-4 py-2 text-sm rounded font-medium transition"
                    :class="step === 2 ? 'bg-[#23527c]/10 text-[#23527c]' : 'text-[#23527c] hover:bg-gray-100'">
                    Account Information
                </button>
            </nav>
        </div>

        {{-- Right Panel --}}
        <div class="col-span-3 p-8">
            <form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Step 1: User Information --}}
                <div x-show="step === 1" x-cloak>
                    <h3 class="text-lg font-bold text-[#23527c] mb-6">User Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-1 text-sm font-medium text-[#23527c]">First Name <span style="color: red;">*</span></label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}"
                                class="w-full border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:shadow-lg focus:outline-none" required/>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-[#23527c]">Last Name <span style="color: red;">*</span></label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}"
                                class="w-full border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:shadow-lg focus:outline-none" required/>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-[#23527c]">Phone Number</label>
                            <input type="text" name="phone_number" value="{{ old('phone_number') }}"
                                class="w-full border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:shadow-lg focus:outline-none"/>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-[#23527c]">Employee Number <span style="color: red;">*</span></label>
                            <input type="text" name="employee_number" value="{{ old('employee_number') }}"
                                class="w-full border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:shadow-lg focus:outline-none" required/>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-[#23527c]">Position (Role) <span style="color: red;">*</span></label>
                            <x-select-dropdown-employee name="role" :options="$roleOptions->toArray()" placeholder="Select Role" required />
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-[#23527c]">Department <span class="text-red-500">*</span></label>
                            <x-select-dropdown-employee 
                                name="department" 
                                :options="['Production Department' => 'Production Department']" 
                                :selected="old('department', 'Production Department')" 
                                required 
                            />
                        </div>
                    </div>

                    {{-- Navigation --}}
                    <div class="flex justify-between gap-4 mt-6">
                        <a href="{{ route('employees.index') }}" class="inline-flex items-center px-3 py-2 bg-[#5a9fd4] hover:bg-[#4a8bc2] border border-[#5a9fd4] hover:border-[#4a8bc2] text-white text-sm font-medium transition-colors duration-200">
                             <x-icons-back class="w-2 h-2 text-white" /> 
                             Back 
                        </a>
                        <button type="button"
                            @click="step = 2"
                            class="px-3 py-2 bg-[#5bb75b] border border-[#43a143] text-white text-sm font-medium hover:bg-[#42a542]">
                            Next
                        </button>
                    </div>
                </div>

                {{-- Step 2: Account Information --}}
                <div x-show="step === 2" x-cloak>
                    <h3 class="text-lg font-bold text-[#23527c] mb-6">Account Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-1 text-sm font-medium text-[#23527c]">Email <span style="color: red;">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="name@example.com"
                                class="w-full border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400" required />
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-[#23527c]">Password <span style="color: red;">*</span></label>
                            <div class="flex gap-2">
                                <input type="text" name="password" id="password"
                                    placeholder="Enter or generate password"
                                    class="w-full border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400" required />
                                <button type="button" onclick="generatePassword()"
                                    class="px-3 py-2 text-sm font-medium text-white bg-[#323B76] hover:bg-[#444d90] transition">
                                    Generate
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Password requirements --}}
                    <div class="mt-4">
                        <h2 class="mb-2 text-sm font-semibold text-[#23527c]">Password requirements:</h2>
                        <ul class="space-y-1 text-gray-500 list-disc list-inside text-sm">
                            <li>Must be between 6 and 20 characters long</li>
                            <li>Must include at least one lowercase letter</li>
                            <li>Must include at least one number</li>
                            <li>Must include at least one special character (e.g., ., !, @, #, ?)</li>
                            <li>Or can generate password to auto-fill the field</li>
                        </ul>
                    </div>

                    {{-- Final Buttons --}}
                    <div class="flex justify-between gap-4 mt-6">
                        <button type="button"
                            @click="step = 1"
                            class="inline-flex items-center px-3 py-2 bg-[#5a9fd4] hover:bg-[#4a8bc2] border border-[#5a9fd4] hover:border-[#4a8bc2] text-white text-sm font-medium transition-colors duration-200">
                            <x-icons-back class="w-2 h-2 text-white" /> 
                            Back
                        </button>
                        <button type="submit"
                            class="px-3 py-2 bg-[#5bb75b] border border-[#43a143] text-white text-sm font-medium hover:bg-[#42a542]">
                            Save
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- JS: Password Generator & Image Preview --}}
<script>
    function generatePassword() {
        const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+";
        let password = "";
        for (let i = 0; i < 12; i++) {
            password += charset.charAt(Math.floor(Math.random() * charset.length));
        }
        document.getElementById("password").value = password;
    }

    document.getElementById('photo').addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (file) {
            document.getElementById('photoPreview').src = URL.createObjectURL(file);
        }
    });
</script>
@endsection
