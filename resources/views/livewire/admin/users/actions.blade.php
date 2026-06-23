<div class="flex items-center justify-end">
    @if ($canDelete)
        <button
            wire:click="deleteUser({{ $id }})"
            type="button"
            title="Eliminar usuario"
            class="inline-flex items-center gap-1 rounded-lg bg-rose-50 px-2.5 py-1.5 text-xs font-medium text-rose-600 transition hover:bg-rose-100 hover:text-rose-700"
        >
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Eliminar
        </button>
    @else
        <span class="text-xs text-slate-300" title="No disponible">—</span>
    @endif
</div>
