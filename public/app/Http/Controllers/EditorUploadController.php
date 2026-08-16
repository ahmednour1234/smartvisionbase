<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class EditorUploadController extends Controller
{
    /**
     * CKEditor/TinyMCE upload
     * - يحفظ فعليًا في: {project}/public/public/{folder}
     * - يُرجع URL مطلق بالشكل: https://domain.com/public/public/{folder}/{uuid}.{ext}
     * - يقبل الحقول: upload | main_image | image | file
     * - يمكن تمرير ?folder=editor|events...
     */
    public function store(Request $request)
    {
        try {
            // 1) تحديد الحقل + المجلد
            $field = $this->resolveUploadField($request);
            if (!$field) {
                throw ValidationException::withMessages([
                    'upload' => 'No upload file found (expected: upload, main_image, image, or file).',
                ]);
            }

            $folder = trim((string) $request->get('folder', 'editor'));
            if ($folder === '') $folder = 'editor';

            // 2) تحقق (صور + SVG اختياريًا)
            $request->validate([
                $field => [
                    'required','file','max:5120',
                    'mimetypes:image/jpeg,image/png,image/gif,image/webp,image/svg+xml',
                ],
            ], [
                "{$field}.mimetypes" => 'Only jpeg, png, gif, webp, or svg images are allowed.',
            ]);

            $file = $request->file($field);

            // 3) مجلد الحفظ الفعلي: /public/public/{folder}
            $publicBase = public_path('public');                 // => {project}/public/public
            $targetDir  = $publicBase . DIRECTORY_SEPARATOR . $folder; // => .../public/public/{folder}
            if (!is_dir($targetDir)) {
                @mkdir($targetDir, 0755, true);
            }

            // 4) اسم الملف
            $ext      = strtolower($file->getClientOriginalExtension() ?: 'jpg');
            $safeExt  = in_array($ext, ['jpg','jpeg','png','gif','webp','svg']) ? $ext : 'jpg';
            $filename = Str::uuid()->toString() . '.' . $safeExt;

            // 5) نقل الملف
            $file->move($targetDir, $filename);

            // 6) الروابط (مطلق = بـ public/public/ كما طلبت)
            $appUrl       = rtrim(config('app.url') ?: $request->getSchemeAndHttpHost(), '/');
            $relativePath = 'public/public/' . $folder . '/' . $filename;            // public/public/editor/xxx.jpg
            $absoluteUrl  = $appUrl . '/' . $relativePath;                           // https://domain.com/public/public/editor/xxx.jpg

            // 7) استجابة
            return response()->json([
                'url'           => $absoluteUrl,   // CKEditor سيستخدمه كما هو
                'location'      => $absoluteUrl,   // TinyMCE
                'absolute_url'  => $absoluteUrl,   // للوضوح
                'relative_path' => $relativePath,  // لو حبيت تخزّن النسبي
            ], 200);

        } catch (ValidationException $e) {
            return response()->json(['error' => ['message' => $e->getMessage()]], 422);
        } catch (\Throwable $e) {
            return response()->json(['error' => ['message' => 'Upload failed: '.$e->getMessage()]], 500);
        }
    }

    protected function resolveUploadField(Request $request): ?string
    {
        foreach (['upload', 'main_image', 'image', 'file'] as $key) {
            if ($request->hasFile($key)) return $key;
        }
        return null;
    }
}
