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
        html, body { overflow-x: hidden; max-width: 100%; }
        body { font-family: 'Inter', sans-serif; }
    </style>
    @vite(['resources/css/app.css', 'resources/css/utils.css', 'resources/css/index.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="bg-slate-100 text-slate-900 antialiased min-h-screen overflow-x-hidden">
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
        class="flex min-h-screen w-full max-w-full overflow-x-hidden"
        x-data="{
            sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
            mobileSidebarOpen: false,
            sectionNavOpen: JSON.parse(localStorage.getItem('sidebarSectionNavOpen') || '{}'),
            sectionCollapsedOpen: null,
            toggleSidebar() {
                this.sidebarCollapsed = !this.sidebarCollapsed;
                localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
                this.sectionCollapsedOpen = null;
            },
            toggleMobileSidebar() {
                this.mobileSidebarOpen = !this.mobileSidebarOpen;
                if (this.mobileSidebarOpen) {
                    this.sectionCollapsedOpen = null;
                }
            },
            closeMobileSidebar() {
                this.mobileSidebarOpen = false;
            },
            toggleSidebarFromHeader() {
                if (window.matchMedia('(min-width: 1024px)').matches) {
                    this.toggleSidebar();
                } else {
                    this.toggleMobileSidebar();
                }
            },
            isSectionOpen(code) {
                return this.sectionNavOpen[code] !== false;
            },
            toggleSectionNav(code) {
                this.sectionNavOpen[code] = !this.isSectionOpen(code);
                localStorage.setItem('sidebarSectionNavOpen', JSON.stringify(this.sectionNavOpen));
            },
            toggleSectionCollapsedMenu(code) {
                this.sectionCollapsedOpen = this.sectionCollapsedOpen === code ? null : code;
            },
            showSidebarLabels() {
                return !this.sidebarCollapsed || this.mobileSidebarOpen;
            },
            showSidebarIconsOnly() {
                return this.sidebarCollapsed && !this.mobileSidebarOpen;
            }
        }"
        @keydown.escape.window="closeMobileSidebar(); sectionCollapsedOpen = null"
    >
        {{-- Overlay móvil / tablet --}}
        <div
            x-cloak
            x-show="mobileSidebarOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="closeMobileSidebar()"
            class="fixed inset-0 z-30 bg-slate-900/60 backdrop-blur-sm lg:hidden"
        ></div>

        <aside
            class="fixed inset-y-0 left-0 z-40 flex h-full w-60 max-w-[85vw] flex-col border-r border-slate-800 bg-slate-900 text-slate-100 transition-[transform,width] duration-200 ease-out lg:relative lg:z-40 lg:h-auto lg:min-h-screen lg:max-w-none lg:shrink-0"
            :class="[
                mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
                sidebarCollapsed ? 'lg:w-[4.25rem]' : 'lg:w-60',
            ]"
        >
            <div class="flex h-14 items-center gap-2 border-b border-slate-800/80 px-3">
                <img src="{{ asset('images/logo-initial.svg') }}"
                     alt="{{ config('app.name') }}"
                     class="h-8 w-8 shrink-0 rounded-lg bg-white/5 p-0.5 object-contain">
                <div class="min-w-0 flex-1" x-show="showSidebarLabels()" x-transition.opacity>
                    <p class="truncate text-sm font-semibold text-white">Church CFE</p>
                    <p class="truncate text-[11px] text-slate-400">Panel de control</p>
                </div>
            </div>

            <nav class="flex-1 min-h-0 space-y-1 overflow-y-auto px-2 py-4" @click="if ($event.target.closest('a[href]')) closeMobileSidebar()">
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
                    <span class="truncate" x-show="showSidebarLabels()" x-transition.opacity>Inicio</span>
                </a>

                @include('partials.sidebar-menu')
            </nav>

            <div class="hidden border-t border-slate-800/80 p-2 lg:block">
                <button
                    type="button"
                    @click="toggleSidebar()"
                    class="flex w-full items-center justify-center gap-2 rounded-lg py-2 text-sm text-slate-400 transition hover:bg-slate-800 hover:text-white"
                    :title="sidebarCollapsed ? 'Expandir menú' : 'Encoger menú'"
                >
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                    </svg>
                    <span x-show="showSidebarLabels()" class="truncate">Encoger</span>
                </button>
            </div>
        </aside>

        <div class="flex min-h-screen min-w-0 flex-1 flex-col">
            <header class="relative z-20 flex h-14 shrink-0 items-center justify-between gap-2 overflow-hidden border-b border-slate-200/80 bg-white/95 px-4 shadow-sm backdrop-blur-sm lg:px-6">
                <div class="flex min-w-0 flex-1 items-center gap-2 sm:gap-3">
                    <button
                        type="button"
                        @click="toggleSidebarFromHeader()"
                        class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 transition hover:bg-slate-50 hover:text-slate-900"
                        :title="window.matchMedia('(min-width: 1024px)').matches ? (sidebarCollapsed ? 'Expandir menú' : 'Contraer menú') : (mobileSidebarOpen ? 'Cerrar menú' : 'Abrir menú')"
                        :aria-expanded="window.matchMedia('(min-width: 1024px)').matches ? !sidebarCollapsed : mobileSidebarOpen"
                    >
                        <svg x-show="!mobileSidebarOpen" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-cloak x-show="mobileSidebarOpen" class="h-5 w-5 lg:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                    <div class="hidden h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-indigo-50 sm:flex">
                        <svg class="h-4 w-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                    </div>
                    <h1 class="truncate text-sm font-semibold text-slate-800">{{ $heading ?? $title ?? 'Inicio' }}</h1>
                </div>

                <div class="flex shrink-0 items-center gap-1 sm:gap-2">
                    <div class="hidden items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 md:flex">
                        <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-500"></span>
                        <span class="text-[11px] font-medium text-emerald-700">En línea</span>
                    </div>

                    <div class="mx-1 hidden h-6 w-px bg-slate-200 md:block"></div>

                    <div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false">
                        <button
                            type="button"
                            @click="open = !open"
                            class="flex items-center gap-1.5 rounded-xl border border-transparent py-1 pl-1 pr-1 transition-all hover:border-slate-200 hover:bg-slate-100 active:bg-slate-200 sm:gap-2.5 sm:pr-3"
                        >
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-indigo-700 text-xs font-bold text-white shadow-sm">
                                {{ $initials }}
                            </span>
                            <div class="hidden min-w-0 text-left sm:block">
                                <p class="max-w-[9rem] truncate text-xs font-semibold leading-tight text-slate-900">{{ $displayName }}</p>
                                @if($displayEmail)
                                    <p class="max-w-[9rem] truncate text-[11px] leading-tight text-slate-400">{{ $displayEmail }}</p>
                                @endif
                            </div>
                            <svg class="hidden h-3.5 w-3.5 shrink-0 text-slate-400 transition-transform duration-150 sm:block"
                                 :class="open ? 'rotate-180' : ''"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div
                            x-show="open"
                            x-transition
                            @click.away="open = false"
                            class="absolute right-0 z-50 mt-2 w-64 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl shadow-slate-900/10 ring-1 ring-slate-900/5"
                            style="display:none"
                        >
                            <div class="bg-gradient-to-br from-indigo-600 to-indigo-800 px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/20 text-sm font-bold text-white">
                                        {{ $initials }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold text-white">{{ $displayName }}</p>
                                        @if($displayEmail)
                                            <p class="mt-0.5 truncate text-xs text-indigo-200">{{ $displayEmail }}</p>
                                        @endif
                                        @if($u?->username)
                                            <p class="mt-0.5 text-[11px] text-indigo-300">{{ $u->username }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if($u?->getRoleNames()->first())
                            <div class="flex items-center gap-2 border-b border-slate-100 px-4 py-2.5">
                                <span class="text-xs text-slate-500">Rol:</span>
                                <span class="text-xs font-medium text-slate-700">{{ $u->getRoleNames()->first() }}</span>
                            </div>
                            @endif

                            <div class="p-2">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-medium text-red-600 transition-colors hover:bg-red-50 hover:text-red-700">
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

            <main class="min-w-0 flex-1 overflow-x-hidden overflow-y-auto p-4 lg:p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
