<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\HomeSection;
use Illuminate\Http\Request;

class BlogController extends Controller
{
public function index(Request $request)
{
    $locale = app()->getLocale();
    $blog_section = HomeSection::where('is_active', true)->where('id', 8)->first();

    $perPage = 6; // عدد العناصر في كل صفحة
    $page = (int) $request->get('page', 1);
    $skip = ($page - 1) * $perPage;

    $allblogs = Blog::where('active', true)->latest()->get();
    $blogs = $allblogs->slice($skip, $perPage)->values();
    $total = $allblogs->count();
    $totalPages = ceil($total / $perPage);

    return view('web.content.blogs', compact(
        'blog_section',
        'blogs',
        'total',
        'totalPages',
        'page',
        'locale'
    ));
}
public function show($id)
{
    $locale = app()->getLocale();

    $blog = Blog::where('active', true)->findOrFail($id);
$latestblogs=Blog::where('active',true)->paginate(4);
    return view('web.content.singleblog', compact('blog', 'locale','latestblogs'));
}

}
