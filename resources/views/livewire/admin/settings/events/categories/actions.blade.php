<x-ui.datatable-actions>
    @if ($canView ?? false)
        <a
            href="{{ route('admin.settings.events.categories.show', $id) }}"
            wire:navigate
            title="Ver categoría"
            class="inline-flex min-h-9 min-w-9 shrink-0 items-center justify-center rounded-lg bg-sky-50 text-sky-600 transition hover:bg-sky-100 hover:text-sky-700"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
        </a>
    @endif

    @if ($canEdit ?? false)
        <a
            href="{{ route('admin.settings.events.categories.edit', $id) }}"
            wire:navigate
            title="Editar categoría"
            class="inline-flex min-h-9 min-w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 transition hover:bg-indigo-100 hover:text-indigo-700"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
        </a>
    @endif

    @if ($canDelete ?? false)
        <x-ui.delete-action-button
            method="deleteEventCategory"
            :record-id="$id"
            :disabled="! ($isDeletable ?? true)"
            :disabled-reason="$deleteBlockReason ?? 'No se puede eliminar: el registro está en uso'"
            title="Eliminar categoría"
            class="shrink-0"
        />
    @elseif ($showDeleteDisabled ?? false)
        <x-ui.delete-action-button
            method="deleteEventCategory"
            :record-id="$id"
            disabled
            :disabled-reason="$deleteDisabledReason ?? 'No puedes eliminar esta categoría'"
            title="Eliminar categoría"
            class="shrink-0"
        />
    @endif

    @if (! ($canView ?? false) && ! ($canEdit ?? false) && ! ($canDelete ?? false) && ! ($showDeleteDisabled ?? false))
        <span class="text-xs text-slate-300" title="No disponible">—</span>
    @endif
</x-ui.datatable-actions>
