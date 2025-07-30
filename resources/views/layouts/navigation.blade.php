<!-- Top Navbar -->
<nav id="top-navbar"
    class=" w-full bg-[#2d326b] flex items-center justify-between border-b-4 border-[#6a95d2] shadow-[0_4px_6px_#e2f2ff] transition-all duration-300">

     <!-- Left Side: Hamburger Menu + Logo -->
    <div class="flex items-center gap-2">
        <!-- Toggle Button (Hamburger) -->
        <button id="sidebar-toggle"
            class="text-[#2d326b] p-4 hover:bg-[#3c49a3] transition">
            <!-- Menu 4 Dots Icon -->
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" 
                viewBox="-10.24 -10.24 532.48 532.48" 
                fill="white" stroke="white" stroke-width="20.99">
                <g>
                    <path d="M50.047,0C22.404,0,0,22.4,0,50.047c0,27.646,22.404,50.046,50.047,50.046 c27.642,0,50.046-22.4,50.046-50.046C100.093,22.4,77.689,0,50.047,0z"></path>
                    <path d="M256,0c-27.642,0-50.047,22.4-50.047,50.047c0,27.646,22.404,50.046,50.047,50.046 c27.642,0,50.047-22.4,50.047-50.046C306.047,22.4,283.642,0,256,0z"></path>
                    <path d="M461.953,100.093c27.638,0,50.047-22.4,50.047-50.046C512,22.4,489.591,0,461.953,0 s-50.046,22.4-50.046,50.047C411.907,77.693,434.315,100.093,461.953,100.093z"></path>
                    <path d="M50.047,205.953C22.404,205.953,0,228.353,0,256s22.404,50.047,50.047,50.047 c27.642,0,50.046-22.4,50.046-50.047S77.689,205.953,50.047,205.953z"></path>
                    <path d="M256,205.953c-27.642,0-50.047,22.4-50.047,50.047s22.404,50.047,50.047,50.047 c27.642,0,50.047-22.4,50.047-50.047S283.642,205.953,256,205.953z"></path>
                    <path d="M461.953,205.953c-27.638,0-50.046,22.4-50.046,50.047s22.408,50.047,50.046,50.047 S512,283.647,512,256S489.591,205.953,461.953,205.953z"></path>
                    <path d="M50.047,411.907C22.404,411.907,0,434.307,0,461.953C0,489.6,22.404,512,50.047,512 c27.642,0,50.046-22.4,50.046-50.047C100.093,434.307,77.689,411.907,50.047,411.907z"></path>
                    <path d="M256,411.907c-27.642,0-50.047,22.4-50.047,50.046C205.953,489.6,228.358,512,256,512 c27.642,0,50.047-22.4,50.047-50.047C306.047,434.307,283.642,411.907,256,411.907z"></path>
                    <path d="M461.953,411.907c-27.638,0-50.046,22.4-50.046,50.046c0,27.647,22.408,50.047,50.046,50.047 S512,489.6,512,461.953C512,434.307,489.591,411.907,461.953,411.907z"></path>
                </g>
            </svg>
        </button>
        
        <!-- Logo + Title -->
        <h2 id="welcome-header"
            class="uppercase text-md text-white flex items-center gap-2">
            <img src="{{ asset('img/logo.svg') }}" alt="Pacific Synergy Logo"
                class="w-8 h-8 flex-shrink-0">
            Pacific Synergy
        </h2>
    </div>

        <!-- Right Side Items -->
    <div class="flex items-center">
        <!-- Notification Bell -->
<button id="dropdownNotificationButton" data-dropdown-toggle="dropdownNotification"
    class="text-[#1B224F] px-2 py-4 hover:bg-[#ffd322] transition group">
    <svg class="w-4 h-5 text-white fill-white group-hover:text-[#2d326b] group-hover:fill-[#2d326b] transition-colors" 
         xmlns="http://www.w3.org/2000/svg" 
         viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round"
            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
    </svg>
