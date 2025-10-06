<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use App\Models\Notification;
use App\Models\AuditLog;

class ProfileController extends Controller
{
    /**
     * Show the profile edit form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        // ✅ Handle photo upload
        if ($request->hasFile('photo')) {
            if (
                $user->photo &&
                Storage::disk('public')->exists($user->photo) &&
                basename($user->photo) !== 'default.jpg'
            ) {
                Storage::disk('public')->delete($user->photo);
            }

            $user->photo = $request->file('photo')->store('profile', 'public');
        }

        // ✅ Reset email verification if email changed
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // ✅ Log activity + audit
        AuditLog::create([
            'user_id'    => $user->id,
            'event'      => 'user_profile_update',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'context'    => [
                'name'  => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')),
                'email' => $user->email,
            ],
        ]);

        return Redirect::route('profile.edit')
            ->with('status', 'profile-updated')
            ->with('tab', $request->input('tab', 'info'));
    }

    /**
     * Update only the profile photo.
     */
    public function updatePhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $user = $request->user();

        if ($request->hasFile('photo')) {
            if (
                $user->photo &&
                Storage::disk('public')->exists($user->photo) &&
                basename($user->photo) !== 'default.jpg'
            ) {
                Storage::disk('public')->delete($user->photo);
            }

            $user->photo = $request->file('photo')->store('profile', 'public');
            $user->save();
        }

        AuditLog::create([
            'user_id'    => $user->id,
            'event'      => 'user_profile_update',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'context'    => [
                'name'  => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')),
                'email' => $user->email,
            ],
        ]);

        return redirect()->route('profile.edit')
            ->with('status', 'photo-updated')
            ->with('tab', 'info');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        if (
            $user->photo &&
            Storage::disk('public')->exists($user->photo) &&
            basename($user->photo) !== 'default.jpg'
        ) {
            Storage::disk('public')->delete($user->photo);
        }

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        // Maintain "tab" state on reload
        $request->session()->flash('tab', $request->input('tab', 'password'));

        $request->validate(
            [
                'current_password' => ['required', 'current_password'],
                'new_password'     => [
                    'required',
                    'string',
                    'min:6',
                    'max:20',
                    'regex:/[a-z]/',
                    'regex:/[0-9]/',
                    'regex:/[.,@$!%*?&]/',
                    'confirmed',
                ],
            ],
            [
                'current_password.required'       => 'Your current password is required.',
                'current_password.current_password' => 'The current password you entered is incorrect.',
                'new_password.regex'              => 'Password must contain at least one lowercase letter, one number, and one special character.',
                'new_password.confirmed'          => 'New password confirmation does not match.',
            ]
        );

        $user = Auth::user();
        $user->password = bcrypt($request->new_password);
        $user->save();

        AuditLog::create([
            'user_id'    => $user->id,
            'event'      => 'user_profile_update',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'context'    => [
                'name'  => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')),
                'email' => $user->email,
            ],
        ]);

        Notification::create([
            'user_id' => $user->id,
            'type'    => 'profile',
            'message' => "<span style=\"color:#23527c;font-weight:bold;\">{$user->first_name} {$user->last_name}</span> updated password.",
            'is_read' => false,
        ]);

        return back()
            ->with('success', 'Password updated successfully.')
            ->with('tab', 'password');
    }
}
