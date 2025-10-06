@extends('layouts.app')
@section('title', content: 'User')
@section('content')
<div class="container mx-auto px-4" x-data="{ tab: '{{ session('tab', 'info') }}' }">
<!-- Header -->
    <div class="mb-4">
        <h1 class="text-xl font-bold text-[#23527c]">Edit User Profile</h1>
    </div>

    <div class="border-t border-b border-gray-200 mb-4 mx-auto grid grid-cols-1 lg:grid-cols-4 gap-6">
        {{-- Sidebar --}}
        <div class="col-span-1 p-8 text-center">
            {{-- Profile Image --}}
            <div class="relative w-40 h-40 mx-auto mb-4">
            <form method="POST" action="{{ route('employees.update', $user->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <img id="photoPreview"
                    src="{{ $user->photo ? asset('storage/' . $user->photo) : asset('profile/default.jpg') }}"
                    onerror="this.onerror=null;this.src='{{ asset('img/default.jpg') }}';"
                    alt="Profile Photo"
                    class="w-full h-full object-cover border border-gray-300 p-1" />
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

                {{-- Upload Text Trigger --}}
                <label for="photo" class="text-sm text-blue-600 hover:underline cursor-pointer">
                    Upload Profile Picture
                </label>

                @error('photo')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror

            {{-- Sidebar Tabs --}}
            <nav class="mt-6 space-y-2 text-left">
                <button type="button"
                    @click="tab = 'info'"
                    class="w-full text-left px-4 py-2 text-sm rounded font-medium transition"
                    :class="tab === 'info' ? 'bg-[#23527c]/10 text-[#23527c]' : 'text-[#23527c] hover:bg-gray-100'">
                    User Information
                </button>
                <button type="button"
                    @click="tab = 'status'"
                    class="w-full text-left px-4 py-2 text-sm rounded font-medium transition"
                    :class="tab === 'status' ? 'bg-[#23527c]/10 text-[#23527c]' : 'text-[#23527c] hover:bg-gray-100'">
                    Account Status
                </button>
                <button type="button"
                    @click="tab = 'account'"
                    class="w-full text-left px-4 py-2 text-sm rounded font-medium transition"
                    :class="tab === 'account' ? 'bg-[#23527c]/10 text-[#23527c]' : 'text-[#23527c] hover:bg-gray-100'">
                    Account Information
                </button>
            </nav>
        </div>

        {{-- Right Panel --}}
        <div class="col-span-3 p-8">

            {{-- Tab: User Information --}}
                <div x-show="tab === 'info'" x-cloak>
                    <h3 class="text-lg font-bold text-[#23527c] mb-6">User Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-1 text-sm font-medium text-[#23527c]">First Name <span class="text-red-500">*</span></label>
                            <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}"
                                class="w-full border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:shadow-lg focus:outline-none" />
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-[#23527c]">Last Name <span class="text-red-500">*</span></label>
                            <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}"
                                class="w-full border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:shadow-lg focus:outline-none" />
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-[#23527c]">Phone Number <span class="text-red-500">*</span></label>
                            <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}"
                                class="w-full border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:shadow-lg focus:outline-none" />
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-[#23527c]">Employee Number <span class="text-red-500">*</span></label>
                            <input type="text" name="employee_number" value="{{ old('employee_number', $user->employee_number) }}"
                                class="w-full border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:shadow-lg focus:outline-none" />
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-[#23527c]">Role</label>
                            <x-select-dropdown-employee 
                                name="role" 
                                :options="$roleOptions" 
                                :selected="old('role', $user->getRoleNames()->first())" 
                                required 
                            />
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-[#23527c]">Department <span class="text-red-500">*</span></label>
                            <x-select-dropdown-employee 
                                name="department" 
                                :options="['Production Department' => 'Production Department']" 
                                :selected="old('department', $user->department ?? 'Production Department')" 
                                required />
                        </div>
                    </div>
                </div>

                {{-- Tab: Account Status --}}
                <div x-show="tab === 'status'" x-cloak>
                    <h3 class="text-lg font-bold text-[#23527c] mb-6">Account Status</h3>
                    <div class="grid grid-cols-1 gap-6 w-1/2">
                        <div>
                            <label class="block mb-1 text-sm font-medium text-[#23527c]">Account Status</label>
                            <input type="hidden" name="email" value="{{ old('email', $user->email) }}">
                            <x-select-dropdown
                                name="status" 
                                :options="['Active' => 'Active', 'Locked' => 'Locked']" 
                                :selected="old('status', $user->status)" 
                                required 
                            />
                        </div>
                    </div>

                    {{-- Password requirements --}}
                    <div class="mt-4">
                        <h2 class="mb-2 text-sm font-semibold text-[#23527c]">Locking Account:</h2>
                        <ul class="space-y-1 text-gray-500 list-disc list-inside text-sm">
                            <li>Locking the account will log the user out and block future access without deleting their records.</li>
                            <li>Active sessions will be cleared by the system.</li>
                        </ul>
                    </div>
                </div>

                {{-- Action Buttons (only for info & status tabs) --}}
                <div class="flex justify-between gap-4 mt-8" 
                    x-show="tab === 'info' || tab === 'status'" 
                    x-cloak>
                    <a href="{{ route('employees.index') }}"
                    class="inline-flex items-center px-3 py-2 bg-[#5a9fd4] hover:bg-[#4a8bc2] border border-[#5a9fd4] text-white text-sm font-medium">
                    <x-icons-back class="w-2 h-2 text-white" /> Back
                    </a>
                    <button type="submit"
                        class="px-3 py-2 bg-[#5bb75b] border border-[#43a143] text-white text-sm font-medium hover:bg-[#42a542]">
                        Save
                    </button>
                </div>
            </form>

            {{-- Account Information --}}
            <div x-show="tab === 'account'" x-cloak>
                <h3 class="text-lg font-bold text-[#23527c] mb-6">Account Information</h3>

                {{-- Email Field --}}
                <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                    <div>
                        <label class="block mb-1 text-sm font-medium text-[#23527c]">
                            Email
                        </label>
                        <p class="w-1/2 border border-gray-300 px-3 py-2 text-sm shadow-sm bg-gray-100 text-gray-500">{{ $user->email }}</p>
                    </div>
                </div>

                {{-- Reset Password --}}
                <div class="mt-6">
                    <label class="block mb-2 text-sm font-medium text-[#23527c]">
                        Reset Password
                    </label>

                    {{-- 🚨 Separate form ONLY for sending reset link --}}
                    <form method="POST" action="{{ route('admin.password.email') }}" class="flex items-center gap-4">
                        @csrf
                        <input type="hidden" id="reset-email" name="email" value="{{ old('email', $user->email) }}">
                        <button type="submit"
                            onclick="document.getElementById('reset-email').value = document.getElementById('email').value"
                            class="px-3 py-2 text-sm font-medium text-white bg-[#323B76] hover:bg-[#444d90] transition">
                            Send Reset Link
                        </button>
                    </form>

                    {{-- Password requirements --}} 
                    <div class="mt-4"> 
                        <h2 class="mb-2 text-sm font-semibold text-[#23527c]">Password Reset Link:</h2> 
                        <ul class="space-y-1 text-gray-500 list-disc list-inside text-sm"> 
                            <li>A password reset link will be sent to the user’s registered email address.</li> 
                        </ul> 
                    </div> 

                    {{-- Feedback --}}
                    @if (session('status'))
                        <p class="mt-2 text-sm text-green-600">{{ session('status') }}</p>
                    @endif
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>


        </div>
    </div>
</div>

{{-- JS: Preview new profile photo --}}
<script>
    document.getElementById('photo')?.addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (file) {
            document.getElementById('photoPreview').src = URL.createObjectURL(file);
        }
    });
</script>
@endsection
