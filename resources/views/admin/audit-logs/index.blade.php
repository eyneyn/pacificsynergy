{{-- resources/views/admin/audit-logs/index.blade.php --}}
@extends('layouts.app')

@section('content')
    {{-- Page Title --}}
    <h2 class="text-xl mb-2 font-bold text-[#23527c]">Activity Logs</h2>

    {{-- Back to Configuration Link --}}
    <a href="{{ url('admin/dashboard') }}" class="text-xs text-gray-500 hover:text-[#23527c] mb-4 inline-flex items-center">
        <x-icons-back-confi/>
        Admin Dashboard
    </a>

    {{-- Filters --}}
    <form method="GET" class="bg-white border border-gray-200 p-4 mb-4 grid grid-cols-1 md:grid-cols-6 gap-3 text-xs">
        {{-- Search --}}
        <div>
            <label for="q" class="block text-gray-700 mb-1 font-medium">Search</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-2 text-gray-500">
                    <x-icons-search class="w-4 h-4"/>
                </span>
                <input
                    type="text"
                    id="q"
                    name="q"
                    value="{{ $filters['q'] ?? '' }}"
                    placeholder="User, IP, UA"
                    class="w-full pl-8 pr-2 py-2 text-sm border border-gray-300 shadow-md focus-within:border-blue-500 focus-within:shadow-lg focus-within:outline-none placeholder-gray-400"
                />
            </div>
        </div>

        {{-- Event --}}
        <div>
            <label for="event" class="block text-gray-700 mb-1 font-medium">Event</label>
            <select id="event" name="event"
                    class="w-full p-2 text-sm border border-gray-300 rounded focus:border-blue-500 focus:shadow-lg focus:outline-none">
                <option value="all" @selected(($filters['event'] ?? 'all') === 'all')>All Events</option>

                @foreach($eventsList as $category => $events)
                    <optgroup label="{{ $category }}">
                        @foreach($events as $val => $label)
                            <option value="{{ $val }}" @selected(($filters['event'] ?? '') === $val)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>

        {{-- User --}}
        <div>
            <label for="user_id" class="block text-gray-700 mb-1 font-medium">User</label>
            <select id="user_id" name="user_id"
                    class="w-full p-2 text-sm border border-gray-300  focus:border-blue-500 focus:shadow-lg focus:outline-none">
                <option value="">All Users</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" @selected(($filters['user_id'] ?? '') == $u->id)>
                        {{ $u->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Date From --}}
        <div>
            <label for="date_from" class="block text-gray-700 mb-1 font-medium">Date From</label>
            <input type="date" id="date_from" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                class="w-full p-2 text-sm border border-gray-300  focus:border-blue-500 focus:shadow-lg focus:outline-none">
        </div>

        {{-- Date To --}}
        <div>
            <label for="date_to" class="block text-gray-700 mb-1 font-medium">Date To</label>
            <input type="date" id="date_to" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                class="w-full p-2 text-sm border border-gray-300  focus:border-blue-500 focus:shadow-lg focus:outline-none">
        </div>

        {{-- Buttons --}}
        <div class="flex items-end gap-2 text-sm">
            <button class="px-4 py-2 bg-[#323B76] border border-[#323B76] hover:bg-[#444d90] text-white ">
                Filter
            </button>
            <a href="{{ route('audit-logs.index') }}"
            class="px-4 py-2 border border-gray-300 hover:bg-gray-50 ">
                Reset
            </a>
        </div>
    </form>

    <table class="w-full text-sm text-left border border-[#E5E7EB] border-collapse shadow-sm">
        <thead>
            <tr class="text-xs text-white uppercase bg-[#35408e]">
                <th class="p-2 border border-[#d9d9d9] text-center">Activity</th>
                <th class="p-2 border border-[#d9d9d9] text-center">User</th>
                <th class="p-2 border border-[#d9d9d9] text-center">Timestamp (Asia/Manila)</th>
                <th class="p-2 border border-[#d9d9d9] text-center">IP Address</th>
                <th class="p-2 border border-[#d9d9d9] text-center">Details</th>
            </tr>
        </thead>
        <tbody>
            @php
                $eventLabels = [
                    'login'            => 'Logged In',
                    'logout'           => 'Logged Out',
                    'failed_login'     => 'Failed Login',
                    'password_reset'   => 'Password Reset',

                    'report_create'    => 'Created Report',
                    'report_edit'      => 'Edited Report',
                    'report_validate'  => 'Validated Report',
                    'report_pdf'  => 'Export to Daily Report',
                    
                    'role_add'     => 'Created Role',
                    'role_update'  => 'Updated Role',

                    'employee_add'     => 'Created Employee',
                    'employee_update'  => 'Updated Employee',

                    'standard_create'  => 'Created Standard',
                    'standard_edit'    => 'Updated Standard',
                    'standard_delete'  => 'Deleted Standard',

                    'defect_add'        => 'Created Defect',
                    'defect_update'        => 'Update Defect',
                    'defect_delete'        => 'Deleted Defect',

                    'maintenance_store'        => 'Created Maintenance',
                    'maintenance_update'        => 'Updated Maintenance',
                    'maintenance_destroy'        => 'Deleted Maintenance',

                    'line_store'        => 'Created Line',
                    'line_update'        => 'Updated Line',
                    'line_destroy'        => 'Deleted Line',

                    'material_summary_export'        => 'Material Summary Export',
                    'material_annual_export'        => 'Material Annual Export',
                    'material_monthly_export'        => 'Material Monthly Export',

                    'line_summary_export'        => 'Line Summary Export',
                    'line_annual_export'        => 'Line Annual Export',
                    'line_monthly_export'        => 'Line Monthly Export',
                ];
            @endphp

            @forelse($logs as $log)
                <tr class="bg-white border-b border-[#d9d9d9] hover:bg-[#e5f4ff]">
                    {{-- Activity --}}
                    <td class="p-2 text-gray-600 text-left flex items-center gap-2">
                        @if($log->event === 'login')
                            {{-- Login Icon --}}
                            <svg class="shrink-0 w-4 h-4 transition duration-75" 
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 96.943 96.943" fill="#3c49a3" aria-hidden="true">
                                <g>
                                    <path d="M35.775,83.92h49.805V13.025H35.773c-1.104,0-2-0.896-2-2V3.66c0-1.104,0.896-2,2-2h59.168c1.104,0,2,0.896,2,2v89.623
                                        c0,1.104-0.896,2-2,2H35.775c-1.105,0-2-0.896-2-2V85.92C33.775,84.814,34.67,83.92,35.775,83.92z"></path>
                                    <path d="M0.588,47.058l26.922-26.92c0.75-0.751,2.078-0.75,2.828,0l6.387,6.388c0.781,0.781,0.781,2.047,0,2.828L24.565,41.516
                                        h52.644c1.104,0,2,0.896,2,2v9.912c0,1.104-0.896,2-2,2H24.565l12.16,12.162c0.781,0.781,0.781,2.047,0,2.828l-6.387,6.389
                                        c-0.375,0.375-0.885,0.586-1.414,0.586c-0.531,0-1.039-0.211-1.414-0.586L0.588,49.471C0.213,49.096,0,48.587,0,48.058
                                        C0,47.529,0.213,47.021,0.588,47.058z"></path>
                                </g>
                            </svg>


                        @elseif($log->event === 'logout')
                            {{-- Logout Icon --}}
                            <svg class="shrink-0 w-4 h-4 transition duration-75" 
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 96.943 96.943" fill="#3c49a3" aria-hidden="true">
                                <g>
                                    <path d="M61.168,83.92H11.364V13.025H61.17c1.104,0,2-0.896,2-2V3.66c0-1.104-0.896-2-2-2H2c-1.104,0-2,0.896-2,2v89.623 c0,1.104,0.896,2,2,2h59.168c1.105,0,2-0.896,2-2V85.92C63.168,84.814,62.274,83.92,61.168,83.92z"></path>
                                    <path d="M96.355,47.058l-26.922-26.92c-0.75-0.751-2.078-0.75-2.828,0l-6.387,6.388c-0.781,0.781-0.781,2.047,0,2.828 l12.16,12.162H19.737c-1.104,0-2,0.896-2,2v9.912c0,1.104,0.896,2,2,2h52.644L60.221,67.59c-0.781,0.781-0.781,2.047,0,2.828 l6.387,6.389c0.375,0.375,0.885,0.586,1.414,0.586c0.531,0,1.039-0.211,1.414-0.586l26.922-26.92 c0.375-0.375,0.586-0.885,0.586-1.414C96.943,47.941,96.73,47.433,96.355,47.058z"></path>
                                </g>
                            </svg>

                        @elseif($log->event === 'failed_login')
                            {{-- Failed Login --}}
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m-2-2a9 9 0 110-18 9 9 0 010 18z"/>
                            </svg>

                        @elseif(in_array($log->event, ['report_pdf','report_validate','report_edit','report_create']))
                            {{-- Production Report --}}
                            <svg class="w-6 h-6" fill="#3c49a3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M20 8L14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8zM9 19H7v-9h2v9zm4 0h-2v-6h2v6zm4 0h-2v-3h2v3zM14 9h-1V4l5 5h-4z"/>
                            </svg>
                        
                        @elseif(in_array($log->event, ['material_summary_export','material_annual_export','material_monthly_export','line_summary_export','line_annual_export','line_monthly_export']))
                            {{-- Export Excel Icon --}}
                            <svg class="shrink-0 w-5 h-5 transition duration-75" fill="#3c49a3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 32 32">
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

                        @elseif(in_array($log->event, ['line_store','line_update','line_destroy']))
                            {{-- Line Icon --}}
                            <svg class="w-6 h-6" fill="#3c49a3" viewBox="0 0 292.074 292.073" xmlns="http://www.w3.org/2000/svg"><path d="M190.166,182.858h-23.509c-4.863,0-8.814,3.945-8.814,8.814c0,4.876,3.951,8.815,8.814,8.815h23.509 c4.864,0,8.821-3.939,8.821-8.815C198.987,186.798,195.03,182.858,190.166,182.858z"></path><path d="M235.028,182.858h-24.235c-4.864,0-8.815,3.945-8.815,8.814c0,4.876,3.951,8.815,8.815,8.815h24.235 c4.863,0,8.803-3.939,8.803-8.815C243.831,186.798,239.891,182.858,235.028,182.858z"></path><path d="M60.415,106.727h77.5c4.871,0,8.815-3.942,8.815-8.812c0-4.875-3.944-8.817-8.815-8.817h-77.5 c-4.87,0-8.818,3.942-8.818,8.817C51.596,102.784,55.544,106.727,60.415,106.727z"></path><path d="M283.247,265.34h-11.518V116.655c0-4.87-3.957-8.818-8.821-8.818h-19.077V17.909c0-4.87-3.951-8.818-8.814-8.818h-24.235 c-4.864,0-8.815,3.948-8.815,8.818v89.928h-7.752V17.909c0-4.87-3.957-8.818-8.821-8.818h-24.229 c-4.863,0-8.809,3.948-8.809,8.818v89.928H29.166c-4.87,0-8.824,3.948-8.824,8.818V265.34H8.818c-4.875,0-8.818,3.945-8.818,8.827 c0,4.87,3.942,8.815,8.818,8.815h20.348h31.249h77.5h125h20.344c4.864,0,8.815-3.945,8.815-8.815 C292.067,269.279,288.123,265.34,283.247,265.34z M69.223,265.34v-64.857h59.862v64.857H69.223z"></path></svg>

                        @elseif(in_array($log->event, ['defect_add','defect_update','defect_delete']))
                            {{-- Defect Icon --}}
                            <svg class="w-6 h-6" fill="#3c49a3" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M478.582,309.137l-31.117-17.994c2.074-11.406,3.307-23.121,3.307-35.143c0-12.025-1.233-23.721-3.307-35.129l31.1-17.961 c13.778-7.965,18.499-25.57,10.53-39.344l-36.457-63.197c-7.951-13.773-25.569-18.494-39.344-10.543l-31.117,17.943 c-17.817-15.139-38.404-27.078-60.879-35.143V36.721c0-15.914-12.911-28.813-28.813-28.813h-72.932 c-15.916,0-28.8,12.898-28.8,28.813v35.906c-22.565,8.064-43.125,20.004-60.938,35.143L98.743,89.807 c-6.618-3.813-14.488-4.846-21.852-2.867c-7.399,1.979-13.697,6.799-17.51,13.416l-36.489,63.197 c-3.827,6.617-4.866,14.488-2.887,21.869c1.979,7.363,6.813,13.662,13.435,17.488l31.117,17.961 c-2.092,11.408-3.343,23.104-3.343,35.129c0,12.021,1.251,23.736,3.343,35.156l-31.117,17.98 c-13.792,7.951-18.499,25.568-10.548,39.361l36.475,63.178c3.826,6.619,10.124,11.439,17.523,13.418 c7.364,1.979,15.233,0.939,21.852-2.887l31.104-17.98c17.813,15.139,38.354,27.064,60.906,35.08v35.969 c0,15.92,12.884,28.816,28.8,28.816h72.932c15.902,0,28.813-12.896,28.813-28.816v-35.969c22.475-8.016,43.062-19.941,60.861-35.08 l31.135,17.98c13.774,7.951,31.393,3.225,39.344-10.563l36.475-63.164C497.063,334.705,492.343,317.088,478.582,309.137z M256.01,389.414c-73.691,0-133.414-59.723-133.414-133.414c0-73.678,59.723-133.4,133.414-133.4 c73.663,0,133.399,59.723,133.399,133.4C389.409,329.691,329.673,389.414,256.01,389.414z"></path><path d="M327.496,184.514c-9.18-9.166-24.043-9.166-33.195,0l-38.291,38.305l-38.291-38.305c-9.148-9.166-24.029-9.166-33.195,0 c-9.148,9.166-9.148,24.012,0,33.178L222.832,256l-38.309,38.322c-9.148,9.166-9.148,24.012,0,33.178 c4.576,4.576,10.598,6.867,16.598,6.867c6.004,0,12.025-2.291,16.598-6.867l38.291-38.305l38.291,38.305 c4.576,4.576,10.58,6.867,16.584,6.867c6.018,0,12.021-2.291,16.611-6.867c9.152-9.166,9.152-24.012,0-33.178L289.173,256 l38.323-38.309C336.648,208.525,336.648,193.68,327.496,184.514z"></path></svg>

                        @elseif(in_array($log->event, ['maintenance_store','maintenance_update','maintenance_destroy']))
                            {{-- Maintenance Icon --}}
                            <svg class="w-6 h-6" viewBox="0 0 512 512" fill="#3c49a3" xmlns="http://www.w3.org/2000/svg"><title>maintenance-documents</title><path d="M320,64 L405.333333,149.333333 L405.333333,426.666667 L64,426.666667 L64,64 L320,64 Z M302.326888,106.666667 L106.666667,106.666667 L106.666667,384 L362.666667,384 L362.666667,167.006445 L302.326888,106.666667 Z M256,7.10542736e-15 L298.666667,42.6666667 L42.6666667,42.6666667 L42.6666667,362.666667 L7.10542736e-15,362.666667 L7.10542736e-15,7.10542736e-15 L256,7.10542736e-15 Z M244.302904,167.174593 C260.439702,188.157298 265.883899,213.970305 260.713161,232.815619 C260.06747,235.91652 282.811168,260.09809 328.944255,305.360329 C344.0292,320.445274 344.0292,335.530218 328.944255,350.615163 C314.74666,364.812758 300.549065,365.64791 286.35147,353.120621 L211.482391,282.046388 C192.635434,287.217603 166.823081,281.773415 145.841366,265.636132 C130.452444,245.401095 125.144195,218.951922 129.431109,199.995106 L162.251622,232.815619 L195.072135,216.405362 L211.482391,183.58485 L178.661879,150.764337 C197.618105,146.477784 224.068368,151.785327 244.302904,167.174593 Z"></path></svg>

                        @elseif(in_array($log->event, ['standard_add','standard_update','standard_delete']))
                            {{-- Standard Icon --}}
                            <svg class="w-6 h-6" fill="#3c49a3" viewBox="0 0 52 52" xmlns="http://www.w3.org/2000/svg"><path d="M38.77,25.61c1.42,0,2.54,1.54,3.79,2.07s3.19.24,4.13,1.24.71,2.84,1.24,4.14S50,35.42,50,36.84s-1.54,2.54-2.07,3.78-.23,3.19-1.24,4.14-2.83.7-4.13,1.24-2.37,2.06-3.79,2.06S36.23,46.53,35,46s-3.19-.24-4.13-1.24-.71-2.84-1.24-4.14-2.07-2.36-2.07-3.78,1.53-2.54,2.07-3.78.23-3.19,1.24-4.14,2.83-.71,4.13-1.24S37.36,25.61,38.77,25.61ZM26.71,41a4.82,4.82,0,0,1,.38.7c.5,1.22.47,2.83.89,4.08H3.39A1.5,1.5,0,0,1,2,44.15H2V42.56A1.51,1.51,0,0,1,3.39,41H26.71ZM43,33.18a.55.55,0,0,0-.81,0h0l-4.38,5-2-2a.55.55,0,0,0-.81,0h0l-.82.77a.52.52,0,0,0,0,.77h0l2.8,2.8a1.13,1.13,0,0,0,.82.35,1.05,1.05,0,0,0,.82-.35l5.19-5.77a.62.62,0,0,0,0-.77h0ZM6.65,12.3A1.38,1.38,0,0,1,8,13.73H8V36a1.38,1.38,0,0,1-1.32,1.43H3.33A1.39,1.39,0,0,1,2,36H2V13.73A1.39,1.39,0,0,1,3.33,12.3H6.65Zm19,0a1.43,1.43,0,0,1,1.43,1.43h0V32c-.68,1.57-2.63,3-2.63,4.81a2.48,2.48,0,0,0,.06.54H21.35A1.43,1.43,0,0,1,19.92,36h0V13.73a1.43,1.43,0,0,1,1.43-1.43h4.3Zm-9.71,0a1.52,1.52,0,0,1,1.59,1.43h0V36a1.52,1.52,0,0,1-1.59,1.43h-1.6A1.52,1.52,0,0,1,12.75,36h0V13.73a1.52,1.52,0,0,1,1.59-1.43h1.6Zm17.91,0a1.52,1.52,0,0,1,1.6,1.43h0V24.21a7,7,0,0,1-1.5.94,19.63,19.63,0,0,1-3.28.69V13.73a1.52,1.52,0,0,1,1.59-1.43h1.59Zm8.63,0a1.39,1.39,0,0,1,1.33,1.43h0v11.5l-.21-.08c-1.58-.67-3-2.63-4.83-2.63a2.79,2.79,0,0,0-.94.17v-9a1.39,1.39,0,0,1,1.33-1.43h3.32Zm-.07-8.36a1.51,1.51,0,0,1,1.4,1.59h0V7.12a1.51,1.51,0,0,1-1.4,1.59h-39A1.5,1.5,0,0,1,2,7.12H2V5.53A1.5,1.5,0,0,1,3.39,3.94h39Z"></path></svg>

                        @elseif(in_array($log->event, ['employee_add','employee_update']))
                            {{-- Employee Icon --}}
                            <svg class="w-5 h-5 text-teal-700" fill="currentColor" viewBox="0 0 20 18" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14 2a3.963 3.963 0 0 0-1.4.267 6.439 6.439 0 0 1-1.331 6.638A4 4 0 1 0 14 2Zm1 9h-1.264A6.957 6.957 0 0 1 15 15v2a2.97 2.97 0 0 1-.184 1H19a1 1 0 0 0 1-1v-1a5.006 5.006 0 0 0-5-5ZM6.5 9a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9ZM8 10H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5Z"/>
                            </svg>

                        @elseif(in_array($log->event, ['role_add','role_update']))
                            {{-- Role Icon --}}
                            <svg class="w-6 h-6 text-pink-700" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>

                        @else
                            {{-- Default (Info Circle) --}}
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z"/>
                            </svg>
                        @endif

                        <span>{{ $eventLabels[$log->event] ?? ucfirst(str_replace('_',' ', $log->event)) }}</span>
                    </td>


                    {{-- User --}}
                    <td class="p-2 border border-[#d9d9d9] text-gray-600 text-center">
                        @if($log->user)
                            <div class="font-medium">{{ $log->user->name }}</div>
                            <div class="text-gray-500 text-xs">{{ $log->user->email }}</div>
                        @else
                            <span class="text-gray-500 italic">System / Unknown</span>
                        @endif
                    </td>

                    {{-- Timestamp --}}
                    <td class="p-2 border border-[#d9d9d9] text-[#23527c] text-center">
                        {{ optional($log->created_at)->timezone('Asia/Manila')->format('M d, Y H:i:s') }}
                    </td>

                    {{-- IP --}}
                    <td class="p-2 border border-[#d9d9d9] text-gray-600 text-center">{{ $log->ip_address ?? '—' }}</td>

                    {{-- Details --}}
                    <td class="p-2 border border-[#d9d9d9] text-gray-600 text-left">
                        @if(in_array($log->event, ['report_create','report_edit','report_validate']))
                            {{ $log->context['sku'] ?? 'Unknown SKU' }} | {{ $log->context['line'] ?? 'Line ?' }}

                        @elseif(in_array($log->event, ['standard_add','standard_update','standard_delete']))
                            Standard: {{ $log->context['standard'] ?? 'Unknown Standard' }}

                        @elseif(in_array($log->event, ['defect_add','defect_update','defect_delete']))
                            Defect: {{ $log->context['defect'] ?? 'Unknown Defect' }}

                        @elseif(in_array($log->event, ['maintenance_store','maintenance_update','maintenance_destroy']))
                            Maintenance: {{ $log->context['maintenance'] ?? 'Unknown Maintenance' }}

                        @elseif(in_array($log->event, ['line_store','line_update','line_destroy']))
                            Line: {{ $log->context['line'] ?? 'Unknown Line' }}

                        @elseif(in_array($log->event, [
                            'material_summary_export','material_annual_export','material_monthly_export',
                            'line_summary_export','line_annual_export','line_monthly_export'
                        ]))
                            Generated Excel report:
                            @if(!empty($log->context['material']))
                                {{ $log->context['material'] }}
                            @elseif(!empty($log->context['line']))
                                {{ $log->context['line'] }}
                            @endif

                        @elseif($log->event === 'report_pdf')
                            Generated PDF report: {{ $log->context['report'] ?? 'Unknown Report' }}

                         @elseif(in_array($log->event, ['employee_add','employee_update']))
                            Employee: {{ $log->context['employee'] ?? 'Unknown Employee' }}

                        @elseif(in_array($log->event, ['role_add','role_update']))
                            Role: {{ $log->context['role'] ?? 'Unknown Role' }}

                        @elseif($log->event === 'login')
                            <span class="text-left">Successful Login</span>

                        @elseif($log->event === 'logout')
                            <span class="text-left">Successful Logged Out</span>

                        @else
                            —
                        @endif
                    </td>

                </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-2 border border-[#d9d9d9] text-gray-600 text-center">No matching records found</td>
                    </tr>
                @endforelse
        </tbody>
    </table>

    {{-- Entries Info + Pagination --}}
    <div class="mt-4 flex flex-col md:flex-row items-center justify-between text-sm text-gray-600 gap-2">
        {{-- Entries Information --}}
        <div>
            @if($logs->total() > 0)
                Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} entries
            @else
                Showing 0 to 0 of 0 entries
            @endif
        </div>

        {{-- Pagination --}}
        <div>
            {{ $logs->appends(request()->query())->links('pagination::tailwind') }}
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dateFrom = document.getElementById('date_from');
        const dateTo = document.getElementById('date_to');

        // When user picks a "from" date → update "to" min
        dateFrom.addEventListener('change', function () {
            if (dateFrom.value) {
                dateTo.min = dateFrom.value;

                // If currently chosen "to" date is earlier, reset it
                if (dateTo.value && dateTo.value < dateFrom.value) {
                    dateTo.value = dateFrom.value;
                }
            } else {
                dateTo.removeAttribute('min'); // reset if cleared
            }
        });

        // Ensure correct restriction on page load if values already exist
        if (dateFrom.value) {
            dateTo.min = dateFrom.value;
        }
    });
</script>
@endsection
