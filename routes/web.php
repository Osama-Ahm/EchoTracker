<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Landing page for public visitors
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : view('landing');
})->name('landing');

Auth::routes();

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/home', [DashboardController::class, 'index'])->name('home');

    // Incident management
    Route::get('/incidents', [IncidentController::class, 'index'])->name('incidents.index');
    Route::get('/incidents/create', [IncidentController::class, 'create'])->name('incidents.create');
    Route::post('/incidents', [IncidentController::class, 'store'])->name('incidents.store');
    Route::get('/incidents/{incident}', [IncidentController::class, 'show'])->name('incidents.show');
    Route::get('/incidents/{incident}/edit', [IncidentController::class, 'edit'])->name('incidents.edit');
    Route::put('/incidents/{incident}', [IncidentController::class, 'update'])->name('incidents.update');
    Route::delete('/incidents/{incident}', [IncidentController::class, 'destroy'])->name('incidents.destroy');
    Route::get('/my-incidents', [IncidentController::class, 'my'])->name('incidents.my');
    Route::get('/map', [IncidentController::class, 'map'])->name('incidents.map');

    // Profile routes
    Route::get('/profile', [UserProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [UserProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [UserProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [UserProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [UserProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/incidents', [AdminDashboardController::class, 'incidents'])->name('incidents');
        Route::get('/users', [AdminDashboardController::class, 'users'])->name('users');
        Route::patch('/incidents/{incident}/status', [AdminDashboardController::class, 'updateIncidentStatus'])->name('incidents.update-status');
        Route::delete('/incidents/{incident}', [AdminDashboardController::class, 'deleteIncident'])->name('incidents.delete');
        Route::patch('/users/{user}/role', [AdminDashboardController::class, 'updateUserRole'])->name('users.update-role');
    });
});
