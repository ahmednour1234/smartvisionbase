<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\HomeSection;
use App\Models\Sponsor;
use App\Models\SponsorCategory;
use Illuminate\Http\Request;
use App\Repositories\EventRepository;

class SponsorController extends Controller
{
      protected EventRepository $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

public function index(Request $request)
{
    $locale = app()->getLocale();
        if ($request->query('event')) {
            $this->eventRepository->runMaintenanceCommand();
        }

    $sponsor_section = HomeSection::where('is_active', true)->where('id', 7)->first();

    $categories = SponsorCategory::where('active',true)->with(['sponsors' => function ($q) {
        $q->where('active', true)->orderBy('sort', 'asc');
    }])->get();

    return view('web.content.sponsor', compact('sponsor_section', 'categories', 'locale'));
}

public function loadMoreSponsors(Request $request, $categoryId)
{
    $locale = app()->getLocale();
    $offset = $request->get('offset', 0);

    $sponsors = Sponsor::where('category_sponsor_id', $categoryId)
        ->where('active', true)
        ->orderBy('sort', 'desc')
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
