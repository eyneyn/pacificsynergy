<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Notification;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    /**
     * Display a listing of the roles.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));

        $roles = Role::with(['permissions', 'users'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->withCount('users')
            ->orderBy('name')
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

        Notification::roleEvent('created', $role->name, $role->id);

        AuditLog::create([
            'user_id'    => Auth::id(),
            'event'      => 'role_add',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'context'    => [
                'role'  => $role->name ?? 'Unknown Role',
            ],
        ]);

        return redirect()->route('roles.edit', $role->id)
            ->with('success', 'Role created successfully.');
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

        Notification::roleEvent('updated', $role->name, $role->id);

        AuditLog::create([
            'user_id'    => Auth::id(),
            'event'      => 'role_update',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'context'    => [
                'role'  => $role->name ?? 'Unknown Role',
            ],
        ]);

        return redirect()->route('roles.edit', $role->id)
            ->with('success', 'Role updated successfully.');
    }
}
