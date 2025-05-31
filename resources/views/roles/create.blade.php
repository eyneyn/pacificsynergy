@extends('layouts.app')

@section('content')
<div x-data="{ step: 1, form: { title: '', description: '', job_level: '', role_type: '', salary: '', benefits: '', teams: [], permissions: {} } }" >

<!-- Back Button -->
<a href="" class="flex items-center text-sm text-gray-500 hover:text-[#2d326b] mb-4">
    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
    </svg>
    Roles
</a>

<form action="" method="POST">
    @csrf

<!-- Card Container -->
<div class="bg-white rounded-sm border border-gray-200 p-6 shadow-md space-y-5 transition-all duration-300 hover:shadow-xl hover:border-[#E5E7EB]">
    
    
<div class="mb-6 flex items-center justify-between">
    <!-- Heading -->
    <div>
        <h2 class="text-2xl font-semibold text-[#2d326b]">Create New Role</h2>
        <p class="text-sm text-gray-500">Complete the steps to define a new role in your organization.</p>
    </div>
    <button type="submit"
        class="inline-flex items-center gap-2 px-4 py-2 bg-[#323B76] hover:bg-[#444d90] border border-[#323B76] text-white text-sm font-medium rounded-md">
        Save Role
    </button>
</div>

<div class="space-y-5">

        <!-- Basic Production Form -->
        <table class="min-w-64 mb-8 text-sm border border-gray-200 shadow-sm">
            <tbody class="bg-gray-100 text-[#2d326b]">
                <tr>
                    <td class="text-left px-4 py-3 font-semibold">Role Title</td>
                    <td class="text-left px-4 py-3">
                        <input type="text" name="role" placeholder="position" class="w-full border border-gray-300 placeholder-gray-400 rounded px-3 py-1 text-sm text-center">
                    </td>
                </tr>
            </tbody>
        </table>

        <h4 class="text-lg font-semibold text-[#2d326b]">Set Permissions</h4>

<!-- ADMIN ACCESS -->
<div class="bg-gray-100 border border-gray-200 rounded-xl p-5 shadow-sm">
    <div class="flex justify-between items-center mb-4">
        <h4 class="text-sm font-semibold text-[#2d326b]">Admin Access</h4>
        <button type="button" @click="
            form.permissions.dashboard_access = true;
            form.permissions.roles_permission = true;
            form.permissions.employee_management = true;
        " class="text-xs text-[#323B76] hover:underline">Enable all</button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <template x-for="(perm, key) in {
            dashboard_access: {
                label: 'Dashboard Access',
                desc: 'Provides access to the system’s main dashboard, where summaries are displayed for administrative monitoring'
            },
            roles_permission: {
                label: 'Roles & Permission Management',
                desc: 'Grants the ability to create, edit, and delete user roles, as well as assign or modify access permissions for each role within the system.'
            },
            employee_management: {
                label: 'Employee Management',
                desc: 'Allows full control over employee records, including adding new users, editing details, updating positions, and removing users from the system.'
            }
        }" :key="key">
            <div class="flex items-start justify-between border border-gray-200 bg-white rounded-lg p-4 h-full">
                <div class="pr-2">
                    <!-- Updated label color -->
                    <p class="text-sm font-medium text-[#2d326b]" x-text="perm.label"></p>
                    <p class="text-xs text-gray-500 mt-1" x-text="perm.desc"></p>
                </div>
                <!-- Toggle Switch -->
                <label class="inline-flex items-center ml-3 cursor-pointer mt-1">
                    <input
                        type="checkbox"
                        x-model="form.permissions[key]"
                        class="sr-only peer"
                    >
                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-[#35408e] relative transition duration-200">
                        <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform transform peer-checked:translate-x-full"></div>
                    </div>
                </label>
            </div>
        </template>
    </div>
</div>





        <!-- MANAGER ACCESS -->
        <div class="bg-[#f9fafb] border border-gray-200 rounded-xl p-5 shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h4 class="text-sm font-semibold text-[#2d326b]">Manager Access</h4>
                <button type="button" @click="
                    form.permissions.new_hires = true;
                    form.permissions.employee_data = true;
                    form.permissions.view_employees = true;
                    form.permissions.edit_any_roles = true;
                " class="text-xs text-[#323B76] hover:underline">Enable all</button>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <template x-for="(perm, key) in {
                    new_hires: {
                        label: 'Access to new hires',
                        desc: 'Can create job posting and manage new hires'
                    },
                    employee_data: {
                        label: 'Can add or delete employee',
                        desc: 'This enables general access to employee information'
                    },
                    view_employees: {
                        label: 'Can see information about other employees',
                        desc: 'Enables view-only access to profiles. No edit rights.'
                    },
                    edit_any_roles: {
                        label: 'Can add/edit roles',
                        desc: 'This enables general access to creating or modifying roles'
                    }
                }" :key="key">
                    <div class="flex items-start justify-between border border-gray-200 bg-white rounded-lg p-4">
                        <div>
                            <p class="text-sm font-medium text-gray-900" x-text="perm.label"></p>
                            <p class="text-xs text-gray-500 mt-1" x-text="perm.desc"></p>
                        </div>
                        <label class="inline-flex items-center ml-3 cursor-pointer mt-1">
                            <input type="checkbox" :checked="form.permissions[key]" @change="form.permissions[key] = !form.permissions[key]" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-[#35408e] relative transition duration-200">
                                <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform transform peer-checked:translate-x-full"></div>
                            </div>
                        </label>
                    </div>
                </template>
            </div>
        </div>
    </div>





{{-- 
    <!-- Step Navigation -->
    <div class="flex border-b mb-6 space-x-6">
        <template x-for="(label, index) in ['Basic Info', 'Set Permissions', 'Review Details']">
            <div :class="{'text-[#2d326b] font-bold border-b-2 border-[#2d326b]': step === index + 1, 'text-gray-400': step !== index + 1}" class="pb-2 cursor-pointer" @click="step = index + 1">
                <span x-text="label"></span>
            </div>
        </template>
    </div>

    <!-- Step Content -->
    <div class="space-y-6">
        <!-- Step 1 -->
        <div x-show="step === 1" x-cloak>
            @include('roles.partials.step-basic-info')
        </div>

        <!-- Step 2 -->
        <div x-show="step === 2" x-cloak>
            @include('roles.partials.step-permissions')
        </div>

        <!-- Step 3 -->
        <div x-show="step === 3" x-cloak>
            @include('roles.partials.step-review')
        </div>
    </div>

    <!-- Navigation Buttons -->
    <div class="mt-6 flex justify-between">
        <button @click="step = Math.max(1, step - 1)" x-show="step > 1" class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200">Back</button>
        <button @click="step === 3 ? $el.closest('form')?.submit() : step++" class="px-4 py-2 text-sm text-white bg-[#323B76] hover:bg-[#444d90] rounded-md">
            <span x-text="step === 3 ? 'Create Role' : 'Continue'"></span>
        </button>
    </div> --}}
</div>
@endsection
