<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\SponsorCategoryRequest;
use App\Models\SponsorCategory;
use App\Repositories\SponsorCategoryRepository;
use Illuminate\Http\Request;
use App\Helpers\FileHelper;

class SponsorCategoryController extends Controller
{
    protected $repo;

    public function __construct(SponsorCategoryRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $query = SponsorCategory::query();

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where('name', 'like', "%$searchTerm%")
                  ->orWhere('name_en', 'like', "%$searchTerm%");
        }

        $categories = $query->latest()->paginate(10);
        return view('content.sponsor_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('content.sponsor_categories.create');
    }

    public function store(SponsorCategoryRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo'] = FileHelper::uploadImage($request->file('logo'), 'sponsor_categories');
        }

        $this->repo->create($data);
        return redirect()->route('admin.sponsor_categories.index')
                         ->with('success', __('sponsor_category.created'));
    }

    public function edit(SponsorCategory $sponsorCategory)
    {
        return view('content.sponsor_categories.edit', compact('sponsorCategory'));
    }

    public function update(SponsorCategoryRequest $request, SponsorCategory $sponsorCategory)
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo'] = FileHelper::uploadImage($request->file('logo'), 'sponsor_categories');
        }

        $this->repo->update($sponsorCategory, $data);
        return redirect()->route('admin.sponsor_categories.index')
                         ->with('success', __('sponsor_category.updated'));
    }

    public function destroy(SponsorCategory $sponsorCategory)
    {
        $this->repo->delete($sponsorCategory);
        return redirect()->route('admin.sponsor_categories.index')
                         ->with('success', __('sponsor_category.deleted'));
    }

    public function show(SponsorCategory $sponsorCategory)
    {
        return view('content.sponsor_categories.show', compact('sponsorCategory'));
    }

    public function deactivate(SponsorCategory $sponsorCategory)
    {
        $sponsorCategory->active = !$sponsorCategory->active;
        $sponsorCategory->save();

        return redirect()->route('admin.sponsor_categories.index')
                         ->with('success', $sponsorCategory->active
                             ? __('sponsor_category.activated')
                             : __('sponsor_category.deactivated'));
    }
}
