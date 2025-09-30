@extends('layouts.app')
@section('title', content: 'Roles')
@section('content')
<div class="container mx-auto px-4">
        <!-- Title -->
        <div class="flex-1 text-center">
            <h2 class="text-2xl m-4 font-bold text-[#23527c]">New Role</h2>
        </div>

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

    <form action="{{ route('roles.store') }}" method="POST">
        @csrf

            <div class="mb-6 flex items-center justify-between">
                    <a href="{{ route('roles.index') }}" class="inline-flex items-center px-3 py-2 bg-[#5a9fd4] hover:bg-[#4a8bc2] text-white text-sm font-medium transition-colors duration-200 border border-[#4590ca] hover:border-[#4a8bc2]">
                        <x-icons-back class="w-2 h-2 text-white" />
                        Back
                    </a>
                <button 
                    type="submit"
                    :disabled="!hasAnyPermission"
                    class="inline-flex items-center justify-center gap-1 bg-[#5bb75b] border border-[#43a143] text-white px-3 py-2 hover:bg-[#42a542] text-sm
                           disabled:cursor-not-allowed disabled:opacity-50 disabled:bg-gray-400 disabled:border-gray-400">
                    <x-icons-save class="w-2 h-2 text-white" />
                    Save Role
                </button>
            </div>

            <div class="space-y-5">
                <table class="min-w-64 mb-8 text-sm border border-[#E5E7EB] shadow-sm">
                    <tbody class="bg-[#e2f2ff] text-[#23527c]">
                        <tr>
                            <td class="text-left px-4 py-3 font-semibold">Role Title <span class="text-red-500">*</span></td>
                            <td class="text-left px-4 py-3">
                                <input 
                                    type="text" 
                                    name="role" 
                                    placeholder="Position"
                                    x-model="form.role"
                                    class="w-full border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none placeholder-gray-400 px-2 py-1 text-sm"
                                >
                                @error('role')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </td>
                        </tr>
                    </tbody>
                </table>

@include('roles.partials.permission-section', [
    'title' => 'Admin Access',
    'permissions' => [
        'user.dashboard' => [
            'label' => 'Dashboard',
            'desc'  => 'Can view the user dashboard.',
        ],
        'roles.permission' => [
            'label' => 'Roles & Permission Management',
            'desc'  => 'Manage user roles and access rights.',
        ],
        'employees.index' => [
            'label' => 'User Management',
            'desc'  => 'Manage user accounts.',
        ],
    ],
])

@include('roles.partials.permission-section', [
    'title' => 'Production Access',
    'permissions' => [
        'analytics.dashboard' => [
            'label' => 'Dashboard',
            'desc'  => 'Overview of analytics KPIs.',
        ],
        'report.index' => [
            'label' => 'View Reports',
            'desc'  => 'Read-only access to production reports.',
        ],
        'report.add' => [
            'label' => 'Add Reports',
            'desc'  => 'Create new production reports.',
        ],
        'report.edit' => [
            'label' => 'Edit Reports',
            'desc'  => 'Modify existing production reports.',
        ],
        'report.validate' => [
            'label' => 'Validate Reports',
            'desc'  => 'Approve/validate submitted reports.',
        ],
        'analytics.index' => [
            'label' => 'Analytics & Reporting',
            'desc'  => 'View analytics dashboards and charts.',
        ],
        'configuration.index' => [
            'label' => 'Modify Metrics',
            'desc'  => 'Update standard metrics and references.',
        ],
    ],
])
            </div>
        </div>
    </form>
</div>
@endsection
