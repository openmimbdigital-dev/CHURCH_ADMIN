<?php

namespace App\Actions\User;

use App\Models\Church;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class SetUserCurrentChurch
{
    public function handle(User $user, int $churchId): User
    {
        return DB::transaction(function () use ($user, $churchId) {
            $church = Church::query()
                ->whereKey($churchId)
                ->where('is_active', true)
                ->first();

            if (! $church || ! $user->churches()->whereKey($churchId)->exists()) {
                throw new AccessDeniedHttpException('No tienes acceso a la iglesia seleccionada.');
            }

            DB::table('church_user')
                ->where('user_id', $user->id)
                ->update(['current_church' => null]);

            $user->churches()->updateExistingPivot($churchId, [
                'current_church' => $churchId,
            ]);

            $zoneId = (int) $church->administrative_zone_id;

            DB::table('administrative_zone_user')
                ->where('user_id', $user->id)
                ->update(['current_zone' => null]);

            if ($user->administrativeZones()->whereKey($zoneId)->exists()) {
                $user->administrativeZones()->updateExistingPivot($zoneId, [
                    'current_zone' => $zoneId,
                ]);
            } else {
                $user->administrativeZones()->sync([
                    $zoneId => ['current_zone' => $zoneId],
                ]);
            }

            $user->update(['current_church_id' => $churchId]);

            return $user->fresh(['churches', 'currentChurch', 'administrativeZones']);
        });
    }
}
