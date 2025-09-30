<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\Notification;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the employees.
     */
    public function index(Request $request)
    {
        $role       = $request->query('role');
        $search     = trim((string) $request->query('search', ''));
        $sort       = $request->query('sort', 'created_at');
        $direction  = $request->query('direction', 'desc');

        $users = User::query()
            // add a subselect for first role name, used for sorting "position"
            ->addSelect([
                'position_name' => DB::table('model_has_roles')
                    ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                    ->whereColumn('model_has_roles.model_id', 'users.id')
                    ->where('model_has_roles.model_type', User::class)
                    ->select('roles.name')
                    ->limit(1)
            ])
            ->with('roles')
            ->when($role, fn ($q) => $q->role($role)) // Spatie scope
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name',  'like', "%{$search}%")
                    ->orWhere('email',      'like', "%{$search}%");
                });
            });

        // Sorting
        switch ($sort) {
            case 'full_name':
                $users->orderBy('last_name', $direction)->orderBy('first_name', $direction);
                break;
            case 'email':
                $users->orderBy('email', $direction);
                break;
            case 'position':
                $users->orderBy('position_name', $direction);
                break;
            case 'department':
                $users->orderBy('department', $direction);
                break;
            case 'created_at':
            default:
                $users->orderBy('created_at', $direction);
                break;
        }

        $users = $users->get();

        return view('employees.index', [
            'users' => $users,
            'role' => $role,
            'search' => $search,
            'currentSort' => $sort,
            'currentDirection' => $direction,
        ]);
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create()
    {
        $roleOptions = Role::pluck('name', 'name');
        return view('employees.create', compact('roleOptions'));
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'employee_number' => 'required|string|max:255|unique:users,employee_number',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'department' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'role' => 'required|string|exists:roles,name',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Default photo path (storage/app/public/profile/default.jpg)
        $photoPath = 'profile/default.jpg';

        // Handle uploaded photo
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('profile', 'public');
        } else {
            // Ensure default.jpg exists in storage/app/public/profile
            if (!Storage::disk('public')->exists($photoPath)) {
                if (File::exists(public_path('img/default.jpg'))) {
                    Storage::disk('public')->put(
                        $photoPath,
                        File::get(public_path('img/default.jpg'))
                    );
                }
            }
        }

        $user = User::create([
            'first_name'       => $request->first_name,
            'last_name'        => $request->last_name,
            'employee_number'  => $request->employee_number,
            'email'            => $request->email,
            'password'         => Hash::make($request->password),
            'department'       => $request->department,
            'phone_number'     => $request->phone_number,
            'photo'            => $photoPath, // e.g. "profile/uuid.png"
            'status'           => 'Active',   // ✅ default to Active
        ]);

        $user->assignRole($request->role);

        // 🔔 Notification
        Notification::employeeEvent('created', $user);

        // 📝 Audit Log
        AuditLog::create([
            'user_id'    => Auth::id(),
            'event'      => 'employee_add',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'context'    => [
                'employee' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? 'Unknown Employee')),
                'status'   => $user->status, // optional: record status in logs
            ],
        ]);

        return redirect()->route('employees.index')->with('success', 'User created successfully.');
    }



    /**
     * Display the specified employee.
     */
    public function view(User $user)
    {
        return view('employees.view', compact('user'));
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(User $user)
    {
        $roleOptions = Role::pluck('name')->mapWithKeys(fn ($name) => [$name => ucfirst($name)])->toArray();
        return view('employees.edit', compact('user', 'roleOptions'));
    }

    /**
     * Update the specified employee in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name'       => 'required|string|max:255',
            'last_name'        => 'required|string|max:255',
            'employee_number'  => 'required|string|max:255|unique:users,employee_number,' . $user->id,
            'email'            => 'required|email|unique:users,email,' . $user->id,
            'password'         => 'nullable|string|min:8',
            'department'       => 'nullable|string',
            'phone_number'     => 'nullable|string',
            'role'             => 'required|string|exists:roles,name',
            'photo'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status'           => 'required|in:Active,Locked',
        ]);

        // Handle photo
        if ($request->hasFile('photo')) {
            if ($user->photo && Storage::disk('public')->exists($user->photo) && basename($user->photo) !== 'default.jpg') {
                Storage::disk('public')->delete($user->photo);
            }
            $user->photo = $request->file('photo')->store('profile', 'public');
        }

        // Update fields
        $user->first_name      = $validated['first_name'];
        $user->last_name       = $validated['last_name'];
        $user->email           = $validated['email'];
        $user->employee_number = $validated['employee_number'];
        $user->department      = $validated['department'];
        $user->phone_number    = $validated['phone_number'];
        $user->status          = $validated['status'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
        $user->syncRoles([$validated['role']]);

        // 🚨 Force logout if account is locked
        if ($user->status === 'Locked') {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        // 🔔 Notification
        Notification::employeeEvent('updated', $user);

        // 📝 Audit Log
        AuditLog::create([
            'user_id'    => Auth::id(),
            'event'      => 'employee_update',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'context'    => [
                'employee' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? 'Unknown Employee')),
                'status'   => $user->status,
            ],
        ]);

        return redirect()->route('employees.view', $user->id)->with('success', 'User updated successfully.');
    }


}
