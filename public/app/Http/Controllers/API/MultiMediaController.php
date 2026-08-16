<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\Multimedia\MultiMediaCollection;
use App\Http\Resources\Multimedia\MultiMediaResource;
use App\Models\MultiMedia;
use Illuminate\Http\Request;

class MultiMediaController extends Controller
{
    public function index(Request $request)
    {
        $q = MultiMedia::query()
            ->where('active', 1)
            ->with(['category' => fn($c) => $c->where('active', 1)]);

        // Filter by category
        if ($categoryId = $request->query('category_id')) {
            $q->where('multi_media_category_id', (int) $categoryId);
        }

        // Optional search by name (ar/en)
        if ($search = trim((string) $request->query('q', ''))) {
            $q->where(function ($qq) use ($search) {
                $qq->where('name_en', 'LIKE', "%{$search}%")
                   ->orWhere('name_ar', 'LIKE', "%{$search}%");
            });
        }

        // Order by date desc then id desc
        $q->orderByDesc('date')->orderByDesc('id');

        // Pagination (cap 50)
        $perPage = max(1, min((int) $request->query('per_page', 12), 50));
        $paginator = $q->paginate($perPage)->appends($request->query());

        return new MultiMediaCollection($paginator);
    }

    public function show(Request $request, MultiMedia $media)
    {
        if (! $media->active) {
            abort(404);
        }

        $media->load(['category' => fn($c) => $c->where('active', 1)]);
        return new MultiMediaResource($media);
    }
}
