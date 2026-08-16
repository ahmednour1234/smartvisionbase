<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\Speaker\SpeakerCollection;
use App\Http\Resources\Speaker\SpeakerResource;
use App\Http\Resources\SpeakerTime\SpeakerTimeResource;
use App\Models\Speaker;
use App\Models\SpeakerTime;
use Illuminate\Http\Request;

class SpeakerController extends Controller
{
    /**
     * List speakers (type = 1)
     */
    public function indexSpeakers(Request $request)
    {
        return $this->indexByType($request, 1);
    }

    /**
     * List special guests (same model) (type = 2)
     */
    public function indexSpecialGuests(Request $request)
    {
        return $this->indexByType($request, 2);
    }

    /**
     * Show single speaker by route-model-binding
     */
    public function show(Request $request, Speaker $speaker)
    {
        // استجابة أساسية
        $resource = new SpeakerResource($speaker);

        // إرفاق أقرب أوقات (اختياري)
        if ($request->boolean('include_times')) {
            $limit   = (int) $request->query('limit', 10);
            $status  = $request->query('status', 'available'); // المتاح افتراضيًا
            $active  = $request->query('active');              // null|0|1

            $q = SpeakerTime::query()
                ->where('speaker_id', $speaker->id)
                ->when($status, fn ($qq) => $qq->where('status', $status))
                ->when(!is_null($active), fn ($qq) => $qq->where('active', $request->boolean('active') ? 1 : 0))
                ->whereDate('date', '>=', now()->toDateString())
                ->orderBy('date')
                ->orderBy('time_from')
                ->limit(max(1, min($limit, 50)));

            $times = $q->get();

            // نضيفها تحت مفتاح "times" بدون التأثير على بنية SpeakerResource
            $resource->additional([
                'times' => SpeakerTimeResource::collection($times),
            ]);
        }

        return $resource;
    }

    /**
     * Shared index logic with search + pagination + ordering
     */
    protected function indexByType(Request $request, int $type)
    {
        $q = Speaker::query()->where('type', $type);

        // ===== section filter (direct column `speakers.section`) =====
        // يدعم ?section=3 أو ?section=3,5,8 أو ?section[]=3&section[]=5
        $sectionParam = $request->query('section');

        if (!is_null($sectionParam) && $sectionParam !== '') {
            // إلى مصفوفة IDs صحيحة
            $sectionIds = collect(is_array($sectionParam) ? $sectionParam : explode(',', (string) $sectionParam))
                ->map(fn ($v) => (int) trim((string) $v))
                ->filter(fn ($v) => $v > 0)
                ->unique()
                ->values()
                ->all();

            if (!empty($sectionParam)) {
                $q->where('section', $sectionParam);
            }
        }

        // ===== search by name_ar / name_en (case-insensitive) =====
        if ($search = trim((string) $request->query('name', ''))) {
            $q->where(function ($qq) use ($search) {
                $qq->where('name_en', 'LIKE', "%{$search}%")
                   ->orWhere('name_ar', 'LIKE', "%{$search}%");
            });
        }

        // ===== sorting: orders desc ثم id desc =====
        $q->orderby('orders','asc')->orderByDesc('id');

        // ===== pagination =====
        $perPage = (int) $request->query('per_page', 12);
        $perPage = max(1, min($perPage, 50));

        $paginator = $q->paginate($perPage)->appends($request->query());

        return new SpeakerCollection($paginator);
    }
}
