<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FileHelper;

class GalleryController extends Controller
{
    public function index()
    {
        $galleries = Gallery::latest()->paginate(10);
        return view('content.galleries.index', compact('galleries'));
    }

    public function create()
    {
        return view('content.galleries.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'images'   => 'required|array|min:1',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        foreach ($request->file('images') as $image) {
            $path = FileHelper::uploadImage($image, 'uploads/galleries');

            Gallery::create([
                'image'  => $path,
                'active' => true,
            ]);
        }

        return redirect()->route('dashboard.galleries.index')->with('success', 'تم رفع الصور بنجاح');
    }

    public function edit(Gallery $gallery)
    {
        return view('content.galleries.edit', compact('gallery'));
    }

    public function update(Request $request, Gallery $gallery)
    {
        $request->validate([
            'image' => 'nullable',
        ]);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($gallery->image);

            $gallery->image = FileHelper::uploadImage($request->file('image'), 'uploads/galleries');
        }

        $gallery->save();

        return redirect()->route('dashboard.galleries.index')->with('success', 'تم التحديث بنجاح');
    }

    public function destroy(Gallery $gallery)
    {
        Storage::disk('public')->delete($gallery->image);
        $gallery->delete();

        return redirect()->back()->with('success', 'تم حذف الصورة بنجاح');
    }
}
