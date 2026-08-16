<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\Blog\BlogCollection;
use App\Http\Resources\Blog\BlogResource;
use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $q = Blog::query()
            ->where('active', 1);

        // بحث اختياري: ?q=
        if ($search = trim((string) $request->query('q', ''))) {
            $q->where(function ($qq) use ($search) {
                $qq->where('name_en', 'LIKE', "%{$search}%")
                   ->orWhere('name_ar', 'LIKE', "%{$search}%")
                   ->orWhere('title_en', 'LIKE', "%{$search}%")
                   ->orWhere('title_ar', 'LIKE', "%{$search}%")
                   ->orWhere('description_en', 'LIKE', "%{$search}%")
                   ->orWhere('description_ar', 'LIKE', "%{$search}%");
            });
        }

        // الترتيب: sort ASC ثم id ASC
        $q->orderBy('sort', 'asc')->orderBy('id', 'asc');

        // Pagination موحّد
        $perPage = (int) $request->query('per_page', 12);
        $perPage = max(1, min($perPage, 50));

        $paginator = $q->paginate($perPage)->appends($request->query());

        return new BlogCollection($paginator);
    }

    public function show(Request $request, Blog $blog)
    {
        // ممنوع عرض غير النشط
        if ((int) $blog->active !== 1) {
            abort(404);
        }

        return new BlogResource($blog);
    }
}
