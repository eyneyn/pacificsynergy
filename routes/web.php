<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StandardController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\ProductionReportController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\MaterialController;

Route::get('/', function () {
    return redirect()->route('login');  // Redirect root URL to login page
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/roles', function () {
    return view('roles.roles_permission');
})->name('roles.roles_permission');

Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');

Route::get('/analytics/index', [AnalyticsController::class, 'index'])->name('analytics.index');

Route::get('/metrics/configuration', [ConfigurationController::class, 'index'])->name('metrics.configuration');

// ------ Standard Metric ----
Route::get('/metrics/standard/index', [StandardController::class, 'index'])->name('metrics.standard.index');
Route::get('/metrics/standard/add', action: [StandardController::class, 'add'])->name('metrics.standard.add');
Route::post('/metrics/standards/store', [StandardController::class, 'store'])->name('metrics.standard.store');
Route::get('/metrics/standards/{standard}', [StandardController::class, 'view'])->name('metrics.standard.view');
Route::get('/metrics/standards/{standard}/edit', [StandardController::class, 'editForm'])->name('metrics.standard.edit');
Route::put('/metrics/standards/{standard}', [StandardController::class, 'edit'])->name('metrics.standard.update');
Route::delete('/metrics/standards/{standard}', [StandardController::class, 'destroy'])->name('metrics.standard.destroy');

// ------ Defect ----
Route::get('/metrics/defect/index', action: [ConfigurationController::class, 'defect'])->name('metrics.defect.index');
Route::get('/metrics/defect/add', action: [ConfigurationController::class, 'add_defect'])->name('metrics.defect.add');
Route::post('/metrics/defect/store', [ConfigurationController::class, 'defect_store'])->name('metrics.defect.store');
Route::get('/metrics/defect/{defect}', [ConfigurationController::class, 'view_defect'])->name('metrics.defect.view');
Route::get('/metrics/defect/{defect}/edit', [ConfigurationController::class, 'defect_edit'])->name('metrics.defect.edit');
Route::put('/metrics/defect/{defect}', [ConfigurationController::class, 'defect_update'])->name('metrics.defect.update');
Route::delete('/metrics/defect/{defect}', [ConfigurationController::class, 'defect_destroy'])->name('metrics.defect.destroy');

//  ------ Maintenance ----
Route::get('/metrics/maintenance/index', action: [ConfigurationController::class, 'maintenance'])->name('metrics.maintenance.index');
Route::get('/metrics/maintenance/add', action: [ConfigurationController::class, 'add_maintenance'])->name('metrics.maintenance.add');
Route::post('/metrics/maintenance/store', [ConfigurationController::class, 'maintenance_store'])->name('metrics.maintenance.store');
Route::get('/metrics/maintenance/{maintenance}', [ConfigurationController::class, 'view_maintenance'])->name('metrics.maintenance.view');
Route::get('/metrics/maintenance/{maintenance}/edit', [ConfigurationController::class, 'maintenance_edit'])->name('metrics.maintenance.edit');
Route::put('/metrics/maintenance/{maintenance}', [ConfigurationController::class, 'maintenance_update'])->name('metrics.maintenance.update');
Route::delete('/metrics/maintenance/{maintenance}', [ConfigurationController::class, 'maintenance_destroy'])->name('metrics.maintenance.destroy');

//  ------ Line ----
Route::post('/configuration/store', [ConfigurationController::class, 'line_store'])->name('configuration.store');
Route::patch('/configuration/{line}', action: [ConfigurationController::class, 'line_update'])->name('configuration.update');
Route::delete('/configuration/{line}', [ConfigurationController::class, 'line_destroy'])->name('configuration.destroy');

Route::get('/metrics/formula', action: [ConfigurationController::class, 'formula'])->name('metrics.formula');


//  ------ Production Report ----
Route::get('/report/index', action: [ProductionReportController::class, 'index'])->name('report.index');
Route::get('/report/view/{report}', [ProductionReportController::class, 'view'])->name('report.view');
Route::get('/report/add', action: [ProductionReportController::class, 'add'])->name('report.add');
Route::post('/report/store', [ProductionReportController::class, 'store'])->name('report.store');
Route::get('/report/{report}/edit', [ProductionReportController::class, 'edit'])->name('report.edit');
Route::put('/report/{report}', [ProductionReportController::class, 'update'])->name('report.update');
Route::delete('report/{report}', [ProductionReportController::class, 'destroy'])->name('report.destroy');

//  ------ Material  ----
Route::get('/analytics/material/index', action: [MaterialController::class, 'index'])->name('analytics.materials.index');



require __DIR__.'/auth.php';
