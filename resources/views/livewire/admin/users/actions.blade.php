<div class="flex items-center justify-end gap-1">
    @if ($canEdit)
        <a
            href="{{ route('admin.users.edit', $id) }}"
            wire:navigate
            title="Editar usuario"
            class="inline-flex min-h-9 min-w-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 transition hover:bg-indigo-100 hover:text-indigo-700"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
        </a>
    @endif

    @if ($canDelete)
        <button
            wire:click="deleteUser({{ $id }})"
            type="button"
            title="Eliminar usuario"
            class="inline-flex min-h-9 min-w-9 items-center justify-center rounded-lg bg-rose-50 text-rose-600 transition hover:bg-rose-100 hover:text-rose-700"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </button>
    @endif

    @if (! $canEdit && ! $canDelete)
        <span class="text-xs text-slate-300" title="No disponible">—</span>
    @endif
</div>
