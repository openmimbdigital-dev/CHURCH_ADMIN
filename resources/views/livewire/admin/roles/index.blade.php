<div class="relative mx-auto w-full max-w-[90rem]">
    <nav class="mb-6 flex items-center gap-x-2 text-xs font-medium text-slate-500">
        <a href="{{ route('dashboard') }}" wire:navigate class="rounded px-1.5 py-0.5 hover:bg-slate-200/60">Inicio</a>
        <span class="text-slate-300">/</span>
        <a href="{{ route('admin.users.index') }}" wire:navigate class="rounded px-1.5 py-0.5 hover:bg-slate-200/60">Usuarios</a>
        <span class="text-slate-300">/</span>
        <span class="font-semibold text-slate-900">Roles y permisos</span>
    </nav>

    <header class="mb-6">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-stretch lg:justify-between">
            <div class="min-w-0 flex-1 border-l-4 border-primary-500 pl-5">
                <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-primary-600/90">Seguridad</p>
                <h1 class="mt-2 text-xl font-bold tracking-tight text-slate-900 md:text-2xl">Roles y permisos</h1>
                <p class="mt-2 max-w-xl text-sm text-slate-600">
                    Define roles del sistema y controla qué puede hacer cada perfil en la plataforma.
                </p>
            </div>
            <div class="grid shrink-0 grid-cols-2 gap-3 sm:grid-cols-3 sm:max-w-lg lg:self-center">
                <div class="card-corporate p-4">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Roles</p>
                    <p class="mt-2 text-3xl font-semibold tabular-nums text-slate-900">{{ $roles->count() }}</p>
                </div>
                <div class="card-corporate p-4">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Permisos</p>
                    <p class="mt-2 text-3xl font-semibold tabular-nums text-primary-600">{{ $totalPerms }}</p>
                </div>
                <div class="card-corporate col-span-2 p-4 sm:col-span-1">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Asignados</p>
                    <p class="mt-2 text-3xl font-semibold tabular-nums text-emerald-600">{{ $roles->sum('users_count') }}</p>
                </div>
            </div>
        </div>
    </header>

    @can('roles.create')
        <div class="mb-4 flex justify-end">
            <button
                wire:click="openCreate"
                type="button"
                class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500/30"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo rol
            </button>
        </div>
    @endcan

    <section class="panel-corporate mb-6">
        <div class="panel-corporate-header">
            <h2 class="font-semibold text-slate-800">Buscar roles</h2>
        </div>
        <div class="p-4">
            <div class="relative max-w-sm">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    wire:model.live.debounce.300ms="search"
                    type="search"
                    placeholder="Buscar rol..."
                    class="input-corporate w-full pl-10"
                >
            </div>
        </div>
    </section>

    <div class="space-y-3">
        @foreach ($roles as $role)
            @php
                $isProtected = in_array($role->name, ['superAdmin']);
                $isSuperAdmin = $role->name === 'superAdmin';
                $isExpanded = $expandedRole === $role->id;

                $roleColors = [
                    'superAdmin' => ['bg' => 'bg-violet-100', 'text' => 'text-violet-700', 'border' => 'border-violet-200'],
                    'Administrador' => ['bg' => 'bg-primary-100', 'text' => 'text-primary-700', 'border' => 'border-primary-200'],
                    'Pastor' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200'],
                    'Presbitero' => ['bg' => 'bg-sky-100', 'text' => 'text-sky-700', 'border' => 'border-sky-200'],
                    'Co-pastor' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'border' => 'border-amber-200'],
                    'Lider' => ['bg' => 'bg-teal-100', 'text' => 'text-teal-700', 'border' => 'border-teal-200'],
                    'Asistente' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'border' => 'border-slate-200'],
                    'Maestro' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'border' => 'border-blue-200'],
                    'Coordinador educativo' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'border' => 'border-purple-200'],
                ];
                $color = $roleColors[$role->name] ?? ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'border' => 'border-slate-200'];
                $roleMeta = config("permissions.roles.{$role->name}");
            @endphp

            <article class="card-corporate overflow-hidden">
                <div class="flex flex-col gap-3 justify-between px-5 py-4 sm:flex-row sm:items-center">
                    <div class="flex min-w-0 items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $color['bg'] }}">
                            <svg class="h-5 w-5 {{ $color['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-sm font-semibold text-slate-900">{{ $role->name }}</span>
                                @if ($isProtected)
                                    <span class="inline-flex items-center gap-1 rounded-full border px-2 py-0.5 text-[11px] font-medium {{ $color['bg'] }} {{ $color['text'] }} {{ $color['border'] }}">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                        Protegido
                                    </span>
                                @endif
                            </div>
                            <p class="mt-0.5 text-xs text-slate-500">
                                {{ $roleMeta['description'] ?? 'Rol del sistema' }}
                            </p>
                            <div class="mt-1 flex items-center gap-3 text-xs text-slate-400">
                                @if ($isSuperAdmin)
                                    <span>Acceso completo al sistema</span>
                                @else
                                    <span>{{ $role->permissions_count }} {{ $role->permissions_count === 1 ? 'permiso' : 'permisos' }}</span>
                                @endif
                                <span class="text-slate-200">·</span>
                                <span>{{ $role->users_count }} {{ $role->users_count === 1 ? 'usuario' : 'usuarios' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex shrink-0 items-center gap-2">
                        @if (! $isSuperAdmin)
                            <button
                                wire:click="toggleExpand({{ $role->id }})"
                                type="button"
                                @class([
                                    'inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium transition',
                                    'bg-slate-800 text-white' => $isExpanded,
                                    'bg-slate-100 text-slate-600 hover:bg-slate-200' => ! $isExpanded,
                                ])
                            >
                                <svg @class(['h-3.5 w-3.5 transition-transform duration-200', 'rotate-180' => $isExpanded]) fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                                Permisos
                            </button>
                        @endif

                        @can('roles.view')
                            <button
                                wire:click="openEdit({{ $role->id }})"
                                type="button"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-primary-50 px-3 py-1.5 text-xs font-medium text-primary-700 transition hover:bg-primary-100"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                @if ($isSuperAdmin || ! auth()->user()->can('roles.edit'))
                                    Ver
                                @else
                                    Editar
                                @endif
                            </button>
                        @endcan

                        @can('roles.delete')
                            @if (! $isProtected)
                                <button
                                    wire:click="confirmDelete({{ $role->id }})"
                                    type="button"
                                    @class([
                                        'inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium transition',
                                        'bg-rose-50 text-rose-600 hover:bg-rose-100' => $role->users_count === 0,
                                        'cursor-not-allowed bg-slate-100 text-slate-400' => $role->users_count > 0,
                                    ])
                                    @if ($role->users_count > 0) title="Tiene usuarios asignados" @endif
                                >
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Eliminar
                                </button>
                            @endif
                        @endcan
                    </div>
                </div>

                @if ($isExpanded && ! $isSuperAdmin)
                    <div class="border-t border-primary-100/80 bg-primary-50/30 px-5 py-4">
                        @if ($role->permissions_count === 0)
                            <p class="text-sm italic text-slate-400">Sin permisos asignados.</p>
                        @else
                            @php
                                $permsByModule = [];
                                foreach ($modules as $moduleKey => $module) {
                                    $permsInModule = $role->permissions->filter(
                                        fn ($p) => array_key_exists($p->name, $module['permissions'])
                                    );
                                    if ($permsInModule->isNotEmpty()) {
                                        $permsByModule[$moduleKey] = ['module' => $module, 'perms' => $permsInModule];
                                    }
                                }
                                $allModulePerms = collect($modules)->flatMap(fn ($m) => array_keys($m['permissions']))->all();
                                $otherPerms = $role->permissions->filter(fn ($p) => ! in_array($p->name, $allModulePerms));
                            @endphp
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach ($permsByModule as $data)
                                    <div>
                                        <p class="mb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ $data['module']['name'] }}</p>
                                        <div class="space-y-1">
                                            @foreach ($data['perms'] as $perm)
                                                <div class="flex items-center gap-2">
                                                    <svg class="h-3.5 w-3.5 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    <span class="text-xs text-slate-700">{{ $data['module']['permissions'][$perm->name] ?? $perm->name }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                                @if ($otherPerms->isNotEmpty())
                                    <div>
                                        <p class="mb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Otros</p>
                                        <div class="space-y-1">
                                            @foreach ($otherPerms as $perm)
                                                <div class="flex items-center gap-2">
                                                    <svg class="h-3.5 w-3.5 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    <span class="text-xs text-slate-700">{{ $perm->name }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endif
            </article>
        @endforeach

        @if ($roles->isEmpty())
            <div class="card-corporate py-14 text-center">
                <p class="text-sm text-slate-400">No se encontraron roles.</p>
            </div>
        @endif
    </div>

    @if ($showModal)
        @php
            $editingRole = $selected_id ? \Spatie\Permission\Models\Role::find($selected_id) : null;
            $isSuper = $editingRole?->name === 'superAdmin';
            $isProtected = in_array($editingRole?->name, ['superAdmin']);
            $canEdit = auth()->user()->can('roles.edit') && ! $isSuper;
            $canAssign = auth()->user()->can('permissions.assign') && ! $isSuper;
            $isReadOnly = $isSuper || ! auth()->user()->can('roles.edit');
        @endphp
        <div
            class="fixed inset-0 z-50 flex items-start justify-center bg-black/50 p-4 pt-16 backdrop-blur-sm"
            x-on:keydown.escape.window="$wire.closeModal()"
        >
            <div class="flex max-h-[85vh] w-full max-w-2xl flex-col overflow-hidden rounded-2xl bg-white shadow-xl ring-1 ring-slate-200/80">
                <div class="section-corporate-header flex shrink-0 items-center justify-between px-6 py-4">
                    <h3 class="text-lg font-bold text-slate-900">
                        @if (! $selected_id)
                            Nuevo rol
                        @elseif ($isSuper)
                            Ver rol: superAdmin
                        @else
                            {{ $canEdit ? 'Editar' : 'Ver' }} rol: {{ $editingRole?->name }}
                        @endif
                    </h3>
                    <button wire:click="closeModal" type="button" class="text-slate-400 transition hover:text-slate-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="flex-1 space-y-5 overflow-y-auto px-6 py-5">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">
                            Nombre del rol @if (! $isReadOnly)<span class="text-red-500">*</span>@endif
                        </label>
                        @if ($isProtected)
                            <div class="flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5">
                                <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <span class="text-sm font-medium text-slate-600">{{ $name }}</span>
                                <span class="ml-auto text-xs text-slate-400">Rol protegido del sistema</span>
                            </div>
                        @elseif ($isReadOnly)
                            <div class="input-corporate bg-slate-50 text-slate-600">{{ $name }}</div>
                        @else
                            <input
                                wire:model="name"
                                type="text"
                                placeholder="Ej: Auditor"
                                class="input-corporate w-full @error('name') border-red-400 bg-red-50 @enderror"
                            >
                            @error('name')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <div>
                        <div class="mb-3 flex items-center justify-between">
                            <label class="text-sm font-medium text-slate-700">Permisos</label>
                            @if ($canAssign)
                                <div class="flex items-center gap-2">
                                    <button
                                        type="button"
                                        wire:click="$set('selectedPerms', {{ json_encode($allPermissions->pluck('name')->toArray()) }})"
                                        class="text-xs font-medium text-primary-600 hover:text-primary-700"
                                    >
                                        Seleccionar todos
                                    </button>
                                    <span class="text-slate-200">·</span>
                                    <button
                                        type="button"
                                        wire:click="$set('selectedPerms', [])"
                                        class="text-xs font-medium text-slate-500 hover:text-slate-700"
                                    >
                                        Limpiar
                                    </button>
                                </div>
                            @endif
                        </div>

                        @if ($isSuper)
                            <div class="flex items-center gap-3 rounded-xl border border-violet-100 bg-violet-50 px-4 py-3">
                                <svg class="h-5 w-5 shrink-0 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                <p class="text-sm text-violet-800">
                                    El rol <strong>superAdmin</strong> tiene acceso completo. Sus permisos no se gestionan individualmente.
                                </p>
                            </div>
                        @else
                            <div class="space-y-5 rounded-xl border border-primary-100 bg-primary-50/40 p-4">
                                @foreach ($modules as $module)
                                    @php
                                        $modulePermNames = array_keys($module['permissions']);
                                        $moduleSelected = count(array_intersect($modulePermNames, $selectedPerms));
                                        $moduleTotal = count($modulePermNames);
                                    @endphp
                                    <div>
                                        <div class="mb-2 flex items-center justify-between">
                                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ $module['name'] }}</p>
                                            <span class="text-[11px] text-slate-400">{{ $moduleSelected }}/{{ $moduleTotal }}</span>
                                        </div>
                                        <div class="grid grid-cols-1 gap-1.5 sm:grid-cols-2">
                                            @foreach ($module['permissions'] as $permName => $permLabel)
                                                <label @class([
                                                    'flex cursor-pointer items-center gap-2.5 rounded-lg border px-3 py-2 transition',
                                                    'border-primary-200 bg-primary-50' => in_array($permName, $selectedPerms),
                                                    'border-slate-100 bg-white hover:border-slate-200 hover:bg-slate-50' => ! in_array($permName, $selectedPerms),
                                                    'cursor-default opacity-70' => ! $canAssign,
                                                ])>
                                                    <input
                                                        type="checkbox"
                                                        wire:model="selectedPerms"
                                                        value="{{ $permName }}"
                                                        @disabled(! $canAssign)
                                                        class="h-3.5 w-3.5 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                                                    >
                                                    <span class="text-xs leading-tight text-slate-700">{{ $permLabel }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex shrink-0 justify-end gap-3 border-t border-slate-100 px-6 py-4">
                    <button
                        wire:click="closeModal"
                        type="button"
                        class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                    >
                        {{ $isReadOnly ? 'Cerrar' : 'Cancelar' }}
                    </button>
                    @if (! $isReadOnly && (($selected_id && auth()->user()->can('roles.edit')) || (! $selected_id && auth()->user()->can('roles.create'))))
                        <button
                            wire:click="save"
                            type="button"
                            class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-700"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $selected_id ? 'Guardar cambios' : 'Crear rol' }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
