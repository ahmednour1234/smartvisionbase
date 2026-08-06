<?php

use App\Http\Controllers\Api\AuthCookieController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\LeadActivityController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\LookupController;
use App\Http\Controllers\Api\MeetingController;
use App\Http\Controllers\Api\Admin\AuditController;
use App\Http\Controllers\Api\Admin\LookupAdminController;
use App\Http\Controllers\Api\Admin\UserAdminController;
use App\Http\Middleware\EnsureIsAdmin;
use App\Http\Middleware\RequirePermission;
use Illuminate\Support\Facades\Route;

app('router')->aliasMiddleware('is_admin', EnsureIsAdmin::class);
app('router')->aliasMiddleware('require_perm', RequirePermission::class);

Route::prefix('auth')->group(function () {
    // Stateless token login
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    // SPA cookie mode (HttpOnly cookie)
    Route::post('/spa/login', [AuthCookieController::class, 'login']);
    Route::post('/spa/logout', [AuthCookieController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/me', [AuthCookieController::class, 'me'])->middleware('auth:sanctum');
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/dashboard/summary', [DashboardController::class, 'summary'])
        ->middleware('require_perm:dashboard.view');

    Route::get('/lookups', [LookupController::class, 'all'])
        ->middleware('require_perm:lookups.view');

    // Leads
    Route::get('/leads', [LeadController::class, 'index'])->middleware('require_perm:leads.view');
    Route::post('/leads', [LeadController::class, 'store'])->middleware('require_perm:leads.create');
    Route::get('/leads/{lead}', [LeadController::class, 'show'])->middleware('require_perm:leads.view');
    Route::put('/leads/{lead}', [LeadController::class, 'update'])->middleware('require_perm:leads.update');
    Route::delete('/leads/{lead}', [LeadController::class, 'destroy'])->middleware('require_perm:leads.delete');

    // Lead Activities / Notes
    Route::get('/leads/{lead}/activities', [LeadActivityController::class, 'index'])
        ->middleware('require_perm:leads.view');
    Route::post('/leads/{lead}/notes', [LeadActivityController::class, 'addNote'])
        ->middleware('require_perm:leads.update');

    // Meetings
    Route::get('/leads/{lead}/meetings', [MeetingController::class, 'indexByLead'])
        ->middleware(['require_perm:leads.view', 'require_perm:meetings.manage']);
    Route::post('/meetings', [MeetingController::class, 'store'])->middleware('require_perm:meetings.manage');
    Route::put('/meetings/{id}', [MeetingController::class, 'update'])->middleware('require_perm:meetings.manage');
    Route::delete('/meetings/{id}', [MeetingController::class, 'destroy'])->middleware('require_perm:meetings.manage');

    // Admin routes
    Route::prefix('admin')->middleware(['is_admin'])->group(function () {
        Route::get('/audit', [AuditController::class, 'index'])->middleware('require_perm:audit.view');

        Route::get('/users', [UserAdminController::class, 'index'])->middleware('require_perm:users.manage');
        Route::post('/users', [UserAdminController::class, 'store'])->middleware('require_perm:users.manage');
        Route::put('/users/{id}', [UserAdminController::class, 'update'])->middleware('require_perm:users.manage');
        Route::delete('/users/{id}', [UserAdminController::class, 'destroy'])->middleware('require_perm:users.manage');

        Route::get('/lookups', [LookupAdminController::class, 'index'])->middleware('require_perm:lookups.manage');
        Route::post('/lookups', [LookupAdminController::class, 'store'])->middleware('require_perm:lookups.manage');
        Route::put('/lookups/{type}/{id}', [LookupAdminController::class, 'update'])->middleware('require_perm:lookups.manage');
        Route::delete('/lookups/{type}/{id}', [LookupAdminController::class, 'destroy'])->middleware('require_perm:lookups.manage');
    });
});
