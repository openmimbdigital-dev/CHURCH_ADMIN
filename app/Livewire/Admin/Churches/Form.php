<?php

namespace App\Livewire\Admin\Churches;

use App\Enums\ChurchCategory;
use App\Livewire\Forms\Churches\ChurchForm;
use App\Models\AdministrativeZone;
use App\Models\Business;
use App\Models\Church;
use App\Models\City;
use Illuminate\Support\Collection;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Form extends Component
{
    public ?Church $church = null;

    public ChurchForm $churchForm;

    /** @var Collection<int, Business> */
    public Collection $businesses;

    /** @var Collection<int, AdministrativeZone> */
    public Collection $zones;

    /** @var Collection<int, Church> */
    public Collection $parentChurches;

    /** @var Collection<int, City> */
    public Collection $cities;

    /** @var array<int, string> */
    public array $categories = [];

    public function mount(?Church $church = null): void
    {
        $this->zones = collect();
        $this->parentChurches = collect();
        $this->categories = collect(ChurchCategory::cases())
            ->mapWithKeys(fn (ChurchCategory $c) => [$c->value => $c->label()])
            ->all();

        $viewer = auth()->user();

        if (! $viewer->isSuperAdmin() && ! $viewer->business_id) {
            abort(403, 'No tienes un negocio asignado.');
        }

        if ($church) {
            if (! $viewer->can('churches.edit')) {
                abort(403);
            }

            if (! $church->isVisibleToAuthUser()) {
                abort(403);
            }

            $this->church = $church;
            $this->churchForm->fillFromChurch($church);
        } else {
            if (! $viewer->can('churches.create')) {
                abort(403);
            }
        }

        $this->businesses = $viewer->isSuperAdmin()
            ? Business::query()->where('is_active', true)->orderBy('name')->get(['id', 'name'])
            : collect();

        if (! $viewer->isSuperAdmin()) {
            $this->churchForm->business_id = $viewer->business_id;
            $this->loadZones();
        } elseif ($this->churchForm->business_id) {
            $this->loadZones();
        }

        if ($this->churchForm->administrative_zone_id && $this->motherChurchSelectEnabled()) {
            $this->loadParentChurches();
        }

        $this->cities = City::query()
            ->with('department:id,name')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'department_id']);
    }

    public function updatedChurchFormBusinessId(): void
    {
        $this->churchForm->administrative_zone_id = null;
        $this->churchForm->parent_id = null;
        $this->loadZones();
        $this->parentChurches = collect();
    }

    public function updatedChurchFormAdministrativeZoneId(): void
    {
        $this->churchForm->parent_id = null;
        $this->loadParentChurches();
    }

    public function updatedChurchFormCategory(): void
    {
        if (! ChurchCategory::requiresMotherChurchValue($this->churchForm->category)) {
            $this->churchForm->parent_id = null;
            $this->parentChurches = collect();

            return;
        }

        $this->loadParentChurches();
    }

    public function motherChurchSelectEnabled(): bool
    {
        return ChurchCategory::requiresMotherChurchValue($this->churchForm->category);
    }

    public function save(): void
    {
        $this->churchForm->save($this->church, auth()->user());

        LivewireAlert::title($this->church ? 'Iglesia actualizada' : 'Iglesia creada')
            ->text($this->church
                ? 'Los datos de la iglesia se guardaron correctamente.'
                : 'La iglesia fue registrada correctamente.')
            ->success()
            ->asToast()
            ->show();

        $this->redirect(route('admin.churches.index'), navigate: true);
    }

    protected function resolvedBusinessId(): ?int
    {
        if (auth()->user()->isSuperAdmin()) {
            return $this->churchForm->business_id;
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
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    protected function loadParentChurches(): void
    {
        if (! $this->churchForm->administrative_zone_id || ! $this->motherChurchSelectEnabled()) {
            $this->parentChurches = collect();

            return;
        }

        $businessId = $this->resolvedBusinessId();

        $this->parentChurches = Church::query()
            ->where('business_id', $businessId)
            ->where('administrative_zone_id', $this->churchForm->administrative_zone_id)
            ->where('category', ChurchCategory::Principal->value)
            ->when($this->church?->id, fn ($q) => $q->where('id', '!=', $this->church->id))
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function render()
    {
        return view('livewire.admin.churches.form')
            ->title($this->church ? 'Editar iglesia' : 'Crear iglesia');
    }
}
