<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the employees.
     */
    public function index()
    {
        $users = User::with('roles')->latest()->get();
        return view('employees.index', compact('users'));
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

        // Handle photo upload or set default photo
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos', 'public');
        } else {
            $photoPath = 'photos/default.jpg';
            if (!Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->put($photoPath, file_get_contents(public_path('img/default.jpg')));
            }
        }

        // Create the user
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'employee_number' => $request->employee_number,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'department' => $request->department,
            'photo' => $photoPath,
        ]);

        // Assign role using Spatie
        $user->assignRole($request->role);

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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'employee_number' => 'required|string|max:255|unique:users,employee_number,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'department' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'role' => 'required|string|exists:roles,name',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $user->photo = $request->file('photo')->store('photos', 'public');
        }

        // Update user fields
        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        $user->email = $validated['email'];
        $user->employee_number = $validated['employee_number'];
        $user->department = $validated['department'];
        $user->phone_number = $validated['phone_number'];

        // Update password if provided
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // Sync roles
        $user->syncRoles([$validated['role']]);

        return redirect()->route('employees.view', $user->id)->with('success', 'User updated successfully.');
    }
}
