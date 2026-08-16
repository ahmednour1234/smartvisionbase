<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\Package\PackageCollection;
use App\Http\Resources\Package\PackageResource;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $q = Package::query()->where('active','1')
            ->when($request->filled('active'), function ($qq) use ($request) {
                $qq->where('active', (int) $request->query('active'));
            });

        // Optional search (?q=)
        if ($search = trim((string) $request->query('q', ''))) {
            $q->where(function ($qq) use ($search) {
                $qq->where('name_en', 'LIKE', "%{$search}%")
                   ->orWhere('name_ar', 'LIKE', "%{$search}%")
                   ->orWhere('title_en', 'LIKE', "%{$search}%")
                   ->orWhere('title_ar', 'LIKE', "%{$search}%");
            });
        }

        // Order by sort ASC (then id ASC)
        $q->orderBy('sort', 'asc')->orderBy('id', 'asc');

        // Pagination
        $perPage = (int) $request->query('per_page', 12);
        $perPage = max(1, min($perPage, 50));

        $paginator = $q->paginate($perPage)->appends($request->query());

        return new PackageCollection($paginator);
    }

    public function show(Request $request, Package $package)
    {
        return new PackageResource($package);
    }
}
