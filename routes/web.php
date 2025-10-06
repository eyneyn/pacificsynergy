<?php

use App\Http\Controllers\DashboardAdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StandardController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\ProductionReportController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LineController;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Auth\SetPasswordController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// Auth routes
require __DIR__.'/auth.php';

// Redirect root URL to login page
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/2fa/setup', [AuthenticatedSessionController::class, 'setup2fa'])->name('2fa.setup');
Route::get('/2fa/verify', [AuthenticatedSessionController::class, 'show2faForm'])->name('2fa.verify.form');
Route::post('/2fa/verify', [AuthenticatedSessionController::class, 'verify2fa'])->name('2fa.verify');

Route::post('/employees/{user}/reset-2fa', [EmployeeController::class, 'reset2fa'])
    ->name('employees.reset2fa')
    ->middleware(['auth','role:Admin']);

Route::prefix('admin')->middleware(['auth','permission:user.dashboard'])->group(function () {
    Route::get('/dashboard', [DashboardAdminController::class, 'index'])->name('admin.dashboard');

    Route::get('/setting', [DashboardAdminController::class, 'setting'])->name('setting.index');
    Route::put('/setting', [DashboardAdminController::class, 'updateSetting'])->name('setting.update');

    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

});

// Profile routes (requires authentication)
Route::middleware('auth')->prefix('account')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::patch('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.update.photo');
});

// 🔑 Password setup (NO auth middleware)
Route::get('/set-password/{token}', function($token) {
    return view('auth.set-password', ['token' => $token]);
})->name('password.set.form');

Route::post('/set-password', [SetPasswordController::class, 'store'])
    ->name('password.set');

// Production Report routes
Route::prefix('report')->middleware(['auth','permission:report.index'])->group(function () {
    Route::get('/index', [ProductionReportController::class, 'index'])->name('report.index');
    Route::get('/view/{report}', [ProductionReportController::class, 'view'])->middleware('permission:report.index')->name('report.view');
    Route::get('/add', [ProductionReportController::class, 'add'])->middleware('permission:report.add')->name('report.add');
    Route::post('/store', [ProductionReportController::class, 'store'])->middleware('permission:report.add')->name('report.store');
    Route::get('/{report}/edit', [ProductionReportController::class, 'edit'])->middleware('permission:report.edit')->name('report.edit');
    Route::put('/{report}', [ProductionReportController::class, 'update'])->middleware('permission:report.edit')->name('report.update');
    Route::get('/{report}/pdf', [ProductionReportController::class, 'viewPDF'])->middleware('permission:report.index')->name('report.pdf');
    Route::patch('/report/{id}/validate', [ProductionReportController::class, 'validateReport'])->middleware('permission:report.validate')->name('report.validate');
    });

// Configuration Routes
Route::prefix('configuration')->middleware(['auth','permission:configuration.index'])->group(function () {
    Route::get('/index', [ConfigurationController::class, 'index'])->name('configuration.index');

    // ------ Standard Metric ----
    Route::get('/standard/index', [StandardController::class, 'index'])->name('configuration.standard.index');
    Route::get('/standard/add', [StandardController::class, 'add'])->name('configuration.standard.add');
    Route::post('/standards/store', [StandardController::class, 'store'])->name('configuration.standard.store');
    Route::get('/standards/{standard}', [StandardController::class, 'view'])->name('configuration.standard.view');
    Route::get('/standards/{standard}/edit', [StandardController::class, 'editForm'])->name('configuration.standard.edit');
    Route::put('/standards/{standard}', [StandardController::class, 'edit'])->name('configuration.standard.update');
    Route::delete('/standards/{standard}', [StandardController::class, 'destroy'])->name('configuration.standard.destroy');

    // ------ Defect ----
    Route::get('/defect/index', [ConfigurationController::class, 'defect'])->name('configuration.defect.index');
    Route::get('/defect/add', [ConfigurationController::class, 'add_defect'])->name('configuration.defect.add');
    Route::post('/defect/store', [ConfigurationController::class, 'defect_store'])->name('configuration.defect.store');
    Route::get('/defect/{defect}', [ConfigurationController::class, 'view_defect'])->name('configuration.defect.view');
    Route::get('/defect/{defect}/edit', [ConfigurationController::class, 'defect_edit'])->name('configuration.defect.edit');
    Route::put('/defect/{defect}', [ConfigurationController::class, 'defect_update'])->name('configuration.defect.update');
    Route::delete('/defect/{defect}', [ConfigurationController::class, 'defect_destroy'])->name('configuration.defect.destroy');

    // ------ Maintenance ----
    Route::get('/maintenance/index', [ConfigurationController::class, 'maintenance'])->name('configuration.maintenance.index');
    Route::post('/maintenance/store', [ConfigurationController::class, 'maintenance_store'])->name('configuration.maintenance.store');
    Route::put('/configuration/maintenance/update/{maintenance}', [ConfigurationController::class, 'maintenance_update'])->name('configuration.maintenance.update');
    Route::delete('/maintenance/destroy/{maintenance}', [ConfigurationController::class, 'maintenance_destroy'])->name('configuration.maintenance.destroy');

    // ------ Line ----
    Route::get('/line/index', [ConfigurationController::class, 'line'])->name('configuration.line.index');
    Route::post('/line/store', [ConfigurationController::class, 'line_store'])->name('configuration.line.store');
    Route::put('/line/update/{line_number}', [ConfigurationController::class, 'line_update'])->name('configuration.line.update');
    Route::delete('/line/destroy/{line_number}', [ConfigurationController::class, 'line_destroy'])->name('configuration.line.destroy');
});

