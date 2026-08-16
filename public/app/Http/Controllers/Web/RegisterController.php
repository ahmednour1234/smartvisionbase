<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\ClientQrMail;
use App\Mail\SponsorWelcomeMail;
use App\Mail\VerifyCodeMail;
use App\Models\Client;
use App\Models\Form;
use App\Models\HomeSection;
use App\Models\Qrcode as QrcodeModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;
use App\Services\WhatsAppClient; 
class RegisterController extends Controller
{
public function index()
{
    // نجيب سيكشن رقم 12
    $section = HomeSection::find(12);

    // لو موجود ومفعل → افتح صفحة التسجيل
    if ($section && (int) $section->is_active === 1) {
        return view('web.content.register');
    }

    // غير كده → مقفول
    return view('web.content.closed')
        ->with('danger', 'التسجيل مقفول حاليًا.');
}

 
 public function selectCategory(Request $request)
    {
        $data = $request->validate([
            'category'    => 'required|string|max:255',
            'section'     => 'required|string|max:255',
            'section_key' => 'nullable|string|max:50',
        ]);

        // لو كان فيه اختيار سابق، امسحه وخلي الجديد
        session()->forget(['selected_category', 'selected_section', 'selected_section_key']);

        session([
            'selected_category'    => $data['category'],
            'selected_section'     => $data['section'],
            'selected_section_key' => $data['section_key'] ?? null,
        ]);

        return response()->json(['success' => true]);
    }

    public function nextPage()
    {
        if (!session()->has('selected_category') || !session()->has('selected_section')) {
            return redirect()->route('web.register')
                ->withErrors(['Please select a category first.']);
        }

        // هنا اعرض الصفحة التالية، ومعاك في الـ session:
        // selected_category, selected_section, selected_section_key
        return view('web.content.register_form', [
            'category' => session('selected_category'),
            'section'  => session('selected_section'),
            'section_key' => session('selected_section_key'),
        ]);
    }
    public function becomesponsor()
    {
        return view('web.content.becomesponsor');
    }

