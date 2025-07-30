<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductionReport;
use App\Models\Status;
use App\Models\ProductionReportHistory;

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

View::composer('*', function ($view) {
    $userId = Auth::id();

    $statusNotifications = Status::with(['productionReport', 'user'])
        ->where('user_id', $userId)
        ->latest()
        ->take(5)
        ->get();

    $changeLogs = ProductionReportHistory::with(['report', 'user'])
        ->where('updated_by', $userId)
        ->orderBy('updated_at', 'desc')
        ->take(5)
        ->get();

    $view->with('statusNotifications', $statusNotifications);
    $view->with('changeLogs', $changeLogs);
});

}
}
