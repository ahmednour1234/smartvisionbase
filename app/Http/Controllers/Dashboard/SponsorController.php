<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;
use Illuminate\Http\Request;

class SponsorController extends Controller
{
    public function index()
    {
        $sponsors = Sponsor::orderBy('section')->orderBy('sort_order')->orderByDesc('id')->paginate(18);
        return view('dashboard.sponsors.index', compact('sponsors'));
    }

    public function create()
    {
        $sponsor = new Sponsor(['is_active' => true, 'sort_order' => 0]);
        return view('dashboard.sponsors.form', compact('sponsor'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'url' => ['nullable','url','max:255'],
            'section' => ['nullable','string','max:255'],
            'logo' => ['nullable','image','max:4096'],
            'is_active' => ['nullable','boolean'],
            'sort_order' => ['nullable','integer','min:0','max:999999'],
        ]);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = uniqid('spn_').'.'.$file->getClientOriginalExtension();
            $path = public_path('uploads/sponsors');
            if (!is_dir($path)) { @mkdir($path, 0775, true); }
            $file->move($path, $filename);
            $data['logo'] = 'uploads/sponsors/'.$filename;
        }
        Sponsor::create($data);
        return redirect()->route('dashboard.sponsors.index')->with('success', 'Sponsor created.');
    }

    public function edit(Sponsor $sponsor)
    {
        return view('dashboard.sponsors.form', compact('sponsor'));
    }

    public function update(Request $request, Sponsor $sponsor)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'url' => ['nullable','url','max:255'],
            'section' => ['nullable','string','max:255'],
            'logo' => ['nullable','image','max:4096'],
            'is_active' => ['nullable','boolean'],
            'sort_order' => ['nullable','integer','min:0','max:999999'],
        ]);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = uniqid('spn_').'.'.$file->getClientOriginalExtension();
            $path = public_path('uploads/sponsors');
            if (!is_dir($path)) { @mkdir($path, 0775, true); }
            $file->move($path, $filename);
            $data['logo'] = 'uploads/sponsors/'.$filename;
        }
        $sponsor->update($data);
        return redirect()->route('dashboard.sponsors.index')->with('success', 'Sponsor updated.');
    }

    public function destroy(Sponsor $sponsor)
    {
        $sponsor->delete();
        return redirect()->route('dashboard.sponsors.index')->with('success', 'Sponsor deleted.');
    }
}


