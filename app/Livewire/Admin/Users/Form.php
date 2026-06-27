<?php

namespace App\Livewire\Admin\Users;

use App\Livewire\Forms\Users\UserForm;
use App\Models\AdministrativeZone;
use App\Models\Business;
use App\Models\Church;
use App\Models\User;
use Illuminate\Support\Collection;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Form extends Component
{
    public ?User $user = null;

    public UserForm $userForm;

    /** @var Collection<int, Business> */
    public Collection $businesses;

    /** @var Collection<int, AdministrativeZone> */
    public Collection $zones;

    /** @var Collection<int, Church> */
    public Collection $churches;

    public function mount(?User $user = null): void
    {
        $this->zones = collect();
        $this->churches = collect();

        $viewer = auth()->user();

        if (! $viewer->isSuperAdmin() && ! $viewer->business_id) {
            abort(403, 'No tienes un negocio asignado.');
        }

        if ($user) {
            if (! $viewer->can('users.edit')) {
                abort(403);
            }

            if (! $user->isVisibleToAuthUser()) {
                abort(403);
            }

            $this->user = $user;
            $this->userForm->fillFromUser($user);
        } else {
            if (! $viewer->can('users.create')) {
                abort(403);
            }
        }

        $this->businesses = $viewer->isSuperAdmin()
            ? Business::query()->where('is_active', true)->orderBy('name')->get(['id', 'name'])
            : collect();

        if (! $viewer->isSuperAdmin()) {
            $this->userForm->business_id = $viewer->business_id;
            $this->loadZones();
        } elseif ($this->userForm->business_id) {
            $this->loadZones();
        }

        if ($this->userForm->administrative_zone_id) {
            $this->loadChurches();
        }
    }

    public function updatedUserFormBusinessId(): void
    {
        $this->userForm->administrative_zone_id = null;
        $this->userForm->church_ids = [];
        $this->loadZones();
        $this->churches = collect();
    }

    public function updatedUserFormAdministrativeZoneId(): void
    {
        $this->userForm->church_ids = [];
        $this->loadChurches();
    }

    public function save(): void
    {
        $this->userForm->save($this->user, auth()->user());

        LivewireAlert::title($this->user ? 'Usuario actualizado' : 'Usuario creado')
            ->text($this->user
                ? 'Los datos del usuario se guardaron correctamente.'
                : 'El usuario fue registrado correctamente.')
            ->success()
            ->asToast()
            ->show();

        $this->redirect(route('admin.users.index'), navigate: true);
    }

    protected function resolvedBusinessId(): ?int
    {
        if (auth()->user()->isSuperAdmin()) {
            return $this->userForm->business_id;
        }

        return auth()->user()->business_id;
    }

    protected function loadZones(): void
    {
        $businessId = $this->resolvedBusinessId();

        if (! $businessId) {
            $this->zones = collect();

            return;
        }

        $this->zones = AdministrativeZone::query()
            ->where('business_id', $businessId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    protected function loadChurches(): void
    {
        if (! $this->userForm->administrative_zone_id) {
            $this->churches = collect();

            return;
        }

        $this->churches = Church::query()
            ->where('administrative_zone_id', $this->userForm->administrative_zone_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function render()
    {
        return view('livewire.admin.users.form')
            ->title($this->user ? 'Editar usuario' : 'Crear usuario');
    }
}
