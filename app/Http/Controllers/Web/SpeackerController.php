<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\HomeSection;
use App\Models\Speaker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SpeackerController extends Controller
{
public function index(Request $request)
{

    $speaker_section = HomeSection::where('is_active', true)->where('id', 4)->first();


    $perPage = 12;
    $page = $request->get('page', 1);
    $skip = ($page - 1) * $perPage;

    $allSpeakers = Speaker::where('active', true)->get();
    $speakers = $allSpeakers->slice($skip, $perPage)->values();
    $total = $allSpeakers->count();
    $totalPages = ceil($total / $perPage);
    return view('web.content.speaker', compact(
        'speaker_section',
        'speakers',
      'total',
        'totalPages',
        'page'
    ));
}
}
