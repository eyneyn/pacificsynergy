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
use Illuminate\Support\Facades\Password;
use App\Notifications\SetPasswordNotification;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Mail;

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
                    ->orWhere('email',      'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    // search against the computed position_name
                    ->orWhere(DB::raw('(SELECT roles.name 
                            FROM model_has_roles 
                            JOIN roles ON roles.id = model_has_roles.role_id 
                            WHERE model_has_roles.model_id = users.id 
                            AND model_has_roles.model_type = "' . addslashes(User::class) . '" 
                            LIMIT 1)'), 'like', "%{$search}%");
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
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'required|string|max:255',
            'employee_number' => 'required|string|max:255|unique:users,employee_number',
            'email'           => 'required|email|unique:users,email',
            'department'      => 'required|string',
            'phone_number'    => 'nullable|string',
            'role'            => 'required|string|exists:roles,name',
            'photo'           => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Default photo path
        $photoPath = 'profile/default.jpg';
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('profile', 'public');
        } else {
            if (!Storage::disk('public')->exists($photoPath)) {
                if (File::exists(public_path('img/default.jpg'))) {
                    Storage::disk('public')->put(
                        $photoPath,
                        File::get(public_path('img/default.jpg'))
                    );
                }
            }
        }

        // Generate random temporary password
        $tempPassword = Str::random(12);

        // Create user
        $user = User::create([
            'first_name'      => $request->first_name,
            'last_name'       => $request->last_name,
            'employee_number' => $request->employee_number,
            'email'           => $request->email,
            'password'        => Hash::make($tempPassword), // placeholder
            'department'      => $request->department,
            'phone_number'    => $request->phone_number,
            'photo'           => $photoPath,
            'status'          => 'Active',
        ]);

        $user->assignRole($request->role);

        // Notifications + audit log
        Notification::employeeEvent('created', $user);

        AuditLog::create([
            'user_id'    => Auth::id(),
            'event'      => 'employee_add',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'context'    => [
                'employee' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? 'Unknown Employee')),
                'status'   => $user->status,
            ],
        ]);

        return redirect()->route('employees.view', $user->id)
            ->with('success', 'User created successfully. You can now send them a login link.');
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
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'required|string|max:255',
            'employee_number' => 'required|string|max:255|unique:users,employee_number,' . $user->id,
            'email'           => 'required|email|unique:users,email,' . $user->id,
            'password'        => 'nullable|string|min:8',
            'department'      => 'required|string',
            'phone_number'    => 'nullable|string',
            'role'            => 'required|string|exists:roles,name',
            'photo'           => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status'          => 'required|in:Active,Locked',
        ]);

        // Handle photo
        if ($request->hasFile('photo')) {
            if ($user->photo && Storage::disk('public')->exists($user->photo) && basename($user->photo) !== 'default.jpg') {
                Storage::disk('public')->delete($user->photo);
            }
            $user->photo = $request->file('photo')->store('profile', 'public');
        }

        // Update fields
        $user->fill([
            'first_name'      => $validated['first_name'],
            'last_name'       => $validated['last_name'],
            'email'           => $validated['email'],
            'employee_number' => $validated['employee_number'],
            'department'      => $validated['department'],
            'phone_number'    => $validated['phone_number'],
            'status'          => $validated['status'],
        ]);

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
        $user->syncRoles([$validated['role']]);

        // 🚨 Force logout if locked
        if ($user->status === 'Locked') {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        Notification::employeeEvent('updated', $user);

        AuditLog::create([
            'user_id'    => Auth::id(),
            'event'      => 'employee_update',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'context'    => [
                'employee' => $user->first_name . ' ' . $user->last_name,
                'status'   => $user->status,
            ],
        ]);

        return redirect()->route('employees.view', $user->id)
            ->with('user_updated', 'User profile has been updated.');
    }
    public function sendLoginLink(User $user)
    {
        $token = Password::createToken($user);

        // ✅ Use our custom notification
        $user->notify(new SetPasswordNotification($token));

        return back()->with('login_link_sent', 'Login link sent to ' . $user->email);
    }
    public function reset2fa(User $user)
    {
        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();

        // Reset 2FA
        $user->google2fa_secret = $secret;
        $user->two_factor_enabled = false; // must re-verify
        $user->save();

        // Generate QR
        $qrData = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );
        $renderer = new ImageRenderer(new RendererStyle(200), new SvgImageBackEnd());
        $writer = new Writer($renderer);
        $qrSvg = $writer->writeString($qrData);

        // Send QR email
        Mail::send('emails.2fa-reset', [
            'user' => $user,
            'qrSvg' => $qrSvg
        ], function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Your Two-Factor Authentication has been reset');
        });

        return back()->with('two_fa_reset', '2FA has been reset and QR code sent to ' . $user->email);
    }
}
