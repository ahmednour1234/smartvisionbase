<?php
use App\Http\Controllers\authentications\LoginBasic;
use App\Http\Controllers\dashboard\RolesController;
use App\Http\Controllers\dashboard\SettingController;
use App\Http\Controllers\dashboard\Analytics;
use App\Http\Controllers\dashboard\UserController;
use App\Http\Controllers\dashboard\EventController;
use App\Http\Controllers\dashboard\SpeakerController;
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
use App\Http\Controllers\dashboard\LeadController;
use App\Models\Client;
use App\Models\Event;
use App\Models\HomeSection;
use App\Http\Controllers\EditorUploadController;
Route::post('/editor/upload', [EditorUploadController::class, 'store'])
    ->name('editor.upload');
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

Route::prefix('admin/speakers')
  ->name('admin.speakers.')
  ->middleware(['auth']) // أو ['auth:admin'] لو عندك guard خاص بالإدمن
  ->group(function () {
    Route::get('/', [SpeakerController::class, 'index'])->name('index');
    Route::get('/create', [SpeakerController::class, 'create'])->name('create');
    Route::post('/', [SpeakerController::class, 'store'])->name('store');
    Route::get('/{speaker}/edit', [SpeakerController::class, 'edit'])->name('edit');
    Route::get('/{speaker}', [SpeakerController::class, 'show'])->name('show');
    Route::put('/{speaker}', [SpeakerController::class, 'update'])->name('update');
    Route::delete('/{speaker}', [SpeakerController::class, 'destroy'])->name('destroy');
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
             Route::delete('/{id}/destroy', [MultiMediaController::class, 'destroy'])->name('destroy');

        Route::delete('/multi-medias/{id}/image', [MultiMediaController::class, 'destroyImage'])->name('images.destroy');
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

Route::prefix('dashboard')->middleware(['auth'])->name('dashboard.')->group(function () {
    Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');   // صفحة الفلاتر والإحصائيات
    Route::get('/leads/{id}', [LeadController::class, 'show'])->name('leads.show'); // صفحة التفاصيل
});

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
