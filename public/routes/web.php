<?php
use App\Http\Controllers\authentications\LoginBasic;
use App\Http\Controllers\dashboard\RolesController;
use App\Http\Controllers\dashboard\SettingController;
use App\Http\Controllers\dashboard\Analytics;
use App\Http\Controllers\dashboard\UserController;
use App\Http\Controllers\dashboard\EventController;
use App\Http\Controllers\dashboard\SpeakerController as Speaker;
use App\Http\Controllers\dashboard\SponsorCategoryController;
use App\Http\Controllers\dashboard\SponsorController;
use App\Http\Controllers\dashboard\EventScheduleController;
use App\Http\Controllers\dashboard\MultimediaCategoryController;
use App\Http\Controllers\dashboard\MultiMediaController;
use App\Http\Controllers\dashboard\PackageController;
use App\Http\Controllers\dashboard\BlogController;
use App\Http\Controllers\dashboard\ClientController;
use App\Http\Controllers\dashboard\FormController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\language\LanguageController;
use App\Http\Controllers\dashboard\AdController;
use App\Http\Controllers\dashboard\BecomeSponsorsController;
use App\Http\Controllers\dashboard\GalleryController;
use App\Http\Controllers\dashboard\SeoController;
use App\Http\Controllers\dashboard\HomeSectionController;
use App\Http\Controllers\dashboard\PixelController;
use App\Http\Controllers\dashboard\QrcodeController;
use App\Models\Client;
use App\Models\Event;
use App\Models\HomeSection;
use App\Http\Controllers\API\LeadFormController;
use App\Http\Controllers\dashboard\CompanyController;
// routes/web.php
use App\Http\Controllers\dashboard\RefLinkController;

use App\Http\Controllers\dashboard\MarketingRefController;
use App\Http\Controllers\dashboard\SpeakerScheduleController;
use App\Http\Controllers\dashboard\SpeakerTimeController;
use App\Http\Controllers\dashboard\BookingController;
use App\Http\Controllers\dashboard\AttendanceCompanyController;
use App\Http\Controllers\EditorUploadController;
use App\Http\Controllers\dashboard\LeadController;
use App\Http\Controllers\dashboard\InvitationController;

    Route::delete('multi-medias/{media}/images/{index}', [MultiMediaController::class, 'destroyImage'])
        ->name('multi-medias.images.destroy');

    // حذف لينك واحد من الـ links
    Route::delete('multi-medias/{media}/links/{index}', [MultiMediaController::class, 'destroyLink'])
        ->name('multi-medias.links.destroy');
Route::post('/editor/upload', [EditorUploadController::class, 'store'])
    ->name('editor.upload');
Route::middleware(['auth'])->prefix('dashboard')->group(function () {
    Route::resource('marketing-refs', MarketingRefController::class)->except(['show']);
});
Route::get('/r/{ref:code}', [RefLinkController::class, 'hit'])
    ->name('ref.hit');

// رابط قصير مع مسار مخصص (اختياري)
Route::get('/r/{ref:code}/{path?}', [RefLinkController::class, 'hit'])
    ->where('path', '.*')
    ->name('ref.hit.path');
Route::middleware('auth')
  ->get('/dashboard', [Analytics::class, 'index'])
  ->name('dashboard-analytics');

// Login Route
Route::get('/login', [LoginBasic::class, 'index'])->name('login-basic');
Route::post('login', [LoginBasic::class, 'login'])->name('login');
Route::post('logout', [LoginBasic::class, 'logout'])->name('logout');

// Locale
Route::get('lang/{locale}', [LanguageController::class, 'swap']);

// Main Page Route (Protected)

