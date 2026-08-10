<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HomeSection;
use App\Helpers\FileHelper;

class HomeSectionController extends Controller
{
    public function index()
    {
        $sections = HomeSection::orderBy('section_order')->get();
        return view('content.home_sections.index', compact('sections'));
    }

    public function create()
    {
        return view('content.home_sections.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|array',
            'title.ar'     => 'required|string|max:255',
            'title.en'     => 'required|string|max:255',
            'description'  => 'nullable|array',
            'description.ar' => 'nullable|string',
            'description.en' => 'nullable|string',
            'media_type'   => 'required|in:image,video',
            'media'        => 'required|file|mimes:jpg,jpeg,png,mp4|max:10240', // max 10MB
            'section_order'=> 'required|integer',
            'is_active'    => 'boolean',
        ]);

        // رفع الصورة أو الفيديو
        $data['media_path'] = FileHelper::uploadImage($request->file('media'), 'uploads/home_sections', $data['media_type']);

        HomeSection::create($data);

        return redirect()->route('dashboard.home_sections.index')->with('success', 'تم الإضافة بنجاح');
    }

    public function edit(HomeSection $homeSection)
    {
        return view('content.home_sections.edit', compact('homeSection'));
    }
public function update(Request $request, HomeSection $homeSection)
{
    $validated = $request->validate([
        'title'           => 'sometimes|array',
        'title.ar'        => 'sometimes|string|max:255',
        'title.en'        => 'sometimes|string|max:255',
        'description'     => 'sometimes|nullable|array',
        'description.ar'  => 'nullable|string',
        'description.en'  => 'nullable|string',
        'media_type'      => 'sometimes|in:image,video,link',
        'media'           => 'sometimes',
        'media_path'      => 'sometimes|nullable|string', // سيتم استخدامه عند media_type = link
        'thumbnail'       => 'sometimes',
        'section_order'   => 'sometimes|integer',
        'is_active'       => 'sometimes|boolean',
    ]);

    $data = [];

    // فقط الحقول التي تم إرسالها
    foreach ($validated as $key => $value) {
        if (!in_array($key, ['media', 'thumbnail'])) {
            $data[$key] = $value;
        }
    }

    // التعامل مع media_type
    $mediaType = $request->input('media_type', $homeSection->media_type);

    // إذا تم رفع ملف media
    if ($mediaType !== 'link' && $request->hasFile('media')) {
        $data['media_path'] = FileHelper::uploadImage(
            $request->file('media'),
            'uploads/home_sections',
            $mediaType
        );
    }

    // إذا كان النوع رابط (link) وتم إرسال media_path كقيمة
    if ($mediaType === 'link' && $request->filled('media_path')) {
        $data['media_path'] = $request->input('media_path');
    }

if ($request->hasFile('thumbnail')) {
    $data['thumbnail'] = FileHelper::uploadImage(
        $request->file('thumbnail'),
        'uploads/home_sections/thumbnails',
        'image'
    );
}

    $homeSection->update($data);

    return redirect()->back()->with('success', __('تم التحديث بنجاح'));
}

    public function toggleActivate(HomeSection $homeSection)
    {
        $homeSection->is_active = ! $homeSection->is_active;
        $homeSection->save();

        return redirect()->back()->with('success', 'تم تغيير حالة التفعيل');
    }
}
