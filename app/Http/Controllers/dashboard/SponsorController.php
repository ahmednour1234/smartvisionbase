<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\SponsorRequest;
use App\Models\Sponsor;
use App\Models\SponsorCategory;
use App\Repositories\SponsorRepository;
use Illuminate\Http\Request;
use App\Helpers\FileHelper;

class SponsorController extends Controller
{
    protected $repo;

    public function __construct(SponsorRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $categories = SponsorCategory::all();
        $sponsors = $this->repo->filter($request);
        return view('content.sponsors.index', compact('sponsors', 'categories'));
    }

    public function create()
    {
        $categories = SponsorCategory::all();
        return view('content.sponsors.create', compact('categories'));
    }

    public function store(SponsorRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = FileHelper::uploadImage($request->file('image'), 'sponsors');
        }

        $this->repo->create($data);
        return redirect()->route('admin.sponsors.index')->with('success', __('sponsor.created'));
    }

    public function edit(Sponsor $sponsor)
    {
        $categories = SponsorCategory::all();
        return view('content.sponsors.edit', compact('sponsor', 'categories'));
    }

    public function update(SponsorRequest $request, Sponsor $sponsor)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = FileHelper::uploadImage($request->file('image'), 'sponsors');
        }

        $this->repo->update($sponsor, $data);
        return redirect()->route('admin.sponsors.index')->with('success', __('sponsor.updated'));
    }

    public function destroy(Sponsor $sponsor)
    {
        $this->repo->delete($sponsor);
        return redirect()->route('admin.sponsors.index')->with('success', __('sponsor.deleted'));
    }

    public function show(Sponsor $sponsor)
    {
        return view('content.sponsors.show', compact('sponsor'));
    }

    public function deactivate(Sponsor $sponsor)
    {
        $sponsor->active = !$sponsor->active;
        $sponsor->save();

        return redirect()->route('admin.sponsors.index')
            ->with('success', $sponsor->active
                ? __('sponsor.activated')
                : __('sponsor.deactivated'));
    }
}
