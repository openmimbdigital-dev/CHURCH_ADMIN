<x-ui.datatable-actions>
    @if ($canEdit)
        <a
            href="{{ route('admin.users.edit', $id) }}"
            wire:navigate
            title="Editar usuario"
            class="inline-flex min-h-9 min-w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 transition hover:bg-indigo-100 hover:text-indigo-700"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
        </a>
    @endif

    @if ($canDelete)
        <x-ui.delete-action-button
            method="deleteUser"
            :record-id="$id"
            :disabled="! ($isDeletable ?? true)"
            :disabled-reason="$deleteBlockReason ?? 'No se puede eliminar: el registro está en uso'"
            title="Eliminar usuario"
            class="shrink-0"
        />
    @endif

    @if (! $canEdit && ! $canDelete)
        <span class="text-xs text-slate-300" title="No disponible">—</span>
    @endif
</x-ui.datatable-actions>
