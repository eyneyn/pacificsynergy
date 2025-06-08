{{-- Admin Navigation Section --}}
@canany(['roles.permission', 'employees.index'])
    <p class="text-gray-500 dark:text-gray-400 uppercase font-semibold text-xs px-4 py-2 tracking-wider">
        Administrator
    </p>
    <li>
        <button type="button"
            class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-[#444d90] dark:text-white dark:hover:bg-[#444d90]/90"
            aria-controls="dropdown-user-management" data-collapse-toggle="dropdown-user-management">
            {{-- User Management Icon --}}
            <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 18">
                <path d="M14 2a3.963 3.963 0 0 0-1.4.267 6.439 6.439 0 0 1-1.331 6.638A4 4 0 1 0 14 2Zm1 9h-1.264A6.957 6.957 0 0 1 15 15v2a2.97 2.97 0 0 1-.184 1H19a1 1 0 0 0 1-1v-1a5.006 5.006 0 0 0-5-5ZM6.5 9a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9ZM8 10H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5Z"/>
            </svg>
            <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">User Management</span>
            {{-- Dropdown Arrow --}}
            <svg class="w-3 h-3" fill="none" viewBox="0 0 10 6" xmlns="http://www.w3.org/2000/svg">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m1 1 4 4 4-4"/>
            </svg>
        </button>
        <ul id="dropdown-user-management" class="hidden py-2 space-y-2">
            @can('roles.permission')
                <li>
                    <a href="{{ route('roles.index') }}"
                        class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-[#376faa] dark:text-white dark:hover:bg-[#444d90]/90">
                        Roles
                    </a>
                </li>
            @endcan
            @can('employees.index')
                <li>
                    <a href="{{ route('employees.index') }}"
                        class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-[#376faa] dark:text-white dark:hover:bg-[#444d90]/90">
                        Employees
                    </a>
                </li>
            @endcan
        </ul>
    </li>
@endcanany
