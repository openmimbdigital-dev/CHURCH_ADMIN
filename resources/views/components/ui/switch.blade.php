@props([
    'id' => null,
    'label' => null,
    'description' => null,
])

@php
    $switchId = $id ?? 'switch-' . uniqid();
@endphp

<div {{ $attributes->only('class')->merge(['class' => 'flex flex-col gap-1']) }}>
    <label for="{{ $switchId }}" class="inline-flex cursor-pointer items-center gap-3">
        <span class="relative inline-flex shrink-0">
            <input
                type="checkbox"
                id="{{ $switchId }}"
                {{ $attributes->except('class')->class('peer sr-only') }}
            >
            <span
                aria-hidden="true"
                class="block h-6 w-11 rounded-full bg-slate-200 transition-colors duration-200 peer-checked:bg-primary-400 peer-focus-visible:ring-2 peer-focus-visible:ring-primary-200/80 peer-focus-visible:ring-offset-2"
            ></span>
            <span
                aria-hidden="true"
                class="pointer-events-none absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white shadow-sm transition-transform duration-200 peer-checked:translate-x-5"
            ></span>
        </span>

        @if ($label)
            <span class="text-sm font-medium text-slate-700">{{ $label }}</span>
        @endif
    </label>

    @if ($description)
        <p class="pl-14 text-xs text-slate-500">{{ $description }}</p>
    @endif
</div>
