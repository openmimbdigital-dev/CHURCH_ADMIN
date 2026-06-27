<?php

namespace App\Livewire\Forms\Businesses;

use App\Actions\Business\CreateOrUpdateBusiness;
use App\Models\Business;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Form;

class BusinessForm extends Form
{
    public string $name = '';

    public ?string $nit = null;

    public ?string $email = null;

    public ?string $phone_number = null;

    public ?string $address = null;

    public ?int $city_id = null;

    public bool $is_active = true;

    public function fillFromBusiness(Business $business): void
    {
        $this->name = $business->name;
        $this->nit = $business->nit;
        $this->email = $business->email;
        $this->phone_number = $business->phone_number;
        $this->address = $business->address;
        $this->city_id = $business->city_id;
        $this->is_active = (bool) $business->is_active;
    }

    /**
     * @return array<string, mixed>
     */
    public function validateFor(?Business $business, User $actor): array
    {
        abort_unless($actor->isSuperAdmin(), 403);

        return $this->validate($this->rules($business));
    }

    public function save(?Business $business, User $actor): Business
    {
        $validated = $this->validateFor($business, $actor);

        return app(CreateOrUpdateBusiness::class)->handle($validated, $business, $actor);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(?Business $business): array
    {
        $businessId = $business?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('businesses', 'name')->ignore($businessId)->whereNull('deleted_at'),
            ],
            'nit' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('businesses', 'nit')->ignore($businessId)->whereNull('deleted_at'),
            ],
            'email' => ['nullable', 'email', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'city_id' => [
                'nullable',
                'integer',
                Rule::exists('cities', 'id')->where('is_active', true),
            ],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del negocio es obligatorio.',
            'name.unique' => 'Ya existe un negocio con este nombre.',
            'name.max' => 'El nombre no puede superar 255 caracteres.',
            'nit.unique' => 'Ya existe un negocio con este NIT.',
            'nit.max' => 'El NIT no puede superar 50 caracteres.',
            'email.email' => 'El correo electrónico no es válido.',
            'email.max' => 'El correo no puede superar 255 caracteres.',
            'phone_number.max' => 'El teléfono no puede superar 30 caracteres.',
            'address.max' => 'La dirección no puede superar 500 caracteres.',
            'city_id.exists' => 'La ciudad seleccionada no es válida.',
        ];
    }
}
