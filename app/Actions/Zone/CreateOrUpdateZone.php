<?php

namespace App\Actions\Zone;

use App\Models\AdministrativeZone;
use App\Models\User;

class CreateOrUpdateZone
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data, ?AdministrativeZone $zone, User $actor): AdministrativeZone
    {
        $businessId = $actor->isSuperAdmin()
            ? ($data['business_id'] ?? null)
            : $actor->business_id;

        return AdministrativeZone::updateOrCreate(
            ['id' => $zone?->id],
            [
                'business_id' => $businessId,
                'name' => $data['name'],
                'address' => $data['address'] ?? null,
                'city_id' => $data['city_id'] ?? null,
                'is_active' => (bool) ($data['is_active'] ?? true),
            ]
        );
    }
}
