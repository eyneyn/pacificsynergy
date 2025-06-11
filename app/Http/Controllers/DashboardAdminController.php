<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;

class DashboardAdminController extends Controller
{
public function index()
{
    $totalUsers = User::count();
    $totalRoles = Role::count();
    $adminCount = User::role('Admin')->count(); // Ensure roles are attached using Spatie
    $recentUsers = User::with('roles')->latest()->take(5)->get();

    return view('admin.dashboard', compact('totalUsers', 'totalRoles', 'adminCount', 'recentUsers'));
}
}
