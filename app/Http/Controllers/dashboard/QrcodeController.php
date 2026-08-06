<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\Qrcode as QrcodeModel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Mail\ClientQrMail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class QrcodeController extends Controller
{
    public function index(Request $request)
    {
        $qrcodes = QrcodeModel::query();

        if ($request->filled('register_id')) {
            $qrcodes->where('register_id', $request->register_id);
        }


        if ($request->filled('short_code')) {
            $qrcodes->where('short_code', $request->short_code);
        }

        if ($request->filled('from_date')) {
            $qrcodes->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $qrcodes->whereDate('created_at', '<=', $request->to_date);
        }

        $qrcodes = $qrcodes->latest()->paginate(20);

        return view('content.qrcodes.index', compact('qrcodes'));
    }

    public function toggleStatus($id)
    {
        $qrcode = QrcodeModel::findOrFail($id);
        $qrcode->active = !$qrcode->active;
        $qrcode->save();

        return redirect()->back()->with('success', 'تم تحديث الحالة بنجاح');
    }

public function regenerateAndResend($id)
{
    $qrcode = QrcodeModel::findOrFail($id);
    $client = Client::findOrFail($qrcode->register_id);

    // 1) توليد short_code فريد
    do {
        $shortCode = Str::random(10);
    } while (QrcodeModel::where('short_code', $shortCode)->exists());

    $qrUrl = url('/qr/' . $shortCode);

    // 2) تجهيز مسار التخزين داخل storage/app/public/qrcodes
    $disk = Storage::disk('public');           // يعادل storage/app/public
    $dir  = 'qrcodes';

    if (!$disk->exists($dir)) {
        $disk->makeDirectory($dir);
    }

    // 3) إنشاء اسم ملف وحفظه بمسار مطلق
    $fileName     = 'qr_' . $client->id . '_' . now()->format('YmdHis') . '.png';
    $relativePath = $dir . '/' . $fileName;    // يُخزن في قاعدة البيانات
    $absolutePath = $disk->path($relativePath); // مسار مطلق للكتابة الفعلية

    // 4) توليد صورة الـ QR مباشرة إلى الملف
    QrCode::format('png')
        ->size(300)
        ->margin(1)
        ->generate($qrUrl, $absolutePath);

    // 5) حذف الصورة القديمة لو موجودة
    if (!empty($qrcode->qrcode) && $disk->exists($qrcode->qrcode)) {
        $disk->delete($qrcode->qrcode);
    }

    // 6) تحديث السجل (خزّن المسار النسبي داخل عمود qrcode)
    $qrcode->update([
        'qrcode'     => $relativePath,   // مثال: qrcodes/qr_123_20250816....png
        'short_code' => $shortCode,
    ]);

    // 7) إنشاء رابط عام للصورة وإرسال الإيميل
    $qrImageUrl = $disk->url($relativePath);   // مثال: /storage/qrcodes/qr_...
    Mail::to($client->email)->send(new ClientQrMail($client, $qrUrl, $qrImageUrl));

    return redirect()->back()->with('success', 'تم توليد كود جديد وإرسال البريد');
}
    /**
     * صفحة ماسح QR
     */
    public function scanner()
    {
        return view('content.qrcodes.scan');
    }

    /**
     * فحص كود QR وتسجيل الحضور
     */
    public function check(Request $request)
    {
        $code = trim((string) $request->input('code'));

        if ($code === '') {
            return response()->json([
                'status'  => 'error',
                'message' => 'برجاء إدخال الكود.',
            ], 422);
        }

        // استخراج الكود من رابط كامل إن لزم
        if (Str::startsWith($code, ['http://', 'https://'])) {
            $parts = explode('/', rtrim($code, '/'));
            $code  = end($parts);
        }

        $qr = QrcodeModel::where('short_code', $code)->first();

        if (!$qr) {
            return response()->json([
                'status'  => 'error',
                'message' => 'رمز QR غير صالح.',
            ], 404);
        }

        if (isset($qr->active) && !$qr->active) {
            return response()->json([
                'status'  => 'error',
                'message' => 'هذا الكود غير فعّال.',
            ], 403);
        }

        if ((int) $qr->scan > 0) {
            return response()->json([
                'status'     => 'scanned',
                'message'    => 'تم استخدام الرمز مسبقًا.',
                'short_code' => $code,
                'scan_at'    => $qr->scan_at ?? $qr->updated_at ?? $qr->created_at,
            ]);
        }

        // تسجيل الحضور لأول مرة
        $qr->scan = 1;
        // لو عندك عمود scan_at هيتم تخزين الوقت
        if ($qr->isFillable('scan_at') || \Schema::hasColumn($qr->getTable(), 'scan_at')) {
            $qr->scan_at = now();
        }
        $qr->save();

        return response()->json([
            'status'     => 'success',
            'message'    => 'تم تسجيل الحضور بنجاح.',
            'short_code' => $code,
            'scan_at'    => $qr->scan_at ?? $qr->updated_at ?? $qr->created_at,
        ]);
    }

    /**
     * قائمة الحضور (scan = 1) مع بيانات العميل وتاريخ الحضور
     */
    public function attendees(Request $request)
    {
        $table = (new QrcodeModel)->getTable(); // اسم الجدول (مثلاً: qrcodes)

        $attendees = QrcodeModel::query()
            ->where($table . '.scan', 1)
            ->when($request->filled('register_id'), fn($q) =>
                $q->where($table . '.register_id', $request->register_id)
            )
            ->when($request->filled('short_code'), fn($q) =>
                $q->where($table . '.short_code', $request->short_code)
            )
            // فلترة بالتاريخ على attendance_at (scan_at أولاً)
            ->when($request->filled('from_date'), fn($q) =>
                $q->whereDate(
                    DB::raw("COALESCE($table.scan_at, $table.updated_at, $table.created_at)"),
                    '>=',
                    request('from_date')
                )
            )
            ->when($request->filled('to_date'), fn($q) =>
                $q->whereDate(
                    DB::raw("COALESCE($table.scan_at, $table.updated_at, $table.created_at)"),
                    '<=',
                    request('to_date')
                )
            )
            ->leftJoin('clients', 'clients.id', '=', $table . '.register_id')
            ->select([
                $table . '.id',
                $table . '.register_id',
                $table . '.short_code',
                $table . '.scan',
                DB::raw("COALESCE($table.scan_at, $table.updated_at, $table.created_at) AS attendance_at"),
                'clients.name  as client_name',
                'clients.email as client_email',
                'clients.phone as client_phone',
            ])
            ->orderByDesc('attendance_at')
            ->paginate(20)
            ->withQueryString();

        return view('content.qrcodes.attendees', compact('attendees'));
    }

}
