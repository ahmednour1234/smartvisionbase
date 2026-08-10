<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\MultimediaCategory;
use App\Models\MultiMedia;
use Illuminate\Http\Request;

class MultiMediaController extends Controller
{
  public function index(Request $request)
  {
    $locale = app()->getLocale();

    $multi_media_categories = MultimediaCategory::where('active', 1)->get();

    return view('web.content.multimedia', compact(
      'locale',
      'multi_media_categories'
    ));
  }
  public function show($id)
  {
    $locale = app()->getLocale();

    $multimedias = MultiMedia::where('active', true)->where('multi_media_category_id', $id)->get();
    $multi_media_category = MultimediaCategory::where('active', true)->findOrFail($id);
    return view('web.content.showmultimedia', compact('multi_media_category', 'locale', 'multimedias'));
  }
}
