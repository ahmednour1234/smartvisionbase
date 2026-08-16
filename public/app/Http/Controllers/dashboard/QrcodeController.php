<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Qrcode as QrcodeModel;
use App\Mail\ClientQrMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class QrcodeController extends Controller
{
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
    public function index(Request $request)
    {
        $q = QrcodeModel::query();

        if ($request->filled('register_id')) $q->where('register_id', $request->register_id);
        if ($request->filled('short_code'))  $q->where('short_code',  $request->short_code);
        if ($request->filled('from_date'))   $q->whereDate('created_at', '>=', $request->from_date);
        if ($request->filled('to_date'))     $q->whereDate('created_at', '<=', $request->to_date);

        $qrcodes = $q->latest()->paginate(20);
        return view('content.qrcodes.index', compact('qrcodes'));
    }

    public function toggleStatus($id)
    {
        $qr = QrcodeModel::findOrFail($id);
        $qr->active = ! $qr->active;
        $qr->save();
        return back()->with('success', 'تم تحديث الحالة بنجاح');
    }

    public function regenerateAndResends($id)
    {
        $qrRec  = QrcodeModel::findOrFail($id);
        $client = Client::findOrFail($qrRec->register_id);

        // تأكد من إعدادات البريد الأساسية
        $mailCheck = $this->isMailConfigured();
        if ($mailCheck !== true) {
            Log::error('Mail config invalid', ['issues' => $mailCheck]);
            return back()->with('error', 'إعدادات البريد غير كاملة. راجع ملف .env (MAIL_*, أو public mailer).');
        }

        try {
            // 1) مجلد الصور
            $dir = public_path('qrcodes');
            if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
                throw new \RuntimeException("Cannot create directory: {$dir}");
            }

            // 2) توليد بيانات QR
            $short = Str::random(10);
            $qrUrl = url('/qr/' . $short);
            $file  = 'qr_' . now()->format('YmdHis') . '_' . $client->id . '.png';
            $path  = $dir . '/' . $file;

            // 3) إنشاء الصورة
            QrCode::format('png')->size(300)->generate($qrUrl, $path);
            if (!file_exists($path)) {
                throw new \RuntimeException("QR file not found after generation: {$path}");
            }
            $qrImageUrl = asset('public/qrcodes/' . $file);

            // 4) حفظ في الداتابيز
            DB::transaction(function () use ($qrRec, $file, $short, $client) {
                $payload = [
                    'qrcode'     => $file,
                    'short_code' => $short,
                    'active'     => 1,
                    'scan'       => 0,
                ];
                if (schema()->hasColumn($qrRec->getTable(), 'email')) {
                    $payload['email'] = $client->email;
                }
                $qrRec->update($payload);
            });

            // 5) إرسال الإيميل عبر mailer مناسب للدومين
            $mailable = (new ClientQrMail($client, $qrImageUrl, $qrImageUrl))
                ->attach($path, ['as' => 'YourBadge.png', 'mime' => 'image/png']);

            $mailer = $this->pickMailerFor($client->email); // smtp | public | failover
            $useQueue = filter_var(env('MAIL_USE_QUEUE', false), FILTER_VALIDATE_BOOLEAN);

            if ($useQueue) {
                Mail::mailer($mailer)->to($client->email)->queue($mailable);
            } else {
                Mail::mailer($mailer)->to($client->email)->send($mailable);
            }

            Log::info('QR regenerated & email dispatched', [
                'qrcode_id' => $qrRec->id,
                'client_id' => $client->id,
                'email'     => $client->email,
                'mailer'    => $mailer,
                'queued'    => $useQueue,
            ]);

            return back()->with('success', 'تم توليد كود جديد وإرسال البريد بنجاح.');
        } catch (TransportExceptionInterface $e) {
            Log::error('SMTP transport failed', [
                'qrcode_id' => $id,
                'client_id' => $client->id ?? null,
                'email'     => $client->email ?? null,
                'error'     => $e->getMessage(),
            ]);
            return back()->with('error', 'تعذّر الإرسال عبر SMTP: ' . $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('QR regenerate/send failed', [
                'qrcode_id' => $id,
                'client_id' => $client->id ?? null,
                'email'     => $client->email ?? null,
                'error'     => $e->getMessage(),
            ]);
            return back()->with('error', 'فشل الإرسال: ' . $e->getMessage());
        }
    }

    public function scanner()
    {
        return view('content.qrcodes.scan');
    }

    public function check(Request $request)
    {
        $code = trim((string) $request->input('code'));
        if (\Illuminate\Support\Str::startsWith($code, ['http://', 'https://'])) {
            $parts = explode('/', rtrim($code, '/'));
            $code = end($parts);
        }

        $qr = QrcodeModel::where('short_code', $code)->first();

        if (!$qr) {
            return response()->json(['status' => 'error','message' => 'رمز QR غير صالح.'], 404);
        }
        if ((int)$qr->active !== 1) {
            return response()->json(['status' => 'inactive','message' => 'هذا الرمز غير مُفعّل.'], 403);
        }
        if ($qr->scan > 0) {
            return response()->json(['status' => 'scanned','message' => 'تم استخدام الرمز مسبقًا.'], 409);
        }

        $qr->increment('scan');

        return response()->json([
            'status' => 'success',
            'message' => 'تم تسجيل الحضور بنجاح.',
            'short_code' => $code,
        ]);
    }

    /* ================= Helpers ================= */

    private function isOfficialEmail(string $email): bool
    {
        $domain = strtolower(substr(strrchr($email, '@'), 1));
        $free = [
            'gmail.com','yahoo.com','hotmail.com','outlook.com','live.com','msn.com','aol.com',
            'icloud.com','proton.me','protonmail.com','yandex.com','gmx.com','zoho.com','mail.com'
        ];
        return ! in_array($domain, $free, true);
    }

    private function pickMailerFor(string $email): string
    {
        // لو عامل failover في config استخدمه دايمًا
        if (config('mail.mailers.failover')) {
            return 'failover';
        }

        $default = config('mail.default', 'smtp');
        $public  = env('PUBLIC_MAILER_NAME', 'public'); // اسم mailer لعناوين عامة

        if (!$this->isOfficialEmail($email) && config("mail.mailers.$public")) {
            return $public;
        }

        return $default;
    }

    private function isMailConfigured()
    {
        $keys = [
            'mail.default',
            'mail.from.address',
            'mail.mailers.smtp.host',
            'mail.mailers.smtp.port',
        ];
        $issues = [];
        foreach ($keys as $k) {
            if (!config($k)) $issues[] = $k;
        }
        return empty($issues) ? true : $issues;
    }
}

/* schema() shortcut */
if (! function_exists('schema')) {
    function schema() { return \Illuminate\Support\Facades\Schema::getFacadeRoot(); }
    
}
