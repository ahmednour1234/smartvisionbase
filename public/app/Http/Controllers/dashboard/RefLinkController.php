<?php
namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\MarketingRef;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class RefLinkController extends Controller
{
    private const DEFAULT_TARGET = '/register';

    public function hit(Request $request, MarketingRef $ref, ?string $path = null)
    {
        // اختر الهدف:
        // 1) لو المستخدم حدّد مسار في اللينك /r/CODE/{path} استخدمه
        // 2) وإلا خُد أول مسار من allowed_paths لو موجود
        // 3) وإلا استخدم /register
        $allowed = $ref->allowed_paths ?: [];
        $firstAllowed = Arr::first($allowed, null, ltrim(self::DEFAULT_TARGET, '/'));

        $targetPath = '/'.ltrim($path ?: $firstAllowed, '/');

        // لو المرجع غير صالح للمسار المختار: 404
        if (! $ref->isUsableForPath($targetPath)) {
            abort(404);
        }

        // سجّل الزيارة
        try {
            $ref->hits()->create([
                'ip'         => substr($request->ip() ?? '', 0, 64),
                'user_agent' => substr($request->userAgent() ?? '', 0, 255),
                'path'       => $targetPath,
            ]);
        } catch (\Throwable $e) {}

        // خزّن الكود 7 أيام
        cookie()->queue(cookie('mr_code', $ref->code, 60*24*7));

        return redirect($targetPath);
    }
}
