<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMultimediaCategoryRequest;
use App\Http\Requests\UpdateMultimediaCategoryRequest;
use App\Repositories\MultimediaCategoryRepository;
use Illuminate\Http\Request;
use App\Helpers\FileHelper;

class MultimediaCategoryController extends Controller
{
    protected $repo;

    public function __construct(MultimediaCategoryRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index()
    {
        $categories = $this->repo->allPaginated();
        return view('content.multi_media.index', compact('categories'));
    }

    public function create()
    {
        return view('content.multi_media.create');
    }

    public function store(StoreMultimediaCategoryRequest $request)
    {
        $data = $request->validated();

        $data['logo'] = FileHelper::uploadImage($request->file('logo'), 'multimedia_categories');

        $this->repo->create($data);

        return redirect()->route('dashboard.multimedia-categories.index')
                         ->with('success', __('multi_media.save'));
    }

    public function show($id)
    {
        $category = $this->repo->find($id);
        return view('content.multi_media.show', compact('category'));
    }

    public function edit($id)
    {
        $category = $this->repo->find($id);
        return view('content.multi_media.edit', compact('category'));
    }

    public function update(UpdateMultimediaCategoryRequest $request, $id)
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo'] = FileHelper::uploadImage($request->file('logo'), 'multimedia_categories');
        }

        $this->repo->update($id, $data);

        return redirect()->route('dashboard.multimedia-categories.index')
                         ->with('success', __('multi_media.update'));
    }

    public function activate($id)
    {
        $this->repo->toggleActive($id);

        return redirect()->back()->with('success', __('multi_media.active') . ' ' . __('multi_media.update'));
    }
}
