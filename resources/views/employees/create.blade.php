@extends('layouts.app')
@section('title', content: 'User')
@section('content')
<div class="container mx-auto px-4" 
     x-data="{ step: {{ ($errors->has('email') || $errors->has('password') || $errors->has('password_confirmation')) ? 2 : 1 }} }">

    <!-- Header -->
    <div class="mb-4">
        <h1 class="text-xl font-bold text-[#23527c]">New Employee</h1>
    </div>

    {{-- 🔔 Modal Alerts (Success, Error, Validation) --}}
    <x-alert-message />

    <div class="border-t border-b border-gray-200 mb-4 mx-auto grid grid-cols-1 lg:grid-cols-4 gap-6">
        {{-- Sidebar --}}
        <div class="col-span-1 p-8 text-center">
            {{-- Profile Image Upload --}}
            <form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="relative w-40 h-40 mx-auto mb-4 overflow-hidden border border-gray-300 rounded">

                <img id="photoPreview"
                    src="{{ asset('storage/profile/default.jpg') }}"
                    onerror="this.onerror=null;this.src='{{ asset('img/default.jpg') }}';"
                    alt="Profile Photo"
                    class="object-cover w-full h-full" />

                {{-- Pencil Icon Trigger --}}
                <label for="photo"
                    class="absolute bottom-2 right-2 bg-white p-1 rounded-full shadow hover:bg-gray-100 cursor-pointer">
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.25 2.25 0 113.182 3.182L6.75 20.25H3v-3.75L16.732 3.732z" />
                    </svg>
                </label>

                <input type="file" name="photo" id="photo" accept="image/*" class="hidden">
            </div>

            <label for="photo" class="text-sm text-blue-600 hover:underline cursor-pointer">
                Upload Profile Picture
            </label>

            @error('photo')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
            <p id="photoError" class="text-red-500 text-sm mt-1 hidden"></p>

            {{-- Sidebar Navigation --}}
            <nav class="mt-6 space-y-2 text-left">
                <button type="button" @click="step = 1"
                    class="w-full text-left px-4 py-2 text-sm rounded font-medium transition"
                    :class="step === 1 ? 'bg-[#23527c]/10 text-[#23527c]' : 'text-[#23527c] hover:bg-gray-100'">
                    User Information
                </button>
                <button type="button" @click="step = 2"
                    class="w-full text-left px-4 py-2 text-sm rounded font-medium transition"
                    :class="step === 2 ? 'bg-[#23527c]/10 text-[#23527c]' : 'text-[#23527c] hover:bg-gray-100'">
                    Account Information
                </button>
            </nav>
        </div>

        {{-- Right Panel --}}
        <div class="col-span-3 p-8">

                {{-- Step 1: User Information --}}
                <div x-show="step === 1" x-cloak x-data="{ errorMessage: '' }">
                    <h3 class="text-lg font-bold text-[#23527c] mb-6">User Information</h3>

                    <p x-text="errorMessage" class="text-red-500 text-sm mb-6"></p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- First Name --}}
                        <div>
                            <label class="block mb-1 text-sm font-medium text-[#23527c]">
                                First Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}"
                                class="w-full px-3 py-2 text-sm focus:outline-none focus:shadow-lg
                                {{ $errors->has('first_name') ? 'border-red-500 border' : 'border border-gray-300 focus:border-blue-500' }}" required />
                            @error('first_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Last Name --}}
                        <div>
                            <label class="block mb-1 text-sm font-medium text-[#23527c]">
                                Last Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}"
                                class="w-full px-3 py-2 text-sm focus:outline-none focus:shadow-lg
                                {{ $errors->has('last_name') ? 'border-red-500 border' : 'border border-gray-300 focus:border-blue-500' }}" required />
                            @error('last_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Phone Number --}}
                        <div>
                            <label class="block mb-1 text-sm font-medium text-[#23527c]">Phone Number </label>
                            <input type="text" name="phone_number" value="{{ old('phone_number') }}"
                                class="w-full px-3 py-2 text-sm focus:outline-none focus:shadow-lg
                                {{ $errors->has('phone_number') ? 'border-red-500 border' : 'border border-gray-300 focus:border-blue-500' }}"/>
                            @error('phone_number')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Employee Number --}}
                        <div>
                            <label class="block mb-1 text-sm font-medium text-[#23527c]">
                                Employee Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="employee_number" value="{{ old('employee_number') }}"
                                class="w-full px-3 py-2 text-sm focus:outline-none focus:shadow-lg
                                {{ $errors->has('employee_number') ? 'border-red-500 border' : 'border border-gray-300 focus:border-blue-500' }}" required />
                            @error('employee_number')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Role --}}
                        <div>
                            <label class="block mb-1 text-sm font-medium text-[#23527c]">
                                Position (Role) <span class="text-red-500">*</span>
                            </label>
                            <x-select-dropdown-employee name="role" :options="$roleOptions->toArray()" placeholder="Select Role" required />
                            @error('role')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Department --}}
                        <div>
                            <label class="block mb-1 text-sm font-medium text-[#23527c]">
                                Department <span class="text-red-500">*</span>
                            </label>
                            <x-select-dropdown-employee 
                                name="department" 
                                :options="['Production Department' => 'Production Department']" 
                                :selected="old('department', 'Production Department')" 
                                required />
                            @error('department')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Navigation --}}
                    <div class="flex flex-col gap-2 mt-6">
                        <div class="flex justify-between gap-4">
                            <a href="{{ route('employees.index') }}"
                            class="inline-flex items-center px-3 py-2 bg-[#5a9fd4] hover:bg-[#4a8bc2] border border-[#5a9fd4] hover:border-[#4a8bc2] text-white text-sm font-medium transition-colors duration-200">
                            <x-icons-back class="w-2 h-2 text-white" /> Back
                            </a>
                            <button type="button"
                                @click="
                                    let requiredFields = ['first_name', 'last_name', 'employee_number', 'role', 'department'];
                                    let allFilled = true;

                                    requiredFields.forEach(field => {
                                        let el = document.querySelector(`[name='${field}']`);
                                        if (!el || !el.value.trim()) {
                                            allFilled = false;
                                            el.classList.add('border-red-500');
                                        } else {
                                            el.classList.remove('border-red-500');
                                        }
                                    });

                                    if (allFilled) {
                                        errorMessage = '';
                                        step = 2;
                                    } else {
                                        errorMessage = 'Please fill out all required fields before proceeding.';
                                    }
                                "
                                class="px-3 py-2 bg-[#5bb75b] border border-[#43a143] text-white text-sm font-medium hover:bg-[#42a542]">
                                Next
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Step 2: Account Information --}}
                <div x-show="step === 2" x-cloak>
                    <h3 class="text-lg font-bold text-[#23527c] mb-6">Account Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                        {{-- Email --}}
                        <div>
                            <label class="block mb-1 text-sm font-medium text-[#23527c]">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="name@example.com"
                                class="w-1/2 px-3 py-2 text-sm focus:outline-none focus:shadow-lg placeholder-gray-400
                                {{ $errors->has('email') ? 'border-red-500 border' : 'border border-gray-300 focus:border-blue-500' }}" required />
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email requirements --}}
                        <div class="mt-4">
                            <h2 class="mb-2 text-sm font-semibold text-[#23527c]">Account Creation:</h2>
                            <h3 class="mb-2 text-sm text-[#23527c]">When creating an employee account, you must provide a valid company-issued email address.</h2>
                            <ul class="space-y-1 text-gray-500 list-disc list-inside text-sm">
                                <li>Personal or external emails (e.g., Gmail, Yahoo, etc.) are not allowed.</li>
                                <li>This ensures accounts are properly verified and linked to the company system.</li>
                            </ul>
                        </div>
                    </div>

                    {{-- Final Buttons --}}
                    <div class="flex justify-between gap-4 mt-6">
                        <button type="button" @click="step = 1"
                                class="inline-flex items-center px-3 py-2 bg-[#5a9fd4] hover:bg-[#4a8bc2] border border-[#5a9fd4] hover:border-[#4a8bc2] text-white text-sm font-medium transition-colors duration-200">
                            <x-icons-back class="w-2 h-2 text-white" /> Back
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

<script>
    // Handle profile picture preview + size validation
    const photoInput = document.getElementById('photo');
    const photoPreview = document.getElementById('photoPreview');
    const photoError = document.getElementById('photoError');

    photoInput.addEventListener('change', function (event) {
        const file = event.target.files[0];
        photoError.classList.add('hidden');

        if (!file) return;

        // Check file size (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
            photoError.textContent = "The photo must not be greater than 2MB.";
            photoError.classList.remove('hidden');
            event.target.value = "";
            return;
        }

        // Show preview
        const reader = new FileReader();
        reader.onload = function (e) {
            photoPreview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });
</script>
@endsection