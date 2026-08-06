<?php
namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\Seo;
use Illuminate\Http\Request;
use App\Http\Requests\StoreSeoRequest;
use App\Http\Requests\UpdateSeoRequest;
use App\Repositories\SeoRepository;

class SeoController extends Controller
{
    protected $repo;

    public function __construct(SeoRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $query = Seo::query();

        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        if ($request->filled('title')) {
            $query->where('title->' . app()->getLocale(), 'like', '%' . $request->title . '%');
        }

        $seos = $query->latest()->paginate(15);

        return view('content.seo.index', compact('seos'));
    }

    public function create()
    {
        return view('content.seo.create');
    }

    public function store(StoreSeoRequest $request)
    {
        $this->repo->create($request->validated());

        return redirect()->route('dashboard.seos.index')->with('success', 'تم الإنشاء بنجاح');
    }

    public function edit(Seo $seo)
    {
        return view('content.seo.edit', compact('seo'));
    }

    public function update(UpdateSeoRequest $request, Seo $seo)
    {
        $this->repo->update($seo, $request->validated());

        return redirect()->route('dashboard.seos.index')->with('success', 'تم التحديث بنجاح');
    }

    public function show(Seo $seo)
    {
        return view('content.seo.show', compact('seo'));
    }
}
