<?php

use App\Http\Controllers\Web\SponsorController;
use App\Http\Controllers\Web\AboutController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\VotingController;
use App\Http\Controllers\Web\PrizeController;
use App\Http\Controllers\Web\ContactController;
use App\Http\Controllers\Web\GalleryController;
use App\Http\Controllers\Web\LuxuryCommunityMemberController;

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
Route::get('/voting', [VotingController::class, 'index'])->name('web.voting');
Route::get('/company/{id}', [VotingController::class, 'show'])->name('web.company_details');
Route::get('/prizes', [PrizeController::class, 'index'])->name('web.prizes');
Route::get('/contact', [ContactController::class, 'index'])->name('web.contact');
Route::post('vote/{id}', [App\Http\Controllers\Web\VotingController::class, 'vote'])->name('web.vote');
Route::get('/gallery', [GalleryController::class, 'index'])->name('web.gallery');
Route::post('/store/contact',[\App\Http\Controllers\Web\ContactController::class, 'store'])->name('web.contact.store');
Route::get('/luxury', [LuxuryCommunityMemberController::class, 'index'])->name('web.luxury');
Route::get('/Join', [LuxuryCommunityMemberController::class, 'luxurystore'])->name('web.luxurystore');
Route::post('/Join/store', [LuxuryCommunityMemberController::class, 'store'])->name('web.luxury-members.store');
Route::get('/voting/load-more', [VotingController::class, 'loadMore'])->name('voting.loadMore');

