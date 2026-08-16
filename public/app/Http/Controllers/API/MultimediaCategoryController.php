<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\Multimedia\MultimediaCategoryCollection;
use App\Http\Resources\Multimedia\MultimediaCategoryResource;
use App\Models\MultimediaCategory;
use Illuminate\Http\Request;

class MultimediaCategoryController extends Controller
{
    public function index(Request $request)
    {
        $q = MultimediaCategory::query()->where('active', 1);

        // Optional search on name/description
        if ($search = trim((string) $request->query('q', ''))) {
            $q->where(function ($qq) use ($search) {
                $qq->where('name_en', 'LIKE', "%{$search}%")
                   ->orWhere('name_ar', 'LIKE', "%{$search}%")
                   ->orWhere('description_en', 'LIKE', "%{$search}%")
                   ->orWhere('description_ar', 'LIKE', "%{$search}%");
            });
        }

        // Sort (newest first by id)
        $q->orderByDesc('id');

        // Pagination (cap 50)
        $perPage = max(1, min((int) $request->query('per_page', 12), 50));
        $paginator = $q->paginate($perPage)->appends($request->query());

        return new MultimediaCategoryCollection($paginator);
    }

    public function show(Request $request, MultimediaCategory $category)
    {
        if ((int) $category->active !== 1) {
            abort(404);
        }
        return new MultimediaCategoryResource($category);
    }
}
