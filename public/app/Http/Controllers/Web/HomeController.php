<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Event;
use App\Models\Gallery;
use App\Models\HomeSection;
use App\Models\Speaker;
use App\Models\Sponsor;
use App\Models\EventSchedule;
use App\Models\Company;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    public function index()
    {
        $locale = app()->getLocale();

        // TTL للكاش
        $ttl = now()->addMinutes(1);

        // مفتاح للإلغاء التلقائي عند أي تعديل في جدول home_sections
        $sectionsVersion = Cache::remember('home:sections:version', $ttl, function () {
            return (string) (HomeSection::query()->max('updated_at') ?? now());
        });

        // 1) أقسام الصفحة الرئيسية (مرتبطة + مفعّلة)
        // ملاحظة: متعمّد نرجع Collection مع key=id عشان يسهل الوصول السريع
        $sections = Cache::remember("home:sections:{$sectionsVersion}:{$locale}", $ttl, function () {
            return HomeSection::query()
                ->select('id', 'title', 'description', 'media_type', 'media_path', 'section_order', 'is_active', 'thumbnail')
                ->where('is_active', true)
                ->orderBy('section_order')
                ->get()
                ->keyBy('id');
        });

        $home_slider      = $sections->get(1);
        $promo_section    = $sections->get(2);
        $about_section    = $sections->get(3);
        $speaker_section  = $sections->get(4);
        $schedule_section = $sections->get(5);
        $gallery_section  = $sections->get(6);
        $sponsor_section  = $sections->get(7);
        $blog_section     = $sections->get(8);
        $full_promo       = $sections->get(9);
        // القسم 10 (voting) هنتعامل معه في الواجهة حسب is_active
        // القسم 11 (إحصائيات/ضيوف مميّزين) هيظهر حسب البيانات.

        // 2) الشركات (أعمدة أقل + ترتيب)
        $perPage   = 8;
        $companies = Cache::remember('home:companies', $ttl, function () {
            return Company::query()
                ->select('id', 'name_en','name_ar', 'image', 'orders', 'active')
                ->where('active', 1)
                ->orderBy('orders', 'asc')
                ->get();
        });

        // 3) الحدث الحالي (حسب منطقك: الأقدم/الأحدث… هنا أخدنا الأحدث)
        $event = Cache::remember('home:event', $ttl, function () {
            return Event::query()
                ->orderByDesc('id')
                ->first();
        });

        // 4) المتحدثون
        $speakers = Cache::remember('home:speakers', $ttl, function () {
            return Speaker::query()
                ->select('id', 'name_en', 'name_ar', 'image', 'orders', 'active', 'type','title_en','title_ar')
                ->where('active', true)
                ->where('type', 1)                 // متحدثين
                ->orderBy('orders', 'asc')
                ->limit(6)
                ->get();
        });

        // ضيوف مميزون (إصلاح orWhere مع تجميع)
        $specialGuestsKey = 'home:special_guests:' . now()->format('YmdH');
        $special_guests   = Cache::remember($specialGuestsKey, now()->addHour(), function () {
            return Speaker::query()
                ->select('id', 'name_en', 'name_ar', 'image', 'orders', 'active', 'type', 'number_of_followers')
                ->where('active', true)
                ->where('type', 2)
                ->where(function ($q) {
                    // لو number_of_followers رقم ≥ 1,000,000 أو نص فيه 'm' (زي 1.2M)
                    $q->where('number_of_followers', '>=', 1000000)
                      ->orWhereRaw("LOWER(COALESCE(number_of_followers,'')) LIKE '%m%'");
                })
                ->inRandomOrder()
                ->limit(6)
                ->get();
        });

        // 5) جدول الفعاليات (أول 4 أيام * 4 جلسات/يوم)
        $eventDays = [];
        $schedules = collect();

        if ($event) {
            $days = Cache::remember("home:event:{$event->id}:days", $ttl, function () use ($event) {
                return EventSchedule::query()
                    ->where('event_id', $event->id)
                    ->orderBy('start_datetime')
                    ->selectRaw('DATE(start_datetime) as d')
                    ->distinct()
                    ->limit(4)
                    ->pluck('d');
            });

            if ($days->isNotEmpty()) {
                $schedKey = "home:event:{$event->id}:schedules:" . md5($days->join(','));
                $allSchedules = Cache::remember($schedKey, $ttl, function () use ($event, $days) {
                    return EventSchedule::query()
                        ->select('id', 'event_id',        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'location_ar',
        'location_en', 'start_datetime', 'end_datetime','logo')
                        ->with(['speakers:id,name_en']) // قلّل الأعمدة
                        ->where('event_id', $event->id)
                        ->whereIn(DB::raw('DATE(start_datetime)'), $days->all())
                        ->orderBy('start_datetime')
                        ->get();
                });

                $grouped = $allSchedules
                    ->groupBy(fn ($s) => Carbon::parse($s->start_datetime)->toDateString())
                    ->map(fn ($items) => $items->take(4));

                foreach ($days as $d) {
                    $eventDays[$d] = Carbon::parse($d)->translatedFormat('j F Y');
                    if ($grouped->has($d)) {
                        $schedules = $schedules->merge($grouped->get($d));
                    }
                }
            }
        }

        // 6) المعرض/الرعاة/المدونة
        $galleries = Cache::remember('home:galleries', $ttl, function () {
            return Gallery::query()
                ->select('id','image')
                ->where('active', true)
                ->orderByDesc('created_at')
                ->limit(1)
                ->get();
        });

        $sponsors = Cache::remember('home:sponsors', $ttl, function () {
            return Sponsor::query()
                ->select('sponsors.id', 'sponsors.name_ar','sponsors.name_en','sponsors.title_ar','sponsors.title_en', 'sponsors.image', 'sponsors.orders', 'sponsors.active', 'sponsors.category_sponsor_id')
                ->join('sponsor_categories as sc', 'sc.id', '=', 'sponsors.category_sponsor_id')
                ->where('sponsors.active', true)
                ->where('sc.active', true)
                ->orderBy('sc.orders', 'asc')        // ترتيب حسب التصنيف
                ->orderBy('sponsors.orders', 'asc')  // ثم داخل التصنيف
                ->orderBy('sponsors.created_at', 'asc')
                ->limit(100)
                ->get();
        });

        // مدونة (من غير كاش لو محتاج paginate)
        $blogs = Blog::query()
            ->select('id',     'name_ar', 'name_en', 'title_ar', 'title_en',
        'description_ar', 'description_en', 'image', 'created_at', 'active')
            ->where('active', true)
            ->orderByDesc('created_at')
            ->paginate(3);

        // متغير الواجهة المتوافق (gallieries)
        $gallieries = $galleries;

        // 7) الدول (مع import صحيح لـ Http وإرجاع باللّغة)
        $countriesKey = "countries.v2.{$locale}";
        try {
            $countries = Cache::remember($countriesKey, now()->addDays(7), function () use ($locale) {
                $res = Http::timeout(10)
                    ->acceptJson()
                    ->get('https://restcountries.com/v3.1/all?fields=cca2,name,flags,translations')
                    ->throw()
                    ->json();

                return collect($res)->map(function ($c) use ($locale) {
                        return [
                            'code'     => $c['cca2'] ?? null,
                            'name_en'  => data_get($c, 'name.common'),
                            'name_ar'  => data_get($c, 'translations.ara.common'),
                            'flag_svg' => data_get($c, 'flags.svg'),
                            'flag_png' => data_get($c, 'flags.png'),
                        ];
                    })
                    ->filter(fn ($x) => !empty($x['code']))
                    ->sortBy(fn ($x) => mb_strtolower((string)($locale === 'ar' ? ($x['name_ar'] ?? $x['name_en']) : ($x['name_en'] ?? $x['name_ar']))))
                    ->values()
                    ->all();
            });
        } catch (\Throwable $e) {
            report($e);
            $countries = Cache::get($countriesKey, []);
        }

        return view('web.content.home', compact(
            'home_slider',
            'sections',
            'promo_section',
            'about_section',
            'event',
            'speakers',
            'speaker_section',
            'eventDays',
            'schedules',
            'schedule_section',
            'gallieries',
            'gallery_section',
            'sponsor_section',
            'sponsors',
            'blogs',
            'blog_section',
            'full_promo',
            'companies',
            'perPage',
            'special_guests',
            'countries'
        ));
    }
}
