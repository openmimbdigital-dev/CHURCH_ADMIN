<?php

namespace App\Livewire\Admin\Zones;

use App\Livewire\Forms\Zones\ZoneForm;
use App\Models\AdministrativeZone;
use App\Models\Business;
use App\Models\City;
use Illuminate\Support\Collection;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Form extends Component
{
    public ?AdministrativeZone $zone = null;

    public ZoneForm $zoneForm;

    /** @var Collection<int, Business> */
    public Collection $businesses;

    /** @var Collection<int, City> */
    public Collection $cities;

    public function mount(?AdministrativeZone $zone = null): void
    {
        $viewer = auth()->user();

        if (! $viewer->isSuperAdmin() && ! $viewer->business_id) {
            abort(403, 'No tienes un negocio asignado.');
        }

        if ($zone) {
            if (! $viewer->can('zones.edit')) {
                abort(403);
            }

            if (! $zone->isVisibleToAuthUser()) {
                abort(403);
            }

            $this->zone = $zone;
            $this->zoneForm->fillFromZone($zone);
        } else {
            if (! $viewer->can('zones.create')) {
                abort(403);
            }
        }

        $this->businesses = $viewer->isSuperAdmin()
            ? Business::query()->where('is_active', true)->orderBy('name')->get(['id', 'name'])
            : collect();

        if (! $viewer->isSuperAdmin()) {
            $this->zoneForm->business_id = $viewer->business_id;
        }

        $this->cities = City::query()
            ->with('department:id,name')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'department_id']);
    }

    public function save(): void
    {
        $this->zoneForm->save($this->zone, auth()->user());

        LivewireAlert::title($this->zone ? 'Zona actualizada' : 'Zona creada')
            ->text($this->zone
                ? 'Los datos de la zona se guardaron correctamente.'
                : 'La zona fue registrada correctamente.')
            ->success()
            ->asToast()
            ->show();

        $this->redirect(route('admin.zones.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.zones.form')
            ->title($this->zone ? 'Editar zona' : 'Crear zona');
    }
}
