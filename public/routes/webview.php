<?php

use App\Http\Controllers\Web\SponsorController;
use App\Http\Controllers\Web\AboutController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\GalleryController;
use App\Http\Controllers\Web\RegisterController;
use App\Http\Controllers\Web\VotingController;
    use App\Http\Controllers\Web\WaWebhookController;
use App\Http\Controllers\Web\ContactController;
use App\Http\Controllers\Web\RefLinkController;

// صفحة الواجهة الرئيسية
Route::get('/r/{ref:code}', [RefLinkController::class, 'hit'])
    ->name('ref.hit');

// رابط قصير مع مسار مخصص (اختياري)
Route::get('/r/{ref:code}/{path?}', [RefLinkController::class, 'hit'])
    ->where('path', '.*')
    ->name('ref.hit.path');
Route::get('/', [HomeController::class, 'index'])->name('web.home');
Route::get('/about', [AboutController::class, 'index'])->name('web.about');
Route::get('/speaker', [\App\Http\Controllers\Web\SpeackerController::class, 'index'])->name('web.speaker');
Route::get('/special', [\App\Http\Controllers\Web\SpecialController::class, 'index'])->name('web.special');

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
// routes/web.php
Route::get('/voting', [VotingController::class, 'index'])->name('web.voting');
Route::get('/company/{id}', [VotingController::class, 'show'])->name('web.company_details');
Route::post('vote/{id}', [App\Http\Controllers\Web\VotingController::class, 'vote'])->name('web.vote');


Route::get('/register/verify', [RegisterController::class, 'showVerifyForm'])->name('web.register.verify.form');
Route::post('/register/verify', [RegisterController::class, 'verify'])->name('web.register.verify');

Route::post('/register/resend-code', [RegisterController::class, 'resendCode'])->name('web.register.resend');

Route::get('/register/verified', [RegisterController::class, 'verifiedPage'])->name('web.register.verified');

Route::post('/register/select-category', [RegisterController::class, 'selectCategory'])->name('register.selectCategory');
Route::get('/register/next', [RegisterController::class, 'nextPage'])->name('register.nextPage');

Route::get('/voting/load-more', [VotingController::class, 'loadMore'])->name('voting.loadMore');
// routes/web.php
Route::get('/thank-you', [RegisterController::class, 'thankYou'])
    ->name('web.thank.you');
Route::get('/contact', [ContactController::class, 'index'])->name('web.contact');

Route::post('/store/contact',[\App\Http\Controllers\Web\ContactController::class, 'store'])->name('web.contact.store');
// Route::get('/webhooks/whatsapp',  [WaWebhookController::class, 'verify']);   // للتحقق (GET)
// Route::post('/webhooks/whatsapp', [WaWebhookController::class, 'receive']);  // للاستقبال (POST)
// صفحة سياسة الخصوصية
Route::view('/privacy', 'privacy')->name('privacy');

// (اختياري) صفحة حذف البيانات المطلوبة من Meta
Route::view('/data-deletion', 'data-deletion')->name('data.deletion');

Route::get('/wa/quick-text', function () {
    $phoneId = env('META_PHONE_NUMBER_ID');
    $token   = env('META_WA_TOKEN');
    $to      = '201288564609'; // رقمك بصيغة دولية
    $url     = "https://graph.facebook.com/v23.0/{$phoneId}/messages";

    $res = Http::withToken($token)->post($url, [
        'messaging_product' => 'whatsapp',
        'to'   => $to,
        'type' => 'text',
        'text' => ['body' => 'اختبار: وصلت؟'],
    ]);

    return response()->json($res->json(), $res->status());
});
Route::get('/wa/test-text', [RegisterController::class, 'testFreeText']);
Route::get('/webhooks/whatsapp', [RegisterController::class, 'whatsappWebhookVerify']);
Route::post('/webhooks/whatsapp', [RegisterController::class, 'whatsappWebhook']);
