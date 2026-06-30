@php
    $isEdit = (bool) $eventCategory;
    $labelClass = 'block text-sm font-medium text-slate-700 mb-1.5';
    $errorClass = 'mt-1.5 text-xs text-red-600';
    $checkboxClass = 'h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500/30';
@endphp

<div class="relative mx-auto w-full min-w-0 max-w-[90rem] overflow-x-hidden">
    <nav class="mb-6 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs font-medium text-slate-500">
        <a href="{{ route('dashboard') }}" wire:navigate class="rounded px-1.5 py-0.5 hover:bg-slate-200/60">Inicio</a>
        <span class="text-slate-300">/</span>
        <a href="{{ route('admin.settings.events.index') }}" wire:navigate class="rounded px-1.5 py-0.5 hover:bg-slate-200/60">Configuración</a>
        <span class="text-slate-300">/</span>
        <a href="{{ route('admin.settings.events.categories') }}" wire:navigate class="rounded px-1.5 py-0.5 hover:bg-slate-200/60">Categorías</a>
        <span class="text-slate-300">/</span>
        <span class="font-semibold text-slate-900">{{ $isEdit ? 'Editar' : 'Crear' }}</span>
    </nav>

    <header class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0 flex-1 border-l-4 border-primary-500 pl-4 sm:pl-5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-primary-600/90">Categorías de eventos</p>
            <h1 class="mt-2 text-xl font-bold tracking-tight text-slate-900 md:text-2xl">
                {{ $isEdit ? 'Editar categoría' : 'Crear categoría' }}
            </h1>
            <p class="mt-2 max-w-xl text-sm text-slate-600">
                {{ $isEdit ? 'Modifica la información y el alcance de la categoría.' : 'Define el nombre, tipo y alcance de la nueva categoría.' }}
            </p>
        </div>
        <a
            href="{{ route('admin.settings.events.categories') }}"
            wire:navigate
            class="inline-flex min-h-11 w-full shrink-0 items-center justify-center gap-2 rounded-xl border border-primary-200/80 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-primary-50/50 sm:w-auto"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
    </header>

    <form wire:submit="save" class="space-y-6">
        <section class="section-corporate">
            <div class="section-corporate-header">
                <h2 class="font-semibold text-slate-800">Información general</h2>
            </div>
            <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label for="name" class="{{ $labelClass }}">Nombre <span class="text-rose-500">*</span></label>
                    <input id="name" type="text" wire:model="categoryForm.name" class="input-corporate" placeholder="Ej: Reunión de jóvenes">
                    @error('categoryForm.name') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="{{ $labelClass }}">Descripción</label>
                    <textarea id="description" wire:model="categoryForm.description" rows="3" class="input-corporate min-h-[6rem] resize-y" placeholder="Descripción opcional de la categoría"></textarea>
                    @error('categoryForm.description') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="type" class="{{ $labelClass }}">Tipo <span class="text-rose-500">*</span></label>
                    <select id="type" wire:model="categoryForm.type" class="input-corporate">
                        @foreach ($typeOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('categoryForm.type') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-end pb-1">
                    <x-ui.switch
                        id="category-active"
                        wire:model="categoryForm.active"
                        label="Categoría activa"
                        description="Si está inactiva, no estará disponible para nuevos eventos."
                    />
                </div>
                @error('categoryForm.active') <p class="{{ $errorClass }} md:col-span-2">{{ $message }}</p> @enderror
            </div>
        </section>

        <section class="section-corporate">
            <div class="section-corporate-header">
                <h2 class="font-semibold text-slate-800">Alcance</h2>
            </div>
            <div class="space-y-6 p-5">
                @if ($isSuperAdmin)
                    <div class="rounded-xl border border-primary-100 bg-primary-50/40 p-4">
                        <label class="inline-flex cursor-pointer items-start gap-3">
                            <input
                                type="checkbox"
                                wire:model.live="categoryForm.general"
                                class="{{ $checkboxClass }} mt-0.5"
                            >
                            <span>
                                <span class="block text-sm font-medium text-slate-800">Categoría general</span>
                                <span class="mt-1 block text-xs text-slate-600">Disponible para todos los negocios e iglesias. No requiere asignación por iglesia.</span>
                            </span>
                        </label>
                        @error('categoryForm.general') <p class="{{ $errorClass }} mt-2">{{ $message }}</p> @enderror
                    </div>
                @endif

                @if ($isSuperAdmin && ! $categoryForm->general)
                    <div class="space-y-5">
                        <div>
                            <p class="{{ $labelClass }}">Negocios <span class="text-rose-500">*</span></p>
                            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach ($businessHierarchy as $business)
                                    <label class="flex cursor-pointer items-center gap-2.5 rounded-xl border border-slate-200 bg-white px-3 py-2.5 transition hover:border-primary-200 hover:bg-primary-50/30">
                                        <input
                                            type="checkbox"
                                            wire:model.live="categoryForm.selected_business_ids"
                                            value="{{ $business->id }}"
                                            class="{{ $checkboxClass }}"
                                        >
                                        <span class="text-sm text-slate-800">{{ $business->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        @if ($categoryForm->selected_business_ids !== [])
                            <div>
                                <p class="{{ $labelClass }}">Zonas administrativas</p>
                                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
                                    @foreach ($businessHierarchy->whereIn('id', $categoryForm->selected_business_ids) as $business)
                                        @foreach ($business->administrativeZones as $zone)
                                            <label class="flex cursor-pointer items-center gap-2.5 rounded-xl border border-slate-200 bg-white px-3 py-2.5 transition hover:border-primary-200 hover:bg-primary-50/30">
                                                <input
                                                    type="checkbox"
                                                    wire:model.live="categoryForm.selected_zone_ids"
                                                    value="{{ $zone->id }}"
                                                    class="{{ $checkboxClass }}"
                                                >
                                                <span class="min-w-0">
                                                    <span class="block text-sm text-slate-800">{{ $zone->name }}</span>
                                                    <span class="block text-xs text-slate-500">{{ $business->name }}</span>
                                                </span>
                                            </label>
                                        @endforeach
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if ($categoryForm->selected_zone_ids !== [])
                            <div>
                                <p class="{{ $labelClass }}">Iglesias <span class="text-rose-500">*</span></p>
                                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
                                    @foreach ($businessHierarchy as $business)
                                        @foreach ($business->administrativeZones->whereIn('id', $categoryForm->selected_zone_ids) as $zone)
                                            @foreach ($zone->churches as $church)
                                                <label class="flex cursor-pointer items-center gap-2.5 rounded-xl border border-slate-200 bg-white px-3 py-2.5 transition hover:border-primary-200 hover:bg-primary-50/30">
                                                    <input
                                                        type="checkbox"
                                                        wire:model="categoryForm.selected_church_ids"
                                                        value="{{ $church->id }}"
                                                        class="{{ $checkboxClass }}"
                                                    >
                                                    <span class="min-w-0">
                                                        <span class="block text-sm text-slate-800">{{ $church->name }}</span>
                                                        <span class="block text-xs text-slate-500">{{ $zone->name }}</span>
                                                    </span>
                                                </label>
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                </div>
                                @error('categoryForm.selected_church_ids') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                            </div>
                        @endif
                    </div>
                @elseif (! $isSuperAdmin)
                    @if ($isPresbitero && $actorZones->isNotEmpty())
                        <div>
                            <p class="{{ $labelClass }}">Zona administrativa</p>
                            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                @foreach ($actorZones as $zone)
                                    <label class="flex items-center gap-2.5 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5">
                                        @if ($viewer->can('churches.viewAll'))
                                            <input
                                                type="checkbox"
                                                wire:model.live="categoryForm.selected_zone_ids"
                                                value="{{ $zone->id }}"
                                                class="{{ $checkboxClass }}"
                                            >
                                        @else
                                            <input
                                                type="checkbox"
                                                checked
                                                disabled
                                                class="{{ $checkboxClass }}"
                                            >
                                        @endif
                                        <span class="text-sm text-slate-800">{{ $zone->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div>
                        <p class="{{ $labelClass }}">Iglesia{{ $isPresbitero && $viewer->can('churches.viewAll') ? 's' : '' }} <span class="text-rose-500">*</span></p>
                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                            @if ($isPresbitero && $viewer->can('churches.viewAll'))
                                @foreach ($actorChurches as $church)
                                    <label class="flex cursor-pointer items-center gap-2.5 rounded-xl border border-slate-200 bg-white px-3 py-2.5 transition hover:border-primary-200 hover:bg-primary-50/30">
                                        <input
                                            type="checkbox"
                                            wire:model="categoryForm.selected_church_ids"
                                            value="{{ $church->id }}"
                                            class="{{ $checkboxClass }}"
                                        >
                                        <span class="text-sm text-slate-800">{{ $church->name }}</span>
                                    </label>
                                @endforeach
                            @else
                                @php $church = $viewer->currentChurch; @endphp
                                @if ($church)
                                    <div class="flex items-center gap-2.5 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5">
                                        <input type="checkbox" checked disabled class="{{ $checkboxClass }}">
                                        <span class="text-sm text-slate-800">{{ $church->name }}</span>
                                    </div>
                                @endif
                            @endif
                        </div>
                        @error('categoryForm.selected_church_ids') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                    </div>
                @elseif ($isSuperAdmin && $categoryForm->general)
                    <p class="text-sm text-slate-600">Esta categoría será visible para todos los negocios e iglesias del sistema.</p>
                @endif
            </div>
        </section>

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a
                href="{{ route('admin.settings.events.categories') }}"
                wire:navigate
                class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-primary-200/80 bg-white px-5 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-primary-50/50 sm:w-auto"
            >
                Cancelar
            </a>
            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="save"
                class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500/30 disabled:opacity-60 sm:w-auto"
            >
                <span wire:loading.remove wire:target="save">{{ $isEdit ? 'Guardar cambios' : 'Crear categoría' }}</span>
                <span wire:loading wire:target="save">Guardando...</span>
            </button>
        </div>
    </form>
</div>
