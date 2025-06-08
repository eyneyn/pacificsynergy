@extends('layouts.app')

@section('content')
<!-- Back Button -->
<a href="{{ route('employees.index') }}" class="flex items-center text-sm text-gray-500 hover:text-[#2d326b] mb-6">
    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
    </svg>
    Back to Employees
</a>

<form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="bg-white border border-gray-200 rounded-sm shadow-lg p-8 space-y-10 hover:shadow-xl transition duration-300">
        <!-- Header -->
        <div class="-mx-8 px-8 pb-4 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-semibold text-[#2d326b]">Create New User</h2>
                <p class="text-sm text-gray-500 mt-1">Fill out the form below to register a new user.</p>
            </div>
            <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-md text-white text-sm font-medium bg-[#323B76] hover:bg-[#444d90] transition disabled:opacity-50 disabled:cursor-not-allowed">
                Register User
            </button>
        </div>

        <!-- Form Body -->
        <div class="lg:flex lg:gap-8 pb-4">
            <!-- Left: Profile Image -->
            <div class="w-full lg:max-w-xs flex flex-col items-center text-center">
                <div class="relative w-40 h-40 mb-4">
                    <img id="photoPreview"
                        src="{{ asset('storage/app/public/default.jpg') }}"
                        onerror="this.onerror=null;this.src='{{ asset('img/default.jpg') }}';"
                        class="w-full h-full p-1 object-cover rounded-full border border-gray-300"
                        alt="Profile Photo">
                    <label for="photo"
                        class="absolute bottom-2 right-2 bg-white p-1 rounded-full shadow hover:bg-gray-100 cursor-pointer">
                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.25 2.25 0 113.182 3.182L6.75 20.25H3v-3.75L16.732 3.732z" />
                        </svg>
                    </label>
                    <input type="file" name="photo" id="photo" accept="image/*" class="hidden">
                </div>
                <label for="photo" class="text-sm text-blue-600 hover:underline cursor-pointer">Choose Profile Picture</label>
                @error('photo')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Right: User Info -->
            <div class="flex-1 space-y-10 pt-1">
                <!-- Basic Info -->
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-[#2d326b]">User Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-[#2d326b]">Last Name</label>
                            <input type="text" name="last_name" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm shadow-sm" />
                            @error('last_name')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-[#2d326b]">First Name</label>
                            <input type="text" name="first_name" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm shadow-sm" />
                            @error('first_name')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-[#2d326b]">Phone Number</label>
                            <input type="text" name="phone_number" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm shadow-sm" />
                            @error('phone_number')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-[#2d326b]">Employee Number</label>
                            <input type="text" name="employee_number" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm shadow-sm" />
                            @error('employee_number')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="role" class="block mb-2 text-sm font-medium text-[#2d326b]">Position (Role Access)</label>
                            <x-select-dropdown name="role" :options="$roleOptions->toArray()" placeholder="Select Role" required />
                            @error('role')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-[#2d326b]">Department</label>
                            <x-select-dropdown name="department" :options="['Production Department' => 'Production Department']" placeholder="Select Department" required />
                            @error('department')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Account Security -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-[#2d326b] flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#2d326b]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.5 10.5V6a4.5 4.5 0 00-9 0v4.5M4.5 10.5A1.5 1.5 0 006 12v6a1.5 1.5 0 001.5 1.5h9A1.5 1.5 0 0018 18v-6a1.5 1.5 0 00-1.5-1.5h-12z" />
                        </svg>
                        Account Security
                    </h3>
                    <p class="text-xs text-gray-500">Set up login credentials</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-[#2d326b]">Email</label>
                            <input type="email" name="email" placeholder="name@example.com"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm shadow-sm placeholder-gray-400" required />
                            @error('email')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-[#2d326b]">Password</label>
                            <div class="flex gap-2">
                                <input type="text" name="password" id="password"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm shadow-sm placeholder-gray-400"
                                    placeholder="Enter or generate a password" required />
                                <button type="button" onclick="generatePassword()"
                                    class="px-3 py-2 text-sm font-medium text-white bg-[#323B76] rounded-md hover:bg-[#444d90] transition">
                                    Generate
                                </button>
                            </div>
                            @error('password')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- JS: Password Generator & Image Preview -->
<script>
    // Generate a random password and set it to the password input
    function generatePassword() {
        const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+";
        let password = "";
        for (let i = 0; i < 12; i++) {
            password += charset.charAt(Math.floor(Math.random() * charset.length));
        }
        document.getElementById("password").value = password;
    }

    // Preview selected profile image
    document.getElementById('photo').addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (file) {
            document.getElementById('photoPreview').src = URL.createObjectURL(file);
        }
    });
</script>
@endsection
