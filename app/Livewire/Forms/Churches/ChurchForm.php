<?php

namespace App\Livewire\Forms\Churches;

use App\Actions\Church\CreateOrUpdateChurch;
use App\Enums\ChurchCategory;
use App\Models\Church;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Form;

class ChurchForm extends Form
{
    public ?int $business_id = null;

    public ?int $administrative_zone_id = null;

    public ?int $parent_id = null;

    public string $name = '';

    public ?string $address = null;

    public ?int $city_id = null;

    public string $category = '';

    public bool $is_active = true;

    public function fillFromChurch(Church $church): void
    {
        $this->business_id = $church->business_id;
        $this->administrative_zone_id = $church->administrative_zone_id;
        $this->parent_id = $church->parent_id;
        $this->name = $church->name;
        $this->address = $church->address;
        $this->city_id = $church->city_id;
        $this->category = $church->category?->value ?? '';
        $this->is_active = (bool) $church->is_active;
    }

    /**
     * @return array<string, mixed>
     */
    public function validateFor(?Church $church, User $actor): array
    {
        if (! $actor->isSuperAdmin()) {
            $this->business_id = $actor->business_id;
        }

        if (! ChurchCategory::requiresMotherChurchValue($this->category)) {
            $this->parent_id = null;
        }

        return $this->validate($this->rules($church, $actor));
    }

    public function save(?Church $church, User $actor): Church
    {
        $validated = $this->validateFor($church, $actor);

        return app(CreateOrUpdateChurch::class)->handle($validated, $church, $actor);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(?Church $church, User $actor): array
    {
        $churchId = $church?->id;
        $businessId = $actor->isSuperAdmin() ? $this->business_id : $actor->business_id;

        $rules = [
            'administrative_zone_id' => [
                'required',
                'integer',
                Rule::exists('administrative_zones', 'id')
                    ->where(fn ($query) => $query->where('business_id', $businessId)->whereNull('deleted_at')),
            ],
            'parent_id' => [
                Rule::requiredIf(fn () => ChurchCategory::requiresMotherChurchValue($this->category)),
                'nullable',
                'integer',
                Rule::exists('churches', 'id')
                    ->where(fn ($query) => $query
                        ->where('business_id', $businessId)
                        ->where('administrative_zone_id', $this->administrative_zone_id)
                        ->where('category', ChurchCategory::Principal->value)
                        ->whereNull('deleted_at')
                        ->when($churchId, fn ($q) => $q->where('id', '!=', $churchId))),
            ],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('churches', 'name')
                    ->ignore($churchId)
                    ->where(fn ($query) => $query->where('business_id', $businessId)->whereNull('deleted_at')),
            ],
            'address' => ['nullable', 'string', 'max:500'],
            'city_id' => [
                'nullable',
                'integer',
                Rule::exists('cities', 'id')->where('is_active', true),
            ],
            'category' => ['required', 'string', Rule::in(array_column(ChurchCategory::cases(), 'value'))],
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
            'business_id.required' => 'Debes seleccionar un negocio.',
            'business_id.exists' => 'El negocio seleccionado no es válido.',
            'administrative_zone_id.required' => 'Debes seleccionar una zona administrativa.',
            'administrative_zone_id.exists' => 'La zona seleccionada no es válida para este negocio.',
            'parent_id.exists' => 'La iglesia madre seleccionada no es válida para esta zona.',
            'parent_id.required' => 'Debes seleccionar una iglesia madre para esta categoría.',
            'name.required' => 'El nombre de la iglesia es obligatorio.',
            'name.unique' => 'Ya existe una iglesia con este nombre en el negocio seleccionado.',
            'name.max' => 'El nombre no puede superar 255 caracteres.',
            'address.max' => 'La dirección no puede superar 500 caracteres.',
            'city_id.exists' => 'La ciudad seleccionada no es válida.',
            'category.required' => 'Debes seleccionar una categoría.',
            'category.in' => 'La categoría seleccionada no es válida.',
        ];
    }
}
