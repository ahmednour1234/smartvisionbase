<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\ClientQrMail;
use App\Mail\VerifyCodeMail;
use App\Models\Client;
use App\Models\Form;
use App\Models\HomeSection;
use App\Models\Qrcode as QrcodeModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class RegisterController extends Controller
{
    /** Hudur API */
    private const HUDUR_URL = 'https://www.hudur.net/WebSiteApi/regData.php';
    private const HUDUR_EVENT_CODE = 'Z7hblo5iUczXGpaDEaEsAA==';

    public function index()
    {
        $section = HomeSection::find(10);

        if ($section && (int) $section->is_active === 1) {
            return view('web.content.register');
        }

        return view('web.content.closed')
            ->with('danger', 'التسجيل مقفول حاليًا.');
    }

    public function becomesponsor()
    {
        $section = HomeSection::find(11);

        if ($section && (int) $section->is_active === 1) {
            return view('web.content.becomesponsor');
        }

        return view('web.content.closesponsor')
            ->with('danger', 'التسجيل مقفول حاليًا.');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email',
            'phone'        => 'required|string|max:100',
            'job'          => 'required|string|max:255',
            'country_code' => 'required|string|max:10',
            'company_name' => 'nullable|string|max:255',
            'img'          => 'nullable|image|max:5120',
            'type'         => 'required|in:1,2', // 1: Email flow, 2: SMS notify admin
        ]);

        $data['email'] = Str::lower(trim($data['email']));

        $latestForm = Form::latest('id')->first();
        if (! $latestForm) {
            return back()->withErrors(['form_id' => 'لا يوجد نموذج form حالياً.'])->withInput();
        }
        $data['form_id'] = $latestForm->id;

        $client = Client::create($data + ['status' => 'pending']);

        if ($request->hasFile('img')) {
            $path = $request->file('img')->store('clients', 'public');
            $client->update(['img' => $path]);
        }

        // type=2 → SMS إشعار للإدارة فقط
        if ((int) $data['type'] === 2) {
            $this->notifyAdminSms($data);
            return back()->with('success', 'تم تسجيلك بنجاح، سنقوم بالتواصل معك قريبًا');
        }

        // type=1:
        if ($this->isOfficialEmail($client->email)) {
            try {
                // ✅ OTP + Hudur send (مطلوب منك)
                $this->sendVerificationCode($client);
            } catch (\Throwable $e) {
                Log::error('Send verify code failed: ' . $e->getMessage());
                return back()->withErrors(['email' => 'تعذر إرسال كود التحقق حاليًا. حاول لاحقًا.']);
            }

            return redirect()
                ->route('web.register.verify.form', ['email' => $client->email])
                ->with('success', 'تم إرسال كود التحقق إلى بريدك.');
        }

        return redirect()
            ->route('web.home')
            ->with('warning', 'تم إنشاء حسابك بحالة Pending. سيتم مراجعته والتواصل معك قريبًا.');
    }

    public function showVerifyForm(Request $request)
    {
        $email = $request->query('email');

        if ($email && ! $this->isOfficialEmail($email)) {
            return redirect()
                ->route('web.home')
                ->with('warning', 'هذا البريد غير مؤهل لخطوة التحقق. حسابك Pending حاليًا.');
        }

        return view('web.content.verify', compact('email'));
    }

    public function verify(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'code'  => 'required|digits:6',
        ]);

        $email = Str::lower(trim($data['email']));

        if (! $this->isOfficialEmail($email)) {
            return redirect()
                ->route('web.home')
                ->with('success', 'You are registered as "pending". Company email is required for verification and QR');
        }

        $code = preg_replace('/\D/', '', $data['code']);

        $client = Client::where('email', $email)
            ->whereNotNull('verify_code_hash')
            ->orderByDesc('verify_code_expires_at')
            ->first();

        if (! $client) {
            return back()->withErrors(['email' => 'لا يوجد كود تحقق فعّال لهذا البريد.']);
        }

        if ($client->verify_code_expires_at && now()->greaterThan($client->verify_code_expires_at)) {
            return back()->withErrors(['code' => 'الكود منتهي الصلاحية. أعد الإرسال من فضلك.']);
        }

        $given = hash('sha256', $code);
        if (! hash_equals($client->verify_code_hash, $given)) {
            return back()->withErrors(['code' => 'الكود غير صحيح.']);
        }

        // نجاح التحقق
        $client->email_verified_at      = now();
        $client->verify_code_hash       = null;
        $client->verify_code_expires_at = null;

        try {
            $this->generateAndSendQrCode($client);
            $client->status = 'verified';
            $client->save();

            // ✅ بعد verify ابعت Hudur (مطلوب منك)
            $this->sendToHudur($client);

            return redirect()
                ->route('web.thank.you')
                ->with([
                    'next_url' => route('web.home'),
                    'success'  => 'Your email is verified. Your QR code has been sent to your inbox.',
                ]);
        } catch (\Throwable $e) {
            Log::error("QR generation/mail error: " . $e->getMessage());

            $client->status = 'verified';
            $client->save();

            // حتى لو فشل QR/email، ما نضيّعش Hudur (اختياري بس مفيد)
            try {
                $this->sendToHudur($client);
            } catch (\Throwable $ex) {
                Log::error("Hudur send failed after verify (fallback): " . $ex->getMessage());
            }

            return redirect()
                ->route('web.thank.you')
                ->with([
                    'next_url' => route('web.home'),
                    'success'  => 'تم التحقق من بريدك. حدثت مشكلة مؤقتة في إرسال الـ QR — فضلاً تواصل معنا.',
                ]);
        }
    }

    public function resendCode(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $email = Str::lower(trim($request->email));

        if (! $this->isOfficialEmail($email)) {
            return redirect()
                ->route('web.home')
                ->with('warning', 'هذا البريد غير مؤهل لاستقبال كود التحقق. حسابك Pending.');
        }

        $client = Client::where('email', $email)->orderByDesc('id')->first();
        if (! $client) {
            return back()->withErrors(['email' => 'هذا البريد غير مسجل.']);
        }

        try {
            // ✅ resend OTP + Hudur (نفس قاعدة "وأنت بتبعتله كود")
            $this->sendVerificationCode($client);
        } catch (\Throwable $e) {
            Log::error("Resend verify code error: " . $e->getMessage());
            return back()->withErrors(['email' => 'تعذر إرسال الكود الآن. حاول لاحقًا.']);
        }

        return back()->with('success', 'تم إرسال كود تحقق جديد إلى بريدك.');
    }

    public function thankYou(Request $request)
    {
        $next = $request->session()->pull('next_url', null);

        if (empty($next)) {
            return redirect()->route('web.home');
        }

        return view('web.thank-you', ['nextUrl' => $next]);
    }

    /* ================= Helpers ================= */

    protected function isOfficialEmail(string $email): bool
    {
        $domain = Str::lower(Str::after($email, '@'));
        $free = [
            'gmail.com','yahoo.com','hotmail.com','outlook.com','live.com','msn.com','aol.com',
            'icloud.com','proton.me','protonmail.com','yandex.com','gmx.com','zoho.com','mail.com'
        ];

        return ! in_array($domain, $free, true);
    }

    protected function sendVerificationCode(Client $client): void
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $client->verify_code_hash       = hash('sha256', $code);
        $client->verify_code_expires_at = now()->addMinutes(20);
        $client->save();

        // ✅ ارسال OTP
        Mail::to($client->email)->send(new VerifyCodeMail($client, $code));

        // ✅ وده المطلوب: وانت بتبعتله كود للابمييل ابعت Hudur كمان
        try {
            $this->sendToHudur($client);
        } catch (\Throwable $e) {
            Log::error("Hudur send failed during OTP send: " . $e->getMessage());
        }
    }

    /**
     * إرسال بيانات العميل إلى Hudur (form-data)
     */
    protected function sendToHudur(Client $client): void
    {
        $mobile = trim(($client->country_code ?? '') . ($client->phone ?? ''));

        $payload = [
            'eventCode' => self::HUDUR_EVENT_CODE,
            'name'      => (string) $client->name,
            'mobile'    => (string) $mobile,
            'email'     => (string) $client->email,
            'job'       => (string) $client->job,
        ];

        $response = Http::asForm()
            ->timeout(20)
            ->post(self::HUDUR_URL, $payload);

        if (! $response->successful()) {
            Log::warning('Hudur API failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
                'email'  => $client->email,
            ]);
            return;
        }

        // لو رجع JSON زي الصورة
        Log::info('Hudur API success', [
            'email' => $client->email,
            'resp'  => $response->json(),
        ]);
    }

    protected function generateAndSendQrCode(Client $client): void
    {
        do {
            $shortCode = Str::upper(Str::random(10));
        } while (QrcodeModel::where('short_code', $shortCode)->exists());

        $qrUrl = url('/qr/' . $shortCode);

        File::ensureDirectoryExists(public_path('qrcode'));
        $fileName     = 'qr_' . $client->id . '_' . time() . '.png';
        $relativePath = '/qrcode/' . $fileName;
        $absolutePath = public_path($relativePath);

        $png = QrCode::format('png')
            ->size(300)
            ->errorCorrection('H')
            ->generate($qrUrl);

        File::put($absolutePath, $png);

        // لو عندك مشكلة في asset هنا، غالباً الصحيح:
        // $qrImageUrl = asset(ltrim($relativePath, '/'));
        $qrImageUrl = asset(ltrim($relativePath, '/'));

        QrcodeModel::create([
            'qrcode'      => $relativePath,
            'active'      => 1,
            'short_code'  => $shortCode,
            'email'       => $client->email,
            'scan'        => 0,
            'register_id' => $client->id,
        ]);

        Mail::to($client->email)->send(new ClientQrMail($client, $qrUrl, $qrImageUrl));
    }

    protected function notifyAdminSms(array $data): void
    {
        try {
            $url  = "https://app.community-ads.com/SendSMSAPI/api/SMSSender/SendSMS";
            $uuid = Str::uuid()->toString();

            $smsText  = "New Smart Vision Summit Dubai Sponsor\n";
            $smsText .= "Name: {$data['name']}\n";
            $smsText .= "Email: {$data['email']}\n";
            $smsText .= "Phone: {$data['country_code']}{$data['phone']}\n";
            $smsText .= "Source: https://dubai.smartvisionexpo.com//\n";
            $smsText .= "Position: {$data['job']}\n";
            $smsText .= "Company: {$data['company_name']}";

            $postData = [
                "UserName"    => "Smartvision2",
                "Password"    => "=-bZ%Jp_UI",
                "SMSText"     => $smsText,
                "SMSLang"     => "e",
                "SMSSender"   => "SmartVision",
                "SMSReceiver" => "01224984005",
                "SMSID"       => $uuid,
            ];

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_HTTPHEADER     => ["Content-Type: application/json"],
                CURLOPT_POSTFIELDS     => json_encode($postData, JSON_UNESCAPED_UNICODE),
                CURLOPT_TIMEOUT        => 20,
            ]);
            $response = curl_exec($ch);
            $error    = curl_error($ch);
            curl_close($ch);

            if ($error) Log::error("cURL SMS Error: $error");
            else        Log::info("SMS sent. Response: $response");
        } catch (\Throwable $e) {
            Log::error("SMS Exception: " . $e->getMessage());
        }
    }
}
