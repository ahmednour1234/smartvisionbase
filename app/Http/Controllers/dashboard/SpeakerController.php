<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\SpeakerRequest;
use App\Models\Speaker;
use Illuminate\Http\Request;
use App\Repositories\SpeakerRepository;
use App\Helpers\FileHelper;

class SpeakerController extends Controller
{
    protected $repo;

    public function __construct(SpeakerRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $query = Speaker::query();

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name_ar', 'like', "%$searchTerm%")
                  ->orWhere('name_en', 'like', "%$searchTerm%");
            });
        }

        $speakers = $query->latest()->paginate(10);
        return view('content.speakers.index', compact('speakers'));
    }

    public function create()
    {
        return view('content.speakers.create');
    }

    public function store(SpeakerRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = FileHelper::uploadImage($request->file('image'), 'speakers');
        }

        $this->repo->create($data);
        return redirect()->route('admin.speakers.index')->with('success', __('speaker.created'));
    }

    public function edit(Speaker $speaker)
    {
        return view('content.speakers.edit', compact('speaker'));
    }

    public function update(SpeakerRequest $request, Speaker $speaker)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = FileHelper::uploadImage($request->file('image'), 'speakers');
        }

        $this->repo->update($speaker, $data);
        return redirect()->route('admin.speakers.index')->with('success', __('speaker.updated'));
    }

    public function destroy(Speaker $speaker)
    {
        $this->repo->delete($speaker);
        return redirect()->route('admin.speakers.index')->with('success', __('speaker.deleted'));
    }

    public function show(Speaker $speaker)
    {
        return view('content.speakers.show', compact('speaker'));
    }
}
