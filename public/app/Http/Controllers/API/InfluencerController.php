<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\Influencer\InfluencerCollection;
use App\Http\Resources\Influencer\InfluencerResource;
use App\Models\Company;
use App\Models\Setting;
use App\Models\Voting;
use Illuminate\Http\Request;

class InfluencerController extends Controller
{
    /**
     * GET /api/influencers
     * Params:
     *  - per_page (int)      : افتراضي 25
     *  - page (int)          : افتراضي 1
     *  - active (bool)       : افتراضي true
     *  - category (string)   : فلتر بالفئة
     *  - sort (string)       : orders|created_at|count_vote|number_of_followers
     *  - dir (string)        : asc|desc (افتراضي asc لـ orders، وdesc لغيرها)
     */
public function index(Request $request)
{
    $perPage  = (int) $request->integer('per_page', 25);
    $active   = $request->has('active') ? $request->boolean('active') : true;
    $category = $request->query('category');

    // NEW: search by English name
    $name = trim((string) $request->query('name', ''));

    $sort = $request->query('sort', 'orders');
    $dir  = strtolower($request->query('dir', $sort === 'orders' ? 'asc' : 'desc'));
    if (! in_array($dir, ['asc','desc'], true)) {
        $dir = 'asc';
    }

    $q = Company::query()
        ->when($active !== null, fn($qq) => $qq->where('active', $active ? 1 : 0))
        ->when($category, fn($qq) => $qq->where('category', $category))
        // NEW: filter by name_en LIKE %name%
        ->when($name !== '', function ($qq) use ($name) {
            // نهرب % و _ عشان ما تبوّظش البحث
            $needle = str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $name);
            $qq->where('name_en', 'LIKE', "%{$needle}%");
        });

    // حماية من الأعمدة غير المعروفة
    $sortable = ['orders','created_at','count_vote','number_of_followers'];
    $sortCol  = in_array($sort, $sortable, true) ? $sort : 'orders';

    $paginator = $q->orderBy('orders', 'asc')
        ->paginate($perPage)
        ->appends($request->query());

    return new InfluencerCollection($paginator);
}


    /**
     * GET /api/influencers/{idOrSlug}
     * يدعم {id} أو {name_en} كـ slug
     */
    public function show(Request $request, string $idOrSlug)
    {
        $company = Company::query()
            ->where(function ($qq) use ($idOrSlug) {
                $qq->where('id', is_numeric($idOrSlug) ? (int)$idOrSlug : 0)
                   ->orWhere('name_en', $idOrSlug);
            })
            ->firstOrFail();

        return new InfluencerResource($company);
    }

    /**
     * POST /api/influencers/{id}/vote
     * Throttle: نفس الـ IP على نفس الشركة كل 30 دقيقة
     */
    public function vote(Request $request, int $id)
    {
        // 0) التحقق من فتح/إغلاق التصويت من الإعدادات
        $votingEnabled = (int) (Setting::query()->value('voting') ?? 0);
        if ($votingEnabled !== 1) {
            return response()->json([
                'message' => 'Voting is now closed. Thank you.',
            ], 403);
        }

        // 1) الشركة و IP
        $company = Company::findOrFail($id);
        $ip = $request->ip();

        // 2) منع تكرار التصويت خلال 30 دقيقة
        $recent = Voting::where('company_id', $company->id)
            ->where('ip_address', $ip)
            ->where('created_at', '>=', now()->subMinutes(30))
            ->first();

        if ($recent) {
            return response()->json([
                'message' => 'You have already voted for this Influencer',
            ], 429);
        }

        // 3) إنشاء التصويت وتحديث العداد
        Voting::create([
            'company_id' => $company->id,
            'ip_address' => $ip,
        ]);
        $company->increment('count_vote');

        return response()->json([
            'message'     => 'Thanks for voting for ' . ($company->name_en ?? 'this influencer') . '!',
            'company_id'  => $company->id,
            'count_vote'  => (int) $company->fresh()->count_vote,
        ]);
    }
}
