@php
    $user = filament()->auth()->user();
@endphp

@if($user)
    @php
        $profilePhotoUrl = $user->profile_photo_url ?? $user->avatar ?? null;
        if ($profilePhotoUrl && !filter_var($profilePhotoUrl, FILTER_VALIDATE_URL)) {
            $profilePhotoUrl = asset($profilePhotoUrl);
        }
        $initials = \App\Support\UserInitials::generate($user->name ?? null);
    @endphp

    <div class="flex items-center rtl:mr-2 ltr:ml-2" style="display: flex !important;">
        @if($profilePhotoUrl)
            <img 
                src="{{ $profilePhotoUrl }}"
                alt="{{ $user->name }}"
                class="w-8 h-8 rounded-full object-cover"
                style="width: 32px; height: 32px; border-radius: 50%;"
            />
        @else
            <div class="w-8 h-8 rounded-full bg-neutral-900 dark:bg-neutral-900 flex items-center justify-center text-white text-xs font-semibold" style="width: 32px; height: 32px; border-radius: 50%; background-color: #000; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600;">
                {{ $initials }}
            </div>
        @endif
    </div>
@endif
