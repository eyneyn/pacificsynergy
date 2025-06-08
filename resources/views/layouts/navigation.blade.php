<!-- Hamburger Button (Mobile & Tablet) -->
<button id="sidebar-toggle" class="lg:hidden fixed top-4 left-4 z-50 text-white bg-[#1B224F] p-2 rounded-md focus:outline-none">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
    </svg>
</button>

<!-- Sidebar -->
<aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen py-5 px-3 transition-transform -translate-x-full lg:translate-x-0 bg-[#2d326b] border-r border-[#2d326b]" aria-label="Sidebar">
    <div class="h-full overflow-y-auto">
        <!-- Sidebar Header -->
        <div class="flex justify-between items-center px-2 mb-6">
            <span class="font-medium sm:text-xl text-[#f9fafb]">Pacific Synergy</span>
            <!-- Sidebar Close Button (Mobile) -->
            <button id="sidebar-close" class="lg:hidden text-white p-1 rounded-md hover:bg-[#444d90] hidden">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <!-- Sidebar Navigation -->
        <ul class="space-y-2 font-medium">
            @include('layouts.partials.navigation._admin')
            @include('layouts.partials.navigation._production')
            @include('layouts.partials.navigation._logout')
        </ul>
    </div>
</aside>

<!-- Top Navbar -->
<nav class="fixed top-0 left-0 right-0 z-30 lg:ml-64 bg-white flex items-center justify-between px-6 py-3 shadow-md">
    <!-- Welcome Message -->
    <h1 class="pl-12 lg:pl-0 text-sm sm:text-base md:text-lg lg:text-xl font-semibold text-[#2d326b] truncate max-w-[70%] sm:max-w-full">
        Hi, {{ Auth::user()->first_name }}. Welcome Back.
    </h1>
    <div class="flex items-center space-x-4">
        <!-- User Profile Dropdown -->
        <div class="relative" id="user-menu-container">
            <button type="button" class="flex items-center space-x-2 px-3 py-1 bg-white border border-[#444d90] rounded-full shadow-sm transition duration-200 hover:shadow-md" id="user-menu-button">
                <img src="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : asset('img/default.jpg') }}" alt="User Avatar" class="w-8 h-8 rounded-full object-cover">
                <span class="text-sm font-semibold text-[#2d326b]">{{ Auth::user()->last_name }},{{ Auth::user()->first_name }}</span>
                <svg class="w-4 h-4 text-gray-600 transition duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <!-- Dropdown Menu -->
            <div class="absolute right-0 z-50 hidden mt-2 w-48 bg-white divide-y divide-gray-100 rounded-lg shadow-sm" id="user-dropdown">
                <div class="px-4 py-3">
                    <span class="block text-sm text-gray-900">{{ Auth::user()->email }}</span>
                    <span class="block text-sm text-gray-500 truncate">{{ Auth::user()->getRoleNames()->first() ?? 'No Role Assigned' }}</span>
                </div>
                <ul class="py-2">
                    <li>
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('Profile') }}</a>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>