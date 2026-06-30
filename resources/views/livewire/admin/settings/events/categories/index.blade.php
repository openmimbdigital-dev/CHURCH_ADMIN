<div class="relative mx-auto w-full min-w-0 max-w-[90rem] overflow-x-hidden">
    <nav class="mb-6 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs font-medium text-slate-500">
        <a href="{{ route('dashboard') }}" wire:navigate class="rounded px-1.5 py-0.5 hover:bg-slate-200/60">Inicio</a>
        <span class="text-slate-300">/</span>
        <a href="{{ route('admin.settings.events.index') }}" wire:navigate class="rounded px-1.5 py-0.5 hover:bg-slate-200/60">Configuración</a>
        <span class="text-slate-300">/</span>
        <a href="{{ route('admin.settings.events.index') }}" wire:navigate class="rounded px-1.5 py-0.5 hover:bg-slate-200/60">Eventos y reuniones</a>
        <span class="text-slate-300">/</span>
        <span class="font-semibold text-slate-900">{{ $config['title'] }}</span>
    </nav>

    <header class="mb-8 space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="min-w-0 flex-1 border-l-4 border-primary-500 pl-4 sm:pl-5">
                <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-primary-600/90">Eventos y reuniones</p>
                <h1 class="mt-2 text-xl font-bold tracking-tight text-slate-900 md:text-2xl">{{ $config['title'] }}</h1>
                <p class="mt-2 max-w-xl text-sm text-slate-600">{{ $config['description'] }}</p>
            </div>
            <div class="flex w-full shrink-0 flex-col gap-2 sm:w-auto sm:flex-row">
                @can('settings.event.category.create')
                    <x-ui.create-button :href="route('admin.settings.events.categories.create')" class="w-full justify-center sm:w-auto">
                        Crear categoría
                    </x-ui.create-button>
                @endcan
                <a
                href="{{ route('admin.settings.events.index') }}"
                wire:navigate
                class="inline-flex min-h-11 w-full shrink-0 items-center justify-center gap-2 rounded-xl border border-primary-200/80 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-primary-50/50 sm:w-auto"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver
            </a>
            </div>
        </div>

        <div class="grid w-full grid-cols-2 gap-3 sm:max-w-lg sm:grid-cols-3">
            <div class="card-corporate p-4">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Total</p>
                <p class="mt-2 text-2xl font-semibold tabular-nums text-slate-900 sm:text-3xl">{{ $stats['total'] }}</p>
            </div>
            <div class="card-corporate p-4">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Activas</p>
                <p class="mt-2 text-2xl font-semibold tabular-nums text-emerald-600 sm:text-3xl">{{ $stats['active'] }}</p>
            </div>
            <div class="card-corporate col-span-2 p-4 sm:col-span-1">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Generales</p>
                <p class="mt-2 text-2xl font-semibold tabular-nums text-primary-600 sm:text-3xl">{{ $stats['general'] }}</p>
            </div>
        </div>
    </header>

    <section class="panel-corporate">
        <div class="panel-corporate-header px-4 py-4 sm:px-5">
            <h2 class="font-semibold text-slate-800">Categorías registradas</h2>
        </div>
        <x-ui.datatable-scroll class="p-0">
            <livewire:admin.settings.events.categories.datatable-event-categories />
        </x-ui.datatable-scroll>
    </section>
</div>
