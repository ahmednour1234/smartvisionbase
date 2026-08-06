<?php

use App\Http\Controllers\DocController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect('/admin/login');
    }

    $user = auth()->user();
    $roleSlug = $user->role?->slug ?? 'sales';
    
    return $roleSlug === 'sales'
        ? redirect('/employee')
        : redirect('/admin');
});

Route::post('/logout', function () {
    $panel = request()->segment(1);
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect("/{$panel}/login");
})->name('logout');

Route::get('/admin/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/admin/login');
});

Route::get('/employee/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/employee/login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/docs/proforma/{company}', [DocController::class, 'proforma'])->name('docs.proforma');
    Route::get('/docs/contract/{company}', [DocController::class, 'contract'])->name('docs.contract');
});
