<header class="h-16 bg-gradient-to-r from-red-600 to-black text-white border-b flex items-center justify-between px-4 md:px-6">
    <div class="flex items-center gap-3">
        <button type="button" class="md:hidden inline-flex items-center justify-center rounded-md p-2 text-white/80 hover:bg-white/10">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        <h1 class="text-base md:text-lg font-semibold">@yield('page_title', 'Dashboard')</h1>
    </div>
    <div class="flex items-center gap-3">
        <span class="hidden sm:inline text-sm text-white/80">Hi, {{ Auth::user()->name ?? 'User' }}</span>
        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/10 text-white">
            {{ strtoupper(mb_substr(Auth::user()->name ?? 'U', 0, 1)) }}
        </span>
    </div>
</header>


