<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\LeadFormController;
use App\Http\Controllers\API\ClientApiController;
use App\Http\Controllers\API\SpeakerAuthController;
use App\Http\Controllers\API\SponsorAuthController;
use App\Http\Controllers\API\ClientAuthController;
use App\Http\Controllers\API\SpeakerController;
use App\Http\Controllers\API\SponsorCategoryController;
use App\Http\Controllers\API\SponsorController;
use App\Http\Controllers\API\PackageController;
use App\Http\Controllers\API\BlogController;
use App\Http\Controllers\API\MultimediaCategoryController;
use App\Http\Controllers\API\MultiMediaController;
use App\Http\Controllers\API\HomeSectionController;
use App\Http\Controllers\API\EventController;
use App\Http\Controllers\API\EventScheduleController;
use App\Http\Controllers\API\Client\ClientApiController as bookClientApiController;
use App\Http\Controllers\API\Speaker\SpeakerApiController;
use App\Http\Controllers\API\InfluencerController;
use App\Http\Controllers\API\Chat\ChatRoomController;
use App\Http\Controllers\API\Chat\ChatMessageController;
use App\Http\Controllers\API\BookingController;
use App\Http\Controllers\API\SponsorScheduleController;
use App\Http\Controllers\API\ClientBookingController;
use App\Http\Controllers\API\Chat\DirectChatController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\PublicSettingController;
use App\Http\Controllers\MetaCapiController;
Route::post('/meta/capi', [MetaCapiController::class, 'send'])
    ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

Route::get('/settings', [PublicSettingController::class, 'show']); // public, no auth

Route::prefix('chat/direct')->group(function () {
    // Get / ensure room by booking_id (بدون إنشاء لو غير موجود)
    Route::get('room', [DirectChatController::class, 'showRoom']);            // ?booking_id=123

    // List messages for a booking room

    // Protected (write)
    Route::middleware('auth:client,sanctum,speaker')->group(function () {
        // Create-or-return room using only booking_id (client_id/sponsor_id تُستخرج من جدول bookings)
        Route::post('room', [DirectChatController::class, 'upsertRoom']);      // { booking_id, title? }
    Route::post('messageschat', [DirectChatController::class, 'listMessages']);    // ?booking_id=123&per_page=30
    Route::get('myRooms', [DirectChatController::class, 'myRooms']);    // ?booking_id=123&per_page=30
    Route::post('pusher/auth', [DirectChatController::class, 'pusherAuthorize']);
    // Send a new message in booking chat (sender inferred from auth)
        Route::post('messages', [DirectChatController::class, 'sendMessage']); // { booking_id, message?, attachments?, replied_to_id? }
    });
});

Route::get('speakers',         [SpeakerController::class, 'indexSpeakers']);      // type = 1
Route::get('special-guests',   [SpeakerController::class, 'indexSpecialGuests']); // type = 2
Route::get('speakers/{speaker}', [SpeakerController::class, 'show']);             // show by id
// Sponsor Categories
Route::get('sponsor-categories', [SponsorCategoryController::class, 'index']);
Route::get('sponsor-categories/{category}', [SponsorCategoryController::class, 'show']); // Route Model Binding

// Sponsors
Route::get('sponsors', [SponsorController::class, 'index']);              // ?category_id=&q=&per_page=
Route::get('sponsors/{sponsor}', [SponsorController::class, 'show']);     // returns sponsor + its category
// packages
Route::get('packages', [PackageController::class, 'index']);
Route::get('packages/{package}', [PackageController::class, 'show']);
// Blogs

Route::get('blogs', [BlogController::class, 'index']);
Route::get('blogs/{blog}', [BlogController::class, 'show']);



Route::get('multimedia-categories',            [MultimediaCategoryController::class, 'index']);
Route::get('multimedia-categories/{category}', [MultimediaCategoryController::class, 'show']);

Route::get('multimedia',        [MultiMediaController::class, 'index']); // ?category_id=&q=&per_page=
Route::get('multimedia/{media}',[MultiMediaController::class, 'show']);

Route::get('home/sections', [HomeSectionController::class, 'index']);                 // all sections (keyed)
Route::get('home/sections/{identifier}', [HomeSectionController::class, 'show']);  


Route::get('home/event', [EventController::class, 'current']); // الحدث الحالي (مؤشَّر للهوم)
Route::get('events/{event}', [EventController::class, 'show']); // عرض حدث واحد


Route::get('events/{event}/schedule', [EventScheduleController::class, 'index']); // days + schedules + speakers
Route::get('event-schedule/{schedule}', [EventScheduleController::class, 'show']); // one schedule


Route::get('influencers', [InfluencerController::class, 'index']);
Route::get('influencers/{idOrSlug}', [InfluencerController::class, 'show']);
Route::post('influencers/{id}/vote', [InfluencerController::class, 'vote']);

Route::get('schedules/sponsors', [BookingController::class, 'publicSchedules']);


Route::post('/clients/register', [ClientApiController::class, 'store']);
Route::post('/lead-form', [LeadFormController::class, 'store']);
Route::post('/dashboard/clients/{id}/send-code', [ClientApiController::class, 'resendQrCode'])->name('dashboard.clients.sendCode');
Route::match(['get', 'post'], '/lead-form', [LeadFormController::class, 'handle']);


