@extends('layouts.app')

@section('content')
<div 
    x-data="{
        step: 1,
        form: {
            role: '',
            permissions: {
                'roles.permission': false,
                'employees.index': false,
                'analytics.dashboard': false,
                'report.index': false,
                'report.add': false,
                'report.edit': false,
                'report.validate': false,
                'report.delete': false,
                'report.pdf': false,
                'analytics.index': false,
                'configuration.index': false,
                'user.dashboard': false // ✅ Include this since it's in your x-for
            }
        },
        get hasAnyPermission() {
            return Object.values(this.form.permissions).some(Boolean);
        }
    }"
>
    <a href="{{ route('roles.index') }}" class="flex items-center text-sm text-gray-500 hover:text-[#2d326b] mb-4">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        Roles
    </a>

    <form action="{{ route('roles.store') }}" method="POST">
        @csrf

        <div class="bg-white rounded-sm border border-gray-200 p-6 shadow-md space-y-5 transition-all duration-300 hover:shadow-xl hover:border-[#E5E7EB]">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-semibold text-[#2d326b]">Create New Role</h2>
                    <p class="text-sm text-gray-500">Complete the steps to define a new role in your organization.</p>
                </div>
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
                <table class="min-w-64 mb-8 text-sm border border-gray-200 shadow-sm">
                    <tbody class="bg-gray-100 text-[#2d326b]">
                        <tr>
                            <td class="text-left px-4 py-3 font-semibold">Role Title</td>
                            <td class="text-left px-4 py-3">
                                <input 
                                    type="text" 
                                    name="role" 
                                    placeholder="position"
                                    x-model="form.role"
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
                        <div class="space-x-4">
                            <button 
                                type="button" 
                                @click="Object.assign(form.permissions, {
                                    'roles.permission': true,
                                    'employees.index': true,
                                    'user.dashboard': true
                                })" 
                                class="text-xs text-[#323B76] hover:underline"
                            >
                                Enable all
                            </button>
                            <button 
                                type="button" 
                                @click="Object.assign(form.permissions, {
                                    'roles.permission': false,
                                    'employees.index': false,
                                    'user.dashboard': false
                                })" 
                                class="text-xs text-gray-500 hover:underline"
                            >
                                Disable all
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <template x-for="entry in Object.entries({
                            'user.dashboard': {
                                label: 'Dashboard',
                                desc: 'Can view the user dashboard.'
                            },
                            'roles.permission': {
                                label: 'Roles & Permission Management',
                                desc: 'Grants the ability to manage user roles and access rights.'
                            },
                            'employees.index': {
                                label: 'Employee Management',
                                desc: 'Manage employee records and account statuses.'
                            }
                        })" :key="entry[0]">
                            <div class="flex items-start justify-between border border-gray-200 bg-white rounded-lg p-4 h-full">
                                <div class="pr-2">
                                    <p class="text-sm font-medium text-[#2d326b]" x-text="entry[1].label"></p>
                                    <p class="text-xs text-gray-500 mt-1" x-text="entry[1].desc"></p>
                                </div>
                                <input type="hidden" :name="'permissions[' + entry[0] + ']'" :value="form.permissions[entry[0]] ? 1 : 0">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" x-model="form.permissions[entry[0]]" class="sr-only peer">
                                    <div class="relative w-11 h-6 bg-gray-200 rounded-full 
                                                after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white 
                                                after:rounded-full after:h-5 after:w-5 after:transition-all 
                                                peer-checked:after:translate-x-full peer-checked:bg-[#2D3A8C]">
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
                        <div class="space-x-4">
                            <button 
                                type="button" 
                                @click="Object.assign(form.permissions, {
                                    'analytics.dashboard': true,
                                    'report.index': true,
                                    'report.add': true,
                                    'report.edit': true,
                                    'report.validate': true,
                                    'report.pdf': true,
                                    'analytics.index': true,
                                    'configuration.index': true
                                })"
                                class="text-xs text-[#323B76] hover:underline"
                            >
                                Enable all
                            </button>
                            <button 
                                type="button" 
                                @click="Object.assign(form.permissions, {
                                    'analytics.dashboard': false,
                                    'report.index': false,
                                    'report.add': false,
                                    'report.edit': false,
                                    'report.validate': false,
                                    'report.pdf': false,
                                    'analytics.index': false,
                                    'configuration.index': false
                                })"
                                class="text-xs text-gray-500 hover:underline"
                            >
                                Disable all
                            </button>
                        </div>
                    </div>
                    <div class="grid md:grid-cols-3 gap-4">
                        <template x-for="entry in Object.entries({
                            'analytics.dashboard': {
                                label: 'Dashboard',
                                desc: 'Can view production KPIs and summaries.'
                            },
                            'report.index': {
                                label: 'View Reports',
                                desc: 'Access to view production reports.'
                            },
                            'report.add': {
                                label: 'Add Report',
                                desc: 'Can submit new production data.'
                            },
                            'report.edit': {
                                label: 'Edit Report',
                                desc: 'Modify submitted reports.'
                            },
                            'report.validate': {
                                label: 'Validate Report',
                                desc: 'Verify and lock reports.'
                            },
                            'analytics.index': {
                                label: 'View Analytics',
                                desc: 'Detailed analytical dashboard access.'
                            },
                            'configuration.index': {
                                label: 'Modify Configurations',
                                desc: 'Edit production formulas and standards.'
                            }
                        })" :key="entry[0]">
                            <div class="flex items-start justify-between border border-gray-200 bg-white rounded-lg p-4 h-full">
                                <div class="pr-2">
                                    <p class="text-sm font-medium text-[#2d326b]" x-text="entry[1].label"></p>
                                    <p class="text-xs text-gray-500 mt-1" x-text="entry[1].desc"></p>
                                </div>
                                <input type="hidden" :name="'permissions[' + entry[0] + ']'" :value="form.permissions[entry[0]] ? 1 : 0">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" x-model="form.permissions[entry[0]]" class="sr-only peer">
                                    <div class="relative w-11 h-6 bg-gray-200 rounded-full 
                                                after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white 
                                                after:rounded-full after:h-5 after:w-5 after:transition-all 
                                                peer-checked:after:translate-x-full peer-checked:bg-[#2D3A8C]">
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
