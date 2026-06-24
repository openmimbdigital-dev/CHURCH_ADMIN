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

            $this->syncLeadership(
                $savedUser,
                $data['administrative_zone_id'] ?? null,
                $data['church_id'] ?? null,
                $businessId
            );

            return $savedUser->fresh();
        });
    }

    protected function syncLeadership(
        User $user,
        ?int $zoneId,
        ?int $churchId,
        ?int $businessId
    ): void {
        $user->ledChurches()->detach();
        $user->ledAdministrativeZones()->detach();

        $zoneName = null;
        $churchName = null;

        if ($churchId) {
            $church = Church::query()
                ->with('administrativeZone')
                ->whereKey($churchId)
                ->when($businessId, fn ($q) => $q->where('business_id', $businessId))
                ->first();

            if ($church) {
                $user->ledChurches()->attach($church->id);
                $churchName = $church->name;
                $zoneName = $church->administrativeZone?->name;
            }
        } elseif ($zoneId) {
            $zone = AdministrativeZone::query()
                ->whereKey($zoneId)
                ->when($businessId, fn ($q) => $q->where('business_id', $businessId))
                ->first();

            if ($zone) {
                $user->ledAdministrativeZones()->attach($zone->id);
                $zoneName = $zone->name;
            }
        }

        $user->update([
            'administrative_zone_name' => $zoneName,
            'church_name' => $churchName,
        ]);
    }
}
