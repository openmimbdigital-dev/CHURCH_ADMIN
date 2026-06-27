<?php

namespace App\Livewire\Forms\Users;

use App\Actions\User\CreateOrUpdateUser;
use App\Models\Church;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Form;

class UserForm extends Form
{
    public string $username = '';

    public string $first_name = '';

    public string $last_name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public ?string $phone_number = null;

    public bool $status = true;

    public ?int $business_id = null;

    public ?int $administrative_zone_id = null;

    /** @var array<int> */
    public array $church_ids = [];

    public ?int $default_church_id = null;

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
        $this->password_confirmation = '';

        $user->load([
            'administrativeZones:id',
            'churches' => fn ($query) => $query->select('churches.id'),
        ]);

        $this->administrative_zone_id = $user->administrativeZones->first()?->id;
        $this->church_ids = $user->churches->pluck('id')->map(fn ($id) => (int) $id)->all();

        $this->default_church_id = $user->churches
            ->first(fn ($church) => (int) $church->pivot->current_church === (int) $church->id)
            ?->id;

        $this->normalizeDefaultChurchId();
    }

    /**
     * @return array<string, mixed>
     */
    public function validateFor(?User $user, User $actor): array
    {
        if (! $actor->isSuperAdmin()) {
            $this->business_id = $actor->business_id;
            $this->applyCurrentChurchContext($actor);
        }

        $this->normalizeDefaultChurchId();

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
            'church_ids' => ['nullable', 'array'],
            'church_ids.*' => [
                'integer',
                Rule::exists('churches', 'id')
                    ->where(fn ($query) => $query
                        ->when($this->administrative_zone_id, fn ($q) => $q->where('administrative_zone_id', $this->administrative_zone_id))
                        ->where('business_id', $businessId)
                        ->where('is_active', true)),
            ],
            'default_church_id' => [
                Rule::requiredIf(count($this->church_ids) > 1),
                'nullable',
                'integer',
                Rule::in($this->church_ids),
            ],
        ];

        $securePassword = Password::min(8)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols();

        if ($user) {
            $rules['password'] = ['nullable', 'string', 'confirmed', $securePassword];
            $rules['password_confirmation'] = ['required_with:password'];
        } else {
            $rules['password'] = ['required', 'string', 'confirmed', $securePassword];
            $rules['password_confirmation'] = ['required'];
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
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.letters' => 'La contraseña debe incluir al menos una letra.',
            'password.mixed' => 'La contraseña debe incluir mayúsculas y minúsculas.',
            'password.numbers' => 'La contraseña debe incluir al menos un número.',
            'password.symbols' => 'La contraseña debe incluir al menos un carácter especial.',
            'password_confirmation.required' => 'Debes confirmar la contraseña.',
            'password_confirmation.required_with' => 'Debes confirmar la nueva contraseña.',
            'business_id.required' => 'Debes seleccionar un negocio.',
            'business_id.exists' => 'El negocio seleccionado no es válido.',
            'administrative_zone_id.exists' => 'La zona seleccionada no es válida para este negocio.',
            'church_ids.*.exists' => 'Una o más iglesias seleccionadas no son válidas para esta zona.',
            'default_church_id.required' => 'Debes indicar la iglesia predeterminada al iniciar sesión.',
            'default_church_id.in' => 'La iglesia predeterminada debe estar entre las iglesias seleccionadas.',
        ];
    }

    public function normalizeDefaultChurchId(): void
    {
        $churchIds = array_map('intval', $this->church_ids);

        if ($churchIds === []) {
            $this->default_church_id = null;

            return;
        }

        if (count($churchIds) === 1) {
            $this->default_church_id = $churchIds[0];

            return;
        }

        if ($this->default_church_id && ! in_array((int) $this->default_church_id, $churchIds, true)) {
            $this->default_church_id = null;
        }
    }

    protected function applyCurrentChurchContext(User $actor): void
    {
        if (! $actor->current_church_id) {
            return;
        }

        $currentChurchId = (int) $actor->current_church_id;
        $churchIds = array_map('intval', $this->church_ids);

        if (! in_array($currentChurchId, $churchIds, true)) {
            $this->church_ids[] = $currentChurchId;
        }

        if (! $this->administrative_zone_id) {
            $this->administrative_zone_id = Church::query()
                ->whereKey($currentChurchId)
                ->value('administrative_zone_id');
        }
    }
}
