<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AuthChangePasswordRequest;
use App\Http\Requests\Api\AuthLoginRequest;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(AuthLoginRequest $request)
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();
        if (!$user || !$user->is_active) {
            AuditService::log($user?->id, 'login_failed', 'user', $user?->id, [], AuditService::ip($request));
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if (!Hash::check($data['password'], $user->password_hash)) {
            AuditService::log($user?->id, 'login_failed', 'user', $user?->id, [], AuditService::ip($request));
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Revoke previous tokens (single active session per user)
        $user->tokens()->delete();

        $token = $user->createToken('sv-crm')->plainTextToken;

        AuditService::log($user->id, 'login', 'user', $user->id, [], AuditService::ip($request));

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'must_change_password' => (int) $user->must_change_password,
            ],
        ]);
    }

    public function me(Request $request)
    {
        return response()->json(['user' => $request->user()]);
    }

    public function changePassword(AuthChangePasswordRequest $request)
    {
        $u = $request->user();
        if (!$u) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $data = $request->validated();

        if (!Hash::check($data['current_password'], $u->password_hash)) {
            return response()->json(['message' => 'Invalid current password'], 422);
        }

        $u->password_hash = Hash::make($data['new_password']);
        $u->must_change_password = 0;
        $u->save();

        AuditService::log($u->id, 'password_changed', 'user', $u->id, [], AuditService::ip($request));

        return response()->json(['message' => 'Password updated']);
    }

    public function logout(Request $request)
    {
        $u = $request->user();
        if ($u) {
            $u->tokens()->delete();
            AuditService::log($u->id, 'logout', 'user', $u->id, [], AuditService::ip($request));
        }

        return response()->json(['message' => 'Logged out']);
    }
}
