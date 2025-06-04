<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StandardController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\ProductionReportController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\MaterialController;

// Redirect root URL to login page
Route::get('/', function () {
    return redirect()->route('login');
});

// Dashboard route (requires authentication and verification)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile routes (requires authentication)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Roles routes
Route::get('/roles', function () {
    return view('roles.roles_permission');
})->name('roles.roles_permission');
Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');

// Analytics routes
Route::get('/analytics/index', [AnalyticsController::class, 'index'])->name('analytics.index');

// Configuration routes
Route::get('/configuration/index', [ConfigurationController::class, 'index'])->name('configuration.index');

Route::prefix('configuration')->group(function () {
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

// Production Report routes
Route::prefix('report')->group(function () {
    Route::get('/index', [ProductionReportController::class, 'index'])->name('report.index');
    Route::get('/view/{report}', [ProductionReportController::class, 'view'])->name('report.view');
    Route::get('/add', [ProductionReportController::class, 'add'])->name('report.add');
    Route::post('/store', [ProductionReportController::class, 'store'])->name('report.store');
    Route::get('/{report}/edit', [ProductionReportController::class, 'edit'])->name('report.edit');
    Route::put('/{report}', [ProductionReportController::class, 'update'])->name('report.update');
    Route::get('/{report}/pdf', [ProductionReportController::class, 'viewPDF'])->name('report.pdf');
});

// Material analytics route
Route::get('/analytics/material/index', [MaterialController::class, 'index'])->name('analytics.materials.index');

// Analytics dashboard view
Route::get('/analytics/dashboard', function () {
    return view('analytics.dashboard');
})->name('analytics.dashboard');

// Auth routes
require __DIR__.'/auth.php';