// Users Routes (Protected)
Route::middleware('auth')
  ->prefix('users')
  ->name('users.')
  ->group(function () {
    Route::get('/dashboard', [UserController::class, 'index'])->name('index');
    Route::post('/dashboard', [UserController::class, 'store'])->name('store');
    Route::put('/dashboard/{id}', [UserController::class, 'update'])->name('update');
    Route::delete('/dashboard/{id}', [UserController::class, 'destroy'])->name('destroy');
    Route::get('/dashboard/export', [UserController::class, 'export'])->name('export');
  });
  Route::prefix('dashboard/companies')->middleware(['auth'])->name('dashboard.companies.')->group(function () {
    Route::get('/', [CompanyController::class, 'index'])->name('index');
    Route::get('create', [CompanyController::class, 'create'])->name('create');
    Route::post('store', [CompanyController::class, 'store'])->name('store');
    Route::get('{company}/edit', [CompanyController::class, 'edit'])->name('edit');
    Route::put('{company}', [CompanyController::class, 'update'])->name('update');
    Route::get('{company}', [CompanyController::class, 'show'])->name('show');
    Route::patch('{id}/activate', [CompanyController::class, 'activate'])->name('activate');
    Route::patch('{id}/deactivate', [CompanyController::class, 'deactivate'])->name('deactivate');
});
Route::middleware('auth')
  ->prefix('showevents')
  ->name('admin.events.')
  ->group(function () {
    Route::get('/', [EventController::class, 'index'])->name('index');
    Route::get('edit/{event}', [EventController::class, 'edit'])->name('edit');
    Route::put('/{event}', [EventController::class, 'update'])->name('update');
    Route::delete('/{event}', [EventController::class, 'destroy'])->name('destroy');
  });
Route::middleware('auth')->group(function () {
  Route::get('/roles', [RolesController::class, 'index'])->name('dashboard-users-roles');
  Route::post('/roles/store', [RolesController::class, 'store'])->name('roles.store');
  Route::put('/roles/{id}', [RolesController::class, 'update'])->name('roles.update');
  Route::delete('/roles/{id}', [RolesController::class, 'destroy'])->name('roles.destroy');
});

// Setting (Protected)
Route::middleware('auth')
  ->prefix('setting')
  ->group(function () {
    Route::get('/', [SettingController::class, 'index'])->name('dashboard-setting');
    Route::post('/', [SettingController::class, 'store'])->name('dashboard-setting-store');
  });



Route::prefix('admin/sponsor_categories')
  ->name('admin.sponsor_categories.')
  ->middleware(['auth']) // غيّر هذا لو عندك guard خاص
  ->group(function () {
    Route::get('/', [SponsorCategoryController::class, 'index'])->name('index');
    Route::get('/create', [SponsorCategoryController::class, 'create'])->name('create');
    Route::post('/', [SponsorCategoryController::class, 'store'])->name('store');
    Route::get('/{sponsorCategory}', [SponsorCategoryController::class, 'show'])->name('show');
    Route::get('/{sponsorCategory}/edit', [SponsorCategoryController::class, 'edit'])->name('edit');
    Route::put('/{sponsorCategory}', [SponsorCategoryController::class, 'update'])->name('update');
    Route::delete('/{sponsorCategory}', [SponsorCategoryController::class, 'destroy'])->name('destroy');
    Route::get('/{sponsorCategory}/deactivate', [SponsorCategoryController::class, 'deactivate'])->name('deactivate');
  });
Route::delete('companies/{company}', [CompanyController::class, 'destroy'])->name('dashboard.companies.destroy');

Route::prefix('admin/sponsors')
  ->as('admin.sponsors.')
  ->middleware(['auth']) // أضف أي وسطاء تحتاجها مثل admin فقط
  ->group(function () {
    Route::get('/', [SponsorController::class, 'index'])->name('index');
    Route::get('/create', [SponsorController::class, 'create'])->name('create');
    Route::post('/', [SponsorController::class, 'store'])->name('store');

    Route::get('/{sponsor}/edit', [SponsorController::class, 'edit'])->name('edit');
    Route::put('/{sponsor}', [SponsorController::class, 'update'])->name('update');
    Route::delete('/{sponsor}', [SponsorController::class, 'destroy'])->name('destroy');

    Route::get('/{sponsor}', [SponsorController::class, 'show'])->name('show');
    Route::get('/{sponsor}/toggle', [SponsorController::class, 'deactivate'])->name('deactivate');
  });
