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
    $data  = $request->validated();

    // هات السجل الحالي عشان ندمج الصور القديمة مع الجديدة
    $media = $this->repo->find($id); // لو معندكش find في الريبو: $media = \App\Models\MultiMedia::findOrFail($id);
    $existingImages = is_array($media->images) ? $media->images : (empty($media->images) ? [] : (array) $media->images);

    $newImages   = [];
    $allowedExt  = ['jpg','jpeg','png','webp','gif'];

    // استقبال صور متعددة أو ZIP (بدون حذف القديم)
    if ($request->hasFile('images')) {
        $files = $request->file('images');
        if (!is_array($files)) {
            $files = [$files];
        }

        foreach ($files as $file) {
            if (!$file || !$file->isValid()) continue;

            $ext = strtolower($file->getClientOriginalExtension());

            if ($ext === 'zip') {
                $zip = new ZipArchive;
                $zipName    = Str::uuid()->toString();
                $extractDir = storage_path("app/tmp_extract/{$zipName}");
                File::ensureDirectoryExists($extractDir, 0775, true);

                if ($zip->open($file->getRealPath()) === true) {
                    $zip->extractTo($extractDir);
                    $zip->close();

                    // خُد كل الملفات (Recursively)
                    foreach (File::allFiles($extractDir) as $img) {
                        $fExt = strtolower($img->getExtension());
                        if (!in_array($fExt, $allowedExt, true)) {
                            continue;
                        }

                        $filename   = (string) Str::uuid() . '.' . $fExt;
                        $destination = public_path('multi_media/images');
                        File::ensureDirectoryExists($destination, 0775, true);
                        File::move($img->getPathname(), $destination . '/' . $filename);

                        $newImages[] = 'multi_media/images/' . $filename; // مسار نسبي للعرض بـ asset()
                    }

                    // نظف بعد ما تخلص
                    File::deleteDirectory($extractDir);
                }
            } else {
                // صورة مباشرة
                $newImages[] = FileHelper::uploadImage($file, 'multi_media/images');
            }
        }

        // دمج القديم مع الجديد (بدون تكرار)
        $data['images'] = array_values(array_unique(array_merge($existingImages, $newImages)));
    } else {
        // ما جاش صور جديدة → سيب القديم زي ما هو وماتلمسش عمود images
        unset($data['images']);
    }

    // روابط الفيديوهات: ما نحدّثهاش إلا لو جاية في الريكوست
    if ($request->has('links')) {
        $data['links'] = array_values(array_filter($request->input('links', [])));
    } else {
        unset($data['links']);
    }

    $this->repo->update($id, $data);

    return redirect()
        ->route('dashboard.multi-medias.index')
        ->with('success', __('multi_media.update'));
}


public function destroyImage(Request $request, $id)
{
    $data = $request->validate([
        // مسار الصورة كما هو مخزَّن في الداتابيز (مثال: multi_media/images/abc.jpg)
        'image'       => 'required|string',
        // امسح الملف من السيرفر كمان؟ افتراضي true
        'delete_file' => 'sometimes|boolean',
    ]);

    // هات السجل (ريبو أو Eloquent مباشر)
    $media = method_exists($this->repo, 'find')
        ? $this->repo->find($id)
        : \App\Models\MultiMedia::findOrFail($id);

    if (! $media) {
        abort(404);
    }

    // جهّز الآراي
    $images = is_array($media->images)
        ? $media->images
        : (empty($media->images) ? [] : (array) $media->images);

    // طبّع المسارات للمقارنة (استبدال الباك سلاش وازالة الـ leading slash)
    $normalize = function ($p) {
        return ltrim(str_replace('\\', '/', $p), '/');
    };

    $needle     = $normalize($data['image']);
    $normalized = array_map($normalize, $images);

    // دور على الصورة بالمسار الكامل
    $idx = array_search($needle, $normalized, true);

    // لو مش لاقيها، جرّب بالمقارنة على اسم الملف فقط (basename)
    if ($idx === false) {
        $base = basename($needle);
        foreach ($normalized as $i => $p) {
            if (basename($p) === $base) { $idx = $i; break; }
        }
    }

    if ($idx === false) {
        return response()->json([
            'success' => false,
            'message' => 'Image not found in this record.',
        ], 422);
    }

    // احذف من الآراي وحدّث الداتابيز
    $removedPath = $images[$idx];
    unset($images[$idx]);
    $images = array_values(array_unique($images));

    if (method_exists($this->repo, 'update')) {
        $this->repo->update($id, ['images' => $images]);
    } else {
        $media->update(['images' => $images]);
    }

    // امسح الملف من السيرفر لو مطلوب
    if ($request->boolean('delete_file', true)) {
        $full = public_path('/' . $normalize($removedPath));
        if (is_file($full)) {
            @File::delete($full);
        }
    }

    return response()->json([
        'success' => true,
        'removed' => $removedPath,
        'images'  => $images,
    ]);
}

    public function activate($id)
    {
        $this->repo->toggleActive($id);
        return redirect()->back()->with('success', __('multi_media.active') . ' ' . __('multi_media.update'));
    }

    public function destroy($id)
    {
        // هات السجل أولًا للتعامل مع الصور
        $media = method_exists($this->repo, 'find')
            ? $this->repo->find($id)
            : MultiMedia::findOrFail($id);

        // امسح الصور من السيرفر
        $images = is_array($media->images) ? $media->images
            : (empty($media->images) ? [] : (array)$media->images);

        foreach ($images as $path) {
            $full = public_path('/' . ltrim(str_replace('\\','/',$path), '/'));
            if (is_file($full)) {
                @File::delete($full);
            }
        }

        // احذف السجل
        if (method_exists($this->repo, 'delete')) {
            $this->repo->delete($id);
        } else {
            $media->delete();
        }

        return redirect()
            ->route('dashboard.multi-medias.index')
            ->with('success', __('multi_media.delete_success'));
    }

}
