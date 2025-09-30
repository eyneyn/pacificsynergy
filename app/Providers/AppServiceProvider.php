<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;

use App\Models\ProductionReport;
use App\Models\Status;
use App\Models\ProductionReportHistory;
use App\Models\ProductionIssues;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\AuditLog;

use App\Observers\ProductionReportObserver;
use App\Observers\ProductionIssuesObserver;
use App\Observers\StatusObserver;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\PasswordReset;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ✅ Allow Super Admin to bypass all `@can()` checks
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Admin') ? true : null;
        });

        // ✅ Notifications in navigation
        View::composer('layouts.navigation', function ($view) {
            $user = Auth::user();

            $notifications = Notification::visibleTo($user)
                ->latest()
                ->take(20)
                ->get();

            $unreadCount = Notification::visibleTo($user)
                ->where('is_read', false)
                ->count();

            $now = now()->timezone(config('app.timezone', 'UTC'));

            // Sort by newest first
            $sorted = collect($notifications)->sortByDesc('created_at')->values();

            // Strict time-based grouping
            $new = $sorted->filter(fn($n) => $n->created_at && $n->created_at->diffInMinutes($now) <= 120);
            $today = $sorted->filter(fn($n) => $n->created_at && $n->created_at->diffInMinutes($now) > 120 && $n->created_at->diffInMinutes($now) < 1440);
            $earlier = $sorted->filter(fn($n) => $n->created_at && $n->created_at->diffInMinutes($now) >= 1440);

            $view->with([
                'newNotifications'     => $new,
                'todayNotifications'   => $today,
                'earlierNotifications' => $earlier,
                'unreadCount'          => $unreadCount,
                'filter'               => 'all',
            ]);
        });

        // ✅ Global settings
        View::composer('*', function ($view) {
            $view->with('settings', \App\Models\Setting::first());
        });

        // ✅ Register observers
        ProductionReport::observe(ProductionReportObserver::class);
        ProductionIssues::observe(ProductionIssuesObserver::class);
        Status::observe(StatusObserver::class);

        // ✅ Audit Logs for auth events
        Event::listen(Login::class, function ($event) {
            $req = request();
            AuditLog::create([
                'user_id'    => $event->user?->id,
                'event'      => 'login',
                'ip_address' => $req?->ip(),
                'user_agent' => $req?->userAgent(),
                'context'    => ['guard' => $event->guard ?? null],
            ]);
        });

        Event::listen(Logout::class, function ($event) {
            $req = request();
            AuditLog::create([
                'user_id'    => $event->user?->id,
                'event'      => 'logout',
                'ip_address' => $req?->ip(),
                'user_agent' => $req?->userAgent(),
                'context'    => ['guard' => $event->guard ?? null],
            ]);
        });

        Event::listen(Failed::class, function ($event) {
            $req = request();
            AuditLog::create([
                'user_id'    => optional($event->user)->id,
                'event'      => 'failed_login',
                'ip_address' => $req?->ip(),
                'user_agent' => $req?->userAgent(),
                'context'    => [
                    'credentials' => array_intersect_key($event->credentials ?? [], array_flip(['email','username'])),
                    'guard'       => $event->guard ?? null,
                ],
            ]);
        });

        Event::listen(PasswordReset::class, function ($event) {
            $req = request();
            AuditLog::create([
                'user_id'    => $event->user?->id,
                'event'      => 'password_reset',
                'ip_address' => $req?->ip(),
                'user_agent' => $req?->userAgent(),
                'context'    => null,
            ]);
        });
    }
}