Route::prefix('admin/event_schedules')
  ->as('admin.event_schedules.')
  ->middleware(['auth']) // أضف أي وسطاء تحتاجها مثل admin فقط
  ->group(function () {
    Route::get('/', [EventScheduleController::class, 'index'])->name('index');
    Route::get('/create', [EventScheduleController::class, 'create'])->name('create');
    Route::post('/', [EventScheduleController::class, 'store'])->name('store');

    Route::get('/{eventSchedule}/edit', [EventScheduleController::class, 'edit'])->name('edit');
    Route::put('/{eventSchedule}', [EventScheduleController::class, 'update'])->name('update');
    Route::delete('/{eventSchedule}', [EventScheduleController::class, 'destroy'])->name('destroy');

    Route::get('/{eventSchedule}', [EventScheduleController::class, 'show'])->name('show');
    Route::get('/{eventSchedule}/toggle', [EventScheduleController::class, 'deactivate'])->name('deactivate');
  });

Route::prefix('admin')
  ->name('dashboard.')
  ->middleware(['auth'])
  ->group(function () {
    Route::prefix('multimedia-categories')
      ->name('multimedia-categories.')
      ->group(function () {
        Route::get('/', [MultimediaCategoryController::class, 'index'])->name('index');
        Route::get('/create', [MultimediaCategoryController::class, 'create'])->name('create');
        Route::post('/', [MultimediaCategoryController::class, 'store'])->name('store');

        Route::get('/{id}', [MultimediaCategoryController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [MultimediaCategoryController::class, 'edit'])->name('edit');
        Route::put('/{id}', [MultimediaCategoryController::class, 'update'])->name('update');

        Route::post('/{id}/activate', [MultimediaCategoryController::class, 'activate'])->name('activate');
      });
  });

Route::prefix('admin')
  ->name('dashboard.')
  ->middleware(['auth'])
  ->group(function () {
    Route::prefix('multi-medias')
      ->name('multi-medias.')
      ->group(function () {
        Route::get('/', [MultiMediaController::class, 'index'])->name('index');
        Route::get('/create', [MultiMediaController::class, 'create'])->name('create');
        Route::post('/', [MultiMediaController::class, 'store'])->name('store');
        Route::get('/{id}', [MultiMediaController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [MultiMediaController::class, 'edit'])->name('edit');
        Route::put('/{id}', [MultiMediaController::class, 'update'])->name('update');
        Route::post('/{id}/activate', [MultiMediaController::class, 'activate'])->name('activate');
        Route::delete('/{id}', [MultiMediaController::class, 'destroy'])->name('destroy');
      });
  });

Route::prefix('admin')
  ->as('dashboard.')
  ->middleware(['auth'])
  ->group(function () {
    Route::resource('packages', PackageController::class);
    Route::post('packages/{package}/activate', [PackageController::class, 'activate'])->name('packages.activate');
  });

Route::prefix('admin/blogs')
  ->name('dashboard.blogs.')
  ->middleware('auth')
  ->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('/create', [BlogController::class, 'create'])->name('create');
    Route::post('/', [BlogController::class, 'store'])->name('store');
    Route::get('/{blog}', [BlogController::class, 'show'])->name('show');
    Route::get('/{blog}/edit', [BlogController::class, 'edit'])->name('edit');
    Route::put('/{blog}', [BlogController::class, 'update'])->name('update');
    Route::delete('/{blog}', [BlogController::class, 'destroy'])->name('destroy');

    // زر التفعيل/التعطيل
    Route::post('/{blog}/activate', [BlogController::class, 'activate'])->name('activate');
  });

Route::prefix('admin')
  ->name('admin.')
  ->middleware('auth')
  ->group(function () {
    Route::prefix('forms')
      ->name('forms.')
      ->group(function () {
        Route::get('/', [FormController::class, 'index'])->name('index');
        Route::post('/store', [FormController::class, 'store'])->name('store');
      });
  });
Route::resource('clients', ClientController::class)
  ->middleware('auth')
  ->names('dashboard.clients');
Route::post('clients/import', [ClientController::class, 'importExcel'])
  ->middleware('auth')
  ->name('dashboard.clients.import');
