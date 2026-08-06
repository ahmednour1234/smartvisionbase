<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Gallery;
use App\Models\HomeSection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GalleryController extends Controller
{
   public function index()
{
    $gallery_section = HomeSection::where('is_active', true)->where('id', 6)->first();
    $event           = Event::first();

    $gallieries = Gallery::where('active', true)
        ->orderBy('created_at', 'desc')
        ->get();


    return view('web.content.gallery', compact(
        'event',
        'gallieries',
        'gallery_section',
    ));
}
}
