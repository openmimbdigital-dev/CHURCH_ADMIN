<div class="min-w-0 max-w-full space-y-4 overflow-x-hidden sm:space-y-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 px-5 py-8 shadow-xl sm:px-8 sm:py-10">
        <div class="absolute inset-0 opacity-30" style="background-image:radial-gradient(rgba(255,255,255,.15) 1px,transparent 1px);background-size:24px 24px;"></div>
        <div class="absolute -top-20 -right-20 h-64 w-64 rounded-full bg-indigo-600/20 blur-3xl"></div>
        <div class="absolute -bottom-16 -left-10 h-48 w-48 rounded-full bg-primary-700/20 blur-3xl"></div>

        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center gap-6">
            <img src="{{ asset('images/logo-initial.svg') }}" alt="{{ config('app.name') }}" class="h-16 w-16 drop-shadow-lg shrink-0">
            <div>
                <p class="text-indigo-300 text-xs font-semibold uppercase tracking-widest mb-1">Panel de control</p>
                <h1 class="text-2xl sm:text-3xl font-bold text-white leading-tight">
                    Bienvenido, <span class="text-indigo-300">{{ auth()->user()?->full_name ?: auth()->user()?->username }}</span>
                </h1>
                <p class="mt-1.5 text-slate-400 text-sm">
                    Hoy es {{ \Carbon\Carbon::now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}.
                </p>
            </div>
        </div>
    </div>

    <div class="flex flex-col gap-2 rounded-xl border border-slate-200 bg-white px-4 py-4 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:px-5">
        <div class="flex items-center gap-3">
            <div class="h-2 w-2 animate-pulse rounded-full bg-emerald-400"></div>
            <span class="text-sm text-slate-600">Sistema operativo y en línea</span>
        </div>
        <span class="text-xs text-slate-400">Church CFE · Laravel 12 + Livewire</span>
    </div>
</div>
