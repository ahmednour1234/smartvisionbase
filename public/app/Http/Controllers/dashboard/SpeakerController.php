<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\SpeakerRequest;
use App\Models\{Speaker, SpeakerTime, Booking};
use Illuminate\Http\Request;
use App\Repositories\SpeakerRepository;
use App\Helpers\FileHelper;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SpeakerController extends Controller
{
    protected $repo;

    public function __construct(SpeakerRepository $repo)
    {
        $this->repo = $repo;
    }
public function index(Request $request, $type)
{
    $query = Speaker::query();

    // بحث بالاسم
    if ($request->filled('search')) {
        $searchTerm = $request->string('search');
        $query->where(function ($q) use ($searchTerm) {
            $q->where('name_ar', 'like', "%{$searchTerm}%")
              ->orWhere('name_en', 'like', "%{$searchTerm}%");
        });
    }

    // فلترة بالـ section فقط إذا كان type = 2
    if ((int)$type === 2 && $request->filled('section')) {
        $section = $request->input('section');
        $query->where('section', $section); // فلترة المتحدثين بناءً على الـ section
    }

    // تطبيق الفلترة على النوع والفرز
    $speakers = $query->where('type', $type)
                      ->latest()
                      ->paginate(10)
                      ->appends($request->only(['search', 'section'])); // للحفاظ على البارامز في الروابط

    return view('content.speakers.index', compact('speakers', 'type'));
}


public function create($type)
{
    $cacheKey = 'countries.v2'; // غيّرنا المفتاح عشان نمسح القديم الفاضي
    $ttl      = now()->addDays(7);

    // ابدأ باللي في الكاش (إن وجد)
    $countries = Cache::get($cacheKey);

    try {
        $fresh = $this->fetchCountriesFromRest();
        if (!empty($fresh)) {
            $countries = $fresh;
            Cache::put($cacheKey, $countries, $ttl);
        }
        // ملاحظة: لو الـ API رجّع فاضي فعلاً، ما نكتبش فاضي على الكاش.
    } catch (\Throwable $e) {
        // لو فيه فشل: احتفظ بما في الكاش (إن وجد). لا تكتب فاضي على الكاش.
        report($e);
    }

    $countries = $countries ?? [];

    return view('content.speakers.create', [
        'type'      => $type,
        'countries' => $countries,
    ]);
}

/**
 * جلب + تطبيع البيانات من Rest Countries
 * يرمي استثناء لو فشل — حتى لا يكتب remember/put قيمة فاشلة.
 */
private function fetchCountriesFromRest(): array
{
    $res = Http::timeout(10)
        ->acceptJson()
        ->get('https://restcountries.com/v3.1/all?fields=cca2,name,flags,translations')
        ->throw() // أي فشل هيعمل استثناء ولن نخزّن قيمة فاشلة
        ->json();

    return collect($res)
        ->map(function ($c) {
            return [
                'code'     => $c['cca2'] ?? null,                               // ISO-3166 alpha-2
                'name_en'  => data_get($c, 'name.common'),
                'name_ar'  => data_get($c, 'translations.ara.common'),
                'flag_svg' => data_get($c, 'flags.svg'),
                'flag_png' => data_get($c, 'flags.png'),
            ];
        })
        ->filter(fn ($x) => !empty($x['code'])) // لازم كود الدولة
        ->sortBy(fn ($x) => mb_strtolower((string)($x['name_ar'] ?? $x['name_en'] ?? '')))
        ->values()
        ->all();
}

    public function store(SpeakerRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = FileHelper::uploadImage($request->file('image'), 'speakers');
        }
if (!empty($data['password'])) {
    $data['password'] = \Illuminate\Support\Facades\Hash::make($data['password']);
}

        $this->repo->create($data);
        return redirect()->back()->with('success', __('speaker.created'));
    }


public function edit(int $type, Speaker $speaker)
{
    // مفتاح جديد لتفادي الكاش الفاضي المخزّن سابقًا
    $cacheKey = 'countries.v2';
    $ttl      = now()->addDays(7);

    // اقرأ الموجود في الكاش أولًا (إن وُجد)
    $countries = Cache::get($cacheKey);

    try {
        $fresh = $this->fetchCountriesFromRest();
        if (!empty($fresh)) {
            $countries = $fresh;
            Cache::put($cacheKey, $countries, $ttl); // خزّن فقط لو في بيانات
        }
        // لو API رجّع فاضي فعلًا، ما نكتبش فاضي على الكاش
    } catch (\Throwable $e) {
        report($e); // احتفظ بما في الكاش إن وُجد
    }

    $countries = $countries ?? [];

    // تأكد إن السجلّ من نفس النوع
    abort_unless(in_array($type, [1, 2], true), 404);
    if ((int) $speaker->type !== (int) $type) {
        abort(404);
    }

    return view('content.speakers.edit', compact('speaker', 'type', 'countries'));
}

/**
 * يجلب الدول من Rest Countries ويُطبعها، ويرمي استثناء عند الفشل.
 */
// App\Http\Controllers\Dashboard\SpeakerController.php
public function update(int $type, SpeakerRequest $request, Speaker $speaker)
{
    // dd($request->all);
    $data = $request->validated();

    if ($request->hasFile('image')) {
        $data['image'] = FileHelper::uploadImage($request->file('image'), 'speakers');
    }

    // ثبّت النوع لو لزم
    $data['type'] = $type;


    $this->repo->update($speaker, $data);

    return redirect()
        ->back()
        ->with('success', __('speaker.updated'));
}


    public function destroy(Speaker $speaker)
    {
        $this->repo->delete($speaker);
        return redirect()->back()->with('success', __('speaker.deleted'));
    }


public function show(int $type, Request $request, Speaker $speaker)
{
    // أي تبويب نشط؟ (other | times | bookings)
    $activeTab = $request->query('tab', 'other');

    // مواعيد السبيكر
    $times = SpeakerTime::where('speaker_id', $speaker->id)
        ->orderBy('date')
        ->orderBy('time_from')
        ->paginate(10, ['*'], 'times_page');

    // حجوزات السبيكر (للمتابعة فقط)
    $bookings = Booking::with(['client','speakerTime'])
        ->where('speaker_id', $speaker->id)
        ->orderByDesc('time_from')
        ->paginate(10, ['*'], 'bookings_page');

    return view('content.speakers.show', compact('speaker','times','bookings','activeTab','type'));
}
}
