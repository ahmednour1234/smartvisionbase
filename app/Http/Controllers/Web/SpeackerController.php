<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\HomeSection;
use App\Models\Speaker;
use Illuminate\Http\Request;

class SpeackerController extends Controller
{
    public function index(Request $request)
    {
        $speaker_section = HomeSection::where('is_active', true)
            ->where('id', 4)
            ->first();

        $perPage = 12;

        $speakers = Speaker::where('active', true)
            ->orderBy('orders', 'asc')
            ->paginate($perPage);

        // ✅ لو الطلب Ajax (من السكربت بتاع الـ infinite scroll) نرجّع فقط كروت السبيكرز
        if ($request->ajax()) {
            $html = view('web.content.partials._speaker_cards', [
                'speakers' => $speakers,
            ])->render();

            return response()->json([
                'html'      => $html,
                'next_page' => $speakers->currentPage() < $speakers->lastPage()
                    ? $speakers->currentPage() + 1
                    : null,
            ]);
        }

        // ✅ أوّل تحميل للصفحة (view كامل)
        return view('web.content.speaker', [
            'speaker_section' => $speaker_section,
            'speakers'        => $speakers,
        ]);
    }
}
