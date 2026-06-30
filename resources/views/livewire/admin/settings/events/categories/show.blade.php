<div class="relative mx-auto w-full min-w-0 max-w-[90rem] overflow-x-hidden">
    <nav class="mb-6 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs font-medium text-slate-500">
        <a href="{{ route('dashboard') }}" wire:navigate class="rounded px-1.5 py-0.5 hover:bg-slate-200/60">Inicio</a>
        <span class="text-slate-300">/</span>
        <a href="{{ route('admin.settings.events.index') }}" wire:navigate class="rounded px-1.5 py-0.5 hover:bg-slate-200/60">Configuración</a>
        <span class="text-slate-300">/</span>
        <a href="{{ route('admin.settings.events.categories') }}" wire:navigate class="rounded px-1.5 py-0.5 hover:bg-slate-200/60">Categorías</a>
        <span class="text-slate-300">/</span>
        <span class="font-semibold text-slate-900">{{ $eventCategory->name }}</span>
    </nav>

    <header class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0 flex-1 border-l-4 border-primary-500 pl-4 sm:pl-5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-primary-600/90">Categoría de evento</p>
            <h1 class="mt-2 text-xl font-bold tracking-tight text-slate-900 md:text-2xl">{{ $eventCategory->name }}</h1>
            <div class="mt-3 flex flex-wrap items-center gap-2">
                @if ($eventCategory->active)
                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 ring-1 ring-emerald-600/20">Activa</span>
                @else
                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600 ring-1 ring-slate-500/20">Inactiva</span>
                @endif
                @if ($eventCategory->general)
                    <span class="inline-flex items-center rounded-full bg-primary-50 px-2.5 py-1 text-xs font-medium text-primary-700 ring-1 ring-primary-600/20">General</span>
                @else
                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600 ring-1 ring-slate-500/20">Por iglesia</span>
                @endif
                <span class="inline-flex items-center rounded-full bg-sky-50 px-2.5 py-1 text-xs font-medium text-sky-700 ring-1 ring-sky-600/20">
                    {{ $eventCategory->type?->label() ?? '—' }}
                </span>
            </div>
        </div>
        <div class="flex w-full shrink-0 flex-col gap-2 sm:w-auto sm:flex-row">
            <a
                href="{{ route('admin.settings.events.categories') }}"
                wire:navigate
                class="inline-flex min-h-11 flex-1 items-center justify-center gap-2 rounded-xl border border-primary-200/80 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-primary-50/50 sm:flex-none"
            >
                Volver al listado
            </a>
            @can('settings.event.category.edit')
                @if ($eventCategory->isManagedByAuthUser())
                    <a
                        href="{{ route('admin.settings.events.categories.edit', $eventCategory) }}"
                        wire:navigate
                        class="inline-flex min-h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700 sm:flex-none"
                    >
                        Editar categoría
                    </a>
                @endif
            @endcan
        </div>
    </header>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <section class="section-corporate lg:col-span-2">
            <div class="section-corporate-header">
                <h2 class="font-semibold text-slate-800">Detalle</h2>
            </div>
            <dl class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Descripción</dt>
                    <dd class="mt-1 text-sm text-slate-900">{{ $eventCategory->description ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Tipo</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-900">{{ $eventCategory->type?->label() ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Ámbito</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-900">{{ $eventCategory->general ? 'General (todos)' : 'Por iglesia' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Registro</dt>
                    <dd class="mt-1 text-sm text-slate-900">{{ $eventCategory->created_at?->format('d/m/Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Última actualización</dt>
                    <dd class="mt-1 text-sm text-slate-900">{{ $eventCategory->updated_at?->format('d/m/Y H:i') }}</dd>
                </div>
            </dl>
        </section>

        <section class="section-corporate">
            <div class="section-corporate-header">
                <h2 class="font-semibold text-slate-800">Resumen</h2>
            </div>
            <div class="space-y-4 p-5">
                <div class="card-corporate p-4">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Eventos asociados</p>
                    <p class="mt-2 text-2xl font-semibold tabular-nums text-slate-900">{{ $eventCategory->events()->count() }}</p>
                </div>
                @unless ($eventCategory->general)
                    <div class="card-corporate p-4">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Iglesias asignadas</p>
                        <p class="mt-2 text-2xl font-semibold tabular-nums text-slate-900">{{ $assignments->count() }}</p>
                    </div>
                @endunless
            </div>
        </section>
    </div>

    @unless ($eventCategory->general)
        <section class="section-corporate mt-6">
            <div class="section-corporate-header">
                <h2 class="font-semibold text-slate-800">Asignación por iglesia</h2>
            </div>
            <div class="overflow-x-auto p-4">
                @if ($assignments->isEmpty())
                    <p class="px-1 py-6 text-center text-sm text-slate-400">No hay iglesias asignadas a esta categoría.</p>
                @else
                    <table class="min-w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 text-xs font-semibold uppercase tracking-wider text-slate-500">
                                @if (auth()->user()->isSuperAdmin())
                                    <th class="px-3 py-3">Negocio</th>
                                @endif
                                @if (auth()->user()->isSuperAdmin() || auth()->user()->isPresbitero())
                                    <th class="px-3 py-3">Zona</th>
                                @endif
                                <th class="px-3 py-3">Iglesia</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($assignments as $assignment)
                                <tr class="hover:bg-slate-50/50">
                                    @if (auth()->user()->isSuperAdmin())
                                        <td class="px-3 py-3 text-slate-600">{{ $assignment->business?->name ?? '—' }}</td>
                                    @endif
                                    @if (auth()->user()->isSuperAdmin() || auth()->user()->isPresbitero())
                                        <td class="px-3 py-3 text-slate-600">{{ $assignment->church?->administrativeZone?->name ?? '—' }}</td>
                                    @endif
                                    <td class="px-3 py-3 font-medium text-slate-900">{{ $assignment->church?->name ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </section>
    @endunless
</div>
