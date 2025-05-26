<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\CommunityEventController;
use App\Http\Controllers\VolunteerController;
use App\Http\Controllers\ForumController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Landing page for public visitors
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : view('landing');
})->name('home');

// Dedicated landing page route (always shows landing page)
Route::get('/landing', function () {
    return view('landing');
})->name('landing');

Auth::routes();

// Add this in the auth routes section
Route::get('/register/type', [App\Http\Controllers\Auth\RegisterController::class, 'showTypeSelection'])
    ->name('register.type');
Route::post('/register/type', [App\Http\Controllers\Auth\RegisterController::class, 'processTypeSelection'])
    ->name('register.type.process');

// Modify the existing register route to redirect to type selection
Route::get('/register', function() {
    // If user_type is already set in session, proceed to registration
    if (session()->has('user_type')) {
        return app()->make('App\Http\Controllers\Auth\RegisterController')->showRegistrationForm();
    }
    
    // Otherwise redirect to type selection
    return redirect()->route('register.type');
})->name('register');

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

    // Evidence routes
    Route::get('/incidents/{incident}/evidence', [App\Http\Controllers\IncidentEvidenceController::class, 'index'])->name('incidents.evidence.index');
    Route::post('/incidents/{incident}/evidence', [App\Http\Controllers\IncidentEvidenceController::class, 'store'])->name('incidents.evidence.store');
    Route::delete('/evidence/{evidence}', [App\Http\Controllers\IncidentEvidenceController::class, 'destroy'])->name('evidence.destroy');

    // Analytics routes
    Route::get('/analytics', [App\Http\Controllers\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/export', [App\Http\Controllers\AnalyticsController::class, 'exportReport'])->name('analytics.export');

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
        Route::get('/evidence', [AdminDashboardController::class, 'evidence'])->name('evidence');
        Route::get('/incidents/{incident}/view', [AdminDashboardController::class, 'viewIncident'])->name('incidents.view');
        Route::patch('/incidents/{incident}/status', [AdminDashboardController::class, 'updateIncidentStatus'])->name('incidents.update-status');
        Route::delete('/incidents/{incident}', [AdminDashboardController::class, 'deleteIncident'])->name('incidents.delete');
        Route::patch('/users/{user}/role', [AdminDashboardController::class, 'updateUserRole'])->name('users.update-role');
        Route::patch('/evidence/{evidence}/verify', [AdminDashboardController::class, 'verifyEvidence'])->name('evidence.verify');
        Route::delete('/evidence/{evidence}', [AdminDashboardController::class, 'deleteEvidence'])->name('evidence.delete');
        Route::get('/export', [AdminDashboardController::class, 'exportReport'])->name('export');
    });

    // Community engagement routes
    Route::prefix('community')->name('community.')->group(function () {
        Route::get('/', [CommunityController::class, 'index'])->name('index');
        Route::get('/leaderboard', [CommunityController::class, 'leaderboard'])->name('leaderboard');
        Route::get('/badges', [CommunityController::class, 'badges'])->name('badges');
        Route::get('/profile/{user?}', [CommunityController::class, 'profile'])->name('profile');
        Route::get('/points-history', [CommunityController::class, 'pointsHistory'])->name('points-history');
        Route::get('/search', [CommunityController::class, 'search'])->name('search');
        Route::get('/notifications', [CommunityController::class, 'notifications'])->name('notifications');

        // Events
        Route::prefix('events')->name('events.')->group(function () {
            Route::get('/', [CommunityEventController::class, 'index'])->name('index');
            Route::get('/create', [CommunityEventController::class, 'create'])->name('create');
            Route::post('/', [CommunityEventController::class, 'store'])->name('store');
            Route::get('/my-events', [CommunityEventController::class, 'myEvents'])->name('my-events');
            Route::get('/{event}', [CommunityEventController::class, 'show'])->name('show');
            Route::get('/{event}/edit', [CommunityEventController::class, 'edit'])->name('edit');
            Route::put('/{event}', [CommunityEventController::class, 'update'])->name('update');
            Route::delete('/{event}', [CommunityEventController::class, 'destroy'])->name('destroy');
            Route::post('/{event}/rsvp', [CommunityEventController::class, 'rsvp'])->name('rsvp');
            Route::delete('/{event}/rsvp', [CommunityEventController::class, 'cancelRsvp'])->name('cancel-rsvp');
            Route::patch('/{event}/status', [CommunityEventController::class, 'updateStatus'])->name('update-status');
            Route::post('/{event}/attendance', [CommunityEventController::class, 'markAttendance'])->name('mark-attendance');
        });

        // Volunteer opportunities
        Route::prefix('volunteer')->name('volunteer.')->group(function () {
            Route::get('/', [VolunteerController::class, 'index'])->name('index');
            Route::get('/create', [VolunteerController::class, 'create'])->name('create');
            Route::post('/', [VolunteerController::class, 'store'])->name('store');
            Route::get('/my-applications', [VolunteerController::class, 'myApplications'])->name('my-applications');
            Route::get('/my-opportunities', [VolunteerController::class, 'myOpportunities'])->name('my-opportunities');
            Route::get('/{opportunity}', [VolunteerController::class, 'show'])->name('show');
            Route::get('/{opportunity}/edit', [VolunteerController::class, 'edit'])->name('edit');
            Route::put('/{opportunity}', [VolunteerController::class, 'update'])->name('update');
            Route::delete('/{opportunity}', [VolunteerController::class, 'destroy'])->name('destroy');
            Route::post('/{opportunity}/apply', [VolunteerController::class, 'apply'])->name('apply');
            Route::delete('/{opportunity}/apply', [VolunteerController::class, 'withdrawApplication'])->name('withdraw');
            Route::get('/{opportunity}/applications', [VolunteerController::class, 'manageApplications'])->name('manage-applications');
            Route::patch('/applications/{application}', [VolunteerController::class, 'updateApplicationStatus'])->name('update-application');
        });
    });

    // Forum routes
    Route::prefix('forums')->name('forums.')->group(function () {
        Route::get('/', [ForumController::class, 'index'])->name('index');
        Route::get('/{forum}', [ForumController::class, 'show'])->name('show');
        Route::get('/{forum}/create-topic', [ForumController::class, 'createTopic'])->name('create-topic');
        Route::post('/{forum}/topics', [ForumController::class, 'storeTopic'])->name('store-topic');

        Route::prefix('topics')->name('topics.')->group(function () {
            Route::get('/{topic}', [ForumController::class, 'showTopic'])->name('show');
            Route::post('/{topic}/reply', [ForumController::class, 'replyToTopic'])->name('reply');
            Route::get('/{topic}/edit', [ForumController::class, 'editTopic'])->name('edit');
            Route::put('/{topic}', [ForumController::class, 'updateTopic'])->name('update');
            Route::delete('/{topic}', [ForumController::class, 'deleteTopic'])->name('delete');
            Route::patch('/{topic}/pin', [ForumController::class, 'pinTopic'])->name('pin');
            Route::patch('/{topic}/lock', [ForumController::class, 'lockTopic'])->name('lock');
        });

        Route::prefix('replies')->name('replies.')->group(function () {
            Route::get('/{reply}/edit', [ForumController::class, 'editReply'])->name('edit');
            Route::put('/{reply}', [ForumController::class, 'updateReply'])->name('update');
            Route::delete('/{reply}', [ForumController::class, 'deleteReply'])->name('delete');
        });
    });

    // Authorities Portal Routes
    Route::middleware(['auth', 'authority.access'])->prefix('authorities')->name('authorities.')->group(function () {
        Route::get('/', [App\Http\Controllers\AuthoritiesPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/incidents', [App\Http\Controllers\AuthoritiesPortalController::class, 'incidents'])->name('incidents');
        Route::get('/incidents/{incident}', [App\Http\Controllers\AuthoritiesPortalController::class, 'showIncident'])->name('incidents.show');
        Route::post('/incidents/{incident}/comment', [App\Http\Controllers\AuthoritiesPortalController::class, 'addComment'])->name('incidents.comment');
        Route::get('/settings', [App\Http\Controllers\AuthoritiesPortalController::class, 'settings'])->name('settings');
        Route::post('/settings', [App\Http\Controllers\AuthoritiesPortalController::class, 'updateSettings'])->name('settings.update');
    });

    // Authority Setup for new authority users
    Route::middleware(['auth'])->get('/authorities/setup', [App\Http\Controllers\AuthoritySetupController::class, 'setup'])->name('authorities.setup');
    Route::middleware(['auth'])->post('/authorities/setup', [App\Http\Controllers\AuthoritySetupController::class, 'store'])->name('authorities.setup.store');
});




