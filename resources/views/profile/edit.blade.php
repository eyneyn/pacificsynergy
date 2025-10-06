@extends('layouts.app')
@section('title', content: 'Profile')
@section('content')
<div class="container mx-auto px-4">

    <!-- Header -->
    <div class="mb-4">
        <h1 class="text-xl font-bold text-[#23527c]">My Profile</h1>
    </div>

    <div class="border-t border-b border-gray-200 mb-4 mx-auto"> 
        <div 
            x-data="{ tab: '{{ session('tab', 'info') }}' }"
            class="grid grid-cols-1 lg:grid-cols-4 gap-6"
        >

            {{-- Sidebar --}}
            <div class="col-span-1 p-8 text-center">
                {{-- Profile Image --}}
                <form method="POST" action="{{ route('profile.update.photo') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="relative w-40 h-40 mx-auto mb-4">
                        <img id="photoPreview"
                            src="{{ $user->photo ? asset('storage/' . $user->photo) : asset('img/default.jpg') }}"
                            onerror="this.onerror=null;this.src='{{ asset('img/default.jpg') }}';"
                            alt="User Photo"
                            class="w-full h-full object-cover border border-gray-300 p-1"
                        />
                        <label for="photo" class="absolute bottom-2 right-2 p-1 rounded-full shadow hover:bg-gray-100 cursor-pointer">
                            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.25 2.25 0 113.182 3.182L6.75 20.25H3v-3.75L16.732 3.732z" />
                            </svg>
                        </label>
                        <input type="file" name="photo" id="photo" accept="image/*" class="hidden">
                    </div>

                    <button type="submit" class="mt-1 px-4 py-1 text-sm font-medium bg-[#323B76] text-white hover:bg-[#444d90] transition">
                        Save Photo
                    </button>

                    @if (session('status') === 'photo-updated')
                        <p class="text-green-600 text-sm mt-2">Photo updated successfully.</p>
                    @endif
                    @error('photo') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </form>

                {{-- Sidebar Navigation --}}
                <nav class="mt-6 space-y-2 text-left">
                    <button @click="tab = 'info'" type="button"
                        class="w-full text-left px-4 py-2 text-sm rounded font-medium transition"
                        :class="tab === 'info' ? 'bg-[#23527c]/10 text-[#23527c]' : 'text-[#23527c] hover:bg-gray-100'">
                        Personal Information
                    </button>
                    <button @click="tab = 'password'" type="button"
                        class="w-full text-left px-4 py-2 text-sm rounded font-medium transition"
                        :class="tab === 'password' ? 'bg-[#23527c]/10 text-[#23527c]' : 'text-[#23527c] hover:bg-gray-100'">
                        Change Password
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
                
                {{-- Personal Info --}}
                <form method="POST" action="{{ route('profile.update') }}" x-show="tab === 'info'" x-cloak>
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="tab" value="info">

                    <h3 class="text-lg font-bold text-[#23527c] mb-6">User Information</h3>
                    @if(session('status') === 'profile-updated')
                        <div class="text-green-600 text-sm mb-4">Profile updated successfully.</div>
                    @endif
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- First Name --}}
                        <div>
                            <label class="block mb-1 text-sm font-medium text-[#23527c]">First Name <span class="text-red-500">*</span></label>
                            <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}"
                                class="w-full border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:shadow-lg focus:outline-none" required/>
                            @error('first_name') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                        </div>
                        {{-- Last Name --}}
                        <div>
                            <label class="block mb-1 text-sm font-medium text-[#23527c]">Last Name <span class="text-red-500">*</span></label>
                            <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}"
                                class="w-full border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:shadow-lg focus:outline-none" required/>
                            @error('last_name') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                        </div>
                        {{-- Phone --}}
                        <div>
                            <label class="block mb-1 text-sm font-medium text-[#23527c]">Phone Number <span class="text-red-500">*</span></label>
