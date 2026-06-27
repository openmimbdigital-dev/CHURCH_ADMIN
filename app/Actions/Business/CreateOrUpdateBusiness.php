<?php

namespace App\Actions\Business;

use App\Models\Business;
use App\Models\User;
use Illuminate\Support\Str;

class CreateOrUpdateBusiness
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data, ?Business $business, User $actor): Business
    {
        abort_unless($actor->isSuperAdmin(), 403);

        $attributes = [
            'name' => $data['name'],
            'slug' => $this->resolveSlug($data['name'], $business),
            'nit' => $data['nit'] ?? null,
            'email' => $data['email'] ?? null,
            'phone_number' => $data['phone_number'] ?? null,
            'address' => $data['address'] ?? null,
            'city_id' => $data['city_id'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? true),
        ];

        if ($business) {
            $business->update($attributes);

            return $business->fresh();
        }

        return Business::create($attributes);
    }

    protected function resolveSlug(string $name, ?Business $business): string
    {
        $base = Str::slug($name);

        if ($base === '') {
            $base = 'negocio';
        }

        if ($business && $business->name === $name) {
            return $business->slug;
        }

        $slug = $base;
        $suffix = 1;

        while (
            Business::withTrashed()
                ->where('slug', $slug)
                ->when($business, fn ($query) => $query->whereKeyNot($business->id))
                ->exists()
        ) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
