@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold text-[#2d326b] mb-6">My Profile</h2>

<div 
    x-data="{ tab: '{{ session('success') || $errors->has('current_password') || $errors->has('new_password') || $errors->has('new_password_confirmation') ? 'password' : 'info' }}' }" 
    class="grid grid-cols-1 lg:grid-cols-4 gap-6"
>
    {{-- Sidebar --}}
    <div class="col-span-1 bg-white border border-gray-200 rounded-md shadow-md p-6 text-center">
        {{-- Profile Image Form --}}
        <form method="POST" action="{{ route('profile.update.photo') }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="relative w-28 h-28 mx-auto mb-4">
                <img 
                    id="photoPreview"
                    src="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : asset('img/default.jpg') }}"
                    alt="User Photo"
                    class="w-full h-full rounded-full object-cover border border-gray-300 p-1"
                >
                <label for="photo" class="absolute bottom-2 right-2 bg-white p-1 rounded-full shadow hover:bg-gray-100 cursor-pointer">
                    {{-- Edit Icon --}}
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.25 2.25 0 113.182 3.182L6.75 20.25H3v-3.75L16.732 3.732z" />
                    </svg>
                </label>
                <input type="file" name="photo" id="photo" accept="image/*" class="hidden">
            </div>
            <div class="mb-2">
                <button type="submit"
                    class="mt-1 px-4 py-1 text-sm font-medium bg-[#2d326b] text-white rounded hover:bg-[#444d90] transition">
                    Save Photo
                </button>
                @if (session('status') === 'photo-updated')
                    <p class="text-green-600 text-sm mt-2">Photo updated successfully.</p>
                @endif
                @error('photo')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </form>
        {{-- Name and Role --}}
        <div class="mt-4">
            <h3 class="text-lg font-semibold text-[#2d326b]">
                {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
            </h3>
            <p class="text-sm text-gray-500">
                {{ Auth::user()->getRoleNames()->first() ?? 'Your Position' }}
            </p>
        </div>
        {{-- Sidebar Navigation --}}
        <nav class="mt-6 space-y-2 text-left">
            <button @click="tab = 'info'" type="button"
                class="w-full text-left px-4 py-2 text-sm rounded font-medium transition"
                :class="tab === 'info' ? 'bg-[#2d326b]/10 text-[#2d326b]' : 'text-gray-600 hover:bg-gray-100'">
                Personal Information
            </button>
            <button @click="tab = 'password'" type="button"
                class="w-full text-left px-4 py-2 text-sm rounded font-medium transition"
                :class="tab === 'password' ? 'bg-[#2d326b]/10 text-[#2d326b]' : 'text-gray-600 hover:bg-gray-100'">
                Change Password
            </button>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full text-left px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded font-medium transition">
                    Log Out
                </button>
            </form>
        </nav>
    </div>

    {{-- Right Panel --}}
    <div class="col-span-3 bg-white border border-gray-200 rounded-md shadow-md p-6">
        {{-- Personal Information Form --}}
        <form method="POST" action="{{ route('profile.update') }}" x-show="tab === 'info'" x-cloak>
            @csrf
            @method('PATCH')
            @if(session('success'))
                <div class="text-green-600 text-sm mb-4">
                    {{ session('success') }}
                </div>
            @endif
            <h3 class="text-lg font-bold text-[#2d326b] mb-6">User Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- First Name --}}
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-600">First Name</label>
                    <input type="text" name="first_name" value="{{ old('first_name', Auth::user()->first_name) }}"
                        class="w-full rounded border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#2d326b] focus:ring-[#2d326b]" />
                    @error('first_name')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                {{-- Last Name --}}
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-600">Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name', Auth::user()->last_name) }}"
                        class="w-full rounded border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#2d326b] focus:ring-[#2d326b]" />
                    @error('last_name')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                {{-- Phone Number --}}
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-600">Phone Number</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number', Auth::user()->phone_number) }}"
                        maxlength="11" pattern="^09\d{9}$"
                        class="w-full rounded border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#2d326b] focus:ring-[#2d326b]" />
                    @error('phone_number')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                {{-- Email --}}
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-600">Email</label>
                    <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}"
                        class="w-full rounded border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#2d326b] focus:ring-[#2d326b]" />
                    @error('email')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                {{-- Position --}}
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-600">Position</label>
                    <p class="w-full rounded border border-gray-300 px-3 py-2 text-sm shadow-sm bg-gray-100 text-gray-500">
                        {{ Auth::user()->getRoleNames()->first() ?? 'Your Position' }}
                    </p>
                </div>
                {{-- Department --}}
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-600">Department</label>
                    <p class="w-full rounded border border-gray-300 px-3 py-2 text-sm shadow-sm bg-gray-100 text-gray-500">
                        {{ Auth::user()->department }}
                    </p>
                </div>
            </div>
            <div class="flex justify-end gap-4 mt-8">
                <button type="reset"
                    class="px-5 py-2 text-sm font-medium border border-[#2d326b] text-[#2d326b] rounded hover:bg-[#2d326b]/10 transition">
                    Discard Changes
                </button>
                <button type="submit"
                    class="px-5 py-2 text-sm font-medium bg-[#2d326b] text-white rounded hover:bg-[#444d90] transition">
                    Save Changes
                </button>
            </div>
        </form>

        {{-- Password Update Form --}}
        <form method="POST" action="{{ route('profile.password.update') }}"
            x-show="tab === 'password'" x-cloak
            x-data="{ changePass: {{ $errors->has('current_password') || $errors->has('new_password') || $errors->has('new_password_confirmation') ? 'true' : 'false' }} }">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Email (readonly) --}}
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-600">Email</label>
                    <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" disabled
                        class="w-full rounded border border-gray-300 px-3 py-2 text-sm shadow-sm bg-gray-100 text-gray-500" />
                    @error('email')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div></div>
                {{-- Current Password --}}
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-600">Current Password</label>
                    <input type="password" name="current_password"
                        class="w-full border border-gray-300 px-3 py-2 rounded text-sm shadow-sm focus:border-[#2d326b] focus:ring-[#2d326b]"
                        required />
                    @error('current_password')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div></div>
                {{-- New Password --}}
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-600">New Password</label>
                    <input type="password" name="new_password"
                        class="w-full border border-gray-300 px-3 py-2 rounded text-sm shadow-sm focus:border-[#2d326b] focus:ring-[#2d326b]"
                        :required="changePass" />
                    @error('new_password')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                {{-- Confirm Password --}}
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-600">Confirm New Password</label>
                    <input type="password" name="new_password_confirmation"
                        class="w-full border border-gray-300 px-3 py-2 rounded text-sm shadow-sm focus:border-[#2d326b] focus:ring-[#2d326b]"
                        :required="changePass" />
                    @error('new_password_confirmation')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                {{-- Password requirements --}}
                <div class="mt-4">
                    <h2 class="mb-2 text-sm font-semibold text-blue-950">Password requirements:</h2>
                    <ul class="space-y-1 text-gray-500 list-disc list-inside text-sm">
                        <li>Must be between 6 and 20 characters long</li>
                        <li>Must include at least one lowercase letter</li>
                        <li>Must include at least one number and one special character (e.g., ., !, @, #, ?)</li>
                    </ul>
                </div>
            </div>
            <div class="flex flex-wrap justify-end items-center gap-4 mt-8">
                <div class="flex gap-4">
                    <button type="reset"
                        class="px-5 py-2 text-sm font-medium border border-[#2d326b] text-[#2d326b] rounded hover:bg-[#2d326b]/10 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-5 py-2 text-sm font-medium bg-[#2d326b] text-white rounded hover:bg-[#444d90] transition">
                        Update
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- JS: Image Preview --}}
<script>
    // Preview selected profile image before upload
    document.getElementById('photo').addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (file) {
            document.getElementById('photoPreview').src = URL.createObjectURL(file);
        }
    });
</script>
@endsection