Route::prefix('roles')->middleware(['auth','permission:roles.permission'])->group(function () {
    Route::get('/', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('/store', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('/{role}', [RoleController::class, 'update'])->name('roles.update');
});

Route::prefix('employees')->middleware(['auth','permission:employees.index'])->group(function () {
    Route::get('/', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/store', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/employees/{user}', [EmployeeController::class, 'view'])->name('employees.view');
    Route::get('/{user}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('/employees/{user}', [EmployeeController::class, 'update'])->name('employees.update');

    Route::post('/{user}/send-login-link', [EmployeeController::class, 'sendLoginLink'])->name('employees.sendLoginLink');
});

// Group all analytics under a common prefix + middleware
Route::prefix('analytics')
    ->name('analytics.')
    ->middleware(['auth'])
    ->group(function () {
    
    // Analytics index (Reports Overview)
    Route::get('/index', [AnalyticsController::class, 'index'])->name('index');

    // Material Analytics
    Route::get('/material/index', [MaterialController::class, 'index'])
        ->name('material.index');

    Route::get('/material/monthly_report', [MaterialController::class, 'monthly_report'])
        ->name('material.monthly_report');

    Route::get('/material/export_excel', [MaterialController::class, 'exportExcel'])
        ->name('material.export_excel');

    Route::get('/material/export-annual', [MaterialController::class, 'exportExcelAnnual'])
    ->name('material.export_excel_annual');

    // Material Dashboard Analytics
    Route::get('/material_utilization', [MaterialController::class, 'material_utilization'])
        ->name('material_utilization');

    Route::get('/export_excel_material_summary', [MaterialController::class, 'exportExcelMaterialSummary'])
        ->name('export_excel_material_summary');

    // Line Analytics
    Route::get('/line/index', [LineController::class, 'index'])
        ->name('line.index');

    Route::get('/line/monthly_report', [LineController::class, 'monthly_report'])
        ->name('line.monthly_report');

        Route::get('/line/export_excel', [LineController::class, 'exportExcel']) // ✅ correct
            ->name('line.export_excel');

        Route::get('/line/export-annual', [LineController::class, 'exportExcelAnnual']) 
            ->name('line.export_excel_annual');

    // Line Dashboard Analytics
    Route::get('/line_efficiency', [LineController::class, 'line_efficiency'])
        ->name('line_efficiency');

        Route::get('/export_excel_line_summary', [LineController::class, 'exportExcelLineSummary'])
    ->name('export_excel_line_summary');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.read');

Route::get('/notifications/dropdown', [NotificationController::class, 'dropdown'])
    ->name('notifications.dropdown');

});


//Route::get('/heartbeat', function () {
  //  return response()->json(['status' => 'alive']);
//})->middleware('auth');

//Route::fallback(function () {
//    return response()->view('errors.404', [], 404);
//});