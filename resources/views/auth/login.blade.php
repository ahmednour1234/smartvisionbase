<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - {{ config('app.name', 'SmartVision') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .glass { background: rgba(255,255,255,0.08); backdrop-filter: blur(10px); }
        .input-base {
            width: 100%;
            border-radius: .75rem;
            border: 1px solid rgba(255,255,255,.25);
            background: rgba(255,255,255,.08);
            color: #fff;
            padding: .75rem 1rem;
        }
        .input-base::placeholder { color: rgba(255,255,255,.7); }
        .input-base:focus { outline: none; box-shadow: 0 0 0 3px rgba(239,68,68,.35); border-color: rgba(239,68,68,.7); }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-red-700 via-black to-black text-white">
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="w-full max-w-md glass rounded-2xl border border-white/10 shadow-2xl p-8">
            <div class="text-center">
                <div class="text-2xl font-semibold">{{ config('app.name', 'SmartVision') }}</div>
                <div class="mt-1 text-white/70 text-sm">Dashboard Login</div>
            </div>
            <form method="POST" action="{{ route('login.perform') }}" class="mt-8 space-y-5">
                @csrf
                <div>
                    <label class="block text-sm text-white/80 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" class="input-base">
                    @error('email') <p class="text-sm text-red-300 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm text-white/80 mb-1">Password</label>
                    <input type="password" name="password" placeholder="••••••••" class="input-base">
                    @error('password') <p class="text-sm text-red-300 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-center justify-between">
                    <label class="inline-flex items-center gap-2 text-sm text-white/80">
                        <input type="checkbox" name="remember" class="h-4 w-4 rounded border-white/30 bg-white/10">
                        Remember me
                    </label>
                    <a href="{{ route('home') }}" class="text-sm text-white/70 hover:text-white">Back to site</a>
                </div>
                <button class="w-full rounded-xl bg-gradient-to-r from-red-600 to-black px-4 py-2.5 text-sm font-medium hover:from-red-500 hover:to-zinc-900 border border-white/10">
                    Sign in
                </button>
                @if ($errors->any())
                    <div class="text-sm text-red-300">
                        {{ $errors->first() }}
                    </div>
                @endif
            </form>
        </div>
    </div>
</body>
</html>


