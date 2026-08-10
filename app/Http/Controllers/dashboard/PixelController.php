<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pixel;

class PixelController extends Controller
{
    public function index()
    {
        $pixels = Pixel::latest()->paginate(10);
        return view('content.pixels.index', compact('pixels'));
    }

    public function create()
    {
        return view('content.pixels.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'pixel_id' => 'required|string|max:255',
        ]);

        Pixel::create($request->only('name', 'pixel_id'));

        return redirect()->route('dashboard.pixels.index')->with('success', 'Pixel created successfully.');
    }

    public function edit(Pixel $pixel)
    {
        return view('content.pixels.edit', compact('pixel'));
    }

    public function update(Request $request, Pixel $pixel)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'pixel_id' => 'required|string|max:255',
        ]);

        $pixel->update($request->only('name', 'pixel_id'));

        return redirect()->route('dashboard.pixels.index')->with('success', 'Pixel updated successfully.');
    }

    public function destroy(Pixel $pixel)
    {
        $pixel->delete();
        return redirect()->route('dashboard.pixels.index')->with('success', 'Pixel deleted successfully.');
    }

    public function toggleActive(Pixel $pixel)
    {
        $pixel->active = !$pixel->active;
        $pixel->save();

        return redirect()->back()->with('success', 'Pixel status updated.');
    }
}
