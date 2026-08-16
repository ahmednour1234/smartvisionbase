<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Home\HomeSectionResource;
use App\Models\HomeSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeSectionController extends Controller
{
    /**
     * خريطة المفاتيح الثابتة لأقسام الهوم (عدّل الأسماء لو تحب)
     */
    protected function keysMap(): array
    {
        return [
            1  => ['key' => 'home_slider',      'label' => 'Homepage Slider'],
            2  => ['key' => 'promo_section',    'label' => 'Top Promo'],
            3  => ['key' => 'about_section',    'label' => 'About'],
            4  => ['key' => 'speaker_section',  'label' => 'Speakers'],
            5  => ['key' => 'schedule_section', 'label' => 'Schedule'],
            6  => ['key' => 'gallery_section',  'label' => 'Gallery'],
            7  => ['key' => 'sponsor_section',  'label' => 'Sponsors'],
            8  => ['key' => 'blog_section',     'label' => 'Blog'],
            9  => ['key' => 'full_promo',       'label' => 'Full Promo'],
            10 => ['key' => 'section_10',       'label' => 'Extra Section 10'],
            11 => ['key' => 'section_11',       'label' => 'Extra Section 11'],
        ];
    }

    /**
     * GET /api/home/sections
     * يرجّع Object مفهرس بالمفاتيح (لا يوجد pagination)
     */
    public function index(Request $request)
    {
        $ids   = array_keys($this->keysMap());
        $ttl   = now()->addMinutes(1);

        // بنخزّن الـ collection نفسها (Active + Order)
        $sections = Cache::remember('home:sections:v1', $ttl, function () use ($ids) {
            return HomeSection::query()
                ->where('is_active', true)
                ->whereIn('id', $ids)
                ->orderBy('section_order')
                ->get()
                ->keyBy('id');
        });

        // نعمل payload مفهرس حسب المفاتيح الثابتة
        $payload = [];
        foreach ($this->keysMap() as $id => $info) {
            $model = $sections->get($id);
            $payload[$info['key']] = $model
                ? (new HomeSectionResource($model))->toArray($request)
                : null; // لو مش Active أو مش موجود
        }

        // نرجّع كمان تعريف المفاتيح ليسهّل على الـ Front
        return response()->json([
            'data' => $payload,
            'keys' => $this->keysMap(),
        ]);
    }

    /**
     * GET /api/home/sections/{identifier}
     * identifier = key من keysMap أو رقم id
     */
    public function show(Request $request, string $identifier)
    {
        // حوّل المفتاح إلى id إن لزم
        $map = $this->keysMap();
        $id  = null;

        if (is_numeric($identifier)) {
            $id = (int) $identifier;
        } else {
            // ابحث بالمفتاح
            foreach ($map as $mid => $info) {
                if ($info['key'] === $identifier) {
                    $id = $mid; break;
                }
            }
        }

        if (!$id) abort(404);

        $section = HomeSection::query()
            ->where('id', $id)
            ->where('is_active', true)
            ->firstOrFail();

        return new HomeSectionResource($section);
    }
}
