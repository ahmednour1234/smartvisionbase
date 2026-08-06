<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'SmartVision') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Surface & inputs */
        input[type="text"], input[type="email"], input[type="tel"], input[type="url"], input[type="datetime-local"], input[type="password"], textarea, select {
            border: 1px solid #e5e7eb;
            border-radius: .75rem;
            padding: .8rem 1rem;
            background: #fff;
            transition: border-color .15s ease, box-shadow .15s ease, background-color .15s ease;
        }
        input::placeholder, textarea::placeholder { color: #9ca3af; }
        input:focus, textarea:focus, select:focus { outline: none; box-shadow: 0 0 0 3px rgba(239,68,68,.15); border-color: #ef4444; }

        /* Buttons */
        .btn-primary { background: linear-gradient(90deg, #ef4444, #0a0a0a); color: #fff; border-radius: .75rem; padding: .7rem 1.1rem; }
        .btn-primary:hover { filter: brightness(1.05); }
        .btn-outline { background: #fff; border: 1px solid #e5e7eb; border-radius: .75rem; padding: .7rem 1.1rem; }
        .btn-outline:hover { background: #f9fafb; }

        /* Card polish */
        .dash-card { border: 1px solid #e5e7eb; border-radius: 1rem; background: #ffffff; box-shadow: 0 10px 30px rgba(17,24,39,.06); }
        .dash-card:hover { box-shadow: 0 16px 40px rgba(17,24,39,.10); }

        /* Section titles */
        .form-section-title { font-size: .85rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: .02em; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-zinc-50 via-white to-zinc-100 text-gray-900">
    <div class="flex min-h-screen">
        @include('partials.dashboard.sidebar')
        <div class="flex-1 flex flex-col">
            @include('partials.dashboard.topbar')
            <main class="p-6 md:p-10">
                <x-alert />
                @yield('content')
            </main>
            <footer class="mt-auto border-t bg-gradient-to-r from-zinc-900 via-zinc-800 to-red-700 text-white/90">
                <div class="mx-auto max-w-7xl px-6 py-4 text-sm flex items-center justify-between">
                    <span>&copy; {{ date('Y') }} <a href="https://smartvisionsummit.com" target="_blank" rel="noopener noreferrer">Smart Vision Summit</a></span>
                    <span></span>
                </div>
            </footer>
        </div>
    </div>
</body>
</html>


