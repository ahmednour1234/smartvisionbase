<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ClientLoginRequest;
use App\Http\Requests\Client\ClientRegisterRequest;
use App\Http\Requests\Client\ClientUpdateRequest;
use App\Http\Resources\Client\ClientResource;
use App\Http\Resources\Qrcode\QrcodeResource;
use App\Mail\VerifyCodeMail;
use App\Models\Client;
use App\Models\Form;
use App\Models\Qrcode as QrcodeModel;
use App\Repositories\Client\ClientRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ClientAuthController extends Controller
{
    public function __construct(private ClientRepositoryInterface $repo) {}

    /** Register new client (with logging) */
    public function register(ClientRegisterRequest $request)
    {
        $traceId   = (string) Str::uuid();
        $startedAt = microtime(true);

        $data = $request->validated();
        $data['email'] = Str::lower(trim($data['email']));

        Log::info('ClientRegister START', [
            'trace_id' => $traceId,
            'email'    => $data['email'],
            'phone'    => $data['phone'] ?? null,
            'cc'       => $data['country_code'] ?? null,
        ]);

        $latestForm = Form::query()->latest('id')->first();
        if (!$latestForm) {
            Log::warning('ClientRegister NO_FORM', ['trace_id' => $traceId]);
            return response()->json([
                'message' => 'No active form is available now.',
                'errors'  => ['form_id' => ['No active form is available now.']],
                'trace_id'=> $traceId,
            ], 422);
        }
        $data['form_id'] = $latestForm->id;

        $isOfficial     = $this->isOfficialEmail($data['email']);
        $data['status'] = $isOfficial ? 'awaiting_verification' : 'pending';

        $client = $this->repo->create($data);
        Log::info('ClientRegister CREATED', [
            'trace_id'  => $traceId,
            'client_id' => $client->id,
            'status'    => $client->status,
            'isOfficial'=> $isOfficial,
        ]);

        // WhatsApp (اختياري)
        try {
            if (class_exists(\App\Services\WhatsAppClient::class)) {
                $wa = app(\App\Services\WhatsAppClient::class);
                $to = method_exists($wa, 'e164Digits')
                    ? $wa::e164Digits($data['country_code'] ?? '', $data['phone'] ?? '')
                    : ($data['country_code'] ?? '') . ($data['phone'] ?? '');
                $wa->sendTemplate(
                    to: $to,
                    templateName: 'hello_world',
                    langCode: 'en_US',
                    components: []
                );
                Log::info('WA hello_world SENT', ['trace_id' => $traceId, 'to' => $to]);
            }
        } catch (Throwable $e) {
            Log::warning('WA hello_world FAILED', [
                'trace_id'  => $traceId,
                'client_id' => $client->id,
                'error'     => $e->getMessage(),
                'file'      => $e->getFile(),
                'line'      => $e->getLine(),
            ]);
        }

        // رسمي: لا نُصدر توكن هنا
        if ($isOfficial) {
            $code = $this->makeSixDigits();
            $this->repo->saveVerifyCode($client, $code);

            Log::info('ClientRegister VERIFY_CODE_GENERATED', [
                'trace_id'   => $traceId,
                'client_id'  => $client->id,
                'expires_at' => optional($client->verify_code_expires_at)->toIso8601String(),
                'code_dbg'   => $code,
            ]);

            Log::info('MAILER CONTEXT', array_merge(['trace_id' => $traceId], $this->mailerContext()));

            try {
                Mail::to($client->email)->send(new VerifyCodeMail($client, $code));
                Log::info('VerifyMail SENT', [
                    'trace_id'  => $traceId,
                    'client_id' => $client->id,
                    'email'     => $client->email,
                ]);
            } catch (Throwable $e) {
                Log::error('VerifyMail FAILED', [
                    'trace_id'  => $traceId,
                    'client_id' => $client->id,
                    'email'     => $client->email,
                    'error'     => $e->getMessage(),
                    'file'      => $e->getFile(),
                    'line'      => $e->getLine(),
                ]);
            }

            Log::info('ClientRegister DONE', [
                'trace_id' => $traceId,
                'took_ms'  => (int) ((microtime(true) - $startedAt) * 1000),
            ]);

            return response()->json([
                'message'        => 'Account accepted. Verification code sent to your email.',
                'account_status' => 'awaiting_verification',
                'data'           => new ClientResource($client),
                'trace_id'       => $traceId,
            ], 201);
        }

        // عام (pending): نُصدر توكن فورًا
        $token = $client->createToken('client_api')->plainTextToken;

        Log::info('ClientRegister PENDING_FREE_EMAIL', [
            'trace_id'  => $traceId,
            'client_id' => $client->id,
        ]);

        Log::info('ClientRegister DONE', [
            'trace_id' => $traceId,
            'took_ms'  => (int) ((microtime(true) - $startedAt) * 1000),
        ]);

        return response()->json([
            'message'        => 'Account pending review.',
            'account_status' => 'pending',
            'token'          => $token,
            'data'           => new ClientResource($client),
            'trace_id'       => $traceId,
        ], 201);
    }

    /** Login — تُرجع سبب الخطأ بوضوح (email | password) */
    public function login(ClientLoginRequest $request)
    {
        $traceId   = (string) Str::uuid();
        $startedAt = microtime(true);

        $fcmToken  = trim((string) $request->input('fcm_token', ''));
        $platform  = trim((string) $request->input('platform', ''));
        $deviceId  = trim((string) $request->input('device_id', ''));

        $email  = Str::lower(trim($request->input('email')));
        $client = $this->repo->findByEmail($email);

        // إيميل مش موجود
        if (!$client) {
            Log::warning('ClientLogin EMAIL_NOT_FOUND', ['trace_id' => $traceId, 'email' => $email]);
            return response()->json([
                'message'  => 'Email not found',
                'reason'   => 'email',
                'trace_id' => $traceId
            ], 401);
        }

        // باسورد غير صحيحة
        if (!Hash::check($request->input('password'), $client->password)) {
            Log::warning('ClientLogin WRONG_PASSWORD', ['trace_id' => $traceId, 'client_id' => $client->id]);
            return response()->json([
                'message'  => 'Incorrect password',
                'reason'   => 'password',
                'trace_id' => $traceId
            ], 401);
        }

        $dirty = false;
        if ($fcmToken !== '' && $client->fcm_token !== $fcmToken) {
            $client->fcm_token = $fcmToken; $dirty = true;
        }
        if ($platform !== '' && property_exists($client, 'fcm_platform')) {
            $client->fcm_platform = $platform; $dirty = true;
        }
        if ($deviceId !== '' && property_exists($client, 'device_id')) {
            $client->device_id = $deviceId; $dirty = true;
        }
        if (property_exists($client, 'last_login_at')) {
            $client->last_login_at = now(); $dirty = true;
        }
        if (property_exists($client, 'last_login_ip')) {
            $client->last_login_ip = $request->ip(); $dirty = true;
        }
        if ($dirty) {
            try { $client->save(); } catch (\Throwable $e) {
                Log::warning('ClientLogin FCM_SAVE_FAILED', [
                    'trace_id'  => $traceId,
                    'client_id' => $client->id,
                    'error'     => $e->getMessage(),
                ]);
            }
        }

        $token = $client->createToken('client_api')->plainTextToken;

        Log::info('ClientLogin OK', [
            'trace_id'  => $traceId,
            'client_id' => $client->id,
            'status'    => $client->status,
            'took_ms'   => (int) ((microtime(true) - $startedAt) * 1000),
        ]);

        return response()->json([
            'message'        => 'Login successful',
            'account_status' => (strtolower((string)($client->status ?? '')) === 'complete') ? 'verify' : (string)($client->status ?? ''),
            'token'          => $token,
            'data'           => new ClientResource($client),
            'trace_id'       => $traceId,
        ]);
    }

    /** (NEW) Update only the authenticated client's image (file | base64 | url | delete) */
    public function updateImage(Request $request)
    {
        $client = $request->user('client');
        if (!$client) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'image'        => 'nullable',
            'img'          => 'nullable',
            'delete_image' => 'nullable|boolean',
        ]);

        $deleteFlag = $request->boolean('delete_image', false);

        /** @var UploadedFile|null $uploaded */
        $uploaded = $request->file('image') ?: $request->file('img');
        $strInput = $request->input('image', $request->input('img'));

        if ($deleteFlag && !$uploaded && !(is_string($strInput) && $strInput !== '')) {
            $this->safeDelete($client->img);
            $client->img = null;
            $client->save();

            return response()->json([
                'message' => 'Image removed successfully.',
                'data'    => new ClientResource($client),
            ]);
        }

        if (!$uploaded && !(is_string($strInput) && $strInput !== '')) {
            return response()->json(['message' => 'No image provided.'], 422);
        }

        try {
            $newPath = $this->storeClientImage($uploaded, $strInput);
        } catch (\Throwable $e) {
            Log::error('ClientUpdateImage STORE_FAILED', [
                'client_id' => $client->id,
                'error'     => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Failed to store image.'], 422);
        }

        if (!empty($client->img)) {
            $this->safeDelete($client->img);
        }

        $client->img = $newPath;
        $client->save();

        return response()->json([
            'message' => 'Image updated successfully.',
            'data'    => new ClientResource($client),
        ]);
    }

    private function storeClientImage(UploadedFile|string|null $uploaded, ?string $str = null): string
    {
        if ($uploaded instanceof UploadedFile) {
            if (!in_array(strtolower($uploaded->extension()), ['jpg','jpeg','png','webp','gif'])) {
                throw new \RuntimeException('Unsupported image type.');
            }

            if (class_exists(\App\Helpers\FileHelper::class) && method_exists(\App\Helpers\FileHelper::class, 'uploadImage')) {
                return \App\Helpers\FileHelper::uploadImage($uploaded, 'clients');
            }

            return $uploaded->store('clients', ['disk' => 'public']);
        }

        $str = (string) ($str ?? '');
        if ($str === '') {
            throw new \RuntimeException('Empty image input.');
        }

        if (preg_match('/^data:image\/([a-zA-Z0-9.+-]+);base64,/', $str, $m)) {
            if (class_exists(\App\Helpers\FileHelper::class) && method_exists(\App\Helpers\FileHelper::class, 'uploadBase64Image')) {
                return \App\Helpers\FileHelper::uploadBase64Image($str, 'clients');
            }

            [$meta, $b64] = explode(',', $str, 2);
            $ext = strtolower($m[1]);
            $ext = $ext === 'jpeg' ? 'jpg' : $ext;

            $data = base64_decode($b64, true);
            if ($data === false) throw new \RuntimeException('Invalid base64 payload.');

            $name = 'clients/'.Str::uuid().'.'.$ext;
            Storage::disk('public')->put($name, $data);
            return $name;
        }

        if (preg_match('/^https?:\/\//i', $str)) {
            if (class_exists(\App\Helpers\FileHelper::class) && method_exists(\App\Helpers\FileHelper::class, 'uploadFromUrl')) {
                return \App\Helpers\FileHelper::uploadFromUrl($str, 'clients');
            }
            return $str;
        }

        return $str;
    }

    private function safeDelete(?string $path): void
    {
        if (!is_string($path) || $path === '') return;
        if (preg_match('/^https?:\/\//i', $path)) return;

        try {
            if (class_exists(\App\Helpers\FileHelper::class) && method_exists(\App\Helpers\FileHelper::class, 'delete')) {
                \App\Helpers\FileHelper::delete($path);
                return;
            }

            $disk = Storage::disk('public');
            if (method_exists($disk, 'fileExists')) {
                if ($disk->fileExists($path)) {
                    $disk->delete($path);
                }
            } else {
                if ($disk->exists($path)) {
                    $disk->delete($path);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('ClientImage SAFE_DELETE_FAILED', ['path' => $path, 'error' => $e->getMessage()]);
        }
    }

    /** Send/Resend verification code for OFFICIAL emails only (auth required) */
    public function sendVerificationCode(Request $request)
    {
        $client = $request->user('client');
        if (!$client) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if (!$this->isOfficialEmail($client->email)) {
            return response()->json(['message' => 'Verification is available for official emails only.'], 422);
        }

        if (strtolower((string)$client->status) !== 'awaiting_verification') {
            $client->status = 'awaiting_verification';
            $client->save();
        }

        $code = $this->makeSixDigits();
        $this->repo->saveVerifyCode($client, $code);

        Log::info('ClientSendVerifyCode OK', [
            'client_id' => $client->id,
            'expires_at'=> optional($client->verify_code_expires_at)->toIso8601String(),
            'code_dbg'  => $code,
        ]);

        try {
            Mail::to($client->email)->send(new VerifyCodeMail($client, $code));
        } catch (Throwable $e) {
            Log::error('ClientSendVerifyCode FAILED', [
                'client_id' => $client->id,
                'error'     => $e->getMessage(),
            ]);
        }

        return response()->json([
            'message'        => 'Verification code sent to your email.',
            'account_status' => 'awaiting_verification',
        ]);
    }

    /** Verify account directly for OFFICIAL emails (auth required) — بدون إصدار توكن هنا */
    public function verifyOfficial(Request $request)
    {
        $client = $request->user('client');
        if (!$client) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if (!$this->isOfficialEmail($client->email)) {
            return response()->json(['message' => 'Direct verification is available for official emails only.'], 422);
        }

        if (strtolower((string)$client->status) !== 'awaiting_verification') {
            return response()->json(['message' => 'Your account is not awaiting verification.'], 422);
        }

        $data = $request->validate([
            'code' => ['required','digits:6'],
        ]);

        if (empty($client->verify_code_hash) || empty($client->verify_code_expires_at)) {
            return response()->json(['message' => 'No active verification code. Please request a new one.'], 422);
        }

        if (now()->greaterThan(Carbon::parse($client->verify_code_expires_at))) {
            return response()->json(['message' => 'This code has expired. Please request a new one.'], 422);
        }

        $given = hash('sha256', preg_replace('/\D/', '', $data['code']));
        if (!hash_equals($client->verify_code_hash, $given)) {
            return response()->json(['message' => 'Incorrect code.'], 422);
        }

        // Success → mark verified + clear code + send QR
        $client->email_verified_at = now();
        $client->status = 'verify';
        $client->save();
        $this->repo->clearVerifyCode($client);

        $qrSent = $this->generateAndSendQr($client);

        Log::info('ClientVerifyOfficial OK', ['client_id' => $client->id, 'qr_sent' => $qrSent]);

        // ملاحظة: لا يوجد token في الرد هنا
        return response()->json([
            'message'        => 'Account verified successfully.',
            'account_status' => 'verify',
            'qr_email_sent'  => (bool) $qrSent,
            'data'           => new ClientResource($client),
        ]);
    }

    /** Verify email with 6-digit code (public) — هنا فقط ننشئ التوكن بعد النجاح */
    public function verify(Request $request)
    {
        $traceId   = (string) Str::uuid();
        $startedAt = microtime(true);

        $data = $request->validate([
            'email' => ['required','email'],
            'code'  => ['required','digits:6'],
        ]);

        $email = Str::lower(trim($data['email']));
        $code  = preg_replace('/\D/', '', $data['code']);

        Log::info('ClientVerify START', ['trace_id' => $traceId, 'email' => $email]);

        $client = Client::where('email', $email)
            ->whereNotNull('verify_code_hash')
            ->orderByDesc('verify_code_expires_at')
            ->first();

        if (!$client) {
            Log::warning('ClientVerify NO_ACTIVE_CODE', ['trace_id' => $traceId, 'email' => $email]);
            return response()->json(['message' => 'No active verification code for this email.', 'trace_id' => $traceId], 422);
        }
        if (now()->greaterThan(Carbon::parse($client->verify_code_expires_at))) {
            Log::warning('ClientVerify CODE_EXPIRED', [
                'trace_id' => $traceId, 'client_id' => $client->id,
                'expires_at' => optional($client->verify_code_expires_at)->toIso8601String()
            ]);
            return response()->json(['message' => 'This code has expired. Please request a new one.', 'trace_id' => $traceId], 422);
        }

        $given = hash('sha256', $code);
        if (!hash_equals($client->verify_code_hash, $given)) {
            Log::warning('ClientVerify CODE_MISMATCH', ['trace_id' => $traceId, 'client_id' => $client->id]);
            return response()->json(['message' => 'Incorrect code.', 'trace_id' => $traceId], 422);
        }

        $client->email_verified_at = now();
        $client->status = 'verified';
        $client->save();
        $this->repo->clearVerifyCode($client);

        $qrSent = $this->generateAndSendQr($client);

        // === إصدار التوكن هنا فقط ===
        $token = $client->createToken('client_api')->plainTextToken;

        Log::info('ClientVerify OK', [
            'trace_id'  => $traceId,
            'client_id' => $client->id,
            'qr_sent'   => $qrSent,
            'took_ms'   => (int) ((microtime(true) - $startedAt) * 1000),
        ]);

        return response()->json([
            'message'        => 'Email verified successfully.',
            'account_status' => 'verified',
            'qr_email_sent'  => (bool) $qrSent,
            'token'          => $token,
            'data'           => new ClientResource($client),
            'trace_id'       => $traceId,
        ]);
    }

    /** Send forgot-password code */
    public function forgotPassword(Request $request)
    {
        $traceId   = (string) Str::uuid();
        $startedAt = microtime(true);

        $request->validate(['email' => ['required','email']]);
        $email = Str::lower(trim($request->input('email')));

        Log::info('ClientForgot START', ['trace_id' => $traceId, 'email' => $email]);

        $client = $this->repo->findByEmail($email);
        if (!$client) {
            Log::info('ClientForgot NO_ACCOUNT', ['trace_id' => $traceId, 'email' => $email]);
            return response()->json(['message' => 'If this email exists, a code has been sent.', 'trace_id' => $traceId]);
        }

        $code = $this->makeSixDigits();
        $this->repo->saveResetCode($client, $code);

        Log::info('ClientForgot RESET_CODE_GENERATED', [
            'trace_id'   => $traceId,
            'client_id'  => $client->id,
            'expires_at' => optional($client->reset_code_expires_at)->toIso8601String(),
            'code_dbg'   => $code,
        ]);

        Log::info('MAILER CONTEXT', array_merge(['trace_id' => $traceId], $this->mailerContext()));

        try {
            if (class_exists(\App\Mail\ResetPasswordCodeMail::class)) {
                Mail::to($client->email)->send(new \App\Mail\ResetPasswordCodeMail($client, $code));
                Log::info('ResetMail SENT', ['trace_id' => $traceId, 'client_id' => $client->id]);
            } else {
                Log::warning('ResetMail CLASS_NOT_FOUND', ['trace_id' => $traceId]);
            }
        } catch (Throwable $e) {
            Log::error('ResetMail FAILED', [
                'trace_id'  => $traceId,
                'client_id' => $client->id,
                'email'     => $client->email,
                'error'     => $e->getMessage(),
                'file'      => $e->getFile(),
                'line'      => $e->getLine(),
            ]);
        }

        Log::info('ClientForgot DONE', [
            'trace_id' => $traceId,
            'took_ms'  => (int) ((microtime(true) - $startedAt) * 1000),
        ]);

        return response()->json(['message' => 'If this email exists, a code has been sent.', 'trace_id' => $traceId]);
    }

    /** Reset password with code */
    public function resetPassword(Request $request)
    {
        $traceId   = (string) Str::uuid();
        $startedAt = microtime(true);

        $data = $request->validate([
            'email'    => ['required','email'],
            'code'     => ['required','digits:6'],
            'password' => ['required','string','min:6','confirmed'],
        ]);

        $email = Str::lower(trim($data['email']));
        $code  = preg_replace('/\D/', '', $data['code']);

        Log::info('ClientReset START', ['trace_id' => $traceId, 'email' => $email]);

        $client = Client::where('email', $email)->first();
        if (!$client || !$client->reset_code_hash) {
            Log::warning('ClientReset INVALID_EMAIL_OR_CODE', ['trace_id' => $traceId]);
            return response()->json(['message' => 'Invalid code or email.', 'trace_id' => $traceId], 422);
        }
        if (now()->greaterThan(Carbon::parse($client->reset_code_expires_at))) {
            Log::warning('ClientReset CODE_EXPIRED', [
                'trace_id' => $traceId,
                'client_id'=> $client->id,
                'expires_at' => optional($client->reset_code_expires_at)->toIso8601String()
            ]);
            return response()->json(['message' => 'This code has expired.', 'trace_id' => $traceId], 422);
        }

        $given = hash('sha256', $code);
        if (!hash_equals($client->reset_code_hash, $given)) {
            Log::warning('ClientReset CODE_MISMATCH', ['trace_id' => $traceId, 'client_id' => $client->id]);
            return response()->json(['message' => 'Incorrect code.', 'trace_id' => $traceId], 422);
        }

        $this->repo->updatePassword($client, $data['password']);
        $this->repo->clearResetCode($client);

        Log::info('ClientReset OK', [
            'trace_id'  => $traceId,
            'client_id' => $client->id,
            'took_ms'   => (int) ((microtime(true) - $startedAt) * 1000),
        ]);

        return response()->json(['message' => 'Password reset successfully.', 'trace_id' => $traceId]);
    }

    /** Profile of current client */
    public function me(Request $request)
    {
        $client = $request->user('client');
        return response()->json(['data' => new ClientResource($client)]);
    }

    /** Update profile (includes optional image update too) */
    public function updateProfile(ClientUpdateRequest $request)
    {
        $client = $request->user('client');

        $data = $request->safe()->except(['image', 'img', 'delete_image']);

        if ($request->boolean('delete_image')) {
            if (!empty($client->image) && class_exists(\App\Helpers\FileHelper::class) && method_exists(\App\Helpers\FileHelper::class, 'delete')) {
                try { \App\Helpers\FileHelper::delete($client->image); } catch (\Throwable $e) { /* ignore */ }
            } else {
                if (!empty($client->image) && Storage::disk('public')->exists($client->image)) {
                    try { Storage::disk('public')->delete($client->image); } catch (\Throwable $e) {}
                }
            }
            $data['image'] = null;
        }

        $uploaded = $request->file('image') ?: $request->file('img');
        if ($uploaded instanceof UploadedFile) {
            if (class_exists(\App\Helpers\FileHelper::class) && method_exists(\App\Helpers\FileHelper::class, 'uploadImage')) {
                $data['image'] = \App\Helpers\FileHelper::uploadImage($uploaded, 'clients');
            } else {
                $data['image'] = $uploaded->store('clients', ['disk' => 'public']);
            }
        } else {
            $str = $request->input('image', $request->input('img'));
            if (is_string($str) && $str !== '') {
                if (preg_match('/^data:image\\/(png|jpe?g|webp);base64,/', $str)) {
                    if (class_exists(\App\Helpers\FileHelper::class) && method_exists(\App\Helpers\FileHelper::class, 'uploadBase64Image')) {
                        $data['image'] = \App\Helpers\FileHelper::uploadBase64Image($str, 'clients');
                    } else {
                        $data['image'] = $str;
                    }
                } else {
                    $data['image'] = $str;
                }
            }
        }

        $updated = $this->repo->update($client, $data);

        Log::info('ClientUpdateProfile OK', ['client_id' => $client->id]);

        return response()->json([
            'message' => 'Profile updated.',
            'data'    => new ClientResource($updated),
        ]);
    }

    /** Logout (revoke current token) */
    public function logout(Request $request)
    {
        $client = $request->user('client');
        $client->currentAccessToken()?->delete();
        Log::info('ClientLogout OK', ['client_id' => $client->id]);
        return response()->json(['message' => 'Logged out']);
    }

    /* ================= Helpers ================= */

    protected function isOfficialEmail(string $email): bool
    {
        $domain = Str::lower(Str::after($email, '@'));
        $free = [
            'gmail.com','yahoo.com','hotmail.com','outlook.com','live.com','msn.com','aol.com',
            'icloud.com','proton.me','protonmail.com','yandex.com','gmx.com','zoho.com','mail.com'
        ];
        return !in_array($domain, $free, true);
    }

    protected function makeSixDigits(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /** Safe snapshot of mail config (no secrets) */
    protected function mailerContext(): array
    {
        $default = Config::get('mail.default');
        $from    = Config::get('mail.from', []);
        $mailer  = Config::get("mail.mailers.$default", []);

        unset($mailer['password'], $mailer['username'], $mailer['transport']);

        return [
            'mail_default' => $default,
            'mail_from'    => [
                'address' => $from['address'] ?? null,
                'name'    => $from['name'] ?? null,
            ],
            'mail_mailer'  => $mailer,
        ];
    }

    /** My QR Code (returns last active only; requires verified account) */
    public function myQrcode(Request $request)
    {
        $client = $request->user('client');
        if (!$client) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $status = strtolower((string)($client->status ?? ''));
        $rawStatus = strtolower(trim((string) $status));
        $normalized = match ($rawStatus) {
            'compelete' => 'complete',
            'verify'    => 'verified',
            'confirm'   => 'confirmed',
            default     => $rawStatus,
        };

        $accepted = ['verified', 'confirmed', 'complete'];
        $isVerified = (!empty($client->email_verified_at)) || in_array($normalized, $accepted, true);

        if (!$isVerified) {
            if ($status === 'pending') {
                return response()->json(['message' => 'Your account is pending organizer approval.'], 403);
            }
            return response()->json(['message' => 'Your account needs to be verified first.'], 403);
        }

        $qr = QrcodeModel::where('register_id', $client->id)
            ->where('active', 1)
            ->latest('id')
            ->first();

        if (!$qr) {
            return response()->json(['message' => 'No active QR code found for this account'], 404);
        }

        return response()->json(['data' => new QrcodeResource($qr)]);
    }

    /**
     * Generate & send QR (only if account is verified).
     * Will NOT send to public email domains unless $forceSend = true.
     */
    private function generateAndSendQr(Client $client, bool $forceSend = false): bool
    {
        $status = strtolower((string)($client->status ?? ''));
        if (empty($client->email_verified_at) || $status !== 'verified') {
            return false;
        }

        $emailDomain = strtolower((string) substr(strrchr($client->email, "@"), 1));
        $publicDomains = [
            'gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com',
            'aol.com', 'icloud.com', 'live.com', 'protonmail.com', 'proton.me'
        ];
        if (in_array($emailDomain, $publicDomains, true) && !$forceSend) {
            return false;
        }

        QrcodeModel::where('register_id', $client->id)->delete();

        $shortCode = Str::random(10);
        $qrUrl     = url('/qr/' . $shortCode);

        $directory = public_path('qrcodes');
        if (!file_exists($directory)) {
            @mkdir($directory, 0755, true);
        }

        $fileName   = 'qr_' . time() . '_' . $client->id . '.png';
        $filePath   = $directory . '/' . $fileName;

        $qrImageUrl = url('public/qrcodes/'.$fileName);

        if (class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
            \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(300)->generate($qrUrl, $filePath);
        } elseif (class_exists(\QrCode::class)) {
            \QrCode::format('png')->size(300)->generate($qrUrl, $filePath);
        } else {
            return false;
        }

        if (class_exists(\App\Mail\ClientQrMail::class)) {
            try {
                Mail::to($client->email)->send(new \App\Mail\ClientQrMail($client, $qrUrl, $qrImageUrl));
            } catch (Throwable $e) {
                Log::error('ClientQrMail FAILED', [
                    'client_id' => $client->id,
                    'error'     => $e->getMessage(),
                ]);
            }
        }

        QrcodeModel::create([
            'qrcode'      => $fileName,
            'active'      => 1,
            'short_code'  => $shortCode,
            'email'       => $client->email,
            'scan'        => 0,
            'register_id' => $client->id,
        ]);

        return true;
    }
}
