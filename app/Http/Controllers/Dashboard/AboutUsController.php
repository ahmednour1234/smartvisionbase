<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AboutUs;
use Illuminate\Http\Request;

class AboutUsController extends Controller
{
    public function edit()
    {
        $about = AboutUs::query()->first();
        if (! $about) {
            $about = new AboutUs([
                'title_ar' => '',
                'title_en' => '',
                'content_ar' => '',
                'content_en' => '',
                'image' => null,
                'is_active' => true,
            ]);
            $about->save();
        }
        return view('dashboard.about.edit', compact('about'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'title_ar' => ['required','string','max:255'],
            'title_en' => ['required','string','max:255'],
            'content_ar' => ['required','string'],
            'content_en' => ['required','string'],
            'image' => ['nullable','string','max:255'],
            'image_upload' => ['nullable','file','mimes:png,jpg,jpeg,webp,svg','max:6144'],
            'is_active' => ['nullable','boolean'],
        ]);

        $about = AboutUs::query()->firstOrCreate([], [
            'title_ar' => '',
            'title_en' => '',
            'content_ar' => '',
            'content_en' => '',
            'image' => null,
            'is_active' => true,
        ]);

        $about->fill($data);

        if ($request->hasFile('image_upload')) {
            $file = $request->file('image_upload');
            $filename = 'about_'.time().'.'.$file->getClientOriginalExtension();
            $destination = public_path('uploads');
            if (!is_dir($destination)) {
                @mkdir($destination, 0755, true);
            }
            $file->move($destination, $filename);
            $about->image = 'uploads/'.$filename;
        }

        $about->is_active = (bool) ($data['is_active'] ?? false);
        $about->save();

        return redirect()->route('dashboard.about.edit')->with('success', 'About Us updated successfully.');
    }
}


