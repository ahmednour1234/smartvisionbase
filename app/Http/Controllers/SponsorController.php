<?php

namespace App\Http\Controllers;

use App\Models\Sponsor;
use Illuminate\Http\Request;

class SponsorController extends Controller
{
    public function index()
    {
        $perPage = 6;

        $sponsors = Sponsor::query()
            ->where('is_active', true)
            ->orderByRaw('section IS NULL')
            ->orderBy('section')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate($perPage);

        $groups = $sponsors->getCollection()->groupBy(function ($sponsor) {
            return $sponsor->section ?: __('Our Previous Clients');
        });

        return view('site.sponsors.index', compact('sponsors', 'groups'));
    }

    
    public function loadMore(Request $request)
    {
        $sponsors = Sponsor::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(6, ['*'], 'page', $request->get('page', 1));

        $html = view('site.sponsors.partials.items', [
            'items' => $sponsors->getCollection(),
            'title' => __('Our Previous Clients'),
        ])->render();

        return response()->json([
            'html' => $html,
            'has_more' => $sponsors->hasMorePages(),
            'next_page' => $sponsors->currentPage() + 1,
        ]);
    }
}