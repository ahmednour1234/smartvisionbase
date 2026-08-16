<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMultiMediaRequest;
use App\Http\Requests\UpdateMultiMediaRequest;
use App\Models\MultiMedia;
use App\Repositories\MultiMediaRepository;
use App\Models\MultimediaCategory;
use Illuminate\Http\Request;
    use App\Helpers\FileHelper;
use ZipArchive;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class MultiMediaController extends Controller
{
    protected $repo;

    public function __construct(MultiMediaRepository $repo)
    {
        $this->repo = $repo;
    }

  public function index(Request $request)
{
    $query = MultiMedia::query();

    if ($request->filled('name')) {
        $query->where('name_ar', 'like', '%' . $request->name . '%')
              ->orWhere('name_en', 'like', '%' . $request->name . '%');
    }

    if ($request->filled('date')) {
        $query->whereDate('date', $request->date);
    }

    if ($request->filled('multi_media_category_id')) {
        $query->where('multi_media_category_id', $request->multi_media_category_id);
    }

    $multiMedias = $query->latest()->paginate(10);
    $categories = MultimediaCategory::pluck('name_ar', 'id');

    return view('content.multi_mediall.index', compact('multiMedias', 'categories'));
}


    public function create()
    {
        $categories = MultimediaCategory::pluck('name_ar', 'id');
        return view('content.multi_mediall.create', compact('categories'));
    }


public function store(StoreMultiMediaRequest $request)
{
    $data = $request->validated();
    $storedImages = [];

    foreach ($request->file('images') as $file) {
        $ext = strtolower($file->getClientOriginalExtension());

        if ($ext === 'zip') {
            $zip = new \ZipArchive;
            if ($zip->open($file->getRealPath()) === true) {
                $tempDir = public_path('multi_media/temp_' . \Str::uuid());
                \File::makeDirectory($tempDir, 0755, true);
                $zip->extractTo($tempDir);
                $zip->close();

                foreach (\File::allFiles($tempDir) as $img) {
                    $imgExt = strtolower($img->getExtension());
                    if (in_array($imgExt, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        $newName = \Str::uuid() . '.' . $imgExt;
                        $finalPath = public_path('multi_media/images/' . $newName);
                        \File::ensureDirectoryExists(public_path('multi_media/images'));
                        \File::move($img->getRealPath(), $finalPath);
                        $storedImages[] = 'multi_media/images/' . $newName;
                    }
                }

                \File::deleteDirectory($tempDir);
            }
        } elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $newName = \Str::uuid() . '.' . $ext;
            $finalPath = public_path('multi_media/images/' . $newName);
            \File::ensureDirectoryExists(public_path('multi_media/images'));
            $file->move(public_path('multi_media/images'), $newName);
            $storedImages[] = 'multi_media/images/' . $newName;
        }
    }

    $data['images'] = $storedImages;

    if ($request->filled('links')) {
        $data['links'] = array_filter($request->input('links'));
    }

    $this->repo->create($data);

    return redirect()->route('dashboard.multi-medias.index')
                     ->with('success', __('multi_media.save'));
}


    public function show($id)
    {
        $media = $this->repo->find($id);
        return view('content.multi_mediall.show', compact('media'));
    }

    public function edit($id)
    {
        $media = $this->repo->find($id);
        $categories = MultimediaCategory::pluck('name_ar', 'id');
        return view('content.multi_mediall.edit', compact('media', 'categories'));
    }
public function update(UpdateMultiMediaRequest $request, $id)
{
    $data = $request->validated();
    $allImages = [];

    // التعامل مع صور مرفوعة مفردة أو متعددة أو ZIP
    if ($request->hasFile('images')) {
        $files = $request->file('images');

        // إذا كان المفترض ملف واحد فقط وتم رفعه بدون multiple
        if (!is_array($files)) {
            $files = [$files];
        }

        foreach ($files as $file) {
            if ($file->getClientOriginalExtension() === 'zip') {
                $zip = new ZipArchive;
                $zipName = Str::uuid()->toString();
                $extractPath = public_path("multi_media/tmp/{$zipName}");

                if ($zip->open($file->getRealPath()) === true) {
                    $zip->extractTo($extractPath);
                    $zip->close();

                    $extractedFiles = File::files($extractPath);
                    foreach ($extractedFiles as $image) {
                        $extension = strtolower($image->getExtension());
                        if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
                            $filename = Str::uuid().'.'.$extension;
                            $destination = public_path('multi_media/images');
                            File::ensureDirectoryExists($destination);
                            File::move($image->getPathname(), $destination.'/'.$filename);
                            $allImages[] = 'multi_media/images/'.$filename;
                        }
                    }

                    File::deleteDirectory($extractPath);
                }
            } else {
                $allImages[] = FileHelper::uploadImage($file, 'multi_media/images');
            }
        }

        $data['images'] = $allImages;
    }

    // روابط الفيديوهات (إذا وُجدت)
    if ($request->filled('links')) {
        $data['links'] = array_filter($request->input('links'));
    }

    $this->repo->update($id, $data);

    return redirect()->route('dashboard.multi-medias.index')
                     ->with('success', __('multi_media.update'));
}

    public function activate($id)
    {
        $this->repo->toggleActive($id);
        return redirect()->back()->with('success', __('multi_media.active') . ' ' . __('multi_media.update'));
    }

    public function destroy($id)
    {
        $this->repo->delete($id);
        return redirect()->back()->with('success', __('multi_media.deleted'));
    }
    
    public function destroyImage(MultiMedia $media, int $index)
    {
        $images = is_array($media->images) ? $media->images : [];

        if (! isset($images[$index])) {
            return back()->with('error', 'Image not found.');
        }

        $imagePath = $images[$index];

        // حذف الصورة من الـ storage (disk public)
        if ($imagePath && Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }

        // حذفها من المصفوفة وإعادة الفهرسة
        unset($images[$index]);
        $images = array_values($images);

        $media->images = $images;
        $media->save();

        return back()->with('success', 'Image deleted successfully.');
    }

    // ================== دالة حذف لينك واحد ==================

    public function destroyLink(MultiMedia $media, int $index)
    {
        $links = is_array($media->links) ? $media->links : [];

        if (! isset($links[$index])) {
            return back()->with('error', 'Link not found.');
        }

        unset($links[$index]);
        $links = array_values($links);

        $media->links = $links;
        $media->save();

        return back()->with('success', 'Link deleted successfully.');
    }
}
