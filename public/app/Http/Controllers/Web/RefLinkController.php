<?php
// app/Http/Controllers/Web/RefLinkController.php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MarketingRef;
use Illuminate\Http\Request;

class RefLinkController extends Controller
{
    private const DEFAULT_TARGET = '/register';

    // GET /r/{ref:code} أو /r/{ref:code}/{path?}
    public function hit(Request $request, MarketingRef $ref, ?string $path = null)
    {
        $targetPath = '/'.ltrim($path ?: ltrim(self::DEFAULT_TARGET, '/'), '/');

        if (! $ref->isUsableForPath($targetPath)) {
            abort(404);
        }

        try {
            $ref->hits()->create([
                'ip'         => substr($request->ip() ?? '', 0, 64),
                'user_agent' => substr($request->userAgent() ?? '', 0, 255),
                'path'       => $targetPath,
            ]);
        } catch (\Throwable $e) {}

        cookie()->queue(cookie('mr_code', $ref->code, 60*24*7));

        return redirect($targetPath);
    }
}
