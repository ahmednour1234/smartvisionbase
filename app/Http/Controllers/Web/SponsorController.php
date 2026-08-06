<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\HomeSection;
use App\Models\Sponsor;
use App\Models\SponsorCategory;
use Illuminate\Http\Request;

class SponsorController extends Controller
{
public function index(Request $request)
{
    $locale = app()->getLocale();

    $sponsor_section = HomeSection::where('is_active', true)->where('id', 7)->first();

    $categories = SponsorCategory::with(['sponsors' => function ($q) {
        $q->where('active', true)->orderBy('created_at', 'desc');
    }])->get();

    return view('web.content.sponsor', compact('sponsor_section', 'categories', 'locale'));
}

public function loadMoreSponsors(Request $request, $categoryId)
{
    $locale = app()->getLocale();
    $offset = $request->get('offset', 0);

    $sponsors = Sponsor::where('category_sponsor_id', $categoryId)
        ->where('active', true)
        ->orderBy('created_at', 'desc')
        ->skip($offset)
        ->take(6)
        ->get();

    $data = $sponsors->map(function ($sponsor) use ($locale) {
        return [
            'id' => $sponsor->id,
            'image' => asset($sponsor->image),
            'name' => $locale == 'ar' ? $sponsor->name_ar : $sponsor->name_en,
            'title' => $locale == 'ar' ? $sponsor->title_ar : $sponsor->title_en,
        ];
    });

    return response()->json($data);
}

}
