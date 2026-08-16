<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Speaker\SpeakerLoginRequest;
use App\Http\Requests\Speaker\SpeakerRegisterRequest;
use App\Http\Requests\Speaker\SpeakerUpdatePasswordRequest;
use App\Http\Resources\Speaker\SpeakerResource;
use App\Models\Speaker;
use App\Repositories\Speaker\SpeakerRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SpeakerAuthController extends Controller
{
    public function __construct(private SpeakerRepositoryInterface $repo) {}

    /** Register */
    public function register(SpeakerRegisterRequest $request)
    {
        $data = $request->validated();
        // تأكد من تشفير كلمة المرور
        $data['password'] = Hash::make($data['password']);

        /** @var Speaker $speaker */
        $speaker = $this->repo->create($data);

        // أنشئ توكن للوصول
        $token = $speaker->createToken('speaker_api')->plainTextToken;

        return response()->json([
            'message' => 'Registered successfully',
            'token'   => $token,
            'data'    => new SpeakerResource($speaker),
        ], 201);
    }

    /** Login */
    public function login(SpeakerLoginRequest $request)
    {
        /** @var Speaker|null $speaker */
        $speaker = $this->repo->findByEmail($request->input('email'));

        if (!$speaker || !Hash::check($request->input('password'), $speaker->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // (اختياري) اجعل الجلسة وحيدة
        // $speaker->tokens()->delete();

        $token = $speaker->createToken('speaker_api')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token'   => $token,
            'data'    => new SpeakerResource($speaker),
        ]);
    }

    /** Current user */
    public function me(Request $request)
    {
        /** @var Speaker $user */
        $user = $request->user('speaker'); // guard: speaker
        return response()->json(['data' => new SpeakerResource($user)]);
    }

    /** Update password */
    public function updatePassword(SpeakerUpdatePasswordRequest $request)
    {
        /** @var Speaker $speaker */
        $speaker = $request->user('speaker');

        if (!Hash::check($request->input('current_password'), $speaker->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 422);
        }

        $this->repo->updatePassword($speaker, $request->input('password'));

        // (اختياري) إبطال التوكن الحالي بعد التغيير
        // $speaker->currentAccessToken()?->delete();

        return response()->json(['message' => 'Password updated successfully']);
    }

    /** Update profile */
    public function updateProfile(Request $request)
    {
        /** @var Speaker $speaker */
        $speaker = $request->user('speaker');

        $data = $request->validate([
            'name_ar'         => ['nullable','string','max:255'],
            'name_en'         => ['nullable','string','max:255'],
            'title_ar'        => ['nullable','string','max:255'],
            'title_en'        => ['nullable','string','max:255'],
            'company_name_ar' => ['nullable','string','max:255'],
            'company_name_en' => ['nullable','string','max:255'],
            'image'           => ['nullable','string','max:2048'],
            'linkedin'        => ['nullable','string','max:2048'],
            'social_links'    => ['nullable','string','max:4096'],
            'youtube'         => ['nullable','string','max:2048'],
            'facebook'        => ['nullable','string','max:2048'],
            'tiktok'          => ['nullable','string','max:2048'],
            'instgram'        => ['nullable','string','max:2048'],
            'country_code'    => ['nullable','string','max:5'],
        ]);

        $speaker = $this->repo->update($speaker, $data);

        return response()->json([
            'message' => 'Profile updated',
            'data'    => new SpeakerResource($speaker),
        ]);
    }

    /** Logout (revoke current token) */
    public function logout(Request $request)
    {
        /** @var Speaker $speaker */
        $speaker = $request->user('speaker');

        // احذف التوكن الحالي فقط
        $speaker->currentAccessToken()?->delete();

        return response()->json(['message' => 'Logged out']);
    }
}