<input type="text" name="phone_number" id="phone_number"
       maxlength="11" pattern="^09\d{9}$"
       value="{{ old('phone_number', $user->phone_number) }}"
       class="w-full border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:shadow-lg focus:outline-none" 
       required />                            @error('phone_number') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                        </div>
                        {{-- Email --}}
                        <div>
                            <label class="block mb-1 text-sm font-medium text-[#23527c]">Email</label>
                            <p class="w-full border border-gray-300 px-3 py-2 text-sm shadow-sm bg-gray-100 text-gray-500">
                                {{ $user->email }}
                            </p>
                            <input type="hidden" name="email" value="{{ $user->email }}">
                        </div>
                        {{-- Position --}}
                        <div>
                            <label class="block mb-1 text-sm font-medium text-[#23527c]">Position</label>
                            <p class="w-full border border-gray-300 px-3 py-2 text-sm shadow-sm bg-gray-100 text-gray-500">
                                {{ $user->getRoleNames()->first() ?? 'Your Position' }}
                            </p>
                        </div>
                        {{-- Department --}}
                        <div>
                            <label class="block mb-1 text-sm font-medium text-[#23527c]">Department</label>
                            <p class="w-full border border-gray-300 px-3 py-2 text-sm shadow-sm bg-gray-100 text-gray-500">
                                {{ $user->department }}
                            </p>
                        </div>
                    </div>

                    <div class="flex justify-end gap-4 mt-8">
                        <button type="reset"
                            class="px-3 py-2 text-sm font-medium border border-[#23527c] text-[#23527c] hover:bg-[#23527c]/10">
                            Discard Changes
                        </button>
                        <button type="submit"
                            class="px-3 py-2 text-sm font-medium text-white bg-[#5bb75b] border border-[#43a143] hover:bg-[#42a542]">
                            Update Changes
                        </button>
                    </div>
                </form>

                {{-- Password Update --}}
                <form method="POST" action="{{ route('profile.password.update') }}" x-show="tab === 'password'" x-cloak>
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="tab" value="password">
                    <h3 class="text-lg font-bold text-[#23527c] mb-6">Change Password</h3>
                        
                        @if(session('success'))
                            <div class="text-green-600 text-sm mb-4">{{ session('success') }}</div>
                        @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Current Password --}}
                        <div>
                            <label class="block mb-1 text-sm font-medium text-[#23527c]">Current Password</label>
                            <input type="password" name="current_password"
                                class="w-full border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:shadow-lg focus:outline-none" required/>
                            @error('current_password') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div></div>
                        {{-- New Password --}}
                        <div>
                            <label class="block mb-1 text-sm font-medium text-[#23527c]">New Password</label>
                            <input type="password" name="new_password" id="new_password"
                                class="w-full border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:shadow-lg focus:outline-none" required/>
                            <div id="password-strength" class="text-xs mt-1"></div>
                            @error('new_password') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                        </div>
                        {{-- Confirm Password --}}
                        <div>
                            <label class="block mb-1 text-sm font-medium text-[#23527c]">Confirm New Password</label>
                            <input type="password" name="new_password_confirmation"
                                class="w-full border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:shadow-lg focus:outline-none" required/>
                            @error('new_password_confirmation') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
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
                            <li>Or simply click the "Generate" to generate a password in the password input field.</li>
                        </ul>
                    </div>

                    <div class="flex justify-end gap-4 mt-8">
                        <button type="reset"
                            class="px-3 py-2 text-sm font-medium border border-[#23527c] text-[#23527c] hover:bg-[#23527c]/10">
                            Discard Changes
                        </button>
                        <button type="submit"
                            class="px-3 py-2 text-sm font-medium text-white bg-[#5bb75b] border border-[#43a143] hover:bg-[#42a542]">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    // Preview Image
    document.getElementById('photo')?.addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (file) {
            document.getElementById('photoPreview').src = URL.createObjectURL(file);
        }
    });

    // Phone Number Validation
    const phoneInput = document.getElementById('phone_number');
    if (phoneInput) {
        phoneInput.oninvalid = function(e) {
            e.target.setCustomValidity("The phone number must start with 09 and contain exactly 11 digits.");
        };
        phoneInput.oninput = function(e) {
            e.target.setCustomValidity("");
        };
    }

    // Current Password Validation
    const currentPass = document.querySelector('input[name="current_password"]');
    if (currentPass) {
        currentPass.oninvalid = function(e) {
            e.target.setCustomValidity("Please enter your current password.");
        };
        currentPass.oninput = function(e) {
            e.target.setCustomValidity("");
        };
    }

    // New Password Validation
    const newPass = document.querySelector('input[name="new_password"]');
    if (newPass) {
        newPass.setAttribute("pattern", "(?=.*[a-z])(?=.*[0-9])(?=.*[.,@$!%*?&]).{6,20}");
        newPass.oninvalid = function(e) {
            e.target.setCustomValidity("Password must be 6–20 characters and contain at least one lowercase letter, one number, and one special character.");
        };
        newPass.oninput = function(e) {
            e.target.setCustomValidity("");
        };
    }

    // Confirm Password Validation (real-time match check)
    const confirmPass = document.querySelector('input[name="new_password_confirmation"]');
    if (confirmPass && newPass) {
        confirmPass.oninput = function(e) {
            if (confirmPass.value !== newPass.value) {
                confirmPass.setCustomValidity("Passwords do not match.");
            } else {
                confirmPass.setCustomValidity("");
            }
        };
        confirmPass.oninvalid = function(e) {
            if (!confirmPass.value) {
                confirmPass.setCustomValidity("Please confirm your new password.");
            } else {
                confirmPass.setCustomValidity("Passwords do not match.");
            }
        };
    }
    // Password Strength Meter
    const newPasswordInput = document.getElementById("new_password");
    const strengthDisplay = document.getElementById("password-strength");

    if (newPasswordInput) {
        newPasswordInput.addEventListener("input", function () {
            const value = newPasswordInput.value;
            let strength = 0;

            if (value.length >= 6) strength++; // length check
            if (/[a-z]/.test(value)) strength++; // lowercase
            if (/[0-9]/.test(value)) strength++; // number
            if (/[.,@$!%*?&]/.test(value)) strength++; // special char
            if (value.length >= 12) strength++; // bonus for longer

            let message = "";
            let color = "";

            switch (strength) {
                case 0:
                case 1:
                    message = "Weak";
                    color = "text-red-600";
                    break;
                case 2:
                case 3:
                    message = "Medium";
                    color = "text-yellow-600";
                    break;
                case 4:
                case 5:
                    message = "Strong";
                    color = "text-green-600";
                    break;
            }

            strengthDisplay.textContent = `Strength: ${message}`;
            strengthDisplay.className = `text-xs mt-1 font-semibold ${color}`;
        });
    }
</script>

@endsection
