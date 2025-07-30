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


// Redirect root URL to login page
Route::get('/', function () {
    return redirect()->route('login');
});

Route::prefix('admin')->middleware(['permission:user.dashboard'])->group(function () {
    Route::get('/dashboard', [DashboardAdminController::class, 'index'])->name('admin.dashboard');
});

// Profile routes (requires authentication)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::patch('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.update.photo');
});

// Production Report routes
Route::prefix('report')->middleware(['permission:report.index'])->group(function () {
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
Route::prefix('configuration')->middleware(['permission:configuration.index'])->group(function () {
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



Route::prefix('roles')->middleware(['permission:roles.index'])->group(function () {
    Route::get('/', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('/store', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('/{role}', [RoleController::class, 'update'])->name('roles.update');
});

Route::prefix('employees')->middleware(['permission:employees.index'])->group(function () {
    Route::get('/', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/store', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/employees/{user}', [EmployeeController::class, 'view'])->name('employees.view');
    Route::get('/{user}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('/employees/{user}', [EmployeeController::class, 'update'])->name('employees.update');


});

// Group all analytics under a common prefix + middleware if needed
Route::prefix('analytics')->name('analytics.')->group(function () {
    
    // Analytics index (Reports Overview)
    Route::get('/index', [AnalyticsController::class, 'index'])->name('index'); // Permission: analytics.index

    // Material Analytics
    Route::get('/material/index', [MaterialController::class, 'index'])->name('material.index'); // Permission: analytics.materials.index
    Route::get('/material/monthly_report', [MaterialController::class, 'monthly_report'])->name('material.monthly_report');

    //Line Analytics
    Route::get('/line/index', [LineController::class, 'index'])->name('line.index'); // Permission: analytics.materials.index
    Route::get('/line/monthly_report', [LineController::class, 'monthly_report'])->name('line.monthly_report');

    // Material Dashboard Analytics
    Route::get('/material_utilization', [MaterialController::class, 'material_utilization'])->name('material_utilization'); // Permission: analytics.materials.index


});

// Auth routes
require __DIR__.'/auth.php';

// Fallback route for undefined URLs
Route::fallback(function () {
    // Redirect back if possible, otherwise to dashboard or home
    return Redirect::back(302)->with('error', 'Page not found or invalid search query.');
});