<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        $keys = [
            'site_name_ar','site_name_en',
            'phone','whatsapp','email',
            'address_ar','address_en',
            'logo','logo_upload',
            'facebook','instagram','x','linkedin','tiktok','youtube',
            'map_lat','map_lng',
            'home_sections',
        ];

        $values = [];
        foreach ($keys as $k) {
            $values[$k] = Setting::getValue($k);
        }

        return view('dashboard.settings.edit', ['settings' => $values]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'site_name_ar' => ['required','string','max:255'],
            'site_name_en' => ['required','string','max:255'],
            'phone' => ['nullable','string','max:30'],
            'whatsapp' => ['nullable','string','max:30'],
            'email' => ['nullable','email','max:255'],
            'address_ar' => ['nullable','string','max:255'],
            'address_en' => ['nullable','string','max:255'],
            'logo' => ['nullable','string','max:255'],
            'logo_upload' => ['nullable','file','mimes:png,jpg,jpeg,svg,webp','max:4096'],
            'facebook' => ['nullable','url','max:255'],
            'instagram' => ['nullable','url','max:255'],
            'x' => ['nullable','url','max:255'],
            'linkedin' => ['nullable','url','max:255'],
            'tiktok' => ['nullable','url','max:255'],
            'youtube' => ['nullable','url','max:255'],
            'map_lat' => ['nullable','numeric'],
            'map_lng' => ['nullable','numeric'],
            'home_sections' => ['nullable','string','max:2000'],
        ]);

        foreach ($data as $k => $v) {
            if ($k === 'logo_upload') continue;
            Setting::setValue($k, $v);
        }

        if ($request->hasFile('logo_upload')) {
            $file = $request->file('logo_upload');
            $filename = 'logo_'.time().'.'.$file->getClientOriginalExtension();
            $destination = public_path('uploads');
            if (!is_dir($destination)) {
                @mkdir($destination, 0755, true);
            }
            $file->move($destination, $filename);
            $relativePath = 'uploads/'.$filename;
            Setting::setValue('logo_upload', $relativePath);
        }

        return redirect()->route('dashboard.settings.edit')->with('success', 'Settings updated successfully.');
    }
}


