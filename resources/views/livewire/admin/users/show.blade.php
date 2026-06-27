<div class="relative mx-auto w-full min-w-0 max-w-[90rem] overflow-x-hidden">
    <nav class="mb-6 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs font-medium text-slate-500">
        <a href="{{ route('dashboard') }}" wire:navigate class="rounded px-1.5 py-0.5 hover:bg-slate-200/60">Inicio</a>
        <span class="text-slate-300">/</span>
        <a href="{{ route('admin.users.index') }}" wire:navigate class="rounded px-1.5 py-0.5 hover:bg-slate-200/60">Usuarios</a>
        <span class="text-slate-300">/</span>
        <span class="font-semibold text-slate-900">{{ $user->username }}</span>
    </nav>

    <header class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0 flex-1 border-l-4 border-primary-500 pl-4 sm:pl-5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-primary-600/90">Usuario</p>
            <h1 class="mt-2 text-xl font-bold tracking-tight text-slate-900 md:text-2xl">{{ $user->full_name }}</h1>
            <div class="mt-3 flex flex-wrap items-center gap-2">
                @if ($user->status)
                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 ring-1 ring-emerald-600/20">Activo</span>
                @else
                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600 ring-1 ring-slate-500/20">Inactivo</span>
                @endif
                @if ($role = $user->roles->first())
                    <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-700 ring-1 ring-indigo-600/20">{{ $role->name }}</span>
                @endif
                <span class="text-sm text-slate-500">{{ $user->username }}</span>
            </div>
        </div>
        <div class="flex w-full shrink-0 flex-col gap-2 sm:w-auto sm:flex-row">
            <a
                href="{{ route('admin.users.index') }}"
                wire:navigate
                class="inline-flex min-h-11 flex-1 items-center justify-center gap-2 rounded-xl border border-primary-200/80 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-primary-50/50 sm:flex-none"
            >
                Volver al listado
            </a>
            @can('users.edit')
                <a
                    href="{{ route('admin.users.edit', $user) }}"
                    wire:navigate
                    class="inline-flex min-h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700 sm:flex-none"
                >
                    Editar usuario
                </a>
            @endcan
        </div>
    </header>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <section class="section-corporate lg:col-span-2">
            <div class="section-corporate-header">
                <h2 class="font-semibold text-slate-800">Datos personales</h2>
            </div>
            <dl class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Usuario</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-900">{{ $user->username }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Correo</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-900">{{ $user->email }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Nombre</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-900">{{ $user->first_name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Apellido</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-900">{{ $user->last_name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Teléfono</dt>
                    <dd class="mt-1 text-sm text-slate-900">{{ $user->phone_number ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Rol</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-900">{{ $user->roles->first()?->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Registro</dt>
                    <dd class="mt-1 text-sm text-slate-900">{{ $user->created_at?->format('d/m/Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Última actualización</dt>
                    <dd class="mt-1 text-sm text-slate-900">{{ $user->updated_at?->format('d/m/Y H:i') }}</dd>
                </div>
            </dl>
        </section>

        <section class="section-corporate">
            <div class="section-corporate-header">
                <h2 class="font-semibold text-slate-800">Organización</h2>
            </div>
            <dl class="space-y-5 p-5">
                @if (auth()->user()->isSuperAdmin())
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Negocio</dt>
                        <dd class="mt-1 text-sm font-medium text-slate-900">{{ $user->business?->name ?? '—' }}</dd>
                    </div>
                @endif
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Zona al iniciar sesión</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-900">
                        @php
                            $currentZoneId = $user->administrativeZones->first()?->pivot?->current_zone;
                            $currentZone = $currentZoneId
                                ? $user->administrativeZones->firstWhere('id', $currentZoneId)
                                : $user->administrativeZones->first();
                        @endphp
                        {{ $currentZone?->name ?? '—' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Iglesias asignadas</dt>
                    <dd class="mt-2">
                        @if ($user->churches->isEmpty())
                            <span class="text-sm text-slate-400">Sin iglesias asignadas.</span>
                        @else
                            <ul class="space-y-2">
                                @foreach ($user->churches as $church)
                                    @php
                                        $isDefault = (int) $church->pivot->current_church === (int) $church->id;
                                    @endphp
                                    <li @class([
                                        'rounded-xl border px-3 py-2.5 text-sm font-medium',
                                        'border-primary-200 bg-primary-50/60 text-slate-900' => $isDefault,
                                        'border-slate-100 bg-slate-50/50 text-slate-900' => ! $isDefault,
                                    ])>
                                        <div class="flex flex-wrap items-center gap-2">
                                            @can('churches.view')
                                                <a
                                                    href="{{ route('admin.churches.show', $church) }}"
                                                    wire:navigate
                                                    class="text-primary-700 transition hover:text-primary-800 hover:underline"
                                                >
                                                    {{ $church->name }}
                                                </a>
                                            @else
                                                {{ $church->name }}
                                            @endcan
                                            @if ($isDefault)
                                                <span class="inline-flex rounded-full bg-primary-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-primary-700">
                                                    Predeterminada al login
                                                </span>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </dd>
                </div>
            </dl>
        </section>
    </div>
</div>
