<?php

namespace App\Livewire\Forms\Zones;

use App\Actions\Zone\CreateOrUpdateZone;
use App\Models\AdministrativeZone;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Form;

class ZoneForm extends Form
{
    public string $name = '';

    public ?string $address = null;

    public ?int $city_id = null;

    public bool $is_active = true;

    public ?int $business_id = null;

    public function fillFromZone(AdministrativeZone $zone): void
    {
        $this->name = $zone->name;
        $this->address = $zone->address;
        $this->city_id = $zone->city_id;
        $this->is_active = (bool) $zone->is_active;
        $this->business_id = $zone->business_id;
    }

    /**
     * @return array<string, mixed>
     */
    public function validateFor(?AdministrativeZone $zone, User $actor): array
    {
        if (! $actor->isSuperAdmin()) {
            $this->business_id = $actor->business_id;
        }

        return $this->validate($this->rules($zone, $actor));
    }

    public function save(?AdministrativeZone $zone, User $actor): AdministrativeZone
    {
        $validated = $this->validateFor($zone, $actor);

        return app(CreateOrUpdateZone::class)->handle($validated, $zone, $actor);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(?AdministrativeZone $zone, User $actor): array
    {
        $zoneId = $zone?->id;
        $businessId = $actor->isSuperAdmin() ? $this->business_id : $actor->business_id;

        $rules = [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('administrative_zones', 'name')
                    ->ignore($zoneId)
                    ->where(fn ($query) => $query->where('business_id', $businessId)->whereNull('deleted_at')),
            ],
            'address' => ['nullable', 'string', 'max:500'],
            'city_id' => [
                'nullable',
                'integer',
                Rule::exists('cities', 'id')->where('is_active', true),
            ],
            'is_active' => ['boolean'],
        ];

        if ($actor->isSuperAdmin()) {
            $rules['business_id'] = [
                'required',
                'integer',
                Rule::exists('businesses', 'id')->where('is_active', true),
            ];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la zona es obligatorio.',
            'name.unique' => 'Ya existe una zona con este nombre en el negocio seleccionado.',
            'name.max' => 'El nombre no puede superar 255 caracteres.',
            'address.max' => 'La dirección no puede superar 500 caracteres.',
            'city_id.exists' => 'La ciudad seleccionada no es válida.',
            'business_id.required' => 'Debes seleccionar un negocio.',
            'business_id.exists' => 'El negocio seleccionado no es válido.',
        ];
    }
}
