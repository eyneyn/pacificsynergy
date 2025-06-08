<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of the roles.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $roles = Role::with(['permissions', 'users'])
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhereHas('permissions', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->withCount('users')
            ->get();

        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        return view('roles.create');
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'role' => 'required|string|unique:roles,name',
            'permissions' => 'nullable|array',
        ]);

        $selectedPermissions = collect($request->permissions)
            ->filter(fn($value) => $value)
            ->keys();

        if ($selectedPermissions->isEmpty()) {
            return back()->withInput()->withErrors([
                'permissions' => 'You must select at least one permission.',
            ]);
        }

        $role = Role::create(['name' => $request->role]);
        $role->syncPermissions($selectedPermissions->toArray());

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        $role->load('permissions'); // Ensure permissions are loaded
        return view('roles.edit', compact('role'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'role' => [
                'required',
                Rule::unique('roles', 'name')->ignore($role->id)->where(function ($query) {
                    return $query->where('guard_name', config('auth.defaults.guard'));
                }),
            ],
            'permissions' => 'nullable|array',
        ]);

        $selectedPermissions = collect($request->permissions)
            ->filter(fn($value) => $value)
            ->keys()
            ->toArray();

        if (empty($selectedPermissions)) {
            return back()->withInput()->withErrors([
                'permissions' => 'You must select at least one permission.',
            ]);
        }

        $role->update(['name' => $request->role]);
        $role->syncPermissions($selectedPermissions);

        return redirect()->route('roles.edit', $role->id)->with('success', 'Role updated successfully.');
    }
}
