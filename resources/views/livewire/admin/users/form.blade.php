@php
    $isEdit = (bool) $user;
    $labelClass = 'block text-sm font-medium text-slate-700 mb-1.5';
    $errorClass = 'mt-1.5 text-xs text-red-600';
@endphp

<div class="relative mx-auto w-full min-w-0 max-w-[90rem] overflow-x-hidden">
    <nav class="mb-6 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs font-medium text-slate-500">
        <a href="{{ route('dashboard') }}" wire:navigate class="rounded px-1.5 py-0.5 hover:bg-slate-200/60">Inicio</a>
        <span class="text-slate-300">/</span>
        <a href="{{ route('admin.users.index') }}" wire:navigate class="rounded px-1.5 py-0.5 hover:bg-slate-200/60">Usuarios</a>
        <span class="text-slate-300">/</span>
        <span class="font-semibold text-slate-900">{{ $isEdit ? 'Editar' : 'Crear' }}</span>
    </nav>

    <header class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0 flex-1 border-l-4 border-primary-500 pl-4 sm:pl-5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-primary-600/90">Usuarios</p>
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
                <h2 class="font-semibold text-slate-800">Datos personales</h2>
            </div>
            <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2">
                <div>
                    <label for="username" class="{{ $labelClass }}">Usuario <span class="text-rose-500">*</span></label>
                    <input id="username" type="text" wire:model="userForm.username" class="input-corporate" placeholder="nombre.usuario" autocomplete="off">
                    @error('userForm.username') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="{{ $labelClass }}">Correo <span class="text-rose-500">*</span></label>
                    <input id="email" type="email" wire:model="userForm.email" class="input-corporate" placeholder="usuario@ejemplo.com" autocomplete="off">
                    @error('userForm.email') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="first_name" class="{{ $labelClass }}">Nombre <span class="text-rose-500">*</span></label>
                    <input id="first_name" type="text" wire:model="userForm.first_name" class="input-corporate" placeholder="Nombre">
                    @error('userForm.first_name') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="last_name" class="{{ $labelClass }}">Apellido <span class="text-rose-500">*</span></label>
                    <input id="last_name" type="text" wire:model="userForm.last_name" class="input-corporate" placeholder="Apellido">
                    @error('userForm.last_name') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="phone_number" class="{{ $labelClass }}">Teléfono</label>
                    <input id="phone_number" type="tel" wire:model="userForm.phone_number" class="input-corporate" placeholder="+57 300 000 0000">
                    @error('userForm.phone_number') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2 grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <x-ui.password-input
                            id="password"
                            wire:model="userForm.password"
                            :label="'Contraseña'"
                            :required="! $isEdit"
                            :optional-hint="$isEdit ? 'opcional' : null"
                            :placeholder="$isEdit ? 'Dejar en blanco para no cambiar' : 'Contraseña segura'"
                            autocomplete="new-password"
                        />
                        @error('userForm.password') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <x-ui.password-input
                            id="password_confirmation"
                            wire:model="userForm.password_confirmation"
                            label="Confirmar contraseña"
                            :required="! $isEdit"
                            :optional-hint="$isEdit ? 'si cambias la contraseña' : null"
                            placeholder="Repite la contraseña"
                            autocomplete="new-password"
                        />
                        @error('userForm.password_confirmation') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2 rounded-xl border border-primary-100 bg-primary-50/40 p-4">
                        <p class="text-sm font-medium text-slate-800">Requisitos de contraseña segura</p>
                        <p class="mt-1 text-xs leading-relaxed text-slate-600">
                            La contraseña debe cumplir todos los siguientes criterios:
                        </p>
                        <ul class="mt-3 grid grid-cols-1 gap-2 text-xs text-slate-600 sm:grid-cols-2">
                            <li class="flex items-start gap-2">
                                <span class="mt-0.5 text-primary-600" aria-hidden="true">•</span>
                                <span>Mínimo <strong class="font-semibold text-slate-700">8 caracteres</strong></span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="mt-0.5 text-primary-600" aria-hidden="true">•</span>
                                <span>Al menos una letra <strong class="font-semibold text-slate-700">mayúscula</strong> y una <strong class="font-semibold text-slate-700">minúscula</strong></span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="mt-0.5 text-primary-600" aria-hidden="true">•</span>
                                <span>Al menos un <strong class="font-semibold text-slate-700">número</strong> (0–9)</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="mt-0.5 text-primary-600" aria-hidden="true">•</span>
                                <span>Al menos un <strong class="font-semibold text-slate-700">carácter especial</strong> (por ejemplo: <code class="rounded bg-white/80 px-1 py-0.5 text-[11px] text-slate-700">! @ # $ % *</code>)</span>
                            </li>
                        </ul>
                        @if ($isEdit)
                            <p class="mt-3 text-xs leading-relaxed text-slate-500">
                                Al editar, deja ambos campos en blanco si no deseas cambiar la contraseña actual.
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <section class="section-corporate">
            <div class="section-corporate-header">
                <h2 class="font-semibold text-slate-800">Organización</h2>
            </div>
            <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2">
                @if (auth()->user()->isSuperAdmin())
                    <div class="md:col-span-2">
                        <label for="business_id" class="{{ $labelClass }}">Negocio <span class="text-rose-500">*</span></label>
                        <select id="business_id" wire:model.live="userForm.business_id" class="input-corporate">
                            <option value="">Selecciona un negocio</option>
                            @foreach ($businesses as $business)
                                <option value="{{ $business->id }}">{{ $business->name }}</option>
                            @endforeach
                        </select>
                        @error('userForm.business_id') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                    </div>
                @endif

                <div @class(['md:col-span-2' => ! auth()->user()->isSuperAdmin()])>
                    <label for="administrative_zone_id" class="{{ $labelClass }}">Zona administrativa</label>
                    <select
                        id="administrative_zone_id"
                        wire:model.live="userForm.administrative_zone_id"
                        class="input-corporate"
                        @disabled(! $userForm->business_id && auth()->user()->isSuperAdmin())
                    >
                        <option value="">
                            @if (auth()->user()->isSuperAdmin() && ! $userForm->business_id)
                                Primero selecciona un negocio
                            @else
                                Selecciona una zona
                            @endif
                        </option>
                        @foreach ($zones as $zone)
                            <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                        @endforeach
                    </select>
                    @error('userForm.administrative_zone_id') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                @if ($userForm->administrative_zone_id)
                    <div class="md:col-span-2">
                        <p class="{{ $labelClass }}">Iglesias</p>
                        <p class="mb-3 text-xs text-slate-500">Selecciona una o más iglesias de la zona.</p>
                        @if ($churches->isEmpty())
                            <p class="text-sm text-slate-400">No hay iglesias activas en esta zona.</p>
                        @else
                            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                @foreach ($churches as $church)
                                    <label @class([
                                        'flex cursor-pointer items-center gap-2.5 rounded-xl border px-3 py-2.5 transition',
                                        'border-primary-200 bg-primary-50' => in_array($church->id, $userForm->church_ids),
                                        'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50' => ! in_array($church->id, $userForm->church_ids),
                                    ])>
                                        <input
                                            type="checkbox"
                                            wire:model.live="userForm.church_ids"
                                            value="{{ $church->id }}"
                                            class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                                        >
                                        <span class="text-sm text-slate-700">{{ $church->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                        @error('userForm.church_ids') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                        @error('userForm.church_ids.*') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror

                        @if (count($userForm->church_ids) > 1)
                            <div class="mt-5 rounded-xl border border-primary-100 bg-primary-50/40 p-4">
                                <label for="default_church_id" class="{{ $labelClass }}">
                                    Iglesia predeterminada al iniciar sesión <span class="text-rose-500">*</span>
                                </label>
                                <p class="mb-3 text-xs leading-relaxed text-slate-600">
                                    Este usuario está asignado a más de una iglesia. Indica cuál debe quedar activa por defecto cuando inicie sesión.
                                    El sistema guardará esa iglesia en el contexto del usuario y la zona administrativa correspondiente.
                                </p>
                                <select id="default_church_id" wire:model="userForm.default_church_id" class="input-corporate">
                                    <option value="">Selecciona la iglesia predeterminada</option>
                                    @foreach ($churches->whereIn('id', $userForm->church_ids) as $church)
                                        <option value="{{ $church->id }}">{{ $church->name }}</option>
                                    @endforeach
                                </select>
                                @error('userForm.default_church_id') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                            </div>
                        @elseif (count($userForm->church_ids) === 1)
                            <p class="mt-4 text-xs leading-relaxed text-slate-500">
                                Al tener una sola iglesia asignada, se usará automáticamente como contexto predeterminado al iniciar sesión.
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </section>

        <section class="section-corporate">
            <div class="section-corporate-header">
                <h2 class="font-semibold text-slate-800">Estado</h2>
            </div>
            <div class="p-5">
                <x-ui.switch
                    id="user-status"
                    wire:model="userForm.status"
                    label="Usuario activo"
                    description="Si está desactivado, el usuario no podrá iniciar sesión."
                />
                @error('userForm.status') <p class="{{ $errorClass }} mt-2">{{ $message }}</p> @enderror
            </div>
        </section>

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a
                href="{{ route('admin.users.index') }}"
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
                <span wire:loading.remove wire:target="save">{{ $isEdit ? 'Guardar cambios' : 'Crear usuario' }}</span>
                <span wire:loading wire:target="save">Guardando...</span>
            </button>
        </div>
    </form>
</div>
