<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Models\Setting;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DashboardAdminController extends Controller
{
public function index()
{
    $totalUsers        = User::count();
    $totalRoles        = Role::count();
    $adminCount        = User::role('Admin')->count(); // optional
    $usersWithoutRole  = User::doesntHave('roles')->count();
    $rolesWithoutUsers = Role::doesntHave('users')->count();
    $recentUsers       = User::with('roles')->latest()->take(5)->get();

    return view('admin.dashboard', compact(
        'totalUsers',
        'totalRoles',
        'adminCount',
        'usersWithoutRole',
        'rolesWithoutUsers',
        'recentUsers'
    ));
}
public function setting()
{
    $settings = Setting::first();

    $backgroundBase64 = null;
    $logoBase64 = null;

    if ($settings && $settings->background_image && Storage::disk('public')->exists($settings->background_image)) {
        $backgroundContent = Storage::disk('public')->get($settings->background_image);
        $backgroundMime = Storage::disk('public')->mimeType($settings->background_image);
        $backgroundBase64 = 'data:' . $backgroundMime . ';base64,' . base64_encode($backgroundContent);
    }

    if ($settings && $settings->logo && Storage::disk('public')->exists($settings->logo)) {
        $logoContent = Storage::disk('public')->get($settings->logo);
        $logoMime = Storage::disk('public')->mimeType($settings->logo);
        $logoBase64 = 'data:' . $logoMime . ';base64,' . base64_encode($logoContent);
    }

    return view('setting.index', compact('settings', 'backgroundBase64', 'logoBase64'));
}


public function updateSetting(Request $request)
{
    $request->validate([
        'company_name'      => 'required|string|max:255',
        'logo'              => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
        'background_image'  => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
    ]);

    $setting = Setting::firstOrNew([]);
    $setting->company_name = $request->company_name;

    // --- Handle Logo Upload ---
    if ($request->hasFile('logo')) {
        // delete old logo if exists
        if ($setting->logo && Storage::disk('public')->exists($setting->logo)) {
            Storage::disk('public')->delete($setting->logo);
        }

        // this saves under storage/app/public/settings/logo/...
        $path = $request->file('logo')->store('settings/logo', 'public');
        $setting->logo = $path;
    }

    // --- Handle Background Upload ---
    if ($request->hasFile('background_image')) {
        if ($setting->background_image && Storage::disk('public')->exists($setting->background_image)) {
            Storage::disk('public')->delete($setting->background_image);
        }

        $path = $request->file('background_image')->store('settings/backgrounds', 'public');
        $setting->background_image = $path;
    }

    $setting->save();

    return redirect()
        ->route('setting.index')
        ->with('success', 'Settings updated successfully!');
}




}
