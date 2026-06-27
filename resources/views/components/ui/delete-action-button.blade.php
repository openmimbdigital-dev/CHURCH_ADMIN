@props([
    'method' => null,
    'recordId' => null,
    'disabled' => false,
    'disabledReason' => 'No se puede eliminar: el registro está en uso',
    'title' => 'Eliminar',
])

<button
    type="button"
    @if (! $disabled && $method && $recordId !== null)
        wire:click="{{ $method }}({{ $recordId }})"
    @endif
    @disabled($disabled)
    title="{{ $disabled ? $disabledReason : $title }}"
    {{ $attributes->class([
        'inline-flex min-h-9 min-w-9 shrink-0 items-center justify-center rounded-lg transition',
        'bg-rose-50 text-rose-600 hover:bg-rose-100 hover:text-rose-700' => ! $disabled,
        'cursor-not-allowed bg-slate-100 text-slate-400 opacity-70' => $disabled,
    ]) }}
>
    @if ($slot->isEmpty())
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
        </svg>
    @else
        {{ $slot }}
    @endif
</button>
