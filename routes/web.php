<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Dashboard\EventController as DashboardEventController;
use App\Http\Controllers\Dashboard\SettingController as DashboardSettingController;
use App\Http\Controllers\Dashboard\AboutUsController as DashboardAboutUsController;
use App\Http\Controllers\Dashboard\ContactMessageController as DashboardContactMessageController;
use App\Http\Controllers\Dashboard\MediaController as DashboardMediaController;
use App\Http\Controllers\Dashboard\PasswordController as DashboardPasswordController;
use App\Http\Controllers\Dashboard\UserController as DashboardUserController;
use App\Http\Controllers\Dashboard\TestimonialController as DashboardTestimonialController;
use App\Http\Controllers\Dashboard\FaqController as DashboardFaqController;
use App\Http\Controllers\Dashboard\VideoController as DashboardVideoController;
use App\Http\Controllers\Dashboard\SponsorController as DashboardSponsorController;
use App\Http\Controllers\SponsorController as PublicSponsorController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// PUBLIC ROUTES (locale is handled by middleware; URLs without 'ar/en' still work)

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{slug}', [EventController::class, 'show'])->name('events.show');

Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'sendContact'])->name('contact.send');
Route::get('/sponsors', [PublicSponsorController::class, 'index'])->name('sponsors.index');
Route::get('/sponsors/load-more', [PublicSponsorController::class, 'loadMore'])->name('site.sponsors.load-more');
// Locale switcher (redirect keeping path)
Route::get('/lang/{locale}', function ($locale) {
    if (! in_array($locale, ['en','ar'])) abort(404);
    session(['app_locale' => $locale]);
    app()->setLocale($locale);
    return back();
})->name('lang.switch');

// AUTH routes (prefer Breeze/Fortify style include if present)
if (file_exists(base_path('routes/auth.php'))) {
    require base_path('routes/auth.php');
}
// Minimal auth routes (no Breeze/UI)
Route::get('/login', [\App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])->name('login.perform');
// Fallback minimal logout route
Route::post('/logout', function () {
    if (Auth::check()) {
        Auth::logout();
    }
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('home');
})->name('logout');

// DASHBOARD (protected by simple auth)
Route::middleware('auth')->prefix('dashboard')
    ->name('dashboard.')
    ->group(function () {

        Route::get('/', [\App\Http\Controllers\Dashboard\DashboardController::class, 'index'])
            ->name('index');

        Route::resource('events', DashboardEventController::class)->except('show');
        Route::get('events/{event}/toggle-active', [DashboardEventController::class, 'toggleActive'])
            ->name('events.toggle-active');
        Route::get('events/{event}/toggle-featured', [DashboardEventController::class, 'toggleFeatured'])
            ->name('events.toggle-featured');

        Route::get('settings', [DashboardSettingController::class, 'edit'])->name('settings.edit');
        Route::post('settings', [DashboardSettingController::class, 'update'])->name('settings.update');

        Route::get('about', [DashboardAboutUsController::class, 'edit'])->name('about.edit');
        Route::post('about', [DashboardAboutUsController::class, 'update'])->name('about.update');

        Route::get('contacts', [DashboardContactMessageController::class, 'index'])->name('contacts.index');
        Route::get('contacts/{contactMessage}', [DashboardContactMessageController::class, 'show'])->name('contacts.show');

        Route::get('media', [DashboardMediaController::class, 'index'])->name('media.index');
        Route::post('media', [DashboardMediaController::class, 'store'])->name('media.store');
        Route::delete('media', [DashboardMediaController::class, 'destroy'])->name('media.destroy');

        // Account password
        Route::get('account/password', [DashboardPasswordController::class, 'edit'])->name('account.password.edit');
        Route::post('account/password', [DashboardPasswordController::class, 'update'])->name('account.password.update');

        // Users management
        Route::resource('users', DashboardUserController::class)->except('show');

        // Testimonials
        Route::resource('testimonials', DashboardTestimonialController::class)->except('show');

        // FAQs
        Route::resource('faqs', DashboardFaqController::class)->except('show');

        // Videos
        Route::resource('videos', DashboardVideoController::class)->except('show');

        // Sponsors
        Route::resource('sponsors', DashboardSponsorController::class)->except('show');
    });

// Public gallery page
Route::get('/gallery', function () {
    $dir = public_path('uploads/media');
    $files = [];
    if (is_dir($dir)) {
        foreach (glob($dir . DIRECTORY_SEPARATOR . '*') as $path) {
            if (is_file($path)) {
                $files[] = [
                    'path' => 'uploads/media/' . basename($path),
                    'ext' => strtolower(pathinfo($path, PATHINFO_EXTENSION)),
                    'mtime' => filemtime($path),
                ];
            }
        }
    }
    usort($files, fn($a, $b) => $b['mtime'] <=> $a['mtime']);
    return view('site.gallery', compact('files'));
})->name('gallery');
