@extends('layouts.app')
@section('title', content: 'Setting')
@section('content')

<div class="mx-32">
    <h2 class="text-xl mb-2 font-bold text-[#23527c]">Settings</h2>

    {{-- Back to Dashboard --}}
    <a href="{{ url('admin/dashboard') }}" class="text-xs text-gray-500 hover:text-[#23527c] mb-4 inline-flex items-center">    
        <x-icons-back-confi/>
        Admin Dashboard
    </a>

<!-- Profile Preview Card -->
    <div class="relative mb-8 border border-gray-300 rounded-lg shadow-md overflow-hidden">
        <!-- Cover / Background -->
        <div class="h-80 bg-gray-200 relative">
            @if(!empty($settings->background_image))
                <img src="{{ asset('storage/'.$settings->background_image) }}" 
                    class="w-full h-full object-cover"
                    alt="Background Image">
            @else
                <div class="flex items-center justify-center h-full text-gray-400 text-sm">
                    No Background Image
                </div>
            @endif
        </div>

        <!-- Profile Logo - Positioned to overlap cover and content area -->
        <div class="absolute left-6 top-60">
            <div class="w-52 h-52 rounded-full border-4 border-white overflow-hidden bg-gray-100 shadow-lg">
                @if(!empty($settings->logo))
                    <img src="{{ asset('storage/'.$settings->logo) }}" 
                        class="w-full h-full object-cover"
                        alt="Company Logo">
                @else
                    <div class="flex items-center justify-center h-full text-gray-400 text-xs">
                        No Logo
                    </div>
                @endif
            </div>
        </div>

<!-- Company Name on left, Edit button on right (same row) -->
<div class="pt-10 px-6 pb-20 flex justify-between items-start ml-56">
    <!-- Left: Company Name + subtitle -->
    <div>
        <h3 class="text-2xl font-bold text-[#323B76]">
            {{ $settings->company_name ?? 'Company Name' }}
        </h3>
        <p class="text-sm text-gray-500">Preview of your company profile</p>
    </div>

    <!-- Right: Edit Button -->
    <button onclick="toggleModal(true)"
            class="inline-flex items-center justify-center gap-1 p-2 bg-[#323B76] hover:bg-[#444d90] border border-[#323B76] text-white text-sm font-medium">
            <x-icons-edit class="w-2 h-2" />
            Edit
    </button>
</div>

    </div>

</div>

<!-- Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white w-full max-w-2xl rounded-lg shadow-lg p-6 relative">
        <!-- Close Button -->
        <button onclick="toggleModal(false)"
                class="absolute top-2 right-2 text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>

        <h2 class="text-xl font-bold text-[#23527c] mb-4">Edit Company Profile</h2>

        <form action="{{ route('setting.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Company Name -->
            <div class="mb-4">
                <label for="company_name" class="block text-sm font-medium text-[#23527c] mb-1">Company Name <span style="color: red;">*</span></label>
                <input type="text" 
                       id="company_name" 
                       name="company_name" 
                       value="{{ old('company_name', $settings->company_name ?? '') }}"
                       class="w-full px-3 py-2 border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400">
            </div>

            <!-- Logo Upload -->
            <div class="mb-4">
                <label for="logo" class="block text-sm font-medium text-[#23527c] mb-1">Upload Logo <span style="color: red;">*</span></label>
                <input type="file" id="logo" name="logo" accept="image/*"
                       class="w-full px-3 py-2 border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400">
            </div>

            <!-- Background Upload -->
            <div class="mb-4">
                <label for="background_image" class="block text-sm font-medium text-[#23527c] mb-1">Upload Background <span style="color: red;">*</span></label>
                <input type="file" id="background_image" name="background_image" accept="image/*"
                       class="w-full px-3 py-2 border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400">
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <button type="button" onclick="toggleModal(false)"
                        class="px-3 py-2 text-gray-600 bg-white border border-gray-300  hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit"
                        class="inline-flex items-center justify-center gap-1 bg-[#5bb75b] border border-[#43a143] text-white px-3 py-2 hover:bg-[#42a542] text-sm">
                    <x-icons-save class="w-2 h-2 text-white" />
                    Save
                </button>
            </div>
        </form>
    </div>
</div>


<!-- JavaScript for image preview (optional enhancement) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Logo preview
    const logoInput = document.getElementById('logo');
    if (logoInput) {
        logoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // You can add preview functionality here if needed
                    console.log('Logo selected:', file.name);
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Background image preview
    const bgInput = document.getElementById('background_image');
    if (bgInput) {
        bgInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // You can add preview functionality here if needed
                    console.log('Background image selected:', file.name);
                };
                reader.readAsDataURL(file);
            }
        });
    }
});

function toggleModal(show = true) {
    const modal = document.getElementById('editModal');
    if (show) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    } else {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}
</script>
@endsection
