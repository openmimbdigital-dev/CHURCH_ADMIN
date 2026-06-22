<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name') }} — {{ config('app.name') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo-initial.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
    </style>
    @vite(['resources/css/app.css', 'resources/css/utils.css', 'resources/css/index.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="bg-slate-100 text-slate-900 antialiased min-h-screen">
    @php
        $u = auth()->user();
        $displayName = ($u?->full_name ?: null) ?? $u?->username ?? $u?->email ?? 'Usuario';
        $displayEmail = $u?->email ?? '';
        $initials = strtoupper(mb_substr(preg_replace('/\s+/', '', $displayName), 0, 2));
        if (mb_strlen($displayName) >= 2 && preg_match('/\s/u', $displayName)) {
            $parts = preg_split('/\s+/u', trim($displayName));
            $initials = mb_strtoupper(mb_substr($parts[0] ?? '', 0, 1) . mb_substr($parts[count($parts) - 1] ?? '', 0, 1));
        }
    @endphp

    <div
        class="min-h-screen flex"
        x-data="{
            sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
            toggleSidebar() {
                this.sidebarCollapsed = !this.sidebarCollapsed;
                localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
            }
        }"
    >
        <aside
            class="relative z-40 flex flex-col bg-slate-900 text-slate-100 shrink-0 border-r border-slate-800 transition-[width] duration-200 ease-out"
            :class="sidebarCollapsed ? 'w-[4.25rem]' : 'w-60'"
        >
            <div class="h-14 flex items-center px-3 border-b border-slate-800/80 gap-2">
                <img src="{{ asset('images/logo-initial.svg') }}"
                     alt="{{ config('app.name') }}"
                     class="h-8 w-8 shrink-0 rounded-lg object-contain bg-white/5 p-0.5">
                <div class="min-w-0 flex-1" x-show="!sidebarCollapsed" x-transition.opacity>
                    <p class="font-semibold text-white truncate text-sm">Church CFE</p>
                    <p class="text-[11px] text-slate-400 truncate">Panel de control</p>
                </div>
            </div>

            <nav class="flex-1 min-h-0 space-y-1 overflow-y-auto py-4 px-2">
                <a
                    href="{{ route('dashboard') }}"
                    wire:navigate
                    class="flex items-center gap-3 rounded-lg px-2.5 py-2.5 text-sm font-medium transition
                        {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}"
                    title="Inicio"
                >
                    <svg class="h-5 w-5 shrink-0 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    <span class="truncate" x-show="!sidebarCollapsed" x-transition.opacity>Inicio</span>
                </a>
            </nav>

            <div class="p-2 border-t border-slate-800/80">
                <button
                    type="button"
                    @click="toggleSidebar()"
                    class="w-full flex items-center justify-center gap-2 rounded-lg py-2 text-slate-400 hover:bg-slate-800 hover:text-white transition text-sm"
                    :title="sidebarCollapsed ? 'Expandir menú' : 'Encoger menú'"
                >
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" class="truncate">Encoger</span>
                </button>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0 min-h-screen">
            <header class="relative z-20 h-14 bg-white/95 backdrop-blur-sm border-b border-slate-200/80 flex items-center justify-between px-4 lg:px-6 shrink-0 shadow-sm">
                <div class="min-w-0 flex items-center gap-3">
                    <div class="hidden sm:flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-indigo-50">
                        <svg class="h-4 w-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                    </div>
                    <h1 class="text-sm font-semibold text-slate-800 truncate">{{ $heading ?? $title ?? 'Inicio' }}</h1>
                </div>

                <div class="flex items-center gap-2">
                    <div class="hidden md:flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-[11px] font-medium text-emerald-700">En línea</span>
                    </div>

                    <div class="hidden md:block h-6 w-px bg-slate-200 mx-1"></div>

                    <div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false">
                        <button
                            type="button"
                            @click="open = !open"
                            class="flex items-center gap-2.5 rounded-xl pl-1 pr-3 py-1 hover:bg-slate-100 active:bg-slate-200 transition-all border border-transparent hover:border-slate-200"
                        >
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-indigo-700 text-white text-xs font-bold shadow-sm">
                                {{ $initials }}
                            </span>
                            <div class="hidden sm:block text-left min-w-0">
                                <p class="text-xs font-semibold text-slate-900 truncate max-w-[9rem] leading-tight">{{ $displayName }}</p>
                                @if($displayEmail)
                                    <p class="text-[11px] text-slate-400 truncate max-w-[9rem] leading-tight">{{ $displayEmail }}</p>
                                @endif
                            </div>
                            <svg class="h-3.5 w-3.5 text-slate-400 hidden sm:block shrink-0 transition-transform duration-150"
                                 :class="open ? 'rotate-180' : ''"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div
                            x-show="open"
                            x-transition
                            @click.away="open = false"
                            class="absolute right-0 mt-2 w-64 rounded-2xl border border-slate-200 bg-white shadow-xl shadow-slate-900/10 ring-1 ring-slate-900/5 z-50 overflow-hidden"
                            style="display:none"
                        >
                            <div class="px-4 py-4 bg-gradient-to-br from-indigo-600 to-indigo-800">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/20 text-white font-bold text-sm">
                                        {{ $initials }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-white truncate">{{ $displayName }}</p>
                                        @if($displayEmail)
                                            <p class="text-xs text-indigo-200 truncate mt-0.5">{{ $displayEmail }}</p>
                                        @endif
                                        @if($u?->username)
                                            <p class="text-[11px] text-indigo-300 mt-0.5">{{ $u->username }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if($u?->getRoleNames()->first())
                            <div class="px-4 py-2.5 border-b border-slate-100 flex items-center gap-2">
                                <span class="text-xs text-slate-500">Rol:</span>
                                <span class="text-xs font-medium text-slate-700">{{ $u->getRoleNames()->first() }}</span>
                            </div>
                            @endif

                            <div class="p-2">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-full flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors">
                                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        Cerrar sesión
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-4 lg:p-6 overflow-auto">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
