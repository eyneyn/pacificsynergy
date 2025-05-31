    <!-- layouts/navigation.blade.php -->
    <!-- Hamburger Button (Mobile & Tablet) -->
    <button id="sidebar-toggle" class="lg:hidden fixed top-4 left-4 z-50 text-white bg-[#1B224F] p-2 rounded-md focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>

    <!-- Sidebar -->
    <aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen py-5 px-3 transition-transform -translate-x-full lg:translate-x-0 bg-[#2d326b] border-r border-[#2d326b]" aria-label="Sidebar">
        <div class="h-full overflow-y-auto">
            <div class="flex justify-between items-center px-2 mb-6">
                <span class="font-medium sm:text-xl text-[#f9fafb]">Pacific Synergy</span>
                <button id="sidebar-close" class="lg:hidden text-white p-1 rounded-md hover:bg-[#444d90] hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <ul class="space-y-2 font-medium">
                @role('Admin')
                <p class="text-gray-500 dark:text-gray-400 uppercase font-semibold text-xs px-4 py-2 tracking-wider">
                        Administrator
                </p>
                    <li>
                        <a href="" 
                            class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-[#444d90] dark:hover:bg-[#444d90]/90 group">
                            <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M15.024 22C16.2771 22 17.3524 21.9342 18.2508 21.7345C19.1607 21.5323 19.9494 21.1798 20.5646 20.5646C21.1798 19.9494 21.5323 19.1607 21.7345 18.2508C21.9342 17.3524 22 16.2771 22 15.024V12C22 10.8954 21.1046 10 20 10H12C10.8954 10 10 10.8954 10 12V20C10 21.1046 10.8954 22 12 22H15.024Z" />
                                <path d="M2 15.024C2 16.2771 2.06584 17.3524 2.26552 18.2508C2.46772 19.1607 2.82021 19.9494 3.43543 20.5646C4.05065 21.1798 4.83933 21.5323 5.74915 21.7345C5.83628 21.7538 5.92385 21.772 6.01178 21.789C7.09629 21.9985 8 21.0806 8 19.976L8 12C8 10.8954 7.10457 10 6 10H4C2.89543 10 2 10.8954 2 12V15.024Z" />
                                <path d="M8.97597 2C7.72284 2 6.64759 2.06584 5.74912 2.26552C4.8393 2.46772 4.05062 2.82021 3.4354 3.43543C2.82018 4.05065 2.46769 4.83933 2.26549 5.74915C2.24889 5.82386 2.23327 5.89881 2.2186 5.97398C2.00422 7.07267 2.9389 8 4.0583 8H19.976C21.0806 8 21.9985 7.09629 21.789 6.01178C21.772 5.92385 21.7538 5.83628 21.7345 5.74915C21.5322 4.83933 21.1798 4.05065 20.5645 3.43543C19.9493 2.82021 19.1606 2.46772 18.2508 2.26552C17.3523 2.06584 16.2771 2 15.024 2H8.97597Z" />
                            </svg>
                            <span class="ms-3">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <button type="button" class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-[#444d90] dark:text-white dark:hover:bg-[#444d90]/90" aria-controls="dropdown-user-management" data-collapse-toggle="dropdown-user-management">
                            <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 18">
                                <path d="M14 2a3.963 3.963 0 0 0-1.4.267 6.439 6.439 0 0 1-1.331 6.638A4 4 0 1 0 14 2Zm1 9h-1.264A6.957 6.957 0 0 1 15 15v2a2.97 2.97 0 0 1-.184 1H19a1 1 0 0 0 1-1v-1a5.006 5.006 0 0 0-5-5ZM6.5 9a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9ZM8 10H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5Z"/>
                            </svg>
                                <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">User Management</span>
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                </svg>
                        </button>
                        <ul id="dropdown-user-management" class="hidden py-2 space-y-2">
                            <li>
                                <a href="{{ route('roles.roles_permission')}}" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-[#376faa] dark:text-white dark:hover:bg-[#444d90]/90">Roles</a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-[#376faa] dark:text-white dark:hover:bg-[#444d90]/90">Employees</a>
                            </li>
                        </ul>
                    </li>
                @endrole

                <p class="text-gray-500 dark:text-gray-400 uppercase font-semibold text-xs px-4 py-2 tracking-wider">
                    Production
               </p>
              
                <li>
                    <a href="" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-[#376faa] dark:hover:bg-[#444d90]/90 group">
                        <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 294 294">
                            <g>
                            <path d="M279,250H15c-8.284,0-15,6.716-15,15s6.716,15,15,15h264c8.284,0,15-6.716,15-15S287.284,250,279,250z"></path>
                            <path d="M30.5,228h47c5.247,0,9.5-4.253,9.5-9.5v-130c0-5.247-4.253-9.5-9.5-9.5h-47c-5.247,0-9.5,4.253-9.5,9.5v130 C21,223.747,25.253,228,30.5,228z"></path>
                            <path d="M123.5,228h47c5.247,0,9.5-4.253,9.5-9.5v-195c0-5.247-4.253-9.5-9.5-9.5h-47c-5.247,0-9.5,4.253-9.5,9.5v195 C114,223.747,118.253,228,123.5,228z"></path>
                            <path d="M216.5,228h47c5.247,0,9.5-4.253,9.5-9.5v-105c0-5.247-4.253-9.5-9.5-9.5h-47c-5.247,0-9.5,4.253-9.5,9.5v105 C207,223.747,211.253,228,216.5,228z"></path>
                            </g>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Dashboard</span>
                    </a>
                </li>
               
               <li>
                    <a href="{{ url('report/index') }}"
                    class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-[#376faa] dark:hover:bg-[#444d90]/90 group">
                    <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="currentColor"
                        viewBox="0 0 24 24">
                        <path d="M20 8L14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8zM9 19H7v-9h2v9zm4 0h-2v-6h2v6zm4 0h-2v-3h2v3zM14 9h-1V4l5 5h-4z"/>
                    </svg>
                    <span class="flex-1 ms-3 whitespace-nowrap">Production Reports</span>
                        {{-- @if ($submittedReportCount > 0)
                            <span class="inline-flex items-center justify-center min-w-[1.5rem] h-5 px-2 ms-2 text-xs font-bold text-white bg-red-600 rounded-full">
                                {{ $submittedReportCount }}
                            </span>
                        @endif --}}
                    </a>
               </li>
               <li>
                  <a href="{{ url ('analytics/index')}}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-[#376faa] dark:hover:bg-[#444d90]/90 group">
                     <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" 
                           aria-hidden="true" 
                           xmlns="http://www.w3.org/2000/svg" 
                           fill="currentColor" 
                           viewBox="0 0 32 32">
                        <g id="SVGRepo_iconCarrier">
                        <defs>
                           <style>.cls-1{fill:currentColor;}</style>
                        </defs>
                        <rect class="cls-1" height="10" rx="1" ry="1" width="6" x="17" y="17"></rect>
                        <rect class="cls-1" height="16" rx="1" ry="1" width="6" x="25" y="11"></rect>
                        <rect class="cls-1" height="12" rx="1" ry="1" width="6" x="9" y="15"></rect>
                        <rect class="cls-1" height="7" rx="1" ry="1" width="6" x="1" y="20"></rect>
                        <path d="M31,25H1v3a3,3,0,0,0,3,3H28a3,3,0,0,0,3-3Z"></path>
                        <path class="cls-1" d="M4,17H2a1,1,0,0,1,0-2H3.52L10,6.94a1,1,0,1,1,1.56,1.24L4.78,16.62A1,1,0,0,1,4,17Z"></path>
                        <path class="cls-1" d="M21.25,11.44a1,1,0,0,1-.62-.22,1,1,0,0,1-.16-1.4l6.75-8.44A1,1,0,0,1,28,1h2a1,1,0,0,1,0,2H28.48L22,11.06A1,1,0,0,1,21.25,11.44Z"></path>
                        <rect class="cls-1" height="6" transform="translate(-0.8 16.4) rotate(-53.14)" width="2" x="15" y="6"></rect>
                        <circle cx="12" cy="6" r="3"></circle>
                        <circle cx="20" cy="11.99" r="3"></circle>
                        </g>
                     </svg>
                     <span class="flex-1 ms-3 whitespace-nowrap">Analytics & Reports</span>
                  </a>
               </li>
               <li>
                  <button type="button" class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-[#376faa] dark:text-white dark:hover:bg-[#444d90]/90" aria-controls="dropdown-production" data-collapse-toggle="dropdown-production">
                        <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                           <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                           <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                           <g id="SVGRepo_iconCarrier">
                           <title>Audiomack icon</title>
                           <path d="M.33 11.39s.54-.09.77.14c.22.23.07.71-.22.72-.3.01-.57.06-.77-.14a.443.443 0 01.22-.72zm5.88 3.26c-.05.01-.11-.02-.16-.06-.39-.53-.53-2.37-.71-2.48-.18-.11-.85 1.02-2.19.9-.55-.05-1.12-.41-1.45-.66.03-.41.03-1.39.86-1.07.51.19 1.37.72 2.13-.23.84-1.05 1.3-.74 1.57-.51.28.22.1 1.41.51 1.08.41-.33 2.08-2.39 2.08-2.39s1.29-1.29 1.49.06c.2 1.36 1.04 2.87 1.27 2.82.22-.04 2.82-5.27 3.19-5.61.37-.34 1.63-.29 1.57.57-.06.87-.19 6.25-.19 6.25s-.15 1.52.09.71c.1-.34.21-.64.34-1 .64-2.03 1.73-5.51 2.28-7.3.12-.42.23-.79.32-1.07v-.01c.03-.13.06-.23.09-.32.05-.15.08-.26.09-.28.02-.07.09-.12.19-.16.09-.06.2-.06.31-.06.31-.03.69.01 1.04.11.11 0 .22.03.32.11 0 0 .01 0 .02.01.03.02.06.05.1.1h.01c.01.02.03.05.05.07.19.29.31.81.19 1.74-.3 2.31-.53 7.07-.53 7.07s-.05.23.44-.77c.01-.04.03-.07.05-.1.03-.02.06-.04.1-.08.29-.36 1.09-.56 1.65-.56.23.03.43.09.54.16.22.33.09 1.55.09 1.55-.46.04-1.34.29-1.65.33-.31.05-.78 2.05-1.44 1.85-.66-.21-2.13-1.12-2.13-1.24 0-.11.12-1.44.15-1.79v-.07-.01c.03-.27.01-.39-.12-.12-.11.23-.58 1.72-1.11 3.34-.05.14-1.05 3.13-1.18 3.49-.15.42-.29.75-.38.91-.13.19-.32.3-.58.23-.65-.2-1.46-1.08-1.47-1.3-.02-1.24.06-7.9-.24-7.35-.32.57-2.73 4.52-2.73 4.52-.04.01-.07.01-.11.01-.17-.02-.44-.07-.51-.23 0-.01-.01-.02-.01-.03-.01-.01-.01-.02-.02-.04-.03-.11-.04-.23-.07-.33-.11-.36-.28-.88-.47-1.4-.27-.9-.56-1.82-.61-1.92-.09-.2-.22-.12-.35 0-.54.45-1.68 2.45-2.72 2.56z"></path>
                           </g>
                        </svg>
                        <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Production Metrics</span>
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                        </svg>
                  </button>
                  <ul id="dropdown-production" class="hidden py-2 space-y-2">
                     <li>
                        <a href="{{ url ('metrics/standard/index')}}" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-[#376faa] dark:text-white dark:hover:bg-[#444d90]">Product Standard</a>
                     </li>
                     <li>
                        <a href="{{ url ('metrics/configuration')}}" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-[#376faa] dark:text-white dark:hover:bg-[#444d90]">Configuration </a>
                     </li>
                  </ul>
               </li>
                <ul class="pt-4 mt-4 space-y-2 font-medium border-t border-gray-200 dark:border-gray-700">
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="route('logout')" 
                            onclick="event.preventDefault();
                            this.closest('form').submit();" 
                            class="flex items-center p-2 text-white rounded-lg hover:bg-[#363c7e] transition">
                           <svg class="shrink-0 w-4 h-4 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 96.943 96.943"
                                fill="currentColor"
                                aria-hidden="true">
                            <g>
                                <path d="M61.168,83.92H11.364V13.025H61.17c1.104,0,2-0.896,2-2V3.66c0-1.104-0.896-2-2-2H2c-1.104,0-2,0.896-2,2v89.623 c0,1.104,0.896,2,2,2h59.168c1.105,0,2-0.896,2-2V85.92C63.168,84.814,62.274,83.92,61.168,83.92z"></path>
                                <path d="M96.355,47.058l-26.922-26.92c-0.75-0.751-2.078-0.75-2.828,0l-6.387,6.388c-0.781,0.781-0.781,2.047,0,2.828 l12.16,12.162H19.737c-1.104,0-2,0.896-2,2v9.912c0,1.104,0.896,2,2,2h52.644L60.221,67.59c-0.781,0.781-0.781,2.047,0,2.828 l6.387,6.389c0.375,0.375,0.885,0.586,1.414,0.586c0.531,0,1.039-0.211,1.414-0.586l26.922-26.92 c0.375-0.375,0.586-0.885,0.586-1.414C96.943,47.941,96.73,47.433,96.355,47.058z"></path>
                            </g>
                            </svg>
                            <span class="ml-3">Logout</span>
                        </a>
                    </li>
                </ul>
            </ul>
        </div>
    </aside>

    <!-- Top Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-30 lg:ml-64 bg-white flex items-center justify-between px-6 py-3 shadow-md">
        <h1 class="text-lg lg:text-xl font-semibold text-blue-950">Hi, {{ Auth::user()->first_name }}. Welcome Back.</h1>

        <div class="flex items-center space-x-4">
            <!-- Notifications -->
            {{-- <button class="p-3 bg-transparent border border-[#5D67A1] rounded-md hover:bg-[#dae2ff] transition">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 00-4 0v.341C8.67 6.165 8 7.388 8 8.75v5.408c0 .538-.214 1.055-.595 1.437L6 17h5m2 0v1a2 2 0 11-4 0v-1m4 0H9" />
                </svg>
            </button> --}}

        <!-- User Profile Dropdown -->
        <div class="relative" id="user-menu-container">
            <button type="button"
                class="flex items-center space-x-2 px-3 py-1 bg-white border border-[#444d90] rounded-full shadow-sm transition duration-200 hover:shadow-md"
                id="user-menu-button">
                <img src="" alt="User Avatar" class="w-8 h-8 rounded-full">
                <span class="text-sm font-semibold text-gray-800">{{ Auth::user()->first_name }}</span>
                <svg class="w-4 h-4 text-gray-600 transition duration-200" fill="none"
                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

        <!-- Dropdown menu -->
        <div class="absolute right-0 z-50 hidden mt-2 w-48 bg-white divide-y divide-gray-100 rounded-lg shadow-sm"
            id="user-dropdown">
            <div class="px-4 py-3">
                <span class="block text-sm text-gray-900"></span>
                <span class="block text-sm text-gray-500 truncate"></span>
            </div>
            <ul class="py-2">
                <li>
                    <a href="{{ route('profile.edit')}}"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"> {{ __('Profile') }}</a>
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
    </div>
    </div>
</nav>
