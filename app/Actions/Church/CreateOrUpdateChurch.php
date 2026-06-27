<?php

namespace App\Actions\Church;

use App\Models\Church;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateOrUpdateChurch
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data, ?Church $church, User $actor): Church
    {
        $businessId = $actor->isSuperAdmin()
            ? ($data['business_id'] ?? null)
            : $actor->business_id;

        return DB::transaction(function () use ($data, $church, $actor, $businessId) {
            $savedChurch = Church::updateOrCreate(
                ['id' => $church?->id],
                [
                    'business_id' => $businessId,
                    'administrative_zone_id' => $data['administrative_zone_id'],
                    'parent_id' => $data['parent_id'] ?? null,
                    'name' => $data['name'],
                    'address' => $data['address'] ?? null,
                    'city_id' => $data['city_id'] ?? null,
                    'category' => $data['category'],
                    'is_active' => (bool) ($data['is_active'] ?? true),
                ]
            );

            if ($church === null) {
                $this->assignCreatorToChurch($actor, $savedChurch);
            }

            return $savedChurch;
        });
    }

    protected function assignCreatorToChurch(User $actor, Church $church): void
    {
        if ($actor->churches()->whereKey($church->id)->exists()) {
            return;
        }

        $isFirstChurch = ! $actor->churches()->exists();

        $actor->churches()->attach($church->id, [
            'current_church' => $isFirstChurch ? $church->id : null,
        ]);

        if ($isFirstChurch) {
            $actor->update(['current_church_id' => $church->id]);
        }
    }
}
