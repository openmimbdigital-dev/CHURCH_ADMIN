<?php

namespace App\Livewire\Forms\Users;

use App\Actions\User\CreateOrUpdateUser;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Form;

class UserForm extends Form
{
    public string $username = '';

    public string $first_name = '';

    public string $last_name = '';

    public string $email = '';

    public string $password = '';

    public ?string $phone_number = null;

    public bool $status = true;

    public ?int $business_id = null;

    public ?int $administrative_zone_id = null;

    public ?int $church_id = null;

    public function fillFromUser(User $user): void
    {
        $this->username = $user->username;
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->phone_number = $user->phone_number;
        $this->status = (bool) $user->status;
        $this->business_id = $user->business_id;
        $this->password = '';

        $church = $user->ledChurches()->first();
        if ($church) {
            $this->church_id = $church->id;
            $this->administrative_zone_id = $church->administrative_zone_id;
        } else {
            $zone = $user->ledAdministrativeZones()->first();
            $this->administrative_zone_id = $zone?->id;
            $this->church_id = null;
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function validateFor(?User $user, User $actor): array
    {
        if (! $actor->isSuperAdmin()) {
            $this->business_id = $actor->business_id;
        }

        return $this->validate($this->rules($user, $actor));
    }

    public function save(?User $user, User $actor): User
    {
        $validated = $this->validateFor($user, $actor);

        return app(CreateOrUpdateUser::class)->handle($validated, $user, $actor);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(?User $user, User $actor): array
    {
        $userId = $user?->id;
        $businessId = $actor->isSuperAdmin() ? $this->business_id : $actor->business_id;

        $rules = [
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($userId)->whereNull('deleted_at'),
            ],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId)->whereNull('deleted_at'),
            ],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'status' => ['boolean'],
            'administrative_zone_id' => [
                'nullable',
                'integer',
                Rule::exists('administrative_zones', 'id')
                    ->where(fn ($query) => $query->where('business_id', $businessId)->where('is_active', true)),
            ],
            'church_id' => [
                'nullable',
                'integer',
                Rule::exists('churches', 'id')
                    ->where(fn ($query) => $query
                        ->when($this->administrative_zone_id, fn ($q) => $q->where('administrative_zone_id', $this->administrative_zone_id))
                        ->where('business_id', $businessId)
                        ->where('is_active', true)),
            ],
        ];

        if ($user) {
            $rules['password'] = ['nullable', 'string', 'min:8'];
        } else {
            $rules['password'] = ['required', 'string', 'min:8'];
        }

        if ($actor->isSuperAdmin()) {
            $rules['business_id'] = ['required', 'integer', Rule::exists('businesses', 'id')->where('is_active', true)];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'username.required' => 'El usuario es obligatorio.',
            'username.unique' => 'Este nombre de usuario ya está en uso.',
            'first_name.required' => 'El nombre es obligatorio.',
            'last_name.required' => 'El apellido es obligatorio.',
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'El correo debe ser válido.',
            'email.unique' => 'Este correo ya está en uso.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'business_id.required' => 'Debes seleccionar un negocio.',
            'business_id.exists' => 'El negocio seleccionado no es válido.',
            'administrative_zone_id.exists' => 'La zona seleccionada no es válida para este negocio.',
            'church_id.exists' => 'La iglesia seleccionada no es válida para esta zona.',
        ];
    }
}
