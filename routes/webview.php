<?php

use App\Http\Controllers\Web\SponsorController;
use App\Http\Controllers\Web\AboutController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\GalleryController;
use App\Http\Controllers\Web\ContactController;
use App\Http\Controllers\Web\RegisterController;

// صفحة الواجهة الرئيسية

Route::get('/', [HomeController::class, 'index'])->name('web.home');
Route::get('/about', [AboutController::class, 'index'])->name('web.about');
Route::get('/speaker', [\App\Http\Controllers\Web\SpeackerController::class, 'index'])->name('web.speaker');
Route::get('/sponsor', [SponsorController::class, 'index'])->name('web.sponsors');
Route::get('/load-more-sponsors/{categoryId}', [SponsorController::class, 'loadMoreSponsors']);
Route::get('/package', [\App\Http\Controllers\Web\PackageController::class, 'index'])->name('web.packages');
Route::get('/schdule', [\App\Http\Controllers\Web\SchduleController::class, 'index'])->name('web.schdule');
Route::get('/blogs', [\App\Http\Controllers\Web\BlogController::class, 'index'])->name('web.blogs');
Route::get('/blog/{id}', [\App\Http\Controllers\Web\BlogController::class, 'show'])
    ->name('web.blog.show');
Route::get('/allmulti_media', [\App\Http\Controllers\Web\MultiMediaController::class, 'index'])
    ->name('web.multi_media');
Route::get('/multi_media/{id}', [\App\Http\Controllers\Web\MultiMediaController::class, 'show'])
    ->name('web.multi_media.show');
Route::get('/register',[\App\Http\Controllers\Web\RegisterController::class, 'index'])->name('web.register');
Route::get('/becomesponsor',[\App\Http\Controllers\Web\RegisterController::class, 'becomesponsor'])->name('web.becomesponsor');
Route::post('/storeregister',[\App\Http\Controllers\Web\RegisterController::class, 'store'])->name('web.register.store');
Route::get('/gallery', [GalleryController::class, 'index'])->name('web.gallery');
Route::get('/contact', [ContactController::class, 'index'])->name('web.contact');
Route::post('/store/contact',[\App\Http\Controllers\Web\ContactController::class, 'store'])->name('web.contact.store');
Route::get('/register/verify', [RegisterController::class, 'showVerifyForm'])->name('web.register.verify.form');
Route::post('/register/verify', [RegisterController::class, 'verify'])->name('web.register.verify');

Route::post('/register/resend-code', [RegisterController::class, 'resendCode'])->name('web.register.resend');

Route::get('/register/verified', [RegisterController::class, 'verifiedPage'])->name('web.register.verified');
Route::get('/thank-you', [RegisterController::class, 'thankYou'])
    ->name('web.thank.you');