Route::prefix('chat')->group(function () {
    // Rooms

    // Messages
    Route::get('rooms/{idOrSlug}/messages',  [ChatMessageController::class, 'index']);
    Route::post('rooms/{idOrSlug}/messages', [ChatMessageController::class, 'store']);
});

Route::prefix('chat')->group(function () {
    // Rooms (public reads)
    Route::get('rooms', [ChatRoomController::class, 'index']);
    Route::get('rooms/{idOrSlug}', [ChatRoomController::class, 'show']);
    Route::get('rooms/{idOrSlug}/participants', [ChatRoomController::class, 'participants']);

    // Messages (public read, auth write)
    Route::get('rooms/{idOrSlug}/messages', [ChatMessageController::class, 'index']);

    // Auth-required actions (client|sanctum|speaker)
    Route::middleware('auth:client,sanctum,speaker')->group(function () {
        // Rooms (mutations)
        Route::post('rooms', [ChatRoomController::class, 'store']);
        Route::patch('rooms/{idOrSlug}', [ChatRoomController::class, 'update']);
        Route::delete('rooms/{idOrSlug}', [ChatRoomController::class, 'destroy']);
        Route::post('rooms/join', [ChatRoomController::class, 'join']);
        Route::post('rooms/{idOrSlug}/leave', [ChatRoomController::class, 'leave']);

        // Messages (create)
        Route::post('rooms/{idOrSlug}/messages', [ChatMessageController::class, 'store']);
    });
});

Route::prefix('speaker')->group(function () {
    Route::post('/register', [SpeakerAuthController::class, 'register']);
    Route::post('/login',    [SpeakerAuthController::class, 'login']);

    Route::middleware('auth:speaker')->group(function () {
            // My time slots
    Route::get('times',           [SpeakerApiController::class, 'myTimes']);

    // My bookings + filters
    Route::get('bookings',        [SpeakerApiController::class, 'myBookings']);
    Route::get('bookings/next',   [SpeakerApiController::class, 'nextBooking']);
    Route::get('bookings/{booking}', [SpeakerApiController::class, 'showBooking'])->whereNumber('booking');

    // Approve / Reject
    Route::post('bookings/{booking}/status', [SpeakerApiController::class, 'updateBookingStatus'])->whereNumber('booking');
        Route::get('/me',                 [SpeakerAuthController::class, 'me']);
        Route::post('/logout',            [SpeakerAuthController::class, 'logout']);
        Route::post('/profile',            [SpeakerAuthController::class, 'updateProfile']);
        Route::post('/password',           [SpeakerAuthController::class, 'updatePassword']);
    });
});
Route::prefix('sponsor')->group(function () {
    Route::post('register', [SponsorAuthController::class, 'register']);
    Route::post('login',    [SponsorAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me',            [SponsorAuthController::class, 'me']);
        Route::post('password',     [SponsorAuthController::class, 'updatePassword']);
        Route::post('profile',      [SponsorAuthController::class, 'updateProfile']);
        Route::post('logout',       [SponsorAuthController::class, 'logout']);
        Route::get('/schedule', [SponsorScheduleController::class, 'index']);
            Route::get('/bookings', [BookingController::class, 'indexForSponsor']);

        Route::post('/bookings/{id}/accept', [BookingController::class, 'accept']);
    Route::post('/bookings/{id}/reject', [BookingController::class, 'reject']);
    });
});


Route::prefix('client')->group(function () {
    Route::post('register',         [ClientAuthController::class, 'register']);
    Route::post('login',            [ClientAuthController::class, 'login']);
    Route::post('verify',           [ClientAuthController::class, 'verify']);         // email + 6-digit code

    Route::post('forgot-password',  [ClientAuthController::class, 'forgotPassword']); // send code
    Route::post('reset-password',   [ClientAuthController::class, 'resetPassword']);  // email + code + new password
        Route::post('send-verification-code', [ClientAuthController::class, 'sendVerificationCode']); // للإيميلات الرسمية فقط
        Route::post('verify-official',        [ClientAuthController::class, 'verifyOfficial']);       // تفعيل مباشر رسمي فقط

    Route::middleware('auth:client,sanctum,sponsor')->group(function () {
            Route::get('speakers/{speaker}/times', [bookClientApiController::class, 'speakerTimes'])->whereNumber('speaker');
    Route::get('/qrcode', [ClientAuthController::class, 'myQrcode']);
    Route::get('/bookings', [ClientBookingController::class, 'index']);
   Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('bookings/{id}',    [BookingController::class, 'show']);
    // Client bookings (mine)
    // Route::get('bookings',        [bookClientApiController::class, 'myBookings']);
    // Route::get('bookings/{booking}', [bookClientApiController::class, 'showBooking'])->whereNumber('booking');

    // Create booking
    // Route::post('bookings',       [bookClientApiController::class, 'createBooking']);
        Route::post('update',        [ClientAuthController::class, 'updateProfile']);
        Route::get('me',            [ClientAuthController::class, 'me']);
        Route::post('logout',       [ClientAuthController::class, 'logout']);
                Route::post('image',       [ClientAuthController::class, 'updateImage']);

    });
});

Route::middleware('auth:client,speaker,sponsor,sanctum')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
});