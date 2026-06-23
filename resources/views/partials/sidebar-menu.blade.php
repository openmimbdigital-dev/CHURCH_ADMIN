@foreach ($menuSections as $section)
    {{-- Sidebar colapsado --}}
    <div
        x-cloak
        x-show="sidebarCollapsed"
        class="relative"
        @click.outside="if (sectionCollapsedOpen === '{{ $section->code }}') sectionCollapsedOpen = null"
    >
        <button
            type="button"
            @click.stop="toggleSectionCollapsedMenu('{{ $section->code }}')"
            class="flex w-full items-center justify-center rounded-lg px-2.5 py-2.5 text-sm font-medium transition text-slate-300 hover:bg-slate-800/80 hover:text-white"
            title="{{ $section->name }}"
            :aria-expanded="sectionCollapsedOpen === '{{ $section->code }}'"
        >
            <x-menu-icon :path="$section->icon" />
        </button>
        <div
            x-show="sectionCollapsedOpen === '{{ $section->code }}'"
            x-transition
            class="absolute left-0 right-0 top-full z-[100] mt-1 flex flex-col gap-0.5 rounded-xl border border-slate-700/90 bg-slate-900 p-1 shadow-lg ring-1 ring-white/5"
        >
            @foreach ($section->activeItems as $item)
                <a
                    href="{{ $item->url ?? '#' }}"
                    @if(($item->url ?? '#') !== '#') wire:navigate @endif
                    @click="sectionCollapsedOpen = null"
                    class="flex items-center justify-center rounded-lg py-2.5 text-slate-300 hover:bg-slate-800 hover:text-white transition"
                    title="{{ $item->name }}"
                >
                    @if ($item->icon)
                        <x-menu-icon :path="$item->icon" class="h-5 w-5 text-indigo-400" />
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    {{-- Sidebar expandido --}}
    <div x-show="!sidebarCollapsed" x-cloak class="space-y-0.5">
        <button
            type="button"
            @click="toggleSectionNav('{{ $section->code }}')"
            class="w-full flex items-center gap-3 rounded-lg px-2.5 py-2.5 text-sm font-medium transition text-left text-slate-300 hover:bg-slate-800/80 hover:text-white"
        >
            <x-menu-icon :path="$section->icon" />
            <span class="truncate flex-1">{{ $section->name }}</span>
            <svg
                class="h-4 w-4 shrink-0 text-slate-400 transition-transform duration-200"
                :class="isSectionOpen('{{ $section->code }}') ? 'rotate-180' : ''"
                fill="none" stroke="currentColor" viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="isSectionOpen('{{ $section->code }}')" class="mt-0.5 space-y-0.5 border-l border-slate-700/80 ml-4 pl-3">
            @foreach ($section->activeItems as $item)
                <a
                    href="{{ $item->url ?? '#' }}"
                    @if(($item->url ?? '#') !== '#') wire:navigate @endif
                    class="flex items-center gap-2 rounded-lg px-2.5 py-2 text-sm transition
                        {{ request()->fullUrlIs(url($item->url ?? '#')) || request()->is(ltrim($item->url ?? '#', '/')) ? 'bg-slate-800/90 text-white font-medium' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}"
                    title="{{ $item->name }}"
                >
                    @if ($item->icon)
                        <x-menu-icon :path="$item->icon" class="h-4 w-4 text-indigo-400" />
                    @endif
                    <span class="truncate">{{ $item->name }}</span>
                </a>
            @endforeach
        </div>
    </div>
@endforeach
