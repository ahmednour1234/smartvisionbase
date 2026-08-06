<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthCookieController extends Controller
{
    /**
     * Sanctum SPA mode login (HttpOnly session cookie).
     * Flow:
     *  1) GET /sanctum/csrf-cookie
     *  2) POST /auth/login-cookie (with X-XSRF-TOKEN header automatically set by axios)
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !$user->is_active || !Hash::check($data['password'], $user->password_hash)) {
            AuditService::log($user?->id, 'login_failed', 'user', $user?->id, [], AuditService::ip($request));
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Session-based authentication (HttpOnly cookie) for SPA.
        Auth::login($user, (bool) ($data['remember'] ?? false));
        $request->session()->regenerate();

        AuditService::log($user->id, 'login_cookie', 'user', $user->id, [], AuditService::ip($request));

        return response()->json([
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

    public function logout(Request $request)
    {
        $u = $request->user();

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($u) {
            AuditService::log($u->id, 'logout_cookie', 'user', $u->id, [], AuditService::ip($request));
        }

        return response()->noContent();
    }
}