Route::get('all/clients/export', [ClientController::class, 'exportExcel'])
  ->middleware('auth')
  ->name('dashboard.clients.export');
Route::get('dashboard/clients/export/template', [ClientController::class, 'exportTemplate'])->name(
  'dashboard.clients.export.template'
);
Route::get('clients/excel/all', [ClientController::class, 'excel'])
  ->middleware('auth')
  ->name('dashboard.clients.excel');

  //////////////////////////////////////////
  Route::resource('becomesponosrs', BecomeSponsorsController::class)
  ->middleware('auth')
  ->names('dashboard.becomesponsor');
Route::post('becomesponosrs/import', [BecomeSponsorsController::class, 'importExcel'])
  ->middleware('auth')
  ->name('dashboard.becomesponsor.import');
Route::get('all/becomesponosrs/export', [BecomeSponsorsController::class, 'exportExcel'])
  ->middleware('auth')
  ->name('dashboard.becomesponsor.export');
Route::get('dashboard/becomesponosrs/export/template', [BecomeSponsorsController::class, 'exportTemplate'])->name(
  'dashboard.becomesponsor.export.template'
);
Route::get('becomesponosrs/excel/all', [BecomeSponsorsController::class, 'excel'])
  ->middleware('auth')
  ->name('dashboard.becomesponsor.excel');
  //////////////////////////////////////////////////
