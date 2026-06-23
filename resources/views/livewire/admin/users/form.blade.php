@php
    $isEdit = (bool) $user;
    $inputClass = 'w-full min-h-11 rounded-xl border border-slate-200/80 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/15 transition';
    $labelClass = 'block text-sm font-medium text-slate-700 mb-1.5';
    $errorClass = 'mt-1.5 text-xs text-red-600';
    $sectionClass = 'overflow-hidden rounded-2xl border border-slate-200/60 bg-white shadow-sm';
    $sectionHeaderClass = 'border-b border-slate-100/80 bg-slate-50/60 px-5 py-4';
@endphp

<div class="relative mx-auto w-full max-w-4xl">
    <nav class="mb-6 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs font-medium text-slate-500">
        <a href="{{ route('dashboard') }}" wire:navigate class="rounded px-1.5 py-0.5 hover:bg-slate-200/60">Inicio</a>
        <span class="text-slate-300">/</span>
        <a href="{{ route('admin.users.index') }}" wire:navigate class="rounded px-1.5 py-0.5 hover:bg-slate-200/60">Usuarios</a>
        <span class="text-slate-300">/</span>
        <span class="font-semibold text-slate-900">{{ $isEdit ? 'Editar' : 'Crear' }}</span>
    </nav>

    <header class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0 flex-1 border-l-4 border-indigo-500 pl-5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-indigo-600/90">Usuarios</p>
            <h1 class="mt-2 text-xl font-bold tracking-tight text-slate-900 md:text-2xl">
                {{ $isEdit ? 'Editar usuario' : 'Crear usuario' }}
            </h1>
            <p class="mt-2 max-w-xl text-sm text-slate-600">
                {{ $isEdit ? 'Modifica la información del usuario.' : 'Completa los datos para registrar un nuevo usuario.' }}
            </p>
        </div>
        <a
            href="{{ route('admin.users.index') }}"
            wire:navigate
            class="inline-flex min-h-11 shrink-0 items-center justify-center gap-2 rounded-xl border border-slate-200/70 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
    </header>

    <form class="space-y-6" onsubmit="return false">
        {{-- Datos personales --}}
        <section class="{{ $sectionClass }}">
            <div class="{{ $sectionHeaderClass }}">
                <h2 class="font-semibold text-slate-800">Datos personales</h2>
            </div>
            <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2">
                <div>
                    <label for="username" class="{{ $labelClass }}">Usuario <span class="text-rose-500">*</span></label>
                    <input id="username" type="text" wire:model="username" class="{{ $inputClass }}" placeholder="nombre.usuario" autocomplete="off">
                    @error('username') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="{{ $labelClass }}">Correo <span class="text-rose-500">*</span></label>
                    <input id="email" type="email" wire:model="email" class="{{ $inputClass }}" placeholder="usuario@ejemplo.com" autocomplete="off">
                    @error('email') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="first_name" class="{{ $labelClass }}">Nombre <span class="text-rose-500">*</span></label>
                    <input id="first_name" type="text" wire:model="first_name" class="{{ $inputClass }}" placeholder="Nombre">
                    @error('first_name') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="last_name" class="{{ $labelClass }}">Apellido <span class="text-rose-500">*</span></label>
                    <input id="last_name" type="text" wire:model="last_name" class="{{ $inputClass }}" placeholder="Apellido">
                    @error('last_name') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="phone_number" class="{{ $labelClass }}">Teléfono</label>
                    <input id="phone_number" type="tel" wire:model="phone_number" class="{{ $inputClass }}" placeholder="+57 300 000 0000">
                    @error('phone_number') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password" class="{{ $labelClass }}">
                        Contraseña
                        @if ($isEdit)
                            <span class="font-normal text-slate-400">(opcional)</span>
                        @else
                            <span class="text-rose-500">*</span>
                        @endif
                    </label>
                    <input
                        id="password"
                        type="password"
                        wire:model="password"
                        class="{{ $inputClass }}"
                        placeholder="{{ $isEdit ? 'Dejar en blanco para no cambiar' : 'Contraseña segura' }}"
                        autocomplete="new-password"
                    >
                    @error('password') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        {{-- Organización --}}
        <section class="{{ $sectionClass }}">
            <div class="{{ $sectionHeaderClass }}">
                <h2 class="font-semibold text-slate-800">Organización</h2>
            </div>
            <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2">
                @if (auth()->user()->isSuperAdmin())
                    <div class="md:col-span-2">
                        <label for="business_id" class="{{ $labelClass }}">Negocio <span class="text-rose-500">*</span></label>
                        <select id="business_id" wire:model.live="business_id" class="{{ $inputClass }}">
                            <option value="">Selecciona un negocio</option>
                            @foreach ($businesses as $business)
                                <option value="{{ $business->id }}">{{ $business->name }}</option>
                            @endforeach
                        </select>
                        @error('business_id') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                    </div>
                @endif

                <div>
                    <label for="administrative_zone_id" class="{{ $labelClass }}">Zona administrativa</label>
                    <select
                        id="administrative_zone_id"
                        wire:model.live="administrative_zone_id"
                        class="{{ $inputClass }}"
                        @disabled(! $business_id && auth()->user()->isSuperAdmin())
                    >
                        <option value="">
                            @if (auth()->user()->isSuperAdmin() && ! $business_id)
                                Primero selecciona un negocio
                            @else
                                Selecciona una zona
                            @endif
                        </option>
                        @foreach ($zones as $zone)
                            <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                        @endforeach
                    </select>
                    @error('administrative_zone_id') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="church_id" class="{{ $labelClass }}">Iglesia</label>
                    <select
                        id="church_id"
                        wire:model="church_id"
                        class="{{ $inputClass }}"
                        @disabled(! $administrative_zone_id)
                    >
                        <option value="">
                            {{ $administrative_zone_id ? 'Selecciona una iglesia' : 'Primero selecciona una zona' }}
                        </option>
                        @foreach ($churches as $church)
                            <option value="{{ $church->id }}">{{ $church->name }}</option>
                        @endforeach
                    </select>
                    @error('church_id') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        {{-- Estado --}}
        <section class="{{ $sectionClass }}">
            <div class="{{ $sectionHeaderClass }}">
                <h2 class="font-semibold text-slate-800">Estado</h2>
            </div>
            <div class="p-5">
                <label class="flex cursor-pointer items-center gap-3">
                    <input
                        type="checkbox"
                        wire:model="status"
                        class="h-5 w-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20"
                    >
                    <span class="text-sm font-medium text-slate-700">Usuario activo</span>
                </label>
                @error('status') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
            </div>
        </section>
    </form>
</div>
