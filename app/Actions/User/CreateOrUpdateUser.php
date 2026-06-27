<?php

namespace App\Actions\User;

use App\Models\AdministrativeZone;
use App\Models\Church;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateOrUpdateUser
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data, ?User $user, User $actor): User
    {
        return DB::transaction(function () use ($data, $user, $actor) {
            $businessId = $actor->isSuperAdmin()
                ? ($data['business_id'] ?? null)
                : $actor->business_id;

            $userData = [
                'username' => $data['username'],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone_number' => $data['phone_number'] ?? null,
                'status' => (bool) ($data['status'] ?? true),
                'business_id' => $businessId,
            ];

            if (! empty($data['password'])) {
                $userData['password'] = $data['password'];
            }

            $savedUser = User::updateOrCreate(
                ['id' => $user?->id],
                $userData
            );

            $this->syncZoneAndChurches(
                $savedUser,
                isset($data['administrative_zone_id']) ? (int) $data['administrative_zone_id'] : null,
                $data['church_ids'] ?? [],
                $businessId
            );

            return $savedUser->fresh(['administrativeZones', 'churches']);
        });
    }

    /**
     * @param  array<int|string>  $churchIds
     */
    protected function syncZoneAndChurches(
        User $user,
        ?int $zoneId,
        array $churchIds,
        ?int $businessId
    ): void {
        if ($zoneId) {
            $zoneExists = AdministrativeZone::query()
                ->whereKey($zoneId)
                ->when($businessId, fn ($query) => $query->where('business_id', $businessId))
                ->where('is_active', true)
                ->exists();

            $user->administrativeZones()->sync($zoneExists ? [$zoneId] : []);
        } else {
            $user->administrativeZones()->sync([]);
        }

        if (! $zoneId || $churchIds === []) {
            $user->churches()->sync([]);

            return;
        }

        $validChurchIds = Church::query()
            ->whereIn('id', $churchIds)
            ->where('administrative_zone_id', $zoneId)
            ->when($businessId, fn ($query) => $query->where('business_id', $businessId))
            ->where('is_active', true)
            ->pluck('id')
            ->all();

        $user->churches()->sync($validChurchIds);
    }
}
