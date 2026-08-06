<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function index()
    {
        $videos = Video::orderBy('section')->orderBy('sort_order')->orderBy('id')->paginate(15);
        return view('dashboard.videos.index', compact('videos'));
    }

    public function create()
    {
        $video = new Video(['is_active' => true, 'sort_order' => 0]);
        return view('dashboard.videos.form', compact('video'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'youtube_url' => ['required','string','max:255'],
            'section' => ['nullable','string','max:255'],
            'is_active' => ['nullable','boolean'],
            'sort_order' => ['nullable','integer','min:0','max:999999'],
        ]);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        Video::create($data);
        return redirect()->route('dashboard.videos.index')->with('success', 'Video added.');
    }

    public function edit(Video $video)
    {
        return view('dashboard.videos.form', compact('video'));
    }

    public function update(Request $request, Video $video)
    {
        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'youtube_url' => ['required','string','max:255'],
            'section' => ['nullable','string','max:255'],
            'is_active' => ['nullable','boolean'],
            'sort_order' => ['nullable','integer','min:0','max:999999'],
        ]);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $video->update($data);
        return redirect()->route('dashboard.videos.index')->with('success', 'Video updated.');
    }

    public function destroy(Video $video)
    {
        $video->delete();
        return redirect()->route('dashboard.videos.index')->with('success', 'Video deleted.');
    }
}


