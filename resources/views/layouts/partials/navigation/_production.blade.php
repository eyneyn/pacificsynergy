{{-- Production Navigation Section --}}
<p class="text-gray-400 uppercase font-semibold text-xs px-4 py-2 tracking-wider">Production</p>

{{-- Dashboard Link --}}
@php
    $isDashboardActive = request()->is('analytics/line-efficiency') || request()->is('analytics/material-utilization');
@endphp

<li>
    @can('analytics.dashboard')
    <!-- Clickable Dashboard Toggle -->
    <button
        id="dashboard-toggle"
        class="flex items-center w-full p-2 text-base text-white transition duration-75 rounded-lg cursor-pointer
            hover:bg-[#444d90] {{ $isDashboardActive ? 'bg-[#444d90]' : '' }}">
        
        {{-- Dashboard Icon --}}
        <svg class="shrink-0 w-5 h-5 text-gray-300 transition duration-75"
                    aria-hidden="true"
             xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 294 294">
            <g>
                <path d="M279,250H15c-8.284,0-15,6.716-15,15s6.716,15,15,15h264c8.284,0,15-6.716,15-15S287.284,250,279,250z"/>
                <path d="M30.5,228h47c5.247,0,9.5-4.253,9.5-9.5v-130c0-5.247-4.253-9.5-9.5-9.5h-47c-5.247,0-9.5,4.253-9.5,9.5v130 C21,223.747,25.253,228,30.5,228z"/>
                <path d="M123.5,228h47c5.247,0,9.5-4.253,9.5-9.5v-195c0-5.247-4.253-9.5-9.5-9.5h-47c-5.247,0-9.5,4.253-9.5,9.5v195 C114,223.747,118.253,228,123.5,228z"/>
                <path d="M216.5,228h47c5.247,0,9.5-4.253,9.5-9.5v-105c0-5.247-4.253-9.5-9.5-9.5h-47c-5.247,0-9.5,4.253-9.5,9.5v105 C207,223.747,211.253,228,216.5,228z"/>
            </g>
        </svg>
        <span class="flex-1 ms-3 text-left whitespace-nowrap">Dashboard</span>
        {{-- Dropdown Arrow --}}
        <svg id="dashboard-arrow" class="w-3 h-3 text-white ml-auto transition-transform duration-200"
             fill="none" viewBox="0 0 10 6" xmlns="http://www.w3.org/2000/svg">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="m1 1 4 4 4-4" />
        </svg>
    </button>
    
    <!-- Submenu - Show based on click state or active page -->
    <ul id="dashboard-submenu" class="{{ $isDashboardActive ? 'block' : 'hidden' }} py-2 space-y-2">
        <li>
            <a href="{{ url('analytics/line_efficiency') }}"
               class="block p-2 pl-10 text-white rounded-lg transition
               hover:bg-[#444d90] 
               {{ request()->is('analytics/line-efficiency') ? 'bg-[#444d90]' : '' }}">
                Line Efficiency
            </a>
        </li>
        <li>
            <a href="{{ url('analytics/material_utilization') }}"
               class="block p-2 pl-10 text-white rounded-lg transition
               hover:bg-[#444d90] 
               {{ request()->is('analytics/material_utilization') ? 'bg-[#444d90]' : '' }}">
                Material Utilization
            </a>
        </li>
    </ul>
                @endcan
</li>

{{-- JavaScript for Dashboard Toggle Functionality --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dashboardToggleButton = document.getElementById('dashboard-toggle');
    const dashboardSubmenu = document.getElementById('dashboard-submenu');
    const dashboardArrow = document.getElementById('dashboard-arrow');
    
    if (dashboardToggleButton && dashboardSubmenu && dashboardArrow) {
        dashboardToggleButton.addEventListener('click', function() {
            // Toggle submenu visibility
            const isHidden = dashboardSubmenu.classList.contains('hidden');
            
            if (isHidden) {
                dashboardSubmenu.classList.remove('hidden');
                dashboardSubmenu.classList.add('block');
                dashboardArrow.style.transform = 'rotate(180deg)';
            } else {
                dashboardSubmenu.classList.remove('block');
                dashboardSubmenu.classList.add('hidden');
                dashboardArrow.style.transform = 'rotate(0deg)';
            }
        });
    }
});
</script>


{{-- Production Reports Link --}}
<li>
    @can('report.index')
        <a href="{{ url('report/index') }}" class="flex items-center p-2 text-white rounded-lg hover:bg-[#444d90] group
        {{ request()->is('report/index') ? 'bg-[#444d90]' : '' }}">
            <svg class="shrink-0 w-5 h-5 text-gray-300 transition duration-75 group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                <path d="M20 8L14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8zM9 19H7v-9h2v9zm4 0h-2v-6h2v6zm4 0h-2v-3h2v3zM14 9h-1V4l5 5h-4z"/>
            </svg>
            <span class="flex-1 ms-3 whitespace-nowrap">Production Reports</span>
        </a>
    @endcan
</li>

{{-- Analytics & Reports Link --}}
<li>
    @can('analytics.index')
        <a href="{{ url('analytics/index') }}" class="flex items-center p-2 text-white rounded-lg hover:bg-[#444d90]  group
        {{ request()->is('analytics/index') ? 'bg-[#444d90]' : '' }}">
            <svg class="shrink-0 w-5 h-5 text-gray-300 transition duration-75 group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 32 32">
                <g>
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
    @endcan
</li>

{{-- Configuration Link --}}
<li>
    @can('configuration.index')
        <a href="{{ url('configuration/index') }}" class="flex items-center p-2 text-white rounded-lg hover:bg-[#444d90]  group
        {{ request()->is('configuration/index') ? 'bg-[#444d90]' : '' }}">
            <svg class="shrink-0 w-5 h-5 text-gray-300 transition duration-75 group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 512 512">
                <g>
                    <g fill="currentColor" transform="translate(85.331200, 85.329067)">
                        <path d="M149.339733,0 L149.335467,213.3376 L128.002133,213.3376 L128.002133,256.004267 L149.335467,256.004267 L149.339733,341.333333 L192.0064,341.333333 L192.002133,256.004267 L213.335467,256.004267 L213.335467,213.3376 L192.002133,213.3376 L192.0064,0 L149.339733,0 Z M21.3333333,0 L21.3333333,85.3546667 L0,85.3546667 L0,128 L21.3333333,128 L21.3333333,341.333333 L64,341.333333 L64,128 L85.3333333,128 L85.3333333,85.3546667 L64,85.3546667 L64,0 L21.3333333,0 Z M277.314133,0.00426666667 L277.314133,128.004267 L255.9808,128.004267 L255.9936,170.666667 L277.326933,170.666667 L277.326933,341.333333 L320.014933,341.333333 L320.014933,170.666667 L341.348267,170.666667 L341.335467,128.004267 L320.002133,128.004267 L320.014933,0 L277.314133,0.00426666667 Z"></path>
                    </g>
                </g>
            </svg>
            <span class="flex-1 ms-3 whitespace-nowrap">Configuration</span>
        </a>
    @endcan
</li>