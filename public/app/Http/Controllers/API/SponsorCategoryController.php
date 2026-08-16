<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\Sponsor\SponsorCategoryCollection;
use App\Http\Resources\Sponsor\SponsorCategoryResource;
use App\Models\SponsorCategory;
use Illuminate\Http\Request;

class SponsorCategoryController extends Controller
{
    public function index(Request $request)
    {
        $q = SponsorCategory::query()
            ->where('active', 1);

        // بحث بالاسم العربي/الإنجليزي
        if ($search = trim((string) $request->query('q', ''))) {
            $q->where(function ($qq) use ($search) {
                $qq->where('name_en', 'LIKE', "%{$search}%")
                   ->orWhere('name', 'LIKE', "%{$search}%"); // name = العربي حسب موديلك
            });
        }

        // ترتيب افتراضي
        $q->orderByDesc('orders')->orderByDesc('id');

        // Pagination موحّد
        $perPage = (int) $request->query('per_page', 12);
        $perPage = max(1, min($perPage, 50));

        $paginator = $q->paginate($perPage)->appends($request->query());

        return new SponsorCategoryCollection($paginator);
    }

    public function show(Request $request, SponsorCategory $category)
    {
        return new SponsorCategoryResource($category);
    }
}
