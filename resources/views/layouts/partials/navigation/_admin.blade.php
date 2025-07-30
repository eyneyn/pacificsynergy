{{-- Admin Navigation Section --}}
@canany(['user.dashboard','roles.permission', 'employees.index'])
    <p class="text-gray-400 uppercase font-semibold text-xs px-4 py-2 tracking-wider">
        Administrator
    </p>
    {{-- Dashboard Link --}}
    <li>
        @can('user.dashboard')
            <a href="{{ route('admin.dashboard') }}" class="flex items-center p-2 text-white rounded-lg hover:bg-[#444d90] group
            {{ request()->is('admin/dashboard') ? 'bg-[#444d90]' : '' }}">
                <svg class="shrink-0 w-5 h-5 text-gray-300 transition duration-75 group-hover:text-white"
                    aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor"
                    viewBox="0 0 24 24">
                <g>
                    <rect width="24" height="24" fill="none"></rect>
                    <path d="M15.024 22C16.2771 22 17.3524 21.9342 18.2508 21.7345C19.1607 21.5323 19.9494 21.1798 20.5646 20.5646C21.1798 19.9494 21.5323 19.1607 21.7345 18.2508C21.9342 17.3524 22 16.2771 22 15.024V12C22 10.8954 21.1046 10 20 10H12C10.8954 10 10 10.8954 10 12V20C10 21.1046 10.8954 22 12 22H15.024Z" />
                    <path d="M2 15.024C2 16.2771 2.06584 17.3524 2.26552 18.2508C2.46772 19.1607 2.82021 19.9494 3.43543 20.5646C4.05065 21.1798 4.83933 21.5323 5.74915 21.7345C5.83628 21.7538 5.92385 21.772 6.01178 21.789C7.09629 21.9985 8 21.0806 8 19.976V12C8 10.8954 7.10457 10 6 10H4C2.89543 10 2 10.8954 2 12V15.024Z" />
                    <path d="M8.97597 2C7.72284 2 6.64759 2.06584 5.74912 2.26552C4.8393 2.46772 4.05062 2.82021 3.4354 3.43543C2.82018 4.05065 2.46769 4.83933 2.26549 5.74915C2.24889 5.82386 2.23327 5.89881 2.2186 5.97398C2.00422 7.07267 2.9389 8 4.0583 8H19.976C21.0806 8 21.9985 7.09629 21.789 6.01178C21.772 5.92385 21.7538 5.83628 21.7345 5.74915C21.5322 4.83933 21.1798 4.05065 20.5645 3.43543C19.9493 2.82021 19.1606 2.46772 18.2508 2.26552C17.3523 2.06584 16.2771 2 15.024 2H8.97597Z" />
                </g>
                </svg>
                <span class="flex-1 ms-3 whitespace-nowrap">Dashboard</span>
            </a>
        @endcan
    </li>

@php
    $isUserManagementActive = request()->is('roles*') || request()->is('employees*');
@endphp

<li>
    <!-- Clickable User Management Toggle -->
    <button
        id="user-management-toggle"
        class="flex items-center w-full p-2 text-base text-white transition duration-75 rounded-lg cursor-pointer
            hover:bg-[#444d90] {{ $isUserManagementActive ? 'bg-[#444d90]' : '' }}">
        {{-- User Management Icon --}}
        <svg class="shrink-0 w-5 h-5 text-gray-300 transition duration-75"
             aria-hidden="true"
             xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 18">
            <path d="M14 2a3.963 3.963 0 0 0-1.4.267 6.439 6.439 0 0 1-1.331 6.638A4 4 0 1 0 14 2Zm1 9h-1.264A6.957 6.957 0 0 1 15 15v2a2.97 2.97 0 0 1-.184 1H19a1 1 0 0 0 1-1v-1a5.006 5.006 0 0 0-5-5ZM6.5 9a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9ZM8 10H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5Z"/>
        </svg>
        <span class="flex-1 ms-3 text-left whitespace-nowrap">User Management</span>
        {{-- Dropdown Arrow --}}
        <svg id="user-management-arrow" class="w-3 h-3 text-white ml-auto transition-transform duration-200"
             fill="none" viewBox="0 0 10 6" xmlns="http://www.w3.org/2000/svg">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="m1 1 4 4 4-4" />
        </svg>
    </button>
    
    <!-- Submenu - Show based on click state or active page -->
    <ul id="user-management-submenu" class="{{ $isUserManagementActive ? 'block' : 'hidden' }} py-2 space-y-2">
        @can('roles.permission')
            <li>
                <a href="{{ route('roles.index') }}"
                   class="block p-2 pl-10 text-white rounded-lg transition hover:bg-[#444d90]
                   {{ request()->is('roles*') ? 'bg-[#444d90]' : '' }}">
                    Roles
                </a>
            </li>
        @endcan
        @can('employees.index')
            <li>
                <a href="{{ route('employees.index') }}"
                   class="block p-2 pl-10 text-white rounded-lg transition hover:bg-[#444d90]
                   {{ request()->is('employees*') ? 'bg-[#444d90]' : '' }}">
                    Employees
                </a>
            </li>
        @endcan
    </ul>
</li>

{{-- JavaScript for Toggle Functionality --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleButton = document.getElementById('user-management-toggle');
    const submenu = document.getElementById('user-management-submenu');
    const arrow = document.getElementById('user-management-arrow');
    
    if (toggleButton && submenu && arrow) {
        toggleButton.addEventListener('click', function() {
            // Toggle submenu visibility
            const isHidden = submenu.classList.contains('hidden');
            
            if (isHidden) {
                submenu.classList.remove('hidden');
                submenu.classList.add('block');
                arrow.style.transform = 'rotate(180deg)';
            } else {
                submenu.classList.remove('block');
                submenu.classList.add('hidden');
                arrow.style.transform = 'rotate(0deg)';
            }
        });
    }
});
</script>

@endcanany