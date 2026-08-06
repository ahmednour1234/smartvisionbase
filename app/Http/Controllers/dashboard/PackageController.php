<?php
namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Helpers\FileHelper;
use App\Http\Requests\StorePackageRequest;
use App\Http\Requests\UpdatePackageRequest;
use App\Models\Package;
use App\Repositories\PackageRepository;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    protected $repo;

    public function __construct(PackageRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
{
    $query = Package::query();

    if ($request->filled('name')) {
        $query->where('name_ar', 'like', '%' . $request->name . '%')
              ->orWhere('name_en', 'like', '%' . $request->name . '%');
    }

    if ($request->filled('price')) {
        $query->where('price', $request->price);
    }

    $packages = $query->latest()->paginate(10);
    return view('content.packages.index', compact('packages'));
}


    public function create()
    {
        return view('content.packages.create');
    }

    public function store(StorePackageRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $data['image'] = FileHelper::uploadImage($request->file('image'), 'uploads/packages');
        }
        $this->repo->create($data);
        return redirect()->route('dashboard.packages.index')->with('success', __('package.created'));
    }

    public function show($id)
    {
        $package = $this->repo->find($id);
        return view('content.packages.show', compact('package'));
    }

    public function edit($id)
    {
        $package = $this->repo->find($id);
        return view('content.packages.edit', compact('package'));
    }

    public function update(UpdatePackageRequest $request, $id)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $data['image'] = FileHelper::uploadImage($request->file('image'), 'uploads/packages');
        }
        $this->repo->update($id, $data);
        return redirect()->route('dashboard.packages.index')->with('success', __('package.updated'));
    }

    public function destroy($id)
    {
        $this->repo->delete($id);
        return redirect()->back()->with('success', __('package.deleted'));
    }

    public function activate($id)
    {
        $this->repo->toggleActive($id);
        return redirect()->back()->with('success', __('package.status_updated'));
    }
}
