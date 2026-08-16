<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sponsor\SponsorLoginRequest;
use App\Http\Requests\Sponsor\SponsorRegisterRequest;
use App\Http\Requests\Sponsor\SponsorUpdatePasswordRequest;
use App\Http\Resources\Sponsor\SponsorResource;
use App\Models\Sponsor;
use App\Repositories\Sponsor\SponsorRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SponsorAuthController extends Controller
{
    public function __construct(private SponsorRepositoryInterface $repo)
    {
        // لو تفضل وسط الراوتس، احذف هذا
        $this->middleware('auth:sanctum')->only(['me','updatePassword','updateProfile','logout']);
    }

    /** Register */
    public function register(SponsorRegisterRequest $request)
    {
        $data = $request->validated();
        $sponsor = $this->repo->create($data);

        $token = $sponsor->createToken('sponsor_api')->plainTextToken;

        return response()->json([
            'message' => 'Registered successfully',
            'token'   => $token,
            'data'    => new SponsorResource($sponsor),
        ], 201);
    }

    /** Login */
    public function login(SponsorLoginRequest $request)
    {
        $sponsor = $this->repo->findByEmail($request->input('email'));

        if (!$sponsor || !Hash::check($request->input('password'), $sponsor->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // اجعل الجلسة وحيدة إذا أردت:
        // $sponsor->tokens()->delete();
    if ($request->filled('fcm_token')) {
        $sponsor->fcm_token = $request->input('fcm_token');
        $sponsor->save();
    }

        $token = $sponsor->createToken('sponsor_api')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token'   => $token,
            'data'    => new SponsorResource($sponsor),
        ]);
    }

    /** Current user */
    public function me(Request $request)
    {
        /** @var Sponsor $user */
        $user = $request->user(); // sanctum يرجع الـtokenable مباشرة
        return response()->json(['data' => new SponsorResource($user)]);
    }

    /** Update password */
    public function updatePassword(SponsorUpdatePasswordRequest $request)
    {
        /** @var Sponsor $sponsor */
        $sponsor = $request->user();

        if (!Hash::check($request->input('current_password'), $sponsor->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 422);
        }

        $this->repo->updatePassword($sponsor, $request->input('password'));

        // (اختياري) إبطال التوكن الحالي
        // $sponsor->currentAccessToken()?->delete();

        return response()->json(['message' => 'Password updated successfully']);
    }

    /** Update profile */
    public function updateProfile(Request $request)
    {
        /** @var Sponsor $sponsor */
        $sponsor = $request->user();

        $data = $request->validate([
            'name_ar'         => ['nullable','string','max:255'],
            'name_en'         => ['nullable','string','max:255'],
            'title_ar'        => ['nullable','string','max:255'],
            'title_en'        => ['nullable','string','max:255'],
            'company_name_ar' => ['nullable','string','max:255'],
            'company_name_en' => ['nullable','string','max:255'],
            'image'           => ['nullable','string','max:2048'],
            'country_code'    => ['nullable','string','max:5'],
        ]);

        $sponsor = $this->repo->update($sponsor, $data);

        return response()->json([
            'message' => 'Profile updated',
            'data'    => new SponsorResource($sponsor),
        ]);
    }

    /** Logout (revoke current token) */
    public function logout(Request $request)
    {
        /** @var Sponsor $sponsor */
        $sponsor = $request->user();
        $sponsor->currentAccessToken()?->delete();

        return response()->json(['message' => 'Logged out']);
    }
}
