<?php

namespace App\Livewire\Admin\Users;

use App\Models\AdministrativeZone;
use App\Models\Business;
use App\Models\Church;
use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Form extends Component
{
    public ?User $user = null;

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
            $this->fillFromUser($user);
        } else {
            if (! $viewer->can('users.create')) {
                abort(403);
            }
        }

        $this->businesses = $viewer->isSuperAdmin()
            ? Business::query()->where('is_active', true)->orderBy('name')->get(['id', 'name'])
            : collect();

        if (! $viewer->isSuperAdmin()) {
            $this->business_id = $viewer->business_id;
            $this->loadZones();
        } elseif ($this->business_id) {
            $this->loadZones();
        }

        if ($this->administrative_zone_id) {
            $this->loadChurches();
        }
    }

    public function updatedBusinessId(): void
    {
        $this->administrative_zone_id = null;
        $this->church_id = null;
        $this->loadZones();
        $this->churches = collect();
    }

    public function updatedAdministrativeZoneId(): void
    {
        $this->church_id = null;
        $this->loadChurches();
    }

    protected function fillFromUser(User $user): void
    {
        $this->username = $user->username;
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->phone_number = $user->phone_number;
        $this->status = (bool) $user->status;
        $this->business_id = $user->business_id;

        $church = $user->ledChurches()->first();
        if ($church) {
            $this->church_id = $church->id;
            $this->administrative_zone_id = $church->administrative_zone_id;
        } else {
            $zone = $user->ledAdministrativeZones()->first();
            $this->administrative_zone_id = $zone?->id;
        }
    }

    protected function resolvedBusinessId(): ?int
    {
        if (auth()->user()->isSuperAdmin()) {
            return $this->business_id;
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
        if (! $this->administrative_zone_id) {
            $this->churches = collect();

            return;
        }

        $this->churches = Church::query()
            ->where('administrative_zone_id', $this->administrative_zone_id)
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
