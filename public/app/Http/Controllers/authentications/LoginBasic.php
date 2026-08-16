<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class LoginBasic extends Controller
{
    /**
     * Show login page
     */
    public function index()
    {
        // لو المستخدم بالفعل مسجّل دخول، وجّهه حسب نوعه
        if (Auth::check()) {
            return $this->redirectByType(Auth::user());
        }

        $pageConfigs = ['myLayout' => 'blank'];
        return view('content.authentications.auth-login-basic', ['pageConfigs' => $pageConfigs]);
    }

    /**
     * Handle login attempt
     */
    public function login(Request $request)
    {
        // Validate input
        $request->validate([
            'email-username' => 'required|string',
            'password'       => 'required|string',
        ]);

        $login    = (string) $request->input('email-username');
        $password = (string) $request->input('password');

        // Find user by email OR name (username)
        $user = User::query()
            ->where('email', $login)
            ->orWhere('name', $login)
            ->first();

        if ($user && Hash::check($password, $user->password)) {

            // جهّز الصلاحيات من الـ role->data (JSON) إن وُجد
            $permissions = [];
            $role        = $user->role ?? null;

            if ($role && !is_null($role->data)) {
                // decode آمن — في حال JSON غير صالح نرجّع مصفوفة فاضية
                $decoded = json_decode($role->data, true);
                $permissions = is_array($decoded) ? $decoded : [];
            }

            // خزّن الصلاحيات + نوع المستخدم في السيشن (مفيد للمنيو/الفلاتر)
            session([
                'permissions' => $permissions,
                'auth_type'   => (string) ($user->type ?? ''),
            ]);

            // Login + regenerate session to prevent fixation
            $remember = $request->boolean('remember');
            Auth::login($user, $remember);
            $request->session()->regenerate();

            // Redirect based on user type (callcenter -> /dashboard/leads, else analytics)
            return $this->redirectByType($user);
        }

        // Invalid credentials
        throw ValidationException::withMessages([
            'email-username' => ['The provided credentials are incorrect.'],
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Decide redirect target based on user type
     */
    protected function redirectByType(User $user)
    {
        $type = (string) ($user->type ?? '');

        // لو callcenter → leads
        if ($type === 'callcenter') {
            // استخدم intended عشان يحترم الصفحة المطلوبة لو كان فيه middleware redirect
            return redirect()->intended('/dashboard/leads');
        }

        // أي نوع تاني → الداشبورد الافتراضي
        return redirect()->route('dashboard-analytics');
    }
}
