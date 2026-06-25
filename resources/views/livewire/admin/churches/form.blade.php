@php
    $isEdit = (bool) $church;
    $labelClass = 'block text-sm font-medium text-slate-700 mb-1.5';
    $errorClass = 'mt-1.5 text-xs text-red-600';
@endphp

<div class="relative mx-auto w-full max-w-[90rem]">
    <nav class="mb-6 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs font-medium text-slate-500">
        <a href="{{ route('dashboard') }}" wire:navigate class="rounded px-1.5 py-0.5 hover:bg-slate-200/60">Inicio</a>
        <span class="text-slate-300">/</span>
        <a href="{{ route('admin.churches.index') }}" wire:navigate class="rounded px-1.5 py-0.5 hover:bg-slate-200/60">Iglesias</a>
        <span class="text-slate-300">/</span>
        <span class="font-semibold text-slate-900">{{ $isEdit ? 'Editar' : 'Crear' }}</span>
    </nav>

    <header class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0 flex-1 border-l-4 border-primary-500 pl-5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-primary-600/90">Iglesias</p>
            <h1 class="mt-2 text-xl font-bold tracking-tight text-slate-900 md:text-2xl">
                {{ $isEdit ? 'Editar iglesia' : 'Crear iglesia' }}
            </h1>
            <p class="mt-2 max-w-xl text-sm text-slate-600">
                {{ $isEdit ? 'Modifica la información de la iglesia.' : 'Completa los datos para registrar una nueva iglesia.' }}
            </p>
        </div>
        <a
            href="{{ route('admin.churches.index') }}"
            wire:navigate
            class="inline-flex min-h-11 shrink-0 items-center justify-center gap-2 rounded-xl border border-primary-200/80 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-primary-50/50"
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
                <h2 class="font-semibold text-slate-800">Organización</h2>
            </div>
            <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label for="category" class="{{ $labelClass }}">Categoría <span class="text-rose-500">*</span></label>
                    <select id="category" wire:model.live="churchForm.category" class="input-corporate">
                        <option value="">Selecciona una categoría</option>
                        @foreach ($categories as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('churchForm.category') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                @if (auth()->user()->isSuperAdmin())
                    <div class="md:col-span-2">
                        <label for="business_id" class="{{ $labelClass }}">Negocio <span class="text-rose-500">*</span></label>
                        <select id="business_id" wire:model.live="churchForm.business_id" class="input-corporate">
                            <option value="">Selecciona un negocio</option>
                            @foreach ($businesses as $business)
                                <option value="{{ $business->id }}">{{ $business->name }}</option>
                            @endforeach
                        </select>
                        @error('churchForm.business_id') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                    </div>
                @endif

                <div>
                    <label for="administrative_zone_id" class="{{ $labelClass }}">Zona administrativa <span class="text-rose-500">*</span></label>
                    <select
                        id="administrative_zone_id"
                        wire:model.live="churchForm.administrative_zone_id"
                        class="input-corporate"
                        @disabled(! $churchForm->business_id && auth()->user()->isSuperAdmin())
                    >
                        <option value="">
                            @if (auth()->user()->isSuperAdmin() && ! $churchForm->business_id)
                                Primero selecciona un negocio
                            @else
                                Selecciona una zona
                            @endif
                        </option>
                        @foreach ($zones as $zone)
                            <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                        @endforeach
                    </select>
                    @error('churchForm.administrative_zone_id') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="parent_id" class="{{ $labelClass }}">
                        Iglesia madre
                        @if ($this->motherChurchSelectEnabled())
                            <span class="text-rose-500">*</span>
                        @endif
                    </label>
                    <select
                        id="parent_id"
                        wire:model="churchForm.parent_id"
                        class="input-corporate"
                        @disabled(! $this->motherChurchSelectEnabled() || ! $churchForm->administrative_zone_id)
                    >
                        <option value="">
                            @if (! $this->motherChurchSelectEnabled())
                                Solo aplica para categoría Hija o Campo blanco
                            @elseif (! $churchForm->administrative_zone_id)
                                Primero selecciona una zona
                            @else
                                Selecciona una iglesia madre
                            @endif
                        </option>
                        @foreach ($parentChurches as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                        @endforeach
                    </select>
                    @error('churchForm.parent_id') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <section class="section-corporate">
            <div class="section-corporate-header">
                <h2 class="font-semibold text-slate-800">Información general</h2>
            </div>
            <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label for="name" class="{{ $labelClass }}">Nombre <span class="text-rose-500">*</span></label>
                    <input id="name" type="text" wire:model="churchForm.name" class="input-corporate" placeholder="Ej: Iglesia Central">
                    @error('churchForm.name') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="city_id" class="{{ $labelClass }}">Ciudad</label>
                    <select id="city_id" wire:model="churchForm.city_id" class="input-corporate">
                        <option value="">Selecciona una ciudad</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city->id }}">
                                {{ $city->name }}@if ($city->department) — {{ $city->department->name }}@endif
                            </option>
                        @endforeach
                    </select>
                    @error('churchForm.city_id') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="address" class="{{ $labelClass }}">Dirección</label>
                    <input id="address" type="text" wire:model="churchForm.address" class="input-corporate" placeholder="Dirección de la iglesia">
                    @error('churchForm.address') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <section class="section-corporate">
            <div class="section-corporate-header">
                <h2 class="font-semibold text-slate-800">Estado</h2>
            </div>
            <div class="p-5">
                <x-ui.switch
                    id="church-status"
                    wire:model="churchForm.is_active"
                    label="Iglesia activa"
                    description="Si está desactivada, no estará disponible para nuevas asignaciones."
                />
                @error('churchForm.is_active') <p class="{{ $errorClass }} mt-2">{{ $message }}</p> @enderror
            </div>
        </section>

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a
                href="{{ route('admin.churches.index') }}"
                wire:navigate
                class="inline-flex min-h-11 items-center justify-center rounded-xl border border-primary-200/80 bg-white px-5 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-primary-50/50"
            >
                Cancelar
            </a>
            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="save"
                class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500/30 disabled:opacity-60"
            >
                <span wire:loading.remove wire:target="save">{{ $isEdit ? 'Guardar cambios' : 'Crear iglesia' }}</span>
                <span wire:loading wire:target="save">Guardando...</span>
            </button>
        </div>
    </form>
</div>
