<x-ui.datatable-actions>
    @if ($canView)
        <a
            href="{{ route('admin.zones.show', $id) }}"
            wire:navigate
            title="Ver zona"
            class="inline-flex min-h-9 min-w-9 shrink-0 items-center justify-center rounded-lg bg-sky-50 text-sky-600 transition hover:bg-sky-100 hover:text-sky-700"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
        </a>
    @endif

    @if ($canEdit)
        <a
            href="{{ route('admin.zones.edit', $id) }}"
            wire:navigate
            title="Editar zona"
            class="inline-flex min-h-9 min-w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 transition hover:bg-indigo-100 hover:text-indigo-700"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
        </a>
    @endif

    @if ($canDelete)
        <x-ui.delete-action-button
            method="deleteZone"
            :record-id="$id"
            :disabled="! ($isDeletable ?? true)"
            :disabled-reason="$deleteBlockReason ?? 'No se puede eliminar: el registro está en uso'"
            title="Eliminar zona"
            class="shrink-0"
        />
    @endif

    @if (! $canView && ! $canEdit && ! $canDelete)
        <span class="text-xs text-slate-300" title="No disponible">—</span>
    @endif
</x-ui.datatable-actions>
