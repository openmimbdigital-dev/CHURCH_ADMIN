<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guard = 'web';

        $perms = collect([
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'users.activate', 'users.deactivate',
            'education.view', 'education.create', 'education.edit', 'education.delete',
            'members.view', 'members.create', 'members.edit', 'members.delete',
            'reports.view', 'reports.export',
            'settings.view', 'settings.edit',
            'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
            'permissions.view', 'permissions.assign',
            'zones.view', 'zones.create', 'zones.edit', 'zones.delete',
            'churches.view', 'churches.create', 'churches.edit', 'churches.delete',
        ])->mapWithKeys(fn ($name) => [
            $name => Permission::firstOrCreate(['name' => $name, 'guard_name' => $guard]),
        ]);

        $superAdmin = Role::firstOrCreate(['name' => 'superAdmin', 'guard_name' => $guard]);
        $admin = Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => $guard]);
        $pastor = Role::firstOrCreate(['name' => 'Pastor', 'guard_name' => $guard]);
        $presbitero = Role::firstOrCreate(['name' => 'Presbitero', 'guard_name' => $guard]);
        $lider = Role::firstOrCreate(['name' => 'Lider', 'guard_name' => $guard]);
        $coPastor = Role::firstOrCreate(['name' => 'Co-pastor', 'guard_name' => $guard]);
        $asistente = Role::firstOrCreate(['name' => 'Asistente', 'guard_name' => $guard]);
        $maestro = Role::firstOrCreate(['name' => 'Maestro', 'guard_name' => $guard]);
        $coordinadorEducativo = Role::firstOrCreate(['name' => 'Coordinador educativo', 'guard_name' => $guard]);

        $pastorPermissions = $perms->only([
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'education.view', 'education.create', 'education.edit',
            'members.view', 'members.create', 'members.edit',
            'reports.view',
            'settings.view',
            'permissions.view', 'permissions.assign',
            'zones.view', 'zones.create', 'zones.edit',
            'churches.view', 'churches.create', 'churches.edit',
        ])->values();

        $superAdmin->syncPermissions($perms->values());

        $admin->syncPermissions($perms->only([
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'education.view', 'education.create', 'education.edit',
            'members.view', 'members.create', 'members.edit',
            'reports.view', 'reports.export',
            'settings.view',
            'roles.view',
            'permissions.view', 'permissions.assign',
            'zones.view', 'zones.create', 'zones.edit', 'zones.delete',
            'churches.view', 'churches.create', 'churches.edit', 'churches.delete',
        ])->values());

        $pastor->syncPermissions($pastorPermissions);
        $presbitero->syncPermissions($pastorPermissions);
        $coPastor->syncPermissions($perms->only([
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'permissions.view', 'permissions.assign',
        ])->values());

        $asistente->syncPermissions($perms->only([
            'users.view',
            'education.view',
            'members.view', 'members.create', 'members.edit',
            'reports.view',
        ])->values());

        $maestroPermissions = $perms->only([
            'education.view', 'education.create', 'education.edit',
            'members.view',
        ])->values();

        $maestro->syncPermissions($maestroPermissions);
        $coordinadorEducativo->syncPermissions($maestroPermissions);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
