@php
    $isEdit = (bool) $business;
    $labelClass = 'block text-sm font-medium text-slate-700 mb-1.5';
    $errorClass = 'mt-1.5 text-xs text-red-600';
@endphp

<div class="relative mx-auto w-full min-w-0 max-w-[90rem] overflow-x-hidden">
    <nav class="mb-6 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs font-medium text-slate-500">
        <a href="{{ route('dashboard') }}" wire:navigate class="rounded px-1.5 py-0.5 hover:bg-slate-200/60">Inicio</a>
        <span class="text-slate-300">/</span>
        <a href="{{ route('admin.businesses.index') }}" wire:navigate class="rounded px-1.5 py-0.5 hover:bg-slate-200/60">Negocios</a>
        <span class="text-slate-300">/</span>
        <span class="font-semibold text-slate-900">{{ $isEdit ? 'Editar' : 'Crear' }}</span>
    </nav>

    <header class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0 flex-1 border-l-4 border-primary-500 pl-4 sm:pl-5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-primary-600/90">Negocios</p>
            <h1 class="mt-2 text-xl font-bold tracking-tight text-slate-900 md:text-2xl">
                {{ $isEdit ? 'Editar negocio' : 'Crear negocio' }}
            </h1>
            <p class="mt-2 max-w-xl text-sm text-slate-600">
                {{ $isEdit ? 'Modifica la información del negocio.' : 'Completa los datos para registrar un nuevo negocio.' }}
            </p>
        </div>
        <a
            href="{{ route('admin.businesses.index') }}"
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
                    <input id="name" type="text" wire:model="businessForm.name" class="input-corporate" placeholder="Ej: Misión Panamericana de Colombia">
                    @error('businessForm.name') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="nit" class="{{ $labelClass }}">NIT</label>
                    <input id="nit" type="text" wire:model="businessForm.nit" class="input-corporate" placeholder="900100001-1">
                    @error('businessForm.nit') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="{{ $labelClass }}">Correo</label>
                    <input id="email" type="email" wire:model="businessForm.email" class="input-corporate" placeholder="contacto@negocio.org">
                    @error('businessForm.email') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="phone_number" class="{{ $labelClass }}">Teléfono</label>
                    <input id="phone_number" type="tel" wire:model="businessForm.phone_number" class="input-corporate" placeholder="+57 604 123 4567">
                    @error('businessForm.phone_number') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="city_id" class="{{ $labelClass }}">Ciudad</label>
                    <select id="city_id" wire:model="businessForm.city_id" class="input-corporate">
                        <option value="">Selecciona una ciudad</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city->id }}">
                                {{ $city->name }}@if ($city->department) — {{ $city->department->name }}@endif
                            </option>
                        @endforeach
                    </select>
                    @error('businessForm.city_id') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="address" class="{{ $labelClass }}">Dirección</label>
                    <input id="address" type="text" wire:model="businessForm.address" class="input-corporate" placeholder="Dirección de referencia">
                    @error('businessForm.address') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <section class="section-corporate">
            <div class="section-corporate-header">
                <h2 class="font-semibold text-slate-800">Estado</h2>
            </div>
            <div class="p-5">
                <x-ui.switch
                    id="business-status"
                    wire:model="businessForm.is_active"
                    label="Negocio activo"
                    description="Si está desactivado, no estará disponible para nuevas asignaciones."
                />
                @error('businessForm.is_active') <p class="{{ $errorClass }} mt-2">{{ $message }}</p> @enderror
            </div>
        </section>

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a
                href="{{ route('admin.businesses.index') }}"
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
                <span wire:loading.remove wire:target="save">{{ $isEdit ? 'Guardar cambios' : 'Crear negocio' }}</span>
                <span wire:loading wire:target="save">Guardando...</span>
            </button>
        </div>
    </form>
</div>
