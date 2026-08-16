<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Sponsor\SponsorCollection;
use App\Http\Resources\Sponsor\SponsorResource;
use App\Models\Sponsor;
use Illuminate\Http\Request;

class SponsorController extends Controller
{
    public function index(Request $request)
    {
        $q = Sponsor::query()
            ->with(['category' => function ($qq) {
                $qq->where('active', 1);
            }]);

        // فلترة تفعيل السجل من جدول sponsors صراحة
        $q->where('sponsors.active', 1);

        // فلترة بالتصنيف
        if ($categoryId = $request->query('category_id')) {
            $q->where('sponsors.category_sponsor_id', (int) $categoryId);
        }

        // بحث بالاسم/العنوان/الشركة (ar/en)
        if ($search = trim((string) $request->query('name', ''))) {
            $q->where(function ($qq) use ($search) {
                $qq->where('sponsors.name_en', 'LIKE', "%{$search}%")
                   ->orWhere('sponsors.name_ar', 'LIKE', "%{$search}%")
                   ->orWhere('sponsors.title_en', 'LIKE', "%{$search}%")
                   ->orWhere('sponsors.title_ar', 'LIKE', "%{$search}%")
                   ->orWhere('sponsors.company_name_en', 'LIKE', "%{$search}%")
                   ->orWhere('sponsors.company_name_ar', 'LIKE', "%{$search}%");
            });
        }

        // ترتيب داخل التصنيف؛ نستخدم JOIN مع select للسماح بالـ orderBy على جدول sc
        $q->join('sponsor_categories as sc', 'sc.id', '=', 'sponsors.category_sponsor_id')
          ->where('sc.active', 1)
          ->select('sponsors.*') // مهم لتجنّب التباس الأعمدة أثناء الـ pagination/hydration
          ->orderBy('sc.orders', 'asc')        // ترتيب حسب التصنيف
          ->orderBy('sponsors.orders', 'asc')  // ثم داخل التصنيف
          ->orderBy('sponsors.created_at', 'asc')
          ->orderByDesc('sponsors.id');

        // Pagination موحّد
        $perPage = (int) $request->query('per_page', 12);
        $perPage = max(1, min($perPage, 50));

        $paginator = $q->paginate($perPage)->appends($request->query());

        return new SponsorCollection($paginator);
    }

    public function show(Request $request, Sponsor $sponsor)
    {
        $sponsor->load(['category' => fn ($q) => $q->where('active', 1)]);
        return new SponsorResource($sponsor);
    }
}
