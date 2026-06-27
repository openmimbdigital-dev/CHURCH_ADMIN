<div class="relative mx-auto w-full min-w-0 max-w-[90rem] overflow-x-hidden">
    <div class="pointer-events-none absolute -top-4 left-1/2 h-px w-[min(100%,48rem)] -translate-x-1/2 bg-gradient-to-r from-transparent via-primary-300/40 to-transparent" aria-hidden="true"></div>

    <nav class="mb-6 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs font-medium text-slate-500">
        <a href="{{ route('dashboard') }}" wire:navigate class="rounded px-1.5 py-0.5 hover:bg-slate-200/60">Inicio</a>
        <span class="text-slate-300">/</span>
        <span class="font-semibold text-slate-900">Zonas</span>
    </nav>

    <header class="mb-8 space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="min-w-0 flex-1 border-l-4 border-primary-500 pl-4 sm:pl-5">
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
            @can('zones.create')
                <x-ui.create-button :href="route('admin.zones.create')" class="w-full justify-center sm:w-auto">
                    Crear zona
                </x-ui.create-button>
            @endcan
        </div>
        <div class="grid w-full grid-cols-2 gap-3 sm:max-w-xs">
            <div class="card-corporate p-4">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Total</p>
                <p class="mt-2 text-2xl font-semibold tabular-nums text-slate-900 sm:text-3xl">{{ $stats['total'] }}</p>
            </div>
            <div class="card-corporate p-4">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Activas</p>
                <p class="mt-2 text-2xl font-semibold tabular-nums text-emerald-600 sm:text-3xl">{{ $stats['active'] }}</p>
            </div>
        </div>
    </header>

    <section class="panel-corporate">
        <div class="panel-corporate-header px-4 py-4 sm:px-5">
            <h2 class="font-semibold text-slate-800">Zonas registradas</h2>
        </div>
        <div class="datatable-corporate p-3 sm:p-4">
            <livewire:admin.zones.datatable-zones />
        </div>
    </section>
</div>
