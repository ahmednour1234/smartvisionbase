<?php

namespace App\Http\Controllers\dashboard;

use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdRequest;
use App\Http\Requests\UpdateAdRequest;
use App\Models\Ad;
use App\Repositories\AdRepository;
use Illuminate\Http\Request;

class AdController extends Controller
{
    protected $repo;

    public function __construct(AdRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $ads = $this->repo->index([
            'name'   => $request->name,
            'active' => $request->active,
            'from'   => $request->from,
            'to'     => $request->to,
        ]);

        return view('content.ads.index', compact('ads'));
    }

    public function create()
    {
        return view('content.ads.create');
    }

    public function store(StoreAdRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('img')) {
            $data['img'] =  FileHelper::uploadImage($request->file('img'), 'uploads/ads');
        }

        $this->repo->create($data);
        return redirect()->route('dashboard.ads.index')->with('success', __('ads_created_successfully'));
    }

    public function show($id)
    {
        $ad = $this->repo->show($id);
        return view('content.ads.show', compact('ad'));
    }

    public function edit($id)
    {
        $ad = $this->repo->show($id);
        return view('content.ads.edit', compact('ad'));
    }

    public function update(UpdateAdRequest $request, $id)
    {
        $ad = $this->repo->show($id);
        $data = $request->validated();

        if ($request->hasFile('img')) {
            deleteFile($ad->img);
            $data['img'] = FileHelper::uploadImage($request->file('img'), 'uploads/ads');
        }

        $this->repo->update($ad, $data);
        return redirect()->route('dashboard.ads.index')->with('success', __('ads_updated_successfully'));
    }

    public function toggleActive($id)
    {
        $ad = $this->repo->show($id);
        $this->repo->toggleActive($ad);
        return redirect()->back()->with('success', __('ads_status_changed'));
    }
}
