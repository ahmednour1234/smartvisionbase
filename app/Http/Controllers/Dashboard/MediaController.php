<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    protected function mediaDir(): string
    {
        return public_path('uploads/media');
    }

    protected function listFiles(): array
    {
        $dir = $this->mediaDir();
        if (! is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        $files = [];
        foreach (glob($dir . DIRECTORY_SEPARATOR . '*') as $path) {
            if (is_file($path)) {
                $files[] = [
                    'path' => 'uploads/media/' . basename($path),
                    'ext' => strtolower(pathinfo($path, PATHINFO_EXTENSION)),
                    'mtime' => filemtime($path),
                ];
            }
        }
        usort($files, fn($a, $b) => $b['mtime'] <=> $a['mtime']);
        return $files;
    }

    public function index()
    {
        $files = $this->listFiles();
        return view('dashboard.media.index', compact('files'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required','file','mimetypes:image/jpeg,image/png,image/webp,video/mp4,video/webm','max:102400'],
        ]);
        $file = $request->file('file');
        $dir = $this->mediaDir();
        if (! is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        $filename = uniqid('media_') . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $filename);
        return back()->with('success', 'Media uploaded.');
    }

    public function destroy(Request $request)
    {
        $request->validate(['path' => ['required','string']]);
        $path = $request->input('path');
        if (str_starts_with($path, 'uploads/media/')) {
            $full = public_path($path);
            if (file_exists($full) && is_file($full)) {
                @unlink($full);
                return back()->with('success', 'Media deleted.');
            }
        }
        return back()->with('error', 'Invalid file.');
    }
}


