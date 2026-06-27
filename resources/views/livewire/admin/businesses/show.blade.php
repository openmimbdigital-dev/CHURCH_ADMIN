<div class="relative mx-auto w-full min-w-0 max-w-[90rem] overflow-x-hidden">
    <nav class="mb-6 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs font-medium text-slate-500">
        <a href="{{ route('dashboard') }}" wire:navigate class="rounded px-1.5 py-0.5 hover:bg-slate-200/60">Inicio</a>
        <span class="text-slate-300">/</span>
        <a href="{{ route('admin.businesses.index') }}" wire:navigate class="rounded px-1.5 py-0.5 hover:bg-slate-200/60">Negocios</a>
        <span class="text-slate-300">/</span>
        <span class="font-semibold text-slate-900">{{ $business->name }}</span>
    </nav>

    <header class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0 flex-1 border-l-4 border-primary-500 pl-4 sm:pl-5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-primary-600/90">Negocio</p>
            <h1 class="mt-2 text-xl font-bold tracking-tight text-slate-900 md:text-2xl">{{ $business->name }}</h1>
            <div class="mt-3 flex flex-wrap items-center gap-2">
                @if ($business->is_active)
                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 ring-1 ring-emerald-600/20">Activo</span>
                @else
                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600 ring-1 ring-slate-500/20">Inactivo</span>
                @endif
                <span class="text-sm text-slate-500">{{ $business->users_count }} {{ $business->users_count === 1 ? 'usuario' : 'usuarios' }}</span>
                <span class="text-sm text-slate-500">{{ $business->administrative_zones_count }} {{ $business->administrative_zones_count === 1 ? 'zona' : 'zonas' }}</span>
                <span class="text-sm text-slate-500">{{ $business->churches_count }} {{ $business->churches_count === 1 ? 'iglesia' : 'iglesias' }}</span>
            </div>
        </div>
        <div class="flex w-full shrink-0 flex-col gap-2 sm:w-auto sm:flex-row">
            <a
                href="{{ route('admin.businesses.index') }}"
                wire:navigate
                class="inline-flex min-h-11 flex-1 items-center justify-center gap-2 rounded-xl border border-primary-200/80 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-primary-50/50 sm:flex-none"
            >
                Volver al listado
            </a>
            @can('businesses.edit')
                <a
                    href="{{ route('admin.businesses.edit', $business) }}"
                    wire:navigate
                    class="inline-flex min-h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700 sm:flex-none"
                >
                    Editar negocio
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
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">NIT</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-900">{{ $business->nit ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Correo</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-900">{{ $business->email ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Teléfono</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-900">{{ $business->phone_number ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Ciudad</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-900">
                        @if ($business->city)
                            {{ $business->city->name }}@if ($business->city->department) <span class="text-slate-500">— {{ $business->city->department->name }}</span>@endif
                        @else
                            —
                        @endif
                    </dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Dirección</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-900">{{ $business->address ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Slug</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-900">{{ $business->slug }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Registro</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-900">{{ $business->created_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                </div>
            </dl>
        </section>
    </div>
</div>
