<?php

namespace Database\Seeders;

use App\Models\Church;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('Admin12345*');

        $users = [
            ['username' => 'admin', 'first_name' => 'Administrador', 'last_name' => 'Sistema', 'email' => 'admin@church.local', 'role' => 'superAdmin'],
            ['username' => 'joselito.ariza', 'first_name' => 'Joselito', 'last_name' => 'Ariza', 'email' => 'joselito.ariza@church.local', 'role' => 'Presbitero'],
            ['username' => 'juan.berdugo', 'first_name' => 'Juan', 'last_name' => 'Berdugo', 'email' => 'juan.berdugo@church.local', 'role' => 'Pastor'],
            ['username' => 'carlos.admin', 'first_name' => 'Carlos', 'last_name' => 'Mendoza', 'email' => 'carlos.admin@church.local', 'role' => 'Administrador'],
            ['username' => 'maria.lider', 'first_name' => 'María', 'last_name' => 'Gómez', 'email' => 'maria.lider@church.local', 'role' => 'Lider'],
            ['username' => 'pedro.copastor', 'first_name' => 'Pedro', 'last_name' => 'Ramírez', 'email' => 'pedro.copastor@church.local', 'role' => 'Co-pastor'],
            ['username' => 'ana.asistente', 'first_name' => 'Ana', 'last_name' => 'Torres', 'email' => 'ana.asistente@church.local', 'role' => 'Asistente'],
            ['username' => 'luis.maestro', 'first_name' => 'Luis', 'last_name' => 'Vargas', 'email' => 'luis.maestro@church.local', 'role' => 'Maestro'],
            ['username' => 'sofia.coordinadora', 'first_name' => 'Sofía', 'last_name' => 'Castillo', 'email' => 'sofia.coordinadora@church.local', 'role' => 'Coordinador educativo'],
            ['username' => 'diego.pastor', 'first_name' => 'Diego', 'last_name' => 'Herrera', 'email' => 'diego.pastor@church.local', 'role' => 'Pastor'],
        ];

        $rows = [];

        foreach ($users as $data) {
            $user = User::withTrashed()->updateOrCreate(
                ['username' => $data['username']],
                [
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'email' => $data['email'],
                    'password' => $password,
                    'phone_number' => null,
                    'status' => true,
                ]
            );

            if ($user->trashed()) {
                $user->restore();
            }

            $user->syncRoles([$data['role']]);

            $rows[] = [$data['username'], $data['role'], 'Admin12345*'];
        }

        $this->command->table(['Usuario', 'Rol', 'Contraseña'], $rows);

        $this->syncOrganizationAssignments();
    }

    /**
     * @return array<int, string>
     */
    protected function rolesForChurchAssignment(): array
    {
        return [
            'superAdmin',
            'Presbitero',
            'Pastor',
            'Administrador',
            'Lider',
            'Co-pastor',
            'Asistente',
            'Maestro',
            'Coordinador educativo',
        ];
    }

    /**
     * Usuarios base reutilizados como referente de rol en su iglesia principal.
     *
     * @return array<string, array<string, string>>
     */
    protected function preferredSeedUsersByChurch(): array
    {
        return [
            'Centro de Fe y Esperanza' => ['Pastor' => 'juan.berdugo'],
            'Iglesia Sede Medellín' => ['Presbitero' => 'joselito.ariza'],
            'Congregación Fe Norte' => ['Lider' => 'maria.lider'],
            'Campo Misionero Soledad' => ['Co-pastor' => 'pedro.copastor'],
            'Comunidad Fe Chapinero' => ['Administrador' => 'carlos.admin'],
            'Vida Nueva Granada' => ['Pastor' => 'diego.pastor'],
            'Cristo Vive Cartagena' => ['Asistente' => 'ana.asistente'],
            'Fraternidad Centro' => ['Maestro' => 'luis.maestro'],
            'Capilla Educativa Bello' => ['Coordinador educativo' => 'sofia.coordinadora'],
            'Fuente de Vida Santa Marta' => ['superAdmin' => 'admin'],
        ];
    }

    public function syncOrganizationAssignments(): void
    {
        if (Church::query()->doesntExist()) {
            return;
        }

        $password = Hash::make('Admin12345*');

        $churches = Church::query()
            ->with(['business', 'administrativeZone'])
            ->orderBy('id')
            ->get();

        foreach ($churches as $church) {
            foreach ($this->rolesForChurchAssignment() as $role) {
                $user = $this->resolveUserForChurchRole($church, $role, $password);

                if (! $user) {
                    continue;
                }

                $this->assignUserToChurchOrganization(
                    $user,
                    $church,
                    $this->shouldUseCurrentContext($user, $church, $role),
                    $role
                );
            }
        }

        $this->syncMultiChurchWithinZones();
        $this->syncCurrentChurchIdsFromPivot();
    }

    protected function syncCurrentChurchIdsFromPivot(): void
    {
        User::query()->each(fn (User $user) => $user->syncCurrentChurchFromPivot());
    }

    protected function syncMultiChurchWithinZones(): void
    {
        $churchesByZone = Church::query()
            ->orderBy('id')
            ->get()
            ->groupBy('administrative_zone_id');

        foreach ($churchesByZone as $zoneChurches) {
            if ($zoneChurches->count() < 2) {
                continue;
            }

            $churchIds = $zoneChurches->pluck('id')->all();

            foreach ($churchIds as $churchId) {
                $users = User::query()
                    ->whereHas('churches', fn ($query) => $query->where('churches.id', $churchId))
                    ->get();

                foreach ($users as $user) {
                    foreach ($churchIds as $otherChurchId) {
                        if ((int) $otherChurchId === (int) $churchId) {
                            continue;
                        }

                        if ($user->churches()->whereKey($otherChurchId)->exists()) {
                            continue;
                        }

                        $user->churches()->attach($otherChurchId, ['current_church' => null]);
                    }
                }
            }
        }
    }

    protected function resolveUserForChurchRole(Church $church, string $role, string $password): ?User
    {
        if ($role === 'superAdmin') {
            return User::query()->where('username', 'admin')->first();
        }

        $preferredUsername = $this->preferredSeedUsersByChurch()[$church->name][$role] ?? null;

        if ($preferredUsername) {
            $preferred = User::query()->where('username', $preferredUsername)->first();

            if ($preferred) {
                return $preferred;
            }
        }

        $username = $this->usernameForChurchRole($church, $role);
        [$firstName, $lastName] = $this->roleIdentity($role, $church);

        $user = User::withTrashed()->updateOrCreate(
            ['username' => $username],
            [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $username.'@church.local',
                'password' => $password,
                'phone_number' => null,
                'status' => true,
            ]
        );

        if ($user->trashed()) {
            $user->restore();
        }

        $user->syncRoles([$role]);

        return $user;
    }

    protected function usernameForChurchRole(Church $church, string $role): string
    {
        $roleSlug = Str::slug($role, '.');

        return 'c'.$church->id.'.'.$roleSlug;
    }

    /**
     * @return array{0: string, 1: string}
     */
    protected function roleIdentity(string $role, Church $church): array
    {
        $churchLabel = Str::limit($church->name, 60, '');

        return match ($role) {
            'Presbitero' => ['Presbítero', $churchLabel],
            'Pastor' => ['Pastor', $churchLabel],
            'Administrador' => ['Administrador', $churchLabel],
            'Lider' => ['Líder', $churchLabel],
            'Co-pastor' => ['Co-pastor', $churchLabel],
            'Asistente' => ['Asistente', $churchLabel],
            'Maestro' => ['Maestro', $churchLabel],
            'Coordinador educativo' => ['Coordinador', $churchLabel],
            default => [$role, $churchLabel],
        };
    }

    protected function shouldUseCurrentContext(User $user, Church $church, string $role): bool
    {
        if ($role === 'superAdmin') {
            return $church->name === 'Fuente de Vida Santa Marta';
        }

        $preferredUsername = $this->preferredSeedUsersByChurch()[$church->name][$role] ?? null;

        if ($preferredUsername && $user->username === $preferredUsername) {
            return true;
        }

        return $user->username === $this->usernameForChurchRole($church, $role);
    }

    protected function assignUserToChurchOrganization(User $user, Church $church, bool $asCurrentContext, string $role): void
    {
        $zoneId = (int) $church->administrative_zone_id;

        if ($church->business_id) {
            $user->update(['business_id' => $church->business_id]);
        }

        if ($role === 'superAdmin') {
            $user->administrativeZones()->syncWithoutDetaching([
                $zoneId => ['current_zone' => $asCurrentContext ? $zoneId : null],
            ]);

            if (! $user->churches()->whereKey($church->id)->exists()) {
                $user->churches()->attach($church->id, ['current_church' => $asCurrentContext ? $church->id : null]);
            } elseif ($asCurrentContext) {
                DB::table('church_user')->where('user_id', $user->id)->update(['current_church' => null]);
                DB::table('administrative_zone_user')->where('user_id', $user->id)->update(['current_zone' => null]);

                $user->churches()->updateExistingPivot($church->id, ['current_church' => $church->id]);
                $user->administrativeZones()->updateExistingPivot($zoneId, ['current_zone' => $zoneId]);
                $user->update(['current_church_id' => $church->id]);
            }

            return;
        }

        if ($asCurrentContext) {
            DB::table('church_user')->where('user_id', $user->id)->update(['current_church' => null]);
            DB::table('administrative_zone_user')->where('user_id', $user->id)->update(['current_zone' => null]);

            if ($user->administrativeZones()->whereKey($zoneId)->exists()) {
                $user->administrativeZones()->updateExistingPivot($zoneId, ['current_zone' => $zoneId]);
            } else {
                $user->administrativeZones()->attach($zoneId, ['current_zone' => $zoneId]);
            }

            if ($user->churches()->whereKey($church->id)->exists()) {
                $user->churches()->updateExistingPivot($church->id, ['current_church' => $church->id]);
            } else {
                $user->churches()->attach($church->id, ['current_church' => $church->id]);
            }

            $user->update(['current_church_id' => $church->id]);

            return;
        }

        $user->administrativeZones()->syncWithoutDetaching([
            $zoneId => ['current_zone' => null],
        ]);

        if (! $user->churches()->whereKey($church->id)->exists()) {
            $user->churches()->attach($church->id, ['current_church' => null]);
        }
    }
}