</button>
            {{-- <!-- Badge -->
            <span
                class="absolute bottom-5 left-5 w-4 h-4 bg-red-600 text-white text-[11px] font-bold flex items-center justify-center rounded-full">
                2
            </span> --}}
        </button>
        
        <!-- Notification Dropdown -->
        <div id="dropdownNotification"
            class="z-40 hidden w-full max-w-sm bg-white border border-gray-300 shadow-3xl divide-y divide-gray-300"
            aria-labelledby="dropdownNotificationButton">
            <div class="px-3 py-3 font-semibold text-[#2d326b]">Notifications</div>
            <div class="divide-y divide-gray-200">
                @foreach($statusNotifications as $note)
                <a href="{{ route('report.view', $note->production_report_id) }}"
                    class="flex items-start px-4 py-4 hover:bg-[#e2f2ff] transition">
                    <div class="pl-1 text-sm text-gray-700 w-full">
                        <p class="mb-1">
                            Production report
                            <span class="font-medium">{{ $note->productionReport?->production_date ?? 'N/A' }}</span>
                            was successfully
                            <span class="font-semibold text-[#2d326b]">{{ $note->status }}</span>
                            added by
                            <span class="text-gray-800">{{ $note->user?->first_name }}
                                {{ $note->user?->last_name }}</span>
                        </p>
                        <p class="text-xs text-blue-600">{{ $note->created_at?->diffForHumans() ?? '' }}</p>
                    </div>
                </a>
                @endforeach
                @foreach($changeLogs as $log)
                <a href="{{ route('report.view', $log->production_report_id) }}"
                    class="flex items-start px-5 py-4 hover:bg-[#e2f2ff] transition">
                    <div class="pl-1 text-sm text-gray-700 w-full">
                        <p class="mb-1">
                            <span class="font-semibold text-[#2d326b]">Changes</span> made on
                            <span class="font-medium">{{ $log->report?->production_date ?? 'N/A' }}</span>
                            by <span class="text-gray-800">{{ $log->user?->first_name }}
                                {{ $log->user?->last_name }}</span>
                        </p>
                        <p class="text-xs text-blue-600">{{ $log->updated_at?->diffForHumans() ?? '' }}</p>
                    </div>
                </a>
                @endforeach
            </div>
            <a href="#"
                class="block px-4 py-3 text-center font-medium text-sm text-[#2d326b] hover:bg-[#e2f2ff] transition">
                <div class="inline-flex items-center space-x-2">
                    <svg class="w-5 h-5 text-[#2d326b]" fill="currentColor" viewBox="0 0 20 14">
                        <path d="..." />
                    </svg>
                    <span>View all</span>
                </div>
            </a>
        </div>
        
        <!-- User Menu -->
        <div class="relative" id="user-menu-container">
<button type="button"
    class="flex items-center gap-1 p-3 hover:bg-[#ffd322] transition duration-200 group"
    id="user-menu-button">
    <div class="flex items-center gap-2">
        <span class="text-white text-md group-hover:text-[#2d326b] transition-colors">Hi,</span>
        <span class="text-white text-md group-hover:text-[#2d326b] transition-colors">{{ Auth::user()->first_name }}</span>
    </div>
    <img src="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : asset('img/default.jpg') }}"
        alt="User Avatar"
        class="w-7 h-7 p-1 rounded-full">
</button>
            <!-- Dropdown -->
            <div id="user-dropdown"
                class="absolute right-0 z-50 hidden mt-2 w-48 bg-white divide-y divide-gray-300 border border-gray-300 shadow-3xl top-full">
                <div class="px-4 py-3">
                    <span class="block text-sm text-gray-900">{{ Auth::user()->email }}</span>
                    <span class="block text-sm text-gray-500 truncate">{{ Auth::user()->getRoleNames()->first() ??
                        'No Role Assigned' }}</span>
                </div>
                <ul class="py-2">
                    <li>
                        <a href="{{ route('profile.edit') }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-[#e2f2ff]">Profile</a>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-[#e2f2ff]">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<!-- Sidebar -->
<aside id="logo-sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-screen py-5 px-3 transition-transform transform -translate-x-full bg-[#2d326b] border-r border-[#2d326b]"
    aria-label="Sidebar">
    <div class="h-full overflow-y-auto">
        <!-- Sidebar Header -->
        <div class="relative px-2 mb-6">
            <span class="font-medium sm:text-xl text-[#f9fafb]">Pacific Synergy</span>
            <!-- Close Button -->
            <button id="sidebar-close"
                class="absolute top-0 right-0 p-2 text-white hover:text-red-400 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <!-- Navigation -->
        <ul class="space-y-2 font-medium">
            @include('layouts.partials.navigation._admin')
            @include('layouts.partials.navigation._production')
            @include('layouts.partials.navigation._logout')
        </ul>
    </div>
</aside>