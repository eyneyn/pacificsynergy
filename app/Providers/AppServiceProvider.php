<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use App\Models\ProductionReport;

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

        // ✅ Share Submitted Reports Count Globally
        //View::composer('*', function ($view) {
            //$submittedCount = ProductionReport::where('status', 'Submitted')->count();
            //$view->with('submittedReportCount', $submittedCount);
        //});
    }
}
