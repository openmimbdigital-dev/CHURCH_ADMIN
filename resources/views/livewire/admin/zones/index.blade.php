<div class="relative mx-auto w-full max-w-[90rem]">
    <nav class="mb-6 flex items-center gap-x-2 text-xs font-medium text-slate-500">
        <a href="{{ route('dashboard') }}" wire:navigate class="rounded px-1.5 py-0.5 hover:bg-slate-200/60">Inicio</a>
        <span class="text-slate-300">/</span>
        <span class="font-semibold text-slate-900">Zonas</span>
    </nav>

    <header class="mb-6">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-stretch lg:justify-between">
            <div class="min-w-0 flex-1 border-l-4 border-primary-500 pl-5">
                <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-primary-600/90">Administrar iglesia</p>
                <h1 class="mt-2 text-xl font-bold tracking-tight text-slate-900 md:text-2xl">Zonas administrativas</h1>
                <p class="mt-2 max-w-xl text-sm text-slate-600">
                    @if (auth()->user()->isSuperAdmin())
                        Gestión de zonas de todos los negocios.
                    @else
                        Zonas asociadas a tu organización.
                    @endif
                </p>
            </div>
            <div class="grid shrink-0 grid-cols-2 gap-3 sm:max-w-xs lg:self-center">
                <div class="card-corporate p-4">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Total</p>
                    <p class="mt-2 text-3xl font-semibold tabular-nums text-slate-900">{{ $stats['total'] }}</p>
                </div>
                <div class="card-corporate p-4">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Activas</p>
                    <p class="mt-2 text-3xl font-semibold tabular-nums text-emerald-600">{{ $stats['active'] }}</p>
                </div>
            </div>
        </div>
    </header>

    @can('zones.create')
        <div class="mb-4 flex justify-end">
            <a
                href="{{ route('admin.zones.create') }}"
                wire:navigate
                class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500/30"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Crear zona
            </a>
        </div>
    @endcan

    <section class="panel-corporate">
        <div class="panel-corporate-header">
            <h2 class="font-semibold text-slate-800">Zonas registradas</h2>
        </div>
        <div class="datatable-corporate overflow-x-auto p-4">
            <livewire:admin.zones.datatable-zones />
        </div>
    </section>
</div>
