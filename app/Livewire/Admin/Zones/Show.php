<?php

namespace App\Livewire\Admin\Zones;

use App\Models\AdministrativeZone;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Show extends Component
{
    public AdministrativeZone $zone;

    public function mount(AdministrativeZone $zone): void
    {
        if (! auth()->user()->can('zones.view')) {
            abort(403);
        }

        if (! $zone->isVisibleToAuthUser()) {
            abort(403);
        }

        $this->zone = $zone->load([
            'business:id,name',
            'city.department:id,name',
            'churches' => fn ($q) => $q->orderBy('name'),
            'leaders:id,first_name,last_name,username,email',
        ]);
    }

    public function render()
    {
        return view('livewire.admin.zones.show')
            ->title('Zona: '.$this->zone->name);
    }
}
