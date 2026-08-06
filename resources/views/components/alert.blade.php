@php
    $type = session('success') ? 'success' : (session('error') ? 'error' : null);
    $message = session('success') ?? session('error') ?? null;
@endphp
@if($type && $message)
    <div class="mb-6 rounded-md border {{ $type === 'success' ? 'border-green-200 bg-green-50 text-green-800' : 'border-red-200 bg-red-50 text-red-800' }} px-4 py-3">
        <div class="flex items-start gap-3">
            <svg class="h-5 w-5 flex-shrink-0 {{ $type === 'success' ? 'text-green-500' : 'text-red-500' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                @if($type === 'success')
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/>
                @else
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                @endif
            </svg>
            <div class="text-sm leading-6">{{ $message }}</div>
        </div>
    </div>
@endif


