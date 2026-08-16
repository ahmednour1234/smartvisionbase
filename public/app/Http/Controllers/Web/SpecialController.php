<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\HomeSection;
use App\Models\Speaker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

class SpecialController extends Controller
{
    public function index(Request $request)
    {
        // سيكشن الهيدر (مثال)
        $speaker_section = HomeSection::where('is_active', true)->where('id', 4)->first();

        // اجمع كل باراميترات الرابط الحالية كي نعيد استخدامها في روابط الترقيم
        $section = (string) $request->query('section', 'special');   // aps | special
        $rawQ    = (string) $request->query('q', '');
        $q       = trim($rawQ);

        $perPage = (int) $request->integer('per_page', 12);
        if ($perPage < 6 || $perPage > 48) {
            $perPage = 12;
        }

        // نحضّر مصفوفة appends من باراميترات الطلب الحالية، مع ضبط القيم المنظّفة
        $appends          = $request->query(); // كل الموجود
        $appends['q']     = $q;                // نظّفنا q
        $appends['section']  = $section;       // اضمن وجود section
        $appends['per_page'] = $perPage;       // اضمن per_page الصحيح

        // RANDOM() لـ PostgreSQL, RAND() لـ MySQL/MariaDB
        $driver   = DB::connection()->getDriverName();
        $randFunc = $driver === 'pgsql' ? 'RANDOM()' : 'RAND()';

        // الاستعلام
        $speakers = Speaker::query()
            ->where('active', true)
            ->where('section', $section)
            ->where('type', 2) // Special Guest
            ->when($q !== '', function ($w) use ($q) {
                $w->where(function ($s) use ($q) {
                    $s->where('name_ar',  'like', "%{$q}%")
                      ->orWhere('name_en', 'like', "%{$q}%")
                      ->orWhere('title_ar','like', "%{$q}%")
                      ->orWhere('title_en','like', "%{$q}%");
                });
            })
            // 1) VIP أولاً
            // 2) داخل VIP: ترتيب عشوائي
            // 3) غير VIP: حسب orders ASC
            ->orderByRaw("
                vip DESC,
                CASE WHEN vip = 1 THEN {$randFunc} END,
                CASE WHEN vip = 0 THEN orders END ASC,
                id DESC
            ")
            ->paginate($perPage)
            ->appends($appends);

        // جلب الدول/الأعلام مع كاش
        $cacheKey = 'countries.v2';
        try {
            $countries = Cache::remember($cacheKey, now()->addDays(7), function () {
                $res = Http::timeout(10)
                    ->acceptJson()
                    ->get('https://restcountries.com/v3.1/all?fields=cca2,name,flags,translations')
                    ->throw()
                    ->json();

                return collect($res)
                    ->map(function ($c) {
                        return [
                            'code'     => $c['cca2'] ?? null,
                            'name_en'  => data_get($c, 'name.common'),
                            'name_ar'  => data_get($c, 'translations.ara.common'),
                            'flag_svg' => data_get($c, 'flags.svg'),
                            'flag_png' => data_get($c, 'flags.png'),
                        ];
                    })
                    ->filter(fn ($x) => !empty($x['code']))
                    ->sortBy(fn ($x) => mb_strtolower((string)($x['name_ar'] ?? $x['name_en'] ?? '')))
                    ->values()
                    ->all();
            });
        } catch (Throwable $e) {
            report($e);
            $countries = Cache::get($cacheKey, []);
        }

        // خريطة code => flag
        $countryFlags = collect($countries)->mapWithKeys(function ($c) {
            $code = strtoupper($c['code'] ?? '');
            $flag = $c['flag_svg'] ?? ($c['flag_png'] ?? null);
            return $code ? [$code => $flag] : [];
        })->all();

        // أيقونات السوشيال (أسماء الحقول على الموديل)
        $socialPlatforms = [
            'facebook'  => 'facebook-f',
            'twitter'   => 'twitter',
            'linkedin'  => 'linkedin',
            'youtube'   => 'youtube-play',
            'tiktok'    => 'tiktok',     // لو FA6 متاح
            'instgram'  => 'instagram',  // اسم الحقل عندك "instgram"
        ];

        // استجابة AJAX (تحميل لا نهائي/بحث): نرجّع HTML + next_url
        if ($request->ajax()) {
            $html = view('web.content.partials._cards_special', [
                'speakers'        => $speakers,
                'countryFlags'    => $countryFlags,
                'socialPlatforms' => $socialPlatforms,
                'locale'          => app()->getLocale(),
            ])->render();

            return response()->json([
                'html'     => $html,
                'next_url' => $speakers->nextPageUrl(), // يخرج مع نفس الـ appends
            ]);
        }

        // متغيرات إضافية للواجهة
        $total      = $speakers->total();
        $page       = $speakers->currentPage();
        $totalPages = $speakers->lastPage();

        return view('web.content.special', compact(
            'speaker_section',
            'speakers',
            'countries',
            'countryFlags',
            'socialPlatforms',
            'total',
            'totalPages',
            'page',
            'q',
            'section'
        ));
    }
}
