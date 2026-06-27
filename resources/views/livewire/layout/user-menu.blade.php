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
            @if ($displayEmail)
                <p class="max-w-[9rem] truncate text-[11px] leading-tight text-slate-400">{{ $displayEmail }}</p>
            @endif
        </div>
        <svg
            class="hidden h-3.5 w-3.5 shrink-0 text-slate-400 transition-transform duration-150 sm:block"
            :class="open ? 'rotate-180' : ''"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div
        x-show="open"
        x-transition
        @click.away="open = false"
        class="absolute right-0 z-50 mt-2 w-72 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl shadow-slate-900/10 ring-1 ring-slate-900/5"
        style="display:none"
    >
        <div class="bg-gradient-to-br from-indigo-600 to-indigo-800 px-4 py-4">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/20 text-sm font-bold text-white">
                    {{ $initials }}
                </span>
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold text-white">{{ $displayName }}</p>
                    @if ($displayEmail)
                        <p class="mt-0.5 truncate text-xs text-indigo-200">{{ $displayEmail }}</p>
                    @endif
                    @if ($user->username)
                        <p class="mt-0.5 text-[11px] text-indigo-300">{{ $user->username }}</p>
                    @endif
                </div>
            </div>
        </div>

        @if ($user->roleLabelForViewer())
            <div class="flex items-center gap-2 border-b border-slate-100 px-4 py-2.5">
                <span class="text-xs text-slate-500">Rol:</span>
                <span class="text-xs font-medium text-slate-700">{{ $user->roleLabelForViewer() }}</span>
            </div>
        @endif

        @if ($user->churches->isNotEmpty())
            <div class="border-b border-slate-100 px-2 py-2">
                <p class="px-2 pb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                    Iglesia activa
                </p>
                <ul class="max-h-48 space-y-0.5 overflow-y-auto">
                    @foreach ($user->churches as $church)
                        @php
                            $isActive = (int) $user->current_church_id === (int) $church->id
                                || (int) $church->pivot->current_church === (int) $church->id;
                        @endphp
                        <li>
                            <button
                                type="button"
                                wire:click="switchChurch({{ $church->id }})"
                                @click="open = false"
                                wire:loading.attr="disabled"
                                wire:target="switchChurch({{ $church->id }})"
                                @class([
                                    'flex w-full items-center gap-2 rounded-xl px-3 py-2 text-left text-sm transition',
                                    'bg-primary-50 font-medium text-primary-700' => $isActive,
                                    'text-slate-700 hover:bg-slate-50' => ! $isActive,
                                ])
                            >
                                @if ($isActive)
                                    <svg class="h-4 w-4 shrink-0 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                @else
                                    <span class="inline-block h-4 w-4 shrink-0 rounded-full border border-slate-300"></span>
                                @endif
                                <span class="truncate">{{ $church->name }}</span>
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="p-2">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    type="submit"
                    class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-medium text-red-600 transition-colors hover:bg-red-50 hover:text-red-700"
                >
                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Cerrar sesión
                </button>
            </form>
        </div>
    </div>
</div>
