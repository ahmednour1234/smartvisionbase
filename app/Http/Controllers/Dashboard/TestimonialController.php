<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::orderBy('sort_order')->orderByDesc('id')->paginate(15);
        return view('dashboard.testimonials.index', compact('testimonials'));
    }

    public function create()
    {
        $testimonial = new Testimonial(['is_active' => true, 'sort_order' => 0]);
        return view('dashboard.testimonials.form', compact('testimonial'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'role' => ['nullable','string','max:255'],
            'quote' => ['required','string','max:2000'],
            'avatar' => ['nullable','image','max:3072'],
            'is_active' => ['nullable','boolean'],
            'sort_order' => ['nullable','integer','min:0','max:999999'],
        ]);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = uniqid('tst_').'.'.$file->getClientOriginalExtension();
            $path = public_path('uploads/testimonials');
            if (!is_dir($path)) { @mkdir($path, 0775, true); }
            $file->move($path, $filename);
            $data['avatar'] = 'uploads/testimonials/'.$filename;
        }
        Testimonial::create($data);
        return redirect()->route('dashboard.testimonials.index')->with('success', 'Testimonial created.');
    }

    public function edit(Testimonial $testimonial)
    {
        return view('dashboard.testimonials.form', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'role' => ['nullable','string','max:255'],
            'quote' => ['required','string','max:2000'],
            'avatar' => ['nullable','image','max:3072'],
            'is_active' => ['nullable','boolean'],
            'sort_order' => ['nullable','integer','min:0','max:999999'],
        ]);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = uniqid('tst_').'.'.$file->getClientOriginalExtension();
            $path = public_path('uploads/testimonials');
            if (!is_dir($path)) { @mkdir($path, 0775, true); }
            $file->move($path, $filename);
            $data['avatar'] = 'uploads/testimonials/'.$filename;
        }
        $testimonial->update($data);
        return redirect()->route('dashboard.testimonials.index')->with('success', 'Testimonial updated.');
    }

    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();
        return redirect()->route('dashboard.testimonials.index')->with('success', 'Testimonial deleted.');
    }
}


