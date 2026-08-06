<aside class="hidden md:flex md:flex-col w-64 bg-zinc-950 text-white border-r border-white/10">
    <div class="h-16 flex items-center px-6 border-b border-white/10">
        @php $logo = \App\Models\Setting::getValue('logo'); @endphp
        <a href="{{ route('dashboard.index') }}" class="text-lg font-semibold tracking-tight flex items-center gap-2">
            @if($logo)
                <img src="{{ asset($logo) }}" alt="SmartVision" class="h-7 w-auto">
            @else
                {{ config('app.name', 'SmartVision') }}
            @endif
        </a>
    </div>
    <nav class="flex-1 overflow-y-auto p-4 space-y-1">
        <a href="{{ route('dashboard.index') }}"
           class="group flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('dashboard.index') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/5' }}">
            <svg class="h-5 w-5 text-white/50 group-hover:text-white/70" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/>
            </svg>
            Dashboard
        </a>
        <a href="{{ route('dashboard.events.index') }}"
           class="group flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('dashboard.events.*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/5' }}">
            <svg class="h-5 w-5 text-white/50 group-hover:text-white/70" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3M3 11h18M5 19h14a2 2 0 002-2v-6H3v6a2 2 0 002 2z"/>
            </svg>
            Events
        </a>
        <a href="{{ route('dashboard.contacts.index') }}"
           class="group flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('dashboard.contacts.*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/5' }}">
            <svg class="h-5 w-5 text-white/50 group-hover:text-white/70" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
            </svg>
            Contact Messages
        </a>
        <a href="{{ route('dashboard.settings.edit') }}"
           class="group flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('dashboard.settings.*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/5' }}">
            <svg class="h-5 w-5 text-white/50 group-hover:text-white/70" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317a1.724 1.724 0 013.35 0l.262 1.118a1.724 1.724 0 001.516 1.324l1.136.088a1.724 1.724 0 011.004 2.96l-.86.742a1.724 1.724 0 000 2.593l.86.742a1.724 1.724 0 01-1.004 2.96l-1.136.088a1.724 1.724 0 00-1.516 1.324l-.262 1.118a1.724 1.724 0 01-3.35 0l-.262-1.118a1.724 1.724 0 00-1.516-1.324l-1.136-.088a1.724 1.724 0 01-1.004-2.96l.86-.742a1.724 1.724 0 000-2.593l-.86-.742a1.724 1.724 0 011.004-2.96l1.136-.088a1.724 1.724 0 001.516-1.324l.262-1.118z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Settings
        </a>
        <a href="{{ route('dashboard.users.index') }}"
           class="group flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('dashboard.users.*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/5' }}">
            <svg class="h-5 w-5 text-white/50 group-hover:text-white/70" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5V4H2v16h5m10 0v-4a3 3 0 00-3-3H10a3 3 0 00-3 3v4m10 0H7"/>
            </svg>
            Users
        </a>
        <a href="{{ route('dashboard.about.edit') }}"
           class="group flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('dashboard.about.*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/5' }}">
            <svg class="h-5 w-5 text-white/50 group-hover:text-white/70" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m6-6H6"/>
            </svg>
            About Us
        </a>
        <a href="{{ route('dashboard.media.index') }}"
           class="group flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('dashboard.media.*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/5' }}">
            <svg class="h-5 w-5 text-white/50 group-hover:text-white/70" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M4 6h8a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2z"/>
            </svg>
            Media
        </a>
        <a href="{{ route('dashboard.videos.index') }}"
           class="group flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('dashboard.videos.*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/5' }}">
            <svg class="h-5 w-5 text-white/50 group-hover:text-white/70" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7h16M4 12h16M4 17h16"/>
            </svg>
            Videos
        </a>
        <a href="{{ route('dashboard.sponsors.index') }}"
           class="group flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('dashboard.sponsors.*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/5' }}">
            <svg class="h-5 w-5 text-white/50 group-hover:text-white/70" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7h18M3 12h18M3 17h18"/>
            </svg>
            Sponsors
        </a>
        <a href="{{ route('dashboard.testimonials.index') }}"
           class="group flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('dashboard.testimonials.*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/5' }}">
            <svg class="h-5 w-5 text-white/50 group-hover:text-white/70" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 8h10M7 12h8M7 16h6M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5A2 2 0 003 5v14a2 2 0 002 2z"/>
            </svg>
            Testimonials
        </a>
    </nav>
    <div class="p-4 border-t border-white/10">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="w-full inline-flex items-center justify-center rounded-md bg-gradient-to-r from-red-600 to-black px-3 py-2 text-sm font-medium text-white hover:from-red-500 hover:to-zinc-900">
                Logout
            </button>
        </form>
    </div>
    </aside>


