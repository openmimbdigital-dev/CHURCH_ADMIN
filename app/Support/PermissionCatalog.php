<?php

namespace App\Support;

use App\Models\User;

class PermissionCatalog
{
    public static function modulesFor(?User $user = null): array
    {
        $user ??= auth()->user();

        return collect(config('permissions.modules', []))
            ->filter(fn (array $module) => self::moduleVisibleTo($module, $user))
            ->all();
    }

    public static function moduleVisibleTo(array $module, ?User $user = null): bool
    {
        $user ??= auth()->user();

        if ($module['super_admin_only'] ?? false) {
            return (bool) $user?->isSuperAdmin();
        }

        return true;
    }

    public static function permissionNamesFor(?User $user = null): array
    {
        return collect(self::modulesFor($user))
            ->flatMap(fn (array $module) => array_keys($module['permissions']))
            ->values()
            ->all();
    }

    public static function superAdminOnlyPermissionNames(): array
    {
        return collect(config('permissions.modules', []))
            ->filter(fn (array $module) => $module['super_admin_only'] ?? false)
            ->flatMap(fn (array $module) => array_keys($module['permissions']))
            ->values()
            ->all();
    }

    public static function stripSuperAdminOnlyPermissions(array $permissionNames): array
    {
        return array_values(array_diff($permissionNames, self::superAdminOnlyPermissionNames()));
    }
}
