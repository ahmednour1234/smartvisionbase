<?php

use App\Http\Controllers\Web\AuthCookieController;
use App\Http\Middleware\SecureHeaders;
use Illuminate\Support\Facades\Route;

Route::middleware([SecureHeaders::class])->group(function () {
    Route::get('/', function () {
        return redirect('/admin');
    });

    // --- Sanctum SPA (cookie) auth endpoints ---
    // Client flow:
    //   1) GET /sanctum/csrf-cookie
    //   2) POST /auth/login-cookie
    //   3) Use auth:sanctum on API routes (stateful domains)
    Route::prefix('auth')->group(function () {
        Route::post('login-cookie', [AuthCookieController::class, 'login']);
        Route::post('logout-cookie', [AuthCookieController::class, 'logout']);
        Route::get('me-cookie', [AuthCookieController::class, 'me']);
    });
});