    /**
     * Registration: creates the client, decides status based on email domain,
     * sends verification code. Redirect:
     *  - Official email  -> verify page
     *  - Non-official    -> home page
     */

public function store(Request $request)
{
    $data = $request->validate([
        'name'          => 'required|string|max:255',
'email' => ['required', 'email', 'unique:clients,email'],
        'phone'         => 'required|string|max:30',
        'job'           => 'required|string|max:255',
        'country_code'  => 'required|string|max:10',
        'company_name'  => 'nullable|string|max:255',
        'img'           => 'nullable|image',
        'section'       => 'nullable',
        'category'      => 'nullable',
        'type'          => 'required|in:1,2', // 1 visitor – 2 sponsor
    ]);

    // Normalize email
    $data['email'] = Str::lower(trim($data['email']));

    $latestForm = Form::latest('id')->first();
    if (!$latestForm) {
        return back()->withErrors(['form_id' => 'No active form is available now.'])->withInput();
    }

    $isOfficial      = $this->isOfficialEmail($data['email']);
    $data['form_id'] = $latestForm->id;
    $data['status']  = $isOfficial ? 'awaiting_verification' : 'pending';

    $client = Client::create($data);

    // ======= [جديد] رسالة ترحيب واتساب عبر Template hello_world =======
    try {
        $wa   = app(WhatsAppClient::class);
        $to   = WhatsAppClient::e164Digits($data['country_code'], $data['phone']); // مثال: "201276845745"
        // لو عندك Template مختلف فيه متغيرات Body، مرّر components
        $wa->sendTemplate(
            to: $to,
            templateName: 'hello_world',
            langCode: 'en_US',
            components: [] // أمثلة للمتغيرات: [['type'=>'body','parameters'=>[['type'=>'text','text'=>$client->name]]]]
        );
        // Log::info('WA hello_world sent', ['to' => $to]);
    } catch (\Throwable $e) {
        Log::warning('WhatsApp welcome failed: '.$e->getMessage(), ['client_id' => $client->id]);
    }
    // ================================================================

    $nextUrl = route('web.home'); // الوجهة النهائية بعد thank-you

    // ========== Sponsor flow ==========
    if ((int) $data['type'] === 2) {
        // SMS لغير الرسمي فقط
        if (! $isOfficial) {
            try { $this->notifySponsorBySms($data); } catch (\Throwable $e) { Log::error("SMS error: ".$e->getMessage()); }
        }

        if ($isOfficial) {
            try { Mail::to($client->email)->send(new SponsorWelcomeMail($client)); } catch (\Throwable $e) { Log::error("Sponsor mail error: ".$e->getMessage()); }
            try { $this->sendVerificationCode($client); } catch (\Throwable $e) { Log::error("Verify code (sponsor) mail error: ".$e->getMessage()); }

            return redirect()
                ->route('web.verify.form', ['email' => $client->email])
                ->with('success', 'We sent you a verification code. Please verify your email.');
        }

        return redirect()
            ->route('web.thank.you')
            ->with([
                'next_url' => $nextUrl,
                'success'  => 'Thanks for your sponsorship request. Your request is pending review.',
            ]);
    }

    // ========== Visitor flow ==========
    if ($isOfficial) {
        try { $this->sendVerificationCode($client); } catch (\Throwable $e) {
            Log::error("Verify code mail error: ".$e->getMessage());
        }

        return redirect()
            ->route('web.register.verify.form', ['email' => $client->email])
            ->with('success', 'We sent a verification code to your company email.');
    }

    return redirect()
        ->route('web.thank.you')
        ->with([
            'next_url' => $nextUrl,
            'success'  => 'You are registered as "pending". Company email is required for verification and QR.',
        ]);
}

public function thankYou(Request $request)
{
    // اسحب next_url من السيشن (one-time). لو مش موجودة يبقى null
    $next = $request->session()->pull('next_url', null);

    // لو مفيش Next رجّع للـ web.home
    if (empty($next)) {
        return redirect()->route('web.home');
    }

    // غير كده، اعرض صفحة الشكر اللي بتحوّل تلقائيًا للـ next
    return view('web.thank-you', ['nextUrl' => $next]);
}

/**
 * Show the OTP page with 6 separate inputs (only code visible).
 * Email is passed via query and injected as a hidden field.
 */
public function showVerifyForm(Request $request)
{
    $email = $request->query('email');
    return view('web.content.verify', compact('email'));
}

/**
 * Verify the OTP; if visitor -> generate QR and email it.
 * On success: go to thank-you (ثم Home).
 */
public function verify(Request $request)
{
    $data = $request->validate([
        'email' => 'required|email',
        'code'  => 'required|digits:6',
    ]);

    $email = Str::lower(trim($data['email']));
    $code  = preg_replace('/\D/', '', $data['code']); // digits only

    // pick the latest active code for this email
    $client = Client::where('email', $email)
        ->whereNotNull('verify_code_hash')
        ->orderByDesc('verify_code_expires_at')
        ->first();

    if (! $client) {
        return back()->withErrors(['email' => 'No active verification code for this email.']);
    }

    if (now()->greaterThan(Carbon::parse($client->verify_code_expires_at))) {
        return back()->withErrors(['code' => 'This code has expired. Please request a new one.']);
    }

    $given = hash('sha256', $code);
    if (! hash_equals($client->verify_code_hash, $given)) {
        return back()->withErrors(['code' => 'Incorrect code.']);
    }

    // success
    $client->email_verified_at       = now();
    $client->status                  = 'verified';
    $client->verify_code_hash        = null;
    $client->verify_code_expires_at  = null;
    $client->save();

    // رسالة النجاح + الوجهة
    $nextUrl  = route('web.home');
    $success  = 'Your email is verified.';

    // If visitor -> generate & send QR
    if ((int)$client->type === 1) {
        try {
            $this->generateAndSendQrCode($client);
            $success = 'Your email is verified. Your QR code has been sent to your inbox.';
        } catch (\Throwable $e) {
            Log::error("QR generation/mail error: " . $e->getMessage());
            $success = 'Your email is verified. We had an issue sending your QR — please contact support.';
        }
    }

    // بعد التحقق → thank-you → ومنها Home
    return redirect()
        ->route('web.thank.you')
        ->with([
            'next_url' => $nextUrl,
            'success'  => $success,
        ]);
}

/**
 * Resend OTP to email.
 */
public function resendCode(Request $request)
{
    $request->validate(['email' => 'required|email']);
    $email = Str::lower(trim($request->email));

    $client = Client::where('email', $email)->orderByDesc('id')->first();
    if (! $client) {
        return back()->withErrors(['email' => 'Account not found.']);
    }

    try {
        $this->sendVerificationCode($client);
    } catch (\Throwable $e) {
        Log::error("Resend verify code error: " . $e->getMessage());
        return back()->withErrors(['email' => 'Unable to send the code now. Please try again later.']);
    }

    return back()->with('success', 'A new verification code has been sent to your email.');
}

/* ================= Helpers (كما هي) ================= */

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
    // keep leading zeros possible in future if needed
    $n = random_int(0, 999999);
    $code = str_pad((string)$n, 6, '0', STR_PAD_LEFT);

    $client->verify_code_hash       = hash('sha256', $code);
    $client->verify_code_expires_at = now()->addMinutes(20);
    $client->save();

    Mail::to($client->email)->send(new VerifyCodeMail($client, $code));
}

protected function generateAndSendQrCode(Client $client): void
{
    $shortCode = Str::random(10);
    $qrUrl     = url('/qr/' . $shortCode);

    $directory = public_path('qrcodes');
    if (! is_dir($directory)) {
        mkdir($directory, 0755, true);
    }

    $fileName  = 'qr_' . time() . '_' . $client->id . '.png';
    $filePath  = $directory . '/' . $fileName;

    QrCode::format('png')->size(300)->generate($qrUrl, $filePath);
    $qrImageUrl = asset('public/qrcodes/' . $fileName);

    QrcodeModel::create([
        'qrcode'      => $fileName,
        'active'      => 1,
        'short_code'  => $shortCode,
        'email'       => $client->email,
        'scan'        => 0,
        'register_id' => $client->id,
    ]);

    Mail::to($client->email)->send(new ClientQrMail($client, $qrUrl, $qrImageUrl));
}

protected function notifySponsorBySms(array $data): void
{
    $url  = "https://app.community-ads.com/SendSMSAPI/api/SMSSender/SendSMS";
    $uuid = Str::uuid()->toString();

    $smsText  = "Affiliate summit global Sponsor\n";
    $smsText .= "Name: {$data['name']}\n";
    $smsText .= "Email: {$data['email']}\n";
$smsText .= "Phone: {$data['country_code']}{$data['phone']}\n";
    $smsText .= "Source: https://affiliatesummitglobal.com/\n";
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
        CURLOPT_POSTFIELDS     => json_encode($postData),
    ]);

    $response = curl_exec($ch);
    $error    = curl_error($ch);
    curl_close($ch);

    if ($error) Log::error("cURL Error while sending SMS: $error");
    else       Log::info("SMS sent successfully. Response: $response");
}


}
