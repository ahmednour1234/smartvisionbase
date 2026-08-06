<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\AboutUs;
use App\Models\Testimonial;
use App\Models\Faq;
use App\Models\Video;
use App\Models\Sponsor;

class HomeController extends Controller
{
    public function index()
    {
        $nearest = Event::query()->current()->orderBy('start_at')->first()
            ?? Event::query()->upcoming()->orderBy('start_at')->first();

        $upcoming = Event::active()
            ->where('start_at', '>=', now())
            ->orderBy('start_at')
            ->get();

        $about = AboutUs::active();

        $testimonials = Testimonial::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->take(12)
            ->get();

        $faqs = Faq::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->take(10)
            ->get();

        $videos = Video::query()
            ->where('is_active', true)
            ->orderBy('section')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $sponsors = Sponsor::query()
            ->where('is_active', true)
            ->orderByRaw('section IS NULL')
            ->orderBy('section')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(6);

        $groups = $sponsors->getCollection()->groupBy(function ($sponsor) {
            return $sponsor->section ?: __('Our Previous Clients');
        });

        return view('site.home', compact(
            'nearest',
            'upcoming',
            'about',
            'testimonials',
            'faqs',
            'videos',
            'sponsors',
            'groups'
        ));
    }
}