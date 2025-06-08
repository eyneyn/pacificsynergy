@extends('layouts.app')

@section('content')
<!-- Back Button -->
<a href="{{ route('employees.view', $user->id) }}" class="flex items-center text-sm text-gray-500 hover:text-[#2d326b] mb-4">
    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
    </svg>
    Employees
</a>

<!-- Edit Employee Form -->
<form method="POST" action="{{ route('employees.update', $user->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="bg-white border border-gray-200 rounded-sm shadow-lg p-8 space-y-10 hover:shadow-xl transition duration-300">
        <!-- Header -->
        <div class="-mx-8 px-8 pb-4 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-semibold text-[#2d326b]">Edit User Profile</h2>
                <p class="text-sm text-gray-500 mt-1">Modify the employee's information.</p>
            </div>
            <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-md text-white text-sm font-medium bg-[#323B76] hover:bg-[#444d90] transition disabled:opacity-50 disabled:cursor-not-allowed">
                Save Changes
            </button>
        </div>

        <div class="lg:flex lg:gap-8 pb-4">
            <!-- Profile Image Section -->
            <div class="w-full lg:max-w-xs flex flex-col items-center text-center">
                <div class="relative w-40 h-40 mb-5">
                    <img 
                        id="photoPreview"
                        src="{{ asset('storage/' . $user->photo) }}"
                        class="w-full h-full object-cover rounded-full p-1 border border-gray-300"
                        alt="Profile Photo"
                    >
                    <label for="photo"
                        class="absolute bottom-2 right-2 bg-white p-1 rounded-full shadow hover:bg-gray-100 cursor-pointer">
                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.25 2.25 0 113.182 3.182L6.75 20.25H3v-3.75L16.732 3.732z" />
                        </svg>
                    </label>
                    <input type="file" name="photo" id="photo" accept="image/*" class="hidden">
                </div>
                @error('photo')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror

                <h2 class="text-lg font-semibold text-[#2d326b] mt-4">{{ $user->first_name }} {{ $user->last_name }}</h2>
                <p class="text-sm text-gray-600 mt-1">{{ $user->getRoleNames()->first() ?? 'No Role' }}</p>
                <p class="text-sm text-gray-500">{{ $user->email }}</p>
            </div>

            <!-- Form Fields Section -->
            <div class="flex-1 space-y-8 pt-1">
                <!-- User Info -->
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-[#2d326b]">User Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Last Name -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-[#2d326b]">Last Name</label>
                            <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm shadow-sm" />
                            @error('last_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- First Name -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-[#2d326b]">First Name</label>
                            <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm shadow-sm" />
                            @error('first_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Phone Number -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-[#2d326b]">Phone Number</label>
                            <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm shadow-sm" />
                            @error('phone_number')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Employee Number -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-[#2d326b]">Employee Number</label>
                            <input type="text" name="employee_number" value="{{ old('employee_number', $user->employee_number) }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm shadow-sm" />
                            @error('employee_number')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Role -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-[#2d326b]">Role</label>
                            <x-select-role 
                                name="role" 
                                :options="$roleOptions" 
                                :role="old('role', $user->getRoleNames()->first())" 
                            />
                        </div>
                        <!-- Department -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-[#2d326b]">Department</label>
                            <x-select-dropdown 
                                name="department" 
                                value="{{ old('department', $user->department) }}" 
                                :options="['Production Department' => 'Production Department']" 
                                placeholder="Select Department" 
                                required 
                            />
                            @error('department')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Account Security -->
                <div class="space-y-2">
                    <h3 class="text-lg font-semibold text-[#2d326b] flex items-center gap-2">
                        Account Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Email -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-[#2d326b]">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm shadow-sm" />
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <!-- Reset Password Section -->
                    <div class="pt-2">
                        <label class="block mb-2 text-sm font-medium text-[#2d326b]">Reset Password</label>
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm text-gray-500">Click the button below to send a password reset link to the user's email.</p>
                            <a href=""
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-[#323B76] hover:bg-[#444d90] rounded-md transition">
                                Send Reset Link
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Profile Photo Preview Script -->
<script>
    // Preview selected profile photo before upload
    document.getElementById('photo').addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (file) {
            document.getElementById('photoPreview').src = URL.createObjectURL(file);
        }
    });
</script>
@endsection