<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\Prize;
use Illuminate\Http\Request;
use App\Helpers\FileHelper;

class PrizeController extends Controller
{
    public function index()
    {
        $prizes = Prize::latest()->paginate(20);
        return view('content.prizes.index', compact('prizes'));
    }

    public function create()
    {
        return view('content.prizes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'description_en'=>'nullable',
                        'description_ar'=>'nullable',
            'img' => 'nullable',
            'active' => 'nullable|boolean',
            'order'=>'nullable'
        ]);

        if ($request->hasFile('img')) {
            $data['img'] = FileHelper::uploadImage($request->file('img'), 'content/prizes');
        }

        $data['active'] = $data['active'] ?? true;

        Prize::create($data);

        return redirect()->route('dashboard.prizes.index')->with('success', 'Prize created successfully.');
    }

    public function edit(Prize $prize)
    {
        return view('content.prizes.edit', compact('prize'));
    }

    public function update(Request $request, Prize $prize)
    {
        $data = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
                   'description_en'=>'nullable',
                        'description_ar'=>'nullable',
            'img' => 'nullable',
            'active' => 'nullable|boolean',
                        'order'=>'nullable'

        ]);

        if ($request->hasFile('img')) {
            $data['img'] = FileHelper::uploadImage($request->file('img'), 'content/prizes');
        }

        $prize->update($data);

        return redirect()->route('dashboard.prizes.index')->with('success', 'Prize updated successfully.');
    }

    public function destroy(Prize $prize)
    {
        $prize->delete();
        return redirect()->route('dashboard.prizes.index')->with('success', 'Prize deleted.');
    }

}
