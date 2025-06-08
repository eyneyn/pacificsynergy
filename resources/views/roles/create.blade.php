@extends('layouts.app')

@section('content')
<div 
    x-data="{
        step: 1,
        form: {
            title: '',
            description: '',
            job_level: '',
            role_type: '',
            salary: '',
            benefits: '',
            teams: [],
            permissions: {}
        },
        get hasAnyPermission() {
            return Object.values(this.form.permissions).some(Boolean);
        }
    }"
>
    <!-- Back Button -->
    <a href="{{ route('roles.index') }}" class="flex items-center text-sm text-gray-500 hover:text-[#2d326b] mb-4">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        Roles
    </a>

    <form action="{{ route('roles.store') }}" method="POST">
        @csrf

        <!-- Card Container -->
        <div class="bg-white rounded-sm border border-gray-200 p-6 shadow-md space-y-5 transition-all duration-300 hover:shadow-xl hover:border-[#E5E7EB]">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-semibold text-[#2d326b]">Create New Role</h2>
                    <p class="text-sm text-gray-500">Complete the steps to define a new role in your organization.</p>
                </div>
                <!-- Save Button -->
                <button 
                    type="submit"
                    :disabled="!hasAnyPermission"
                    class="inline-flex items-center gap-2 px-4 py-2 border text-sm font-medium rounded-md transition
                           text-white bg-[#323B76] border-[#323B76]
                           hover:bg-[#444d90]
                           disabled:cursor-not-allowed disabled:opacity-50 disabled:bg-gray-400 disabled:border-gray-400"
                >
                    Save Role
                </button>
            </div>

            <div class="space-y-5">
                <!-- Role Title Form -->
                <table class="min-w-64 mb-8 text-sm border border-gray-200 shadow-sm">
                    <tbody class="bg-gray-100 text-[#2d326b]">
                        <tr>
                            <td class="text-left px-4 py-3 font-semibold">Role Title</td>
                            <td class="text-left px-4 py-3">
                                <input 
                                    type="text" 
                                    name="role" 
                                    placeholder="position"
                                    class="w-full border border-gray-300 placeholder-gray-400 rounded px-3 py-1 text-sm text-center"
                                >
                                @error('role')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- ADMIN ACCESS -->
                <h4 class="text-lg font-semibold text-[#2d326b]">Set Permissions</h4>
                <div class="bg-gray-100 border border-gray-200 rounded-xl p-5 shadow-sm">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-sm font-semibold text-[#2d326b]">Admin Access</h4>
                        <!-- Enable All Button (Admin) -->
                        <button 
                            type="button" 
                            @click="
                                form.permissions = {
                                    'roles.permission': false,
                                    'employees.index': false,
                                }
                            " 
                            class="text-xs text-[#323B76] hover:underline"
                        >
                            Enable all
                        </button>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Admin Permissions List -->
                        <template x-for="[key, perm] in Object.entries({
                            'roles.permission': {
                                label: 'Roles & Permission Management',
                                desc: 'Grants the ability to create, edit, and delete user roles, as well as assign or modify access permissions for each role within the system.'
                            },
                            'employees.index': {
                                label: 'Employee Management',
                                desc: 'Allows full control over employee records, including adding new users, editing details, updating positions, and removing users from the system.'
                            }
                        })" :key="key">
                            <div class="flex items-start justify-between border border-gray-200 bg-white rounded-lg p-4 h-full">
                                <div class="pr-2">
                                    <p class="text-sm font-medium text-[#2d326b]" x-text="perm.label"></p>
                                    <p class="text-xs text-gray-500 mt-1" x-text="perm.desc"></p>
                                </div>
                                <input type="hidden" :name="'permissions[' + key + ']'" :value="form.permissions[key] ? 1 : 0">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" x-model="form.permissions[key]" class="sr-only peer">
                                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full 
                                                peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full 
                                                after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white 
                                                after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#2D3A8C]">
                                    </div>
                                </label>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- PRODUCTION ACCESS -->
                <div class="bg-gray-100 border border-gray-200 rounded-xl p-5 shadow-sm">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-sm font-semibold text-[#2d326b]">Production Access</h4>
                        <!-- Enable All Button (Production) -->
                        <button 
                            type="button" 
                            @click="
                                form.permissions = {
                                    'analytics.dashboard': false,
                                    'report.index': false,
                                    'report.add': false,
                                    'report.edit': false,
                                    'report.validate': false,
                                    'report.delete': false,
                                    'report.pdf': false,
                                    'analytics.index': false,
                                    'configuration.index': false,
                                }
                            " 
                            class="text-xs text-[#323B76] hover:underline"
                        >
                            Enable all
                        </button>
                    </div>
                    <div class="grid md:grid-cols-3 gap-4">
                        <!-- Production Permissions List -->
                        <template x-for="[key, perm] in Object.entries({
                            'analytics.dashboard': {
                                label: 'Dashboard',
                                desc: 'Can view the summary of production reports.'
                            },
                            'report.index': {
                                label: 'Can view report',
                                desc: 'This enables general access to view the production report.'
                            },
                            'report.add': {
                                label: 'Can add report',
                                desc: 'This enables general access to add the production report.'
                            },
                            'report.edit': {
                                label: 'Can edit report',
                                desc: 'This enables general access to edit the production report.'
                            },
                            'report.validate': {
                                label: 'Can validate report',
                                desc: 'Allows the user to validate and lock the report.'
                            },
                            'report.delete': {
                                label: 'Can delete report',
                                desc: 'This enables general access to delete the production report.'
                            },
                            'analytics.index': {
                                label: 'Can view the analytics & report',
                                desc: 'Enables the user to view the analytics and report.'
                            },
                            'configuration.index': {
                                label: 'Can modify the production metrics',
                                desc: 'This enables general access to modify the production standard and configuration.'
                            }
                        })" :key="key">
                            <div class="flex items-start justify-between border border-gray-200 bg-white rounded-lg p-4 h-full">
                                <div class="pr-2">
                                    <p class="text-sm font-medium text-[#2d326b]" x-text="perm.label"></p>
                                    <p class="text-xs text-gray-500 mt-1" x-text="perm.desc"></p>
                                </div>
                                <input type="hidden" :name="'permissions[' + key + ']'" :value="form.permissions[key] ? 1 : 0">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" x-model="form.permissions[key]" class="sr-only peer">
                                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full 
                                                peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full 
                                                after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white 
                                                after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#2D3A8C]">
                                    </div>
                                </label>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
