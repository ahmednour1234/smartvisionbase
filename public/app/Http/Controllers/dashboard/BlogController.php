<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBlogRequest;
use App\Http\Requests\UpdateBlogRequest;
use App\Models\Blog;
use App\Helpers\FileHelper;
use App\Repositories\BlogRepository;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    protected $repo;

    public function __construct(BlogRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $query =Blog::query();

        if ($request->filled('name')) {
            $query->where('name_ar', 'like', "%{$request->name}%")
                  ->orWhere('name_en', 'like', "%{$request->name}%");
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        $blogs = $query->latest()->paginate(10);

        return view('content.blogs.index', compact('blogs'));
    }

    public function create()
    {
        return view('content.blogs.create');
    }

   public function store(StoreBlogRequest $request)
{
    $data = $request->validated();

    // رفع الصورة إذا تم تحميلها
    if ($request->hasFile('image')) {
        $data['image'] = FileHelper::uploadImage($request->file('image'), 'uploads/blogs');
    }

    $this->repo->create($data);

    return redirect()->route('dashboard.blogs.index')
                     ->with('success', __('blog.save'));
}




    public function show($id)
    {
        $blog = $this->repo->find($id);
        return view('content.blogs.show', compact('blog'));
    }

    public function edit($id)
    {
        $blog = $this->repo->find($id);
        return view('content.blogs.edit', compact('blog'));
    }

   public function update(UpdateBlogRequest $request, $id)
{
    $data = $request->validated();

    // رفع الصورة إذا تم تحميلها مع استبدال القديمة
    if ($request->hasFile('image')) {
        $data['image'] = FileHelper::uploadImage($request->file('image'), 'uploads/blogs');
    }

    $this->repo->update($id, $data);

    return redirect()->route('dashboard.blogs.index')
                     ->with('success', __('blog.update'));
}

    public function destroy($id)
    {
        $this->repo->delete($id);
        return redirect()->back()->with('success', __('blog.deleted'));
    }

    public function activate($id)
    {
        $this->repo->toggleActive($id);
        return redirect()->back()->with('success', __('blog.update'));
    }
}
