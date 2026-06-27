@props([
    'href' => null,
    'size' => 'md',
])

@php
    $base = 'inline-flex shrink-0 items-center gap-2 rounded-xl bg-primary-600 font-semibold text-white shadow-sm shadow-primary-500/20 transition hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500/30 disabled:cursor-not-allowed disabled:opacity-50 min-h-11';

    $sizes = [
        'md' => 'px-5 py-2.5 text-sm',
        'sm' => 'px-3.5 py-2 text-sm min-h-9',
    ];

    $classes = trim($base . ' ' . ($sizes[$size] ?? $sizes['md']));
@endphp

@if ($href)
    <a href="{{ $href }}" wire:navigate {{ $attributes->merge(['class' => $classes]) }}>
        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        {{ $slot }}
    </a>
@else
    <button type="button" {{ $attributes->merge(['class' => $classes]) }}>
        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        {{ $slot }}
    </button>
@endif
