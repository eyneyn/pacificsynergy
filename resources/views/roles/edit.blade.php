@extends('layouts.app')

@section('content')
<div x-data="roleEditor()" x-init="init(@js($role->permissions->pluck('name')))">

    <!-- Back Button -->
    <a href="{{ route('roles.index') }}" class="flex items-center text-sm text-gray-500 hover:text-[#2d326b] mb-4">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        Back to Roles
    </a>

    <form action="{{ route('roles.update', $role->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Card Container -->
        <div class="bg-white rounded-sm border border-gray-200 p-6 shadow-md space-y-5 hover:shadow-xl">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-semibold text-[#2d326b]">Edit Role</h2>
                    <p class="text-sm text-gray-500">Update the role name and associated permissions.</p>
                </div>
                <button type="submit"
                    :disabled="!hasAnyPermission"
                    class="inline-flex items-center gap-2 px-4 py-2 text-white text-sm font-medium rounded-md border bg-[#323B76] border-[#323B76] hover:bg-[#444d90] disabled:cursor-not-allowed disabled:opacity-50 disabled:bg-gray-400 disabled:border-gray-400">
                    Update Role
                </button>
            </div>

            <!-- Role Name -->
            <table class="min-w-64 mb-8 text-sm border border-gray-200 shadow-sm">
                <tbody class="bg-gray-100 text-[#2d326b]">
                    <tr>
                        <td class="text-left px-4 py-3 font-semibold">Role Title</td>
                        <td class="text-left px-4 py-3">
                            <input type="text" name="role" value="{{ old('role', $role->name) }}" placeholder="position"
                                class="w-full border border-gray-300 placeholder-gray-400 rounded px-3 py-1 text-sm text-center">
                            @error('role')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Success Message -->
            @if (session('success'))
                <div class="mb-4 p-2 text-sm rounded bg-green-100 text-green-800 border border-green-300 text-center max-w-sm mx-auto">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Permission Sections -->
            @include('roles.partials.permission-section', [
                'title' => 'Admin Access',
                'permissions' => [
                    'user.dashboard' => 'Dashboard',
                    'roles.permission' => 'Roles & Permission Management',
                    'employees.index' => 'Employee Management',
                ]
            ])

            @include('roles.partials.permission-section', [
                'title' => 'Production Access',
                'permissions' => [
                    'analytics.dashboard' => 'Dashboard',
                    'report.index' => 'View Reports',
                    'report.add' => 'Add Reports',
                    'report.edit' => 'Edit Reports',
                    'report.validate' => 'Validate Reports',
                    'analytics.index' => 'Analytics & Reporting',
                    'configuration.index' => 'Modify Metrics',
                ]
            ])
        </div>
    </form>
</div>

<!-- Alpine.js Role Editor Logic -->
<script>
    // Alpine.js component for role editing
    function roleEditor() {
        return {
            form: {
                permissions: {},
            },
            // Initialize permissions from backend
            init(existingPermissions) {
                existingPermissions.forEach(p => {
                    this.form.permissions[p] = true;
                });
            },
            // Check if any permission is selected
            get hasAnyPermission() {
                return Object.values(this.form.permissions).some(Boolean);
            }
        }
    }
</script>
@endsection
