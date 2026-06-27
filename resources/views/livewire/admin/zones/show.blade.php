<div class="relative mx-auto w-full min-w-0 max-w-[90rem] overflow-x-hidden">
    <nav class="mb-6 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs font-medium text-slate-500">
        <a href="{{ route('dashboard') }}" wire:navigate class="rounded px-1.5 py-0.5 hover:bg-slate-200/60">Inicio</a>
        <span class="text-slate-300">/</span>
        <a href="{{ route('admin.zones.index') }}" wire:navigate class="rounded px-1.5 py-0.5 hover:bg-slate-200/60">Zonas</a>
        <span class="text-slate-300">/</span>
        <span class="font-semibold text-slate-900">{{ $zone->name }}</span>
    </nav>

    <header class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0 flex-1 border-l-4 border-primary-500 pl-4 sm:pl-5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-primary-600/90">Zona administrativa</p>
            <h1 class="mt-2 text-xl font-bold tracking-tight text-slate-900 md:text-2xl">{{ $zone->name }}</h1>
            <div class="mt-3 flex flex-wrap items-center gap-2">
                @if ($zone->is_active)
                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 ring-1 ring-emerald-600/20">Activa</span>
                @else
                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600 ring-1 ring-slate-500/20">Inactiva</span>
                @endif
                <span class="text-sm text-slate-500">{{ $zone->churches->count() }} {{ $zone->churches->count() === 1 ? 'iglesia' : 'iglesias' }}</span>
            </div>
        </div>
        <div class="flex w-full shrink-0 flex-col gap-2 sm:w-auto sm:flex-row">
            <a
                href="{{ route('admin.zones.index') }}"
                wire:navigate
                class="inline-flex min-h-11 flex-1 items-center justify-center gap-2 rounded-xl border border-primary-200/80 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-primary-50/50 sm:flex-none"
            >
                Volver al listado
            </a>
            @can('zones.edit')
                <a
                    href="{{ route('admin.zones.edit', $zone) }}"
                    wire:navigate
                    class="inline-flex min-h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700 sm:flex-none"
                >
                    Editar zona
                </a>
            @endcan
        </div>
    </header>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <section class="section-corporate lg:col-span-2">
            <div class="section-corporate-header">
                <h2 class="font-semibold text-slate-800">Detalle</h2>
            </div>
            <dl class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2">
                @if (auth()->user()->isSuperAdmin())
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Negocio</dt>
                        <dd class="mt-1 text-sm font-medium text-slate-900">{{ $zone->business?->name ?? '—' }}</dd>
                    </div>
                @endif
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Ciudad</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-900">
                        @if ($zone->city)
                            {{ $zone->city->name }}@if ($zone->city->department) <span class="text-slate-500">— {{ $zone->city->department->name }}</span>@endif
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Dirección</dt>
                    <dd class="mt-1 text-sm text-slate-900">{{ $zone->address ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Registro</dt>
                    <dd class="mt-1 text-sm text-slate-900">{{ $zone->created_at?->format('d/m/Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Última actualización</dt>
                    <dd class="mt-1 text-sm text-slate-900">{{ $zone->updated_at?->format('d/m/Y H:i') }}</dd>
                </div>
            </dl>
        </section>

        <section class="section-corporate">
            <div class="section-corporate-header">
                <h2 class="font-semibold text-slate-800">Líderes</h2>
            </div>
            <div class="p-5">
                @if ($zone->leaders->isEmpty())
                    <p class="text-sm text-slate-400">Sin líderes asignados.</p>
                @else
                    <ul class="space-y-3">
                        @foreach ($zone->leaders as $leader)
                            <li class="rounded-xl border border-slate-100 bg-slate-50/50 px-3 py-2.5">
                                <p class="text-sm font-medium text-slate-900">{{ $leader->full_name }}</p>
                                <p class="text-xs text-slate-500">{{ $leader->username }} · {{ $leader->email }}</p>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </section>
    </div>

    <section class="section-corporate mt-6">
        <div class="section-corporate-header">
            <h2 class="font-semibold text-slate-800">Iglesias de la zona</h2>
        </div>
        <div class="overflow-x-auto p-4">
            @if ($zone->churches->isEmpty())
                <p class="px-1 py-6 text-center text-sm text-slate-400">No hay iglesias registradas en esta zona.</p>
            @else
                <table class="min-w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs font-semibold uppercase tracking-wider text-slate-500">
                            <th class="px-3 py-3">Nombre</th>
                            <th class="hidden px-3 py-3 md:table-cell">Categoría</th>
                            <th class="px-3 py-3">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($zone->churches as $church)
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-3 py-3 font-medium text-slate-900">{{ $church->name }}</td>
                                <td class="hidden px-3 py-3 text-slate-600 md:table-cell">{{ $church->category?->label() ?? '—' }}</td>
                                <td class="px-3 py-3">
                                    @if ($church->is_active)
                                        <span class="inline-flex rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">Activa</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">Inactiva</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </section>
</div>