Route::prefix('dashboard/ads')
  ->name('dashboard.ads.')
  ->middleware(['auth'])
  ->group(function () {
    Route::get('/', [AdController::class, 'index'])->name('index');
    Route::get('/create', [AdController::class, 'create'])->name('create');
    Route::post('/store', [AdController::class, 'store'])->name('store');

    Route::get('/{id}', [AdController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [AdController::class, 'edit'])->name('edit');
    Route::put('/{id}', [AdController::class, 'update'])->name('update');

    Route::post('/{id}/toggle-active', [AdController::class, 'toggleActive'])->name('toggle');
  });

Route::prefix('dashboard')
  ->name('dashboard.')
  ->middleware(['auth'])
  ->group(function () {
    Route::resource('seos', SeoController::class);
    Route::get('home_sections', [HomeSectionController::class, 'index'])
      ->name('home_sections.index')
      ->middleware('auth');

    // إنشاء قسم
    Route::get('home_sections/create', [HomeSectionController::class, 'create'])
      ->name('home_sections.create')
      ->middleware('auth');

    // حفظ القسم الجديد
    Route::post('home_sections', [HomeSectionController::class, 'store'])
      ->name('home_sections.store')
      ->middleware('auth');

    // تعديل القسم
    Route::get('home_sections/{home_section}/edit', [HomeSectionController::class, 'edit'])
      ->name('home_sections.edit')
      ->middleware('auth');

    // تحديث بيانات القسم
    Route::put('home_sections/{home_section}', [HomeSectionController::class, 'update'])
      ->name('home_sections.update')
      ->middleware('auth');

    // تفعيل/إلغاء تفعيل القسم
    Route::put('home_sections/{home_section}/toggle', [HomeSectionController::class, 'toggleActivate'])
      ->name('home_sections.toggle')
      ->middleware('auth');
    Route::resource('galleries', GalleryController::class)->except(['show']);
  });

Route::prefix('dashboard')->name('dashboard.')->middleware(['auth'])->group(function () {

    // Routes for Pixels
    Route::resource('pixels', PixelController::class)->except(['show']);

    // Route for toggling active status
    Route::post('pixels/{pixel}/toggle-active', [PixelController::class, 'toggleActive'])
        ->name('pixels.toggle-active');
});


Route::prefix('dashboard/qrcodes')->middleware(['auth'])->name('dashboard.qrcodes.')->group(function () {
    Route::get('/', [QrcodeController::class, 'index'])->name('index');
    Route::get('/toggle/{id}', [QrcodeController::class, 'toggleStatus'])->name('toggle');
    Route::get('/regenerate/{id}', [QrcodeController::class, 'regenerateAndResend'])->name('regenerate');
});

Route::resource('dashboard/pdfs', \App\Http\Controllers\dashboard\PdfController::class);
Route::post('dashboard/pdfs/{pdf}/toggle-active', [\App\Http\Controllers\dashboard\PdfController::class, 'toggleActive'])->name('pdfs.toggle-active');

// routes/web.php


Route::post('/video-upload-chunk', [HomeSectionController::class, 'uploadChunk'])->name('video.upload.chunk');
Route::get('/qr/{code}', [QrcodeController::class, 'scan'])->name('qr.scan');
// عرض صفحة الكاميرا
Route::get('/qr-scanner', [QrcodeController::class, 'scanner'])->name('qr.scanner');

// التحقق من الكود بعد المسح
Route::post('/qr-scan-check', [QrcodeController::class, 'check'])->name('qr.scan.check');


// routes/web.php
Route::post('admin/upload-image', [EventController::class, 'upload'])->name('admin.upload-image');
// routes/web.php
Route::get('/qrcodes/attendees', [QrcodeController::class, 'attendees'])
    ->name('qrcodes.attendees');


Route::prefix('dashboard')
    ->name('dashboard.')
    ->middleware(['auth']) // عدّل حسب نظام التوثيق عندك
    ->group(function () {

    // عرض جدول المواعيد والحجوزات لسبيكر واحد
    Route::get('speakers/{speaker}/schedule', [SpeakerScheduleController::class, 'show'])
        ->name('speakers.schedule');

    // Times CRUD (بدون حجوزات)
    Route::get('speakers/{speaker}/times/create', [SpeakerScheduleController::class, 'createTime'])
        ->name('speakers.times.create');
    Route::post('speakers/{speaker}/times', [SpeakerScheduleController::class, 'storeTime'])
        ->name('speakers.times.store');

    Route::get('times/{time}/edit', [SpeakerScheduleController::class, 'editTime'])
        ->name('speakers.times.edit');
    Route::put('times/{time}', [SpeakerScheduleController::class, 'updateTime'])
        ->name('speakers.times.update');

    Route::patch('times/{time}/toggle', [SpeakerScheduleController::class, 'toggleTimeActive'])
        ->name('speakers.times.toggle');
    Route::delete('times/{time}', [SpeakerScheduleController::class, 'destroyTime'])
        ->name('speakers.times.destroy');
});


Route::prefix('dashboard')->name('dashboard.')->middleware(['auth'])->group(function () {

    // Time Sheets
    Route::get('speaker-times',              [SpeakerTimeController::class, 'index'])->name('speaker-times.index');
    Route::get('speaker-times/create',       [SpeakerTimeController::class, 'create'])->name('speaker-times.create');
    Route::post('speaker-times',             [SpeakerTimeController::class, 'store'])->name('speaker-times.store');
    Route::get('speaker-times/{time}',       [SpeakerTimeController::class, 'show'])->name('speaker-times.show');
    Route::get('speaker-times/{time}/edit',  [SpeakerTimeController::class, 'edit'])->name('speaker-times.edit');
    Route::put('speaker-times/{time}',       [SpeakerTimeController::class, 'update'])->name('speaker-times.update');
    Route::patch('speaker-times/{time}/toggle', [SpeakerTimeController::class, 'toggleActive'])->name('speaker-times.toggle');
    Route::delete('speaker-times/{time}',    [SpeakerTimeController::class, 'destroy'])->name('speaker-times.destroy');

    // Bookings
    Route::get('bookings',           [BookingController::class, 'index'])->name('bookings.index');
    Route::get('bookings/create',    [BookingController::class, 'create'])->name('bookings.create');
    Route::post('bookings',          [BookingController::class, 'store'])->name('bookings.store');
    Route::get('bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::get('bookings/{booking}/edit', [BookingController::class, 'edit'])->name('bookings.edit');
    Route::put('bookings/{booking}',      [BookingController::class, 'update'])->name('bookings.update');
    Route::delete('bookings/{booking}',   [BookingController::class, 'destroy'])->name('bookings.destroy');
});
Route::middleware(['auth'])
    ->prefix('dashboard/attendance-company')
    ->name('dashboard.attendance_company.')
    ->group(function () {
                Route::get('/template/download', [AttendanceCompanyController::class, 'downloadTemplate'])
            ->name('template');
        Route::get('/',                 [AttendanceCompanyController::class, 'index'])->name('index');
        Route::get('/create',           [AttendanceCompanyController::class, 'create'])->name('create');
        Route::post('/',                [AttendanceCompanyController::class, 'store'])->name('store');
        Route::get('/{attendance_company}/edit', [AttendanceCompanyController::class, 'edit'])->name('edit');
        Route::put('/{attendance_company}',      [AttendanceCompanyController::class, 'update'])->name('update');
        Route::delete('/{attendance_company}',   [AttendanceCompanyController::class, 'destroy'])->name('destroy');

        Route::post('/{attendance_company}/toggle-active',     [AttendanceCompanyController::class, 'toggleActive'])->name('toggleActive');
        Route::post('/{attendance_company}/mark-attendance',   [AttendanceCompanyController::class, 'markAttendance'])->name('markAttendance');
        Route::post('/{attendance_company}/unmark-attendance', [AttendanceCompanyController::class, 'unmarkAttendance'])->name('unmarkAttendance');

        Route::get('/export',  [AttendanceCompanyController::class, 'export'])->name('export');
        Route::post('/import', [AttendanceCompanyController::class, 'import'])->name('import');
    });
    
Route::prefix('dashboard')->name('dashboard.')->middleware(['auth'])->group(function () {
            Route::resource('invitations', InvitationController::class);

        // Import / Export / Template
        Route::post('invitations-import',   [InvitationController::class, 'import'])->name('invitations.import');
        Route::get('invitations-export',    [InvitationController::class, 'export'])->name('invitations.export');
        Route::get('invitations-template',  [InvitationController::class, 'template'])->name('invitations.template');

        // Mark attendance
        Route::patch('invitations/{invitation}/attendance', [InvitationController::class, 'markAttendance'])
            ->name('invitations.attendance');
    Route::get('leads',                 [LeadController::class,'index'])->name('leads.index');
    Route::get('leads/{lead}',          [LeadController::class,'show'])->name('leads.show');
    Route::post('leads',                [LeadController::class,'store'])->name('leads.store');
    Route::put('leads/{lead}',          [LeadController::class,'update'])->name('leads.update');
    Route::delete('leads/{lead}',       [LeadController::class,'destroy'])->name('leads.destroy');
    Route::get('leads/accepted/all',   [LeadController::class, 'accepted'])->name('accepted');      // New

    Route::post('leads/{lead}/assign',  [LeadController::class,'assign'])->name('leads.assign');
    Route::post('leads/{lead}/status',  [LeadController::class,'setStatus'])->name('leads.status');

    Route::post('leads/{lead}/comments',                [LeadController::class,'commentStore'])->name('leads.comments.store');
    Route::delete('lead-comments/{comment}',            [LeadController::class,'commentDestroy'])->name('leads.comments.destroy');

    Route::post('leads/{lead}/calllogs',                [LeadController::class,'callLogStore'])->name('leads.calllogs.store');

    Route::get('leads/export/export',                          [LeadController::class,'export'])->name('leads.export');
    Route::post('leads/import',                         [LeadController::class,'import'])->name('leads.import');
    Route::post('leads/{lead}/accept',                  [LeadController::class,'accept'])->name('leads.accept');
});
Route::prefix('admin/speakers')
  ->name('admin.speakers.')
  ->group(function () {
    Route::get('/{type}', [Speaker::class, 'index'])->name('index');
    Route::get('/create/{type}', [Speaker::class, 'create'])->name('create');
    Route::post('/', [Speaker::class, 'store'])->name('store');
    Route::get('/{type}/{speaker}', [Speaker::class, 'show'])->name('show');
    Route::get('/{type}/edit/{speaker}', [Speaker::class, 'edit'])->name('edit');
        Route::delete('/{speaker}', [Speaker::class, 'destroy'])->name('destroy');
  });
    Route::post('speakets/update/{type}/{speaker}/update', [Speaker::class, 'update'])->name('updatenew');  
  