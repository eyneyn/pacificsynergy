<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait RedirectsUsers
{
    protected function redirectTo(): string
    {
        $user = Auth::user();

        $redirectMap = [
            'user.dashboard' => route('admin.dashboard'),
            'report.index'   => route('report.index'),
            'analytics.index'=> route('analytics.index'),
        ];

        foreach ($redirectMap as $permission => $route) {
            if ($user->can($permission)) {
                return $route;
            }
        }

        return '/profile';
    }
}
